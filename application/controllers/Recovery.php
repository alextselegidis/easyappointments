<?php defined('BASEPATH') or exit('No direct script access allowed');
/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Recovery controller.
 *
 * Handles the recovery page functionality.
 *
 * @package Controllers
 */
class Recovery extends EA_Controller
{
    /**
     * Recovery constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('accounts');
        $this->load->library('email_messages');
    }

    /**
     * Display the password recovery page.
     */
    public function index(): void
    {
        method('get');

        $company_name = setting('company_name');

        html_vars([
            'page_title' => lang('forgot_your_password'),
            'dest_url' => session('dest_url', site_url('backend')),
            'company_name' => $company_name,
        ]);

        $this->load->view('pages/recovery');
    }

    /**
     * Request a password reset link and send it via email.
     */
    public function perform(): void
    {
        try {
            method('post');

            check('username', 'string');
            check('email', 'email');

            $username = request('username');

            if (empty($username)) {
                throw new InvalidArgumentException('No username value provided.');
            }

            // Validate username format
            if (!preg_match('/^[a-zA-Z0-9_@.\-]+$/', $username) || strlen($username) > 255) {
                throw new InvalidArgumentException('Invalid username format.');
            }

            $email = request('email');

            if (empty($email)) {
                throw new InvalidArgumentException('No email value provided.');
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
                throw new InvalidArgumentException('Invalid email format.');
            }

            // Always respond with success to prevent user enumeration
            // Even if the user doesn't exist, we don't reveal that information
            try {
                $reset_data = $this->accounts->generate_reset_token($username, $email);
                $company_color = setting('company_color');

                if ($reset_data) {
                    $reset_link = site_url('recovery/reset?token=' . $reset_data['token']);
                    $settings = [
                        'company_name' => setting('company_name'),
                        'company_link' => setting('company_link'),
                        'company_email' => setting('company_email'),
                        'company_color' =>
                            !empty($company_color) && $company_color != DEFAULT_COMPANY_COLOR ? $company_color : null,
                    ];

                    $this->email_messages->send_password_reset_link($reset_link, $reset_data['email'], $settings);
                }
            } catch (RuntimeException $e) {
                // Log the actual error but don't reveal it to the user
                log_message(
                    'info',
                    'Password recovery attempted for non-existent user: ' .
                        $username .
                        ' / ' .
                        $email .
                        ' from IP: ' .
                        $this->input->ip_address(),
                );
            }

            // Add a small delay to prevent timing attacks
            usleep(random_int(100000, 500000)); // 100-500ms delay

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Display the password reset form.
     */
    public function reset(): void
    {
        method('get');

        $token = $this->input->get('token');

        if (empty($token)) {
            redirect('recovery');
            return;
        }

        // Validate token format (should be 64 hex characters)
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            html_vars([
                'page_title' => lang('reset_password'),
                'token_valid' => false,
                'error_message' => lang('invalid_reset_token'),
            ]);

            $this->load->view('pages/password_reset');

            return;
        }

        // Validate the token
        $user = $this->accounts->validate_reset_token($token);

        if (!$user) {
            html_vars([
                'page_title' => lang('reset_password'),
                'token_valid' => false,
                'error_message' => lang('invalid_or_expired_token'),
            ]);
            $this->load->view('pages/password_reset');
            return;
        }

        html_vars([
            'page_title' => lang('reset_password'),
            'token_valid' => true,
            'token' => $token,
            'company_name' => setting('company_name'),
        ]);

        $this->load->view('pages/password_reset');
    }

    /**
     * Complete the password reset process.
     */
    public function complete(): void
    {
        try {
            method('post');

            $token = request('token');
            $password = request('password');
            $password_confirm = request('password_confirm');

            if (empty($token)) {
                throw new InvalidArgumentException('No token provided.');
            }

            // Validate token format
            if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
                throw new InvalidArgumentException('Invalid token format.');
            }

            if (empty($password)) {
                throw new InvalidArgumentException('No password provided.');
            }

            if (strlen($password) < MIN_PASSWORD_LENGTH) {
                throw new InvalidArgumentException(
                    str_replace('$number', MIN_PASSWORD_LENGTH, lang('password_length_notice')),
                );
            }

            if ($password !== $password_confirm) {
                throw new InvalidArgumentException(lang('passwords_mismatch'));
            }

            $this->accounts->reset_password_with_token($token, $password);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
