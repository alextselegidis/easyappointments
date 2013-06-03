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
     * @return bool Returns the operation result.
     */
    public function send_book_success($customer_data, $appointment_data) {
        $this->CI->load->model('Providers_Model');
        $this->CI->load->model('Services_Model');
        $this->CI->load->model('Settings_Model');
        
        $provider_data = $this->CI->Providers_Model
                ->get_row($appointment_data['id_users_provider']);
        $service_data = $this->CI->Services_Model
                ->get_row($appointment_data['id_services']);
        
        $replace_array = array(
            '$appointment_service'  => $service_data['name'],
            '$appointment_provider' => $provider_data['first_name'] . ' ' . $provider_data['last_name'],
            '$appointment_date'     => date('d/m/Y H:i', strtotime($appointment_data['start_datetime'])),
            '$appointment_duration' => $service_data['duration'] . ' minutes',
            '$appointment_link'     => $this->CI->config->item('base_url') . $appointment_data['hash'],  
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
        $mail->Subject = 'Appointment Book Success!';
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
     * @return bool Returns the operation result.
     */
    public function send_new_appointment($customer_data, $appointment_data) {
        $this->CI->load->model('Providers_Model');
        $this->CI->load->model('Services_Model');
        $this->CI->load->model('Settings_Model');
        
        $provider_data = $this->CI->Providers_Model->get_row($appointment_data['id_users_provider']);
        $service_data = $this->CI->Services_Model->get_row($appointment_data['id_services']);
                
        $replace_array = array(
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
        $mail->FromName = $this->CI->Settings_Model->get_setting('company_name');;
        $mail->AddAddress($provider_data['email']); // "Name" argument crushes the phpmailer class.
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'New Appointment';
        $mail->Body    = $email_html;

        if (!$mail->Send()) {
            throw new NotificationException('Email could not been sent. ' 
                    . 'Mailer Error (Line ' . __LINE__ . '): ' . $mail->ErrorInfo);
        }
        
        return TRUE;
    }
}


/* End of file notifications.php */
/* Location: ./application/libraries/notifications.php */