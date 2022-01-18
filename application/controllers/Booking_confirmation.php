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
 * Booking confirmation controller.
 *
 * Handles the booking confirmation related operations.
 *
 * @package Controllers
 */
class Booking_confirmation extends EA_Controller {
    /**
     * Booking constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('customers_model');
    }

    /**
     * Display the appointment registration success page.
     */
    public function of()
    {
        $appointment_hash = $this->uri->segment(3);

        $occurrences = $this->appointments_model->get(['hash' => $appointment_hash]);

        if (empty($occurrences))
        {
            redirect('appointments'); // The appointment does not exist.

            return;
        }

        $appointment = $occurrences[0];

        unset($appointment['notes']);

        $provider = $this->providers_model->find($appointment['id_users_provider']);

        $this->providers_model->only($provider, [
            'id',
            'first_name',
            'last_name',
            'email',
            'timezone'
        ]);

        $service = $this->services_model->find($appointment['id_services']);

        $this->services_model->only($service, [
            'id',
            'first_name',
            'last_name',
            'email',
            'timezone'
        ]);

        $company_name = setting('company_name');

        script_vars([
            'appointment_data' => $appointment,
            'provider_data' => $provider,
            'service_data' => $service,
            'company_name' => $company_name,
            'google_api_scope' => 'https://www.googleapis.com/auth/calendar',
            'google_api_key' => config('google_api_key'),
            'google_client_id' => config('google_api_key'),
        ]);

        html_vars([
            'page_title' => lang('success'),
        ]);

        $this->load->view('pages/booking_confirmation', html_vars());
    }
}
