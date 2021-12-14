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
 * Providers controller.
 *
 * Handles the providers related operations.
 *
 * @package Controllers
 */
class Providers extends EA_Controller {
    /**
     * Providers constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
    }

    /**
     * Render the backend providers page.
     *
     * On this page admin users will be able to manage providers, which are eventually selected by customers during the 
     * booking process.
     */
    public function index()
    {
        session(['dest_url' => site_url('providers')]);

        if (cannot('view', 'users'))
        {
            show_error('Forbidden', 403);
        }

        $user_id = session('user_id');

        $role_slug = session('role_slug');

        $this->load->view('pages/providers_page', [
            'page_title' => lang('providers'),
            'active_menu' => PRIV_USERS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
            'services' => $this->services_model->get(),
        ]);
    }

    /**
     * Filter providers by the provided keyword.
     */
    public function search()
    {
        try
        {
            if (cannot('view', 'users'))
            {
                show_error('Forbidden', 403);
            }

            $keyword = request('keyword', '');

            $order_by = 'first_name ASC, last_name ASC, email ASC';

            $limit = request('limit', 1000);
            
            $offset = 0;

            $providers = $this->providers_model->search($keyword, $limit, $offset, $order_by);

            json_response($providers);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create a provider.
     */
    public function create()
    {
        try
        {
            $provider = json_decode(request('provider'), TRUE);

            if (cannot('add', 'users'))
            {
                show_error('Forbidden', 403);
            }

            $provider_id = $this->providers_model->save($provider);

            json_response([
                'success' => TRUE,
                'id' => $provider_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update a provider.
     */
    public function update()
    {
        try
        {
            $provider = json_decode(request('provider'), TRUE);

            if (cannot('edit', 'users'))
            {
                show_error('Forbidden', 403);
            }

            $provider_id = $this->providers_model->save($provider);

            json_response([
                'success' => TRUE,
                'id' => $provider_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Remove a provider.
     */
    public function destroy()
    {
        try
        {
            if (cannot('delete', 'users'))
            {
                show_error('Forbidden', 403);
            }

            $provider_id = request('provider_id');

            $this->providers_model->delete($provider_id);

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
     * Find a provider.
     */
    public function find()
    {
        try
        {
            if (cannot('delete', PRIV_USERS))
            {
                show_error('Forbidden', 403);
            }

            $provider_id = request('provider_id');

            $provider = $this->providers_model->find($provider_id);

            json_response($provider);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
