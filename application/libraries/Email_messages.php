<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Email messages library.
 *
 * Handles the email messaging related functionality.
 *
 * @package Libraries
 */
class Email_messages
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * Email_messages constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

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
     * @param string $appointment_link Appointment unique URL.
     * @param string $recipient_email Recipient email address.
     * @param string $ics_stream ICS file contents.
     * @param string|null $timezone Custom timezone.
     *
     * @throws DateInvalidTimeZoneException
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function send_appointment_saved(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings,
        string $subject,
        string $message,
        string $appointment_link,
        string $recipient_email,
        string $ics_stream,
        ?string $timezone = null,
    ): void {
        $appointment_timezone = new DateTimeZone($provider['timezone']);

        $appointment_start = new DateTime($appointment['start_datetime'], $appointment_timezone);

        $appointment_end = new DateTime($appointment['end_datetime'], $appointment_timezone);

        if ($timezone && $timezone !== $provider['timezone']) {
            $custom_timezone = new DateTimeZone($timezone);

            $appointment_start->setTimezone($custom_timezone);
            $appointment['start_datetime'] = $appointment_start->format('Y-m-d H:i:s');

            $appointment_end->setTimezone($custom_timezone);
            $appointment['end_datetime'] = $appointment_end->format('Y-m-d H:i:s');
        }

        $html = $this->CI->load->view(
            'emails/appointment_saved_email',
            [
                'subject' => $subject,
                'message' => $message,
                'appointment' => $appointment,
                'service' => $service,
                'provider' => $provider,
                'customer' => $customer,
                'settings' => $settings,
                'timezone' => $timezone,
                'appointment_link' => $appointment_link,
            ],
            true,
        );

        $php_mailer = $this->get_php_mailer($recipient_email, $subject, $html);

        $php_mailer->addStringAttachment($ics_stream, 'invitation.ics', PHPMailer::ENCODING_BASE64, 'text/calendar');

        $php_mailer->send();
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
     * @throws DateInvalidTimeZoneException
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function send_appointment_deleted(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings,
        string $recipient_email,
        ?string $reason = null,
        ?string $timezone = null,
    ): void {
        $appointment_timezone = new DateTimeZone($provider['timezone']);

        $appointment_start = new DateTime($appointment['start_datetime'], $appointment_timezone);

        $appointment_end = new DateTime($appointment['end_datetime'], $appointment_timezone);

        if ($timezone && $timezone !== $provider['timezone']) {
            $custom_timezone = new DateTimeZone($timezone);

            $appointment_start->setTimezone($custom_timezone);
            $appointment['start_datetime'] = $appointment_start->format('Y-m-d H:i:s');

            $appointment_end->setTimezone($custom_timezone);
            $appointment['end_datetime'] = $appointment_end->format('Y-m-d H:i:s');
        }

        $html = $this->CI->load->view(
            'emails/appointment_deleted_email',
            [
                'appointment' => $appointment,
                'service' => $service,
                'provider' => $provider,
                'customer' => $customer,
                'settings' => $settings,
                'timezone' => $timezone,
                'reason' => $reason,
            ],
            true,
        );

        $subject = lang('appointment_cancelled_title');

        $php_mailer = $this->get_php_mailer($recipient_email, $subject, $html);

        $php_mailer->send();
    }

    /**
     * Send the account recovery details.
     *
     * @param string $password New password.
     * @param string $recipient_email Recipient email address.
     * @param array $settings App settings.
     *
     * @throws Exception
     */
    public function send_password(string $password, string $recipient_email, array $settings): void
    {
        $html = $this->CI->load->view(
            'emails/account_recovery_email',
            [
                'subject' => lang('new_account_password'),
                'message' => str_replace('$password', '<strong>' . $password . '</strong>', lang('new_password_is')),
                'settings' => $settings,
            ],
            true,
        );

        $subject = lang('new_account_password');

        $php_mailer = $this->get_php_mailer($recipient_email, $subject, $html);

        $php_mailer->send();
    }

    /**
     * Create PHP Mailer instance based on the email configuration.
     *
     * @param string|null $recipient_email
     * @param string|null $subject
     * @param string|null $html
     *
     * @return PHPMailer
     *
     * @throws Exception
     */
    private function get_php_mailer(
        ?string $recipient_email = null,
        ?string $subject = null,
        ?string $html = null,
    ): PHPMailer {
        $php_mailer = new PHPMailer(true);

        $php_mailer->isHTML();
        $php_mailer->CharSet = 'UTF-8';
        $php_mailer->SMTPDebug = config('smtp_debug') ? SMTP::DEBUG_SERVER : null;

        if (config('protocol') === 'smtp') {
            $php_mailer->isSMTP();
            $php_mailer->Host = config('smtp_host');
            $php_mailer->SMTPAuth = config('smtp_auth');
            $php_mailer->Username = config('smtp_user');
            $php_mailer->Password = config('smtp_pass');
            $php_mailer->SMTPSecure = config('smtp_crypto');
            $php_mailer->Port = config('smtp_port');
        }

        $from_name = config('from_name') ?: setting('company_name');
        $from_address = config('from_address') ?: setting('company_email');
        $reply_to_address = config('reply_to') ?: setting('company_email');

        $php_mailer->setFrom($from_address, $from_name);
        $php_mailer->addReplyTo($reply_to_address);

        if ($recipient_email) {
            $php_mailer->addAddress($recipient_email);
        }

        if ($subject) {
            $php_mailer->Subject = $subject;
        }

        if ($html) {
            $php_mailer->Body = $html;
            $php_mailer->AltBody = $html;
        }

        return $php_mailer;
    }
}
