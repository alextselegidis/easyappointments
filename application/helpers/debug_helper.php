<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

if ( ! function_exists('dd'))
{
    /**
     * Output the provided variables with "var_dump" and stop the execution.
     * 
     * Example:
     * 
     * dd($appointment, $service, $provider, $customer); 
     *
     * @param mixed ...$vars
     */
    function dd(...$vars)
    {
        var_dump($vars);

        exit(1);
    }
}
