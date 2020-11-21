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
class Migration_Create_appointment_location_column extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        $this->db->query('
            ALTER TABLE `' . $this->db->dbprefix('appointments') . '`
                ADD COLUMN `location` TEXT AFTER `end_datetime`; 
        ');

        $this->db->query('
            ALTER TABLE `' . $this->db->dbprefix('services') . '`
                ADD COLUMN `location` TEXT AFTER `description`; 
        ');
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        $this->db->query('
            ALTER TABLE `' . $this->db->dbprefix('appointments') . '`
                DROP COLUMN `location`; 
        ');

        $this->db->query('
            ALTER TABLE `' . $this->db->dbprefix('services') . '`
                DROP COLUMN `location`; 
        ');
    }
}
