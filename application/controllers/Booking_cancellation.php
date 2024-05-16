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
 * Booking cancellation controller.
 *
 * Handles the booking cancellation related operations.
 *
 * @package Controllers
 */
class Booking_cancellation extends EA_Controller
{
    /**
     * Booking_cancellation constructor.
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
        $this->load->library('webhooks_client');
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
    public function of(string $appointment_hash): void
    {
        try {
            $disable_booking = setting('disable_booking');

            if ($disable_booking) {
                abort(403);
            }

            $cancellation_reason = request('cancellation_reason');

            if ($this->input->method() !== 'post' || empty($cancellation_reason)) {
                abort(403, 'Forbidden');
            }

            $occurrences = $this->appointments_model->get(['hash' => $appointment_hash]);

            if (empty($occurrences)) {
                html_vars([
                    'page_title' => lang('appointment_not_found'),
                    'company_color' => setting('company_color'),
                    'message_title' => lang('appointment_not_found'),
                    'message_text' => lang('appointment_does_not_exist_in_db'),
                    'message_icon' => base_url('assets/img/error.png'),
                    'google_analytics_code' => setting('google_analytics_code'),
                    'matomo_analytics_url' => setting('matomo_analytics_url'),
                    'matomo_analytics_site_id' => setting('matomo_analytics_site_id'),
                ]);

                $this->load->view('pages/booking_message');

                return;
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
                'time_format' => setting('time_format'),
            ];

            $this->appointments_model->delete($appointment['id']);

            $this->synchronization->sync_appointment_deleted($appointment, $provider);

            $this->notifications->notify_appointment_deleted(
                $appointment,
                $service,
                $provider,
                $customer,
                $settings,
                $cancellation_reason,
            );

            $this->webhooks_client->trigger(WEBHOOK_APPOINTMENT_DELETE, $appointment);
        } catch (Throwable $e) {
            log_message('error', 'Booking Cancellation Exception: ' . $e->getMessage());
        }

        html_vars([
            'page_title' => lang('appointment_cancelled_title'),
            'company_color' => setting('company_color'),
            'google_analytics_code' => setting('google_analytics_code'),
            'matomo_analytics_url' => setting('matomo_analytics_url'),
            'matomo_analytics_site_id' => setting('matomo_analytics_site_id'),
        ]);

        $this->load->view('pages/booking_cancellation');
    }
}
