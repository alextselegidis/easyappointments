<?php defined('BASEPATH') or exit('No direct script access allowed');

class Gdpr_api_v1 extends EA_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');
        $this->load->library('tenant_context');

        $this->api->auth();

        $this->load->model('customers_model');
        $this->load->model('appointments_model');
        $this->load->model('consents_model');
    }

    public function export(int $customer_id): void
    {
        try {
            $customer = $this->customers_model->find($customer_id);

            if (!$this->tenant_context->belongs_to_current_tenant($customer)) {
                response('', 404);

                return;
            }

            $appointments = $this->appointments_model->get(
                $this->tenant_context->with_tenant_where('appointments', ['id_users_customer' => $customer_id]),
                null,
                null,
                'start_datetime ASC',
            );

            $consents = [];

            if (!empty($customer['email'])) {
                $consents = $this->consents_model->get(
                    $this->tenant_context->with_tenant_where('consents', ['email' => $customer['email']]),
                    null,
                    null,
                    'create_datetime ASC',
                );
            }

            $result = [
                'customer' => $customer,
                'appointments' => $appointments,
                'consents' => $consents,
            ];

            json_response($result);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function delete(int $customer_id): void
    {
        try {
            $customer = $this->customers_model->find($customer_id);

            if (!$this->tenant_context->belongs_to_current_tenant($customer)) {
                response('', 404);

                return;
            }

            $now = date('Y-m-d H:i:s');

            // Delete future appointments for this customer.
            $future_appointments = $this->appointments_model->get(
                $this->tenant_context->with_tenant_where('appointments', [
                    'id_users_customer' => $customer_id,
                    'start_datetime >=' => $now,
                ]),
            );

            foreach ($future_appointments as $future_appointment) {
                $this->appointments_model->delete((int) $future_appointment['id']);
            }

            // Anonymize customer data to preserve historical records.
            $anonymized_customer = [
                'id' => $customer_id,
                'first_name' => 'Utente',
                'last_name' => 'Anonimo',
                'email' => 'anon-' . $customer_id . '@example.local',
                'phone_number' => '0000000000',
                'address' => 'N/A',
                'city' => 'N/A',
                'state' => 'N/A',
                'zip_code' => '00000',
                'notes' => 'Anonymized by GDPR delete endpoint',
            ];

            if ($this->tenant_context->table_has_tenant_column('users')) {
                $anonymized_customer['tenant_id'] = $this->tenant_context->resolve_tenant_id();
            }

            $this->customers_model->save($anonymized_customer);

            json_response([
                'success' => true,
                'customerId' => $customer_id,
                'deletedFutureAppointments' => count($future_appointments),
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
