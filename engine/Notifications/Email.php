<?php

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Notifications;

use DateTime;
use DateTimeZone;
use EA\Engine\Types\Email as EmailAddress;
use EA\Engine\Types\NonEmptyText;
use EA\Engine\Types\Text;
use EA\Engine\Types\Url;
use EA_Controller;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

/**
 * Email Notifications Class
 *
 * This library handles all the notification email deliveries on the system.
 *
 * Important: The email configuration settings are located at: /application/config/email.php
 *
 * @deprecated
 */
class Email {
    /**
     * Framework Instance
     *
     * @var EA_Controller
     */
    protected $CI;

    /**
     * Contains email configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Class Constructor
     *
     * @param \CI_Controller $CI
     * @param array $config Contains the email configuration to be used.
     */
    public function __construct(\CI_Controller $CI, array $config)
    {
        $this->CI = $CI;
        $this->config = $config;
    }

    /**
     * Send an email with the appointment details.
     *
     * This email template also needs an email title and an email text in order to complete
     * the appointment details.
     *
     * @param array $appointment Contains the appointment data.
     * @param array $provider Contains the provider data.
     * @param array $service Contains the service data.
     * @param array $customer Contains the customer data.
     * @param array $settings Contains settings of the company. At the time the "company_name", "company_link" and
     * "company_email" values are required in the array.
     * @param \EA\Engine\Types\Text $title The email title may vary depending the receiver.
     * @param \EA\Engine\Types\Text $message The email message may vary depending the receiver.
     * @param \EA\Engine\Types\Url $appointment_link_address This link is going to enable the receiver to make changes
     * to the appointment record.
     * @param \EA\Engine\Types\Email $recipient_email The recipient email address.
     * @param \EA\Engine\Types\Text $ics_stream Stream contents of the ICS file.
     * @param string|null $timezone Custom timezone for the notification.
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send_appointment_details(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings,
        Text $title,
        Text $message,
        Url $appointment_link_address,
        EmailAddress $recipient_email,
        Text $ics_stream,
        $timezone = NULL
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
        $appointment_end = new DateTime($appointment['end_datetime'], $appointment_timezone);

        if ($timezone && $timezone !== $provider['timezone'])
        {
            $appointment_timezone = new DateTimeZone($timezone);
            $appointment_start->setTimezone($appointment_timezone);
            $appointment_end->setTimezone($appointment_timezone);
        }

        $html = $this->CI->load->view('emails/appointment_details', [
            'email_title' => $title->get(),
            'email_message' => $message->get(),
            'appointment_service' => $service['name'],
            'appointment_provider' => $provider['first_name'] . ' ' . $provider['last_name'],
            'appointment_start_date' => $appointment_start->format($date_format . ' ' . $time_format),
            'appointment_end_date' => $appointment_end->format($date_format . ' ' . $time_format),
            'appointment_timezone' => $timezones[empty($timezone) ? $provider['timezone'] : $timezone],
            'appointment_link' => $appointment_link_address->get(),
            'company_link' => $settings['company_link'],
            'company_name' => $settings['company_name'],
            'customer_name' => $customer['first_name'] . ' ' . $customer['last_name'],
            'customer_email' => $customer['email'],
            'customer_phone' => $customer['phone_number'],
            'customer_address' => $customer['address'],
        ], TRUE);

        $mailer = $this->create_mailer();
        $mailer->From = $settings['company_email'];
        $mailer->FromName = $settings['company_name'];
        $mailer->AddAddress($recipient_email->get());
        $mailer->Subject = $title->get();
        $mailer->Body = $html;
        $mailer->addStringAttachment($ics_stream->get(), 'invitation.ics');

        if ( ! $mailer->Send())
        {
            throw new RuntimeException('Email could not been sent. Mailer Error (Line ' . __LINE__ . '): '
                . $mailer->ErrorInfo);
        }
    }

    /**
     * Send an email notification to both provider and customer on appointment removal.
     *
     * Whenever an appointment is cancelled or removed, both the provider and customer
     * need to be informed. This method sends the same email twice.
     *
     * <strong>IMPORTANT!</strong> This method's arguments should be taken
     * from database before the appointment record is deleted.
     *
     * @param array $appointment The record data of the removed appointment.
     * @param array $provider The record data of the appointment provider.
     * @param array $service The record data of the appointment service.
     * @param array $customer The record data of the appointment customer.
     * @param array $settings Some settings that are required for this function. As of now this array must contain
     * the following values: "company_link", "company_name", "company_email".
     * @param \EA\Engine\Types\Email $recipient_email The email address of the email recipient.
     * @param \EA\Engine\Types\Text $reason The reason why the appointment is deleted.
     * @param string|null $timezone Custom timezone.
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send_delete_appointment(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings,
        EmailAddress $recipient_email,
        Text $reason,
        $timezone = NULL
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

        $html = $this->CI->load->view('emails/delete_appointment', [
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
            'reason' => $reason->get(),
        ], TRUE);

        $mailer = $this->create_mailer();

        // Send email to recipient.
        $mailer->From = $settings['company_email'];
        $mailer->FromName = $settings['company_name'];
        $mailer->AddAddress($recipient_email->get()); // "Name" argument crushes the phpmailer class.
        $mailer->Subject = lang('appointment_cancelled_title');
        $mailer->Body = $html;

        if ( ! $mailer->Send())
        {
            throw new RuntimeException('Email could not been sent. Mailer Error (Line ' . __LINE__ . '): '
                . $mailer->ErrorInfo);
        }
    }

    /**
     * This method sends an email with the new password of a user.
     *
     * @param \EA\Engine\Types\NonEmptyText $password Contains the new password.
     * @param \EA\Engine\Types\Email $recipientEmail The receiver's email address.
     * @param array $settings The company settings to be included in the email.
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send_password(NonEmptyText $password, EmailAddress $recipientEmail, array $settings)
    {
        $html = $this->CI->load->view('emails/new_password', [
            'email_title' => lang('new_account_password'),
            'email_message' => str_replace('$password', '<strong>' . $password->get() . '</strong>', lang('new_password_is')),
            'company_name' => $settings['company_name'],
            'company_email' => $settings['company_email'],
            'company_link' => $settings['company_link'],
        ], TRUE);

        $mailer = $this->create_mailer();

        $mailer->From = $settings['company_email'];
        $mailer->FromName = $settings['company_name'];
        $mailer->AddAddress($recipientEmail->get()); // "Name" argument crushes the phpmailer class.
        $mailer->Subject = lang('new_account_password');
        $mailer->Body = $html;

        if ( ! $mailer->Send())
        {
            throw new RuntimeException('Email could not been sent. Mailer Error (Line ' . __LINE__ . '): '
                . $mailer->ErrorInfo);
        }
    }

    /**
     * Create PHP Mailer Instance
     *
     * @return PHPMailer
     */
    protected function create_mailer()
    {
        $mailer = new PHPMailer();

        if ($this->config['protocol'] === 'smtp')
        {
            $mailer->isSMTP();
            $mailer->SMTPDebug  = $this->config['smtp_debug'];
            $mailer->Host = $this->config['smtp_host'];
            $mailer->SMTPAuth = $this->config['smtp_auth'];
            $mailer->Username = $this->config['smtp_user'];
            $mailer->Password = $this->config['smtp_pass'];
            $mailer->SMTPSecure = $this->config['smtp_crypto'];
            $mailer->Port = $this->config['smtp_port'];
        }

        $mailer->IsHTML($this->config['mailtype'] === 'html');
        $mailer->CharSet = $this->config['charset'];

        return $mailer;
    }
}
