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
 * Services controller.
 *
 * Handles the services related operations.
 *
 * @package Controllers
 */
class Services extends EA_Controller {
    /**
     * Services constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('services_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
    }

    /**
     * Render the backend services page.
     *
     * On this page admin users will be able to manage services, which are eventually selected by customers during the 
     * booking process.
     */
    public function index()
    {
        session(['dest_url' => site_url('services')]);

        if (cannot('view', PRIV_SERVICES))
        {
            show_error('Forbidden', 403);
        }

        $user_id = session('user_id');

        $role_slug = session('role_slug');

        $this->load->view('pages/services', [
            'page_title' => lang('services'),
            'active_menu' => PRIV_SERVICES,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);
    }

    /**
     * Filter services by the provided keyword.
     */
    public function search()
    {
        try
        {
            if (cannot('view', PRIV_SERVICES))
            {
                show_error('Forbidden', 403);
            }

            $keyword = request('keyword', '');

            $order_by = 'name ASC';

            $limit = request('limit', 1000);
            
            $offset = 0;

            $services = $this->services_model->search($keyword, $limit, $offset, $order_by);

            json_response($services);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create a service.
     */
    public function create()
    {
        try
        {
            $service = json_decode(request('service'), TRUE);

            if (cannot('add', PRIV_SERVICES))
            {
                show_error('Forbidden', 403);
            }

            $service_id = $this->services_model->save($service);

            json_response([
                'success' => TRUE,
                'id' => $service_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update a service.
     */
    public function update()
    {
        try
        {
            $service = json_decode(request('service'), TRUE);

            if (cannot('edit', PRIV_SERVICES))
            {
                show_error('Forbidden', 403);
            }

            $service_id = $this->services_model->save($service);

            json_response([
                'success' => TRUE,
                'id' => $service_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Remove a service.
     */
    public function destroy()
    {
        try
        {
            if (cannot('delete', PRIV_SERVICES))
            {
                show_error('Forbidden', 403);
            }

            $service_id = request('service_id');

            $this->services_model->delete($service_id);

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
     * Find a service.
     */
    public function find()
    {
        try
        {
            if (cannot('delete', PRIV_SERVICES))
            {
                show_error('Forbidden', 403);
            }

            $service_id = request('service_id');

            $service = $this->services_model->find($service_id);

            json_response($service);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
