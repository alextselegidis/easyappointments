<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

use EA\Engine\Notifications\Email as EmailClient;
use EA\Engine\Types\Email;
use EA\Engine\Types\Text;
use EA\Engine\Types\Url;

/**
 * Class Notifications
 *
 * Handles the system notifications (mostly related to scheduling changes).
 */
class Notifications {
    /**
     * @var EA_Controller
     */
    protected $CI;

    /**
     * Notifications constructor.
     */
    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('providers_model');
        $this->CI->load->model('secretaries_model');
        $this->CI->load->model('secretaries_model');
        $this->CI->load->model('admins_model');
        $this->CI->load->model('appointments_model');
        $this->CI->load->model('settings_model');

        $this->CI->load->library('ics_file');
        $this->CI->load->library('timezones');

        $this->CI->config->load('email');
    }

    /**
     * Send the required notifications, related to an appointment creation/modification.
     *
     * @param array $appointment Appointment record.
     * @param array $service Service record.
     * @param array $provider Provider record.
     * @param array $customer Customer record.
     * @param array $settings Required settings for the notification content.
     * @param bool|false $manage_mode
     */
    public function notify_appointment_saved($appointment, $service, $provider, $customer, $settings, $manage_mode = FALSE)
    {
        try
        {
            $email = new EmailClient($this->CI, $this->CI->config->config);

            if ($manage_mode)
            {
                $customer_title = new Text(lang('appointment_changes_saved'));
                $customer_message = new Text('');
                $provider_title = new Text(lang('appointment_details_changed'));
                $provider_message = new Text('');
            }
            else
            {
                $customer_title = new Text(lang('appointment_booked'));
                $customer_message = new Text(lang('thank_you_for_appointment'));
                $provider_title = new Text(lang('appointment_added_to_your_plan'));
                $provider_message = new Text(lang('appointment_link_description'));
            }

            $customer_link = new Url(site_url('appointments/index/' . $appointment['hash']));
            $provider_link = new Url(site_url('backend/index/' . $appointment['hash']));

            $ics_stream = $this->CI->ics_file->get_stream($appointment, $service, $provider, $customer);

            $send_customer = filter_var(
                $this->CI->settings_model->get_setting('customer_notifications'),
                FILTER_VALIDATE_BOOLEAN);

            if ($send_customer === TRUE)
            {
                $email->send_appointment_details($appointment, $provider,
                    $service, $customer, $settings, $customer_title,
                    $customer_message, $customer_link, new Email($customer['email']), new Text($ics_stream), $customer['timezone']);
            }

            $send_provider = filter_var(
                $this->CI->providers_model->get_setting('notifications', $provider['id']),
                FILTER_VALIDATE_BOOLEAN);

            if ($send_provider === TRUE)
            {
                $email->send_appointment_details($appointment, $provider,
                    $service, $customer, $settings, $provider_title,
                    $provider_message, $provider_link, new Email($provider['email']), new Text($ics_stream), $provider['timezone']);
            }

            // Notify admins
            $admins = $this->CI->admins_model->get_batch();

            foreach ($admins as $admin)
            {
                if ($admin['settings']['notifications'] === '0')
                {
                    continue;
                }

                $email->send_appointment_details($appointment, $provider,
                    $service, $customer, $settings, $provider_title,
                    $provider_message, $provider_link, new Email($admin['email']), new Text($ics_stream), $admin['timezone']);
            }

            // Notify secretaries
            $secretaries = $this->CI->secretaries_model->get_batch();

            foreach ($secretaries as $secretary)
            {
                if ($secretary['settings']['notifications'] === '0')
                {
                    continue;
                }

                if (!in_array($provider['id'], $secretary['providers']))
                {
                    continue;
                }

                $email->send_appointment_details($appointment, $provider,
                    $service, $customer, $settings, $provider_title,
                    $provider_message, $provider_link, new Email($secretary['email']), new Text($ics_stream), $secretary['timezone']);
            }
        }
        catch (Exception $exception)
        {
            log_message('error', $exception->getMessage());
            log_message('error', $exception->getTraceAsString());
        }
    }

    /**
     * Send the required notifications, related to an appointment removal.
     *
     * @param array $appointment Appointment record.
     * @param array $service Service record.
     * @param array $provider Provider record.
     * @param array $customer Customer record.
     * @param array $settings Required settings for the notification content.
     */
    public function notify_appointment_deleted($appointment, $service, $provider, $customer, $settings)
    {
        // Send email notification to customer and provider.
        try
        {
            $email = new EmailClient($this->CI, $this->CI->config->config);

            $send_provider = filter_var($this->CI->providers_model->get_setting('notifications', $provider['id']),
                FILTER_VALIDATE_BOOLEAN);

            if ($send_provider === TRUE)
            {
                $email->send_delete_appointment($appointment, $provider,
                    $service, $customer, $settings, new Email($provider['email']),
                    new Text($this->CI->input->post('cancel_reason')));
            }

            $send_customer = filter_var(
                $this->CI->settings_model->get_setting('customer_notifications'),
                FILTER_VALIDATE_BOOLEAN);

            if ($send_customer === TRUE)
            {
                $email->send_delete_appointment($appointment, $provider,
                    $service, $customer, $settings, new Email($customer['email']),
                    new Text($this->CI->input->post('cancel_reason')));
            }

            // Notify admins
            $admins = $this->CI->admins_model->get_batch();

            foreach ($admins as $admin)
            {
                if ($admin['settings']['notifications'] === '0')
                {
                    continue;
                }

                $email->send_delete_appointment($appointment, $provider,
                    $service, $customer, $settings, new Email($admin['email']),
                    new Text($this->CI->input->post('cancel_reason')));
            }

            // Notify secretaries
            $secretaries = $this->CI->secretaries_model->get_batch();

            foreach ($secretaries as $secretary)
            {
                if ($secretary['settings']['notifications'] === '0')
                {
                    continue;
                }

                if (!in_array($provider['id'], $secretary['providers']))
                {
                    continue;
                }

                $email->send_delete_appointment($appointment, $provider,
                    $service, $customer, $settings, new Email($secretary['email']),
                    new Text($this->CI->input->post('cancel_reason')));
            }
        }
        catch (Exception $exception)
        {
            $exceptions[] = $exception;
        }
    }
}
