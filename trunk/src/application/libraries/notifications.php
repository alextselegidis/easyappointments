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
    private $CI;
    
    /**
     * Class Constructor
     */
    public function __construct() {   
        $this->CI =& get_instance();
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
     * Send a success email to the customer that booked 
     * a new appointment.
     * 
     * @expectedException NotificationException Raises when an unexpected 
     * error has occured when the email is send.
     * 
     * @param array $customer_data Associative array with the customer's
     * data. Each key has the same name as the corresponding field in db.
     * @param array $appointment_data Associative array with the appointment's
     * data. Each key has the same name as the corresponding field in db.
     * @param string $email_title The email title is going to inform the customer
     * for the action that was taken.
     * @return bool Returns the operation result.
     */
    public function send_book_success($customer_data, $appointment_data, $email_title) {
        $this->CI->load->model('Providers_Model');
        $this->CI->load->model('Services_Model');
        $this->CI->load->model('Settings_Model');
        
        $provider_data = $this->CI->Providers_Model
                ->get_row($appointment_data['id_users_provider']);
        $service_data = $this->CI->Services_Model
                ->get_row($appointment_data['id_services']);
        
        $replace_array = array(
            '$email_title'          => $email_title,
            '$appointment_service'  => $service_data['name'],
            '$appointment_provider' => $provider_data['first_name'] . ' ' . $provider_data['last_name'],
            '$appointment_date'     => date('d/m/Y H:i', strtotime($appointment_data['start_datetime'])),
            '$appointment_duration' => $service_data['duration'] . ' minutes',
            '$appointment_link'     => $this->CI->config->item('base_url') . 'appointments/index/' . $appointment_data['hash'],  
            '$company_link'         => $this->CI->Settings_Model->get_setting('company_link'),
            '$company_name'         => $this->CI->Settings_Model->get_setting('company_name'),
            '$customer_name'        => $customer_data['first_name'] . ' ' . $customer_data['last_name']
        );
        
        $email_html = file_get_contents(dirname(dirname(__FILE__)) . '/views/emails/book_success.php');
        $email_html = $this->replace_template_variables($replace_array, $email_html);
        
        $mail = new PHPMailer();
        $mail->From = $this->CI->Settings_Model->get_setting('company_email');
        $mail->FromName = $this->CI->Settings_Model->get_setting('company_name');
        $mail->AddAddress($customer_data['email']); // Do not use the name argument, phpmailer crushes.
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $email_title;
        $mail->Body    = $email_html;

        if(!$mail->Send()) {
           throw new NotificationException('Email could not been sent. ' 
                    . 'Mailer Error (' . __LINE__ . '): ' . $mail->ErrorInfo);
        }
        
        return TRUE;
    }
    
    /**
     * Send an email notification to a provider that 
     * a new appointment has been added to his plan.
     * 
     * @expectedException NotificationException Raises when an unexpected 
     * error has occured when the email is send.
     * 
     * @param array $customer_data Associative array with the customer's
     * data. Each key has the same name as the corresponding field in db.
     * @param array $appointment_data Associative array with the appointment's
     * data. Each key has the same name as the corresponding field in db.
     * @param string $email_title The email title is going to inform the provider
     * for the action that was taken.
     * @return bool Returns the operation result.
     */
    public function send_new_appointment($customer_data, $appointment_data, $email_title) {
        $this->CI->load->model('Providers_Model');
        $this->CI->load->model('Services_Model');
        $this->CI->load->model('Settings_Model');
        
        $provider_data = $this->CI->Providers_Model->get_row($appointment_data['id_users_provider']);
        $service_data = $this->CI->Services_Model->get_row($appointment_data['id_services']);
                
        $replace_array = array(
            '$email_title'          => $email_title,
            '$appointment_service'  => $service_data['name'],
            '$appointment_provider' => $provider_data['first_name'] . ' ' . $provider_data['last_name'],
            '$appointment_date'     => date('d/m/Y H:i', strtotime($appointment_data['start_datetime'])),
            '$appointment_duration' => $service_data['duration'] . ' minutes',
            '$appointment_link'     => $this->CI->config->item('base_url') . 'appointments/admin/' . $appointment_data['hash'],
            '$company_link'         => $this->CI->Settings_Model->get_setting('company_link'),
            '$company_name'         => $this->CI->Settings_Model->get_setting('company_name'),
            '$customer_name'        => $customer_data['first_name'] . ' ' . $customer_data['last_name'],
            '$customer_email'       => $customer_data['email'],
            '$customer_phone'       => $customer_data['phone_number'],
            '$customer_address'     => $customer_data['address']
        );
        
        $email_html = file_get_contents(dirname(dirname(__FILE__)) 
                . '/views/emails/new_appointment.php');
        $email_html = $this->replace_template_variables($replace_array, $email_html);
        
        $mail = new PHPMailer();
        $mail->From = $this->CI->Settings_Model->get_setting('company_email');
        $mail->FromName = $this->CI->Settings_Model->get_setting('company_name');
        $mail->AddAddress($provider_data['email']); // "Name" argument crushes the phpmailer class.
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $email_title;
        $mail->Body    = $email_html;

        if (!$mail->Send()) {
            throw new NotificationException('Email could not been sent. ' 
                    . 'Mailer Error (Line ' . __LINE__ . '): ' . $mail->ErrorInfo);
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
     */
    public function send_remove_appointment($appointment_data, $provider_data, 
            $service_data, $customer_data, $company_settings, $to_address) {
      	// :: PREPARE EMAIL REPLACE ARRAY
        $replace_array = array(
            '$email_title'          => 'Appointment Cancelled',
            '$appointment_service'  => $service_data['name'],
            '$appointment_provider' => $provider_data['first_name'] . ' ' . $provider_data['last_name'],
            '$appointment_date'     => date('d/m/Y H:i', strtotime($appointment_data['start_datetime'])),
            '$appointment_duration' => $service_data['duration'] . ' minutes',
            '$company_link'         => $company_settings['company_link'],
            '$company_name'         => $company_settings['company_name']
        );
        
        $email_html = file_get_contents(dirname(dirname(__FILE__)) 
                . '/views/emails/remove_appointment.php');
        $email_html = $this->replace_template_variables($replace_array, $email_html);
        
        // :: SETUP EMAIL OBJECT AND SEND NOTIFICATION
        $mail = new PHPMailer();
        $mail->From         = $company_settings['company_email'];
        $mail->FromName     = $company_settings['company_name'];
        $mail->AddAddress($to_address); // "Name" argument crushes the phpmailer class.
        $mail->IsHTML(true);
        $mail->CharSet      = 'UTF-8';
        $mail->Subject      = 'Appointment Cancelled';
        $mail->Body         = $email_html;

        if (!$mail->Send()) {
            throw new NotificationException('Email could not been sent. ' 
                    . 'Mailer Error (Line ' . __LINE__ . '): ' . $mail->ErrorInfo);
        }
        
        return TRUE;
    }
}

/* End of file notifications.php */
/* Location: ./application/libraries/notifications.php */