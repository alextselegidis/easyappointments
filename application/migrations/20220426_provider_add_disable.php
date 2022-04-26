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
 * Class Migration_provider_add_disable
 *
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_provider_add_disable extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        // Drop table constraints.
        // /* 7:51:07 PM easyap_localhost easyappointments */ ALTER TABLE `ea_users` ADD `disable` BOOL  NULL  DEFAULT '0'   AFTER `lineUserId`;
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('user') . "` ADD `disable` BOOL  NULL  DEFAULT '0' AFTER `lineUserId`");
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        // Drop table constraints.        
    }
}
