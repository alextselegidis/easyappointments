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
class Booking_confirmation extends EA_Controller
{
    /**
     * Booking_confirmation constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('customers_model');

        $this->load->library('ics_file');
    }

    /**
     * Display the appointment registration success page.
     *
     * @throws Exception
     */
    public function of(): void
    {
        $appointment_hash = $this->uri->segment(3);

        $occurrences = $this->appointments_model->get(['hash' => $appointment_hash]);

        if (empty($occurrences)) {
            redirect('appointments'); // The appointment does not exist.

            return;
        }

        $appointment = $occurrences[0];

        html_vars([
            'page_title' => lang('success'),
            'company_color' => setting('company_color'),
            'appointment_hash' => $appointment_hash,
        ]);

        $this->load->view('pages/booking_confirmation');
    }

    /**
     * Download the appointment as an ICS file.
     *
     * @throws Exception
     */
    public function ics(): void
    {
        $appointment_hash = $this->uri->segment(3);

        if (empty($appointment_hash) || !preg_match('/^[a-zA-Z0-9]{12}$/', $appointment_hash)) {
            show_error('Invalid appointment hash.', 400);
            return;
        }

        $occurrences = $this->appointments_model->get(['hash' => $appointment_hash]);

        if (empty($occurrences)) {
            show_error('Appointment not found.', 404);
            return;
        }

        $appointment = $occurrences[0];

        $provider = $this->providers_model->find($appointment['id_users_provider']);
        $service = $this->services_model->find($appointment['id_services']);
        $customer = $this->customers_model->find($appointment['id_users_customer']);

        $ics_stream = $this->ics_file->get_stream($appointment, $service, $provider, $customer);

        $filename = 'appointment-' . date('Y-m-d', strtotime($appointment['start_datetime'])) . '.ics';

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode($filename));
        header('Content-Length: ' . strlen($ics_stream));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('X-Content-Type-Options: nosniff');

        echo $ics_stream;
    }
}
