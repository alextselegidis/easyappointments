<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Customers API v1 controller.
 *
 * @package Controllers
 */
class Customers_api_v1 extends EA_Controller
{
    /**
     * Customers_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');
        $this->load->library('tenant_context');
        $this->load->library('webhooks_client');

        $this->api->auth();

        $this->api->model('customers_model');
    }

    /**
     * Get a customer collection.
     */
    public function index(): void
    {
        try {
            $keyword = $this->api->request_keyword();

            $limit = $this->api->request_limit();

            $offset = $this->api->request_offset();

            $order_by = $this->api->request_order_by();

            $fields = $this->api->request_fields();

            $with = $this->api->request_with();

            $where = $this->tenant_context->with_tenant_where('users');

            $customers = empty($keyword)
                ? $this->customers_model->get($where, $limit, $offset, $order_by)
                : array_values(
                    array_filter(
                        $this->customers_model->search($keyword, $limit, $offset, $order_by),
                        fn($customer) => $this->tenant_context->belongs_to_current_tenant($customer),
                    ),
                );

            foreach ($customers as &$customer) {
                $this->customers_model->api_encode($customer);

                if (!empty($fields)) {
                    $this->customers_model->only($customer, $fields);
                }

                if (!empty($with)) {
                    $this->customers_model->load($customer, $with);
                }
            }

            json_response($customers);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get a single customer.
     *
     * @param int|null $id Customer ID.
     */
    public function show(?int $id = null): void
    {
        try {
            $occurrences = $this->customers_model->get($this->tenant_context->with_tenant_where('users', ['id' => $id]));

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $fields = $this->api->request_fields();

            $customer = $this->customers_model->find($id);

            $this->customers_model->api_encode($customer);

            if (!empty($fields)) {
                $this->customers_model->only($customer, $fields);
            }

            json_response($customer);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new customer.
     */
    public function store(): void
    {
        try {
            $customer = request();

            $this->customers_model->api_decode($customer);

            if ($this->tenant_context->table_has_tenant_column('users')) {
                $customer['tenant_id'] = $this->tenant_context->resolve_tenant_id();
            }

            if (array_key_exists('id', $customer)) {
                unset($customer['id']);
            }

            $customer_id = $this->customers_model->save($customer);

            $created_customer = $this->customers_model->find($customer_id);

            $this->webhooks_client->trigger(WEBHOOK_CUSTOMER_SAVE, $created_customer);

            $this->customers_model->api_encode($created_customer);

            json_response($created_customer, 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a customer.
     *
     * @param int $id Customer ID.
     */
    public function update(int $id): void
    {
        try {
            $occurrences = $this->customers_model->get($this->tenant_context->with_tenant_where('users', ['id' => $id]));

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $original_customer = $occurrences[0];

            $customer = request();

            $this->customers_model->api_decode($customer, $original_customer);

            if ($this->tenant_context->table_has_tenant_column('users')) {
                $customer['tenant_id'] = $this->tenant_context->resolve_tenant_id();
            }

            $customer_id = $this->customers_model->save($customer);

            $updated_customer = $this->customers_model->find($customer_id);

            $this->webhooks_client->trigger(WEBHOOK_CUSTOMER_SAVE, $updated_customer);

            $this->customers_model->api_encode($updated_customer);

            json_response($updated_customer);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Delete a customer.
     *
     * @param int $id Customer ID.
     */
    public function destroy(int $id): void
    {
        try {
            $occurrences = $this->customers_model->get($this->tenant_context->with_tenant_where('users', ['id' => $id]));

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $deleted_customer = $occurrences[0];

            $this->customers_model->delete($id);

            $this->webhooks_client->trigger(WEBHOOK_CUSTOMER_DELETE, $deleted_customer);

            response('', 204);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
