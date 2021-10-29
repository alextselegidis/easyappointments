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
 * Backend API controller
 *
 * Handles the backend API related operations.
 *
 * @package Controllers
 */
class Backend_api extends EA_Controller {
    /**
     * @var array
     */
    protected $permissions;

    /**
     * Backend_api constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('admins_model');
        $this->load->model('appointments_model');
        $this->load->model('consents_model');
        $this->load->model('customers_model');
        $this->load->model('providers_model');
        $this->load->model('roles_model');
        $this->load->model('secretaries_model');
        $this->load->model('service_categories_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
        $this->load->model('users_model');

        $this->load->library('google_sync');
        $this->load->library('ics_file');
        $this->load->library('notifications');
        $this->load->library('synchronization');
        $this->load->library('timezones');

        $role_slug = session('role_slug');

        if ($role_slug)
        {
            $this->permissions = $this->roles_model->get_permissions_by_slug($role_slug);
        }
    }

    /**
     * Get Calendar Events
     *
     * This method will return all the calendar events within a specified period.
     */
    public function ajax_get_calendar_events()
    {
        try
        {
            $start_date = request('startDate') . ' 00:00:00';

            $end_date = request('endDate') . ' 23:59:59';

            $response = [
                'appointments' => $this->appointments_model->get([
                    'is_unavailable' => FALSE,
                    'start_datetime >=' => $start_date,
                    'end_datetime <=' => $end_date
                ]),
                'unavailability_events' => $this->appointments_model->get([
                    'is_unavailable' => TRUE,
                    'start_datetime >=' => $start_date,
                    'end_datetime <=' => $end_date
                ])
            ];

            foreach ($response['appointments'] as &$appointment)
            {
                $appointment['provider'] = $this->providers_model->find($appointment['id_users_provider']);
                $appointment['service'] = $this->services_model->find($appointment['id_services']);
                $appointment['customer'] = $this->customers_model->find($appointment['id_users_customer']);
            }

            unset($appointment);

            $user_id = session('user_id');

            $role_slug = session('role_slug');

            // If the current user is a provider he must only see his own appointments.
            if ($role_slug === DB_SLUG_PROVIDER)
            {
                foreach ($response['appointments'] as $index => $appointment)
                {
                    if ((int)$appointment['id_users_provider'] !== (int)$user_id)
                    {
                        unset($response['appointments'][$index]);
                    }
                }

                foreach ($response['unavailability_events'] as $index => $unavailability_event)
                {
                    if ((int)$unavailability_event['id_users_provider'] !== (int)$user_id)
                    {
                        unset($response['unavailability_events'][$index]);
                    }
                }
            }

            // If the current user is a secretary he must only see the appointments of his providers.
            if ($role_slug === DB_SLUG_SECRETARY)
            {
                $providers = $this->secretaries_model->find($user_id)['providers'];

                foreach ($response['appointments'] as $index => $appointment)
                {
                    if ( ! in_array((int)$appointment['id_users_provider'], $providers))
                    {
                        unset($response['appointments'][$index]);
                    }
                }

                foreach ($response['unavailability_events'] as $index => $unavailability_event)
                {
                    if ( ! in_array((int)$unavailability_event['id_users_provider'], $providers))
                    {
                        unset($response['unavailability_events'][$index]);
                    }
                }
            }

            json_response($response);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Get the registered appointments for the given date period and record.
     *
     * This method returns the database appointments and unavailable periods for the user selected date period and
     * record type (provider or service).
     */
    public function ajax_get_calendar_appointments()
    {
        try
        {
            if ($this->permissions[PRIV_APPOINTMENTS]['view'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $filter_type = request('filter_type');

            if ( ! $filter_type)
            {
                json_response([
                    'appointments' => [],
                    'unavailables' => []
                ]);

                return;
            }

            if ($filter_type == FILTER_TYPE_PROVIDER)
            {
                $where_id = 'id_users_provider';
            }
            else
            {
                $where_id = 'id_services';
            }

            // Get appointments
            $record_id = $this->db->escape(request('record_id'));
            $start_date = $this->db->escape(request('start_date'));
            $end_date = $this->db->escape(date('Y-m-d', strtotime(request('end_date') . ' +1 day')));

            $where_clause = $where_id . ' = ' . $record_id . '
                AND ((start_datetime > ' . $start_date . ' AND start_datetime < ' . $end_date . ') 
                or (end_datetime > ' . $start_date . ' AND end_datetime < ' . $end_date . ') 
                or (start_datetime <= ' . $start_date . ' AND end_datetime >= ' . $end_date . ')) 
                AND is_unavailable = 0
            ';

            $response['appointments'] = $this->appointments_model->get($where_clause);

            foreach ($response['appointments'] as &$appointment)
            {
                $appointment['provider'] = $this->providers_model->find($appointment['id_users_provider']);
                $appointment['service'] = $this->services_model->find($appointment['id_services']);
                $appointment['customer'] = $this->customers_model->find($appointment['id_users_customer']);
            }

            // Get unavailable periods (only for provider).
            $response['unavailables'] = [];

            if ($filter_type == FILTER_TYPE_PROVIDER)
            {
                $where_clause = $where_id . ' = ' . $record_id . '
                    AND ((start_datetime > ' . $start_date . ' AND start_datetime < ' . $end_date . ') 
                    or (end_datetime > ' . $start_date . ' AND end_datetime < ' . $end_date . ') 
                    or (start_datetime <= ' . $start_date . ' AND end_datetime >= ' . $end_date . ')) 
                    AND is_unavailable = 1
                ';

                $response['unavailables'] = $this->appointments_model->get($where_clause);
            }

            foreach ($response['unavailables'] as &$unavailable)
            {
                $unavailable['provider'] = $this->providers_model->find($unavailable['id_users_provider']);
            }

            json_response($response);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Save appointment changes that are made from the backend calendar page.
     */
    public function ajax_save_appointment()
    {
        try
        {
            // Save customer changes to the database.
            $customer_data = request('customer_data');

            if ($customer_data)
            {
                $customer = json_decode($customer_data, TRUE);

                $required_permissions = ( ! isset($customer['id']))
                    ? $this->permissions[PRIV_CUSTOMERS]['add']
                    : $this->permissions[PRIV_CUSTOMERS]['edit'];

                if ($required_permissions == FALSE)
                {
                    throw new Exception('You do not have the required permissions for this task.');
                }

                $customer['id'] = $this->customers_model->save($customer);
            }

            // Save appointment changes to the database.
            $appointment_data = request('appointment_data');

            $manage_mode = ! empty($appointment_data['id']);

            if ($appointment_data)
            {
                $appointment = json_decode($appointment_data, TRUE);

                $required_permissions = ( ! isset($appointment['id']))
                    ? $this->permissions[PRIV_APPOINTMENTS]['add']
                    : $this->permissions[PRIV_APPOINTMENTS]['edit'];

                if ($required_permissions == FALSE)
                {
                    throw new Exception('You do not have the required permissions for this task.');
                }

                // If the appointment does not contain the customer record id, then it means that is going to be
                // inserted. 
                if ( ! isset($appointment['id_users_customer']))
                {
                    $appointment['id_users_customer'] = $customer['id'] ?? $customer_data['id'];
                }

                $appointment['id'] = $this->appointments_model->save($appointment);
            }

            if (empty($appointment['id']))
            {
                throw new RuntimeException('The appointment ID is not available.');
            }

            $appointment = $this->appointments_model->find($appointment['id']);
            $provider = $this->providers_model->find($appointment['id_users_provider']);
            $customer = $this->customers_model->find($appointment['id_users_customer']);
            $service = $this->services_model->find($appointment['id_services']);

            $settings = [
                'company_name' => setting('company_name'),
                'company_link' => setting('company_link'),
                'company_email' => setting('company_email'),
                'date_format' => setting('date_format'),
                'time_format' => setting('time_format')
            ];

            $this->synchronization->sync_appointment_saved($appointment, $service, $provider, $customer, $settings, $manage_mode);

            $this->notifications->notify_appointment_saved($appointment, $service, $provider, $customer, $settings, $manage_mode);

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
     * Delete appointment from the database.
     *
     * This method deletes an existing appointment from the database. Once this action is finished it cannot be undone.
     * Notification emails are send to both provider and customer and the delete action is executed to the Google
     * Calendar account of the provider, if the "google_sync" setting is enabled.
     */
    public function ajax_delete_appointment()
    {
        try
        {
            if (cannot('delete', 'appointments'))
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $appointment_id = request('appointment_id');

            if (empty($appointment_id))
            {
                throw new Exception('No appointment id provided.');
            }

            // Store appointment data for later use in this method.
            $appointment = $this->appointments_model->find($appointment_id);
            $provider = $this->providers_model->find($appointment['id_users_provider']);
            $customer = $this->customers_model->find($appointment['id_users_customer']);
            $service = $this->services_model->find($appointment['id_services']);

            $settings = [
                'company_name' => setting('company_name'),
                'company_email' => setting('company_email'),
                'company_link' => setting('company_link'),
                'date_format' => setting('date_format'),
                'time_format' => setting('time_format')
            ];

            // Delete appointment record from the database.
            $this->appointments_model->delete($appointment_id);

            $this->notifications->notify_appointment_deleted($appointment, $service, $provider, $customer, $settings);

            $this->synchronization->sync_appointment_deleted($appointment, $provider);

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
     * Disable a providers sync setting.
     *
     * This method deletes the "google_sync" and "google_token" settings from the database.
     *
     * After that the provider's appointments will be no longer synced with Google Calendar.
     */
    public function ajax_disable_provider_sync()
    {
        try
        {
            $provider_id = request('provider_id');

            if ( ! $provider_id)
            {
                throw new Exception('Provider id not specified.');
            }

            $user_id = session('user_id');

            if (
                $this->permissions[PRIV_USERS]['edit'] === FALSE
                && (int)$user_id !== (int)$provider_id)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $this->providers_model->set_setting($provider_id, 'google_sync', FALSE);

            $this->providers_model->set_setting($provider_id, 'google_token', NULL);

            $this->appointments_model->clear_google_sync_ids($provider_id);

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
     * Filter the customer records with the given key string.
     */
    public function ajax_filter_customers()
    {
        try
        {
            if ($this->permissions[PRIV_CUSTOMERS]['view'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $key = $this->db->escape_str(request('key'));

            $key = strtoupper($key);

            $where =
                '(first_name LIKE upper("%' . $key . '%") OR ' .
                'last_name  LIKE upper("%' . $key . '%") OR ' .
                'email LIKE upper("%' . $key . '%") OR ' .
                'phone_number LIKE upper("%' . $key . '%") OR ' .
                'address LIKE upper("%' . $key . '%") OR ' .
                'city LIKE upper("%' . $key . '%") OR ' .
                'zip_code LIKE upper("%' . $key . '%") OR ' .
                'notes LIKE upper("%' . $key . '%"))';

            $order_by = 'first_name ASC, last_name ASC';

            $limit = request('limit');

            if ($limit === NULL)
            {
                $limit = 1000;
            }

            $customers = $this->customers_model->get($where, $limit, NULL, $order_by);

            foreach ($customers as &$customer)
            {
                $appointments = $this->appointments_model->get(['id_users_customer' => $customer['id']]);

                foreach ($appointments as &$appointment)
                {
                    $this->appointments_model->attach($appointment, [
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
     * Insert of update unavailable time period to database.
     */
    public function ajax_save_unavailable()
    {
        try
        {
            // Check privileges
            $unavailable = json_decode(request('unavailable'), TRUE);

            $required_permissions = ( ! isset($unavailable['id']))
                ? $this->permissions[PRIV_APPOINTMENTS]['add']
                : $this->permissions[PRIV_APPOINTMENTS]['edit'];

            if ( ! $required_permissions)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $provider = $this->providers_model->find($unavailable['id_users_provider']);

            // Add appointment
            $unavailable['id'] = $this->appointments_model->save_unavailable($unavailable);
            $unavailable = $this->appointments_model->find($unavailable['id']); // fetch all inserted data

            // Google Sync
            try
            {
                $google_sync = $this->providers_model->get_setting($unavailable['id_users_provider'], 'google_sync');

                if ($google_sync)
                {
                    $google_token = json_decode($this->providers_model->get_setting($unavailable['id_users_provider'], 'google_token'));

                    $this->google_sync->refresh_token($google_token->refresh_token);

                    if ($unavailable['id_google_calendar'] == NULL)
                    {
                        $google_event = $this->google_sync->add_unavailable($provider, $unavailable);
                        $unavailable['id_google_calendar'] = $google_event->id;
                        $this->appointments_model->save_unavailable($unavailable);
                    }
                    else
                    {
                        $this->google_sync->update_unavailable($provider, $unavailable);
                    }
                }
            }
            catch (Throwable $e)
            {
                $warnings[] = $e;
            }

            json_response([
                'success' => TRUE,
                'warnings' => $warnings ?? []
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Delete an unavailable time period from database.
     */
    public function ajax_delete_unavailable()
    {
        try
        {
            if ($this->permissions[PRIV_APPOINTMENTS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $unavailable_id = request('unavailable_id');

            $unavailable = $this->appointments_model->find($unavailable_id);

            $provider = $this->providers_model->find($unavailable['id_users_provider']);

            $this->appointments_model->delete($unavailable['id']);

            // Google Sync
            try
            {
                $google_sync = $this->providers_model->get_setting($provider['id'], 'google_sync');

                if ($google_sync == TRUE)
                {
                    $google_token = json_decode($this->providers_model->get_setting($provider['id'], 'google_token'));

                    $this->google_sync->refresh_token($google_token->refresh_token);

                    $this->google_sync->delete_unavailable($provider, $unavailable['id_google_calendar']);
                }
            }
            catch (Throwable $e)
            {
                $warnings[] = $e;
            }

            json_response([
                'success' => TRUE,
                'warnings' => $warnings ?? []
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Insert of update working plan exceptions to database.
     */
    public function ajax_save_working_plan_exception()
    {
        try
        {
            $required_permissions = $this->permissions[PRIV_USERS]['edit'];

            if ( ! $required_permissions)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $date = request('date');

            $working_plan_exception = request('working_plan_exception');

            $provider_id = request('provider_id');

            $this->providers_model->save_working_plan_exception($provider_id, $date, $working_plan_exception);

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
     * Delete a working plan exceptions time period to database.
     */
    public function ajax_delete_working_plan_exception()
    {
        try
        {
            $required_permissions = $this->permissions[PRIV_USERS]['edit'];

            if ( ! $required_permissions)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $date = request('date');

            $provider_id = request('provider_id');

            $this->providers_model->delete_working_plan_exception($provider_id, $date);

            json_response([
                'success' => TRUE
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Save (insert or update) a customer record.
     */
    public function ajax_save_customer()
    {
        try
        {
            $customer = json_decode(request('customer'), TRUE);

            $required_permissions = ( ! isset($customer['id']))
                ? $this->permissions[PRIV_CUSTOMERS]['add']
                : $this->permissions[PRIV_CUSTOMERS]['edit'];

            if ( ! $required_permissions)
            {
                throw new Exception('You do not have the required permissions for this task.');
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
     * Delete customer from database.
     */
    public function ajax_delete_customer()
    {
        try
        {
            if ($this->permissions[PRIV_CUSTOMERS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $this->customers_model->delete(request('customer_id'));

            response();
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Save (insert or update) service record.
     */
    public function ajax_save_service()
    {
        try
        {
            $service = json_decode(request('service'), TRUE);

            $required_permissions = ( ! isset($service['id']))
                ? $this->permissions[PRIV_SERVICES]['add']
                : $this->permissions[PRIV_SERVICES]['edit'];

            if ( ! $required_permissions)
            {
                throw new Exception('You do not have the required permissions for this task.');
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
     * Delete service record from database.
     */
    public function ajax_delete_service()
    {
        try
        {
            if ($this->permissions[PRIV_SERVICES]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
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
     * Filter service records by given key string.
     */
    public function ajax_filter_services()
    {
        try
        {
            if ($this->permissions[PRIV_SERVICES]['view'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $key = $this->db->escape_str(request('key'));

            $where =
                '(name LIKE "%' . $key . '%" OR duration LIKE "%' . $key . '%" OR ' .
                'price LIKE "%' . $key . '%" OR currency LIKE "%' . $key . '%" OR ' .
                'description LIKE "%' . $key . '%")';

            $services = $this->services_model->get($where);

            json_response($services);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Save (insert or update) category record.
     */
    public function ajax_save_service_category()
    {
        try
        {
            $category = json_decode(request('category'), TRUE);

            $required_permissions = ( ! isset($category['id']))
                ? $this->permissions[PRIV_SERVICES]['add']
                : $this->permissions[PRIV_SERVICES]['edit'];

            if ( ! $required_permissions)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $category_id = $this->service_categories_model->save($category);

            json_response([
                'success' => TRUE,
                'id' => $category_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Delete category record from database.
     */
    public function ajax_delete_service_category()
    {
        try
        {
            if ($this->permissions[PRIV_SERVICES]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $service_category_id = request('category_id');

            $this->service_categories_model->delete($service_category_id);

            json_response([
                'success' => TRUE
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Filter services categories with key string.
     */
    public function ajax_filter_service_categories()
    {
        try
        {
            if ($this->permissions[PRIV_SERVICES]['view'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $key = $this->db->escape_str(request('key'));

            $where = '(name LIKE "%' . $key . '%" OR description LIKE "%' . $key . '%")';

            $service_categories = $this->service_categories_model->get($where);

            json_response($service_categories);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Filter admin records with string key.
     */
    public function ajax_filter_admins()
    {
        try
        {
            if ($this->permissions[PRIV_USERS]['view'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $key = $this->db->escape_str(request('key'));

            $where =
                '(first_name LIKE "%' . $key . '%" OR last_name LIKE "%' . $key . '%" ' .
                'OR email LIKE "%' . $key . '%" OR mobile_number LIKE "%' . $key . '%" ' .
                'OR phone_number LIKE "%' . $key . '%" OR address LIKE "%' . $key . '%" ' .
                'OR city LIKE "%' . $key . '%" OR state LIKE "%' . $key . '%" ' .
                'OR zip_code LIKE "%' . $key . '%" OR notes LIKE "%' . $key . '%")';

            $admins = $this->admins_model->get($where);

            json_response($admins);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Save (insert or update) admin record into database.
     */
    public function ajax_save_admin()
    {
        try
        {
            $admin = json_decode(request('admin'), TRUE);

            $required_permissions = ( ! isset($admin['id']))
                ? $this->permissions[PRIV_USERS]['add']
                : $this->permissions[PRIV_USERS]['edit'];
            if ( ! $required_permissions)
            {
                throw new Exception('You do not have the required permissions for this task.');
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
     * Delete an admin record from the database.
     */
    public function ajax_delete_admin()
    {
        try
        {
            if ($this->permissions[PRIV_USERS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $admin_id = request('admin_id');

            $this->admins_model->delete($admin_id);

            json_response([
                'success' => TRUE
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Filter provider records with string key.
     */
    public function ajax_filter_providers()
    {
        try
        {
            if ($this->permissions[PRIV_USERS]['view'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $key = $this->db->escape_str(request('key'));

            $where =
                '(first_name LIKE "%' . $key . '%" OR last_name LIKE "%' . $key . '%" ' .
                'OR email LIKE "%' . $key . '%" OR mobile_number LIKE "%' . $key . '%" ' .
                'OR phone_number LIKE "%' . $key . '%" OR address LIKE "%' . $key . '%" ' .
                'OR city LIKE "%' . $key . '%" OR state LIKE "%' . $key . '%" ' .
                'OR zip_code LIKE "%' . $key . '%" OR notes LIKE "%' . $key . '%")';

            $providers = $this->providers_model->get($where);

            json_response($providers);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Save (insert or update) a provider record into database.
     */
    public function ajax_save_provider()
    {
        try
        {
            $provider = json_decode(request('provider'), TRUE);

            $required_permissions = ( ! isset($provider['id']))
                ? $this->permissions[PRIV_USERS]['add']
                : $this->permissions[PRIV_USERS]['edit'];

            if ( ! $required_permissions)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            if (empty($provider['settings']['working_plan']))
            {
                $provider['settings']['working_plan'] = setting('company_working_plan');
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
     * Delete a provider record from the database.
     */
    public function ajax_delete_provider()
    {
        try
        {
            if ($this->permissions[PRIV_USERS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $provider_id = request('provider_id');

            $this->providers_model->delete($provider_id);

            json_response([
                'success' => TRUE
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Filter secretary records with string key.
     */
    public function ajax_filter_secretaries()
    {
        try
        {
            if ($this->permissions[PRIV_USERS]['view'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $key = $this->db->escape_str(request('key'));

            $where =
                '(first_name LIKE "%' . $key . '%" OR last_name LIKE "%' . $key . '%" ' .
                'OR email LIKE "%' . $key . '%" OR mobile_number LIKE "%' . $key . '%" ' .
                'OR phone_number LIKE "%' . $key . '%" OR address LIKE "%' . $key . '%" ' .
                'OR city LIKE "%' . $key . '%" OR state LIKE "%' . $key . '%" ' .
                'OR zip_code LIKE "%' . $key . '%" OR notes LIKE "%' . $key . '%")';

            $secretaries = $this->secretaries_model->get($where);

            json_response($secretaries);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Save (insert or update) a secretary record into database.
     */
    public function ajax_save_secretary()
    {
        try
        {
            $secretary = json_decode(request('secretary'), TRUE);

            $required_permissions = ( ! isset($secretary['id']))
                ? $this->permissions[PRIV_USERS]['add']
                : $this->permissions[PRIV_USERS]['edit'];
            if ( ! $required_permissions)
            {
                throw new Exception('You do not have the required permissions for this task.');
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
     * Delete a secretary record from the database.
     */
    public function ajax_delete_secretary()
    {
        try
        {
            if ($this->permissions[PRIV_USERS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $secretary_id = request('secretary_id');

            $this->secretaries_model->delete($secretary_id);

            json_response([
                'success' => TRUE
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Save a setting or multiple settings in the database.
     */
    public function ajax_save_settings()
    {
        try
        {
            $type = request('type');

            if ($type == SETTINGS_SYSTEM)
            {
                if ($this->permissions[PRIV_SYSTEM_SETTINGS]['edit'] == FALSE)
                {
                    throw new Exception('You do not have the required permissions for this task.');
                }

                $settings = json_decode(request('settings', FALSE), TRUE);

                // Check if phone number settings are valid.

                $phone_number_required = FALSE;

                $phone_number_shown = FALSE;

                foreach ($settings as $setting)
                {
                    if ($setting['name'] === 'require_phone_number')
                    {
                        $phone_number_required = $setting['value'];
                    }

                    if ($setting['name'] === 'show_phone_number')
                    {
                        $phone_number_shown = $setting['value'];
                    }
                }

                if ($phone_number_required && ! $phone_number_shown)
                {
                    throw new RuntimeException('You cannot hide the phone number in the booking form while it\'s also required!');
                }

                foreach ($settings as $setting)
                {
                    $existing_setting = $this->settings_model->query()->where('name', $setting['name'])->get()->row_array();

                    if ( ! empty($existing_setting))
                    {
                        $setting['id'] = $existing_setting['id'];
                    }

                    $this->settings_model->save($setting);
                }
            }
            else if ($type == SETTINGS_USER)
            {
                if ($this->permissions[PRIV_USER_SETTINGS]['edit'] == FALSE)
                {
                    throw new Exception('You do not have the required permissions for this task.');
                }

                $settings = json_decode(request('settings'), TRUE);

                $this->users_model->save($settings);

                session([
                    'user_email' => $settings['email'],
                    'username' => $settings['settings']['username'],
                    'timezone' => $settings['timezone'],
                ]);
            }

            response();
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * This method checks whether the username already exists in the database.
     */
    public function ajax_validate_username()
    {
        try
        {
            // We will only use the function in the admins_model because it is sufficient for the rest user types for
            // now (providers, secretaries).

            $username = request('username');

            $user_id = request('user_id');

            $is_valid = $this->admins_model->validate_username($username, $user_id);

            json_response([
                'is_valid' => $is_valid,
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Change system language for current user.
     *
     * The language setting is stored in session data and retrieved every time the user visits any of the system pages.
     */
    public function ajax_change_language()
    {
        try
        {
            // Check if language exists in the available languages.
            $found = FALSE;

            foreach (config('available_languages') as $lang)
            {
                if ($lang == request('language'))
                {
                    $found = TRUE;
                    break;
                }
            }

            if ( ! $found)
            {
                throw new Exception('Translations for the given language does not exist (' . request('language') . ').');
            }

            $language = request('language');

            session(['language' => $language]);

            config(['language' => $language]);

            json_response([
                'success' => TRUE
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * This method will return a list of the available Google Calendars.
     *
     * The user will need to select a specific calendar from this list to sync his appointments with. Google access must
     * be already granted for the specific provider.
     */
    public function ajax_get_google_calendars()
    {
        try
        {
            if ( ! request('provider_id'))
            {
                throw new Exception('Provider id is required in order to fetch the google calendars.');
            }

            // Check if selected provider has sync enabled.
            $provider_id = request('provider_id');

            $google_sync = $this->providers_model->get_setting($provider_id, 'google_sync');

            if ( ! $google_sync)
            {
                json_response([
                    'success' => FALSE
                ]);

                return;
            }

            $google_token = json_decode($this->providers_model->get_setting($provider_id, 'google_token'));

            $this->google_sync->refresh_token($google_token->refresh_token);

            $calendars = $this->google_sync->get_google_calendars();

            json_response($calendars);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Select a specific google calendar for a provider.
     *
     * All the appointments will be synced with this particular calendar.
     */
    public function ajax_select_google_calendar()
    {
        try
        {
            $provider_id = request('provider_id');

            $user_id = session('user_id');

            if ($this->permissions[PRIV_USERS]['edit'] == FALSE && (int)$user_id !== (int)$provider_id)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $calendar_id = request('calendar_id');

            $this->providers_model->set_setting($provider_id, 'google_calendar', $calendar_id);

            json_response([
                'success' => TRUE
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Apply global working plan to all providers.
     */
    public function ajax_apply_global_working_plan()
    {
        try
        {
            if ($this->permissions[PRIV_SYSTEM_SETTINGS]['edit'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $working_plan = request('working_plan');

            $providers = $this->providers_model->get();

            foreach ($providers as $provider)
            {
                $this->providers_model->set_setting($provider['id'], 'working_plan', $working_plan);
            }

            response();
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
