<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Easy!Appointments input.
 *
 * @property CI_Benchmark $benchmark
 * @property CI_Cache $cache
 * @property CI_Calendar $calendar
 * @property CI_Config $config
 * @property CI_DB_forge $dbforge
 * @property CI_DB_query_builder $db
 * @property CI_DB_utility $dbutil
 * @property CI_Email $email
 * @property CI_Encrypt $encrypt
 * @property CI_Encryption $encryption
 * @property CI_Exceptions $exceptions
 * @property CI_Hooks $hooks
 * @property CI_Input $input
 * @property CI_Lang $lang
 * @property CI_Loader $load
 * @property CI_Log $log
 * @property CI_Migration $migration
 * @property CI_Output $output
 * @property CI_Profiler $profiler
 * @property CI_Router $router
 * @property CI_Security $security
 * @property CI_Session $session
 * @property CI_URI $uri
 * @property CI_Upload $upload
 *
 * @property string $raw_input_stream
 */
class EA_Input extends CI_Input {
    /**
     * Fetch an item from JSON data.
     *
     * @param string $index Index for item to be fetched from the JSON payload.
     * @param bool|false $xss_clean Whether to apply XSS filtering
     *
     * @return mixed
     */
    public function json(string $index, bool $xss_clean = FALSE)
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        if ($CI->input->get_request_header('Content-Type') !== 'application/json')
        {
            throw new RuntimeException('Cannot get JSON attribute from non-JSON content.');
        }

        $input_stream = $CI->input->raw_input_stream;

        if (empty($input_stream))
        {
            throw new RuntimeException('Cannot get JSON attribute from an empty input stream.');
        }

        $payload = json_decode($input_stream, TRUE);

        $value = $payload[$index] ?? NULL;

        return $value && $xss_clean ? $CI->security->xss_clean($value) : $value;
    }
}
