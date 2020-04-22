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
 * Class Migration_add_appointment_location_column
 *
 * @property CI_Loader load
 * @property CI_DB_query_builder db
 * @property CI_DB_forge dbforge
 * @property Settings_Model settings_model
 */
class Migration_add_appointment_location_column extends CI_Migration {
    /**
     * Upgrade method.
     */
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

    /**
     * Downgrade method.
     */
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
