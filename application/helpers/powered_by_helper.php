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

/**
 * Return Powered by.
 *
 * @return string
 */
function powered_by($company_name = 'Easy!Appointments', $company_link = 'https://easyappointments.org')
{
    $return =
        'Powered by
        <a href="https://easyappointments.org">Easy!Appointments</a>';
    if ($company_name && $company_name != 'Easy!Appointments' && $company_link) {
        $return .= ' | ' .
            '<a href="' . $company_link . '" style="text-decoration: none;">' . $company_name . '</a>';
    }
    return $return;
}
