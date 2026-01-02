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
 * User subscriptions API v1 controller.
 *
 * @package Controllers
 */
class User_subscriptions_api_v1 extends EA_Controller
{
    /**
     * User_subscriptions_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user_subscriptions_model');

        $this->load->library('api');

        $this->api->auth();

        $this->api->model('user_subscriptions_model');
    }

    /**
     * Get user subscriptions.
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

            // Support filtering by customer_id
            $customer_id = $this->input->get('customerId');

            if ($customer_id) {
                $user_subscriptions = $this->user_subscriptions_model->get(
                    ['id_users_customer' => $customer_id],
                    $limit,
                    $offset,
                    $order_by
                );
            } elseif (!empty($keyword)) {
                $user_subscriptions = $this->user_subscriptions_model->search($keyword, $limit, $offset, $order_by);
            } else {
                $user_subscriptions = $this->user_subscriptions_model->get(null, $limit, $offset, $order_by);
            }

            foreach ($user_subscriptions as &$user_subscription) {
                $this->user_subscriptions_model->api_encode($user_subscription);

                if (!empty($fields)) {
                    $this->user_subscriptions_model->only($user_subscription, $fields);
                }

                if (!empty($with)) {
                    $this->user_subscriptions_model->load($user_subscription, $with);
                }
            }

            json_response($user_subscriptions);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get a single user subscription.
     *
     * @param int|null $id User subscription ID.
     */
    public function show(int $id = null): void
    {
        try {
            $occurrences = $this->api->request_occurrences();

            $fields = $this->api->request_fields();

            $with = $this->api->request_with();

            $user_subscription = $this->user_subscriptions_model->find($id);

            $this->user_subscriptions_model->api_encode($user_subscription);

            if (!empty($fields)) {
                $this->user_subscriptions_model->only($user_subscription, $fields);
            }

            if (!empty($with)) {
                $this->user_subscriptions_model->load($user_subscription, $with);
            }

            if (!empty($occurrences)) {
                $this->occurrences->attach($user_subscription, $occurrences);
            }

            json_response($user_subscription);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new user subscription.
     */
    public function store(): void
    {
        try {
            $user_subscription = request();

            $this->user_subscriptions_model->api_decode($user_subscription);

            if (array_key_exists('id', $user_subscription)) {
                unset($user_subscription['id']);
            }

            $user_subscription_id = $this->user_subscriptions_model->save($user_subscription);

            $created_user_subscription = $this->user_subscriptions_model->find($user_subscription_id);

            $this->user_subscriptions_model->api_encode($created_user_subscription);

            json_response($created_user_subscription, 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a user subscription.
     *
     * @param int|null $id User subscription ID.
     */
    public function update(int $id = null): void
    {
        try {
            $occurrences = $this->api->request_occurrences();

            $user_subscription = request();

            $this->user_subscriptions_model->api_decode($user_subscription, $this->user_subscriptions_model->find($id));

            $user_subscription['id'] = $id;

            $user_subscription_id = $this->user_subscriptions_model->save($user_subscription);

            $updated_user_subscription = $this->user_subscriptions_model->find($user_subscription_id);

            $this->user_subscriptions_model->api_encode($updated_user_subscription);

            if (!empty($occurrences)) {
                $this->occurrences->attach($updated_user_subscription, $occurrences);
            }

            json_response($updated_user_subscription);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Delete a user subscription.
     *
     * @param int|null $id User subscription ID.
     */
    public function destroy(int $id = null): void
    {
        try {
            $this->user_subscriptions_model->delete($id);

            response('', 204);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Check quota for a customer.
     */
    public function check_quota(): void
    {
        try {
            $customer_id = $this->input->get('customerId');

            if (!$customer_id) {
                json_response(['error' => 'Customer ID is required.'], 400);
                return;
            }

            $quota_info = $this->user_subscriptions_model->check_quota($customer_id);

            json_response($quota_info);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
