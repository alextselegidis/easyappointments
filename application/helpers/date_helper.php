<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

if (!function_exists('get_date_format')) {
    /**
     * Get the date format based on the current settings.
     *
     * @return string
     */
    function get_date_format(): string
    {
        $date_format = setting('date_format');

        return match ($date_format) {
            'DMY' => 'd/m/Y',
            'MDY' => 'm/d/Y',
            'YMD' => 'Y/m/d',
            default => throw new RuntimeException('Invalid date format value: ' . $date_format),
        };
    }
}

if (!function_exists('get_time_format')) {
    /**
     * Get the time format based on the current settings.
     *
     * @return string
     */
    function get_time_format(): string
    {
        $time_format = setting('time_format');

        return match ($time_format) {
            'military' => 'H:i',
            'regular' => 'g:i a',
            default => throw new RuntimeException('Invalid time format value: ' . $time_format),
        };
    }
}

if (!function_exists('get_date_time_format')) {
    /**
     * Get the date-time format based on the current settings.
     *
     * @return string
     */
    function get_date_time_format(): string
    {
        return get_date_format() . ' ' . get_time_format();
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date string based on the current app settings.
     *
     * @param DateTimeInterface|string $value
     *
     * @return string
     *
     * @throws Exception
     */
    function format_date(DateTimeInterface|string $value): string
    {
        try {
            $value_date_time = $value;

            if (is_string($value_date_time)) {
                $value_date_time = new DateTime($value);
            }

            return $value_date_time->format(get_date_format());
        } catch (Exception $e) {
            log_message('error', 'Invalid date provided to the "format_date" helper function: ' . $e->getMessage());

            return 'Invalid Date';
        }
    }
}

if (!function_exists('format_time')) {
    /**
     * Format a time string based on the current app settings.
     *
     * @param DateTimeInterface|string $value
     *
     * @return string
     *
     * @throws Exception
     */
    function format_time(DateTimeInterface|string $value): string
    {
        try {
            $value_date_time = $value;

            if (is_string($value_date_time)) {
                $value_date_time = new DateTime($value);
            }

            return $value_date_time->format(get_time_format());
        } catch (Exception $e) {
            log_message('error', 'Invalid date provided to the format_time helper function: ' . $e->getMessage());

            return 'Invalid Time';
        }
    }
}

if (!function_exists('format_date_time')) {
    /**
     * Format a time string based on the current app settings.
     *
     * @param DateTimeInterface|string $value
     *
     * @return string
     */
    function format_date_time(DateTimeInterface|string $value): string
    {
        try {
            $value_date_time = $value;

            if (is_string($value_date_time)) {
                $value_date_time = new DateTime($value);
            }

            return $value_date_time->format(get_date_time_format());
        } catch (Exception $e) {
            log_message('error', 'Invalid date provided to the format_date_time helper function: ' . $e->getMessage());

            return 'Invalid Date-Time';
        }
    }
}

if (!function_exists('format_timezone')) {
    /**
     * Format a timezone string based on the current app settings.
     *
     * @param string $value
     *
     * @return string
     */
    function format_timezone(string $value): string
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $CI->load->library('timezones');

        return $CI->timezones->get_timezone_name($value);
    }
}
