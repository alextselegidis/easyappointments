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
 * Blocked_periods controller.
 *
 * Handles the blocked-periods related operations.
 *
 * @package Controllers
 */
class Blocked_periods extends EA_Controller
{
    public array $allowed_blocked_period_fields = ['id', 'name', 'start_datetime', 'end_datetime', 'notes'];

    /**
     * Blocked_periods constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('blocked_periods_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
        $this->load->library('webhooks_client');
    }

    /**
     * Render the backend blocked-periods page.
     *
     * On this page admin users will be able to manage blocked-periods, which are eventually selected by customers during the
     * booking process.
     */
    public function index(): void
    {
        session(['dest_url' => site_url('blocked_periods')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_BLOCKED_PERIODS)) {
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
            'date_format' => setting('date_format'),
            'time_format' => setting('time_format'),
            'first_weekday' => setting('first_weekday'),
        ]);

        html_vars([
            'page_title' => lang('blocked_periods'),
            'active_menu' => PRIV_BLOCKED_PERIODS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/blocked_periods');
    }

    /**
     * Filter blocked-periods by the provided keyword.
     */
    public function search(): void
    {
        try {
            if (cannot('view', PRIV_BLOCKED_PERIODS)) {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = request('order_by', 'update_datetime DESC');

            $limit = request('limit', 1000);

            $offset = (int) request('offset', '0');

            $blocked_periods = $this->blocked_periods_model->search($keyword, $limit, $offset, $order_by);

            json_response($blocked_periods);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new service-category.
     */
    public function store(): void
    {
        try {
            if (cannot('add', PRIV_BLOCKED_PERIODS)) {
                abort(403, 'Forbidden');
            }

            $blocked_period = request('blocked_period');

            $this->blocked_periods_model->only($blocked_period, $this->allowed_blocked_period_fields);

            $blocked_period_id = $this->blocked_periods_model->save($blocked_period);

            $blocked_period = $this->blocked_periods_model->find($blocked_period_id);

            $this->webhooks_client->trigger(WEBHOOK_BLOCKED_PERIOD_SAVE, $blocked_period);

            json_response([
                'success' => true,
                'id' => $blocked_period_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Find a service-category.
     */
    public function find(): void
    {
        try {
            if (cannot('view', PRIV_BLOCKED_PERIODS)) {
                abort(403, 'Forbidden');
            }

            $blocked_period_id = request('blocked_period_id');

            $blocked_period = $this->blocked_periods_model->find($blocked_period_id);

            json_response($blocked_period);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a service-category.
     */
    public function update(): void
    {
        try {
            if (cannot('edit', PRIV_BLOCKED_PERIODS)) {
                abort(403, 'Forbidden');
            }

            $blocked_period = request('blocked_period');

            $this->blocked_periods_model->only($blocked_period, $this->allowed_blocked_period_fields);

            $blocked_period_id = $this->blocked_periods_model->save($blocked_period);

            $blocked_period = $this->blocked_periods_model->find($blocked_period_id);

            $this->webhooks_client->trigger(WEBHOOK_BLOCKED_PERIOD_SAVE, $blocked_period);

            json_response([
                'success' => true,
                'id' => $blocked_period_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Remove a service-category.
     */
    public function destroy(): void
    {
        try {
            if (cannot('delete', PRIV_BLOCKED_PERIODS)) {
                abort(403, 'Forbidden');
            }

            $blocked_period_id = request('blocked_period_id');

            $blocked_period = $this->blocked_periods_model->find($blocked_period_id);

            $this->blocked_periods_model->delete($blocked_period_id);

            $this->webhooks_client->trigger(WEBHOOK_BLOCKED_PERIOD_DELETE, $blocked_period);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
