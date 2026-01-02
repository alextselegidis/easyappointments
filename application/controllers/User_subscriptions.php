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
 * User subscriptions controller.
 *
 * Handles the user subscriptions related operations.
 *
 * @package Controllers
 */
class User_subscriptions extends EA_Controller
{
    public array $allowed_user_subscription_fields = [
        'id',
        'id_users_customer',
        'id_subscription_plans',
        'start_date',
        'end_date',
        'appointments_quota',
        'appointments_used',
        'is_active',
        'notes',
    ];

    /**
     * User_subscriptions constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user_subscriptions_model');
        $this->load->model('subscription_plans_model');
        $this->load->model('customers_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
        $this->load->library('webhooks_client');
    }

    /**
     * Render the backend user subscriptions page.
     */
    public function index(): void
    {
        session(['dest_url' => site_url('user_subscriptions')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_CUSTOMERS)) {
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
            'page_title' => lang('user_subscriptions'),
            'active_menu' => PRIV_CUSTOMERS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/user_subscriptions');
    }

    /**
     * Filter user subscriptions by the provided keyword.
     */
    public function search(): void
    {
        try {
            if (cannot('view', PRIV_CUSTOMERS)) {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = 'id DESC';

            $limit = request('limit');

            $offset = request('offset');

            $user_subscriptions = $this->user_subscriptions_model->search($keyword, $limit, $offset, $order_by);

            // Load related data
            foreach ($user_subscriptions as &$user_subscription) {
                $this->user_subscriptions_model->load($user_subscription, ['customer', 'subscription_plan']);
            }

            json_response($user_subscriptions);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Create a user subscription.
     */
    public function store(): void
    {
        try {
            if (cannot('add', PRIV_CUSTOMERS)) {
                abort(403, 'Forbidden');
            }

            $user_subscription = request();

            $this->user_subscriptions_model->only($user_subscription, $this->allowed_user_subscription_fields);

            $user_subscription_id = $this->user_subscriptions_model->save($user_subscription);

            $created_user_subscription = $this->user_subscriptions_model->find($user_subscription_id);

            $this->webhooks_client->trigger(WEBHOOK_USER_SUBSCRIPTION_SAVE, $created_user_subscription);

            json_response([
                'success' => true,
                'id' => $user_subscription_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a user subscription.
     */
    public function update(): void
    {
        try {
            if (cannot('edit', PRIV_CUSTOMERS)) {
                abort(403, 'Forbidden');
            }

            $user_subscription = request();

            $this->user_subscriptions_model->only($user_subscription, $this->allowed_user_subscription_fields);

            $user_subscription_id = $this->user_subscriptions_model->save($user_subscription);

            $updated_user_subscription = $this->user_subscriptions_model->find($user_subscription_id);

            $this->webhooks_client->trigger(WEBHOOK_USER_SUBSCRIPTION_SAVE, $updated_user_subscription);

            json_response([
                'success' => true,
                'id' => $user_subscription_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Remove a user subscription.
     */
    public function destroy(): void
    {
        try {
            if (cannot('delete', PRIV_CUSTOMERS)) {
                abort(403, 'Forbidden');
            }

            $user_subscription_id = request('user_subscription_id');

            $user_subscription = $this->user_subscriptions_model->find($user_subscription_id);

            $this->user_subscriptions_model->delete($user_subscription_id);

            $this->webhooks_client->trigger(WEBHOOK_USER_SUBSCRIPTION_DELETE, $user_subscription);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Find a user subscription.
     */
    public function find(): void
    {
        try {
            if (cannot('view', PRIV_CUSTOMERS)) {
                abort(403, 'Forbidden');
            }

            $user_subscription_id = request('user_subscription_id');

            $user_subscription = $this->user_subscriptions_model->find($user_subscription_id);

            // Load related data
            $this->user_subscriptions_model->load($user_subscription, ['customer', 'subscription_plan']);

            json_response($user_subscription);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get active subscription for a customer.
     */
    public function get_customer_subscription(): void
    {
        try {
            $customer_id = request('customer_id');

            if (!$customer_id) {
                json_response(['error' => 'Customer ID is required.'], 400);
                return;
            }

            $subscription = $this->user_subscriptions_model->get_active_subscription($customer_id);

            if ($subscription) {
                $this->user_subscriptions_model->load($subscription, ['subscription_plan']);
            }

            json_response($subscription);
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
            $customer_id = request('customer_id');

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
