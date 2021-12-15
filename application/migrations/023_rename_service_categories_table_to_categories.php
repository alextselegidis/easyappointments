<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

class Migration_Rename_service_categories_table_to_categories extends EA_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ($this->db->table_exists('service_categories'))
        {
            $this->dbforge->rename_table('service_categories', 'categories');
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->table_exists('categories'))
        {
            $this->dbforge->rename_table('categories', 'service_categories');
        }
    }
}
