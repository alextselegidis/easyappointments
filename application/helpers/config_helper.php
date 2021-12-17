<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
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
 * @param mixed $default Default value in case the requested config has no value.
 *
 * @return mixed|NULL Returns the requested value or NULL if you assign a new configuration value.
 *
 * @throws InvalidArgumentException
 */
function config($key, $default = NULL)
{
    /** @var EA_Controller $CI */
    $CI = &get_instance();

    if (empty($key))
    {
        throw new InvalidArgumentException('The $key argument cannot be empty.');
    }

    if (is_array($key))
    {
        foreach ($key as $item => $value)
        {
            $CI->config->set_item($item, $value);
        }

        return NULL;
    }

    $value = $CI->config->item($key);

    return $value ?? $default;
}

if ( ! function_exists('js_config'))
{
    /**
     * Get / set the specified JS config value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * Example "Get":
     *
     * $version = js_config('version', '1.0.0');
     *
     * Example "Set":
     *
     * js_config(['version' => '1.0.0']);
     *
     * @param array|string $key Configuration key.
     * @param mixed $default Default value in case the requested config has no value.
     *
     * @return mixed|NULL Returns the requested value or NULL if you assign a new configuration value.
     *
     * @throws InvalidArgumentException
     */
    function js_config($key = NULL, $default = NULL)
    {
        $js_config = config('js_config', []);

        if (empty($key))
        {
            return $js_config;
        }

        if (is_array($key))
        {
            foreach ($key as $item => $value)
            {
                $js_config[$item] = $value;
            }

            config(['js_config' => $js_config]);

            return NULL;
        }

        $value = $js_config[$key] ?? NULL;

        return $value ?? $default;
    }
}

if ( ! function_exists('page_vars'))
{
    /**
     * Get / set the specified JS config value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * Example "Get":
     *
     * $version = page_vars('title', 'Default Title');
     *
     * Example "Set":
     *
     * page_vars(['title' => 'Test Title']);
     *
     * @param array|string $key Configuration key.
     * @param mixed $default Default value in case the requested config has no value.
     *
     * @return mixed|NULL Returns the requested value or NULL if you assign a new configuration value.
     *
     * @throws InvalidArgumentException
     */
    function page_vars($key = NULL, $default = NULL)
    {
        $page_vars = config('page_vars', []);

        if (empty($key))
        {
            return $page_vars;
        }

        if (is_array($key))
        {
            foreach ($key as $item => $value)
            {
                $page_vars[$item] = $value;
            }

            config(['page_vars' => $page_vars]);

            return NULL;
        }

        $value = $page_vars[$key] ?? NULL;

        return $value ?? $default;
    }
}
