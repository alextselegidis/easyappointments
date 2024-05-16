<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Webhooks controller.
 *
 * Handles the webhooks related operations.
 *
 * @package Controllers
 */
class Webhooks extends EA_Controller
{
    public array $allowed_webhook_fields = ['id', 'name', 'url', 'actions', 'secret_token', 'is_ssl_verified', 'notes'];

    /**
     * Webhooks constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('webhooks_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
    }

    /**
     * Render the backend webhooks page.
     *
     * On this page admin users will be able to manage webhooks, which are eventually selected by customers during the
     * booking process.
     */
    public function index(): void
    {
        session(['dest_url' => site_url('webhooks')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_WEBHOOKS)) {
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
            'page_title' => lang('webhooks'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
            'available_actions' => [
                WEBHOOK_APPOINTMENT_SAVE,
                WEBHOOK_APPOINTMENT_DELETE,
                WEBHOOK_UNAVAILABILITY_SAVE,
                WEBHOOK_UNAVAILABILITY_DELETE,
                WEBHOOK_BLOCKED_PERIOD_SAVE,
                WEBHOOK_BLOCKED_PERIOD_DELETE,
                WEBHOOK_CUSTOMER_SAVE,
                WEBHOOK_CUSTOMER_DELETE,
                WEBHOOK_SERVICE_SAVE,
                WEBHOOK_SERVICE_DELETE,
                WEBHOOK_SERVICE_CATEGORY_SAVE,
                WEBHOOK_SERVICE_CATEGORY_DELETE,
                WEBHOOK_PROVIDER_SAVE,
                WEBHOOK_PROVIDER_DELETE,
                WEBHOOK_SECRETARY_SAVE,
                WEBHOOK_SECRETARY_DELETE,
                WEBHOOK_ADMIN_SAVE,
                WEBHOOK_ADMIN_DELETE,
            ],
        ]);

        $this->load->view('pages/webhooks');
    }

    /**
     * Filter webhooks by the provided keyword.
     */
    public function search(): void
    {
        try {
            if (cannot('view', PRIV_WEBHOOKS)) {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = request('order_by', 'update_datetime DESC');

            $limit = request('limit', 1000);

            $offset = (int) request('offset', '0');

            $webhooks = $this->webhooks_model->search($keyword, $limit, $offset, $order_by);

            json_response($webhooks);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new webhook.
     */
    public function store(): void
    {
        try {
            if (cannot('add', PRIV_WEBHOOKS)) {
                abort(403, 'Forbidden');
            }

            $webhook = request('webhook');

            $this->webhooks_model->only($webhook, $this->allowed_webhook_fields);

            $webhook_id = $this->webhooks_model->save($webhook);

            json_response([
                'success' => true,
                'id' => $webhook_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a webhook.
     */
    public function update(): void
    {
        try {
            if (cannot('edit', PRIV_WEBHOOKS)) {
                abort(403, 'Forbidden');
            }

            $webhook = request('webhook');

            $this->webhooks_model->only($webhook, $this->allowed_webhook_fields);

            $webhook_id = $this->webhooks_model->save($webhook);

            json_response([
                'success' => true,
                'id' => $webhook_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Remove a webhook.
     */
    public function destroy(): void
    {
        try {
            if (cannot('delete', PRIV_WEBHOOKS)) {
                abort(403, 'Forbidden');
            }

            $webhook_id = request('webhook_id');

            $this->webhooks_model->delete($webhook_id);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Find a webhook.
     */
    public function find(): void
    {
        try {
            if (cannot('view', PRIV_WEBHOOKS)) {
                abort(403, 'Forbidden');
            }

            $webhook_id = request('webhook_id');

            $webhook = $this->webhooks_model->find($webhook_id);

            json_response($webhook);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
