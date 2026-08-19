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
            'require_captcha' => setting('require_captcha'),
            'altcha_enabled' => setting('altcha_enabled'),
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

            // Apply stricter rate limiting for login attempts (5 failed attempts per 5 minutes)
            if (!$this->apply_login_rate_limit()) {
                json_response(
                    [
                        'success' => false,
                        // ponytail: not localized, as it was not before either. Add a translation key across the 43
                        // language folders if this message ever needs to follow the interface language.
                        'message' => 'Too many login attempts. Please try again in a few minutes.',
                    ],
                    429,
                );

                return;
            }

            check('username', 'string');
            check('password', 'string');
            check('captcha', 'string|null');

            $require_captcha = (bool) setting('require_captcha');

            // Validate CAPTCHA or ALTCHA
            if ($require_captcha) {
                $altcha_enabled = setting('altcha_enabled') === '1';

                if ($altcha_enabled) {
                    check('altcha_payload', 'string|null');
                    $altcha_payload = request('altcha_payload');

                    $this->load->library('altcha_client');

                    if (!$this->altcha_client->verify($altcha_payload)) {
                        json_response([
                            'success' => false,
                            'altcha_verification' => false,
                        ]);
                        return;
                    }
                } else {
                    $captcha = request('captcha');
                    $captcha_phrase = session('captcha_phrase');

                    if (
                        empty($captcha_phrase) ||
                        empty($captcha) ||
                        strtoupper($captcha_phrase) !== strtoupper($captcha)
                    ) {
                        json_response([
                            'success' => false,
                            'captcha_verification' => false,
                        ]);
                        return;
                    }
                }
            }

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

                json_response([
                    'success' => false,
                    'message' => lang('invalid_credentials_provided'),
                ]);

                return;
            }

            $this->clear_login_rate_limit(); // The credentials were valid, so the counted attempts can be forgotten.

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
     * Get the rate limiting cache key of the requesting client.
     *
     * @return string|null Null when the cache driver is not available.
     */
    private function get_login_rate_limit_key(): ?string
    {
        $this->load->driver('cache', ['adapter' => 'file']);

        if (!isset($this->cache) || !is_object($this->cache)) {
            log_message('debug', 'Cache driver not available, skipping rate limit check.');

            return null;
        }

        return 'login_attempts_' . str_replace([':', '.'], '_', $this->input->ip_address());
    }

    /**
     * Count the login attempt of the requesting client and tell whether it is over the limit.
     *
     * Only failed attempts are counted, as the counter is cleared by clear_login_rate_limit() once the credentials
     * turn out to be valid. That way a legitimate user signing in repeatedly is never locked out.
     *
     * @return bool True when the client is allowed to attempt a login.
     */
    private function apply_login_rate_limit(): bool
    {
        try {
            $cache_key = $this->get_login_rate_limit_key();

            if ($cache_key === null) {
                return true;
            }

            $attempts = $this->cache->get($cache_key);

            if ($attempts === false) {
                $this->cache->save($cache_key, 1, LOGIN_RATE_LIMIT_WINDOW);

                return true;
            }

            $this->cache->save($cache_key, $attempts + 1, LOGIN_RATE_LIMIT_WINDOW);

            if ($attempts >= LOGIN_RATE_LIMIT_ATTEMPTS) {
                log_message('error', 'Login rate limit exceeded for IP: ' . $this->input->ip_address());

                return false;
            }
        } catch (Throwable $e) {
            // Log cache errors but don't block login
            log_message('error', 'Cache error in login rate limiting: ' . $e->getMessage());
        }

        return true;
    }

    /**
     * Forget the counted login attempts of the requesting client after a successful login.
     */
    private function clear_login_rate_limit(): void
    {
        try {
            $cache_key = $this->get_login_rate_limit_key();

            if ($cache_key !== null) {
                $this->cache->delete($cache_key);
            }
        } catch (Throwable $e) {
            // Log cache errors but never fail a successful login over them
            log_message('error', 'Cache error in login rate limiting: ' . $e->getMessage());
        }
    }
}
