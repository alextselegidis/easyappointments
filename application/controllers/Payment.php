<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      F.González <fernando@tuta.io>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Payment confirmation controller.
 *
 * Handles the confirmation of a payment.
 *
 *
 * @package Controllers
 */
class Payment extends EA_Controller {
    /**
     * Booking constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('admins_model');
        $this->load->model('secretaries_model');
        $this->load->model('categories_model');
        $this->load->model('services_model');
        $this->load->model('customers_model');
        $this->load->model('settings_model');
        $this->load->model('consents_model');

        $this->load->library('timezones');
        $this->load->library('synchronization');
        $this->load->library('notifications');
        $this->load->library('availability');
        $this->load->library('webhooks_client');

        $this->load->driver('cache', ['adapter' => 'file']);
    }

    /**
     * Render the payment confirmation page.
     */
    public function index()
    {
        if ( ! is_app_installed())
        {
            redirect('installation');

            return;
        }

        $appointment = html_vars('appointment');

        if (empty($appointment)) {
            abort(404, "Forbidden");
        } else {
            $manage_mode = TRUE;
            $company_name = setting('company_name');
            $company_logo = setting('company_logo');
            $company_color = setting('company_color');
            $google_analytics_code = setting('google_analytics_code');
            $matomo_analytics_url = setting('matomo_analytics_url');
            $date_format = setting('date_format');
            $time_format = setting('time_format');

            $display_first_name = setting('display_first_name');
            $require_first_name = setting('require_first_name');
            $display_last_name = setting('display_last_name');
            $require_last_name = setting('require_last_name');
            $display_email = setting('display_email');
            $require_email = setting('require_email');
            $display_phone_number = setting('display_phone_number');
            $require_phone_number = setting('require_phone_number');
            $display_address = setting('display_address');
            $require_address = setting('require_address');
            $display_city = setting('display_city');
            $require_city = setting('require_city');
            $display_zip_code = setting('display_zip_code');
            $require_zip_code = setting('require_zip_code');
            $display_notes = setting('display_notes');
            $require_notes = setting('require_notes');
            $display_cookie_notice = setting('display_cookie_notice');
            $cookie_notice_content = setting('cookie_notice_content');
            $display_terms_and_conditions = setting('display_terms_and_conditions');
            $terms_and_conditions_content = setting('terms_and_conditions_content');
            $display_privacy_policy = setting('display_privacy_policy');
            $privacy_policy_content = setting('privacy_policy_content');

            $theme = request('theme', setting('theme', 'default'));
            if (empty($theme) || ! file_exists(__DIR__ . '/../../assets/css/themes/' . $theme . '.min.css'))
            {
                $theme = 'default';
            }

            $timezones = $this->timezones->to_array();
            $grouped_timezones = $this->timezones->to_grouped_array();
            $provider = $this->providers_model->find($appointment['id_users_provider']);
            $customer = $this->customers_model->find($appointment['id_users_customer']);

            script_vars([
                'date_format' => $date_format,
                'time_format' => $time_format,
                'display_cookie_notice' => $display_cookie_notice,
                'display_any_provider' => setting('display_any_provider'),
            ]);

            html_vars([
                'theme' => $theme,
                'company_name' => $company_name,
                'company_logo' => $company_logo,
                'company_color' => $company_color === '#ffffff' ? '' : $company_color,
                'date_format' => $date_format,
                'time_format' => $time_format,
                'display_first_name' => $display_first_name,
                'display_last_name' => $display_last_name,
                'display_email' => $display_email,
                'display_phone_number' => $display_phone_number,
                'display_address' => $display_address,
                'display_city' => $display_city,
                'display_zip_code' => $display_zip_code,
                'display_notes' => $display_notes,
                'google_analytics_code' => $google_analytics_code,
                'matomo_analytics_url' => $matomo_analytics_url,
                'timezones' => $timezones,
                'grouped_timezones' => $grouped_timezones,
                'appointment' => $appointment,
                'provider' => $provider,
                'customer' => $customer,
            ]);

            $this->load->view('pages/payment');
        }
    }

    /**
     * Validates Stripe payment and render confirmation screen for the appointment.
     *
     * This method sets a flag as paid for an appointment and call the "index" callback
     * to handle the page rendering.
     *
     * @param string $checkout_session_id Stripe session id.
     */
    public function confirm(string $checkout_session_id)
    {
        try
        {
            $stripe_api_key = config('stripe_api_key');

            $stripe = new \Stripe\StripeClient($stripe_api_key);

            $session = $stripe->checkout->sessions->retrieve($checkout_session_id);

            $appointment_hash = $session->client_reference_id;
            $payment_intent = $session->payment_intent;

            $appointment = $this->set_paid($appointment_hash, $payment_intent);

            html_vars(['appointment' => $appointment]);

            $this->index();
        }
        catch (Throwable $e)
        {
            error_log( $e );
            abort(500, 'Internal server error');
        }
    }

    /**
     * Sets a paid flag and paid intent for an appointment to track paid bookings.
     */
    private function set_paid($appointment_hash, $payment_intent)
    {
        try
        {
            $manage_mode = TRUE;

            $occurrences = $this->appointments_model->get(['hash' => $appointment_hash]);

            if (empty($occurrences))
            {
                abort(404, 'Not Found');
            }

            $appointment = $occurrences[0];

            $provider = $this->providers_model->find($appointment['id_users_provider']);

            $customer = $this->customers_model->find($appointment['id_users_customer']);

            $service = $this->services_model->find($appointment['id_services']);


            $appointment['is_paid'] = 1;
            $appointment['payment_intent'] = $payment_intent;
            $this->appointments_model->only($appointment, [
                'id',
                'start_datetime',
                'end_datetime',
                'location',
                'notes',
                'color',
                'is_unavailability',
                'id_users_provider',
                'id_users_customer',
                'id_services',
                'is_paid',
                'payment_intent',
            ]);
            $appointment_id = $this->appointments_model->save($appointment);
            $appointment = $this->appointments_model->find($appointment_id);

            $settings = [
                'company_name' => setting('company_name'),
                'company_link' => setting('company_link'),
                'company_email' => setting('company_email'),
                'date_format' => setting('date_format'),
                'time_format' => setting('time_format')
            ];

            $this->synchronization->sync_appointment_saved($appointment, $service, $provider, $customer, $settings, $manage_mode);

            $this->notifications->notify_appointment_saved($appointment, $service, $provider, $customer, $settings, $manage_mode);

            $this->webhooks_client->trigger(WEBHOOK_APPOINTMENT_SAVE, $appointment);

            return $appointment;
        }
        catch (Throwable $e)
        {
            error_log( $e );
            abort(500, 'Internal server error');
        }
    }

}
