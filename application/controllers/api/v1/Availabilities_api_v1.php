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
 * Availabilities API v1 controller.
 *
 * @package Controllers
 */
class Availabilities_api_v1 extends EA_Controller
{
    /**
     * Availabilities_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');

        $this->api->auth();

        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');

        $this->load->library('availability');
    }

    /**
     * Generate the available hours based on the selected date, service and provider.
     *
     * This resource requires the following query parameters:
     *
     *   - serviceId
     *   - providerI
     *   - date
     *
     * Based on those values it will generate the available hours, just like how the booking page works.
     *
     * You can then safely create a new appointment starting on one of the selected hours.
     *
     * Notice: The returned hours are in the provider's timezone.
     *
     * If no date parameter is provided then the current date will be used.
     */
    public function get(): void
    {
        try {
            $provider_id = request('providerId');

            $service_id = request('serviceId');

            $date = request('date');

            if (!$date) {
                $date = date('Y-m-d');
            }

            $provider = $this->providers_model->find($provider_id);

            $service = $this->services_model->find($service_id);

            $available_hours = $this->availability->get_available_hours($date, $service, $provider);

            json_response($available_hours);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
