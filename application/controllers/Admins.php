<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
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
class Admins extends EA_Controller {
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
    }

    /**
     * Render the backend admins page.
     *
     * On this page admin users will be able to manage admins, which are eventually selected by customers during the
     * booking process.
     */
    public function index()
    {
        session(['dest_url' => site_url('admins')]);

        if (cannot('view', PRIV_USERS))
        {
            show_error('Forbidden', 403);
        }

        $user_id = session('user_id');

        $role_slug = session('role_slug');

        html_vars([
            'page_title' => lang('admins'),
            'active_menu' => PRIV_USERS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/admins', html_vars());
    }

    /**
     * Filter admins by the provided keyword.
     */
    public function search()
    {
        try
        {
            if (cannot('view', PRIV_USERS))
            {
                abort(403,'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = 'first_name ASC, last_name ASC, email ASC';

            $limit = request('limit', 1000);

            $offset = 0;

            $admins = $this->admins_model->search($keyword, $limit, $offset, $order_by);

            json_response($admins);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create an admin.
     */
    public function create()
    {
        try
        {
            $admin = json_decode(request('admin'), TRUE);

            if (cannot('add', PRIV_USERS))
            {
                show_error('Forbidden', 403);
            }

            $admin_id = $this->admins_model->save($admin);

            json_response([
                'success' => TRUE,
                'id' => $admin_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update an admin.
     */
    public function update()
    {
        try
        {
            $admin = json_decode(request('admin'), TRUE);

            if (cannot('edit', PRIV_USERS))
            {
                show_error('Forbidden', 403);
            }

            $admin_id = $this->admins_model->save($admin);

            json_response([
                'success' => TRUE,
                'id' => $admin_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Remove an admin.
     */
    public function destroy()
    {
        try
        {
            if (cannot('delete', PRIV_USERS))
            {
                show_error('Forbidden', 403);
            }

            $admin_id = request('admin_id');

            $this->admins_model->delete($admin_id);

            json_response([
                'success' => TRUE,
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Find an admin.
     */
    public function find()
    {
        try
        {
            if (cannot('view', PRIV_USERS))
            {
                show_error('Forbidden', 403);
            }

            $admin_id = request('admin_id');

            $admin = $this->admins_model->find($admin_id);

            json_response($admin);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
