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
 * Customers controller.
 *
 * Handles the customers related operations.
 *
 * @package Controllers
 */
class Customers extends EA_Controller {
    /**
     * Customers constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('customers_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
    }

    /**
     * Render the backend customers page.
     *
     * On this page admin users will be able to manage customers, which are eventually selected by customers during the 
     * booking process.
     */
    public function index()
    {
        session(['dest_url' => site_url('customers')]);

        if (cannot('view', PRIV_USERS))
        {
            abort(403, 'Forbidden');
        }

        $user_id = session('user_id');

        $role_slug = session('role_slug');

        html_vars([
            'page_title' => lang('customers'),
            'active_menu' => PRIV_CUSTOMERS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/customers', html_vars());
    }

    /**
     * Filter customers by the provided keyword.
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

            $order_by = 'first_name ASC, last_name ASC, email ASC';

            $limit = request('limit', 1000);
            
            $offset = 0;

            $customers = $this->customers_model->search($keyword, $limit, $offset, $order_by);

            foreach ($customers as &$customer)
            {
                $appointments = $this->appointments_model->get(['id_users_customer' => $customer['id']]);

                foreach ($appointments as &$appointment)
                {
                    $this->appointments_model->load($appointment, [
                        'service',
                        'provider',
                    ]);
                }

                $customer['appointments'] = $appointments;
            }

            json_response($customers);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create a customer.
     */
    public function create()
    {
        try
        {
            $customer = json_decode(request('customer'), TRUE);

            if (cannot('add', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $customer_id = $this->customers_model->save($customer);

            json_response([
                'success' => TRUE,
                'id' => $customer_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update a customer.
     */
    public function update()
    {
        try
        {
            $customer = json_decode(request('customer'), TRUE);

            if (cannot('edit', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $customer_id = $this->customers_model->save($customer);

            json_response([
                'success' => TRUE,
                'id' => $customer_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Remove a customer.
     */
    public function destroy()
    {
        try
        {
            if (cannot('delete', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $customer_id = request('customer_id');

            $this->customers_model->delete($customer_id);

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
     * Find a customer.
     */
    public function find()
    {
        try
        {
            if (cannot('view', PRIV_USERS))
            {
                abort(403, 'Forbidden');
            }

            $customer_id = request('customer_id');

            $customer = $this->customers_model->find($customer_id);

            json_response($customer);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
