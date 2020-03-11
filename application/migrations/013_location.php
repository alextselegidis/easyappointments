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

class Migration_location extends CI_Migration {
    public function up()
    {
        $this->db->query('
            ALTER TABLE `ea_appointments`
                ADD COLUMN `location` TEXT AFTER `end_datetime`; 
        ');

        $this->db->query('
            ALTER TABLE `ea_services`
                ADD COLUMN `location` TEXT AFTER `description`; 
        ');
    }

    public function down()
    {
        $this->db->query('
            ALTER TABLE `ea_appointments`
                DROP COLUMN `location`; 
        ');

        $this->db->query('
            ALTER TABLE `ea_services`
                DROP COLUMN `location`; 
        ');
    }
}
