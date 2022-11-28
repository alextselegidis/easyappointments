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
        $this->load->library('webhooks_client');
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

        $user_id = session('user_id');

        if (cannot('view', PRIV_SERVICES))
        {
            if ($user_id)
            {
                abort(403, 'Forbidden');
            }

            redirect('login');

            return;
        }

        $role_slug = session('role_slug');

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
            'event_minimum_duration' => EVENT_MINIMUM_DURATION,
        ]);

        html_vars([
            'page_title' => lang('services'),
            'active_menu' => PRIV_SERVICES,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/services');
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
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = 'row_order ASC, update_datetime DESC';

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
            if (cannot('add', PRIV_SERVICES))
            {
                abort(403, 'Forbidden');
            }

            $service = request('service');

            $service['id_categories'] = $service['id_categories'] ?: NULL;

            $service_id = $this->services_model->save($service);
            
            $service = $this->services_model->find($service_id); 
            
            $this->webhooks_client->trigger(WEBHOOK_SERVICE_SAVE, $service);

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
            if (cannot('edit', PRIV_SERVICES))
            {
                abort(403, 'Forbidden');
            }

            $service = request('service');

            $service['id_categories'] = $service['id_categories'] ?: NULL;

            $service_id = $this->services_model->save($service);

            $service = $this->services_model->find($service_id);

            $this->webhooks_client->trigger(WEBHOOK_SERVICE_SAVE, $service);

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
                abort(403, 'Forbidden');
            }

            $service_id = request('service_id');

            $service = $this->services_model->find($service_id);

            $this->services_model->delete($service_id);

            $this->webhooks_client->trigger(WEBHOOK_SERVICE_DELETE, $service);

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
                abort(403, 'Forbidden');
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

    /**
     * Arrange service order
     */
    public function sort()
    {
        try {

            if (cannot('edit', PRIV_SERVICES))
            {
                abort(403, 'Forbidden');
            }

            $service_id = request('service_id');
            if (($service_id = filter_var($service_id, FILTER_VALIDATE_INT)) === FALSE)
            {
                abort(400,'Invalid ID value');
            }

            $insertAfterId = request('after');
            if (($insertAfterId = filter_var($insertAfterId,FILTER_VALIDATE_INT)) === FALSE)
            {
                abort(400,'Invalid after value, must be ID or -1');
            }

            if ($insertAfterId <= 0)
            {
                $insertAfterId = FALSE;
            }

            $service = $this->services_model->find($service_id);

            $service['row_order'] = $this->services_model->set_service_order($service['id'], $insertAfterId);

            return json_response($service);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}