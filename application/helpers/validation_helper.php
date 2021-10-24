<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
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

    return (bool)$date_time;
}
