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
        $view_data['base_url']      = $this->config->item('base_url');
        $view_data['company_name']  = $this->Settings_Model->get_setting('company_name');
        $view_data['available_providers']   = $this->Providers_Model->get_available_providers();
        $view_data['available_services']    = $this->Services_Model->get_available_services();
        
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
}

/* End of file backend.php */
/* Location: ./application/controllers/backend.php */