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
 * Subscription plans controller.
 *
 * Handles the subscription plans related operations.
 *
 * @package Controllers
 */
class Subscription_plans extends EA_Controller
{
    public array $allowed_subscription_plan_fields = [
        'id',
        'name',
        'description',
        'appointments_per_month',
        'price',
        'is_active',
        'notes',
    ];

    /**
     * Subscription_plans constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('subscription_plans_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
        $this->load->library('webhooks_client');
    }

    /**
     * Render the backend subscription plans page.
     */
    public function index(): void
    {
        session(['dest_url' => site_url('subscription_plans')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_USERS)) {
            if ($user_id) {
                abort(403, 'Forbidden');
            }

            redirect('login');

            return;
        }

        $role_slug = session('role_slug');

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
        ]);

        html_vars([
            'page_title' => lang('subscription_plans'),
            'active_menu' => PRIV_USERS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/subscription_plans');
    }

    /**
     * Filter subscription plans by the provided keyword.
     */
    public function search(): void
    {
        try {
            if (cannot('view', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = 'name ASC';

            $limit = request('limit');

            $offset = request('offset');

            $subscription_plans = $this->subscription_plans_model->search($keyword, $limit, $offset, $order_by);

            json_response($subscription_plans);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Create a subscription plan.
     */
    public function store(): void
    {
        try {
            if (cannot('add', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            $subscription_plan = request();

            $this->subscription_plans_model->only($subscription_plan, $this->allowed_subscription_plan_fields);

            $subscription_plan_id = $this->subscription_plans_model->save($subscription_plan);

            $created_subscription_plan = $this->subscription_plans_model->find($subscription_plan_id);

            $this->webhooks_client->trigger(WEBHOOK_SUBSCRIPTION_PLAN_SAVE, $created_subscription_plan);

            json_response([
                'success' => true,
                'id' => $subscription_plan_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a subscription plan.
     */
    public function update(): void
    {
        try {
            if (cannot('edit', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            $subscription_plan = request();

            $this->subscription_plans_model->only($subscription_plan, $this->allowed_subscription_plan_fields);

            $subscription_plan_id = $this->subscription_plans_model->save($subscription_plan);

            $updated_subscription_plan = $this->subscription_plans_model->find($subscription_plan_id);

            $this->webhooks_client->trigger(WEBHOOK_SUBSCRIPTION_PLAN_SAVE, $updated_subscription_plan);

            json_response([
                'success' => true,
                'id' => $subscription_plan_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Remove a subscription plan.
     */
    public function destroy(): void
    {
        try {
            if (cannot('delete', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            $subscription_plan_id = request('subscription_plan_id');

            $subscription_plan = $this->subscription_plans_model->find($subscription_plan_id);

            $this->subscription_plans_model->delete($subscription_plan_id);

            $this->webhooks_client->trigger(WEBHOOK_SUBSCRIPTION_PLAN_DELETE, $subscription_plan);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Find a subscription plan.
     */
    public function find(): void
    {
        try {
            if (cannot('view', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            $subscription_plan_id = request('subscription_plan_id');

            $subscription_plan = $this->subscription_plans_model->find($subscription_plan_id);

            json_response($subscription_plan);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
