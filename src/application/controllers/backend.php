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
     * Display the backend customers page.
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
    
    /**
     * Displays the backend services page. 
     * 
     * Here the admin user will be able to organize and create the services 
     * that the user will be able to book appointments in frontend. 
     * 
     * NOTICE: The services that each provider is able to service is managed 
     * from the backend services page. 
     */
    public function services() {
        // @task Require user to be logged in the application.
        
        $this->load->model('customers_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
        
        $view['base_url'] = $this->config->item('base_url');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['services'] = $this->services_model->get_batch();
        $view['categories'] = $this->services_model->get_all_categories();
        
        $this->load->view('backend/header', $view);
        $this->load->view('backend/services', $view);
        $this->load->view('backend/footer', $view);
    }
    
    /**
     * Display the backend users page.
     * 
     * In this page the admin user will be able to manage the system users. 
     * By this, we mean the provider, secretary and admin users. This is also
     * the page where the admin defines which service can each provider provide.
     */
    public function users() {
        // @task Require user to be logged in the application.
        
        $this->load->model('providers_model');
        $this->load->model('secretaries_model');
        $this->load->model('admins_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
        
        $view['base_url'] = $this->config->item('base_url');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['admins'] = $this->admins_model->get_batch();
        $view['providers'] = $this->providers_model->get_batch();
        $view['secretaries'] = $this->secretaries_model->get_batch();
        $view['services'] = $this->services_model->get_batch(); 
        
        $this->load->view('backend/header', $view);
        $this->load->view('backend/users', $view);
        $this->load->view('backend/footer', $view);
    }
    
    /**
     * Display the user/system settings.
     * 
     * This page will display the user settings (name, password etc). If current user is
     * an administrator, then he will be able to make change to the current Easy!Appointment 
     * installation (core settings like company name, book timeout etc). 
     */
    public function settings() {
        $this->load->model('settings_model');
        $this->load->model('user_model');
        
        $this->load->library('session');
        
        // @task Apply data for testing this page (this must be done during the login process).
        $this->session->set_userdata('user_id', 18);
        $this->session->set_userdata('user_slug', DB_SLUG_ADMIN);
        
        $user_id = $this->session->userdata('user_id'); 
        
        $view['base_url'] = $this->config->item('base_url');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['user_slug'] = $this->session->userdata('user_slug');
        $view['system_settings'] = $this->settings_model->get_settings();
        $view['user_settings'] = $this->user_model->get_settings($user_id);
        
        $this->load->view('backend/header', $view);
        $this->load->view('backend/settings', $view);
        $this->load->view('backend/footer', $view);
    }
}

/* End of file backend.php */
/* Location: ./application/controllers/backend.php */