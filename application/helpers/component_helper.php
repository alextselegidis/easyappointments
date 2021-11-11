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

if ( ! function_exists('component'))
{
    /**
     * Render a component from the "views/components/*.php" directory.
     *
     * Use this helper function to easily include components into your HTML markup.
     *
     * Any loaded template variables will also be available at the component template, but you may also specify
     * additional values by adding values to the $params parameter.
     *
     * Example:
     *
     * echo component('timezones_dropdown', 'class"form-control"');
     *
     * @param string $component Component template file name.
     * @param string $attributes HTML attributes for the parent component element.
     * @param array $params Additional parameters for the component.
     *
     * @return string
     */
    function component(string $component, string $attributes = '', array $params = []): string
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $vars = array_merge($params, [
            'attributes' => $attributes
        ]);

        return $CI->load->view('components/' . $component, $vars, TRUE);
    }
}
