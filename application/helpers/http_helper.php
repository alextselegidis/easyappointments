<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

if ( ! function_exists('request'))
{
    /**
     * Gets the value of a request variable.
     *
     * Example:
     *
     * $first_name = request('first_name', 'John');
     *
     * @param string|null $key Request variable key.
     * @param mixed $default Default value in case the requested variable has no value.
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    function request(string $key, $default = NULL)
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        if (empty($key))
        {
            throw new InvalidArgumentException('The $key argument cannot be empty.');
        }

        return $CI->input->post_get($key) ?? $CI->input->json($key) ?? $default;
    }
}

if ( ! function_exists('response'))
{
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
    function response(string $content = '', int $status = 200, array $headers = [])
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $CI
            ->output
            ->set_output($content);
    }
}

if ( ! function_exists('response'))
{
    /**
     * Return a new response from the application.
     *
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    function response(string $content = '', int $status = 200, array $headers = [])
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        foreach ($headers as $header)
        {
            $CI->output->set_header($header);
        }

        $CI
            ->output
            ->set_status_header($status)
            ->set_output($content);
    }
}

if ( ! function_exists('json_response'))
{
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
    function json_response(array $content = [], int $status = 200, array $headers = [])
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        foreach ($headers as $header)
        {
            $CI->output->set_header($header);
        }

        $CI
            ->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($content));
    }
}

if ( ! function_exists('json_exception'))
{
    /**
     * Return a new json exception from the application.
     *
     * Example:
     *
     * json_exception($exception); // Add this in a catch block to return the exception information.
     *
     * @param Throwable $exception
     */
    function json_exception(Throwable $exception)
    {
        json_response([
            'success' => FALSE,
            'message' => $exception->getMessage(),
            'trace' => config('debug') ? $exception->getTrace() : []
        ], 500);
    }
}


if ( ! function_exists('abort'))
{
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
    function abort(int $code, string $message = '', array $headers = [])
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        foreach ($headers as $header)
        {
            $CI->output->set_header($header);
        }

        show_error($message, $code);
    }
}
