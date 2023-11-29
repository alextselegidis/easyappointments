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

if (!function_exists('route_api_resource')) {
    /**
     * Define a route for an API resource (includes index, store, update and delete callbacks).
     *
     * @param array $route Route config.
     * @param string $resource Resource name.
     * @param string $prefix URL prefix (e.g. api/v1/).
     */
    function route_api_resource(array &$route, string $resource, string $prefix = ''): void
    {
        $route[$prefix . $resource]['post'] = 'api/v1/' . $resource . '_api_v1/store';
        $route[$prefix . $resource . '/(:num)']['put'] = 'api/v1/' . $resource . '_api_v1/update/$1';
        $route[$prefix . $resource . '/(:num)']['delete'] = 'api/v1/' . $resource . '_api_v1/destroy/$1';
        $route[$prefix . $resource]['get'] = 'api/v1/' . $resource . '_api_v1/index';
        $route[$prefix . $resource . '/(:num)']['get'] = 'api/v1/' . $resource . '_api_v1/show/$1';
    }
}

if (!function_exists('is_callback')) {
    /**
     * Check whether the current request matches the provided controller/method callback.
     *
     * @param string $class Controller class name.
     * @param string $method Controller method name.
     *
     * @return bool
     */
    function is_callback(string $class, string $method): bool
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        return $CI->router->class === $class && $CI->router->method === $method;
    }
}
