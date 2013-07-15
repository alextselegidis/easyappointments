<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backend extends CI_Controller {
    /**
     * Display the main backend page.
     * 
     * This method displays the main backend page. All users login permission can 
     * view this page which displays a calendar with the events of the selected 
     * provider or service. If a user has more priviledges he will see more menus  
     * at the top of the page.
     * 
     * @param string $appointment_hash If given, the appointment edit dialog will 
     * appear when the page loads.
     */
    public function index($appointment_hash = '') {
        // @task Require user to be logged in the application.
        
        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('customers_model');
        $this->load->model('settings_model');
        
        $view['base_url'] = $this->config->item('base_url');
        $view['book_advance_timeout'] = $this->settings_model->get_setting('book_advance_timeout');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['available_providers'] = $this->providers_model->get_available_providers();
        $view['available_services'] = $this->services_model->get_available_services();
        
        if ($appointment_hash != '') {
            $results = $this->appointments_model->get_batch(array('hash' => $appointment_hash));
            $appointment = $results[0];
            $appointment['customer'] = $this->customers_model->get_row($appointment['id_users_customer']);
            $view['edit_appointment'] = $appointment; // This will display the appointment edit dialog on page load.
        } else {
            $view['edit_appointment'] = NULL;
        }
        
        $this->load->view('backend/header', $view);
        $this->load->view('backend/calendar', $view);
        $this->load->view('backend/footer', $view);
    }
    
    /**
     * Display the backend customers page
     * 
     * In this page the user can manage all the customer records of the system.
     */
    public function customers() {
    	// @task Require user to be logged in the application.
    	
        $this->load->model('providers_model');
        $this->load->model('customers_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
        
        $view['base_url'] = $this->config->item('base_url');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['customers'] = $this->customers_model->get_batch();
        $view['available_providers'] = $this->providers_model->get_available_providers();
        $view['available_services'] = $this->services_model->get_available_services();
        
        $this->load->view('backend/header', $view);
        $this->load->view('backend/customers', $view);
        $this->load->view('backend/footer', $view);
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
    
    
}

/* End of file backend.php */
/* Location: ./application/controllers/backend.php */