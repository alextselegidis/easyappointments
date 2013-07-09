<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Contains all the backend ajax calls
 */
class Backend_api extends CI_Controller {
    /**
     * [AJAX] Get the registered appointments for the given date period and record.
     * 
     * This method returns the database appointments for the user selected date 
     * period and record type (provider or service). 
     * 
     * @param {numeric} $_POST['record_id'] Selected record id. 
     * @param {string} $_POST['filter_type'] Could be either FILTER_TYPE_PROVIDER 
     * or FILTER_TYPE_SERVICE.
     * @param {string} $_POST['start_date'] The user selected start date.
     * @param {string} $_POST['end_date'] The user selected end date.
     */
    public function ajax_get_calendar_appointments() {
        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('customers_model');
        
        try {
            if ($_POST['filter_type'] == FILTER_TYPE_PROVIDER) {
                $where_id = 'id_users_provider';
            } else { 
                $where_id = 'id_services';
            }

            $where_clause = array(
                $where_id => $_POST['record_id'],
                'start_datetime >=' => $_POST['start_date'],
                'end_datetime <=' => $_POST['end_date']
            );

            $appointments = $this->appointments_model->get_batch($where_clause);

            foreach($appointments as &$appointment) {
                $appointment['provider'] = $this->providers_model->get_row($appointment['id_users_provider']);
                $appointment['service'] = $this->services_model->get_row($appointment['id_services']);
                $appointment['customer'] = $this->customers_model->get_row($appointment['id_users_customer']);
            }

            echo json_encode($appointments);
            
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavascript($exc))
            ));
        }
    }
    
    /**
     * [AJAX] Save appointment changes that are made from the backend calendar
     * page.
     * 
     * @param array $_POST['appointment_data'] (OPTIONAL) Array with the 
     * appointment data.
     * @param array $_POST['customer_data'] (OPTIONAL) Array with the customer 
     * data.
     */
    public function ajax_save_appointment() {
        try {
        	$this->load->model('appointments_model');
        	$this->load->model('providers_model');
        	$this->load->model('services_model');
        	$this->load->model('customers_model');
        	$this->load->model('settings_model');
        	
            // :: SAVE CUSTOMER CHANGES TO DATABASE
            if (isset($_POST['customer_data'])) {
                $customer_data = json_decode(stripcslashes($_POST['customer_data']), true);
                $customer_data['id'] = $this->customers_model->add($customer_data);
            }
            
        	// :: SAVE APPOINTMENT CHANGES TO DATABASE
            if (isset($_POST['appointment_data'])) {
                $appointment_data = json_decode(stripcslashes($_POST['appointment_data']), true);
                $manage_mode = isset($appointment_data['id']);
                // If the appointment does not contain the customer record id, then it 
                // means that is is going to be inserted. Get the customer's record id.
                if (!isset($appointment_data['id_users_customer'])) {
                    $appointment_data['id_users_customer'] = $customer_data['id'];
                }
                
                $appointment_data['id'] = $this->appointments_model->add($appointment_data);
            }
            
            $appointment_data = $this->appointments_model->get_row($appointment_data['id']);
            $provider_data = $this->providers_model->get_row($appointment_data['id_users_provider']);
            $customer_data = $this->customers_model->get_row($appointment_data['id_users_customer']);
            $service_data = $this->services_model->get_row($appointment_data['id_services']);
            
            $company_settings = array(
            	'company_name' => $this->settings_model->get_setting('company_name'),
            	'company_link' => $this->settings_model->get_setting('company_link'),
            	'company_email' => $this->settings_model->get_setting('company_email')
            );
            
            // :: SYNC APPOINTMENT CHANGES WITH GOOGLE CALENDAR
            try {
                $google_sync = $this->providers_model->get_setting('google_sync', 
                        $appointment_data['id_users_provider']);

                if ($google_sync == TRUE) {
                    $google_token = json_decode($this->providers_model->get_setting('google_token', 
                            $appointment_data['id_users_provider']));

                    $this->load->library('Google_Sync');
                    $this->google_sync->refresh_token($google_token->refresh_token);

                    if ($appointment_data['id_google_calendar'] == NULL) {
                        $this->google_sync->add_appointment($appointment_data, $provider_data, 
                                $service_data, $customer_data, $company_settings);
                    } else {
                        $this->google_sync->update_appointment($appointment_data, $provider_data, 
                                $service_data, $customer_data, $company_settings);
                    }
                }
            } catch(Exception $exc) {
                $warnings[] = exceptionToJavascript($exc);
            }
            
            // :: SEND EMAIL NOTIFICATIONS TO PROVIDER AND CUSTOMER
            try {
                $this->load->library('Notifications');

                if (!$manage_mode) {
                    $customer_title = 'Your appointment has been successfully booked!';
                    $customer_message = 'Thank you for arranging an appointment with us. '   
                            . 'Below you can see the appointment details. Make changes '  
                            . 'by clicking the appointment link.';
                    $customer_link = $this->config->item('base_url') . 'appointments/index/' 
                            . $appointment_data['hash'];

                    $provider_title = 'A new appointment has been added to your plan.';
                    $provider_message = 'You can make changes by clicking the appointment '  
                            . 'link below';
                    $provider_link = $this->config->item('base_url') . 'backend/' 
                            . $appointment_data['hash'];
                } else {
                    $customer_title = 'Appointment changes have been successfully saved!';
                    $customer_message = '';
                    $customer_link = $this->config->item('base_url') . 'appointments/index/' 
                            . $appointment_data['hash'];

                    $provider_title = 'Appointment details have changed.';
                    $provider_message = '';
                    $provider_link = $this->config->item('base_url') . 'backend/' 
                            . $appointment_data['hash'];
                }
            
                $this->notifications->send_appointment_details($appointment_data, $provider_data,
                        $service_data, $customer_data, $company_settings, $customer_title, 
                        $customer_message, $customer_link, $customer_data['email']);

                $this->notifications->send_appointment_details($appointment_data, $provider_data,
                        $service_data, $customer_data, $company_settings, $provider_title, 
                        $provider_message, $provider_link, $provider_data['email']);
                
            } catch(Exception $exc) {
                $warnings[] = exceptionToJavascript($exc);
            }
            
            if (!isset($warnings)) {
                echo json_encode('SUCCESS');
            } else {
                echo json_encode(array(
                    'warnings' => $warnings
                ));
            }
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavascript($exc))
            ));
        }
    }
    
    /**
     * [AJAX] Delete appointment from the database.
     * 
     * This method deletes an existing appointment from the database. Once this 
     * action is finished it cannot be undone. Notification emails are send to both
     * provider and customer and the delete action is executed to the Google Calendar 
     * account of the provider, if the "google_sync" setting is enabled.
     * 
     * @param int $_POST['appointment_id'] The appointment id to be deleted.
     */
    public function ajax_delete_appointment() {    
        try {
            if (!isset($_POST['appointment_id'])) {
                throw new Exception('No appointment id provided.');
            }
            
            // :: STORE APPOINTMENT DATA FOR LATER USE IN THIS METHOD
            $this->load->model('appointments_model');
            $this->load->model('providers_model');
            $this->load->model('customers_model');
            $this->load->model('services_model');
            $this->load->model('settings_model');

            $appointment_data = $this->appointments_model->get_row($_POST['appointment_id']);
            $provider_data = $this->providers_model->get_row($appointment_data['id_users_provider']);
            $customer_data = $this->customers_model->get_row($appointment_data['id_users_customer']);
            $service_data = $this->services_model->get_row($appointment_data['id_services']);

            $company_settings = array(
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_email' => $this->settings_model->get_setting('company_email'),
                'company_link' => $this->settings_model->get_setting('company_link')
            );
            
            // :: DELETE APPOINTMENT RECORD FROM DATABASE
            $this->appointments_model->delete($_POST['appointment_id']);
            
            // :: SYNC DELETE WITH GOOGLE CALENDAR
            if ($appointment_data['id_google_calendar'] != NULL) {
                try {
                    $google_sync = $this->providers_model->get_setting('google_sync', $provider_data['id']);

                    if ($google_sync == TRUE) {
                        $google_token = json_decode($this->providers_model
                                ->get_setting('google_token', $provider_data['id']));
                        $this->load->library('Google_Sync');
                        $this->google_sync->refresh_token($google_token->refresh_token);
                        $this->google_sync->delete_appointment($appointment_data['id_google_calendar']);
                    }    
                } catch(Exception $exc) {
                    $warnings[] = exceptionToJavascript($exc);
                }
            }
            
            // :: SEND NOTIFICATION EMAILS TO PROVIDER AND CUSTOMER
            try {
                $this->load->library('Notifications');
                $this->notifications->send_delete_appointment($appointment_data, $provider_data, 
                        $service_data, $customer_data, $company_settings, $provider_data['email'],
                        $_POST['delete_reason']);
                $this->notifications->send_delete_appointment($appointment_data, $provider_data,
                        $service_data, $customer_data, $company_settings, $customer_data['email'],
                        $_POST['delete_reason']);
            } catch(Exception $exc) {
                $warnings[] = exceptionToJavascript($exc);
            }
            
            // :: SEND RESPONSE TO CLIENT BROWSER
            if (!isset($warnings)) {
                echo json_encode('SUCCESS'); // Everything executed successfully.
            } else {
                echo json_encode(array(
                    'warnings' => $warnings // There were warnings during the operation.
                ));
            }
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavascript($exc))
            ));
        }
    }
    
    /**
     * [AJAX] Disable a providers sync setting.
     * 
     * This method deletes the "google_sync" and "google_token" settings from the 
     * database. After that the provider's appointments will be no longer synced 
     * with google calendar.
     * 
     * @param string $_POST['provider_id'] The selected provider record id.
     */
    public function ajax_disable_provider_sync() {
        try { 
            if (!isset($_POST['provider_id'])) {
                throw new Exception('Provider id not specified.');
            }
            
            $this->load->model('providers_model');
            $this->providers_model->set_setting('google_sync', FALSE, $_POST['provider_id']);
            $this->providers_model->set_setting('google_token', NULL, $_POST['provider_id']);
            
            echo json_encode('SUCCESS');
            
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavascript($exc))
            ));
        }
    }
    
    /**
     * [AJAX] Filter the customer records with the given key string.
     * 
     * @param string $_POST['key'] The filter key string
     * @return array Returns the search results.
     */
    public function ajax_filter_customers() {
    	try {
	    	$this->load->model('customers_model');
	    	
	    	$key = $_POST['key']; //$this->db->escape($_POST['key']);
	    	
	    	$where_clause = 
	    			'first_name LIKE "%' . $key . '%" OR ' . 
	    			'last_name LIKE "%' . $key . '%" OR ' . 
	    			'email LIKE "%' . $key . '%" OR ' .	
	    			'phone_number LIKE "%' . $key . '%" OR ' .
	    			'address LIKE "%' . $key . '%" OR ' .
	    			'city LIKE "%' . $key . '%" OR ' .
	    			'zip_code LIKE "%' . $key . '%" ';		
	    	
	    	echo json_encode($this->customers_model->get_batch($where_clause));
    	} catch(Exception $exc) {
    		echo json_encode(array(
    			'exceptions' => array($exc)	
    		));
    	}
    }
}

/* End of file backend_api.php */
/* Location: ./application/controllers/backend_api.php */