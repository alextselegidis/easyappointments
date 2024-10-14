<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.3.0
 * ---------------------------------------------------------------------------- */

/**
 * Get / set the specified config value.
 *
 * If an array is passed as the key, we will assume you want to set an array of values.
 *
 * Example "Get":
 *
 * $version = config('version', '1.0.0');
 *
 * Example "Set":
 *
 * config(['version' => '1.0.0']);
 *
 * @param array|string $key Configuration key.
 * @param mixed|null $default Default value in case the requested config has no value.
 *
 * @return mixed|NULL Returns the requested value or NULL if you assign a new configuration value.
 *
 * @throws InvalidArgumentException
 */
function config(array|string $key, mixed $default = null): mixed
{
    /** @var EA_Controller $CI */
    $CI = &get_instance();

    if (empty($key)) {
        throw new InvalidArgumentException('The $key argument cannot be empty.');
    }

    if (is_array($key)) {
        foreach ($key as $item => $value) {
            $CI->config->set_item($item, $value);
        }

        return null;
    }

    $value = $CI->config->item($key);

    return $value ?? $default;
}

if (!function_exists('script_vars')) {
    /**
     * Get / set the specified JS config value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * Example "Get":
     *
     * $version = script_vars('version', '1.0.0');
     *
     * Example "Set":
     *
     * script_vars(['version' => '1.0.0']);
     *
     * @param array|string|null $key Configuration key.
     * @param mixed|null $default Default value in case the requested config has no value.
     *
     * @return mixed|NULL Returns the requested value or NULL if you assign a new configuration value.
     *
     * @throws InvalidArgumentException
     */
    function script_vars(array|string $key = null, mixed $default = null): mixed
    {
        $script_vars = config('script_vars', []);

        if (empty($key)) {
            return $script_vars;
        }

        if (is_array($key)) {
            foreach ($key as $item => $value) {
                $script_vars[$item] = $value;
            }

            config(['script_vars' => $script_vars]);

            return null;
        }

        $value = $script_vars[$key] ?? null;

        return $value ?? $default;
    }
}

if (!function_exists('html_vars')) {
    /**
     * Get / set the specified HTML variable.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * Example "Get":
     *
     * $version = html_vars('title', 'Default Title');
     *
     * Example "Set":
     *
     * html_vars(['title' => 'Test Title']);
     *
     * @param array|string|null $key Variable key.
     * @param mixed|null $default Default value in case the requested variable has no value.
     *
     * @return mixed|NULL Returns the requested value or NULL if you assign a new configuration value.
     *
     * @throws InvalidArgumentException
     */
    function html_vars(array|string $key = null, mixed $default = null): mixed
    {
        $html_vars = config('html_vars', []);

        if (empty($key)) {
            return $html_vars;
        }

        if (is_array($key)) {
            foreach ($key as $item => $value) {
                $html_vars[$item] = $value;
            }

            config(['html_vars' => $html_vars]);

            return null;
        }

        $value = $html_vars[$key] ?? null;

        return $value ?? $default;
    }
}

if (!function_exists('vars')) {
    /**
     * Get / set the specified HTML & JS config value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * Example "Get":
     *
     * $version = vars('version', '1.0.0');
     *
     * Example "Set":
     *
     * vars(['version' => '1.0.0']);
     *
     * @param array|string|null $key Configuration key.
     * @param mixed|null $default Default value in case the requested config has no value.
     *
     * @return mixed|NULL Returns the requested value or NULL if you assign a new configuration value.
     *
     * @throws InvalidArgumentException
     */
    function vars(array|string $key = null, mixed $default = null): mixed
    {
        return html_vars($key) ?? (script_vars($key) ?? $default);
    }
}
