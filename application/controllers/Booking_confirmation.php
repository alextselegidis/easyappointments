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

        $this->load->library('google_sync');
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

        $add_to_google_url = $this->google_sync->get_add_to_google_url($appointment['id']);

        $service = $this->services_model->find($appointment['id_services']);

        $customer = $this->customers_model->find($appointment['id_users_customer']);

        $payment_link = null;
        if( isset($service['payment_link']) && !empty(trim($service['payment_link']))){
            $payment_link_vars = array(
                '{$appointment_hash}' => $appointment['hash'],
                '{$customer_email}' => $customer['email'],
            );
            $payment_link_template = $service['payment_link']
                . (str_contains($service['payment_link'], '?')
                    ? '' : '?')
                . 'client_reference_id={$appointment_hash}&prefilled_email={$customer_email}';

            $payment_link = strtr($payment_link_template, $payment_link_vars);
        }

        html_vars([
            'page_title' => lang('success'),
            'company_color' => setting('company_color'),
            'google_analytics_code' => setting('google_analytics_code'),
            'matomo_analytics_url' => setting('matomo_analytics_url'),
            'add_to_google_url' => $add_to_google_url,
            'is_paid' => $appointment['is_paid'],
            'payment_link' => $payment_link,
        ]);

        $this->load->view('pages/booking_confirmation');
    }
}
