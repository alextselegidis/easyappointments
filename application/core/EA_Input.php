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
    public function json(?string $index = null, bool $xss_clean = true): mixed
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        if (strpos((string) $CI->input->get_request_header('Content-Type'), 'application/json') === false) {
            return null;
        }

        $input_stream = $CI->input->raw_input_stream;

        if (empty($input_stream)) {
            return null;
        }

        $payload = json_decode($input_stream, true);

        if ($payload === null && json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        if ($xss_clean && is_array($payload)) {
            $payload = $this->xss_clean_recursive($payload, $CI->security);
        }

        if (empty($index)) {
            return $payload;
        }

        return $payload[$index] ?? null;
    }

    /**
     * Recursively apply XSS cleaning to an array.
     *
     * @param array $data The data to clean.
     * @param CI_Security $security The security instance.
     *
     * @return array The cleaned data.
     */
    private function xss_clean_recursive(array $data, CI_Security $security): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->xss_clean_recursive($value, $security);
            } elseif (is_string($value)) {
                $data[$key] = $security->xss_clean($value);
            }
            // Non-string, non-array values are left as-is (integers, booleans, etc.)
        }

        return $data;
    }
}
