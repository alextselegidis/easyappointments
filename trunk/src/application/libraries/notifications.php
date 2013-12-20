<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');  

require_once dirname(__FILE__) . '/external/class.phpmailer.php';

/**
 * This library handles all the notification email deliveries 
 * on the system.
 * 
 * Custom system settings for the notification section are loaded 
 * during the execution of each class methods.
 */
class Notifications {
    private $ci;
    
    /**
     * Class Constructor
     */
    public function __construct() {   
        $this->ci =& get_instance();
    }
    
    /**
     * Replace the email template variables.
     * 
     * This method finds and replaces the html variables of an email
     * template. It is used to generate dynamic HTML emails that are
     * send as notifications to the system users.
     * 
     * @param array $replace_array Array that contains the variables
     * to be replaced.
     * @param string $email_html The email template hmtl.
     * @return string Returns the new email html that contain the 
     * variables of the $replace_array.
     */
    private function replace_template_variables($replace_array, $email_html) {
        foreach($replace_array as $var=>$value) {
            $email_html = str_replace($var, $value, $email_html);
        }
        
        return $email_html;
    }
    
    /**
     * Send an email with the appointment details. 
     * 
     * This email template also needs an email title and an email text in order to complete
     * the appointment details.
     * 
     * @expectedException Exception Raises when an unexpected error occures.
     * 
     * @param array $appointment_data Contains the appointment data.
     * @param array $provider_data Contains the provider data.
     * @param array $service_data Contains the service data.    
     * @param array $company_settings Contains settings of the company. By the time the 
     * "company_name", "company_link" and "company_email" values are required in the array.
     * @param string $title The email title may vary depending the receiver.
     * @param string $message The email message may vary depending the receiver.
     * @param string $appointment_link This link is going to enable the receiver to make changes
     * to the appointment record.
     * @param string $receiver_address The receiver email address.
     * @return bool Returns the operation result.
     */
    public function send_appointment_details($appointment_data, $provider_data, $service_data, 
            $customer_data, $company_settings, $title, $message, $appointment_link, 
            $receiver_address) {
        
        // :: PREPARE THE EMAIL TEMPLATE REPLACE ARRAY
        $replace_array = array(
            '$email_title'              => $title,
            '$email_message'            => $message,
            
            '$appointment_service'      => $service_data['name'],
            '$appointment_provider'     => $provider_data['first_name'] . ' ' . $provider_data['last_name'],
            '$appointment_start_date'   => date('d/m/Y H:i', strtotime($appointment_data['start_datetime'])),
            '$appointment_end_date'     => date('d/m/Y H:i', strtotime($appointment_data['end_datetime'])),
            '$appointment_link'         => $appointment_link, 
            
            '$company_link'             => $company_settings['company_link'],
            '$company_name'             => $company_settings['company_name'],
            
            '$customer_name'            => $customer_data['first_name'] . ' ' . $customer_data['last_name'],
            '$customer_email'           => $customer_data['email'],
            '$customer_phone'           => $customer_data['phone_number'],
            '$customer_address'         => $customer_data['address'],
            
            // Translations
            'Appointment Details' => $this->ci->lang->line('appointment_details_title'),
            'Service' => $this->ci->lang->line('service'),
            'Provider' => $this->ci->lang->line('provider'),
            'Start' => $this->ci->lang->line('start'),
            'End' => $this->ci->lang->line('end'),
            'Customer Details' => $this->ci->lang->line('customer_details_title'),
            'Name' => $this->ci->lang->line('name'),
            'Email' => $this->ci->lang->line('email'),
            'Phone' => $this->ci->lang->line('phone'),
            'Address' => $this->ci->lang->line('address'),
            'Appointment Link' => $this->ci->lang->line('appointment_link_title')
        );
        
        $email_html = file_get_contents(dirname(dirname(__FILE__)) 
                . '/views/emails/appointment_details.php');
        $email_html = $this->replace_template_variables($replace_array, $email_html);
        
        // :: INSTANTIATE EMAIL OBJECT AND SEND EMAIL
        $mail = new PHPMailer();
        $mail->From = $company_settings['company_email'];
        $mail->FromName = $company_settings['company_name'];
        $mail->AddAddress($receiver_address); // "Name" argument crushes the phpmailer class.
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $title;
        $mail->Body    = $email_html;

        if (!$mail->Send()) {
            throw new Exception('Email could not been sent. Mailer Error (Line ' 
                    . __LINE__ . '): ' . $mail->ErrorInfo);
        }
        
        return TRUE;
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
     * @param array $appointment_data The record data of the removed appointment.
     * @param array $provider_data The record data of the appointment provider.
     * @param array $service_data The record data of the appointment service.
     * @param array $customer_data The record data of the appointment customer.
     * @param array $company_settings Some settings that are required for this function. 
     * By now this array must contain the following values: "company_link", 
     * "company_name", "company_email". 
     * @param string $to_address The email address of the email receiver.
     * @param string $reason The reason why the appointment is deleted.
     */
    public function send_delete_appointment($appointment_data, $provider_data, 
            $service_data, $customer_data, $company_settings, $to_address, $reason) {
      	// :: PREPARE EMAIL REPLACE ARRAY
        $replace_array = array(
            '$email_title'          => $this->ci->lang->line('appointment_cancelled_title'),
            '$email_message'        => $this->ci->lang->line('appointment_removed_from_schedule'),
            '$appointment_service'  => $service_data['name'],
            '$appointment_provider' => $provider_data['first_name'] . ' ' . $provider_data['last_name'],
            '$appointment_date'     => date('d/m/Y H:i', strtotime($appointment_data['start_datetime'])),
            '$appointment_duration' => $service_data['duration'] . ' minutes',
            '$company_link'         => $company_settings['company_link'],
            '$company_name'         => $company_settings['company_name'],
            '$customer_name'        => $customer_data['first_name'] . ' ' . $customer_data['last_name'],
            '$customer_email'       => $customer_data['email'],
            '$customer_phone'       => $customer_data['phone_number'],
            '$customer_address'     => $customer_data['address'],
            '$reason'               => $reason,
            
            // Translations
            'Appointment Details' => $this->ci->lang->line('appointment_details_title'),
            'Service' => $this->ci->lang->line('service'),
            'Provider' => $this->ci->lang->line('provider'),
            'Date' => $this->ci->lang->line('start'),
            'Duration' => $this->ci->lang->line('duration'),
            'Customer Details' => $this->ci->lang->line('customer_details_title'),
            'Name' => $this->ci->lang->line('name'),
            'Email' => $this->ci->lang->line('email'),
            'Phone' => $this->ci->lang->line('phone'),
            'Address' => $this->ci->lang->line('address'),
            'Reason' => $this->ci->lang->line('reason')
        );
        
        $email_html = file_get_contents(dirname(dirname(__FILE__)) 
                    . '/views/emails/delete_appointment.php');
        $email_html = $this->replace_template_variables($replace_array, $email_html);
        
        // :: SETUP EMAIL OBJECT AND SEND NOTIFICATION
        $mail = new PHPMailer();
        $mail->From         = $company_settings['company_email'];
        $mail->FromName     = $company_settings['company_name'];
        $mail->AddAddress($to_address); // "Name" argument crushes the phpmailer class.
        $mail->IsHTML(true);
        $mail->CharSet      = 'UTF-8';
        $mail->Subject      = $this->ci->lang->line('appointment_cancelled_title');
        $mail->Body         = $email_html;

        if (!$mail->Send()) {
            throw new Exception('Email could not been sent. ' 
                    . 'Mailer Error (Line ' . __LINE__ . '): ' . $mail->ErrorInfo);
        }
        
        return TRUE;
    }
    
    /**
     * This method sends an email with the new password of a user. 
     * 
     * @param string $password Contains the new password.
     * @param string $email The receiver's email address.
     */
    public function send_password($password, $email, $company_settings) {
        $replace_array = array(
            '$email_title' => $this->ci->lang->line('new_account_password'),
            '$email_message' => $this->ci->lang->line('new_password_is'),
            '$company_name' => $company_settings['company_name'],
            '$company_email' => $company_settings['company_email'],
            '$company_link' => $company_settings['company_link'],
            '$password' => '<strong>' . $password . '</strong>'
        );
        
        $email_html = file_get_contents(dirname(dirname(__FILE__)) 
                    . '/views/emails/new_password.php');
        $email_html = $this->replace_template_variables($replace_array, $email_html);
        
        // :: SETUP EMAIL OBJECT AND SEND NOTIFICATION
        $mail = new PHPMailer();
        $mail->From = $company_settings['company_email'];
        $mail->FromName = $company_settings['company_name'];
        $mail->AddAddress($email); // "Name" argument crushes the phpmailer class.
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $this->ci->lang->line('new_account_password');
        $mail->Body = $email_html;

        if (!$mail->Send()) {
            throw new Exception('Email could not been sent. ' 
                    . 'Mailer Error (Line ' . __LINE__ . '): ' . $mail->ErrorInfo);
        }
        
        return TRUE;
    }
    
    /**
     * Sends a simple email to notify for a new installation. 
     * 
     * This method will be only used for tracking the number of installations. No personal
     * data will be retrieved for any other cause.
     * 
     * @returns bool Returns the "send()" method result.
     */
    public function send_new_installation($company_name, $company_email, $company_link) {
        $mail = new PHPMailer();
        $mail->From = $company_email;
        $mail->FromName = 'New Installation: ' . $company_name ;
        $mail->AddAddress('info@easyappointments.org');
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'New Easy!Appointments Installation';
        $mail->Body = 'Base URL: ' . $this->ci->config->item('base_url') . '<br>'
                . 'E!A Version: ' . $this->ci->config->item('ea_version') . '<br>'
                . 'Company Name: ' . $company_name . '<br>'
                . 'Company Email: ' . $company_email . '<br>'
                . 'Company Link: ' . $company_link . '<br>';
        return $mail->Send();
    }
}

/* End of file notifications.php */
/* Location: ./application/libraries/notifications.php */