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
 * Subscription plans API v1 controller.
 *
 * @package Controllers
 */
class Subscription_plans_api_v1 extends EA_Controller
{
    /**
     * Subscription_plans_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('subscription_plans_model');

        $this->load->library('api');

        $this->api->auth();

        $this->api->model('subscription_plans_model');
    }

    /**
     * Get subscription plans.
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

            $subscription_plans = empty($keyword)
                ? $this->subscription_plans_model->get(null, $limit, $offset, $order_by)
                : $this->subscription_plans_model->search($keyword, $limit, $offset, $order_by);

            foreach ($subscription_plans as &$subscription_plan) {
                $this->subscription_plans_model->api_encode($subscription_plan);

                if (!empty($fields)) {
                    $this->subscription_plans_model->only($subscription_plan, $fields);
                }

                if (!empty($with)) {
                    $this->subscription_plans_model->load($subscription_plan, $with);
                }
            }

            json_response($subscription_plans);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get a single subscription plan.
     *
     * @param int|null $id Subscription plan ID.
     */
    public function show(int $id = null): void
    {
        try {
            $occurrences = $this->api->request_occurrences();

            $fields = $this->api->request_fields();

            $with = $this->api->request_with();

            $subscription_plan = $this->subscription_plans_model->find($id);

            $this->subscription_plans_model->api_encode($subscription_plan);

            if (!empty($fields)) {
                $this->subscription_plans_model->only($subscription_plan, $fields);
            }

            if (!empty($with)) {
                $this->subscription_plans_model->load($subscription_plan, $with);
            }

            if (!empty($occurrences)) {
                $this->occurrences->attach($subscription_plan, $occurrences);
            }

            json_response($subscription_plan);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new subscription plan.
     */
    public function store(): void
    {
        try {
            $subscription_plan = request();

            $this->subscription_plans_model->api_decode($subscription_plan);

            if (array_key_exists('id', $subscription_plan)) {
                unset($subscription_plan['id']);
            }

            $subscription_plan_id = $this->subscription_plans_model->save($subscription_plan);

            $created_subscription_plan = $this->subscription_plans_model->find($subscription_plan_id);

            $this->subscription_plans_model->api_encode($created_subscription_plan);

            json_response($created_subscription_plan, 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a subscription plan.
     *
     * @param int|null $id Subscription plan ID.
     */
    public function update(int $id = null): void
    {
        try {
            $occurrences = $this->api->request_occurrences();

            $subscription_plan = request();

            $this->subscription_plans_model->api_decode($subscription_plan, $this->subscription_plans_model->find($id));

            $subscription_plan['id'] = $id;

            $subscription_plan_id = $this->subscription_plans_model->save($subscription_plan);

            $updated_subscription_plan = $this->subscription_plans_model->find($subscription_plan_id);

            $this->subscription_plans_model->api_encode($updated_subscription_plan);

            if (!empty($occurrences)) {
                $this->occurrences->attach($updated_subscription_plan, $occurrences);
            }

            json_response($updated_subscription_plan);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Delete a subscription plan.
     *
     * @param int|null $id Subscription plan ID.
     */
    public function destroy(int $id = null): void
    {
        try {
            $this->subscription_plans_model->delete($id);

            response('', 204);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
