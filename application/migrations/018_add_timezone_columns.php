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
 * Class Migration_Add_timezone_columns
 *
 * @property CI_Loader load
 * @property CI_DB_query_builder db
 * @property CI_DB_forge dbforge
 * @property Settings_Model settings_model
 */
class Migration_Add_timezone_columns extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        $this->db->query('
            ALTER TABLE `ea_users`
                ADD `timezone` VARCHAR(256) DEFAULT "UTC" AFTER `notes`;
        ');
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        $this->db->query('
            ALTER TABLE `ea_users`
                DROP COLUMN `timezone`;
        ');
    }
}
