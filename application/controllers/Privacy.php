<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

/**
 * Privacy controller.
 *
 * Handles the privacy related operations.
 *
 * @package Controllers
 */
class Privacy extends EA_Controller
{
    /**
     * Privacy constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('customers_model');
    }

    /**
     * Remove a customer's personal information from the system.
     */
    public function delete_personal_information(): void
    {
        try {
            method('post');

            // Apply rate limiting for privacy deletion requests (3 attempts per 15 minutes)
            $this->apply_privacy_rate_limit();

            // Verify CSRF token
            $this->verify_csrf_token();

            $display_delete_personal_information = setting('display_delete_personal_information');

            if (!$display_delete_personal_information) {
                abort(403, 'Forbidden');
            }

            check('customer_token', 'string');

            $customer_token = request('customer_token');

            if (empty($customer_token)) {
                throw new InvalidArgumentException('Invalid customer token value provided.');
            }

            // Validate token format (alphanumeric, 32 chars)
            if (!preg_match('/^[a-fA-F0-9]{32}$/', $customer_token)) {
                throw new InvalidArgumentException('Invalid customer token format.');
            }

            $customer_id = $this->cache->get('customer-token-' . $customer_token);

            if (empty($customer_id)) {
                throw new InvalidArgumentException(
                    'Customer ID does not exist, please reload the page ' . 'and try again.',
                );
            }

            $this->customers_model->delete($customer_id);

            log_message(
                'info',
                'Customer personal information deleted. Customer ID: ' .
                    $customer_id .
                    ' IP: ' .
                    $this->input->ip_address(),
            );

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Apply rate limiting for privacy deletion requests.
     *
     * @throws RuntimeException If rate limit is exceeded.
     */
    private function apply_privacy_rate_limit(): void
    {
        $this->load->driver('cache', ['adapter' => 'file']);

        $ip = $this->input->ip_address();
        $cache_key = 'privacy_delete_attempts_' . str_replace([':', '.'], '_', $ip);

        $attempts = $this->cache->get($cache_key);

        if ($attempts === false) {
            $this->cache->save($cache_key, 1, 900); // 15 minutes
        } else {
            $this->cache->save($cache_key, $attempts + 1, 900);

            if ($attempts >= 3) {
                log_message('warning', 'Privacy deletion rate limit exceeded for IP: ' . $ip);
                throw new RuntimeException('Too many deletion attempts. Please try again later.');
            }
        }
    }

    /**
     * Verify CSRF token for privacy requests.
     *
     * @throws RuntimeException If CSRF token is invalid.
     */
    private function verify_csrf_token(): void
    {
        $csrf_token = request('csrf_token') ?? $this->input->get_request_header('X-CSRF');
        $csrf_cookie = $this->input->cookie('csrf_cookie');

        if (empty($csrf_token) || empty($csrf_cookie) || !hash_equals($csrf_cookie, $csrf_token)) {
            log_message('warning', 'Invalid CSRF token in privacy request from IP: ' . $this->input->ip_address());
            throw new RuntimeException('Security validation failed. Please refresh the page and try again.');
        }
    }
}
