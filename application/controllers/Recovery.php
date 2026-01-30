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
     * User constructor.
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
     * Recover the user password and notify the user via email.
     */
    public function perform(): void
    {
        try {
            method('post');

            // Apply rate limiting for password recovery (3 attempts per 15 minutes)
            $this->apply_recovery_rate_limit();

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
                $new_password = $this->accounts->regenerate_password($username, $email);

                $company_color = setting('company_color');

                if ($new_password) {
                    $settings = [
                        'company_name' => setting('company_name'),
                        'company_link' => setting('company_link'),
                        'company_email' => setting('company_email'),
                        'company_color' =>
                            !empty($company_color) && $company_color != DEFAULT_COMPANY_COLOR ? $company_color : null,
                    ];

                    $this->email_messages->send_password($new_password, $email, $settings);
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
     * Apply rate limiting specifically for password recovery attempts.
     *
     * @throws RuntimeException If rate limit is exceeded.
     */
    private function apply_recovery_rate_limit(): void
    {
        $this->load->driver('cache', ['adapter' => 'file']);

        $ip = $this->input->ip_address();
        $cache_key = 'recovery_attempts_' . str_replace([':', '.'], '_', $ip);

        $attempts = $this->cache->get($cache_key);

        if ($attempts === false) {
            $this->cache->save($cache_key, 1, 900); // 15 minutes
        } else {
            $this->cache->save($cache_key, $attempts + 1, 900);

            if ($attempts >= 3) {
                log_message('warning', 'Password recovery rate limit exceeded for IP: ' . $ip);
                throw new RuntimeException('Too many recovery attempts. Please try again later.');
            }
        }
    }
}
