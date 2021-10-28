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
 * User controller
 *
 * Handles the user related operations.
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

        $this->load->library('accounts');
    }

    /**
     * Redirect to the login page.
     */
    public function index()
    {
        redirect('user/login');
    }

    /**
     * Display the login page.
     */
    public function login()
    {
        $this->load->view('user/login', [
            'base_url' => config('base_url'),
            'company_name' => setting('company_name'),
            'dest_url' => session('dest_url', site_url('backend'))
        ]);
    }

    /**
     * Display the logout page.
     */
    public function logout()
    {
        $this->session->sess_destroy();

        $this->load->view('user/logout', [
            'base_url' => config('base_url'),
            'company_name' => setting('company_name')
        ]);
    }

    /**
     * Display the password recovery page.
     */
    public function forgot_password()
    {
        $this->load->view('user/forgot_password', [
            'base_url' => config('base_url'),
            'company_name' => setting('company_name')
        ]);
    }

    /**
     * Display the no-permissions page.
     */
    public function no_permissions()
    {
        $this->load->view('user/no_privileges', [
            'base_url' => config('base_url'),
            'company_name' => setting('company_name')
        ]);
    }

    /**
     * Validate the login credentials and if successful, log the user in.
     */
    public function ajax_check_login()
    {
        try
        {
            if ( ! request('username') || ! request('password'))
            {
                throw new Exception('Invalid credentials given!');
            }

            $username = request('username');

            $password = request('password');

            $user_data = $this->accounts->check_login($username, $password);

            if ($user_data)
            {
                session($user_data); // Save data in the session.

                $response = AJAX_SUCCESS;
            }
            else
            {
                $response = AJAX_FAILURE;
            }
        }
        catch (Throwable $e)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $e->getMessage(),
                'trace' => config('debug') ? $e->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Recover the user password and notify the user via email.
     */
    public function ajax_forgot_password()
    {
        try
        {
            $username = request('username');

            if (empty($username))
            {
                throw new InvalidArgumentException('No username value provided.');
            }

            $email = request('email');

            if (empty($email))
            {
                throw new InvalidArgumentException('No email value provided.');
            }

            $new_password = $this->accounts->regenerate_password(
                $username,
                $email
            );

            if ($new_password)
            {
                $email = new EmailClient($this, $this->config->config);

                $settings = [
                    'company_name' => setting('company_name'),
                    'company_link' => setting('company_link'),
                    'company_email' => setting('company_email')
                ];

                $email->send_password(new NonEmptyText($new_password), new Email(request('email')), $settings);
            }

            json_response([
                'success' => TRUE
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
