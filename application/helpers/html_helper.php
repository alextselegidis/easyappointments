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
     */
    function component(string $component, string $attributes = '', array $params = [])
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $vars = array_merge($params, [
            'attributes' => $attributes
        ]);

        echo $CI->load->view('components/' . $component, $vars, true);
    }
}

if ( ! function_exists('extend'))
{
    /**
     * Use this function at the top of view files to mark the layout you are extending from.
     *
     * @param $layout
     */
    function extend($layout)
    {
        config([
            'layout' => [
                'filename' => $layout,
                'sections' => [],
            ]
        ]);
    }
}

if ( ! function_exists('section'))
{
    /**
     * Use this function in view files to mark the beginning and/or end of a layout section.
     *
     * Sections will only be used if the view file extends a layout and will be ignored otherwise.
     *
     * Example:
     *
     * <?php section('content') ?>
     *
     *   <!-- Section Starts -->
     *
     *   <p>This is the content of the section.</p>
     *
     *   <!-- Section Ends -->
     *
     * <?php section('content') ?>
     *
     * @param string $name
     */
    function section(string $name)
    {
        $layout = config('layout');

        if (array_key_exists($name, $layout['sections']))
        {
            $layout['sections'][$name] = ob_get_clean();

            config(['layout' => $layout]);

            return;
        }

        $layout['sections'][$name] = '';

        config(['layout' => $layout]);

        ob_start();
    }
}

if ( ! function_exists('slot'))
{
    /**
     * Use this function in view files to mark a slot that sections can populate from within child templates.
     *
     * @param string $name
     */
    function slot(string $name)
    {
        $layout = config('layout');

        echo $layout['sections'][$name];
    }
}
