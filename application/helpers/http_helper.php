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

if (!function_exists('request')) {
    /**
     * Gets the value of a request variable.
     *
     * Example:
     *
     * $first_name = request('first_name', 'John');
     *
     * @param string|null $key Request variable key.
     * @param mixed|null $default Default value in case the requested variable has no value.
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    function request(?string $key = null, $default = null): mixed
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        if (empty($key)) {
            $payload = $CI->input->post_get($key);

            if (empty($payload)) {
                $payload = $CI->input->json($key);
            }

            return $payload;
        }

        return $CI->input->post_get($key) ?? ($CI->input->json($key) ?? $default);
    }
}

if (!function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * Example:
     *
     * response('This is the response content', 200, []);
     *
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    function response(string $content = '', int $status = 200, array $headers = []): void
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        foreach ($headers as $header) {
            $CI->output->set_header($header);
        }

        $CI->output->set_status_header($status)->set_output($content);
    }
}

if (!function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    function response(string $content = '', int $status = 200, array $headers = []): void
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        foreach ($headers as $header) {
            $CI->output->set_header($header);
        }

        $CI->output->set_status_header($status)->set_output($content);
    }
}

if (!function_exists('json_response')) {
    /**
     * Return a new response from the application.
     *
     * Example:
     *
     * json_response([
     *  'message' => 'This is a JSON property.'
     * ]);
     *
     * @param array $content
     * @param int $status
     * @param array $headers
     */
    function json_response(array $content = [], int $status = 200, array $headers = []): void
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        foreach ($headers as $header) {
            $CI->output->set_header($header);
        }

        $CI->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($content));
    }
}

if (!function_exists('json_exception')) {
    /**
     * Return a new json exception from the application.
     *
     * Example:
     *
     * json_exception($exception); // Add this in a catch block to return the exception information.
     *
     * @param Throwable $e
     */
    function json_exception(Throwable $e): void
    {
        // Get the error message
        $message = $e->getMessage();

        // In production, sanitize certain error messages that might expose sensitive info
        $sensitive_patterns = [
            '/database/i',
            '/sql/i',
            '/query/i',
            '/mysql/i',
            '/postgres/i',
            '/connection/i',
            '/password/i',
            '/credential/i',
            '/api[_\s]?key/i',
            '/secret/i',
            '/token/i',
            '/\\bpath\\b/i',
            '/file[_\s]?system/i',
        ];

        $is_sensitive = false;
        foreach ($sensitive_patterns as $pattern) {
            if (preg_match($pattern, $message)) {
                $is_sensitive = true;
                break;
            }
        }

        // If the error contains sensitive information, use a generic message
        // Allow InvalidArgumentException and RuntimeException with safe messages through
        if ($is_sensitive && !($e instanceof InvalidArgumentException)) {
            $message = 'An error occurred while processing your request. Please try again later.';
        }

        $response = [
            'success' => false,
            'message' => $message,
            'trace' => trace($e),
        ];

        log_message('error', 'JSON exception: ' . json_encode($response));

        unset($response['trace']); // Do not send the trace to the browser as it might contain sensitive info

        json_response($response, 500);
    }
}

if (!function_exists('abort')) {
    /**
     * Throw an HttpException with the given data.
     *
     * Example:
     *
     * if ($error) abort(500);
     *
     * @param int $code
     * @param string $message
     * @param array $headers
     *
     * @return void
     */
    function abort(int $code, string $message = '', array $headers = []): void
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        foreach ($headers as $header) {
            $CI->output->set_header($header);
        }

        show_error($message, $code);
    }
}

if (!function_exists('trace')) {
    /**
     * Prepare a well formatted string for an exception
     *
     * @param Throwable $e
     *
     * @return string
     */
    function trace(Throwable $e): string
    {
        $trace = $e->getTrace();

        $filtered_trace = array_map(function ($entry) {
            return array_filter(
                $entry,
                function ($key) {
                    return $key !== 'object'; // Exclude object data
                },
                ARRAY_FILTER_USE_KEY,
            );
        }, $trace);

        return var_export($filtered_trace, true);
    }
}

if (!function_exists('method')) {
    /**
     * Ensure the HTTP request method matches the expected method.
     *
     * Example:
     *
     * method('POST'); // Throws an exception if the request method is not POST
     *
     * @param string $expected_method The expected HTTP method (GET, POST, PUT, DELETE, etc.)
     *
     * @return void
     *
     * @throws RuntimeException
     */
    function method(string $expected_method): void
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $current_method = $CI->input->method();

        if (strtoupper($current_method) !== strtoupper($expected_method)) {
            throw new RuntimeException("Method not allowed. Expected {$expected_method}, got {$current_method}.");
        }
    }
}

if (!function_exists('check')) {
    /**
     * Validate a request parameter against expected types.
     *
     * Supported types:
     * - numeric: Checks if the value is numeric (integer or float string)
     * - integer: Checks if the value is an integer
     * - string: Checks if the value is a string
     * - bool: Checks if the value is a boolean or boolean-like ('true', 'false', '0', '1')
     * - array: Checks if the value is an array
     * - email: Checks if the value is a valid email
     * - date: Checks if the value is a valid date
     * - null: Allows null/empty values
     *
     * Multiple types can be specified using pipe separator (e.g., 'numeric|null')
     *
     * Example:
     *
     * check('customer_id', 'numeric');
     * check('customer_id', 'numeric|null');
     * check('email', 'email|null');
     *
     * @param string $key The request parameter key to validate
     * @param string $types Pipe-separated list of allowed types
     *
     * @return void
     *
     * @throws InvalidArgumentException If validation fails
     */
    function check(string $key, string $types): void
    {
        $value = request($key);
        $allowed_types = explode('|', $types);

        // Check if value is null/empty and null is allowed
        if (($value === null || $value === '') && in_array('null', $allowed_types)) {
            return;
        }

        // If value is null/empty and null is not allowed, throw error
        if (($value === null || $value === '') && !in_array('null', $allowed_types)) {
            throw new InvalidArgumentException("Request parameter '{$key}' is required and cannot be empty.");
        }

        $valid = false;

        foreach ($allowed_types as $type) {
            $type = trim($type);

            switch ($type) {
                case 'numeric':
                    if (is_numeric($value)) {
                        $valid = true;
                    }
                    break;

                case 'integer':
                    if (is_numeric($value) && (int) $value == $value) {
                        $valid = true;
                    }
                    break;

                case 'string':
                    if (is_string($value)) {
                        $valid = true;
                    }
                    break;

                case 'bool':
                case 'boolean':
                    if (
                        is_bool($value) ||
                        in_array(strtolower((string) $value), ['true', 'false', '0', '1', 'yes', 'no'], true)
                    ) {
                        $valid = true;
                    }
                    break;

                case 'array':
                    if (is_array($value)) {
                        $valid = true;
                    }
                    break;

                case 'email':
                    if (filter_var($value, FILTER_VALIDATE_EMAIL) !== false) {
                        $valid = true;
                    }
                    break;

                case 'date':
                    if (strtotime($value) !== false) {
                        $valid = true;
                    }
                    break;

                case 'json':
                    if (is_string($value) && json_decode($value) !== null) {
                        $valid = true;
                    }
                    break;

                case 'null':
                    // Already handled above
                    break;

                default:
                    throw new InvalidArgumentException("Unknown validation type '{$type}' for parameter '{$key}'.");
            }

            if ($valid) {
                break;
            }
        }

        if (!$valid) {
            $readable_types = implode(' or ', array_filter($allowed_types, fn($t) => $t !== 'null'));
            throw new InvalidArgumentException("Request parameter '{$key}' must be of type {$readable_types}.");
        }
    }
}
