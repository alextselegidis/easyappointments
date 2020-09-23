<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

use EA\Engine\Notifications\Email as EmailClient;
use EA\Engine\Types\Email;
use EA\Engine\Types\Text;
use EA\Engine\Types\Url;

/**
 * Backend API Controller
 *
 * Contains all the backend AJAX callbacks.
 *
 * @property CI_Session session
 * @property CI_Loader load
 * @property CI_Input input
 * @property CI_Output output
 * @property CI_Config config
 * @property CI_Lang lang
 * @property CI_Cache cache
 * @property CI_DB_query_builder db
 * @property CI_Security security
 * @property Google_Sync google_sync
 * @property Ics_file ics_file
 * @property Appointments_Model appointments_model
 * @property Providers_Model providers_model
 * @property Services_Model services_model
 * @property Customers_Model customers_model
 * @property Settings_Model settings_model
 * @property Timezones timezones
 * @property Roles_Model roles_model
 * @property Secretaries_Model secretaries_model
 * @property Admins_Model admins_model
 * @property User_Model user_model
 *
 * @package Controllers
 */
class Backend_api extends CI_Controller {
    /**
     * @var array
     */
    protected $privileges;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('roles_model');

        if ($this->session->userdata('role_slug'))
        {
            $this->privileges = $this->roles_model->get_privileges($this->session->userdata('role_slug'));
        }

        // Set user's selected language.
        if ($this->session->userdata('language'))
        {
            $this->config->set_item('language', $this->session->userdata('language'));
            $this->lang->load('translations', $this->session->userdata('language'));
        }
        else
        {
            $this->lang->load('translations', $this->config->item('language')); // default
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
            $this->output->set_content_type('application/json');
            $this->load->model('appointments_model');
            $this->load->model('customers_model');
            $this->load->model('services_model');
            $this->load->model('providers_model');

            $startDate = $this->input->post('startDate') . ' 00:00:00';
            $endDate = $this->input->post('endDate') . ' 23:59:59';

            $response = [
                'appointments' => $this->appointments_model->get_batch([
                    'is_unavailable' => FALSE,
                    'start_datetime >=' => $startDate,
                    'end_datetime <=' => $endDate
                ]),
                'unavailabilities' => $this->appointments_model->get_batch([
                    'is_unavailable' => TRUE,
                    'start_datetime >=' => $startDate,
                    'end_datetime <=' => $endDate
                ])
            ];

            foreach ($response['appointments'] as &$appointment)
            {
                $appointment['provider'] = $this->providers_model->get_row($appointment['id_users_provider']);
                $appointment['service'] = $this->services_model->get_row($appointment['id_services']);
                $appointment['customer'] = $this->customers_model->get_row($appointment['id_users_customer']);
            }

            $userId = $this->session->userdata('user_id');
            $roleSlug = $this->session->userdata('role_slug');

            // If the current user is a provider he must only see his own appointments.
            if ($roleSlug === DB_SLUG_PROVIDER)
            {
                foreach ($response['appointments'] as $index => $appointment)
                {
                    if ((int)$appointment['id_users_provider'] !== (int)$userId)
                    {
                        unset($response['appointments'][$index]);
                    }
                }

                foreach ($response['unavailabilities'] as $index => $unavailability)
                {
                    if ((int)$unavailability['id_users_provider'] !== (int)$userId)
                    {
                        unset($response['unavailabilities'][$index]);
                    }
                }
            }

            // If the current user is a secretary he must only see the appointments of his providers.
            if ($roleSlug === DB_SLUG_SECRETARY)
            {
                $this->load->model('secretaries_model');
                $providers = $this->secretaries_model->get_row($userId)['providers'];
                foreach ($response['appointments'] as $index => $appointment)
                {
                    if ( ! in_array((int)$appointment['id_users_provider'], $providers))
                    {
                        unset($response['appointments'][$index]);
                    }
                }

                foreach ($response['unavailabilities'] as $index => $unavailability)
                {
                    if ( ! in_array((int)$unavailability['id_users_provider'], $providers))
                    {
                        unset($response['unavailabilities'][$index]);
                    }
                }
            }

            $this->output->set_output(json_encode($response));
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Get the registered appointments for the given date period and record.
     *
     * This method returns the database appointments and unavailable periods for the
     * user selected date period and record type (provider or service).
     */
    public function ajax_get_calendar_appointments()
    {
        try
        {
            if ($this->privileges[PRIV_APPOINTMENTS]['view'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            if ( ! $this->input->post('filter_type'))
            {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['appointments' => []]));
                return;
            }

            $this->load->model('appointments_model');
            $this->load->model('providers_model');
            $this->load->model('services_model');
            $this->load->model('customers_model');

            if ($this->input->post('filter_type') == FILTER_TYPE_PROVIDER)
            {
                $where_id = 'id_users_provider';
            }
            else
            {
                $where_id = 'id_services';
            }

            // Get appointments
            $record_id = $this->db->escape($this->input->post('record_id'));
            $start_date = $this->db->escape($this->input->post('start_date'));
            $end_date = $this->db->escape(date('Y-m-d', strtotime($this->input->post('end_date') . ' +1 day')));

            $where_clause = $where_id . ' = ' . $record_id . '
                AND ((start_datetime > ' . $start_date . ' AND start_datetime < ' . $end_date . ') 
                or (end_datetime > ' . $start_date . ' AND end_datetime < ' . $end_date . ') 
                or (start_datetime <= ' . $start_date . ' AND end_datetime >= ' . $end_date . ')) 
                AND is_unavailable = 0
            ';

            $response['appointments'] = $this->appointments_model->get_batch($where_clause);

            foreach ($response['appointments'] as &$appointment)
            {
                $appointment['provider'] = $this->providers_model->get_row($appointment['id_users_provider']);
                $appointment['service'] = $this->services_model->get_row($appointment['id_services']);
                $appointment['customer'] = $this->customers_model->get_row($appointment['id_users_customer']);
            }

            // Get unavailable periods (only for provider).
            if ($this->input->post('filter_type') == FILTER_TYPE_PROVIDER)
            {
                $where_clause = $where_id . ' = ' . $record_id . '
                    AND ((start_datetime > ' . $start_date . ' AND start_datetime < ' . $end_date . ') 
                    or (end_datetime > ' . $start_date . ' AND end_datetime < ' . $end_date . ') 
                    or (start_datetime <= ' . $start_date . ' AND end_datetime >= ' . $end_date . ')) 
                    AND is_unavailable = 1
                ';

                $response['unavailables'] = $this->appointments_model->get_batch($where_clause);
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Save appointment changes that are made from the backend calendar page.
     */
    public function ajax_save_appointment()
    {
        try
        {
            $this->load->model('appointments_model');
            $this->load->model('providers_model');
            $this->load->model('admins_model');
            $this->load->model('secretaries_model');
            $this->load->model('services_model');
            $this->load->model('customers_model');
            $this->load->model('settings_model');
            $this->load->library('timezones');
            $this->load->model('user_model');

            // Save customer changes to the database.
            if ($this->input->post('customer_data'))
            {
                $customer = json_decode($this->input->post('customer_data'), TRUE);

                $required_privilegesileges = ( ! isset($customer['id']))
                    ? $this->privileges[PRIV_CUSTOMERS]['add']
                    : $this->privileges[PRIV_CUSTOMERS]['edit'];
                if ($required_privilegesileges == FALSE)
                {
                    throw new Exception('You do not have the required privileges for this task.');
                }

                $customer['id'] = $this->customers_model->add($customer);
            }

            // Save appointment changes to the database.
            if ($this->input->post('appointment_data'))
            {
                $appointment = json_decode($this->input->post('appointment_data'), TRUE);

                $required_privilegesileges = ( ! isset($appointment['id']))
                    ? $this->privileges[PRIV_APPOINTMENTS]['add']
                    : $this->privileges[PRIV_APPOINTMENTS]['edit'];
                if ($required_privilegesileges == FALSE)
                {
                    throw new Exception('You do not have the required privileges for this task.');
                }

                $manage_mode = isset($appointment['id']);
                // If the appointment does not contain the customer record id, then it
                // means that is is going to be inserted. Get the customer's record id.
                if ( ! isset($appointment['id_users_customer']))
                {
                    $appointment['id_users_customer'] = $customer['id'];
                }

                $provider_timezone = $this->user_model->get_user_timezone($appointment['id_users_provider']);

                $session_timezone = $this->timezones->get_session_timezone();

                $appointment['start_datetime'] = $this->timezones->convert($appointment['start_datetime'],
                    $session_timezone, $provider_timezone);

                $appointment['end_datetime'] = $this->timezones->convert($appointment['end_datetime'],
                    $session_timezone, $provider_timezone);

                $appointment['id'] = $this->appointments_model->add($appointment);
            }

            $appointment = $this->appointments_model->get_row($appointment['id']);
            $provider = $this->providers_model->get_row($appointment['id_users_provider']);
            $customer = $this->customers_model->get_row($appointment['id_users_customer']);
            $service = $this->services_model->get_row($appointment['id_services']);

            $settings = [
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_link' => $this->settings_model->get_setting('company_link'),
                'company_email' => $this->settings_model->get_setting('company_email'),
                'date_format' => $this->settings_model->get_setting('date_format'),
                'time_format' => $this->settings_model->get_setting('time_format')
            ];

            // Sync appointment changes with Google Calendar.
            try
            {
                $google_sync = $this->providers_model->get_setting('google_sync',
                    $appointment['id_users_provider']);

                if ($google_sync == TRUE)
                {
                    $google_token = json_decode($this->providers_model->get_setting('google_token',
                        $appointment['id_users_provider']));

                    $this->load->library('Google_sync');
                    $this->google_sync->refresh_token($google_token->refresh_token);

                    if ($appointment['id_google_calendar'] == NULL)
                    {
                        $google_event = $this->google_sync->add_appointment($appointment, $provider,
                            $service, $customer, $settings);
                        $appointment['id_google_calendar'] = $google_event->id;
                        $this->appointments_model->add($appointment); // Store google calendar id.
                    }
                    else
                    {
                        $this->google_sync->update_appointment($appointment, $provider,
                            $service, $customer, $settings);
                    }
                }
            }
            catch (Exception $exception)
            {
                $warnings[] = [
                    'message' => $exception->getMessage(),
                    'trace' => config('debug') ? $exception->getTrace() : []
                ];
            }

            // Send email notifications to provider and customer.
            try
            {
                $this->config->load('email');
                $email = new EmailClient($this, $this->config->config);

                $send_provider = $this->providers_model
                    ->get_setting('notifications', $provider['id']);

                if ( ! $manage_mode)
                {
                    $customer_title = new Text(lang('appointment_booked'));
                    $customer_message = new Text(lang('thank_you_for_appointment'));
                    $provider_title = new Text(lang('appointment_added_to_your_plan'));
                    $provider_message = new Text(lang('appointment_link_description'));
                }
                else
                {
                    $customer_title = new Text(lang('appointment_details_changed'));
                    $customer_message = new Text('');
                    $provider_title = new Text(lang('appointment_changes_saved'));
                    $provider_message = new Text('');
                }

                $customer_link = new Url(site_url('appointments/index/' . $appointment['hash']));
                $provider_link = new Url(site_url('backend/index/' . $appointment['hash']));

                $send_customer = $this->settings_model->get_setting('customer_notifications');

                $this->load->library('ics_file');
                $ics_stream = $this->ics_file->get_stream($appointment, $service, $provider, $customer);

                if ((bool)$send_customer === TRUE)
                {
                    $email->sendAppointmentDetails($appointment, $provider,
                        $service, $customer, $settings, $customer_title,
                        $customer_message, $customer_link, new Email($customer['email']), new Text($ics_stream));
                }

                if ($send_provider == TRUE)
                {
                    $email->sendAppointmentDetails($appointment, $provider,
                        $service, $customer, $settings, $provider_title,
                        $provider_message, $provider_link, new Email($provider['email']), new Text($ics_stream));
                }

                // Notify admins
                $admins = $this->admins_model->get_batch();

                foreach($admins as $admin)
                {
                    if (!$admin['settings']['notifications'] === '0')
                    {
                        continue;
                    }

                    $email->sendAppointmentDetails($appointment, $provider,
                        $service, $customer, $settings, $provider_title,
                        $provider_message, $provider_link, new Email($admin['email']), new Text($ics_stream));
                }

                // Notify secretaries
                $secretaries = $this->secretaries_model->get_batch();

                foreach($secretaries as $secretary)
                {
                    if (!$secretary['settings']['notifications'] === '0')
                    {
                        continue;
                    }

                    if (in_array($provider['id'], $secretary['providers']))
                    {
                        continue;
                    }

                    $email->sendAppointmentDetails($appointment, $provider,
                        $service, $customer, $settings, $provider_title,
                        $provider_message, $provider_link, new Email($secretary['email']), new Text($ics_stream));
                }
            }
            catch (Exception $exception)
            {
                $warnings[] = [
                    'message' => $exception->getMessage(),
                    'trace' => config('debug') ? $exception->getTrace() : []
                ];
            }

            if (empty($warnings))
            {
                $response = AJAX_SUCCESS;
            }
            else
            {
                $response = ['warnings' => $warnings];
            }
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
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
            if ($this->privileges[PRIV_APPOINTMENTS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            if ( ! $this->input->post('appointment_id'))
            {
                throw new Exception('No appointment id provided.');
            }

            // Store appointment data for later use in this method.
            $this->load->model('appointments_model');
            $this->load->model('providers_model');
            $this->load->model('admins_model');
            $this->load->model('secretaries_model');
            $this->load->model('customers_model');
            $this->load->model('services_model');
            $this->load->model('settings_model');

            $appointment = $this->appointments_model->get_row($this->input->post('appointment_id'));
            $provider = $this->providers_model->get_row($appointment['id_users_provider']);
            $customer = $this->customers_model->get_row($appointment['id_users_customer']);
            $service = $this->services_model->get_row($appointment['id_services']);

            $settings = [
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_email' => $this->settings_model->get_setting('company_email'),
                'company_link' => $this->settings_model->get_setting('company_link'),
                'date_format' => $this->settings_model->get_setting('date_format'),
                'time_format' => $this->settings_model->get_setting('time_format')
            ];

            // Delete appointment record from the database.
            $this->appointments_model->delete($this->input->post('appointment_id'));

            // Sync removal with Google Calendar.
            if ($appointment['id_google_calendar'] != NULL)
            {
                try
                {
                    $google_sync = $this->providers_model->get_setting('google_sync', $provider['id']);

                    if ($google_sync == TRUE)
                    {
                        $google_token = json_decode($this->providers_model
                            ->get_setting('google_token', $provider['id']));
                        $this->load->library('Google_sync');
                        $this->google_sync->refresh_token($google_token->refresh_token);
                        $this->google_sync->delete_appointment($provider, $appointment['id_google_calendar']);
                    }
                }
                catch (Exception $exception)
                {
                    $warnings[] = [
                        'message' => $exception->getMessage(),
                        'trace' => config('debug') ? $exception->getTrace() : []
                    ];
                }
            }

            // Send notification emails to provider and customer.
            try
            {
                $this->config->load('email');

                $email = new EmailClient($this, $this->config->config);

                $send_provider = $this->providers_model
                    ->get_setting('notifications', $provider['id']);

                if ((bool)$send_provider === TRUE)
                {
                    $email->sendDeleteAppointment($appointment, $provider,
                        $service, $customer, $settings, new Email($provider['email']),
                        new Text($this->input->post('delete_reason')));
                }

                $send_customer = $this->settings_model->get_setting('customer_notifications');

                if ((bool)$send_customer === TRUE)
                {
                    $email->sendDeleteAppointment($appointment, $provider,
                        $service, $customer, $settings, new Email($customer['email']),
                        new Text($this->input->post('delete_reason')));
                }

                // Notify admins
                $admins = $this->admins_model->get_batch();

                foreach($admins as $admin)
                {
                    if (!$admin['settings']['notifications'] === '0')
                    {
                        continue;
                    }

                    $email->sendDeleteAppointment($appointment, $provider,
                        $service, $customer, $settings, new Email($admin['email']),
                        new Text($this->input->post('cancel_reason')));
                }

                // Notify secretaries
                $secretaries = $this->secretaries_model->get_batch();

                foreach($secretaries as $secretary)
                {
                    if (!$secretary['settings']['notifications'] === '0')
                    {
                        continue;
                    }

                    if (in_array($provider['id'], $secretary['providers']))
                    {
                        continue;
                    }

                    $email->sendDeleteAppointment($appointment, $provider,
                        $service, $customer, $settings, new Email($secretary['email']),
                        new Text($this->input->post('cancel_reason')));
                }
            }
            catch (Exception $exception)
            {
                $warnings[] = [
                    'message' => $exception->getMessage(),
                    'trace' => config('debug') ? $exception->getTrace() : []
                ];
            }

            if (empty($warnings))
            {
                $response = AJAX_SUCCESS;
            }
            else
            {
                $response = ['warnings' => $warnings];
            }
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Disable a providers sync setting.
     *
     * This method deletes the "google_sync" and "google_token" settings from the database. After that the provider's
     * appointments will be no longer synced with google calendar.
     */
    public function ajax_disable_provider_sync()
    {
        try
        {
            if ( ! $this->input->post('provider_id'))
            {
                throw new Exception('Provider id not specified.');
            }

            if ($this->privileges[PRIV_USERS]['edit'] == FALSE
                && $this->session->userdata('user_id') != $this->input->post('provider_id'))
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('providers_model');
            $this->load->model('appointments_model');
            $this->providers_model->set_setting('google_sync', FALSE, $this->input->post('provider_id'));
            $this->providers_model->set_setting('google_token', NULL, $this->input->post('provider_id'));
            $this->appointments_model->clear_google_sync_ids($this->input->post('provider_id'));

            $response = AJAX_SUCCESS;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Filter the customer records with the given key string.
     *
     * Outputs the search results.
     */
    public function ajax_filter_customers()
    {
        try
        {
            if ($this->privileges[PRIV_CUSTOMERS]['view'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('appointments_model');
            $this->load->model('services_model');
            $this->load->model('providers_model');
            $this->load->model('customers_model');

            $key = $this->db->escape_str($this->input->post('key'));
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

            $limit = $this->input->post('limit');

            if ($limit === NULL)
            {
                $limit = 1000;
            }

            $customers = $this->customers_model->get_batch($where, $order_by, $limit);

            foreach ($customers as &$customer)
            {
                $appointments = $this->appointments_model
                    ->get_batch(['id_users_customer' => $customer['id']]);

                foreach ($appointments as &$appointment)
                {
                    $appointment['service'] = $this->services_model->get_row($appointment['id_services']);
                    $appointment['provider'] = $this->providers_model->get_row($appointment['id_users_provider']);
                }

                $customer['appointments'] = $appointments;
            }

            $response = $customers;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Insert of update unavailable time period to database.
     */
    public function ajax_save_unavailable()
    {
        try
        {
            // Check privileges
            $unavailable = json_decode($this->input->post('unavailable'), TRUE);

            $required_privileges = ( ! isset($unavailable['id']))
                ? $this->privileges[PRIV_APPOINTMENTS]['add']
                : $this->privileges[PRIV_APPOINTMENTS]['edit'];
            if ($required_privileges == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('appointments_model');
            $this->load->model('providers_model');

            $provider = $this->providers_model->get_row($unavailable['id_users_provider']);

            // Add appointment
            $unavailable['id'] = $this->appointments_model->add_unavailable($unavailable);
            $unavailable = $this->appointments_model->get_row($unavailable['id']); // fetch all inserted data

            // Google Sync
            try
            {
                $google_sync = $this->providers_model->get_setting('google_sync',
                    $unavailable['id_users_provider']);

                if ($google_sync)
                {
                    $google_token = json_decode($this->providers_model->get_setting('google_token',
                        $unavailable['id_users_provider']));

                    $this->load->library('google_sync');
                    $this->google_sync->refresh_token($google_token->refresh_token);

                    if ($unavailable['id_google_calendar'] == NULL)
                    {
                        $google_event = $this->google_sync->add_unavailable($provider, $unavailable);
                        $unavailable['id_google_calendar'] = $google_event->id;
                        $this->appointments_model->add_unavailable($unavailable);
                    }
                    else
                    {
                        $this->google_sync->update_unavailable($provider, $unavailable);
                    }
                }
            }
            catch (Exception $exception)
            {
                $warnings[] = $exception;
            }

            if (isset($warnings))
            {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['warnings' => $warnings]));
            }
            else
            {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(AJAX_SUCCESS));
            }

            $response = AJAX_SUCCESS;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Delete an unavailable time period from database.
     */
    public function ajax_delete_unavailable()
    {
        try
        {
            if ($this->privileges[PRIV_APPOINTMENTS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('appointments_model');
            $this->load->model('providers_model');

            $unavailable = $this->appointments_model->get_row($this->input->post('unavailable_id'));
            $provider = $this->providers_model->get_row($unavailable['id_users_provider']);

            // Delete unavailable
            $this->appointments_model->delete_unavailable($unavailable['id']);

            // Google Sync
            try
            {
                $google_sync = $this->providers_model->get_setting('google_sync', $provider['id']);
                if ($google_sync == TRUE)
                {
                    $google_token = json_decode($this->providers_model->get_setting('google_token', $provider['id']));
                    $this->load->library('google_sync');
                    $this->google_sync->refresh_token($google_token->refresh_token);
                    $this->google_sync->delete_unavailable($provider, $unavailable['id_google_calendar']);
                }
            }
            catch (Exception $exception)
            {
                $warnings[] = $exception;
            }

            if (empty($warnings))
            {
                $response = AJAX_SUCCESS;
            }
            else
            {
                $response = ['warnings' => $warnings];
            }
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Insert of update custom availability periods to database.
     */
    public function ajax_save_custom_availability_period()
    {
        try
        {
            // Check privileges
            $custom_availability_period = json_decode($this->input->post('custom_availability_period'), TRUE);

            $required_privileges = ( ! isset($custom_availability_period['id']))
                ? $this->privileges[PRIV_APPOINTMENTS]['add']
                : $this->privileges[PRIV_APPOINTMENTS]['edit'];
            if ($required_privileges == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('providers_model');

            $success = $this->providers_model->set_custom_availability_period($custom_availability_period, $custom_availability_period['id_users_provider']);

            if ($success)
            {
                $response = AJAX_SUCCESS;
            }
            else
            {
                $response = ['warnings' => 'Error on saving custom availability period.'];
            }
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Delete an custom availability periods time period to database.
     */
    public function ajax_delete_custom_availability_period()
    {
        try
        {
            if ($this->privileges[PRIV_APPOINTMENTS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            // Check privileges
            $custom_availability_period = $this->input->post('custom_availability_period');
            $provider_id = $this->input->post('provider_id');

            $this->load->model('providers_model');

            // Delete unavailable
            $success = $this->providers_model->delete_custom_availability_period($custom_availability_period, $provider_id);

            if ($success)
            {
                $response = AJAX_SUCCESS;
            }
            else
            {
                $response = ['warnings' => 'Error on deleting custom availability period.'];
            }
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Save (insert or update) a customer record.
     */
    public function ajax_save_customer()
    {
        try
        {
            $this->load->model('customers_model');
            $customer = json_decode($this->input->post('customer'), TRUE);

            $required_privilegesileges = ( ! isset($customer['id']))
                ? $this->privileges[PRIV_CUSTOMERS]['add']
                : $this->privileges[PRIV_CUSTOMERS]['edit'];
            if ($required_privilegesileges == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $customer_id = $this->customers_model->add($customer);

            $response = [
                'status' => AJAX_SUCCESS,
                'id' => $customer_id
            ];
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Delete customer from database.
     */
    public function ajax_delete_customer()
    {
        try
        {
            if ($this->privileges[PRIV_CUSTOMERS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('customers_model');

            $this->customers_model->delete($this->input->post('customer_id'));

            $response = AJAX_SUCCESS;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Save (insert or update) service record.
     */
    public function ajax_save_service()
    {
        try
        {
            $this->load->model('services_model');
            $service = json_decode($this->input->post('service'), TRUE);

            $required_privilegesileges = ( ! isset($service['id']))
                ? $this->privileges[PRIV_SERVICES]['add']
                : $this->privileges[PRIV_SERVICES]['edit'];
            if ($required_privilegesileges == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $service_id = $this->services_model->add($service);
            $response = [
                'status' => AJAX_SUCCESS,
                'id' => $service_id
            ];
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Delete service record from database.
     */
    public function ajax_delete_service()
    {
        try
        {
            if ($this->privileges[PRIV_SERVICES]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('services_model');

            $result = $this->services_model->delete($this->input->post('service_id'));

            $response = $result ? AJAX_SUCCESS : AJAX_FAILURE;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Filter service records by given key string.
     */
    public function ajax_filter_services()
    {
        try
        {
            if ($this->privileges[PRIV_SERVICES]['view'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('services_model');

            $key = $this->db->escape_str($this->input->post('key'));

            $where =
                '(name LIKE "%' . $key . '%" OR duration LIKE "%' . $key . '%" OR ' .
                'price LIKE "%' . $key . '%" OR currency LIKE "%' . $key . '%" OR ' .
                'description LIKE "%' . $key . '%")';

            $response = $this->services_model->get_batch($where);
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Save (insert or update) category record.
     */
    public function ajax_save_service_category()
    {
        try
        {
            $this->load->model('services_model');
            $category = json_decode($this->input->post('category'), TRUE);

            $required_privilegesileges = ( ! isset($category['id']))
                ? $this->privileges[PRIV_SERVICES]['add']
                : $this->privileges[PRIV_SERVICES]['edit'];
            if ($required_privilegesileges == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $category_id = $this->services_model->add_category($category);

            $response = [
                'status' => AJAX_SUCCESS,
                'id' => $category_id
            ];
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Delete category record from database.
     */
    public function ajax_delete_service_category()
    {
        try
        {
            if ($this->privileges[PRIV_SERVICES]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('services_model');

            $result = $this->services_model->delete_category($this->input->post('category_id'));

            $response = $result ? AJAX_SUCCESS : AJAX_FAILURE;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Filter services categories with key string.
     */
    public function ajax_filter_service_categories()
    {
        try
        {
            if ($this->privileges[PRIV_SERVICES]['view'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('services_model');

            $key = $this->db->escape_str($this->input->post('key'));

            $where = '(name LIKE "%' . $key . '%" OR description LIKE "%' . $key . '%")';

            $response = $this->services_model->get_all_categories($where);
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Filter admin records with string key.
     */
    public function ajax_filter_admins()
    {
        try
        {
            if ($this->privileges[PRIV_USERS]['view'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('admins_model');

            $key = $this->db->escape_str($this->input->post('key'));

            $where =
                '(first_name LIKE "%' . $key . '%" OR last_name LIKE "%' . $key . '%" ' .
                'OR email LIKE "%' . $key . '%" OR mobile_number LIKE "%' . $key . '%" ' .
                'OR phone_number LIKE "%' . $key . '%" OR address LIKE "%' . $key . '%" ' .
                'OR city LIKE "%' . $key . '%" OR state LIKE "%' . $key . '%" ' .
                'OR zip_code LIKE "%' . $key . '%" OR notes LIKE "%' . $key . '%")';

            $response = $this->admins_model->get_batch($where);
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Save (insert or update) admin record into database.
     */
    public function ajax_save_admin()
    {
        try
        {
            $this->load->model('admins_model');
            $admin = json_decode($this->input->post('admin'), TRUE);

            $required_privileges = ( ! isset($admin['id']))
                ? $this->privileges[PRIV_USERS]['add']
                : $this->privileges[PRIV_USERS]['edit'];
            if ($required_privileges == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $admin_id = $this->admins_model->add($admin);

            $response = [
                'status' => AJAX_SUCCESS,
                'id' => $admin_id
            ];
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Delete an admin record from the database.
     */
    public function ajax_delete_admin()
    {
        try
        {
            if ($this->privileges[PRIV_USERS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('admins_model');

            $result = $this->admins_model->delete($this->input->post('admin_id'));

            $response = $result ? AJAX_SUCCESS : AJAX_FAILURE;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Filter provider records with string key.
     */
    public function ajax_filter_providers()
    {
        try
        {
            if ($this->privileges[PRIV_USERS]['view'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('providers_model');

            $key = $this->db->escape_str($this->input->post('key'));

            $where =
                '(first_name LIKE "%' . $key . '%" OR last_name LIKE "%' . $key . '%" ' .
                'OR email LIKE "%' . $key . '%" OR mobile_number LIKE "%' . $key . '%" ' .
                'OR phone_number LIKE "%' . $key . '%" OR address LIKE "%' . $key . '%" ' .
                'OR city LIKE "%' . $key . '%" OR state LIKE "%' . $key . '%" ' .
                'OR zip_code LIKE "%' . $key . '%" OR notes LIKE "%' . $key . '%")';

            $response = $this->providers_model->get_batch($where);
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Save (insert or update) a provider record into database.
     */
    public function ajax_save_provider()
    {
        try
        {
            $this->load->model('providers_model');
            $provider = json_decode($this->input->post('provider'), TRUE);

            $required_privileges = ( ! isset($provider['id']))
                ? $this->privileges[PRIV_USERS]['add']
                : $this->privileges[PRIV_USERS]['edit'];
            if ($required_privileges == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            if ( ! isset($provider['settings']['working_plan']))
            {
                $this->load->model('settings_model');
                $provider['settings']['working_plan'] = $this->settings_model
                    ->get_setting('company_working_plan');
            }

            $provider_id = $this->providers_model->add($provider);

            $response = [
                'status' => AJAX_SUCCESS,
                'id' => $provider_id
            ];
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Delete a provider record from the database.
     */
    public function ajax_delete_provider()
    {
        try
        {
            if ($this->privileges[PRIV_USERS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('providers_model');

            $result = $this->providers_model->delete($this->input->post('provider_id'));

            $response =$result ? AJAX_SUCCESS : AJAX_FAILURE;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Filter secretary records with string key.
     */
    public function ajax_filter_secretaries()
    {
        try
        {
            if ($this->privileges[PRIV_USERS]['view'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('secretaries_model');

            $key = $this->db->escape_str($this->input->post('key'));

            $where =
                '(first_name LIKE "%' . $key . '%" OR last_name LIKE "%' . $key . '%" ' .
                'OR email LIKE "%' . $key . '%" OR mobile_number LIKE "%' . $key . '%" ' .
                'OR phone_number LIKE "%' . $key . '%" OR address LIKE "%' . $key . '%" ' .
                'OR city LIKE "%' . $key . '%" OR state LIKE "%' . $key . '%" ' .
                'OR zip_code LIKE "%' . $key . '%" OR notes LIKE "%' . $key . '%")';

            $response = $this->secretaries_model->get_batch($where);
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Save (insert or update) a secretary record into database.
     */
    public function ajax_save_secretary()
    {
        try
        {
            $this->load->model('secretaries_model');
            $secretary = json_decode($this->input->post('secretary'), TRUE);

            $required_privileges = ( ! isset($secretary['id']))
                ? $this->privileges[PRIV_USERS]['add']
                : $this->privileges[PRIV_USERS]['edit'];
            if ($required_privileges == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $secretary_id = $this->secretaries_model->add($secretary);

            $response =[
                'status' => AJAX_SUCCESS,
                'id' => $secretary_id
            ];
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Delete a secretary record from the database.
     */
    public function ajax_delete_secretary()
    {
        try
        {
            if ($this->privileges[PRIV_USERS]['delete'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('secretaries_model');

            $result = $this->secretaries_model->delete($this->input->post('secretary_id'));

            $response =$result ? AJAX_SUCCESS : AJAX_FAILURE;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Save a setting or multiple settings in the database.
     */
    public function ajax_save_settings()
    {
        try
        {
            if ($this->input->post('type') == SETTINGS_SYSTEM)
            {
                if ($this->privileges[PRIV_SYSTEM_SETTINGS]['edit'] == FALSE)
                {
                    throw new Exception('You do not have the required privileges for this task.');
                }

                $this->load->model('settings_model');

                $settings = json_decode($this->input->post('settings'), TRUE);

                $this->settings_model->save_settings($settings);
            }
            else
            {
                if ($this->input->post('type') == SETTINGS_USER)
                {
                    if ($this->privileges[PRIV_USER_SETTINGS]['edit'] == FALSE)
                    {
                        throw new Exception('You do not have the required privileges for this task.');
                    }

                    $this->load->model('user_model');

                    $this->user_model->save_user(json_decode($this->input->post('settings'), TRUE));
                }
            }

            $response = AJAX_SUCCESS;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
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

            $this->load->model('admins_model');

            $is_valid = $this->admins_model->validate_username($this->input->post('username'),
                $this->input->post('user_id'));

            $response = $is_valid;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
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

            foreach ($this->config->item('available_languages') as $lang)
            {
                if ($lang == $this->input->post('language'))
                {
                    $found = TRUE;
                    break;
                }
            }

            if ( ! $found)
            {
                throw new Exception('Translations for the given language does not exist (' . $this->input->post('language') . ').');
            }

            $this->session->set_userdata('language', $this->input->post('language'));
            $this->config->set_item('language', $this->input->post('language'));

            $response = AJAX_SUCCESS;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * This method will return a list of the available google calendars.
     *
     * The user will need to select a specific calendar from this list to sync his appointments with. Google access must
     * be already granted for the specific provider.
     */
    public function ajax_get_google_calendars()
    {
        try
        {
            $this->load->library('google_sync');
            $this->load->model('providers_model');

            if ( ! $this->input->post('provider_id'))
            {
                throw new Exception('Provider id is required in order to fetch the google calendars.');
            }

            // Check if selected provider has sync enabled.
            $google_sync = $this->providers_model->get_setting('google_sync', $this->input->post('provider_id'));

            if ($google_sync)
            {
                $google_token = json_decode($this->providers_model->get_setting('google_token',
                    $this->input->post('provider_id')));
                $this->google_sync->refresh_token($google_token->refresh_token);

                $calendars = $this->google_sync->get_google_calendars();

                $response = $calendars;
            }
            else
            {
                $response =AJAX_FAILURE;
            }
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
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
            if ($this->privileges[PRIV_USERS]['edit'] == FALSE
                && $this->session->userdata('user_id') != $this->input->post('provider_id'))
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $this->load->model('providers_model');

            $result = $this->providers_model->set_setting('google_calendar', $this->input->post('calendar_id'),
                $this->input->post('provider_id'));

            $response = $result ? AJAX_SUCCESS : AJAX_FAILURE;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Apply global working plan to all providers.
     */
    public function ajax_apply_global_working_plan()
    {
        try
        {
            if ($this->privileges[PRIV_SYSTEM_SETTINGS]['edit'] == FALSE)
            {
                throw new Exception('You do not have the required privileges for this task.');
            }

            $working_plan = $this->input->post('working_plan');

            $this->load->model('providers_model');

            $providers = $this->providers_model->get_batch();

            foreach ($providers as $provider)
            {
                $this->providers_model->set_setting('working_plan', $working_plan, $provider['id']);
            }

            $response =AJAX_SUCCESS;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
