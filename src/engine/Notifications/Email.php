<?php

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Notifications;

use \EA\Engine\Types\Text;
use \EA\Engine\Types\NonEmptyText;
use \EA\Engine\Types\Url;
use \EA\Engine\Types\Email as EmailAddress;

/**
 * Email Notifications Class
 *
 * This library handles all the notification email deliveries on the system.
 *
 * Important: The email configuration settings are located at: /application/config/email.php
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
     * Replace the email template variables.
     *
     * This method finds and replaces the html variables of an email template. It is used to
     * generate dynamic HTML emails that are send as notifications to the system users.
     *
     * @param array $replaceArray Array that contains the variables to be replaced.
     * @param string $templateHtml The email template HTML.
     *
     * @return string Returns the new email html that contain the variables of the $replaceArray.
     */
    protected function _replaceTemplateVariables(array $replaceArray, $templateHtml)
    {
        foreach ($replaceArray as $name => $value)
        {
            $templateHtml = str_replace($name, $value, $templateHtml);
        }

        return $templateHtml;
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
		
		$longDay = $this->framework->lang->line(strtolower(date('l',strtotime($appointment['start_datetime']))));
		
		$ci =& get_instance();
		$theme_color = $ci->settings_model->get_setting('theme_color');
		switch($theme_color) {
				case 'green':
					$bgcolor='#39C678';
					$borderbottom='#C0F1D6';
					break;
				case 'blue':
					$bgcolor='#124E90';
					$borderbottom='#5884B4';
					break;
				case 'red':
					$bgcolor='#C3262E';
					$borderbottom='#75161B';
					break;
				default:
					$bgcolor='#1E6A40';
					$borderbottom='#123F26';
					break;
			}
		
		$detailsyn = $ci->settings_model->get_setting('show_minimal_details');
		if ($detailsyn == 'no') {
			$summaryline = $service['name'];
			$showhide = '<div style="display:block">';
		} else {
			$summaryline = $provider['first_name'] . ' ' . $provider['last_name'] . ' ' . $this->framework->lang->line('appointment');
			$showhide = '<div style="display:none">';
		}
		
		$address_provider = $provider['address'].', '.$provider['city'].', '.$provider['state'].' '.$provider['zip_code'];
		$address_customer = $customer['address'] . ', ' . $customer['city'] . ' - ' . $customer['zip_code'];
		
        // Prepare template replace array.
        $replaceArray = [
            '$provider_address'	=> str_replace(', ,','',$address_provider), 
			'$background_color' => $bgcolor,
			'$border_bottom' => $borderbottom,
            '$email_title' => $title->get(),
            '$email_message' => $message->get(),
            '$appointment_service' => $service['name'],
            '$appointment_price_currency' => $service['price'] . ' ' . $service['currency'],
            '$appointment_provider' => $provider['first_name'] . ' ' . $provider['last_name'],
            '$appointment_start_date' => $longDay . ', ' . date($date_format . ' ' . $timeFormat, strtotime($appointment['start_datetime'])),
			'$appointment_duration' => $service['duration'] . ' ' . $this->framework->lang->line('minutes'),
			'$appointment_link' => $appointmentLink->get(),
            '$company_link' => $company['company_link'],
            '$company_name' => $company['company_name'],
            '$customer_name' => $customer['first_name'] . ' ' . $customer['last_name'],
            '$customer_email' => $customer['email'],
            '$customer_phone' => $customer['phone_number'],
            '$customer_address' => str_replace(', -','',$address_customer),
            '$appt_notes_field' => $appointment['notes'],
			'$summaryical' => $summaryline,
			'$limitdetails' => $showhide,
			
          // Translations
            'Appointment Details' => $this->framework->lang->line('appointment_details_title'),
            'Appointment' => $this->framework->lang->line('appointment'),
            'Service' => $this->framework->lang->line('service'),
            'Provider' => $this->framework->lang->line('provider'),
            'Duration' => $this->framework->lang->line('duration'),
            'Customer Details' => $this->framework->lang->line('customer_details_title'),
            'Name' => $this->framework->lang->line('name'),
            'Email' => $this->framework->lang->line('email'),
            'Phone' => $this->framework->lang->line('phone'),
			'SMS' => $this->framework->lang->line('sms'),
            'Address' => $this->framework->lang->line('address'),
			'City' => $this->framework->lang->line('city'),
            'Zip' => $this->framework->lang->line('zip_code'),
            'Notes' => $this->framework->lang->line('notes'),
			'Appointment Link' => $this->framework->lang->line('appointment_link_title'),
			'Powered by' => $this->framework->lang->line('powered_by'),
			'Click here to edit, reschedule, or cancel the appointment' => $this->framework->lang->line('edit_reschedule_cancel_appointment')
        ];

        $html = file_get_contents(__DIR__ . '/../../application/views/emails/appointment_details.php');
        $html = $this->_replaceTemplateVariables($replaceArray, $html);

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

		$ci =& get_instance();
		$theme_color = $ci->settings_model->get_setting('theme_color');
		switch($theme_color) {
				case 'green':
					$bgcolor='#39C678';
					$borderbottom='#C0F1D6';
					break;
				case 'blue':
					$bgcolor='#124E90';
					$borderbottom='#5884B4';
					break;
				case 'red':
					$bgcolor='#C3262E';
					$borderbottom='#75161B';
					break;
				default:
					$bgcolor='#1E6A40';
					$borderbottom='#123F26';
					break;
			}

		$detailsyn = $ci->settings_model->get_setting('show_minimal_details');
		if ($detailsyn == 'no') {
			$summaryline = $service['name'];
			$showhide = '<div style="display:block">';
		} else {
			$summaryline = $provider['first_name'] . ' ' . $provider['last_name'] . ' ' . $this->framework->lang->line('appointment');
			$showhide = '<div style="display:none">';
		}			
			
        // Prepare email template data.
		$address_provider = $provider['address'].', '.$provider['city'].', '.$provider['state'].' '.$provider['zip_code'];
		$address_customer = $customer['address'] . ', ' . $customer['city'] . ' - ' . $customer['zip_code'];
		
        $replaceArray = [
			'$background_color' => $bgcolor,
			'$border_bottom' => $borderbottom,

            '$email_title' => $this->framework->lang->line('appointment_cancelled_title'),
            '$email_message' => $this->framework->lang->line('appointment_removed_from_schedule'),
            '$appointment_service' => $service['name'],
            '$appointment_price_currency' => $service['price'] . ' ' . $service['currency'],
            '$appointment_provider' => $provider['first_name'] . ' ' . $provider['last_name'],
            '$appointment_date' => date($date_format . ' ' . $timeFormat, strtotime($appointment['start_datetime'])),
            '$appointment_start_date' => date($date_format . ' ' . $timeFormat, strtotime($appointment['start_datetime'])),
            '$appointment_duration' => $service['duration'] . ' ' . $this->framework->lang->line('minutes'),
            '$company_link' => $company['company_link'],
            '$company_name' => $company['company_name'],
            '$customer_name' => $customer['first_name'] . ' ' . $customer['last_name'],
            '$customer_email' => $customer['email'],
            '$customer_phone' => $customer['phone_number'],
            '$customer_address' => str_replace(', -','',$address_customer),
            '$appt_notes_field' => $appointment['notes'],
            '$reason' => $reason->get(),
			'$limitdetails' => $showhide,
            '$provider_address'	=> str_replace(', ,','',$address_provider), 

            // Translations
            'Appointment Details' => $this->framework->lang->line('appointment_details_title'),
			'Appointment' => $this->framework->lang->line('appointment'),
            'Service' => $this->framework->lang->line('service'),
            'Price' => $this->framework->lang->line('price'),
            'Provider' => $this->framework->lang->line('provider'),
            'Duration' => $this->framework->lang->line('duration'),
            'Customer Details' => $this->framework->lang->line('customer_details_title'),
            'Name' => $this->framework->lang->line('name'),
            'Email' => $this->framework->lang->line('email'),
            'Phone' => $this->framework->lang->line('phone'),
            'SMS' => $this->framework->lang->line('sms'),
            'Address' => $this->framework->lang->line('address'),
            'City' => $this->framework->lang->line('city'),
            'Zip' => $this->framework->lang->line('zip_code'),
            'Notes' => $this->framework->lang->line('notes'),
            'Reason' => $this->framework->lang->line('reason'),
			'Powered by' => $this->framework->lang->line('powered_by'),
			'Click here to edit, reschedule, or cancel the appointment' => $this->framework->lang->line('edit_reschedule_cancel_appointment')
        ];

        $html = file_get_contents(__DIR__ . '/../../application/views/emails/delete_appointment.php');
        $html = $this->_replaceTemplateVariables($replaceArray, $html);

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
		$ci =& get_instance();
		$theme_color = $ci->settings_model->get_setting('theme_color');
		switch($theme_color) {
				case 'green':
					$bgcolor='#39C678';
					$borderbottom='#C0F1D6';
					break;
				case 'blue':
					$bgcolor='#124E90';
					$borderbottom='#5884B4';
					break;
				case 'red':
					$bgcolor='#C3262E';
					$borderbottom='#75161B';
					break;
				default:
					$bgcolor='#1E6A40';
					$borderbottom='#123F26';
					break;
			}

		$replaceArray = [
			'$background_color' => $bgcolor,
			'$border_bottom' => $borderbottom,
            '$email_title' => $this->framework->lang->line('new_account_password'),
            '$email_message' => $this->framework->lang->line('new_password_is'),
            '$company_name' => $company['company_name'],
            '$company_email' => $company['company_email'],
            '$company_link' => $company['company_link'],
            '$password' => '<strong>' . $password->get() . '</strong>',

            // Translations
            'Appointment Details' => $this->framework->lang->line('appointment_details_title'),
			'Powered by' => $this->framework->lang->line('powered_by')		
        ];
		
        $html = file_get_contents(__DIR__ . '/../../application/views/emails/new_password.php');
        $html = $this->_replaceTemplateVariables($replaceArray, $html);
		
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
    
    public function sendTxtMail($pemail, $company_name, $sms_field, $sms_subject, $str){
        $mailer = $this->_createMailer();
        $mailer->From = $pemail;
        $mailer->FromName = $company_name;
        $mailer->AddAddress($sms_field); 
        $mailer->Subject = $sms_subject;
        $mailer->IsHTML(false);
        $mailer->Body = $str;
        if (!$mailer->Send()) {
            throw new \RuntimeException('Email could not be sent. Mailer Error (Line ' . __LINE__ . '): ' 
                . $mailer->ErrorInfo);
        }
	}
	
    public function sendHtmlMail($pemail, $company_name, $email_field, $subject, $msg){
        $mailer = $this->_createMailer();
        $mailer->From = $pemail;
        $mailer->FromName = $company_name;
        $mailer->AddAddress($email_field); 
        $mailer->Subject = $subject;
		$mailer->IsHTML(true);
        $mailer->Body = $msg;
        if (!$mailer->Send()) {
            throw new \RuntimeException('Email could not be sent. Mailer Error (Line ' . __LINE__ . '): ' 
                . $mailer->ErrorInfo);
        }
	}
	
    /**
     * Create PHP Mailer Instance
     *
     * @return \PHPMailer
     */
    protected function _createMailer()
    {
        $mailer = new \PHPMailer;

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
