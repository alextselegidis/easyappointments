<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @author      N.Houari <houarinourredine@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.0
 * ---------------------------------------------------------------------------- */

use \EA\Engine\Types\Text;
use \EA\Engine\Types\Email;
use \EA\Engine\Types\Url;
 
class AppointmentService
{

    private $ci;
    private $lang;

    public function __construct()
    {
        log_message('info', 'Creating new instance of AppointmentService...');
        $this->ci = &get_instance();
        $this->lang =& load_class('Lang', 'core');
        $this->ci->load->library('session');
        $this->lang->load('translations', $this->ci->session->userdata('language'));
        log_message('info', 'AppointmentService created!');
    }

    /**
     * Check whether the appointment information are correct.
     *
     * It will check for the  provider, service and customer existance.
     *
     * @return bool Returns whether all the parameters are valid.
     */
    public function checkAppointment($appointment)
    {
        $service_id = $appointment['id_services'];
        $provider_id = $appointment['id_users_provider'];
        $user_id = $appointment['id_users_customer'];

        $this->ci->load->model('services_model');
        $this->ci->load->model('appointments_model');
        $this->ci->load->model('providers_model');
        $this->ci->load->model('customers_model');

        log_message('info', 'Checking appointment provider=' . $provider_id . ', service=' . $service_id . ' user=' . $user_id);

        // Check service
        $service_exist = $this->ci->services_model->get_row($service_id);
        if (count($service_exist) == 0) {
            log_message('info', 'service not found:' . $service_id);
            return false;
        }
        // Check provider
        try {
            $this->ci->providers_model->get_row($provider_id);
        } catch (\Throwable $th) {
            log_message('info', 'provider not found:' . $provider_id);
            return false;
        }

        // Check customer
        $customer_exist = $this->ci->customers_model->get_row($user_id);
        if (count($customer_exist) == 0) {
            log_message('info', 'customer not found:' . $user_id);
            return false;
        }
        return true;
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
     * @return bool Returns whether the selected datetime is still available.
     */
    public function check_datetime_availability($appointment)
    {
        log_message('info', 'Checking availability of appointment');
        $this->ci->load->model('services_model');
        $this->ci->load->model('appointments_model');

        log_message('info', json_encode($appointment['id_services'], JSON_PRETTY_PRINT));

        $service_duration = $this->ci->services_model->get_value('duration', $appointment['id_services']);
        $exclude_appointments = (isset($appointment['id'])) ? [$appointment['id']] : [];
        $attendants_number = $this->ci->services_model->get_value('attendants_number', $appointment['id_services']);

        if ($attendants_number > 1) {
            // Exclude all the appointments that are currently registered.
            $exclude = $this->ci->appointments_model->get_batch([
                'id_services' => $appointment['id_services'],
                'start_datetime' => $appointment['start_datetime']
            ]);

            if (!empty($exclude) && count($exclude) < $attendants_number) {
                foreach ($exclude as $entry) {
                    $exclude_appointments[] = $entry['id'];
                }
            }
        }

        if ($appointment['id_users_provider'] === ANY_PROVIDER) {
            $appointment['id_users_provider'] = $this->_search_any_provider(
                $appointment['id_services'],
                date('Y-m-d', strtotime($appointment['start_datetime']))
            );
            $_POST['post_data']['appointment']['id_users_provider'] = $appointment['id_users_provider'];
            return TRUE; // The selected provider is always available.
        }

        $available_periods = $this->_get_provider_available_time_periods(
            $appointment['id_users_provider'],
            $appointment['id_services'],
            date('Y-m-d', strtotime($appointment['start_datetime'])),
            $exclude_appointments
        );

        $is_still_available = FALSE;

        foreach ($available_periods as $period) {
            $appt_start = new DateTime($appointment['start_datetime']);
            $appt_start = $appt_start->format('H:i');

            $appt_end = new DateTime($appointment['start_datetime']);
            $appt_end->add(new DateInterval('PT' . $service_duration . 'M'));
            $appt_end = $appt_end->format('H:i');

            $period_start = date('H:i', strtotime($period['start']));
            $period_end = date('H:i', strtotime($period['end']));

            if ($period_start <= $appt_start && $period_end >= $appt_end) {
                $is_still_available = TRUE;
                break;
            }
        }

        return $is_still_available;
    }

    /**
     * Get an array containing the free time periods (start - end) of a selected date.
     *
     * This method is very important because there are many cases where the system needs to know when a provider is
     * available for an appointment. This method will return an array that belongs to the selected date and contains
     * values that have the start and the end time of an available time period.
     *
     * @param int $provider_id Provider record ID.
     * @param int $service_id Service record ID.
     * @param string $selected_date Date to be checked (MySQL formatted string).
     * @param array $excluded_appointment_ids Array containing the IDs of the appointments that will not be taken into
     * consideration when the available time periods are calculated.
     *
     * @return array Returns an array with the available time periods of the provider.
     */
    protected function _get_provider_available_time_periods(
        $provider_id,
        $service_id,
        $selected_date,
        $excluded_appointment_ids = []
    ) {
        $this->ci->load->model('appointments_model');
        $this->ci->load->model('providers_model');
        $this->ci->load->model('services_model');

        // Get the service, provider's working plan and provider appointments.
        $working_plan = json_decode($this->ci->providers_model->get_setting('working_plan', $provider_id), TRUE);

        $provider_appointments = $this->ci->appointments_model->get_batch([
            'id_users_provider' => $provider_id,
        ]);

        // Sometimes it might be necessary to not take into account some appointment records in order to display what
        // the providers' available time periods would be without them.
        foreach ($excluded_appointment_ids as $excluded_appointment_id) {
            foreach ($provider_appointments as $index => $reserved) {
                if ($reserved['id'] == $excluded_appointment_id) {
                    unset($provider_appointments[$index]);
                }
            }
        }

        // Find the empty spaces on the plan. The first split between the plan is due to a break (if any). After that
        // every reserved appointment is considered to be a taken space in the plan.
        $selected_date_working_plan = $working_plan[strtolower(date('l', strtotime($selected_date)))];

        $periods = [];

        if (isset($selected_date_working_plan['breaks'])) {
            $periods[] = [
                'start' => $selected_date_working_plan['start'],
                'end' => $selected_date_working_plan['end']
            ];

            $day_start = new DateTime($selected_date_working_plan['start']);
            $day_end = new DateTime($selected_date_working_plan['end']);

            // Split the working plan to available time periods that do not contain the breaks in them.
            foreach ($selected_date_working_plan['breaks'] as $index => $break) {
                $break_start = new DateTime($break['start']);
                $break_end = new DateTime($break['end']);

                if ($break_start < $day_start) {
                    $break_start = $day_start;
                }

                if ($break_end > $day_end) {
                    $break_end = $day_end;
                }

                if ($break_start >= $break_end) {
                    continue;
                }

                foreach ($periods as $key => $period) {
                    $period_start = new DateTime($period['start']);
                    $period_end = new DateTime($period['end']);

                    $remove_current_period = FALSE;

                    if ($break_start > $period_start && $break_start < $period_end && $break_end > $period_start) {
                        $periods[] = [
                            'start' => $period_start->format('H:i'),
                            'end' => $break_start->format('H:i')
                        ];

                        $remove_current_period = TRUE;
                    }

                    if ($break_start < $period_end && $break_end > $period_start && $break_end < $period_end) {
                        $periods[] = [
                            'start' => $break_end->format('H:i'),
                            'end' => $period_end->format('H:i')
                        ];

                        $remove_current_period = TRUE;
                    }

                    if ($break_start == $period_start && $break_end == $period_end) {
                        $remove_current_period = TRUE;
                    }

                    if ($remove_current_period) {
                        unset($periods[$key]);
                    }
                }
            }
        }

        // Break the empty periods with the reserved appointments.
        foreach ($provider_appointments as $provider_appointment) {
            foreach ($periods as $index => &$period) {
                $appointment_start = new DateTime($provider_appointment['start_datetime']);
                $appointment_end = new DateTime($provider_appointment['end_datetime']);
                $period_start = new DateTime($selected_date . ' ' . $period['start']);
                $period_end = new DateTime($selected_date . ' ' . $period['end']);

                if ($appointment_start <= $period_start && $appointment_end <= $period_end && $appointment_end <= $period_start) {
                    // The appointment does not belong in this time period, so we  will not change anything.
                } else {
                    if ($appointment_start <= $period_start && $appointment_end <= $period_end && $appointment_end >= $period_start) {
                        // The appointment starts before the period and finishes somewhere inside. We will need to break
                        // this period and leave the available part.
                        $period['start'] = $appointment_end->format('H:i');
                    } else {
                        if ($appointment_start >= $period_start && $appointment_end < $period_end) {
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
                        } else if ($appointment_start == $period_start && $appointment_end == $period_end) {
                            unset($periods[$index]); // The whole period is blocked so remove it from the available periods array.
                        } else {
                            if ($appointment_start >= $period_start && $appointment_end >= $period_start && $appointment_start <= $period_end) {
                                // The appointment starts in the period and finishes out of it. We will need to remove
                                // the time that is taken from the appointment.
                                $period['end'] = $appointment_start->format('H:i');
                            } else {
                                if ($appointment_start >= $period_start && $appointment_end >= $period_end && $appointment_start >= $period_end) {
                                    // The appointment does not belong in the period so do not change anything.
                                } else {
                                    if ($appointment_start <= $period_start && $appointment_end >= $period_end && $appointment_start <= $period_end) {
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
     * Search for any provider that can handle the requested service.
     *
     * This method will return the database ID of the provider with the most available periods.
     *
     * @param int $service_id The requested service ID.
     * @param string $selected_date The date to be searched.
     *
     * @return int Returns the ID of the provider that can provide the service at the selected date.
     */
    protected function _search_any_provider($service_id, $selected_date)
    {
        $this->ci->load->model('providers_model');
        $this->ci->load->model('services_model');
        $available_providers = $this->ci->providers_model->get_available_providers();
        $service = $this->ci->services_model->get_row($service_id);
        $provider_id = NULL;
        $max_hours_count = 0;

        foreach ($available_providers as $provider) {
            foreach ($provider['services'] as $provider_service_id) {
                if ($provider_service_id == $service_id) {
                    // Check if the provider is available for the requested date.
                    $empty_periods = $this->_get_provider_available_time_periods(
                        $provider['id'],
                        $service_id,
                        $selected_date
                    );

                    $available_hours = $this->_calculate_available_hours(
                        $empty_periods,
                        $selected_date,
                        $service['duration'],
                        FALSE,
                        $service['availabilities_type']
                    );

                    if ($service['attendants_number'] > 1) {
                        $available_hours = $this->_get_multiple_attendants_hours(
                            $selected_date,
                            $service,
                            $provider
                        );
                    }

                    if (count($available_hours) > $max_hours_count) {
                        $provider_id = $provider['id'];
                        $max_hours_count = count($available_hours);
                    }
                }
            }
        }

        return $provider_id;
    }

    /**
     * Calculate the available appointment hours.
     *
     * Calculate the available appointment hours for the given date. The empty spaces
     * are broken down to 15 min and if the service fit in each quarter then a new
     * available hour is added to the "$available_hours" array.
     *
     * @param array $empty_periods Contains the empty periods as generated by the "_get_provider_available_time_periods"
     * method.
     * @param string $selected_date The selected date to be search (format )
     * @param int $service_duration The service duration is required for the hour calculation.
     * @param bool $manage_mode (optional) Whether we are currently on manage mode (editing an existing appointment).
     * @param string $availabilities_type Optional ('flexible'), the service availabilities type.
     *
     * @return array Returns an array with the available hours for the appointment.
     */
    protected function _calculate_available_hours(
        array $empty_periods,
        $selected_date,
        $service_duration,
        $manage_mode = FALSE,
        $availabilities_type = 'flexible'
    ) {
        $this->ci->load->model('settings_model');

        $available_hours = [];

        foreach ($empty_periods as $period) {
            $start_hour = new DateTime($selected_date . ' ' . $period['start']);
            $end_hour = new DateTime($selected_date . ' ' . $period['end']);
            $interval = $availabilities_type === AVAILABILITIES_TYPE_FIXED ? (int)$service_duration : 15;

            $current_hour = $start_hour;
            $diff = $current_hour->diff($end_hour);

            while (($diff->h * 60 + $diff->i) >= intval($service_duration)) {
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
     * This method will add the extra appointment hours whenever a service accepts multiple attendants.
     *
     * @param string $selected_date The selected appointment date.
     * @param array $service Selected service data.
     * @param array $provider Selected provider data.
     *
     * @return array Returns the available hours array.
     */
    protected function _get_multiple_attendants_hours(
        $selected_date,
        $service,
        $provider
    ) {
        $this->ci->load->model('appointments_model');
        $this->ci->load->model('services_model');
        $this->ci->load->model('providers_model');

        $unavailabilities = $this->ci->appointments_model->get_batch([
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
        $periods = $this->remove_unavailabilities($periods, $unavailabilities);

        $hours = [];

        $interval_value = $service['availabilities_type'] == AVAILABILITIES_TYPE_FIXED ? $service['duration'] : '15';
        $interval = new DateInterval('PT' . (int)$interval_value . 'M');
        $duration = new DateInterval('PT' . (int)$service['duration'] . 'M');

        foreach ($periods as $period) {
            $slot_start = clone $period['start'];
            $slot_end = clone $slot_start;
            $slot_end->add($duration);

            while ($slot_end <= $period['end']) {
                // Check reserved attendants for this time slot and see if current attendants fit.
                $appointment_attendants_number = $this->ci->appointments_model->get_attendants_number_for_period(
                    $slot_start,
                    $slot_end,
                    $service['id']
                );

                if ($appointment_attendants_number < $service['attendants_number']) {
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
     */
    public function remove_breaks($selected_date, $periods, $breaks)
    {
        if (!$breaks) {
            return $periods;
        }

        foreach ($breaks as $break) {
            $break_start = new DateTime($selected_date . ' ' . $break['start']);
            $break_end = new DateTime($selected_date . ' ' . $break['end']);

            foreach ($periods as &$period) {
                $period_start = $period['start'];
                $period_end = $period['end'];

                if ($break_start <= $period_start && $break_end >= $period_start && $break_end <= $period_end) {
                    // left
                    $period['start'] = $break_end;
                    continue;
                }

                if ($break_start >= $period_start && $break_start <= $period_end && $break_end >= $period_start && $break_end <= $period_end) {
                    // middle
                    $period['end'] = $break_start;
                    $periods[] = [
                        'start' => $break_end,
                        'end' => $period_end
                    ];
                    continue;
                }

                if ($break_start >= $period_start && $break_start <= $period_end && $break_end >= $period_end) {
                    // right
                    $period['end'] = $break_start;
                    continue;
                }

                if ($break_start <= $period_start && $break_end >= $period_end) {
                    // break contains period
                    $period['start'] = $break_end;
                    continue;
                }
            }
        }

        return $periods;
    }

    /**
     * Remove the unavailabilities from the available time periods of the selected date.
     *
     * @param array $periods Available time periods.
     * @param array $unavailabilities Unavailabilities of the current date.
     *
     * @return array Returns the available time periods without the unavailabilities.
     */
    public function remove_unavailabilities($periods, $unavailabilities)
    {
        foreach ($unavailabilities as $unavailability) {
            $unavailability_start = new DateTime($unavailability['start_datetime']);
            $unavailability_end = new DateTime($unavailability['end_datetime']);

            foreach ($periods as &$period) {
                $period_start = $period['start'];
                $period_end = $period['end'];

                if ($unavailability_start <= $period_start && $unavailability_end >= $period_start && $unavailability_end <= $period_end) {
                    // left
                    $period['start'] = $unavailability_end;
                    continue;
                }

                if ($unavailability_start >= $period_start && $unavailability_start <= $period_end && $unavailability_end >= $period_start && $unavailability_end <= $period_end) {
                    // middle
                    $period['end'] = $unavailability_start;
                    $periods[] = [
                        'start' => $unavailability_end,
                        'end' => $period_end
                    ];
                    continue;
                }

                if ($unavailability_start >= $period_start && $unavailability_start <= $period_end && $unavailability_end >= $period_end) {
                    // right
                    $period['end'] = $unavailability_start;
                    continue;
                }

                if ($unavailability_start <= $period_start && $unavailability_end >= $period_end) {
                    // Unavaibility contains period
                    $period['start'] = $unavailability_end;
                    continue;
                }
            }
        }

        return $periods;
    }

    public function sendAppointmentNotification($appointment, $service, $provider, $customer, $new_appointment)
    {
        try {
            $this->ci->config->load('email');
            $this->ci->load->model('settings_model');

            $email = new \EA\Engine\Notifications\Email($this->ci, $this->ci->config->config);

            if ($new_appointment){
                $customer_title = new Text($this->lang->line('appointment_booked'));
                $customer_message = new Text($this->lang->line('thank_you_for_appointment'));
                $provider_title = new Text($this->lang->line('appointment_added_to_your_plan'));
                $provider_message = new Text($this->lang->line('appointment_link_description'));
            } else {
                $customer_title = new Text($this->lang->line('appointment_changes_saved'));
                $customer_message = new Text('');
                $provider_title = new Text($this->lang->line('appointment_details_changed'));
                $provider_message = new Text('');
            }

            $customer_link = new Url(site_url('appointments/index/' . $appointment['hash']));
            $provider_link = new Url(site_url('backend/index/' . $appointment['hash']));

            $send_customer = filter_var(
                $this->ci->settings_model->get_setting('customer_notifications'),
                FILTER_VALIDATE_BOOLEAN
            );

            $this->ci->load->library('ics_file');
            $ics_stream = $this->ci->ics_file->get_stream($appointment, $service, $provider, $customer);

            $company_settings = [
                'company_name' => $this->ci->settings_model->get_setting('company_name'),
                'company_email' => $this->ci->settings_model->get_setting('company_email'),
                'company_link' => $this->ci->settings_model->get_setting('company_link'),
                'date_format' => $this->ci->settings_model->get_setting('date_format'),
                'time_format' => $this->ci->settings_model->get_setting('time_format')
            ];

            if ($send_customer === TRUE) {
                $email->sendAppointmentDetails(
                    $appointment,
                    $provider,
                    $service,
                    $customer,
                    $company_settings,
                    $customer_title,
                    $customer_message,
                    $customer_link,
                    new Email($customer['email']),
                    new Text($ics_stream)
                );
            }

            $send_provider = filter_var(
                $this->ci->providers_model->get_setting('notifications', $provider['id']),
                FILTER_VALIDATE_BOOLEAN
            );

            if ($send_provider === TRUE) {
                $email->sendAppointmentDetails(
                    $appointment,
                    $provider,
                    $service,
                    $customer,
                    $company_settings,
                    $provider_title,
                    $provider_message,
                    $provider_link,
                    new Email($provider['email']),
                    new Text($ics_stream)
                );
            }
        } catch (Exception $exc) {
            log_message('error', $exc->getMessage());
            log_message('error', $exc->getTraceAsString());
        }
    }

    /**
     * Cancel an existing appointment.
     *
     * This method removes an appointment from the company's schedule. In order for the appointment to be deleted, the
     * hash string must be provided. The customer can only cancel the appointment if the edit time period is not over
     * yet. Provide the $_POST['cancel_reason'] parameter to describe the cancellation reason.
     *
     * @param string $appointment_hash This is used to distinguish the appointment record.
     */
    public function cancel($appointment_hash, $cancelReason)
    {
        try
        {
            $this->ci->load->model('appointments_model');
            $this->ci->load->model('providers_model');
            $this->ci->load->model('customers_model');
            $this->ci->load->model('services_model');
            $this->ci->load->model('settings_model');

            // Check whether the appointment hash exists in the database.
            $records = $this->ci->appointments_model->get_batch(['hash' => $appointment_hash]);
            if (count($records) == 0)
            {
                throw new Exception('No record matches the provided hash.');
            }

            $appointment = $records[0];
            $provider = $this->ci->providers_model->get_row($appointment['id_users_provider']);
            $customer = $this->ci->customers_model->get_row($appointment['id_users_customer']);
            $service = $this->ci->services_model->get_row($appointment['id_services']);

            $company_settings = [
                'company_name' => $this->ci->settings_model->get_setting('company_name'),
                'company_email' => $this->ci->settings_model->get_setting('company_email'),
                'company_link' => $this->ci->settings_model->get_setting('company_link'),
                'date_format' => $this->ci->settings_model->get_setting('date_format'),
                'time_format' => $this->ci->settings_model->get_setting('time_format')
            ];

            // :: DELETE APPOINTMENT RECORD FROM THE DATABASE.
            if ( ! $this->ci->appointments_model->delete($appointment['id']))
            {
                throw new Exception('Appointment could not be deleted from the database.');
            }

            // :: SYNC APPOINTMENT REMOVAL WITH GOOGLE CALENDAR
            if ($appointment['id_google_calendar'] != NULL)
            {
                try
                {
                    $google_sync = filter_var($this->ci->providers_model
                        ->get_setting('google_sync', $appointment['id_users_provider']), FILTER_VALIDATE_BOOLEAN);

                    if ($google_sync == TRUE)
                    {
                        $google_token = json_decode($this->ci->providers_model
                            ->get_setting('google_token', $provider['id']));
                        $this->ci->load->library('Google_sync');
                        $this->ci->google_sync->refresh_token($google_token->refresh_token);
                        $this->ci->google_sync->delete_appointment($provider, $appointment['id_google_calendar']);
                    }
                }
                catch (Exception $exc)
                {
                    $exceptions[] = $exc;
                }
            }

            // :: SEND NOTIFICATION EMAILS TO CUSTOMER AND PROVIDER
            try
            {
                $this->ci->config->load('email');
                $email = new \EA\Engine\Notifications\Email($this->ci, $this->ci->config->config);

                $send_provider = filter_var($this->ci->providers_model
                    ->get_setting('notifications', $provider['id']), FILTER_VALIDATE_BOOLEAN);

                if ($send_provider === TRUE)
                {
                    $email->sendDeleteAppointment($appointment, $provider,
                        $service, $customer, $company_settings, new Email($provider['email']),
                        new Text($cancelReason));
                }

                $send_customer = filter_var($this->ci->settings_model->get_setting('customer_notifications'),
                    FILTER_VALIDATE_BOOLEAN);

                if ($send_customer === TRUE)
                {
                    $email->sendDeleteAppointment($appointment, $provider,
                        $service, $customer, $company_settings, new Email($customer['email']),
                        new Text($cancelReason));
                }

            }
            catch (Exception $exc)
            {
                $exceptions[] = $exc;
            }
        }
        catch (Exception $exc)
        {
            // Display the error message to the customer.
            $exceptions[] = $exc;
        }
    }
}
