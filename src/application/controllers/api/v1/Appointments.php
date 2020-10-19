<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/API_V1_Controller.php';

use \EA\Engine\Api\V1\Response;
use \EA\Engine\Api\V1\Request;
use \EA\Engine\Types\NonEmptyText;

/**
 * Appointments Controller
 *
 * @package Controllers
 * @subpackage API
 */
class Appointments extends API_V1_Controller {
    /**
     * Appointments Resource Parser
     *
     * @var \EA\Engine\Api\V1\Parsers\Appointments
     */
    protected $parser;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('appointments_model');
        $this->parser = new \EA\Engine\Api\V1\Parsers\Appointments;
        $this->load->library('appointmentservice');
        
        // Call service
        $this->appointmentservice->service1();
    }

    /**
     * GET API Method
     *
     * @param int $id Optional (null), the record ID to be returned.
     */
    public function get($id = NULL)
    {
        try
        {
            $conditions = [
                'is_unavailable' => FALSE
            ];

            if ($id !== NULL)
            {
                $conditions['id'] = $id;
            }

            $appointments = $this->appointments_model->get_batch($conditions, array_key_exists('aggregates', $_GET));

            if ($id !== NULL && count($appointments) === 0)
            {
                $this->_throwRecordNotFound();
            }

            $response = new Response($appointments);

            $response->encode($this->parser)
                ->search()
                ->sort()
                ->paginate()
                ->minimize()
                ->singleEntry($id)
                ->output();

        }
        catch (\Exception $exception)
        {
            exit($this->_handleException($exception));
        }
    }

    /**
     * POST API Method
     */
    public function post()
    {
        try
        {
            // Insert the appointment to the database. 
            $request = new Request();
            $appointment = $request->getBody();
            $this->parser->decode($appointment);

            if (isset($appointment['id']))
            {
                unset($appointment['id']);
            }

            if (!$this->_check_datetime_availability($appointment)){
                $this->_throwRecordAlreadyExists('Provider not available.');
            }

            $id = $this->appointments_model->add($appointment);

            // Fetch the new object from the database and return it to the client.
            $batch = $this->appointments_model->get_batch('id = ' . $id);
            $response = new Response($batch);
            $status = new NonEmptyText('201 Created');
            $response->encode($this->parser)->singleEntry(TRUE)->output($status);
        }
        catch (\Exception $exception)
        {
            exit($this->_handleException($exception));
        }
    }

    /**
     * PUT API Method
     *
     * @param int $id The record ID to be updated.
     */
    public function put($id)
    {
        try
        {
            // Update the appointment record. 
            $batch = $this->appointments_model->get_batch('id = ' . $id);

            if ($id !== NULL && count($batch) === 0)
            {
                $this->_throwRecordNotFound();
            }

            $request = new Request();
            $updatedAppointment = $request->getBody();
            $baseAppointment = $batch[0];
            $this->parser->decode($updatedAppointment, $baseAppointment);
            $updatedAppointment['id'] = $id;
            $id = $this->appointments_model->add($updatedAppointment);

            // Fetch the updated object from the database and return it to the client.
            $batch = $this->appointments_model->get_batch('id = ' . $id);
            $response = new Response($batch);
            $response->encode($this->parser)->singleEntry($id)->output();
        }
        catch (\Exception $exception)
        {
            exit($this->_handleException($exception));
        }
    }

    /**
     * DELETE API Method
     *
     * @param int $id The record ID to be deleted.
     */
    public function delete($id)
    {
        try
        {
            $this->appointments_model->delete($id);

            $response = new Response([
                'code' => 200,
                'message' => 'Record was deleted successfully!'
            ]);

            $response->output();
        }
        catch (\Exception $exception)
        {
            exit($this->_handleException($exception));
        }
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
    protected function _check_datetime_availability($appointment)
    {
        $this->load->model('services_model');
        $this->load->model('appointments_model');

        $service_duration = $this->services_model->get_value('duration', $appointment['id_services']);

        $exclude_appointments = (isset($appointment['id'])) ? [$appointment['id']] : [];

        $attendants_number = $this->services_model->get_value('attendants_number', $appointment['id_services']);

        if ($attendants_number > 1)
        {
            // Exclude all the appointments that are currently registered.
            $exclude = $this->appointments_model->get_batch([
                'id_services' => $appointment['id_services'],
                'start_datetime' => $appointment['start_datetime']
            ]);

            if ( ! empty($exclude) && count($exclude) < $attendants_number)
            {
                foreach ($exclude as $entry)
                {
                    $exclude_appointments[] = $entry['id'];
                }
            }
        }

        if ($appointment['id_users_provider'] === ANY_PROVIDER)
        {
            $appointment['id_users_provider'] = $this->_search_any_provider($appointment['id_services'],
                date('Y-m-d', strtotime($appointment['start_datetime'])));
            $_POST['post_data']['appointment']['id_users_provider'] = $appointment['id_users_provider'];
            return TRUE; // The selected provider is always available.
        }

        $available_periods = $this->_get_provider_available_time_periods(
            $appointment['id_users_provider'], $appointment['id_services'],
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

        return $is_still_available;
    }

    protected function _get_provider_available_time_periods(
        $provider_id,
        $service_id,
        $selected_date,
        $excluded_appointment_ids = []
    ) {
        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');

        // Get the service, provider's working plan and provider appointments.
        $working_plan = json_decode($this->providers_model->get_setting('working_plan', $provider_id), TRUE);

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
                $period_start = new DateTime($selected_date . ' ' . $period['start']);
                $period_end = new DateTime($selected_date . ' ' . $period['end']);

                if ($appointment_start <= $period_start && $appointment_end <= $period_end && $appointment_end <= $period_start)
                {
                    // The appointment does not belong in this time period, so we  will not change anything.
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

}
