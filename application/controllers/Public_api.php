<?php defined('BASEPATH') or exit('No direct script access allowed');

class Public_api extends EA_Controller
{
    private array $allowed_customer_fields = [
        'id',
        'tenant_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'timezone',
        'language',
        'custom_field_1',
        'custom_field_2',
        'custom_field_3',
        'custom_field_4',
        'custom_field_5',
    ];

    private array $allowed_appointment_fields = [
        'id',
        'tenant_id',
        'start_datetime',
        'end_datetime',
        'location',
        'notes',
        'color',
        'status',
        'is_unavailability',
        'id_users_provider',
        'id_users_customer',
        'id_services',
    ];

    public function __construct()
    {
        parent::__construct();

        rate_limit($this->input->ip_address(), 120, 60);

        $this->load->model('services_model');
        $this->load->model('providers_model');
        $this->load->model('customers_model');
        $this->load->model('appointments_model');
        $this->load->model('settings_model');
        $this->load->model('consents_model');

        $this->load->library('availability');
        $this->load->library('notifications');
        $this->load->library('stripe_service');
        $this->load->library('synchronization');
        $this->load->library('webhooks_client');
    }

    public function services(): void
    {
        try {
            if (setting('disable_booking')) {
                abort(403);
            }

            $tenant_id = $this->resolve_tenant_id();
            $services = $this->services_model->get_available_services(true);

            $services = array_values(array_filter($services, fn($service) => $this->belongs_to_tenant($service, $tenant_id)));

            foreach ($services as &$service) {
                $this->services_model->api_encode($service);
            }

            json_response($services);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function providers(): void
    {
        try {
            if (setting('disable_booking')) {
                abort(403);
            }

            $tenant_id = $this->resolve_tenant_id();
            $providers = $this->providers_model->get_available_providers(true);

            $providers = array_values(
                array_filter($providers, fn($provider) => $this->belongs_to_tenant($provider, $tenant_id)),
            );

            foreach ($providers as &$provider) {
                $this->providers_model->api_encode($provider);
            }

            json_response($providers);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function tenant_settings(): void
    {
        try {
            $tenant_id = $this->resolve_tenant_id();

            $tenant = [];

            if ($this->db->table_exists('tenants')) {
                $tenant = $this->db->get_where('tenants', ['id' => $tenant_id, 'is_active' => 1])->row_array() ?: [];
            }

            $response = [
                'tenantId' => $tenant_id,
                'slug' => $tenant['slug'] ?? 'default',
                'name' => $tenant['name'] ?? 'BUUMER',
                'logo' => null,
                'primaryColor' => '#9CDCFE',
                'accentColor' => '#C586C0',
                'sector' => 'beauty',
                'features' => [
                    'requireAddress' => false,
                    'requireVisitType' => false,
                    'bookingMode' => 'slot',
                ],
            ];

            if (!empty($tenant)) {
                if (array_key_exists('logo_url', $tenant) && !empty($tenant['logo_url'])) {
                    $response['logo'] = $tenant['logo_url'];
                }

                if (array_key_exists('primary_color', $tenant) && !empty($tenant['primary_color'])) {
                    $response['primaryColor'] = $tenant['primary_color'];
                }

                if (array_key_exists('accent_color', $tenant) && !empty($tenant['accent_color'])) {
                    $response['accentColor'] = $tenant['accent_color'];
                }

                if (array_key_exists('sector', $tenant) && !empty($tenant['sector'])) {
                    $response['sector'] = $tenant['sector'];
                }

                if (array_key_exists('features_json', $tenant) && !empty($tenant['features_json'])) {
                    $features = json_decode((string) $tenant['features_json'], true);

                    if (is_array($features)) {
                        $response['features'] = array_merge($response['features'], $features);
                    }
                }
            }

            json_response($response);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function payment_options(): void
    {
        try {
            if (setting('disable_booking')) {
                abort(403);
            }

            $tenant_id = $this->resolve_tenant_id();
            $enabled = $this->is_payment_enabled_for_booking($tenant_id);

            json_response([
                'enabled' => $enabled,
                'provider' => $enabled ? 'stripe' : 'none',
                'publishableKey' => $enabled ? (string) config('stripe_publishable_key') : '',
                'currency' => (string) (config('stripe_currency') ?: 'eur'),
                'tenantId' => $tenant_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function availabilities(): void
    {
        try {
            if (setting('disable_booking')) {
                abort(403);
            }

            $tenant_id = $this->resolve_tenant_id();
            $provider_id = request('providerId');
            $service_id = request('serviceId');
            $date = request('date') ?: date('Y-m-d');

            if (empty($provider_id) || empty($service_id)) {
                json_response([]);

                return;
            }

            $provider = $this->providers_model->find((int) $provider_id);
            $service = $this->services_model->find((int) $service_id);

            if (empty($provider) || empty($service)) {
                response('', 404);

                return;
            }

            if (!$this->belongs_to_tenant($provider, $tenant_id) || !$this->belongs_to_tenant($service, $tenant_id)) {
                response('', 404);

                return;
            }

            $available_hours = $this->availability->get_available_hours($date, $service, $provider);

            json_response($available_hours);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function book(): void
    {
        try {
            if (setting('disable_booking')) {
                abort(403);
            }

            $tenant_id = $this->resolve_tenant_id();
            $payload = request();

            $service_id = (int) ($payload['serviceId'] ?? 0);
            $provider_id = (int) ($payload['providerId'] ?? 0);
            $start_datetime = $payload['start'] ?? null;

            if (!$service_id || !$provider_id || !$start_datetime) {
                throw new RuntimeException('Missing booking payload fields.', 422);
            }

            $service = $this->services_model->find($service_id);
            $provider = $this->providers_model->find($provider_id);

            if (empty($service) || empty($provider)) {
                response('', 404);

                return;
            }

            if (!$this->belongs_to_tenant($provider, $tenant_id) || !$this->belongs_to_tenant($service, $tenant_id)) {
                response('', 404);

                return;
            }

            $customer_full_name = trim((string) ($payload['customerName'] ?? ''));
            $customer_phone = trim((string) ($payload['customerPhone'] ?? ''));
            $customer_email = trim((string) ($payload['customerEmail'] ?? ''));
            $customer_notes = trim((string) ($payload['customerNotes'] ?? ''));
            $privacy_consent = filter_var($payload['privacyConsent'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $terms_consent = filter_var($payload['termsConsent'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $marketing_consent = filter_var($payload['marketingConsent'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $payment_intent_id = trim((string) ($payload['paymentIntentId'] ?? ''));

            if (!$privacy_consent || !$terms_consent) {
                throw new RuntimeException('Required legal consents are missing.', 422);
            }

            if ($this->is_payment_enabled_for_booking($tenant_id) && !empty($service['price']) && empty($payment_intent_id)) {
                throw new RuntimeException('Payment is required before booking confirmation.', 422);
            }

            if (mb_strlen($customer_full_name) < 2 || mb_strlen($customer_phone) < 5) {
                throw new RuntimeException('Invalid customer details.', 422);
            }

            if (empty($customer_email)) {
                $customer_email = time() . '@buumer.local';
            }

            $name_parts = preg_split('/\s+/', $customer_full_name);
            $first_name = array_shift($name_parts) ?: 'Cliente';
            $last_name = implode(' ', $name_parts) ?: 'BUUMER';

            $customer = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $customer_email,
                'phone_number' => $customer_phone,
                'notes' => $customer_notes,
                'language' => session('language') ?? config('language'),
            ];

            if ($this->table_has_tenant_column('customers')) {
                $customer['tenant_id'] = $tenant_id;
            }

            if ($this->customers_model->exists($customer)) {
                $customer['id'] = $this->customers_model->find_record_id($customer);
            }

            $this->customers_model->only($customer, $this->allowed_customer_fields);
            $customer_id = $this->customers_model->save($customer);
            $saved_customer = $this->customers_model->find($customer_id);

            $customer_ip = $this->input->ip_address();
            $consent_base = [
                'first_name' => $saved_customer['first_name'] ?? '-',
                'last_name' => $saved_customer['last_name'] ?? '-',
                'email' => $saved_customer['email'] ?? '-',
                'ip' => $customer_ip,
            ];

            if ($this->table_has_tenant_column('consents')) {
                $consent_base['tenant_id'] = $tenant_id;
            }

            $privacy_consent_record = $consent_base;
            $privacy_consent_record['type'] = 'privacy-policy';
            $this->consents_model->save($privacy_consent_record);

            $terms_consent_record = $consent_base;
            $terms_consent_record['type'] = 'terms-and-conditions';
            $this->consents_model->save($terms_consent_record);

            if ($marketing_consent) {
                $marketing_consent_record = $consent_base;
                $marketing_consent_record['type'] = 'marketing';
                $this->consents_model->save($marketing_consent_record);
            }

            $start = new DateTime($start_datetime);
            $date = $start->format('Y-m-d');
            $hour = $start->format('H:i');
            $available_hours = $this->availability->get_available_hours($date, $service, $provider);

            if (!in_array($hour, $available_hours)) {
                throw new RuntimeException(lang('requested_hour_is_unavailable'));
            }

            $appointment = [
                'start_datetime' => $start->format('Y-m-d H:i:s'),
                'id_services' => $service_id,
                'id_users_provider' => $provider_id,
                'id_users_customer' => $customer_id,
                'notes' => $customer_notes,
                'is_unavailability' => false,
                'color' => $service['color'] ?? null,
            ];

            if ($this->table_has_tenant_column('appointments')) {
                $appointment['tenant_id'] = $tenant_id;
            }

            if (empty($payload['end'])) {
                $appointment['end_datetime'] = $this->appointments_model->calculate_end_datetime($appointment);
            } else {
                $appointment['end_datetime'] = (new DateTime($payload['end']))->format('Y-m-d H:i:s');
            }

            if (!empty($payment_intent_id)) {
                $existing_notes = trim((string) ($appointment['notes'] ?? ''));
                $payment_note = '[payment_intent:' . $payment_intent_id . ']';
                $appointment['notes'] = trim($existing_notes . ' ' . $payment_note);
            }

            $appointment_status_options_json = setting('appointment_status_options', '[]');
            $appointment_status_options = json_decode($appointment_status_options_json, true) ?? [];
            $appointment['status'] = $appointment_status_options[0] ?? null;

            $this->appointments_model->only($appointment, $this->allowed_appointment_fields);
            $appointment_id = $this->appointments_model->save($appointment);
            $saved_appointment = $this->appointments_model->find($appointment_id);

            if (!empty($payment_intent_id)) {
                $this->stripe_service->attach_payment_intent_to_appointment($payment_intent_id, (int) $saved_appointment['id'], $tenant_id);
            }

            $company_color = setting('company_color');
            $settings = [
                'company_name' => setting('company_name'),
                'company_email' => setting('company_email'),
                'company_link' => setting('company_link'),
                'company_color' => !empty($company_color) && $company_color != DEFAULT_COMPANY_COLOR
                    ? $company_color
                    : null,
                'date_format' => setting('date_format'),
                'time_format' => setting('time_format'),
            ];

            $this->synchronization->sync_appointment_saved(
                $saved_appointment,
                $service,
                $provider,
                $saved_customer,
                $settings,
            );

            $this->notifications->notify_appointment_saved(
                $saved_appointment,
                $service,
                $provider,
                $saved_customer,
                $settings,
                false,
            );

            $this->webhooks_client->trigger(WEBHOOK_APPOINTMENT_SAVE, $saved_appointment);

            $response = [
                'appointmentId' => $saved_appointment['id'],
                'appointmentHash' => $saved_appointment['hash'],
            ];

            json_response($response, 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    private function resolve_tenant_id(): int
    {
        if (!$this->db->table_exists('tenants')) {
            return 1;
        }

        $host = $this->input->server('HTTP_HOST') ?: '';
        $host_without_port = explode(':', $host)[0] ?? '';

        if (in_array($host_without_port, ['127.0.0.1', 'localhost'], true) || empty($host_without_port)) {
            return 1;
        }

        $tokens = explode('.', $host_without_port);
        $slug = $tokens[0] ?? 'default';

        $tenant = $this->db->get_where('tenants', ['slug' => $slug, 'is_active' => 1])->row_array();

        if (empty($tenant['id'])) {
            $tenant = $this->db->get_where('tenants', ['slug' => 'default', 'is_active' => 1])->row_array();
        }

        return (int) ($tenant['id'] ?? 1);
    }

    private function belongs_to_tenant(array $resource, int $tenant_id): bool
    {
        if (!array_key_exists('tenant_id', $resource) || $resource['tenant_id'] === null) {
            return true;
        }

        return (int) $resource['tenant_id'] === $tenant_id;
    }

    private function table_has_tenant_column(string $table): bool
    {
        return $this->db->table_exists($table) && $this->db->field_exists('tenant_id', $table);
    }

    private function is_payment_enabled_for_booking(int $tenant_id): bool
    {
        if (config('payments_provider') !== 'stripe') {
            return false;
        }

        if (!filter_var(config('stripe_enable_payment_for_booking'), FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }

        if (empty(config('stripe_publishable_key'))) {
            return false;
        }

        if (!$this->db->table_exists('tenants')) {
            return true;
        }

        $tenant = $this->db->get_where('tenants', ['id' => $tenant_id])->row_array();

        if (empty($tenant)) {
            return false;
        }

        return !empty($tenant['stripe_account_id']) && !empty($tenant['stripe_charges_enabled']);
    }
}
