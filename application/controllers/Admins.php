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
 * Admins controller.
 *
 * Handles the admins related operations.
 *
 * @package Controllers
 */
class Admins extends EA_Controller
{
    public array $allowed_admin_fields = [
        'id',
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'notes',
        'timezone',
        'language',
        'ldap_dn',
        'settings',
    ];

    public array $allowed_admin_setting_fields = ['username', 'password', 'notifications', 'calendar_view'];

    /**
     * Admins constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('admins_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
        $this->load->library('webhooks_client');
    }

    /**
     * Render the backend admins page.
     *
     * On this page admin users will be able to manage admins, which are eventually selected by customers during the
     * booking process.
     */
    public function index(): void
    {
        session(['dest_url' => site_url('admins')]);

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
            'timezones' => $this->timezones->to_array(),
            'min_password_length' => MIN_PASSWORD_LENGTH,
            'default_language' => setting('default_language'),
            'default_timezone' => setting('default_timezone'),
        ]);

        html_vars([
            'page_title' => lang('admins'),
            'active_menu' => PRIV_USERS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'grouped_timezones' => $this->timezones->to_grouped_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/admins');
    }

    /**
     * Filter admins by the provided keyword.
     */
    public function search(): void
    {
        try {
            if (cannot('view', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = request('order_by', 'update_datetime DESC');

            $limit = request('limit', 1000);

            $offset = (int) request('offset', '0');

            $admins = $this->admins_model->search($keyword, $limit, $offset, $order_by);

            json_response($admins);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new admin.
     */
    public function store(): void
    {
        try {
            if (cannot('add', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            $admin = request('admin');

            $this->admins_model->only($admin, $this->allowed_admin_fields);

            $this->admins_model->only($admin['settings'], $this->allowed_admin_setting_fields);

            $admin_id = $this->admins_model->save($admin);

            $admin = $this->admins_model->find($admin_id);

            $this->webhooks_client->trigger(WEBHOOK_ADMIN_SAVE, $admin);

            json_response([
                'success' => true,
                'id' => $admin_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Find an admin.
     */
    public function find(): void
    {
        try {
            if (cannot('view', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            $admin_id = request('admin_id');

            $admin = $this->admins_model->find($admin_id);

            json_response($admin);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update an admin.
     */
    public function update(): void
    {
        try {
            if (cannot('edit', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            $admin = request('admin');

            $this->admins_model->only($admin, $this->allowed_admin_fields);

            $this->admins_model->only($admin['settings'], $this->allowed_admin_setting_fields);

            $admin_id = $this->admins_model->save($admin);

            $admin = $this->admins_model->find($admin_id);

            $this->webhooks_client->trigger(WEBHOOK_ADMIN_SAVE, $admin);

            json_response([
                'success' => true,
                'id' => $admin_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Remove an admin.
     */
    public function destroy(): void
    {
        try {
            if (cannot('delete', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            $admin_id = request('admin_id');

            $admin = $this->admins_model->find($admin_id);

            $this->admins_model->delete($admin_id);

            $this->webhooks_client->trigger(WEBHOOK_ADMIN_DELETE, $admin);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
