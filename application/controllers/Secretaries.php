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
 * Secretaries controller.
 *
 * Handles the secretaries related operations.
 *
 * @package Controllers
 */
class Secretaries extends EA_Controller {
    /**
     * Secretaries constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('secretaries_model');
        $this->load->model('providers_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
    }

    /**
     * Render the backend secretaries page.
     *
     * On this page secretary users will be able to manage secretaries, which are eventually selected by customers during the 
     * booking process.
     */
    public function index()
    {
        session(['dest_url' => site_url('secretaries')]);

        if (cannot('view', 'users'))
        {
            show_error('Forbidden', 403);
        }

        $user_id = session('user_id');

        $role_slug = session('role_slug');

        $this->load->view('pages/secretaries/secretaries_page', [
            'page_title' => lang('secretaries'),
            'active_menu' => PRIV_USERS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
            'providers' => $this->providers_model->get(),
        ]);
    }

    /**
     * Filter secretaries by the provided keyword.
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

            $secretaries = $this->secretaries_model->search($keyword, $limit, $offset, $order_by);

            json_response($secretaries);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create a secretary.
     */
    public function create()
    {
        try
        {
            $secretary = json_decode(request('secretary'), TRUE);

            if (cannot('add', 'users'))
            {
                show_error('Forbidden', 403);
            }

            $secretary_id = $this->secretaries_model->save($secretary);

            json_response([
                'success' => TRUE,
                'id' => $secretary_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update a secretary.
     */
    public function update()
    {
        try
        {
            $secretary = json_decode(request('secretary'), TRUE);

            if (cannot('edit', 'users'))
            {
                show_error('Forbidden', 403);
            }

            $secretary_id = $this->secretaries_model->save($secretary);

            json_response([
                'success' => TRUE,
                'id' => $secretary_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Remove a secretary.
     */
    public function destroy()
    {
        try
        {
            if (cannot('delete', 'users'))
            {
                show_error('Forbidden', 403);
            }

            $secretary_id = request('secretary_id');

            $this->secretaries_model->delete($secretary_id);

            json_response([
                'success' => TRUE,
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
