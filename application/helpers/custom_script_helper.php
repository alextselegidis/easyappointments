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

/**
 * Print Google Analytics script.
 *
 * This helper function should be used in view files in order to output the Google Analytics script. It will check
 * whether the code is set in the database and print it, otherwise nothing will be outputted. This eliminates the need
 * for extra checking before outputting.
 */
function custom_script()
{
    $CI =& get_instance();

    $CI->load->model('settings_model');

    $custom_script = $CI->settings_model->get_setting('custom_script');

    if ($custom_script !== '') {
        echo $custom_script;
    }
}
