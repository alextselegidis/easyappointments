<?php defined('BASEPATH') or exit('No direct script access allowed');

class Stripe_api extends EA_Controller
{
    public function __construct()
    {
        parent::__construct();

        rate_limit($this->input->ip_address(), 90, 60);

        $this->load->library('api');
        $this->load->library('stripe_service');
        $this->load->library('tenant_context');
    }

    public function connect_onboard(): void
    {
        try {
            $this->api->auth();

            if (!$this->stripe_service->is_configured()) {
                json_response([
                    'success' => false,
                    'message' => 'Stripe is not configured.',
                ], 501);

                return;
            }

            $tenant_id = (int) request('tenantId');
            if ($tenant_id <= 0) {
                $tenant_id = $this->tenant_context->resolve_tenant_id();
            }

            $tenant = $this->stripe_service->get_tenant($tenant_id);

            if (empty($tenant)) {
                throw new InvalidArgumentException('Tenant not found.');
            }

            $refresh_url = (string) request('refreshUrl');
            $return_url = (string) request('returnUrl');

            if ($refresh_url === '') {
                $refresh_url = site_url('backend');
            }

            if ($return_url === '') {
                $return_url = site_url('backend');
            }

            $account = $this->stripe_service->create_connect_account(
                $tenant_id,
                (string) request('email'),
                (string) ($tenant['name'] ?? ''),
            );

            $link = $this->stripe_service->create_account_link(
                $account['accountId'],
                $refresh_url,
                $return_url,
            );

            json_response([
                'success' => true,
                'tenantId' => $tenant_id,
                'accountId' => $account['accountId'],
                'reusedAccount' => $account['reused'],
                'onboardingUrl' => $link['url'],
                'expiresAt' => $link['expiresAt'],
            ], 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function create_payment_intent(): void
    {
        try {
            if (!$this->stripe_service->is_configured()) {
                json_response([
                    'success' => false,
                    'message' => 'Stripe is not configured.',
                ], 501);

                return;
            }

            $amount = (float) request('amount');
            $appointment_id = request('appointmentId');
            $tenant_id = (int) request('tenantId');

            if ($tenant_id <= 0) {
                $tenant_id = $this->tenant_context->resolve_tenant_id();
            }

            if ($amount <= 0) {
                throw new InvalidArgumentException('Invalid amount.');
            }

            $tenant = $this->stripe_service->get_tenant($tenant_id);

            if (empty($tenant)) {
                throw new InvalidArgumentException('Tenant not found.');
            }

            $metadata = [
                'appointment_id' => (string) ($appointment_id ?? ''),
                'tenant_id' => (string) $tenant_id,
            ];

            $stripe_account_id = (string) ($tenant['stripe_account_id'] ?? '');
            $can_split = $stripe_account_id !== '' && !empty($tenant['stripe_charges_enabled']);

            if ($can_split) {
                $result = $this->stripe_service->create_split_payment_intent($amount, $stripe_account_id, $metadata);
            } else {
                $result = $this->stripe_service->create_payment_intent($amount, $metadata);
            }

            $this->stripe_service->track_payment_intent(
                $tenant_id,
                !empty($appointment_id) ? (int) $appointment_id : null,
                (string) $result['id'],
                $amount,
                (string) ($result['status'] ?? 'created'),
                $can_split,
                $can_split ? $stripe_account_id : null,
            );

            json_response($result, 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function payment_intent_status(): void
    {
        try {
            $payment_intent_id = trim((string) request('paymentIntentId'));

            if ($payment_intent_id === '') {
                throw new InvalidArgumentException('paymentIntentId is required.');
            }

            $status = $this->stripe_service->get_payment_intent_status($payment_intent_id);

            json_response($status);
        } catch (InvalidArgumentException $e) {
            json_response([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function webhook(): void
    {
        try {
            $payload = file_get_contents('php://input') ?: '';
            $signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

            $event = $this->stripe_service->validate_webhook($payload, $signature);

            $event_data_object = $event['data'] ?? null;

            if (is_object($event_data_object) && !empty($event_data_object->transfer_data->destination ?? null)) {
                $this->stripe_service->sync_tenant_account_flags((string) $event_data_object->transfer_data->destination);
            }

            if (is_object($event_data_object) && !empty($event_data_object->id)) {
                $status = (string) ($event_data_object->status ?? $event['type']);
                $this->stripe_service->mark_payment_intent_status(
                    (string) $event_data_object->id,
                    $status,
                    (string) $event['id'],
                    $payload,
                );
            }

            if (!$this->stripe_service->is_supported_event($event['type'])) {
                log_message('info', 'Stripe webhook ignored (unsupported event): ' . $event['type'] . ' (' . $event['id'] . ')');

                json_response([
                    'success' => true,
                    'event' => $event['type'],
                    'ignored' => true,
                ], 202);

                return;
            }

            log_message('info', 'Stripe webhook received: ' . $event['type'] . ' (' . $event['id'] . ')');

            json_response([
                'success' => true,
                'event' => $event['type'],
            ]);
        } catch (InvalidArgumentException $e) {
            json_response([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (RuntimeException $e) {
            json_response([
                'success' => false,
                'message' => $e->getMessage(),
            ], 501);
        } catch (Throwable $e) {
            if (is_a($e, '\\Stripe\\Exception\\SignatureVerificationException')) {
                json_response([
                    'success' => false,
                    'message' => 'Invalid Stripe webhook signature.',
                ], 400);

                return;
            }

            json_exception($e);
        }
    }
}
