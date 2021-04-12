<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/API_V1_Controller.php';
require_once __DIR__ . '/../../Appointments.php';

/**
 * Availabilities Controller
 *
 * @package Controllers
 */
class Availabilities extends API_V1_Controller {
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
        $this->load->library('availability');
    }

    /**
     * GET API Method
     *
     * Provide the "providerId", "serviceId" and "date" GET parameters to get the availabilities for a specific date.
     * If no "date" was provided then the current date will be used.
     */
    public function get()
    {
        try
        {
            $provider_id = $this->input->get('providerId');

            $service_id = $this->input->get('serviceId');

            $date = $this->input->get('date');

            if ( ! $date)
            {
                $date = date('Y-m-d');
            }

            $provider = $this->providers_model->get_row($provider_id);

            $service = $this->services_model->get_row($service_id);

            $available_hours = $this->availability->get_available_hours($date, $service, $provider);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($available_hours));
        }
        catch (Exception $exception)
        {
            $this->handle_exception($exception);
        }
    }
}
