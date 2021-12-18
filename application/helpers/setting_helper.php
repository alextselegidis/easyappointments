<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

if ( ! function_exists('setting'))
{
    /**
     * Get / set the specified setting value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * Example "Get":
     *
     * $company_name = session('company_name', FALSE);
     *
     * Example "Set":
     *
     * setting(['company_name' => 'ACME Inc']);
     *
     * @param array|string $key Setting key.
     * @param mixed $default Default value in case the requested setting has no value.
     *
     * @return mixed|NULL Returns the requested value or NULL if you assign a new setting value.
     *
     * @throws InvalidArgumentException
     */
    function setting($key = NULL, $default = NULL)
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $CI->load->model('settings_model');

        if (empty($key))
        {
            throw new InvalidArgumentException('The $key argument cannot be empty.');
        }

        if (is_array($key))
        {
            foreach ($key as $name => $value)
            {
                $setting = $CI->settings_model->query()->where('name', $name)->get()->row_array();

                if (empty($setting))
                {
                    $setting = [
                        'name' => $name,
                        'value' => $value,
                    ];
                }

                $CI->settings_model->save($setting);
            }

            return NULL;
        }

        $setting = $CI->settings_model->query()->where('name', $key)->get()->row_array();

        return $setting['value'] ?? $default;
    }
}
