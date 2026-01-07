<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Login controller.
 *
 * Handles the login page functionality.
 *
 * @package Controllers
 */
class Login extends EA_Controller
{
    /**
     * Login constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('accounts');
        $this->load->library('ldap_client');
        $this->load->library('email_messages');

        script_vars([
            'dest_url' => session('dest_url', site_url('calendar')),
        ]);
    }

    /**
     * Render the login page.
     */
    public function index(): void
    {
        method('get');

        if (session('user_id')) {
            redirect('calendar');
            return;
        }

        html_vars([
            'page_title' => lang('login'),
            'base_url' => config('base_url'),
            'dest_url' => session('dest_url', site_url('calendar')),
            'company_name' => setting('company_name'),
        ]);

        $this->load->view('pages/login');
    }

    /**
     * Validate the provided credentials and start a new session if the validation was successful.
     */
    public function validate(): void
    {
        try {
            method('post');

            // Apply stricter rate limiting for login attempts (5 attempts per 5 minutes)
            $this->apply_login_rate_limit();

            check('username', 'string');
            check('password', 'string');

            $username = request('username');

            if (empty($username)) {
                throw new InvalidArgumentException('No username value provided.');
            }

            // Validate username format to prevent injection
            if (!preg_match('/^[a-zA-Z0-9_@.\-]+$/', $username) || strlen($username) > 255) {
                throw new InvalidArgumentException(lang('invalid_credentials_provided'));
            }

            $password = request('password');

            if (empty($password)) {
                throw new InvalidArgumentException('No password value provided.');
            }

            // Password length check
            if (strlen($password) > MAX_PASSWORD_LENGTH) {
                throw new InvalidArgumentException(lang('invalid_credentials_provided'));
            }

            $user_data = $this->accounts->check_login($username, $password);

            if (empty($user_data)) {
                $user_data = $this->ldap_client->check_login($username, $password);
            }

            if (empty($user_data)) {
                // Log failed login attempt
                log_message(
                    'info',
                    'Failed login attempt for username: ' . $username . ' from IP: ' . $this->input->ip_address(),
                );
                // Use constant time response to prevent username enumeration
                usleep(random_int(100000, 300000)); // 100-300ms delay
                throw new InvalidArgumentException(lang('invalid_credentials_provided'));
            }

            $this->session->sess_regenerate(true); // Regenerate session ID and delete old session

            session($user_data); // Save data in the session.

            log_message('info', 'Successful login for user: ' . $username . ' from IP: ' . $this->input->ip_address());

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Apply rate limiting specifically for login attempts.
     *
     * @throws RuntimeException If rate limit is exceeded.
     */
    private function apply_login_rate_limit(): void
    {
        $this->load->driver('cache', ['adapter' => 'file']);

        $ip = $this->input->ip_address();
        $cache_key = 'login_attempts_' . str_replace([':', '.'], '_', $ip);

        $attempts = $this->cache->get($cache_key);

        if ($attempts === false) {
            $this->cache->save($cache_key, 1, 300); // 5 minutes
        } else {
            $this->cache->save($cache_key, $attempts + 1, 300);

            if ($attempts >= 5) {
                log_message('warning', 'Login rate limit exceeded for IP: ' . $ip);
                throw new RuntimeException('Too many login attempts. Please try again in a few minutes.');
            }
        }
    }
}
