<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Render the HTML output of a timezone dropdown element.
 *
 * @param string $attributes HTML element attributes of the dropdown.
 *
 * @return string
 */
function render_timezone_dropdown(string $attributes = ''): string
{
    $CI = get_instance();

    $CI->load->library('timezones');

    $timezones = $CI->timezones->to_grouped_array();

    return $CI->load->view('components/timezone_dropdown', [
        'timezones' => $timezones,
        'attributes' => $attributes
    ], TRUE);
}

/**
 * Render the language script tag.
 *
 * @param string $attributes HTML element attributes of the script element.
 *
 * @return string
 */
function render_language_script(string $attributes = ''): string
{
    $CI = get_instance();

    return $CI->load->view('components/language_script', [
        'attributes' => $attributes
    ], TRUE);
}

/**
 * Render the global variables script tag.
 *
 * @param string $attributes HTML element attributes of the script element.
 *
 * @return string
 */
function render_global_variables_script(string $attributes = ''): string
{
    $CI = get_instance();

    return $CI->load->view('components/global_variables_script', [
        'attributes' => $attributes
    ], TRUE);
}
