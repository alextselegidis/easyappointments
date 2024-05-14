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

if (!function_exists('is_assoc')) {
    /**
     * Check if an array is an associative array.
     *
     * @param array $array
     *
     * @return bool
     */
    function is_assoc(array $array): bool
    {
        if (empty($array)) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}

if (!function_exists('array_find')) {
    /**
     * Find the first array element based on the provided function.
     *
     * @param array $array
     * @param callable $callback
     *
     * @return mixed
     */
    function array_find(array $array, callable $callback): mixed
    {
        if (empty($array)) {
            return null;
        }

        if (!is_callable($callback)) {
            throw new InvalidArgumentException('No filter function provided.');
        }

        return array_values(array_filter($array, $callback))[0] ?? null;
    }
}

if (!function_exists('array_fields')) {
    /**
     * Keep only the provided fields of an array.
     *
     * @param array $array
     * @param array $fields
     *
     * @return array
     */
    function array_fields(array $array, array $fields): array
    {
        return array_filter(
            $array,
            function ($field) use ($fields) {
                return in_array($field, $fields);
            },
            ARRAY_FILTER_USE_KEY,
        );
    }
}
