<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backend extends CI_Controller {
    /**
     * Display the main backend page.
     * 
     * This method displays the main backend page. All users login permission can 
     * view this page which displays a calendar with the events of the selected 
     * provider or service. If a user has more priviledges he will see more menus  
     * at the top of the page.
     */
    public function index() {
        // @task Require user to be logged in the application.
        
        $this->load->model('Providers_Model');
        $this->load->model('Services_Model');
        $this->load->model('Settings_Model');
        
        // Display the main backend page.
        $view_data['base_url']     = $this->config->item('base_url');
        $view_data['company_name'] = $this->Settings_Model->get_setting('company_name');
        $view_data['available_providers'] = $this->Providers_Model->get_available_providers();
        $view_data['available_services']  = $this->Services_Model->get_available_services();
        
        $this->load->view('backend/header', $view_data);
        $this->load->view('backend/calendar', $view_data);
        $this->load->view('backend/footer', $view_data);
    }
    
    public function customers() {
        echo '<h1>Not implemented yet.</h1>';
    }
    
    public function services() {
        echo '<h1>Not implemented yet.</h1>';
    }
    
    public function providers() {
        echo '<h1>Not implemented yet.</h1>';
    }
    
    public function settings() {
        echo '<h1>Not implemented yet.</h1>';
    }
    
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
        $this->load->model('Appointments_Model');
        $this->load->model('Providers_Model');
        $this->load->model('Services_Model');
        $this->load->model('Customers_Model');
        
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
        
        $appointments = $this->Appointments_Model->get_batch($where_clause);
        
        foreach($appointments as &$appointment) {
            $appointment['provider'] = $this->Providers_Model
                    ->get_row($appointment['id_users_provider']);
            $appointment['service'] = $this->Services_Model
                    ->get_row($appointment['id_services']);
            $appointment['customer'] = $this->Customers_Model
                    ->get_row($appointment['id_users_customer']);
        }
        
        echo json_encode($appointments);
    }
    
    /**
     * [AJAX] Save appointment changes that are made from the backend calendar
     * page.
     * 
     * @param array $_POST['appointment_data'] (OPTIONAL) Array with the 
     * appointment data.
     * @param array $_POST['customer_data'] (OPTIONAL) Array with the customer 
     * data.
     * 
     * @task Send email notifications to both provider and customer that changes
     * have been made to the appointment.
     * @task Sync changes with google calendar.
     */
    public function ajax_save_appointment_changes() {
        try {
            if (isset($_POST['appointment_data'])) {
                $appointment_data = json_decode(stripcslashes($_POST['appointment_data']), true);
                $this->load->model('Appointments_Model');
                $this->Appointments_Model->add($appointment_data);
                
                if ($appointment_data['id_google_calendar'] != NULL) {
                    $this->load->model('Providers_Model');
                    $provider_settings = json_decode($this->Providers_Model
                            ->get_setting('google_token', $appointment_data['id_users_provider']));
                    
                    if ($provider_settings->google_sync == TRUE) {
                        $this->load->library('Google_Sync');
                        $this->google_sync->refresh_token($provider_settings->refresh_token);
                        $this->google_sync->update_appointment($appointment_data['id']);
                    }
                }
            }
            
            if (isset($_POST['customer_data'])) {
                $customer_data = json_decode(stripcslashes($_POST['customer_data']), true);
                $this->load->model('Customers_Model');
                $this->Customers_Model->add($customer_data);
            }
            
            echo json_encode('SUCCESS');
       
        } catch(Exception $exc) {
            echo json_encode(array(
                'error' => $exc->getMessage()
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
     * 
     * @task Sync action with GCal.
     * @task Send email notifications to provider and customer.
     */
    public function ajax_delete_appointment() {
        try {
            if (!isset($_POST['appointment_id'])) {
                throw new Exception('No appointment id provided.');
            }
            
            // :: STORE APPOINTMENT DATA FOR LATER USE IN THIS METHOD
            $this->load->model('Appointments_Model');
            $this->load->model('Providers_Model');
            $this->load->model('Customers_Model');
            $this->load->model('Services_Model');
            
            $appointment_data = $this->Appointments_Model->get_row($_POST['appointment_id']);
            $provider_data  = $this->Providers_Model->get_row($appointment_data['id_users_provider']);
            $customer_data  = $this->Customers_Model->get_row($appointment_data['id_users_customer']);
            $service_data   = $this->services_Model->get_row($appointment_data['id_services']);
            
            // :: DELETE APPOINTMENT RECORD FROM DATABASE.
            $this->Appointments_Model->delete($_POST['appointment_id']);
            
            // :: SYNC CHANGE TO GOOGLE CALENDAR
            $google_sync = $this->Providers_Model->get_setting('google_sync', 
                    $provider_data['id']);
            
            if ($google_sync == TRUE) {
                $google_token = json_decode($this->Providers_Model->get_setting('google_token', 
                        $provider_data['id']));
                
                $this->load->library('Google_Sync');
                $this->google_sync->refresh_token($google_token->refresh_token);
                $this->google_sync->delete_appointment($appointment_data['id_google_calendar']);
            }
            
            // :: SEND NOTIFICATION EMAILS TO PROVIDER AND CUSTOMER.
            $this->load->library('Notifications');
            $this->notification->send_delete_appointment($appointment_data, $provider_data, 
                    $service_data, $customer_data);
            
            echo json_encode('SUCCESS');
            
        } catch(Exception $exc) {
            echo json_encode(array(
                'error' => $exc->getMessage()
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
            
            $this->load->model('Providers_Model');
            
            $this->Providers_Model->set_setting('google_sync', FALSE, 
                    $_POST['provider_id']);
            $this->Providers_Model->set_setting('google_token', NULL,
                    $_POST['provider_id']);
            
            echo json_encode('SUCCESS');
            
        } catch(Exception $exc) {
            echo json_encode(array(
                'error' => $exc->getMessage()
            ));
        }
    }
}

/* End of file backend.php */
/* Location: ./application/controllers/backend.php */