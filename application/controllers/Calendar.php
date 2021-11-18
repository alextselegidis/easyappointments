<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Calendar controller. 
 * 
 * Handles calendar related operations.
 *
 * @package Controllers 
 */
class Calendar extends EA_Controller {
    /**
     * @var array
     */
    protected $permissions;
    
    /**
     * Calendar constructor.
     */
    public function __construct()
    {
        parent::__construct(); 
        
        $this->load->model('appointments_model'); 
        $this->load->model('customers_model'); 
        $this->load->model('services_model'); 
        $this->load->model('providers_model');

        $this->load->library('google_sync');
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
            $unavailable['id'] = $this->unavailabilities_model->save($unavailable);

            $unavailable = $this->unavailabilities_model->find($unavailable['id']); // fetch all inserted data

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
                        $this->unavailabilities_model->save($unavailable);
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
}
