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
        $this->load->library('webhooks_client');
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

        $services = $this->services_model->get();

        foreach ($services as &$service)
        {
            $this->services_model->only($service, ['id', 'name']);
        }

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
            'company_working_plan' => setting('company_working_plan'),
            'date_format' => setting('date_format'),
            'time_format' => setting('time_format'),
            'first_weekday' => setting('first_weekday'),
            'min_password_length' => MIN_PASSWORD_LENGTH,
            'timezones' => $this->timezones->to_array(),
            'services' => $services,
        ]);

        html_vars([
            'page_title' => lang('providers'),
            'active_menu' => PRIV_USERS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'grouped_timezones' => $this->timezones->to_grouped_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
            'services' => $this->services_model->get(),
        ]);

        $this->load->view('pages/providers');
    }

    /**
     * Filter providers by the provided keyword.
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
            if (cannot('add', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $provider = request('provider');

            $this->providers_model->only($provider, [
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
                'services',
            ]);

            $this->providers_model->only($provider['settings'], [
                'username',
                'password',
                'working_plan',
                'working_plan_exceptions',
                'notifications',
                'calendar_view'
            ]);

            $this->providers_model->optional($provider, [
                'services' => [],
            ]);

            $provider_id = $this->providers_model->save($provider);

            $provider = $this->providers_model->find($provider_id);

            $this->webhooks_client->trigger(WEBHOOK_PROVIDER_SAVE, $provider);

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
            if (cannot('edit', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $provider = request('provider');

            $this->providers_model->only($provider, [
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
                'services',
            ]);

            $this->providers_model->only($provider['settings'], [
                'username',
                'password',
                'working_plan',
                'working_plan_exceptions',
                'notifications',
                'calendar_view'
            ]);

            $this->providers_model->optional($provider, [
                'services' => [],
            ]);

            $provider_id = $this->providers_model->save($provider);

            $provider = $this->providers_model->find($provider_id);

            $this->webhooks_client->trigger(WEBHOOK_PROVIDER_SAVE, $provider);

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
            if (cannot('delete', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $provider_id = request('provider_id');

            $provider = $this->providers_model->find($provider_id);

            $this->providers_model->delete($provider_id);

            $this->webhooks_client->trigger(WEBHOOK_PROVIDER_DELETE, $provider);

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
            if (cannot('view', PRIV_USERS))
            {
                abort(403, 'Forbidden');
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
