<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
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
     * Calendar constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('unavailabilities_model');
        $this->load->model('customers_model');
        $this->load->model('services_model');
        $this->load->model('providers_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('google_sync');
        $this->load->library('notifications');
        $this->load->library('synchronization');
        $this->load->library('timezones');
        $this->load->library('webhooks_client');
    }

    /**
     * Display the main backend page.
     *
     * This method displays the main backend page. All login permission can view this page which displays a calendar
     * with the events of the selected provider or service. If a user has more privileges he will see more menus at the
     * top of the page.
     *
     * @param string $appointment_hash Appointment hash.
     */
    public function index(string $appointment_hash = '')
    {
        session(['dest_url' => site_url('backend/index' . (! empty($appointment_hash) ? '/' . $appointment_hash : ''))]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_APPOINTMENTS))
        {
            if ($user_id)
            {
                abort(403, 'Forbidden');
            }

            redirect('login');

            return;
        }

        $role_slug = session('role_slug');

        $user = $this->users_model->find($user_id);

        $secretary_providers = [];

        if ($role_slug === DB_SLUG_SECRETARY)
        {
            $secretary = $this->secretaries_model->find(session('user_id'));

            $secretary_providers = $secretary['providers'];
        }

        $edit_appointment = NULL;

        if ( ! empty($appointment_hash))
        {
            $occurrences = $this->appointments_model->get(['hash' => $appointment_hash]);

            if ($appointment_hash !== '' && ! empty($occurrences))
            {
                $edit_appointment = $occurrences[0];

                $this->appointments_model->load($edit_appointment, ['customer']);
            }
        }

        $privileges = $this->roles_model->get_permissions_by_slug($role_slug);

        $available_providers = $this->providers_model->get_available_providers();

        if ($role_slug === DB_SLUG_PROVIDER)
        {
            $available_providers = array_values(array_filter($available_providers, function ($available_provider) use ($user_id) {
                return (int)$available_provider['id'] === (int)$user_id;
            }));
        }

        if ($role_slug === DB_SLUG_SECRETARY)
        {
            $available_providers = array_values(array_filter($available_providers, function ($available_provider) use ($secretary_providers) {
                return in_array($available_provider['id'], $secretary_providers);
            }));
        }

        $available_services = $this->services_model->get_available_services();

        $calendar_view = request('view', $user['settings']['calendar_view']);

        $appointment_status_options = setting('appointment_status_options');

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
            'date_format' => setting('date_format'),
            'time_format' => setting('time_format'),
            'first_weekday' => setting('first_weekday'),
            'company_working_plan' => setting('company_working_plan'),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $privileges,
            'calendar_view' => $calendar_view,
            'available_providers' => $available_providers,
            'available_services' => $available_services,
            'secretary_providers' => $secretary_providers,
            'edit_appointment' => $edit_appointment,
            'customers' => $this->customers_model->get(NULL, 50, NULL, 'update_datetime DESC'),
        ]);

        html_vars([
            'page_title' => lang('calendar'),
            'active_menu' => PRIV_APPOINTMENTS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezone' => session('timezone'),
            'timezones' => $this->timezones->to_array(),
            'grouped_timezones' => $this->timezones->to_grouped_array(),
            'privileges' => $privileges,
            'calendar_view' => $calendar_view,
            'available_providers' => $available_providers,
            'available_services' => $available_services,
            'secretary_providers' => $secretary_providers,
            'appointment_status_options' => json_decode($appointment_status_options, TRUE) ?? [],
            'require_first_name' => setting('require_first_name'),
            'require_last_name' => setting('require_last_name'),
            'require_email' => setting('require_email'),
            'require_phone_number' => setting('require_phone_number'),
            'require_address' => setting('require_address'),
            'require_city' => setting('require_city'),
            'require_zip_code' => setting('require_zip_code'),
            'require_notes' => setting('require_notes'),
        ]);

        $this->load->view('pages/calendar');
    }

    /**
     * Render the calendar page and display the selected appointment.
     *
     * This method will call the "index" callback to handle the page rendering.
     *
     * @param string $appointment_hash Appointment hash.
     */
    public function reschedule(string $appointment_hash)
    {
        $this->index($appointment_hash);
    }

    /**
     * Save appointment changes that are made from the backend calendar page.
     */
    public function save_appointment()
    {
        try
        {
            // Save customer changes to the database.
            $customer_data = request('customer_data');

            if ($customer_data)
            {
                $customer = $customer_data;

                $required_permissions = ! empty($customer['id'])
                    ? can('add', PRIV_CUSTOMERS)
                    : can('edit', PRIV_CUSTOMERS);

                if ( ! $required_permissions)
                {
                    throw new RuntimeException('You do not have the required permissions for this task.');
                }

                $this->customers_model->only($customer, [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone_number',
                    'address',
                    'city',
                    'state',
                    'zip_code',
                    'timezone',
                    'language',
                ]);

                $customer['id'] = $this->customers_model->save($customer);
            }

            // Save appointment changes to the database.
            $appointment_data = request('appointment_data');

            $manage_mode = ! empty($appointment_data['id']);

            if ($appointment_data)
            {
                $appointment = $appointment_data;

                $required_permissions = ! empty($appointment['id'])
                    ? can('add', PRIV_APPOINTMENTS)
                    : can('edit', PRIV_APPOINTMENTS);

                if ( ! $required_permissions)
                {
                    throw new RuntimeException('You do not have the required permissions for this task.');
                }

                // If the appointment does not contain the customer record id, then it means that is going to be
                // inserted. 
                if ( ! isset($appointment['id_users_customer']))
                {
                    $appointment['id_users_customer'] = $customer['id'] ?? $customer_data['id'];
                }

                if ($manage_mode && ! empty($appointment['id']))
                {
                    $this->synchronization->remove_appointment_on_provider_change($appointment['id']);
                }

                $this->appointments_model->only($appointment, [
                    'id',
                    'start_datetime',
                    'end_datetime',
                    'location',
                    'notes',
                    'color',
                    'status',
                    'is_unavailability',
                    'id_users_provider',
                    'id_users_customer',
                    'id_services',
                ]);

                $appointment['id'] = $this->appointments_model->save($appointment);
            }

            if (empty($appointment['id']))
            {
                throw new RuntimeException('The appointment ID is not available.');
            }

            $appointment = $this->appointments_model->find($appointment['id']);
            $provider = $this->providers_model->find($appointment['id_users_provider'], TRUE);
            $customer = $this->customers_model->find($appointment['id_users_customer'], TRUE);
            $service = $this->services_model->find($appointment['id_services'], TRUE);

            $settings = [
                'company_name' => setting('company_name'),
                'company_link' => setting('company_link'),
                'company_email' => setting('company_email'),
                'date_format' => setting('date_format'),
                'time_format' => setting('time_format')
            ];

            $this->synchronization->sync_appointment_saved($appointment, $service, $provider, $customer, $settings);

            $this->notifications->notify_appointment_saved($appointment, $service, $provider, $customer, $settings, $manage_mode);

            $this->webhooks_client->trigger(WEBHOOK_APPOINTMENT_SAVE, $appointment);

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
    public function delete_appointment()
    {
        try
        {
            if (cannot('delete', 'appointments'))
            {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $appointment_id = request('appointment_id');

            if (empty($appointment_id))
            {
                throw new InvalidArgumentException('No appointment id provided.');
            }

            // Store appointment data for later use in this method.
            $appointment = $this->appointments_model->find($appointment_id);
            $provider = $this->providers_model->find($appointment['id_users_provider'], TRUE);
            $customer = $this->customers_model->find($appointment['id_users_customer'], TRUE);
            $service = $this->services_model->find($appointment['id_services'], TRUE);

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

            $this->webhooks_client->trigger(WEBHOOK_APPOINTMENT_DELETE, $appointment);

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
     * Insert of update unavailability to database.
     */
    public function save_unavailability()
    {
        try
        {
            // Check privileges
            $unavailability = request('unavailability');

            $required_permissions = ( ! isset($unavailability['id']))
                ? can('add', PRIV_APPOINTMENTS)
                : can('edit', PRIV_APPOINTMENTS);

            if ( ! $required_permissions)
            {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $provider = $this->providers_model->find($unavailability['id_users_provider']);

            $unavailability_id = $this->unavailabilities_model->save($unavailability);

            $unavailability = $this->unavailabilities_model->find($unavailability_id);

            $this->synchronization->sync_unavailability_saved($unavailability, $provider);

            $this->webhooks_client->trigger(WEBHOOK_UNAVAILABILITY_SAVE, $unavailability);

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
     * Delete an unavailability from database.
     */
    public function delete_unavailability()
    {
        try
        {
            if (cannot('delete', PRIV_APPOINTMENTS))
            {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $unavailability_id = request('unavailability_id');

            $unavailability = $this->appointments_model->find($unavailability_id);

            $provider = $this->providers_model->find($unavailability['id_users_provider']);

            $this->unavailabilities_model->delete($unavailability_id);

            $this->synchronization->sync_unavailability_deleted($unavailability, $provider);

            $this->webhooks_client->trigger(WEBHOOK_UNAVAILABILITY_DELETE, $unavailability);

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
     * Insert of update working plan exceptions to database.
     */
    public function save_working_plan_exception()
    {
        try
        {
            if (cannot('edit', PRIV_USERS))
            {
                throw new RuntimeException('You do not have the required permissions for this task.');
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
    public function delete_working_plan_exception()
    {
        try
        {
            $required_permissions = can('edit', PRIV_CUSTOMERS);

            if ( ! $required_permissions)
            {
                throw new RuntimeException('You do not have the required permissions for this task.');
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
    public function get_calendar_appointments_for_table_view()
    {
        try
        {
            $required_permissions = can('view', PRIV_APPOINTMENTS);

            if ( ! $required_permissions)
            {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $start_date = request('start_date') . ' 00:00:00';

            $end_date = request('end_date') . ' 23:59:59';

            $response = [
                'appointments' => $this->appointments_model->get([
                    'start_datetime >=' => $start_date,
                    'end_datetime <=' => $end_date
                ]),
                'unavailabilities' => $this->unavailabilities_model->get([
                    'start_datetime >=' => $start_date,
                    'end_datetime <=' => $end_date
                ])
            ];

            foreach ($response['appointments'] as &$appointment)
            {
                $appointment['provider'] = $this->providers_model->find($appointment['id_users_provider'], TRUE);
                $appointment['service'] = $this->services_model->find($appointment['id_services'], TRUE);
                $appointment['customer'] = $this->customers_model->find($appointment['id_users_customer'], TRUE);
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

                $response['appointments'] = array_values($response['appointments']);

                foreach ($response['unavailabilities'] as $index => $unavailability)
                {
                    if ((int)$unavailability['id_users_provider'] !== (int)$user_id)
                    {
                        unset($response['unavailabilities'][$index]);
                    }
                }

                $response['unavailabilities'] = array_values($response['unavailabilities']);
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

                $response['appointments'] = array_values($response['appointments']);

                foreach ($response['unavailabilities'] as $index => $unavailability)
                {
                    if ( ! in_array((int)$unavailability['id_users_provider'], $providers))
                    {
                        unset($response['unavailabilities'][$index]);
                    }
                }

                $response['unavailabilities'] = array_values($response['unavailabilities']);
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
     * This method returns the database appointments and unavailability periods for the user selected date period and
     * record type (provider or service).
     */
    public function get_calendar_appointments()
    {
        try
        {
            if (cannot('view', PRIV_APPOINTMENTS))
            {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $record_id = request('record_id');

            $filter_type = request('filter_type');

            if ( ! $filter_type && $record_id !== FILTER_TYPE_ALL)
            {
                json_response([
                    'appointments' => [],
                    'unavailabilities' => []
                ]);

                return;
            }

            $record_id = $this->db->escape($record_id);

            if ($filter_type == FILTER_TYPE_PROVIDER)
            {
                $where_id = 'id_users_provider';
            }
            elseif ($filter_type === FILTER_TYPE_SERVICE)
            {
                $where_id = 'id_services';
            }
            else
            {
                $where_id = $record_id;
            }

            // Get appointments
            $start_date = $this->db->escape(request('start_date'));
            $end_date = $this->db->escape(date('Y-m-d', strtotime(request('end_date') . ' +1 day')));

            $where_clause = $where_id . ' = ' . $record_id . '
                AND ((start_datetime > ' . $start_date . ' AND start_datetime < ' . $end_date . ') 
                or (end_datetime > ' . $start_date . ' AND end_datetime < ' . $end_date . ') 
                or (start_datetime <= ' . $start_date . ' AND end_datetime >= ' . $end_date . ')) 
                AND is_unavailability = 0
            ';

            $response['appointments'] = $this->appointments_model->get($where_clause);

            foreach ($response['appointments'] as &$appointment)
            {
                $appointment['provider'] = $this->providers_model->find($appointment['id_users_provider'], TRUE);
                $appointment['service'] = $this->services_model->find($appointment['id_services'], TRUE);
                $appointment['customer'] = $this->customers_model->find($appointment['id_users_customer'], TRUE);
            }

            // Get unavailability periods (only for provider).
            $response['unavailabilities'] = [];

            if ($filter_type == FILTER_TYPE_PROVIDER)
            {
                $where_clause = $where_id . ' = ' . $record_id . '
                    AND ((start_datetime > ' . $start_date . ' AND start_datetime < ' . $end_date . ') 
                    or (end_datetime > ' . $start_date . ' AND end_datetime < ' . $end_date . ') 
                    or (start_datetime <= ' . $start_date . ' AND end_datetime >= ' . $end_date . ')) 
                    AND is_unavailability = 1
                ';

                $response['unavailabilities'] = $this->unavailabilities_model->get($where_clause);
            }

            foreach ($response['unavailabilities'] as &$unavailability)
            {
                $unavailability['provider'] = $this->providers_model->find($unavailability['id_users_provider']);
            }

            json_response($response);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
