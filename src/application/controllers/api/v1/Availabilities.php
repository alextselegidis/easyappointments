<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/API_V1_Controller.php';
require_once __DIR__ . '/../../Appointments.php';

use \EA\Engine\Api\V1\Response;
use \EA\Engine\Api\V1\Request;
use \EA\Engine\Types\UnsignedInt; 

/**
 * Availabilities Controller
 *
 * @package Controllers
 * @subpackage API
 */
class Availabilities extends API_V1_Controller {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model'); 
        $this->load->model('settings_model');
    }

    /**
     * GET API Method
     *
     * Provide the "providerId", "serviceId" and "date" GET parameters to get the availabilities for a specific date.
     * If no "date" was provided then the current date will be used.
     */
    public function get() {
        try {
            $providerId = new UnsignedInt($this->input->get('providerId')); 
            $serviceId = new UnsignedInt($this->input->get('serviceId')); 

            if ($this->input->get('date')) {
                $date = new DateTime($this->input->get('date')); 
            } else {
                $date = new DateTime();
            }

            $service = $this->services_model->get_row($serviceId->get()); 

            $emptyPeriods = $this->_getProviderAvailableTimePeriods($providerId->get(),
                    $date->format('Y-m-d'), []);
            
            $availableHours = $this->_calculateAvailableHours($emptyPeriods, 
                    $date->format('Y-m-d'), $service['duration'], false);

            $this->output
                ->set_content_type('application/json') 
                ->set_output(json_encode($availableHours));

        } catch(\Exception $exception) {
            exit($this->_handleException($exception)); 
        }   
    }

    /**
     * Get an array containing the free time periods (start - end) of a selected date.
     *
     * This method is very important because there are many cases where the system needs to
     * know when a provider is avaible for an appointment. This method will return an array
     * that belongs to the selected date and contains values that have the start and the end
     * time of an available time period.
     *
     * @param numeric $provider_id The provider's record id.
     * @param string $selected_date The date to be checked (MySQL formatted string).
     * @param array $exclude_appointments This array contains the ids of the appointments that
     * will not be taken into consideration when the available time periods are calculated.
     *
     * @return array Returns an array with the available time periods of the provider.
     */
    protected function _getProviderAvailableTimePeriods($provider_id, $selected_date,
            $exclude_appointments = array()) {
        $this->load->model('appointments_model');
        $this->load->model('providers_model');

        // Get the provider's working plan and reserved appointments.
        $working_plan = json_decode($this->providers_model->get_setting('working_plan', $provider_id), TRUE);

        $where_clause = array(
            'id_users_provider' => $provider_id
        );

        $reserved_appointments = $this->appointments_model->get_batch($where_clause);

        // Sometimes it might be necessary to not take into account some appointment records
        // in order to display what the providers' available time periods would be without them.
        foreach ($exclude_appointments as $excluded_id) {
            foreach ($reserved_appointments as $index => $reserved) {
                if ($reserved['id'] == $excluded_id) {
                    unset($reserved_appointments[$index]);
                }
            }
        }

        // Find the empty spaces on the plan. The first split between the plan is due to
        // a break (if exist). After that every reserved appointment is considered to be
        // a taken space in the plan.
        $selected_date_working_plan = $working_plan[strtolower(date('l', strtotime($selected_date)))];
        $available_periods_with_breaks = array();

        if (isset($selected_date_working_plan['breaks'])) {
            $start = new DateTime($selected_date_working_plan['start']);
            $end = new DateTime($selected_date_working_plan['end']);
            $available_periods_with_breaks[] = array(
                'start' => $selected_date_working_plan['start'],
                'end' => $selected_date_working_plan['end']
            );

            // Split the working plan to available time periods that do not contain the breaks in them.
            foreach ($selected_date_working_plan['breaks'] as $index => $break) {
                $break_start = new DateTime($break['start']);
                $break_end = new DateTime($break['end']);

                if ($break_start < $start) {
                    $break_start = $start;
                }

                if ($break_end > $end) {
                    $break_end = $end;
                }

                if ($break_start >= $break_end) {
                    continue;
                }

                foreach ($available_periods_with_breaks as $key => $open_period) {
                    $s = new DateTime($open_period['start']);
                    $e = new DateTime($open_period['end']);

                    if ($s < $break_end && $break_start < $e) { // check for overlap
                        $changed = FALSE;
                        if ($s < $break_start) {
                            $open_start = $s;
                            $open_end = $break_start;
                            $available_periods_with_breaks[] = array(
                                'start' => $open_start->format("H:i"),
                                'end' => $open_end->format("H:i")
                            );
                            $changed = TRUE;
                        }

                        if ($break_end < $e) {
                            $open_start = $break_end;
                            $open_end = $e;
                            $available_periods_with_breaks[] = array(
                                'start' => $open_start->format("H:i"),
                                'end' => $open_end->format("H:i")
                            );
                            $changed = TRUE;
                        }

                        if ($changed) {
                            unset($available_periods_with_breaks[$key]);
                        }
                    }
                }
            }
        }

        // Break the empty periods with the reserved appointments.
        $available_periods_with_appointments = $available_periods_with_breaks;

        foreach($reserved_appointments as $appointment) {
            foreach($available_periods_with_appointments as $index => &$period) {
                $a_start = strtotime($appointment['start_datetime']);
                $a_end =  strtotime($appointment['end_datetime']);
                $p_start = strtotime($selected_date .  ' ' . $period['start']);
                $p_end = strtotime($selected_date .  ' ' .$period['end']);

                if ($a_start <= $p_start && $a_end <= $p_end && $a_end <= $p_start) {
                    // The appointment does not belong in this time period, so we
                    // will not change anything.
                } else if ($a_start <= $p_start && $a_end <= $p_end && $a_end >= $p_start) {
                    // The appointment starts before the period and finishes somewhere inside.
                    // We will need to break this period and leave the available part.
                    $period['start'] = date('H:i', $a_end);
                } else if ($a_start >= $p_start && $a_end <= $p_end) {
                    // The appointment is inside the time period, so we will split the period
                    // into two new others.
                    unset($available_periods_with_appointments[$index]);
                    $available_periods_with_appointments[] = array(
                        'start' => date('H:i', $p_start),
                        'end' => date('H:i', $a_start)
                    );
                    $available_periods_with_appointments[] = array(
                        'start' => date('H:i', $a_end),
                        'end' => date('H:i', $p_end)
                    );
                } else if ($a_start >= $p_start && $a_end >= $p_start && $a_start <= $p_end) {
                    // The appointment starts in the period and finishes out of it. We will
                    // need to remove the time that is taken from the appointment.
                    $period['end'] = date('H:i', $a_start);
                } else if ($a_start >= $p_start && $a_end >= $p_end && $a_start >= $p_end) {
                    // The appointment does not belong in the period so do not change anything.
                } else if ($a_start <= $p_start && $a_end >= $p_end && $a_start <= $p_end) {
                    // The appointment is bigger than the period, so this period needs to be removed.
                    unset($available_periods_with_appointments[$index]);
                }
            }
        }

        return array_values($available_periods_with_appointments);
    }

    /**
     * Calculate the avaialble appointment hours.
     *
     * Calculate the available appointment hours for the given date. The empty spaces
     * are broken down to 15 min and if the service fit in each quarter then a new
     * available hour is added to the "$available_hours" array.
     *
     * @param array $empty_periods Contains the empty periods as generated by the
     * "_getProviderAvailableTimePeriods" method.
     * @param string $selected_date The selected date to be search (format )
     * @param numeric $service_duration The service duration is required for the hour calculation.
     * @param bool $manage_mode (optional) Whether we are currently on manage mode (editing an existing appointment).
     *
     * @return array Returns an array with the available hours for the appointment.
     */
    protected function _calculateAvailableHours(array $empty_periods, $selected_date, $service_duration,
            $manage_mode = FALSE) {
        $this->load->model('settings_model');

        $available_hours = array();

        foreach ($empty_periods as $period) {
            $start_hour = new DateTime($selected_date . ' ' . $period['start']);
            $end_hour = new DateTime($selected_date . ' ' . $period['end']);

            $minutes = $start_hour->format('i');

            if ($minutes % 15 != 0) {
                // Change the start hour of the current space in order to be
                // on of the following: 00, 15, 30, 45.
                if ($minutes < 15) {
                    $start_hour->setTime($start_hour->format('H'), 15);
                } else if ($minutes < 30) {
                    $start_hour->setTime($start_hour->format('H'), 30);
                } else if ($minutes < 45) {
                    $start_hour->setTime($start_hour->format('H'), 45);
                } else {
                    $start_hour->setTime($start_hour->format('H') + 1, 00);
                }
            }

            $current_hour = $start_hour;
            $diff = $current_hour->diff($end_hour);

            while (($diff->h * 60 + $diff->i) >= intval($service_duration)) {
                $available_hours[] = $current_hour->format('H:i');
                $current_hour->add(new DateInterval("PT15M"));
                $diff = $current_hour->diff($end_hour);
            }
        }

        // If the selected date is today, remove past hours. It is important  include the timeout before
        // booking that is set in the backoffice the system. Normally we might want the customer to book
        // an appointment that is at least half or one hour from now. The setting is stored in minutes.
        if (date('m/d/Y', strtotime($selected_date)) === date('m/d/Y')) {
            $book_advance_timeout = $this->settings_model->get_setting('book_advance_timeout');

            foreach($available_hours as $index => $value) {
                $available_hour = strtotime($value);
                $current_hour = strtotime('+' . $book_advance_timeout . ' minutes', strtotime('now'));
                if ($available_hour <= $current_hour) {
                    unset($available_hours[$index]);
                }
            }
        }

        $available_hours = array_values($available_hours);
        sort($available_hours, SORT_STRING );
        $available_hours = array_values($available_hours);

        return $available_hours;
    }
}

/* End of file Appointments.php */
/* Location: ./application/controllers/api/v1/Appointments.php */
