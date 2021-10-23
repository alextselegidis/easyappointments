<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

if ( ! function_exists('storage_path'))
{
    /**
     * Get the path to the storage folder.
     *
     * @param string $path
     *
     * @return string
     */
    function storage_path(string $path = ''): string
    {
        return FCPATH . 'storage/' . trim($path);
    }
}

if ( ! function_exists('base_path'))
{
    /**
     * Get the path to the base of the current installation.
     *
     * @param string $path
     *
     * @return string
     */
    function base_path(string $path = ''): string
    {
        return FCPATH . trim($path);
    }
}

