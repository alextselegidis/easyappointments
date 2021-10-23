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
 * Example "Get":
 *
 * config('version', '1.0.0');
 *
 * Example "Set":
 *
 * config(['version' => '1.0.0']);
 *
 * @param array|string $key Configuration key.
 * @param mixed $default Default value in case the requested config has no value.
 *
 * @return mixed|NULL Returns the configuration value or NULL if setting the configuration key.
 *
 * @throws InvalidArgumentException
 */
function config($key, $default = NULL)
{
    /** @var EA_Controller $CI */
    $CI = &get_instance();

    if (is_null($key))
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
