<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

use EA\Engine\Notifications\Email as EmailClient;
use EA\Engine\Types\Email;
use EA\Engine\Types\NonEmptyText;

/**
 * User Controller
 *
 * @package Controllers
 */
class User extends EA_Controller {
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('settings_model');
        $this->load->model('user_model');
    }

    /**
     * Default Method
     *
     * The default method will redirect the browser to the user/login URL.
     */
    public function index()
    {
        header('Location: ' . site_url('user/login'));
    }

    /**
     * Display the login page.
     *
     * @throws Exception
     */
    public function login()
    {
        $view['base_url'] = config('base_url');
        $view['dest_url'] = $this->session->userdata('dest_url');

        if ( ! $view['dest_url'])
        {
            $view['dest_url'] = site_url('backend');
        }

        $view['company_name'] = $this->settings_model->get_setting('company_name');

        $this->load->view('user/login', $view);
    }

    /**
     * Display the logout page.
     */
    public function logout()
    {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_email');
        $this->session->unset_userdata('role_slug');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('dest_url');

        $view['base_url'] = config('base_url');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $this->load->view('user/logout', $view);
    }

    /**
     * Display the "forgot password" page.
     * @throws Exception
     */
    public function forgot_password()
    {
        $view['base_url'] = config('base_url');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $this->load->view('user/forgot_password', $view);
    }

    /**
     * Display the "not authorized" page.
     * @throws Exception
     */
    public function no_privileges()
    {
        $view['base_url'] = config('base_url');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $this->load->view('user/no_privileges', $view);
    }

    /**
     * Check whether the user has entered the correct login credentials.
     *
     * The session data of a logged in user are the following:
     *   - 'user_id'
     *   - 'user_email'
     *   - 'role_slug'
     *   - 'dest_url'
     */
    public function ajax_check_login()
    {
        try
        {
            if ( ! $this->input->post('username') || ! $this->input->post('password'))
            {
                throw new Exception('Invalid credentials given!');
            }

            $user_data = $this->user_model->check_login($this->input->post('username'), $this->input->post('password'));

            if ($user_data)
            {
                $this->session->set_userdata($user_data); // Save data on user's session.

                $response = AJAX_SUCCESS;
            }
            else
            {
                $response = AJAX_FAILURE;
            }
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Regenerate a new password for the current user, only if the username and
     * email address given correspond to an existing user in db.
     *
     * Required POST Parameters:
     *
     * - string $_POST['username'] Username to be validated.
     * - string $_POST['email'] Email to be validated.
     */
    public function ajax_forgot_password()
    {
        try
        {
            if ( ! $this->input->post('username') || ! $this->input->post('email'))
            {
                throw new Exception('You must enter a valid username and email address in '
                    . 'order to get a new password!');
            }

            $new_password = $this->user_model->regenerate_password(
                $this->input->post('username'),
                $this->input->post('email')
            );

            if ($new_password != FALSE)
            {
                $this->config->load('email');

                $email = new EmailClient($this, $this->config->config);

                $company_settings = [
                    'company_name' => $this->settings_model->get_setting('company_name'),
                    'company_link' => $this->settings_model->get_setting('company_link'),
                    'company_email' => $this->settings_model->get_setting('company_email')
                ];

                $email->send_password(new NonEmptyText($new_password), new Email($this->input->post('email')),
                    $company_settings);
            }

            $response = $new_password != FALSE ? AJAX_SUCCESS : AJAX_FAILURE;
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
