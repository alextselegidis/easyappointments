<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Car2dude - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.0
 * ---------------------------------------------------------------------------- */

/**
 * Class Migration_Change_column_price_type
 *
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Change_column_price_type extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        // Drop table constraints.
        // ALTER TABLE `ea_services` CHANGE `price` `price` VARCHAR(256)  NULL  DEFAULT NULL;
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services') . ' CHANGE `price` `price` VARCHAR(256)  NULL  DEFAULT NULL');
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        // Drop table constraints.        
    }
}
