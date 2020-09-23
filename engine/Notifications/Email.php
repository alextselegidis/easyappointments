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

use \EA\Engine\Types\Text;
use \EA\Engine\Types\NonEmptyText;
use \EA\Engine\Types\Url;
use \EA\Engine\Types\Email as EmailAddress;
use \PHPMailer\PHPMailer\PHPMailer;

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
     * @var CI_Controller
     */
    protected $framework;

    /**
     * Contains email configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Class Constructor
     *
     * @param \CI_Controller $framework
     * @param array $config Contains the email configuration to be used.
     */
    public function __construct(\CI_Controller $framework, array $config)
    {
        $this->framework = $framework;
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
     * @param array $company Contains settings of the company. By the time the
     * "company_name", "company_link" and "company_email" values are required in the array.
     * @param \EA\Engine\Types\Text $title The email title may vary depending the receiver.
     * @param \EA\Engine\Types\Text $message The email message may vary depending the receiver.
     * @param \EA\Engine\Types\Url $appointmentLink This link is going to enable the receiver to make changes
     * to the appointment record.
     * @param \EA\Engine\Types\Email $recipientEmail The recipient email address.
     * @param \EA\Engine\Types\Text $icsStream Stream contents of the ICS file.
     */
    public function sendAppointmentDetails(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $company,
        Text $title,
        Text $message,
        Url $appointmentLink,
        EmailAddress $recipientEmail,
        Text $icsStream
    ) {
        $framework = get_instance();

        $timezones = $framework->timezones->to_array();

        switch ($company['date_format'])
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
                throw new \Exception('Invalid date_format value: ' . $company['date_format']);
        }

        switch ($company['time_format'])
        {
            case 'military':
                $timeFormat = 'H:i';
                break;
            case 'regular':
                $timeFormat = 'g:i A';
                break;
            default:
                throw new \Exception('Invalid time_format value: ' . $company['time_format']);
        }

        // Prepare template replace array.
        $email_title = $title->get();
        $email_message = $message->get();
        $appointment_service = $service['name'];
        $appointment_provider = $provider['first_name'] . ' ' . $provider['last_name'];
        $appointment_start_date = date($date_format . ' ' . $timeFormat, strtotime($appointment['start_datetime']));
        $appointment_end_date = date($date_format . ' ' . $timeFormat, strtotime($appointment['end_datetime']));
        $appointment_timezone = $timezones[$provider['timezone']];
        $appointment_link = $appointmentLink->get();
        $company_link = $company['company_link'];
        $company_name = $company['company_name'];
        $customer_name = $customer['first_name'] . ' ' . $customer['last_name'];
        $customer_email = $customer['email'];
        $customer_phone = $customer['phone_number'];
        $customer_address = $customer['address'];

        ob_start();
        require __DIR__ . '/../../application/views/emails/appointment_details.php';
        $html = ob_get_clean();

        $mailer = $this->_createMailer();
        $mailer->From = $company['company_email'];
        $mailer->FromName = $company['company_name'];
        $mailer->AddAddress($recipientEmail->get());
        $mailer->Subject = $title->get();
        $mailer->Body = $html;
        $mailer->addStringAttachment($icsStream->get(), 'invitation.ics');

        if ( ! $mailer->Send())
        {
            throw new \RuntimeException('Email could not been sent. Mailer Error (Line ' . __LINE__ . '): '
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
     * @param array $company Some settings that are required for this function. By now this array must contain
     * the following values: "company_link", "company_name", "company_email".
     * @param \EA\Engine\Types\Email $recipientEmail The email address of the email recipient.
     * @param \EA\Engine\Types\Text $reason The reason why the appointment is deleted.
     */
    public function sendDeleteAppointment(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $company,
        EmailAddress $recipientEmail,
        Text $reason
    ) {
        $framework = get_instance();


        $timezones = $framework->timezones->to_array();

        switch ($company['date_format'])
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
                throw new \Exception('Invalid date_format value: ' . $company['date_format']);
        }

        switch ($company['time_format'])
        {
            case 'military':
                $timeFormat = 'H:i';
                break;
            case 'regular':
                $timeFormat = 'g:i A';
                break;
            default:
                throw new \Exception('Invalid time_format value: ' . $company['time_format']);
        }

        // Prepare email template data.
        $appointment_service = $service['name'];
        $appointment_provider = $provider['first_name'] . ' ' . $provider['last_name'];
        $appointment_date = date($date_format . ' ' . $timeFormat, strtotime($appointment['start_datetime']));
        $appointment_duration = $service['duration'] . ' ' . $this->framework->lang->line('minutes');
        $appointment_timezone = $timezones[$provider['timezone']];
        $company_link = $company['company_link'];
        $company_name = $company['company_name'];
        $customer_name = $customer['first_name'] . ' ' . $customer['last_name'];
        $customer_email = $customer['email'];
        $customer_phone = $customer['phone_number'];
        $customer_address = $customer['address'];
        $reason = $reason->get();

        ob_start();
        require __DIR__ . '/../../application/views/emails/delete_appointment.php';
        $html = ob_get_clean();

        $mailer = $this->_createMailer();

        // Send email to recipient.
        $mailer->From = $company['company_email'];
        $mailer->FromName = $company['company_name'];
        $mailer->AddAddress($recipientEmail->get()); // "Name" argument crushes the phpmailer class.
        $mailer->Subject = $this->framework->lang->line('appointment_cancelled_title');
        $mailer->Body = $html;

        if ( ! $mailer->Send())
        {
            throw new \RuntimeException('Email could not been sent. Mailer Error (Line ' . __LINE__ . '): '
                . $mailer->ErrorInfo);
        }
    }

    /**
     * This method sends an email with the new password of a user.
     *
     * @param \EA\Engine\Types\NonEmptyText $password Contains the new password.
     * @param \EA\Engine\Types\Email $recipientEmail The receiver's email address.
     * @param array $company The company settings to be included in the email.
     */
    public function sendPassword(NonEmptyText $password, EmailAddress $recipientEmail, array $company)
    {
        $email_title = $this->framework->lang->line('new_account_password');
        $password = '<strong>' . $password->get() . '</strong>';
        $email_message = str_replace('$password', $password, $this->framework->lang->line('new_password_is'));
        $company_name = $company['company_name'];
        $company_email = $company['company_email'];
        $company_link = $company['company_link'];

        ob_start();
        require __DIR__ . '/../../application/views/emails/new_password.php';
        $html = ob_get_clean();

        $mailer = $this->_createMailer();

        $mailer->From = $company['company_email'];
        $mailer->FromName = $company['company_name'];
        $mailer->AddAddress($recipientEmail->get()); // "Name" argument crushes the phpmailer class.
        $mailer->Subject = $this->framework->lang->line('new_account_password');
        $mailer->Body = $html;

        if ( ! $mailer->Send())
        {
            throw new \RuntimeException('Email could not been sent. Mailer Error (Line ' . __LINE__ . '): '
                . $mailer->ErrorInfo);
        }
    }

    /**
     * Create PHP Mailer Instance
     *
     * @return PHPMailer
     */
    protected function _createMailer()
    {
        $mailer = new PHPMailer();

        if ($this->config['protocol'] === 'smtp')
        {
            $mailer->isSMTP();
            $mailer->Host = $this->config['smtp_host'];
            $mailer->SMTPAuth = TRUE;
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
