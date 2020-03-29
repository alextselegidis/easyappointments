<?php defined('BASEPATH') OR exit('No direct script access allowed');

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

class Migration_Add_add_timezone_columns extends CI_Migration {
    public function up()
    {
        $this->db->query('
            ALTER TABLE `ea_appointments`
                ADD `timezone` VARCHAR(256) NULL AFTER `end_datetime`;
        ');

        $this->db->query('
            ALTER TABLE `ea_users`
                ADD `timezone` VARCHAR(256) NULL AFTER `notes`;
        ');
    }

    public function down()
    {
        $this->db->query('
            ALTER TABLE `ea_appointments`
                DROP COLUMN `timezone`;
        ');

        $this->db->query('
            ALTER TABLE `ea_users`
                DROP COLUMN `timezone`;
        ');
    }
}
