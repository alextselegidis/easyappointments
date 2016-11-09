<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

/**
 * Check if Easy!Appointments is installed.
 *
 * This function will check some factors to determine if Easy!Appointments is
 * installed or not. It is possible that the installation is properly configure
 * without being recognized by this method.
 *
 * Notice: You can add more checks into this file in order to further check the
 * installation state of the application.
 *
 * @return bool Returns whether E!A is installed or not.
 */
function is_ea_installed() {
    $ci =& get_instance();
    return $ci->db->table_exists('ea_users');
}

/**
 * Get the data of a sample service.
 *
 * @return array
 */
function get_sample_service() {
    return array(
        'name' => 'Test Service',
        'duration' => 30,
        'price' => 50.0,
        'currency' => 'Euro',
        'description' => 'This is a test service automatically inserted by the installer.',
        'availabilities_type' => 'flexible', 
        'attendants_number' => 1,
        'id_service_categories' => NULL
    );
}

/**
 * Get the data of a sample provider.
 *
 * @return array
 */
function get_sample_provider() {
    return array(
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@doe.com',
        'phone_number' => '0123456789',
        'services' => array(),
        'settings' => array(
            'username' => 'johndoe',
            'password' => '59fe9d073a9c3c606a7e01e402dca4b49b6aa517bd0fdf940c46cb13a7b63dd0',
            'salt' => 'dc5570debc71fc1fe94b1b0aee444fcde5b8cb83d62a6a2b736ead6557d7a2e1',
            'working_plan' => '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"saturday":null,"sunday":null}',
            'notifications' => FALSE,
            'google_sync' => FALSE,
            'sync_past_days' => 5,
            'sync_future_days' => 5,
            'calendar_view' => CALENDAR_VIEW_DEFAULT
        )
    );
}
