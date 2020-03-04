<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * JustInClicks.com - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://www.justinclicks.com/
 * @since       v1.3.0
 * ---------------------------------------------------------------------------- */

/**
 * Quickly fetch the value of a framework configuration.
 *
 * @param string $key Configuration key.
 *
 * @return mixed Returns the configuration value.
 */
function config($key)
{
    $framework = &get_instance();

    return $framework->config->item($key);
}
