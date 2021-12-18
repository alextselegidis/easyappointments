<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Email messages library.
 *
 * Handles the email messaging related functionality.
 *
 * @package Libraries
 */
class Email_messages {
    /**
     * @var EA_Controller
     */
    protected $CI;

    /**
     * Email_messages constructor.
     */
    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('admins_model');
        $this->CI->load->model('appointments_model');
        $this->CI->load->model('providers_model');
        $this->CI->load->model('secretaries_model');
        $this->CI->load->model('secretaries_model');
        $this->CI->load->model('settings_model');

        $this->CI->load->library('email');
        $this->CI->load->library('ics_file');
        $this->CI->load->library('timezones');
    }

    /**
     * Send an email with the appointment details.
     *
     * @param array $appointment Appointment data.
     * @param array $provider Provider data.
     * @param array $service Service data.
     * @param array $customer Customer data.
     * @param array $settings App settings.
     * @param string $subject Email subject.
     * @param string $message Email message.
     * @param string $appointment_link_address Appointment unique URL.
     * @param string $recipient_email Recipient email address.
     * @param string $ics_stream ICS file contents.
     * @param string|null $timezone Custom timezone.
     *
     * @throws Exception
     */
    public function send_appointment_details(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings,
        string $subject,
        string $message,
        string $appointment_link_address,
        string $recipient_email,
        string $ics_stream,
        string $timezone = NULL
    )
    {
        $timezones = $this->CI->timezones->to_array();

        switch ($settings['date_format'])
        {
            case 'DMY':
                $date_format = 'd/m/Y';
                break;
            case 'MDY':
                $date_format = 'm/d/Y';
                break;
            case 'YMD':
                $date_format = 'Y/m/d';
                break;
            default:
                throw new RuntimeException('Invalid date_format value: ' . $settings['date_format']);
        }

        switch ($settings['time_format'])
        {
            case 'military':
                $time_format = 'H:i';
                break;
            case 'regular':
                $time_format = 'g:i a';
                break;
            default:
                throw new RuntimeException('Invalid time_format value: ' . $settings['time_format']);
        }

        $appointment_timezone = new DateTimeZone($provider['timezone']);

        $appointment_start = new DateTime($appointment['start_datetime'], $appointment_timezone);

        $appointment_end = new DateTime($appointment['end_datetime'], $appointment_timezone);

        if ($timezone && $timezone !== $provider['timezone'])
        {
            $appointment_timezone = new DateTimeZone($timezone);

            $appointment_start->setTimezone($appointment_timezone);

            $appointment_end->setTimezone($appointment_timezone);
        }

        $html = $this->CI->load->view('emails/appointment_saved_email', [
            'email_title' => $subject,
            'email_message' => $message,
            'appointment_service' => $service['name'],
            'appointment_provider' => $provider['first_name'] . ' ' . $provider['last_name'],
            'appointment_start_date' => $appointment_start->format($date_format . ' ' . $time_format),
            'appointment_end_date' => $appointment_end->format($date_format . ' ' . $time_format),
            'appointment_timezone' => $timezones[empty($timezone) ? $provider['timezone'] : $timezone],
            'appointment_link' => $appointment_link_address,
            'company_link' => $settings['company_link'],
            'company_name' => $settings['company_name'],
            'customer_name' => $customer['first_name'] . ' ' . $customer['last_name'],
            'customer_email' => $customer['email'],
            'customer_phone' => $customer['phone_number'],
            'customer_address' => $customer['address'],
        ], TRUE);

        $this->CI->email->from($settings['company_email'], $settings['company_email']);

        $this->CI->email->to($recipient_email);

        $this->CI->email->subject($subject);

        $this->CI->email->message($html);

        $this->CI->email->attach($ics_stream, 'attachment', 'invitation.ics');

        if ( ! $this->CI->email->send(FALSE))
        {
            throw new RuntimeException('Email was not sent: ' . $this->CI->email->print_debugger());
        }
    }

    /**
     * Send an email with the appointment removal details.
     *
     * @param array $appointment Appointment data.
     * @param array $provider Provider data.
     * @param array $service Service data.
     * @param array $customer Customer data.
     * @param array $settings App settings.
     * @param string $recipient_email Recipient email address.
     * @param string|null $reason Removal reason.
     * @param string|null $timezone Custom timezone.
     *
     * @throws Exception
     */
    public function send_delete_appointment(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings,
        string $recipient_email,
        string $reason = NULL,
        string $timezone = NULL
    )
    {
        $timezones = $this->CI->timezones->to_array();

        switch ($settings['date_format'])
        {
            case 'DMY':
                $date_format = 'd/m/Y';
                break;
            case 'MDY':
                $date_format = 'm/d/Y';
                break;
            case 'YMD':
                $date_format = 'Y/m/d';
                break;
            default:
                throw new Exception('Invalid date_format value: ' . $settings['date_format']);
        }

        switch ($settings['time_format'])
        {
            case 'military':
                $time_format = 'H:i';
                break;
            case 'regular':
                $time_format = 'g:i a';
                break;
            default:
                throw new Exception('Invalid time_format value: ' . $settings['time_format']);
        }

        $appointment_timezone = new DateTimeZone($provider['timezone']);

        $appointment_start = new DateTime($appointment['start_datetime'], $appointment_timezone);

        if ($timezone && $timezone !== $provider['timezone'])
        {
            $appointment_timezone = new DateTimeZone($timezone);

            $appointment_start->setTimezone($appointment_timezone);
        }

        $html = $this->CI->load->view('emails/appointment_deleted_email', [
            'appointment_service' => $service['name'],
            'appointment_provider' => $provider['first_name'] . ' ' . $provider['last_name'],
            'appointment_date' => $appointment_start->format($date_format . ' ' . $time_format),
            'appointment_duration' => $service['duration'] . ' ' . lang('minutes'),
            'appointment_timezone' => $timezones[empty($timezone) ? $provider['timezone'] : $timezone],
            'company_link' => $settings['company_link'],
            'company_name' => $settings['company_name'],
            'customer_name' => $customer['first_name'] . ' ' . $customer['last_name'],
            'customer_email' => $customer['email'],
            'customer_phone' => $customer['phone_number'],
            'customer_address' => $customer['address'],
            'reason' => $reason,
        ], TRUE);

        $this->CI->email->from($settings['company_email'], $settings['company_email']);

        $this->CI->email->to($recipient_email);

        $this->CI->email->subject(lang('appointment_cancelled_title'));

        $this->CI->email->message($html);

        if ( ! $this->CI->email->send(FALSE))
        {
            throw new RuntimeException('Email was not sent: ' . $this->CI->email->print_debugger());
        }
    }

    /**
     * Send the account recovery details.
     *
     * @param string $password New password.
     * @param string $recipient_email Recipient email address.
     * @param array $settings App settings.
     */
    public function send_password(
        string $password,
        string $recipient_email,
        array $settings
    )
    {
        $html = $this->CI->load->view('emails/account_recovery_email', [
            'email_title' => lang('new_account_password'),
            'email_message' => str_replace('$password', '<strong>' . $password . '</strong>', lang('new_password_is')),
            'company_name' => $settings['company_name'],
            'company_email' => $settings['company_email'],
            'company_link' => $settings['company_link'],
        ], TRUE);

        $this->CI->email->from($settings['company_email'], $settings['company_email']);

        $this->CI->email->to($recipient_email);

        $this->CI->email->subject(lang('new_account_password'));

        $this->CI->email->message($html);

        if ( ! $this->CI->email->send(FALSE))
        {
            throw new RuntimeException('Email was not sent: ' . $this->CI->email->print_debugger());
        }
    }
}
