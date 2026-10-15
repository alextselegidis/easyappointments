<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Validate a date time value.
 *
 * @param string $value Validation value.
 *
 * @return bool Returns the validation result.
 */
function validate_datetime(string $value): bool
{
    $date_time = DateTime::createFromFormat('Y-m-d H:i:s', $value);

    return $date_time && $date_time->format('Y-m-d H:i:s') === $value;
}

/**
 * Validate a hex color value (e.g. "#7cbae8" or "#fff").
 *
 * @param string $value Validation value.
 *
 * @return bool Returns the validation result.
 */
function validate_color(string $value): bool
{
    return (bool) preg_match('/^#[0-9A-Fa-f]{3}([0-9A-Fa-f]{3})?$/', $value);
}
