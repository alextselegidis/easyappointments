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
     *
     * @param string $appointment_hash The appointment hash identifier.
     *
     * @throws Exception
     */
    public function of(string $appointment_hash)
    {
        $occurrences = $this->appointments_model->get(['hash' => $appointment_hash]);

        if (empty($occurrences))
        {
            redirect('appointments'); // The appointment does not exist.
            
            return;
        }

        $appointment = $occurrences[0];

        unset($appointment['notes']);

        $customer = $this->customers_model->find($appointment['id_users_customer']);

        $provider = $this->providers_model->find($appointment['id_users_provider']);

        $service = $this->services_model->find($appointment['id_services']);

        $company_name = setting('company_name');

        $exceptions = $this->session->flashdata('book_success') ?? [];

        $this->load->view('pages/booking_confirmation', [
            'page_title' => lang('success'),
            'appointment_data' => $appointment,
            'provider_data' => [
                'id' => $provider['id'],
                'first_name' => $provider['first_name'],
                'last_name' => $provider['last_name'],
                'email' => $provider['email'],
                'timezone' => $provider['timezone'],
            ],
            'customer_data' => [
                'id' => $customer['id'],
                'first_name' => $customer['first_name'],
                'last_name' => $customer['last_name'],
                'email' => $customer['email'],
                'timezone' => $customer['timezone'],
            ],
            'service_data' => $service,
            'company_name' => $company_name,
            'exceptions' => $exceptions,
            'scripts' => [
                'https://apis.google.com/js/client.js',
                asset_url('assets/vendor/datejs/date.min.js'),
                asset_url('assets/vendor/moment/moment.min.js'),
                asset_url('assets/vendor/moment-timezone/moment-timezone-with-data.min.js'),
                asset_url('assets/js/frontend_book_success.js'),
                asset_url('assets/js/general_functions.js')
            ]
        ]);
    }
}
