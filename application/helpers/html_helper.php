<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
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
     * @param bool $return Whether to return the HTML or echo it directly.
     *
     * @return string Return the HTML if the $return argument is TRUE or NULL.
     */
    function component(string $component, string $attributes = '', array $params = [], bool $return = FALSE)
    {
        /** @var EA_Controller $CI */
        $CI = get_instance();

        $vars = array_merge($params, [
            'attributes' => $attributes
        ]);

        return $CI->load->view('components/' . $component, $vars, $return);
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
                'tmp' => [],
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

        if (array_key_exists($name, $layout['tmp']))
        {
            $layout['sections'][$name][] = ob_get_clean();

            unset($layout['tmp'][$name]);
            
            config(['layout' => $layout]);

            return;
        }

        if (empty($layout['sections'][$name]))
        {
            $layout['sections'][$name] = [];
        }

        $layout['tmp'][$name] = '';

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

        $section = $layout['sections'][$name] ?? NULL;

        if ( ! $section)
        {
            return;
        }

        foreach ($section as $content)
        {
            echo $content;
        }
    }
}
