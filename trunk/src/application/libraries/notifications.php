<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');  

/**
 * This library handles all the notification email deliveries 
 * on the system.
 * 
 * Custom system settings for the notification section are loaded 
 * during the execution of the class methods.
 */
class Notifications {
    /**
     * Class Constructor
     */
    public function __construct() {
        // @task Define some vars and constants
    }
    
    /**
     * Send a success email to the customer that booked 
     * a new appointment.
     * 
     * @param array $customer_data Associative array with the customer's
     * data. Each key has the same name as the corresponding field in db.
     * @param array $appointment_data Associative array with the appointment's
     * data. Each key has the same name as the corresponding field in db.
     * @return bool Returns the operation result.
     */
    public function send_book_success($customer_data, $appointment_data) {
        $CI =& get_instance();
        $CI->load->model('Providers_Model');
        $CI->load->model('Services_Model');
        
        $html = '
            <html>
            <head>
                <title>Appointment Book Success!</title>
            </head>
            <body>
                <h2>Your appointment has been successfully booked!</h2>
                <strong>Appointment Details</strong>
                <table>
                    <tr>
                        <td>Service</td>
                        <td>: ' . $CI->Services_Model->get_value('name', $appointment_data['id_services']) . '</td>
                    </tr>
                    <tr>
                        <td>Provider</td>
                        <td>: ' . $CI->Providers_Model->get_value('last_name', $appointment_data['id_users_provider']) . ' ' . $CI->Providers_Model->get_value('first_name', $appointment_data['id_users_provider']) . '</td>
                    </tr>
                    <tr>
                        <td>Start Date</td>
                        <td>: ' . date('d/m/Y H:i', strtotime($appointment_data['start_datetime'])) . '</td>
                    </tr>
                    <tr>
                        <td>Duration</td>
                        <td>: ' . $CI->Services_Model->get_value('duration', $appointment_data['id_services']) . ' minutes</td>
                    </tr>
                </table>
                <br/><br/>
                <strong>Customer Details</strong>
                <table>
                    <tr>
                        <td>Name</td>
                        <td>: ' . $customer_data['last_name'] . ' ' . $customer_data['first_name'] . '</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>: ' . $customer_data['email'] . '</td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td>: ' . $customer_data['phone_number'] . '</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>: ' . $customer_data['address'] . ', ' . $customer_data['city'] . ', ' . $customer_data['zip_code']  . '</td>
                    </tr>
                </table>
            </body>
            </html>';
        
        // Send email to the customer
        $to = $customer_data['email'];
        
        $CI->load->model('Settings_Model');
        $from_email  = $CI->Settings_Model->get_setting('business_email');
        $from_name   = $CI->Settings_Model->get_setting('business_name'); 
        
        $subject = 'Appointment Book Success!';
        
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $customer_data['last_name'] . ' ' . $customer_data['first_name'] . ' <' . $customer_data['email'] . '>' . "\r\n";
        $headers .= 'From: ' . $from_name . ' <' . $from_email . '>' . "\r\n";
        
        return mail($to, $subject, $html, $headers);
    }
    
    /**
     * Send an email notification to a provider that 
     * a new appointment has been added to his plan.
     * 
     * @param array $customer_data Associative array with the customer's
     * data. Each key has the same name as the corresponding field in db.
     * @param array $appointment_data Associative array with the appointment's
     * data. Each key has the same name as the corresponding field in db.
     * @return bool Returns the operation result.
     */
    public function send_new_appointment($customer_data, $appointment_data) {
        $CI =& get_instance();
        $CI->load->model('Providers_Model');
        $CI->load->model('Services_Model');
        
        $html = '
            <html>
            <head>
                <title>A new appointment has been added to your plan.</title>
            </head>
            <body>
                <h2>A new appointment has been added to your plan.</h2>
                <strong>Appointment Details</strong>
                <table>
                    <tr>
                        <td>Service</td>
                        <td>: ' . $CI->Services_Model->get_value('name', $appointment_data['id_services']) . '</td>
                    </tr>
                    <tr>
                        <td>Provider</td>
                        <td>: ' . $CI->Providers_Model->get_value('last_name', $appointment_data['id_users_provider']) . ' ' . $CI->Providers_Model->get_value('first_name', $appointment_data['id_users_provider']) . '</td>
                    </tr>
                    <tr>
                        <td>Start Date</td>
                        <td>: ' . date('d/m/Y H:i', strtotime($appointment_data['start_datetime'])) . '</td>
                    </tr>
                    <tr>
                        <td>Duration</td>
                        <td>: ' . $CI->Services_Model->get_value('duration', $appointment_data['id_services']) . ' minutes</td>
                    </tr>
                </table>
                <br/><br/>
                <strong>Customer Details</strong>
                <table>
                    <tr>
                        <td>Name</td>
                        <td>: ' . $customer_data['last_name'] . ' ' . $customer_data['first_name'] . '</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>: ' . $customer_data['email'] . '</td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td>: ' . $customer_data['phone_number'] . '</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>: ' . $customer_data['address'] . ', ' . $customer_data['city'] . ', ' . $customer_data['zip_code']  . '</td>
                    </tr>
                </table>
            </body>
            </html>';
        
        // Send email to the customer
        $to = $CI->Providers_Model->get_value('email', $appointment_data['id_users_provider']);
        $providerNicename = $CI->Providers_Model->get_value('last_name', $appointment_data['id_users_provider']) . ' ' . $CI->Providers_Model->get_value('first_name', $appointment_data['id_users_provider']);
        
        $CI->load->model('Settings_Model');
        $fromEmail  = $CI->Settings_Model->get_setting('business_email');
        $fromName   = $CI->Settings_Model->get_setting('business_name'); 
        
        $subject = 'A new appointment has been added to your plan.';
        
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $providerNicename . '<' . $to . '>' . "\r\n";
        $headers .= 'From: ' . $fromName . ' <' . $fromEmail . '>' . "\r\n";
        
        return mail($to, $subject, $html, $headers);
    }
}


/* End of file notifications.php */
/* Location: ./application/libraries/notifications.php */