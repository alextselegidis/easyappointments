<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

use \EA\Engine\Types\NonEmptyText;
use \EA\Engine\Types\Email;

/**
 * User Controller
 *
 * @package Controllers
 */
class User extends CI_Controller {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        // Set user's selected language.
        if ($this->session->userdata('language')) {
        	$this->config->set_item('language', $this->session->userdata('language'));
        	$this->lang->load('translations', $this->session->userdata('language'));
        } else {
        	$this->lang->load('translations', $this->config->item('language')); // default
        }
    }

    /**
     * Default Method
     *
     * The default method will redirect the browser to the user/login URL.
     */
    public function index() {
        header('Location: ' . site_url('user/login'));
    }

    /**
     * Display the login page.
     */
    public function login() {
        $this->load->model('settings_model');

        $view['base_url'] = $this->config->item('base_url');
        $view['dest_url'] = $this->session->userdata('dest_url');

        if (!$view['dest_url']) {
            $view['dest_url'] = site_url('backend');
        }

        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $this->load->view('user/login', $view);
    }

    /**
     * Display the logout page.
     */
    public function logout() {
        $this->load->model('settings_model');

        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_email');
        $this->session->unset_userdata('role_slug');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('dest_url');

        $view['base_url'] = $this->config->item('base_url');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $this->load->view('user/logout', $view);
    }

    /**
     * Display the "forgot password" page.
     */
    public function forgot_password() {
        $this->load->model('settings_model');
        $view['base_url'] = $this->config->item('base_url');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $this->load->view('user/forgot_password', $view);
    }

    /**
     * Display the "not authorized" page.
     */
    public function no_privileges() {
        $this->load->model('settings_model');
        $view['base_url'] = $this->config->item('base_url');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $this->load->view('user/no_privileges', $view);
    }

    /**
     * [AJAX] Check whether the user has entered the correct login credentials.
     *
     * The session data of a logged in user are the following:
     *   - 'user_id'
     *   - 'user_email'
     *   - 'role_slug'
     *   - 'dest_url'
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

    /**
     * Regenerate a new password for the current user, only if the username and
     * email address given correspond to an existing user in db.
     *
     * @param string $_POST['username']
     * @param string $_POST['email']
     */
    public function ajax_forgot_password() {
        try {
            if (!isset($_POST['username']) || !isset($_POST['email'])) {
                throw new Exception('You must enter a valid username and email address in '
                        . 'order to get a new password!');
            }

            $this->load->model('user_model');
            $this->load->model('settings_model');

            $new_password = $this->user_model->regenerate_password($_POST['username'], $_POST['email']);

            if ($new_password != FALSE) {
                $this->config->load('email'); 
                $email = new \EA\Engine\Notifications\Email($this, $this->config->config);
                $company_settings = array(
                    'company_name' => $this->settings_model->get_setting('company_name'),
                    'company_link' => $this->settings_model->get_setting('company_link'),
                    'company_email' => $this->settings_model->get_setting('company_email')
                );

                $email->sendPassword(new NonEmptyText($new_password), new Email($_POST['email']), $company_settings);
            }

            echo ($new_password != FALSE) ? json_encode(AJAX_SUCCESS) : json_encode(AJAX_FAILURE);
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavaScript($exc))
            ));
        }
    }
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
