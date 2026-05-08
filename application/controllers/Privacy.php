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

        // Explicitly load and assign the cache driver. CI's loader normally assigns it
        // to $this->cache automatically, but on some setups (e.g. when rate_limiting is
        // disabled in the config and the rate_limit() helper has not pre-loaded the
        // driver) this property can end up unset, which would cause a "Call to a member
        // function get() on null" further down. Assigning it explicitly here avoids that.
        if (!class_exists('CI_Cache', false)) {
            require_once BASEPATH . 'libraries/Cache/Cache.php';
        }

        $this->cache = new CI_Cache(['adapter' => 'file']);

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

            // Note: CSRF validation is already performed globally by EA_Security
            // (the privacy/* URI is not in csrf_exclude_uris). Re-checking the cookie
            // here would always fail because EA_Security unsets the previous CSRF
            // cookie after validation in order to regenerate a fresh token.

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
        try {
            $ip = $this->input->ip_address();
            $cache_key = 'privacy_delete_attempts_' . str_replace([':', '.'], '_', $ip);

            $attempts = $this->cache->get($cache_key);

            if ($attempts === false) {
                $this->cache->save($cache_key, 1, 900); // 15 minutes
                return;
            }

            $this->cache->save($cache_key, $attempts + 1, 900);

            if ($attempts >= 3) {
                log_message('error', 'Privacy deletion rate limit exceeded for IP: ' . $ip);
                throw new RuntimeException('Too many deletion attempts. Please try again later.');
            }
        } catch (RuntimeException $e) {
            throw $e;
        } catch (Throwable $e) {
            log_message('error', 'Cache error in privacy rate limiting: ' . $e->getMessage());
        }
    }
}
