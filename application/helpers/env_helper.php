<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

if ( ! function_exists('env'))
{
    /**
     * Gets the value of an environment variable.
     *
     * Example:
     *
     * $debug = env('debug', FALSE);
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    function env(string $key, $default = NULL)
    {
        if (empty($key))
        {
            throw new InvalidArgumentException('The $key argument cannot be empty.');
        }

        return $_ENV[$key] ?? $default;
    }
}
