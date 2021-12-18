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
 * Booking cancellation controller.
 *
 * Handles the booking cancellation related operations.
 *
 * @package Controllers
 */
class Booking_cancellation extends EA_Controller {
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

        $this->load->library('synchronization');
        $this->load->library('notifications');
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
    public function of(string $appointment_hash)
    {
        try
        {
            $exceptions = [];

            $occurrences = $this->appointments_model->get(['hash' => $appointment_hash]);

            if (empty($occurrences))
            {
                throw new Exception('No record matches the provided hash.');
            }

            $appointment = $occurrences[0];

            $provider = $this->providers_model->find($appointment['id_users_provider']);

            $customer = $this->customers_model->find($appointment['id_users_customer']);

            $service = $this->services_model->find($appointment['id_services']);

            $settings = [
                'company_name' => setting('company_name'),
                'company_email' => setting('company_email'),
                'company_link' => setting('company_link'),
                'date_format' => setting('date_format'),
                'time_format' => setting('time_format')
            ];

            $this->appointments_model->delete($appointment['id']);

            $this->synchronization->sync_appointment_deleted($appointment, $provider);

            $this->notifications->notify_appointment_deleted($appointment, $service, $provider, $customer, $settings);
        }
        catch (Throwable $e)
        {
            $exceptions[] = $e;
        }

        html_vars([
            'message_title' => lang('appointment_cancelled_title'),
            'message_text' => lang('appointment_cancelled'),
            'message_icon' => base_url('assets/img/success.png'),
            'exceptions' => $exceptions
        ]);

        $this->load->view('pages/booking_message', html_vars());
    }
}
