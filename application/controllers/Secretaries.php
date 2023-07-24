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
        $this->load->library('webhooks_client');
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

        $user_id = session('user_id');
        
        if (cannot('view', PRIV_USERS))
        {
            if ($user_id)
            {
                abort(403, 'Forbidden');
            }

            redirect('login');

            return;
        }

        $role_slug = session('role_slug');

        $providers = $this->providers_model->get();

        foreach ($providers as &$provider)
        {
            $this->providers_model->only($provider, [
                'id',
                'first_name',
                'last_name'
            ]);
        }

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
            'timezones' => $this->timezones->to_array(),
            'min_password_length' => MIN_PASSWORD_LENGTH,
            'providers' => $providers,
        ]);

        html_vars([
            'page_title' => lang('secretaries'),
            'active_menu' => PRIV_USERS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'grouped_timezones' => $this->timezones->to_grouped_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
            'providers' => $this->providers_model->get(),
        ]);

        $this->load->view('pages/secretaries');
    }

    /**
     * Filter secretaries by the provided keyword.
     */
    public function search()
    {
        try
        {
            if (cannot('view', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = 'update_datetime DESC';

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
            if (cannot('add', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $secretary = request('secretary');

            $this->secretaries_model->only($secretary, [
                'first_name',
                'last_name',
                'email',
                'alt_number',
                'phone_number',
                'address',
                'city',
                'state',
                'zip_code',
                'notes',
                'timezone',
                'language',
                'is_private',
                'id_roles',
                'settings',
                'providers',
            ]);

            $this->secretaries_model->only($secretary['settings'], [
                'username',
                'password',
                'notifications',
                'calendar_view'
            ]);

            $this->secretaries_model->optional($secretary, [
                'providers' => [],
            ]);

            $secretary_id = $this->secretaries_model->save($secretary);

            $secretary = $this->secretaries_model->find($secretary_id);

            $this->webhooks_client->trigger(WEBHOOK_SECRETARY_SAVE, $secretary);

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
            if (cannot('edit', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $secretary = request('secretary');
            
            $this->secretaries_model->only($secretary, [
                'id',
                'first_name',
                'last_name',
                'email',
                'alt_number',
                'phone_number',
                'address',
                'city',
                'state',
                'zip_code',
                'notes',
                'timezone',
                'language',
                'is_private',
                'id_roles',
                'settings',
                'providers',
            ]);

            $this->secretaries_model->only($secretary['settings'], [
                'username',
                'password',
                'notifications',
                'calendar_view'
            ]);

            $this->secretaries_model->optional($secretary, [
                'providers' => [],
            ]);


            $secretary_id = $this->secretaries_model->save($secretary);

            $secretary = $this->secretaries_model->find($secretary_id);

            $this->webhooks_client->trigger(WEBHOOK_SECRETARY_SAVE, $secretary);

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
            if (cannot('delete', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $secretary_id = request('secretary_id');

            $secretary = $this->secretaries_model->find($secretary_id);

            $this->secretaries_model->delete($secretary_id);

            $this->webhooks_client->trigger(WEBHOOK_SECRETARY_DELETE, $secretary);

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
     * Find a secretary.
     */
    public function find()
    {
        try
        {
            if (cannot('view', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $secretary_id = request('secretary_id');

            $secretary = $this->secretaries_model->find($secretary_id);

            json_response($secretary);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
