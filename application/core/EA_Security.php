<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Easy!Appointments security.
 *
 * @property EA_Benchmark $benchmark
 * @property EA_Cache $cache
 * @property EA_Calendar $calendar
 * @property EA_Config $config
 * @property EA_DB_forge $dbforge
 * @property EA_DB_query_builder $db
 * @property EA_DB_utility $dbutil
 * @property EA_Email $email
 * @property EA_Encrypt $encrypt
 * @property EA_Encryption $encryption
 * @property EA_Exceptions $exceptions
 * @property EA_Hooks $hooks
 * @property EA_Input $input
 * @property EA_Lang $lang
 * @property EA_Loader $load
 * @property EA_Log $log
 * @property EA_Migration $migration
 * @property EA_Output $output
 * @property EA_Profiler $profiler
 * @property EA_Router $router
 * @property EA_Security $security
 * @property EA_Session $session
 * @property EA_Upload $upload
 * @property EA_URI $uri
 */
class EA_Security extends CI_Security
{
    /**
     * CSRF Verify
     *
     * @return    CI_Security
     */
    public function csrf_verify()
    {
        // If it's not a POST request we will set the CSRF cookie
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            return $this->csrf_set_cookie();
        }

        // Check if URI has been whitelisted from CSRF checks
        if ($exclude_uris = config_item('csrf_exclude_uris')) {
            $uri = load_class('URI', 'core');
            foreach ($exclude_uris as $excluded) {
                if (preg_match('#^' . $excluded . '$#i' . (UTF8_ENABLED ? 'u' : ''), $uri->uri_string())) {
                    return $this;
                }
            }
        }

        // Check CSRF token validity, but don't error on mismatch just yet - we'll want to regenerate
        $csrf_token = $_POST[$this->_csrf_token_name] ?? ($_SERVER['HTTP_X_CSRF'] ?? null);

        $valid =
            isset($csrf_token, $_COOKIE[$this->_csrf_cookie_name]) &&
            is_string($csrf_token) &&
            is_string($_COOKIE[$this->_csrf_cookie_name]) &&
            hash_equals($csrf_token, $_COOKIE[$this->_csrf_cookie_name]);

        // We kill this since we're done and we don't want to pollute the _POST array
        unset($_POST[$this->_csrf_token_name]);

        // Regenerate on every submission?
        if (config_item('csrf_regenerate')) {
            // Nothing should last forever
            unset($_COOKIE[$this->_csrf_cookie_name]);
            $this->_csrf_hash = null;
        }

        $this->_csrf_set_hash();
        $this->csrf_set_cookie();

        if ($valid !== true) {
            $this->csrf_show_error();
        }

        log_message('info', 'CSRF token verified');
        return $this;
    }
}
