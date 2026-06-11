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
 * Easy!Appointments session.
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
class EA_Session extends CI_Session
{
    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->refresh_cross_site_session_cookie();
    }

    /**
     * @param array $params
     */
    protected function _configure(&$params)
    {
        parent::_configure($params);

        if (
            !function_exists('cross_site_cookies_required')
            || !cross_site_cookies_required()
            || !is_https()
        ) {
            return;
        }

        $options = [
            'lifetime' => $params['cookie_lifetime'],
            'path' => $params['cookie_path'],
            'domain' => $params['cookie_domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None',
        ];

        if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 80500) {
            $options['partitioned'] = true;
        }

        @session_set_cookie_params($options);
    }

    /**
     * Re-issue the session cookie with cross-site attributes when embedding is enabled.
     */
    private function refresh_cross_site_session_cookie(): void
    {
        if (
            !function_exists('cross_site_cookies_required')
            || !function_exists('set_application_cookie')
            || !cross_site_cookies_required()
            || !is_https()
            || empty($this->_config['cookie_name'])
        ) {
            return;
        }

        $expires = empty($this->_config['cookie_lifetime']) ? 0 : time() + $this->_config['cookie_lifetime'];

        set_application_cookie(
            $this->_config['cookie_name'],
            session_id(),
            $expires,
            true,
            true,
            true,
        );
    }
}
