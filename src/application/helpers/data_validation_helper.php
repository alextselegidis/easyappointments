<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * JustInClicks.com - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://www.justinclicks.com/
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Check if a string date is valid for insertion or update
 * to the database.
 *
 * @param string $datetime The given date.
 * @return bool Returns the validation result.
 *
 * @link http://stackoverflow.com/a/8105844/1718162 [SOURCE]
 */
function validate_mysql_datetime($datetime)
{
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    return ($dt) ? TRUE : FALSE;
}
