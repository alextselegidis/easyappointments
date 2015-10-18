<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
