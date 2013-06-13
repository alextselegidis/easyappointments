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
        
    }
    
    public function services() {
        
    }
    
    public function providers() {
        
    }
    
    public function settings() {
        
    }
    
    /**
     * [AJAX] Get the registered appointments for the given date period and record.
     * 
     * This method returns the database appointments for the user selected date 
     * period and record type (provider or service). 
     * 
     * @param {numeric} $_POST['record_id'] Selected record id. 
     * @param {string} $_POST['filter_type'] Could be either FILTER_TYPE_PROVIDER or
     * FILTER_TYPE_SERVICE.
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
}

/* End of file backend.php */
/* Location: ./application/controllers/backend.php */