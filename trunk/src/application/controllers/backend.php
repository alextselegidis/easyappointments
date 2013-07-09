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
        
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
        
        $view_data['base_url'] = $this->config->item('base_url');
        $view_data['book_advance_timeout'] = $this->settings_model->get_setting('book_advance_timeout');
        $view_data['company_name'] = $this->settings_model->get_setting('company_name');
        $view_data['available_providers'] = $this->providers_model->get_available_providers();
        $view_data['available_services'] = $this->services_model->get_available_services();
        
        $this->load->view('backend/header', $view_data);
        $this->load->view('backend/calendar', $view_data);
        $this->load->view('backend/footer', $view_data);
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
        
        $view_data['base_url'] = $this->config->item('base_url');
        $view_data['company_name'] = $this->settings_model->get_setting('company_name');
        $view_data['customers'] = $this->customers_model->get_batch();
        $view_data['available_providers'] = $this->providers_model->get_available_providers();
        $view_data['available_services'] = $this->services_model->get_available_services();
        
        $this->load->view('backend/header', $view_data);
        $this->load->view('backend/customers', $view_data);
        $this->load->view('backend/footer', $view_data);
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