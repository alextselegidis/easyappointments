<?php defined('BASEPATH') OR exit('No direct script access allowed');

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
 * Assets URL helper function.
 *
 * This function will create an asset file URL that includes a cache busting parameter in order
 * to invalidate the browser cache in case of an update.
 *
 * @param string $uri Relative URI (just like the one used in the base_url helper).
 * @param string|null $protocol Valid URI protocol.
 *
 * @return string Returns the final asset URL.
 */
function asset_url($uri = '', $protocol = NULL)
{
    $ci =& get_instance();

    $cache_busting_token = ! Config::DEBUG_MODE ? '?' . $ci->config->item('cache_busting_token') : '';

    if (strpos(basename($uri), '.js') !== FALSE && strpos(basename($uri), '.min.js') === FALSE && ! Config::DEBUG_MODE)
    {
        $uri = str_replace('.js', '.min.js', $uri);
    }

    if (strpos(basename($uri), '.css') !== FALSE && strpos(basename($uri), '.min.css') === FALSE && ! Config::DEBUG_MODE)
    {
        $uri = str_replace('.css', '.min.css', $uri);
    }

    return base_url($uri . $cache_busting_token, $protocol);
}
