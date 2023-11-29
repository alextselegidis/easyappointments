<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

class Migration_Revert_rename_service_categories_table_to_categories extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ($this->db->table_exists('categories')) {
            $this->dbforge->rename_table('categories', 'service_categories');
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->table_exists('service_categories')) {
            $this->dbforge->rename_table('service_categories', 'categories');
        }
    }
}
