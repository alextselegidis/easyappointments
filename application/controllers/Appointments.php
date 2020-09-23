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
 * Appointments Controller
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
class Appointments extends CI_Controller {
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->helper('installation');
        $this->load->helper('google_analytics');
        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('customers_model');
        $this->load->model('settings_model');
        $this->load->library('timezones');

        if ($this->session->userdata('language'))
        {
            // Set the user's selected language.
            $this->config->set_item('language', $this->session->userdata('language'));
            $this->lang->load('translations', $this->session->userdata('language'));
        }
        else
        {
            // Set the default language.
            $this->lang->load('translations', $this->config->item('language'));
        }
    }

    /**
     * Default callback method of the application.
     *
     * This method creates the appointment book wizard. If an appointment hash is provided then it means that the
     * customer followed the appointment manage link that was send with the book success email.
     *
     * @param string $appointment_hash The appointment hash identifier.
     */
    public function index($appointment_hash = '')
    {
        try
        {
            if ( ! is_app_installed())
            {
                redirect('installation/index');
                return;
            }

            $available_services = $this->services_model->get_available_services();
            $available_providers = $this->providers_model->get_available_providers();
            $company_name = $this->settings_model->get_setting('company_name');
            $date_format = $this->settings_model->get_setting('date_format');
            $time_format = $this->settings_model->get_setting('time_format');
            $first_weekday = $this->settings_model->get_setting('first_weekday');
            $require_phone_number = $this->settings_model->get_setting('require_phone_number');
            $display_cookie_notice = $this->settings_model->get_setting('display_cookie_notice');
            $cookie_notice_content = $this->settings_model->get_setting('cookie_notice_content');
            $display_terms_and_conditions = $this->settings_model->get_setting('display_terms_and_conditions');
            $terms_and_conditions_content = $this->settings_model->get_setting('terms_and_conditions_content');
            $display_privacy_policy = $this->settings_model->get_setting('display_privacy_policy');
            $privacy_policy_content = $this->settings_model->get_setting('privacy_policy_content');
            $display_any_provider = $this->settings_model->get_setting('display_any_provider');
            $timezones = $this->timezones->to_array();

            // Remove the data that are not needed inside the $available_providers array.
            foreach ($available_providers as $index => $provider)
            {
                $stripped_data = [
                    'id' => $provider['id'],
                    'first_name' => $provider['first_name'],
                    'last_name' => $provider['last_name'],
                    'services' => $provider['services'],
                    'timezone' => $provider['timezone']
                ];
                $available_providers[$index] = $stripped_data;
            }

            // If an appointment hash is provided then it means that the customer is trying to edit a registered
            // appointment record.
            if ($appointment_hash !== '')
            {
                // Load the appointments data and enable the manage mode of the page.
                $manage_mode = TRUE;

                $results = $this->appointments_model->get_batch(['hash' => $appointment_hash]);

                if (empty($results))
                {
                    // The requested appointment doesn't exist in the database. Display a message to the customer.
                    $variables = [
                        'message_title' => lang('appointment_not_found'),
                        'message_text' => lang('appointment_does_not_exist_in_db'),
                        'message_icon' => base_url('assets/img/error.png')
                    ];

                    $this->load->view('appointments/message', $variables);

                    return;
                }

                $appointment = $results[0];
                $provider = $this->providers_model->get_row($appointment['id_users_provider']);
                $customer = $this->customers_model->get_row($appointment['id_users_customer']);

                $customer_token = md5(uniqid(mt_rand(), TRUE));

                $this->load->driver('cache', ['adapter' => 'file']);
                // Save the token for 10 minutes.
                $this->cache->save('customer-token-' . $customer_token, $customer['id'], 600);
            }
            else
            {
                // The customer is going to book a new appointment so there is no need for the manage functionality to
                // be initialized.
                $manage_mode = FALSE;
                $customer_token = FALSE;
                $appointment = [];
                $provider = [];
                $customer = [];
            }

            // Load the book appointment view.
            $variables = [
                'available_services' => $available_services,
                'available_providers' => $available_providers,
                'company_name' => $company_name,
                'manage_mode' => $manage_mode,
                'customer_token' => $customer_token,
                'date_format' => $date_format,
                'time_format' => $time_format,
                'first_weekday' => $first_weekday,
                'require_phone_number' => $require_phone_number,
                'appointment_data' => $appointment,
                'provider_data' => $provider,
                'customer_data' => $customer,
                'display_cookie_notice' => $display_cookie_notice,
                'cookie_notice_content' => $cookie_notice_content,
                'display_terms_and_conditions' => $display_terms_and_conditions,
                'terms_and_conditions_content' => $terms_and_conditions_content,
                'display_privacy_policy' => $display_privacy_policy,
                'privacy_policy_content' => $privacy_policy_content,
                'timezones' => $timezones,
                'display_any_provider' => $display_any_provider,
            ];
        }
        catch (Exception $exception)
        {
            $variables['exceptions'][] = $exception;
        }

        $this->load->view('appointments/book', $variables);
    }

    /**
     * Cancel an existing appointment.
     *
     * This method removes an appointment from the company's schedule. In order for the appointment to be deleted, the
     * hash string must be provided. The customer can only cancel the appointment if the edit time period is not over
     * yet.
     *
     * @param string $appointment_hash This appointment hash identifier.
     */
    public function cancel($appointment_hash)
    {
        try
        {
            // Check whether the appointment hash exists in the database.
            $appointments = $this->appointments_model->get_batch(['hash' => $appointment_hash]);

            if (empty($appointments))
            {
                throw new Exception('No record matches the provided hash.');
            }

            $appointment = $appointments[0];
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

            // Remove the appointment record from the data.
            if ( ! $this->appointments_model->delete($appointment['id']))
            {
                throw new Exception('Appointment could not be deleted from the database.');
            }

            // Remove the appointment from Google Calendar if needed.
            if ($appointment['id_google_calendar'] != NULL)
            {
                try
                {
                    $google_sync = filter_var(
                        $this->providers_model->get_setting('google_sync', $appointment['id_users_provider']),
                        FILTER_VALIDATE_BOOLEAN);

                    if ($google_sync === TRUE)
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
                    $exceptions[] = $exception;
                }
            }

            // Send email notification to customer and provider.
            try
            {
                $email = new EmailClient($this, $this->config->config);

                $send_provider = filter_var($this->providers_model
                    ->get_setting('notifications', $provider['id']),
                    FILTER_VALIDATE_BOOLEAN);

                if ($send_provider === TRUE)
                {
                    $email->sendDeleteAppointment($appointment, $provider,
                        $service, $customer, $settings, new Email($provider['email']),
                        new Text($this->input->post('cancel_reason')));
                }

                $send_customer = filter_var(
                    $this->settings_model->get_setting('customer_notifications'),
                    FILTER_VALIDATE_BOOLEAN);

                if ($send_customer === TRUE)
                {
                    $email->sendDeleteAppointment($appointment, $provider,
                        $service, $customer, $settings, new Email($customer['email']),
                        new Text($this->input->post('cancel_reason')));
                }

            }
            catch (Exception $exception)
            {
                $exceptions[] = $exception;
            }
        }
        catch (Exception $exception)
        {
            // Display the error message to the customer.
            $exceptions[] = $exception;
        }

        $view = [
            'message_title' => lang('appointment_cancelled_title'),
            'message_text' => lang('appointment_cancelled'),
            'message_icon' => base_url('assets/img/success.png')
        ];

        if (isset($exceptions))
        {
            $view['exceptions'] = $exceptions;
        }

        $this->load->view('appointments/message', $view);
    }

    /**
     * GET an specific appointment book and redirect to the success screen.
     *
     * @param string $appointment_hash The appointment hash identifier.
     *
     * @throws Exception
     */
    public function book_success($appointment_hash)
    {
        $appointments = $this->appointments_model->get_batch(['hash' => $appointment_hash]);

        if (empty($appointments))
        {
            redirect('appointments'); // The appointment does not exist.
            return;
        }

        $appointment = $appointments[0];
        unset($appointment['notes']);

        $provider = $this->providers_model->get_row($appointment['id_users_provider']);
        unset($provider['settings'], $provider['notes']);

        $service = $this->services_model->get_row($appointment['id_services']);

        $company_name = $this->settings_model->get_setting('company_name');

        // Get any pending exceptions.
        $exceptions = $this->session->flashdata('book_success');

        $view = [
            'appointment_data' => $appointment,
            'provider_data' => $provider,
            'service_data' => $service,
            'company_name' => $company_name,
        ];

        if ($exceptions)
        {
            $view['exceptions'] = $exceptions;
        }

        $this->load->view('appointments/book_success', $view);
    }

    /**
     * [AJAX] Get the available appointment hours for the given date.
     *
     * This method answers to an AJAX request. It calculates the available hours for the given service, provider and
     * date.
     *
     * Outputs a JSON string with the availabilities.
     */
    public function ajax_get_available_hours()
    {
        try
        {
            $provider_id = $this->input->post('provider_id');
            $service_id = $this->input->post('service_id');
            $selected_date = $this->input->post('selected_date');

            // Do not continue if there was no provider selected (more likely there is no provider in the system).
            if (empty($provider_id))
            {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([]));

                return;
            }

            // If manage mode is TRUE then the following we should not consider the selected appointment when
            // calculating the available time periods of the provider.
            $exclude_appointments = [];

            if ($this->input->post('manage_mode') === 'true')
            {
                $exclude_appointments[] = $this->input->post('appointment_id');
            }

            // If the user has selected the "any-provider" option then we will need to search for an available provider
            // that will provide the requested service.
            if ($provider_id === ANY_PROVIDER)
            {
                $provider_id = $this->search_any_provider($service_id, $selected_date);

                if ($provider_id === NULL)
                {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([]));

                    return;
                }
            }

            $service = $this->services_model->get_row($service_id);
            $provider = $this->providers_model->get_row($provider_id);

            $empty_periods = $this->get_provider_available_time_periods($provider_id,
                $selected_date, $exclude_appointments);

            $available_hours = $this->calculate_available_hours($empty_periods, $selected_date,
                $service['duration'], $service['availabilities_type']);

            if ($service['attendants_number'] > 1)
            {
                $available_hours = $this->get_multiple_attendants_hours($selected_date, $service,
                    $provider);
            }

            $response = $this->consider_book_advance_timeout($selected_date, $available_hours, $provider);
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
     * Search for any provider that can handle the requested service.
     *
     * This method will return the database ID of the provider with the most available periods.
     *
     * @param int $service_id The requested service ID.
     * @param string $selected_date The date to be searched.
     *
     * @return int Returns the ID of the provider that can provide the service at the selected date.
     *
     * @throws Exception
     */
    protected function search_any_provider($service_id, $selected_date)
    {
        $available_providers = $this->providers_model->get_available_providers();

        $service = $this->services_model->get_row($service_id);

        $provider_id = NULL;

        $max_hours_count = 0;

        foreach ($available_providers as $provider)
        {
            foreach ($provider['services'] as $provider_service_id)
            {
                if ($provider_service_id == $service_id)
                {
                    // Check if the provider is available for the requested date.
                    $empty_periods = $this->get_provider_available_time_periods($provider['id'], $selected_date);

                    $available_hours = $this->calculate_available_hours($empty_periods, $selected_date,
                        $service['duration'], $service['availabilities_type']);

                    if ($service['attendants_number'] > 1)
                    {
                        $available_hours = $this->get_multiple_attendants_hours($selected_date, $service,
                            $provider);
                    }

                    if (count($available_hours) > $max_hours_count)
                    {
                        $provider_id = $provider['id'];
                        $max_hours_count = count($available_hours);
                    }
                }
            }
        }

        return $provider_id;
    }

    /**
     * Get an array containing the free time periods (start - end) of a selected date.
     *
     * This method is very important because there are many cases where the system needs to know when a provider is
     * available for an appointment. This method will return an array that belongs to the selected date and contains
     * values that have the start and the end time of an available time period.
     *
     * @param int $provider_id Provider record ID.
     * @param string $selected_date Date to be checked (MySQL formatted string).
     * @param array $excluded_appointment_ids Array containing the IDs of the appointments that will not be taken into
     * consideration when the available time periods are calculated.
     *
     * @return array Returns an array with the available time periods of the provider.
     *
     * @throws Exception
     */
    protected function get_provider_available_time_periods(
        $provider_id,
        $selected_date,
        $excluded_appointment_ids = []
    )
    {
        // Get the service, provider's working plan and provider appointments.
        $working_plan = json_decode($this->providers_model->get_setting('working_plan', $provider_id), TRUE);

        // Get the provider's custom availability periods.
        $custom_availability_periods = json_decode($this->providers_model->get_setting('custom_availability_periods', $provider_id), TRUE);

        $provider_appointments = $this->appointments_model->get_batch([
            'id_users_provider' => $provider_id,
        ]);

        // Sometimes it might be necessary to not take into account some appointment records in order to display what
        // the providers' available time periods would be without them.
        foreach ($excluded_appointment_ids as $excluded_appointment_id)
        {
            foreach ($provider_appointments as $index => $reserved)
            {
                if ($reserved['id'] == $excluded_appointment_id)
                {
                    unset($provider_appointments[$index]);
                }
            }
        }

        // Find the empty spaces on the plan. The first split between the plan is due to a break (if any). After that
        // every reserved appointment is considered to be a taken space in the plan.
        $selected_date_working_plan = $working_plan[strtolower(date('l', strtotime($selected_date)))];

        // Search if the $selected_date is an custom availability period added outside the normal working plan.
        if ($selected_date_working_plan == NULL)
        {
            if (isset($custom_availability_periods[strtolower(date('Y-m-d', strtotime($selected_date)))]))
            {
                $selected_date_working_plan = $custom_availability_periods[strtolower(date('Y-m-d', strtotime($selected_date)))];
            }
        }

        $periods = [];

        if (isset($selected_date_working_plan['breaks']))
        {
            $periods[] = [
                'start' => $selected_date_working_plan['start'],
                'end' => $selected_date_working_plan['end']
            ];

            $day_start = new DateTime($selected_date_working_plan['start']);
            $day_end = new DateTime($selected_date_working_plan['end']);

            // Split the working plan to available time periods that do not contain the breaks in them.
            foreach ($selected_date_working_plan['breaks'] as $index => $break)
            {
                $break_start = new DateTime($break['start']);
                $break_end = new DateTime($break['end']);

                if ($break_start < $day_start)
                {
                    $break_start = $day_start;
                }

                if ($break_end > $day_end)
                {
                    $break_end = $day_end;
                }

                if ($break_start >= $break_end)
                {
                    continue;
                }

                foreach ($periods as $key => $period)
                {
                    $period_start = new DateTime($period['start']);
                    $period_end = new DateTime($period['end']);

                    $remove_current_period = FALSE;

                    if ($break_start > $period_start && $break_start < $period_end && $break_end > $period_start)
                    {
                        $periods[] = [
                            'start' => $period_start->format('H:i'),
                            'end' => $break_start->format('H:i')
                        ];

                        $remove_current_period = TRUE;
                    }

                    if ($break_start < $period_end && $break_end > $period_start && $break_end < $period_end)
                    {
                        $periods[] = [
                            'start' => $break_end->format('H:i'),
                            'end' => $period_end->format('H:i')
                        ];

                        $remove_current_period = TRUE;
                    }

                    if ($break_start == $period_start && $break_end == $period_end)
                    {
                        $remove_current_period = TRUE;
                    }

                    if ($remove_current_period)
                    {
                        unset($periods[$key]);
                    }
                }
            }
        }

        // Break the empty periods with the reserved appointments.
        foreach ($provider_appointments as $provider_appointment)
        {
            foreach ($periods as $index => &$period)
            {
                $appointment_start = new DateTime($provider_appointment['start_datetime']);
                $appointment_end = new DateTime($provider_appointment['end_datetime']);

                if ($appointment_start >= $appointment_end)
                {
                    continue;
                }

                $period_start = new DateTime($selected_date . ' ' . $period['start']);
                $period_end = new DateTime($selected_date . ' ' . $period['end']);

                if ($appointment_start <= $period_start && $appointment_end <= $period_end && $appointment_end <= $period_start)
                {
                    // The appointment does not belong in this time period, so we  will not change anything.
                    continue;
                }
                else
                {
                    if ($appointment_start <= $period_start && $appointment_end <= $period_end && $appointment_end >= $period_start)
                    {
                        // The appointment starts before the period and finishes somewhere inside. We will need to break
                        // this period and leave the available part.
                        $period['start'] = $appointment_end->format('H:i');
                    }
                    else
                    {
                        if ($appointment_start >= $period_start && $appointment_end < $period_end)
                        {
                            // The appointment is inside the time period, so we will split the period into two new
                            // others.
                            unset($periods[$index]);

                            $periods[] = [
                                'start' => $period_start->format('H:i'),
                                'end' => $appointment_start->format('H:i')
                            ];

                            $periods[] = [
                                'start' => $appointment_end->format('H:i'),
                                'end' => $period_end->format('H:i')
                            ];
                        }
                        else if ($appointment_start == $period_start && $appointment_end == $period_end)
                        {
                            unset($periods[$index]); // The whole period is blocked so remove it from the available periods array.
                        }
                        else
                        {
                            if ($appointment_start >= $period_start && $appointment_end >= $period_start && $appointment_start <= $period_end)
                            {
                                // The appointment starts in the period and finishes out of it. We will need to remove
                                // the time that is taken from the appointment.
                                $period['end'] = $appointment_start->format('H:i');
                            }
                            else
                            {
                                if ($appointment_start >= $period_start && $appointment_end >= $period_end && $appointment_start >= $period_end)
                                {
                                    // The appointment does not belong in the period so do not change anything.
                                    continue;
                                }
                                else
                                {
                                    if ($appointment_start <= $period_start && $appointment_end >= $period_end && $appointment_start <= $period_end)
                                    {
                                        // The appointment is bigger than the period, so this period needs to be removed.
                                        unset($periods[$index]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return array_values($periods);
    }

    /**
     * Calculate the available appointment hours.
     *
     * Calculate the available appointment hours for the given date. The empty spaces
     * are broken down to 15 min and if the service fit in each quarter then a new
     * available hour is added to the "$available_hours" array.
     *
     * @param array $empty_periods Contains the empty periods as generated by the "get_provider_available_time_periods"
     * method.
     * @param string $selected_date The selected date to be search (format )
     * @param int $service_duration The service duration is required for the hour calculation.
     * @param string $availabilities_type Optional ('flexible'), the service availabilities type.
     *
     * @return array Returns an array with the available hours for the appointment.
     * @throws Exception
     */
    protected function calculate_available_hours(
        array $empty_periods,
        $selected_date,
        $service_duration,
        $availabilities_type = 'flexible'
    )
    {
        $available_hours = [];

        foreach ($empty_periods as $period)
        {
            $start_hour = new DateTime($selected_date . ' ' . $period['start']);
            $end_hour = new DateTime($selected_date . ' ' . $period['end']);
            $interval = $availabilities_type === AVAILABILITIES_TYPE_FIXED ? (int)$service_duration : 15;

            $current_hour = $start_hour;
            $diff = $current_hour->diff($end_hour);

            while (($diff->h * 60 + $diff->i) >= intval($service_duration))
            {
                $available_hours[] = $current_hour->format('H:i');
                $current_hour->add(new DateInterval('PT' . $interval . 'M'));
                $diff = $current_hour->diff($end_hour);
            }
        }

        return $available_hours;
    }

    /**
     * Get multiple attendants hours.
     *
     * This method will add the additional appointment hours whenever a service accepts multiple attendants.
     *
     * @param string $selected_date The selected appointment date.
     * @param array $service Selected service data.
     * @param array $provider Selected provider data.
     *
     * @return array Returns the available hours array.
     * @throws Exception
     */
    protected function get_multiple_attendants_hours(
        $selected_date,
        $service,
        $provider
    )
    {
        $unavailability_events = $this->appointments_model->get_batch([
            'is_unavailable' => TRUE,
            'DATE(start_datetime)' => $selected_date,
            'id_users_provider' => $provider['id']
        ]);

        $working_plan = json_decode($provider['settings']['working_plan'], TRUE);
        $working_day = strtolower(date('l', strtotime($selected_date)));
        $working_hours = $working_plan[$working_day];

        $periods = [
            [
                'start' => new DateTime($selected_date . ' ' . $working_hours['start']),
                'end' => new DateTime($selected_date . ' ' . $working_hours['end'])
            ]
        ];

        $periods = $this->remove_breaks($selected_date, $periods, $working_hours['breaks']);
        $periods = $this->remove_unavailability_events($periods, $unavailability_events);

        $hours = [];

        $interval_value = $service['availabilities_type'] == AVAILABILITIES_TYPE_FIXED ? $service['duration'] : '15';
        $interval = new DateInterval('PT' . (int)$interval_value . 'M');
        $duration = new DateInterval('PT' . (int)$service['duration'] . 'M');

        foreach ($periods as $period)
        {
            $slot_start = clone $period['start'];
            $slot_end = clone $slot_start;
            $slot_end->add($duration);

            while ($slot_end <= $period['end'])
            {
                // Check reserved attendants for this time slot and see if current attendants fit.
                $appointment_attendants_number = $this->appointments_model->get_attendants_number_for_period($slot_start,
                    $slot_end, $service['id']);

                if ($appointment_attendants_number < $service['attendants_number'])
                {
                    $hours[] = $slot_start->format('H:i');
                }

                $slot_start->add($interval);
                $slot_end->add($interval);
            }
        }

        return $hours;
    }

    /**
     * Remove breaks from available time periods.
     *
     * @param string $selected_date Selected data (Y-m-d format).
     * @param array $periods Time periods of the current date.
     * @param array $breaks Breaks array for the current date.
     *
     * @return array Returns the available time periods without the breaks.
     * @throws Exception
     */
    public function remove_breaks($selected_date, $periods, $breaks)
    {
        if ( ! $breaks)
        {
            return $periods;
        }

        foreach ($breaks as $break)
        {
            $break_start = new DateTime($selected_date . ' ' . $break['start']);
            $break_end = new DateTime($selected_date . ' ' . $break['end']);

            foreach ($periods as &$period)
            {
                $period_start = $period['start'];
                $period_end = $period['end'];

                if ($break_start <= $period_start && $break_end >= $period_start && $break_end <= $period_end)
                {
                    // left
                    $period['start'] = $break_end;
                    continue;
                }

                if ($break_start >= $period_start && $break_start <= $period_end && $break_end >= $period_start && $break_end <= $period_end)
                {
                    // middle
                    $period['end'] = $break_start;
                    $periods[] = [
                        'start' => $break_end,
                        'end' => $period_end
                    ];
                    continue;
                }

                if ($break_start >= $period_start && $break_start <= $period_end && $break_end >= $period_end)
                {
                    // right
                    $period['end'] = $break_start;
                    continue;
                }

                if ($break_start <= $period_start && $break_end >= $period_end)
                {
                    // break contains period
                    $period['start'] = $break_end;
                    continue;
                }
            }
        }

        return $periods;
    }

    /**
     * Remove the unavailability entries from the available time periods of the selected date.
     *
     * @param array $periods Available time periods.
     * @param array $unavailability_events Unavailability events of the current date.
     *
     * @return array Returns the available time periods without the unavailability events.
     *
     * @throws Exception
     */
    public function remove_unavailability_events($periods, $unavailability_events)
    {
        foreach ($unavailability_events as $unavailability_event)
        {
            $unavailability_start = new DateTime($unavailability_event['start_datetime']);
            $unavailability_end = new DateTime($unavailability_event['end_datetime']);

            foreach ($periods as &$period)
            {
                $period_start = $period['start'];
                $period_end = $period['end'];

                if ($unavailability_start <= $period_start && $unavailability_end >= $period_start && $unavailability_end <= $period_end)
                {
                    // left
                    $period['start'] = $unavailability_end;
                    continue;
                }

                if ($unavailability_start >= $period_start && $unavailability_start <= $period_end && $unavailability_end >= $period_start && $unavailability_end <= $period_end)
                {
                    // middle
                    $period['end'] = $unavailability_start;
                    $periods[] = [
                        'start' => $unavailability_end,
                        'end' => $period_end
                    ];
                    continue;
                }

                if ($unavailability_start >= $period_start && $unavailability_start <= $period_end && $unavailability_end >= $period_end)
                {
                    // right
                    $period['end'] = $unavailability_start;
                    continue;
                }

                if ($unavailability_start <= $period_start && $unavailability_end >= $period_end)
                {
                    // Unavailability contains period
                    $period['start'] = $unavailability_end;
                    continue;
                }
            }
        }

        return $periods;
    }

    /**
     * [AJAX] Register the appointment to the database.
     *
     * Outputs a JSON string with the appointment ID.
     */
    public function ajax_register_appointment()
    {
        try
        {
            $this->load->model('appointments_model');
            $this->load->model('providers_model');
            $this->load->model('services_model');
            $this->load->model('customers_model');
            $this->load->model('settings_model');

            $post_data = $this->input->post('post_data');
            $captcha = $this->input->post('captcha');
            $manage_mode = filter_var($post_data['manage_mode'], FILTER_VALIDATE_BOOLEAN);
            $appointment = $post_data['appointment'];
            $customer = $post_data['customer'];

            // Check appointment availability before registering it to the database.
            $appointment['id_users_provider'] = $this->check_datetime_availability();

            if ( ! $appointment['id_users_provider'])
            {
                throw new Exception(lang('requested_hour_is_unavailable'));
            }

            $provider = $this->providers_model->get_row($appointment['id_users_provider']);
            $service = $this->services_model->get_row($appointment['id_services']);

            $require_captcha = $this->settings_model->get_setting('require_captcha');
            $captcha_phrase = $this->session->userdata('captcha_phrase');

            // Validate the CAPTCHA string.
            if ($require_captcha === '1' && $captcha_phrase !== $captcha)
            {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'captcha_verification' => FALSE
                    ]));

                return;
            }

            if ($this->customers_model->exists($customer))
            {
                $customer['id'] = $this->customers_model->find_record_id($customer);
            }

            if (empty($appointment['location']) && ! empty($service['location']))
            {
                $appointment['location'] = $service['location'];
            }

            $customer_id = $this->customers_model->add($customer);

            $appointment['id_users_customer'] = $customer_id;
            $appointment['is_unavailable'] = (int)$appointment['is_unavailable']; // needs to be type casted
            $appointment['id'] = $this->appointments_model->add($appointment);
            $appointment['hash'] = $this->appointments_model->get_value('hash', $appointment['id']);

            $settings = [
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_link' => $this->settings_model->get_setting('company_link'),
                'company_email' => $this->settings_model->get_setting('company_email'),
                'date_format' => $this->settings_model->get_setting('date_format'),
                'time_format' => $this->settings_model->get_setting('time_format')
            ];

            // Synchronize the appointment with the provider's Google Calendar.
            try
            {
                $google_sync = filter_var(
                    $this->providers_model->get_setting('google_sync', $appointment['id_users_provider']),
                    FILTER_VALIDATE_BOOLEAN);

                if ($google_sync === TRUE)
                {
                    $google_token = json_decode(
                        $this->providers_model->get_setting('google_token', $appointment['id_users_provider']));

                    $this->load->library('google_sync');

                    $this->google_sync->refresh_token($google_token->refresh_token);

                    if ($manage_mode === FALSE)
                    {
                        // Add appointment to Google Calendar.
                        $google_event = $this->google_sync->add_appointment($appointment, $provider,
                            $service, $customer, $settings);
                        $appointment['id_google_calendar'] = $google_event->id;
                        $this->appointments_model->add($appointment);
                    }
                    else
                    {
                        // Update appointment to Google Calendar.
                        $appointment['id_google_calendar'] = $this->appointments_model
                            ->get_value('id_google_calendar', $appointment['id']);

                        $this->google_sync->update_appointment($appointment, $provider,
                            $service, $customer, $settings);
                    }
                }
            }
            catch (Exception $exception)
            {
                log_message('error', $exception->getMessage());
                log_message('error', $exception->getTraceAsString());
            }

            // Send email notifications to customer and provider.
            try
            {
                $this->config->load('email');

                $email = new EmailClient($this, $this->config->config);

                if ($manage_mode === FALSE)
                {
                    $customer_title = new Text(lang('appointment_booked'));
                    $customer_message = new Text(lang('thank_you_for_appointment'));
                    $provider_title = new Text(lang('appointment_added_to_your_plan'));
                    $provider_message = new Text(lang('appointment_link_description'));

                }
                else
                {
                    $customer_title = new Text(lang('appointment_changes_saved'));
                    $customer_message = new Text('');
                    $provider_title = new Text(lang('appointment_details_changed'));
                    $provider_message = new Text('');
                }

                $customer_link = new Url(site_url('appointments/index/' . $appointment['hash']));
                $provider_link = new Url(site_url('backend/index/' . $appointment['hash']));

                $send_customer = filter_var(
                    $this->settings_model->get_setting('customer_notifications'),
                    FILTER_VALIDATE_BOOLEAN);

                $this->load->library('ics_file');

                $ics_stream = $this->ics_file->get_stream($appointment, $service, $provider, $customer);

                if ($send_customer === TRUE)
                {
                    $email->sendAppointmentDetails($appointment, $provider,
                        $service, $customer, $settings, $customer_title,
                        $customer_message, $customer_link, new Email($customer['email']), new Text($ics_stream));
                }

                $send_provider = filter_var(
                    $this->providers_model->get_setting('notifications', $provider['id']),
                    FILTER_VALIDATE_BOOLEAN);

                if ($send_provider === TRUE)
                {
                    $email->sendAppointmentDetails($appointment, $provider,
                        $service, $customer, $settings, $provider_title,
                        $provider_message, $provider_link, new Email($provider['email']), new Text($ics_stream));
                }
            }
            catch (Exception $exception)
            {
                log_message('error', $exception->getMessage());
                log_message('error', $exception->getTraceAsString());
            }

            $response = [
                'appointment_id' => $appointment['id'],
                'appointment_hash' => $appointment['hash']
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
     * Check whether the provider is still available in the selected appointment date.
     *
     * It might be times where two or more customers select the same appointment date and time. This shouldn't be
     * allowed to happen, so one of the two customers will eventually get the preferred date and the other one will have
     * to choose for another date. Use this method just before the customer confirms the appointment details. If the
     * selected date was taken in the mean time, the customer must be prompted to select another time for his
     * appointment.
     *
     * @return int Returns the ID of the provider that is available for the appointment.
     *
     * @throws Exception
     */
    protected function check_datetime_availability()
    {
        $this->load->model('services_model');
        $this->load->model('appointments_model');

        $post_data = $this->input->post('post_data');

        $appointment = $post_data['appointment'];

        $service_duration = $this->services_model->get_value('duration', $appointment['id_services']);

        $exclude_appointments = [];

        if (isset($appointment['id']))
        {
            $exclude_appointments[] = $appointment['id'];
        }

        $attendants_number = $this->services_model->get_value('attendants_number', $appointment['id_services']);

        if ($attendants_number > 1)
        {
            // Exclude all the appointments that are currently registered.
            $existing_appointments = $this->appointments_model->get_batch([
                'id_services' => $appointment['id_services'],
                'start_datetime' => $appointment['start_datetime']
            ]);

            if ( ! empty($existing_appointments) && count($existing_appointments) < $attendants_number)
            {
                foreach ($existing_appointments as $existing_appointment)
                {
                    $exclude_appointments[] = $existing_appointment['id'];
                }
            }
        }

        if ($appointment['id_users_provider'] === ANY_PROVIDER)
        {
            $appointment['id_users_provider'] = $this->search_any_provider($appointment['id_services'],
                date('Y-m-d', strtotime($appointment['start_datetime'])));

            return $appointment['id_users_provider'];
        }

        $available_periods = $this->get_provider_available_time_periods(
            $appointment['id_users_provider'],
            date('Y-m-d', strtotime($appointment['start_datetime'])),
            $exclude_appointments);

        $is_still_available = FALSE;

        foreach ($available_periods as $period)
        {
            $appt_start = new DateTime($appointment['start_datetime']);
            $appt_start = $appt_start->format('H:i');

            $appt_end = new DateTime($appointment['start_datetime']);
            $appt_end->add(new DateInterval('PT' . $service_duration . 'M'));
            $appt_end = $appt_end->format('H:i');

            $period_start = date('H:i', strtotime($period['start']));
            $period_end = date('H:i', strtotime($period['end']));

            if ($period_start <= $appt_start && $period_end >= $appt_end)
            {
                $is_still_available = TRUE;
                break;
            }
        }

        return $is_still_available ? $appointment['id_users_provider'] : NULL;
    }

    /**
     * [AJAX] Get Unavailable Dates
     *
     * Get an array with the available dates of a specific provider, service and month of the year. Provide the
     * "provider_id", "service_id" and "selected_date" as GET parameters to the request. The "selected_date" parameter
     * must have the Y-m-d format.
     *
     * Outputs a JSON string with the unavailable dates. that are unavailable.
     */
    public function ajax_get_unavailable_dates()
    {
        try
        {
            $this->load->model('providers_model');
            $this->load->model('services_model');

            $provider_id = $this->input->get('provider_id');
            $service_id = $this->input->get('service_id');
            $appointment_id = $this->input->get_post('appointment_id');
            $manage_mode = $this->input->get_post('manage_mode');
            $selected_date_string = $this->input->get('selected_date');
            $selected_date = new DateTime($selected_date_string);
            $number_of_days_in_month = (int)$selected_date->format('t');
            $unavailable_dates = [];

            $exclude_appointments = [];

            if ($manage_mode === 'true')
            {
                $exclude_appointments[] = $appointment_id;
            }

            $provider_list = $provider_id === ANY_PROVIDER
                ? $this->search_providers_by_service($service_id)
                : [$provider_id];

            // Get the service record.
            $service = $this->services_model->get_row($service_id);

            for ($i = 1; $i <= $number_of_days_in_month; $i++)
            {
                $current_date = new DateTime($selected_date->format('Y-m') . '-' . $i);

                if ($current_date < new DateTime(date('Y-m-d 00:00:00')))
                {
                    // Past dates become immediately unavailable.
                    $unavailable_dates[] = $current_date->format('Y-m-d');
                    continue;
                }

                // Finding at least one slot of availability.
                foreach ($provider_list as $current_provider_id)
                {
                    // Get the provider record.
                    $current_provider = $this->providers_model->get_row($current_provider_id);

                    $empty_periods = $this->get_provider_available_time_periods($current_provider_id,
                        $current_date->format('Y-m-d'), $exclude_appointments);

                    $available_hours = $this->calculate_available_hours($empty_periods, $current_date->format('Y-m-d'),
                        $service['duration'], $service['availabilities_type']);

                    if ( ! empty($available_hours))
                    {
                        break;
                    }

                    if ($service['attendants_number'] > 1)
                    {
                        $available_hours = $this->get_multiple_attendants_hours($current_date->format('Y-m-d'),
                            $service, $current_provider);

                        if ( ! empty($available_hours))
                        {
                            break;
                        }
                    }

                    $available_hours = $this->consider_book_advance_timeout($current_date->format('Y-m-d'),
                        $available_hours, $current_provider);
                }

                // No availability amongst all the provider.
                if (empty($available_hours))
                {
                    $unavailable_dates[] = $current_date->format('Y-m-d');
                }
            }

            $response = $unavailable_dates;
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
     * Search for any provider that can handle the requested service.
     *
     * This method will return the database ID of the providers affected to the requested service.
     *
     * @param int $service_id The requested service ID.
     *
     * @return array Returns the ID of the provider that can provide the requested service.
     */
    protected function search_providers_by_service($service_id)
    {
        $this->load->model('providers_model');
        $available_providers = $this->providers_model->get_available_providers();
        $provider_list = [];

        foreach ($available_providers as $provider)
        {
            foreach ($provider['services'] as $provider_service_id)
            {
                if ($provider_service_id === $service_id)
                {
                    // Check if the provider is affected to the selected service.
                    $provider_list[] = $provider['id'];
                }
            }
        }

        return $provider_list;
    }

    /**
     * Consider the book advance timeout and remove available hours that have passed the threshold.
     *
     * If the selected date is today, remove past hours. It is important  include the timeout before booking
     * that is set in the back-office the system. Normally we might want the customer to book an appointment
     * that is at least half or one hour from now. The setting is stored in minutes.
     *
     * @param string $selected_date The selected date.
     * @param array $available_hours Already generated available hours.
     * @param array $provider Provider information.
     *
     * @return array Returns the updated available hours.
     *
     * @throws Exception
     */
    protected function consider_book_advance_timeout($selected_date, $available_hours, $provider)
    {
        $provider_timezone = new DateTimeZone($provider['timezone']);

        $book_advance_timeout = $this->settings_model->get_setting('book_advance_timeout');

        $threshold = new DateTime('+' . $book_advance_timeout . ' minutes', $provider_timezone);

        foreach ($available_hours as $index => $value)
        {
            $available_hour = new DateTime($selected_date . ' ' . $value, $provider_timezone);

            if ($available_hour->getTimestamp() <= $threshold->getTimestamp())
            {
                unset($available_hours[$index]);
            }
        }

        $available_hours = array_values($available_hours);
        sort($available_hours, SORT_STRING);
        return array_values($available_hours);
    }
}
