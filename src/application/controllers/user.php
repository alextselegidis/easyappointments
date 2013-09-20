<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
    }
    
    public function index() {
        header('Location: ' . $this->config->item('base_url') . 'user/login');
    }
    
    public function login() {
        $view['base_url'] = $this->config->item('base_url');
        $view['dest_url'] = $this->session->userdata('dest_url');
        $this->load->view('user/login', $view);
    }
    
    public function logout() {
        
    }
    
    public function forgot_password() {
        
    }
    
    public function no_privileges() {
        // can't view the requested page.
    }
    
    /**
     * [AJAX] Check whether the user has entered the correct login credentials.
     */
    public function ajax_check_login() {
        try {
            if (!isset($_POST['username']) || !isset($_POST['password'])) {
                throw new Exception('Invalid credentials given!');
            }
            
            $this->load->helper('general');
            $this->load->model('user_model');
            
            $hash_password = $this->hash_password($_POST['password']);
            $user_data = $this->user_model->check_login($_POST['username'], $hash_password);
            
            if ($user_data) {
                $this->session->set_userdata($user_data); // Save data on user's session.
                echo json_encode(AJAX_SUCCESS);
            } else {
                echo json_encode(AJAX_SUCCESS);
            }
            
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavaScript($exc))
            ));
        }
    }
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */