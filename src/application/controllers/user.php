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
        
        if (!$view['dest_url']) {
            $view['dest_url'] = $view['base_url'] . 'backend';
        }
        
        $this->load->view('user/login', $view);
    }
    
    public function logout() {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_email');
        $this->session->unset_userdata('role_slug');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('dest_url');
        
        $view['base_url'] = $this->config->item('base_url');
        
        $this->load->view('user/logout', $view);
    }
    
    public function forgot_password() {
        
    }
    
    public function no_privileges() {
        $view['base_url'] = $this->config->item('base_url');
        $this->load->view('user/no_privileges', $view);
    }
    
    /**
     * [AJAX] Check whether the user has entered the correct login credentials.
     * 
     * The session data of a logged in user are the following:
     *      'user_id'
     *      'user_email'
     *      'role_slug'
     *      'dest_url'
     */
    public function ajax_check_login() {
        try {
            if (!isset($_POST['username']) || !isset($_POST['password'])) {
                throw new Exception('Invalid credentials given!');
            }
            
            $this->load->model('user_model');
            $user_data = $this->user_model->check_login($_POST['username'], $_POST['password']);
            
            if ($user_data) {
                $this->session->set_userdata($user_data); // Save data on user's session.
                echo json_encode(AJAX_SUCCESS);
            } else {
                echo json_encode(AJAX_FAILURE);
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