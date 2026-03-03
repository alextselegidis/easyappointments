<?php defined('BASEPATH') or exit('No direct script access allowed');

class Stripe_service
{
    protected EA_Controller|CI_Controller $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('payments');
    }

    public function is_configured(): bool
    {
        return config('payments_provider') === 'stripe' &&
            !empty(config('stripe_secret_key')) &&
            class_exists('\\Stripe\\StripeClient');
    }

    public function create_payment_intent(float $amount, array $metadata = []): array
    {
        if (!$this->is_configured()) {
            throw new RuntimeException('Stripe is not configured.');
        }

        $stripe_client_class = '\\Stripe\\StripeClient';
        $stripe = new $stripe_client_class(config('stripe_secret_key'));

        $intent = $stripe->paymentIntents->create([
            'amount' => (int) round($amount * 100),
            'currency' => config('stripe_currency') ?: 'eur',
            'metadata' => $metadata,
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return [
            'id' => $intent->id,
            'clientSecret' => $intent->client_secret,
            'status' => $intent->status,
        ];
    }

    public function create_connect_account(int $tenant_id, string $email = '', string $business_name = ''): array
    {
        if (!$this->is_configured()) {
            throw new RuntimeException('Stripe is not configured.');
        }

        $tenant = $this->get_tenant($tenant_id);

        if (empty($tenant)) {
            throw new InvalidArgumentException('Tenant not found.');
        }

        $existing_account_id = (string) ($tenant['stripe_account_id'] ?? '');

        if ($existing_account_id !== '') {
            return [
                'accountId' => $existing_account_id,
                'reused' => true,
            ];
        }

        $stripe_client_class = '\\Stripe\\StripeClient';
        $stripe = new $stripe_client_class(config('stripe_secret_key'));

        $account_payload = [
            'type' => 'express',
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
            'metadata' => [
                'tenant_id' => (string) $tenant_id,
                'tenant_slug' => (string) ($tenant['slug'] ?? ''),
            ],
        ];

        if ($email !== '') {
            $account_payload['email'] = $email;
        }

        if ($business_name !== '') {
            $account_payload['business_profile'] = [
                'name' => $business_name,
            ];
        }

        $account = $stripe->accounts->create($account_payload);

        $this->CI->db
            ->where('id', $tenant_id)
            ->update('tenants', [
                'stripe_account_id' => $account->id,
                'stripe_onboarding_completed' => 0,
                'stripe_details_submitted' => 0,
                'stripe_charges_enabled' => 0,
                'stripe_payouts_enabled' => 0,
                'stripe_updated_at' => date('Y-m-d H:i:s'),
            ]);

        return [
            'accountId' => $account->id,
            'reused' => false,
        ];
    }

    public function create_account_link(string $account_id, string $refresh_url, string $return_url): array
    {
        if (!$this->is_configured()) {
            throw new RuntimeException('Stripe is not configured.');
        }

        if ($account_id === '') {
            throw new InvalidArgumentException('Stripe account ID is required.');
        }

        $stripe_client_class = '\\Stripe\\StripeClient';
        $stripe = new $stripe_client_class(config('stripe_secret_key'));

        $account_link = $stripe->accountLinks->create([
            'account' => $account_id,
            'refresh_url' => $refresh_url,
            'return_url' => $return_url,
            'type' => 'account_onboarding',
        ]);

        return [
            'url' => $account_link->url,
            'expiresAt' => $account_link->expires_at,
        ];
    }

    public function create_split_payment_intent(float $amount, string $destination_account_id, array $metadata = []): array
    {
        if (!$this->is_configured()) {
            throw new RuntimeException('Stripe is not configured.');
        }

        if ($destination_account_id === '') {
            throw new InvalidArgumentException('Destination Stripe account is missing.');
        }

        $stripe_client_class = '\\Stripe\\StripeClient';
        $stripe = new $stripe_client_class(config('stripe_secret_key'));

        $amount_cents = (int) round($amount * 100);
        $fee_percent = (float) (config('stripe_platform_fee_percent') ?: 0);
        $application_fee_amount = (int) round(($amount_cents * $fee_percent) / 100);

        $intent = $stripe->paymentIntents->create([
            'amount' => $amount_cents,
            'currency' => config('stripe_currency') ?: 'eur',
            'metadata' => $metadata,
            'automatic_payment_methods' => ['enabled' => true],
            'application_fee_amount' => $application_fee_amount,
            'transfer_data' => [
                'destination' => $destination_account_id,
            ],
        ]);

        return [
            'id' => $intent->id,
            'clientSecret' => $intent->client_secret,
            'status' => $intent->status,
            'applicationFeeAmount' => $application_fee_amount,
            'destinationAccountId' => $destination_account_id,
        ];
    }

    public function validate_webhook(string $payload, string $signature): array
    {
        $stripe_webhook_class = '\\Stripe\\Webhook';

        if (!class_exists($stripe_webhook_class)) {
            throw new RuntimeException('Stripe SDK is not installed.');
        }

        if ($payload === '') {
            throw new InvalidArgumentException('Stripe webhook payload is empty.');
        }

        if ($signature === '') {
            throw new InvalidArgumentException('Stripe webhook signature is missing.');
        }

        $secret = config('stripe_webhook_secret');

        if (empty($secret)) {
            throw new RuntimeException('Stripe webhook secret is missing.');
        }

        $event = call_user_func([$stripe_webhook_class, 'constructEvent'], $payload, $signature, $secret);

        return [
            'id' => $event->id,
            'type' => $event->type,
            'data' => $event->data->object,
        ];
    }

    public function is_supported_event(string $event_type): bool
    {
        $supported = [
            'payment_intent.succeeded',
            'payment_intent.payment_failed',
            'checkout.session.completed',
            'invoice.paid',
            'invoice.payment_failed',
            'customer.subscription.updated',
            'customer.subscription.deleted',
        ];

        return in_array($event_type, $supported, true);
    }

    public function track_payment_intent(
        int $tenant_id,
        ?int $appointment_id,
        string $stripe_payment_intent_id,
        float $amount,
        string $status,
        bool $is_split,
        ?string $stripe_account_id = null,
    ): void {
        if (!$this->CI->db->table_exists('stripe_payment_intents')) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $existing = $this->CI->db
            ->get_where('stripe_payment_intents', ['stripe_payment_intent_id' => $stripe_payment_intent_id])
            ->row_array();

        $payload = [
            'tenant_id' => $tenant_id,
            'appointment_id' => $appointment_id,
            'stripe_payment_intent_id' => $stripe_payment_intent_id,
            'amount' => $amount,
            'currency' => config('stripe_currency') ?: 'eur',
            'status' => $status,
            'is_split' => $is_split ? 1 : 0,
            'stripe_account_id' => $stripe_account_id,
            'updated_at' => $now,
        ];

        if (empty($existing)) {
            $payload['created_at'] = $now;
            $this->CI->db->insert('stripe_payment_intents', $payload);
            return;
        }

        $this->CI->db
            ->where('stripe_payment_intent_id', $stripe_payment_intent_id)
            ->update('stripe_payment_intents', $payload);
    }

    public function mark_payment_intent_status(string $payment_intent_id, string $status, string $event_id, string $raw_payload): void
    {
        if (!$this->CI->db->table_exists('stripe_payment_intents')) {
            return;
        }

        $this->CI->db
            ->where('stripe_payment_intent_id', $payment_intent_id)
            ->update('stripe_payment_intents', [
                'status' => $status,
                'stripe_event_id' => $event_id,
                'raw_payload' => $raw_payload,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->sync_appointment_payment_state($payment_intent_id, $status);
    }

    public function attach_payment_intent_to_appointment(string $payment_intent_id, int $appointment_id, int $tenant_id): void
    {
        if ($payment_intent_id === '' || !$this->CI->db->table_exists('stripe_payment_intents')) {
            return;
        }

        $this->CI->db
            ->where('stripe_payment_intent_id', $payment_intent_id)
            ->update('stripe_payment_intents', [
                'appointment_id' => $appointment_id,
                'tenant_id' => $tenant_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $tracked = $this->get_tracked_payment_intent($payment_intent_id);
        $status = (string) ($tracked['status'] ?? 'pending');
        $this->sync_appointment_payment_state($payment_intent_id, $status);
    }

    public function get_payment_intent_status(string $payment_intent_id): array
    {
        if ($payment_intent_id === '') {
            throw new InvalidArgumentException('Payment intent ID is required.');
        }

        $tracked = $this->get_tracked_payment_intent($payment_intent_id);

        if (empty($tracked)) {
            throw new InvalidArgumentException('Payment intent not found.');
        }

        return [
            'paymentIntentId' => $payment_intent_id,
            'status' => (string) ($tracked['status'] ?? 'unknown'),
            'appointmentId' => !empty($tracked['appointment_id']) ? (int) $tracked['appointment_id'] : null,
            'tenantId' => !empty($tracked['tenant_id']) ? (int) $tracked['tenant_id'] : null,
            'updatedAt' => (string) ($tracked['updated_at'] ?? ''),
        ];
    }

    public function sync_tenant_account_flags(string $account_id): void
    {
        if (!$this->is_configured() || $account_id === '') {
            return;
        }

        $stripe_client_class = '\\Stripe\\StripeClient';
        $stripe = new $stripe_client_class(config('stripe_secret_key'));
        $account = $stripe->accounts->retrieve($account_id, []);

        $this->CI->db
            ->where('stripe_account_id', $account_id)
            ->update('tenants', [
                'stripe_details_submitted' => !empty($account->details_submitted) ? 1 : 0,
                'stripe_charges_enabled' => !empty($account->charges_enabled) ? 1 : 0,
                'stripe_payouts_enabled' => !empty($account->payouts_enabled) ? 1 : 0,
                'stripe_onboarding_completed' => (!empty($account->details_submitted) && !empty($account->charges_enabled)) ? 1 : 0,
                'stripe_updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function get_tenant(int $tenant_id): ?array
    {
        if (!$this->CI->db->table_exists('tenants')) {
            return null;
        }

        $tenant = $this->CI->db->get_where('tenants', ['id' => $tenant_id])->row_array();

        return empty($tenant) ? null : $tenant;
    }

    private function get_tracked_payment_intent(string $payment_intent_id): ?array
    {
        if (!$this->CI->db->table_exists('stripe_payment_intents')) {
            return null;
        }

        $tracked = $this->CI->db
            ->get_where('stripe_payment_intents', ['stripe_payment_intent_id' => $payment_intent_id])
            ->row_array();

        return empty($tracked) ? null : $tracked;
    }

    private function sync_appointment_payment_state(string $payment_intent_id, string $payment_status): void
    {
        if (!$this->CI->db->table_exists('stripe_payment_intents')) {
            return;
        }

        $tracked = $this->get_tracked_payment_intent($payment_intent_id);

        if (empty($tracked['appointment_id'])) {
            return;
        }

        $appointment = $this->CI->db->get_where('appointments', ['id' => (int) $tracked['appointment_id']])->row_array();

        if (empty($appointment)) {
            return;
        }

        $status_lower = strtolower($payment_status);

        $status_candidates = null;

        if (in_array($status_lower, ['succeeded'], true)) {
            $status_candidates = ['Pagato', 'Paid', 'Confermato', 'Confirmed'];
        } elseif (in_array($status_lower, ['canceled', 'requires_payment_method', 'payment_failed'], true)) {
            $status_candidates = ['Pagamento fallito', 'Payment Failed', 'Annullato', 'Cancelled'];
        } else {
            $status_candidates = ['In attesa pagamento', 'Payment Pending', 'Pending'];
        }

        $new_status = $this->pick_appointment_status_option($status_candidates);
        $existing_notes = trim((string) ($appointment['notes'] ?? ''));

        $notes_without_payment_tag = preg_replace('/\s*\[payment_status:[^\]]+\]/i', '', $existing_notes) ?: '';
        $updated_notes = trim($notes_without_payment_tag . ' [payment_status:' . $payment_status . ']');

        $update_payload = [
            'notes' => $updated_notes,
            'update_datetime' => date('Y-m-d H:i:s'),
        ];

        if (!empty($new_status)) {
            $update_payload['status'] = $new_status;
        }

        $this->CI->db->where('id', (int) $tracked['appointment_id'])->update('appointments', $update_payload);
    }

    private function pick_appointment_status_option(array $candidates): ?string
    {
        $options = json_decode(setting('appointment_status_options', '[]'), true) ?? [];

        if (empty($options)) {
            return null;
        }

        foreach ($candidates as $candidate) {
            foreach ($options as $option) {
                if (mb_strtolower((string) $option) === mb_strtolower((string) $candidate)) {
                    return (string) $option;
                }
            }
        }

        return null;
    }
}
