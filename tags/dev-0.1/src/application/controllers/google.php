<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Google extends CI_Controller { 
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Google API authorization redirect page. 
     * 
     * This is the page that the google api should redirect after the 
     * user allows the api to handle his data. A redirect page must be
     * already set in the $_SESSION array in order to redirect to the 
     * correct page.
     * 
     * @task Make redirect page with session variable.
     */
    public function api_auth() {
        session_start();
        
        if (isset($_SESSION['sync_appointment_id'])) {
            header('Location: ' . $this->config->base_url() . 'appointments/google_sync/' . $_SESSION['sync_appointment_id'] . '?code=' . $_GET['code']);
        } else {
            echo 'An error occured during the Google Calendar API authorization process.';
        }   
    }
}

/* End of file google.php */
/* Location: ./application/controllers/google.php */