<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Google extends CI_Controller { 
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