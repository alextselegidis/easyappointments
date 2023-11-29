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
 * Easy!Appointments input.
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
 *
 * @property string $raw_input_stream
 */
class EA_Input extends CI_Input
{
    /**
     * Fetch an item from JSON data.
     *
     * @param string|null $index Index for item to be fetched from the JSON payload.
     * @param bool|false $xss_clean Whether to apply XSS filtering
     *
     * @return mixed
     */
    public function json(string $index = null, bool $xss_clean = false)
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        if (strpos((string) $CI->input->get_request_header('Content-Type'), 'application/json') === false) {
            return null;
        }

        $input_stream = $CI->input->raw_input_stream;

        if (empty($input_stream)) {
            throw new RuntimeException('Cannot get JSON attribute from an empty input stream.');
        }

        $payload = json_decode($input_stream, true);

        if ($xss_clean) {
            foreach ($payload as $name => $value) {
                $payload[$name] = $CI->security->xss_clean($value);
            }
        }

        if (empty($index)) {
            return $payload;
        }

        return $payload[$index] ?? null;
    }
}
