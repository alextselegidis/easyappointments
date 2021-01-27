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
 * Class Migration_Create_appointment_location_column
 *
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Create_appointment_bg_color_column extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        $this->db->query('
            ALTER TABLE `' . $this->db->dbprefix('appointments') . '`
                ADD COLUMN `bg_color` TEXT AFTER `location`; 
        ');

    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        $this->db->query('
            ALTER TABLE `' . $this->db->dbprefix('appointments') . '`
                DROP COLUMN `bg_color`; 
        ');

    }
}


