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

    return (bool) $date_time;
}

/**
 * Validate an image data URL: a base64 encoded PNG, JPEG, GIF or WebP image.
 *
 * Such a value carries no markup, so it can be stored as sent. SVG is left out on purpose, because it can carry
 * scripts.
 *
 * @param string $value Validation value.
 *
 * @return bool Returns the validation result.
 */
function validate_image_data_url(string $value): bool
{
    return (bool) preg_match('#^data:image/(png|jpe?g|gif|webp);base64,[A-Za-z0-9+/]+={0,2}$#', $value);
}
