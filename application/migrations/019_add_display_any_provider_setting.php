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
 * Class Migration_Add_display_any_provider_setting
 *
 * @property CI_Loader load
 * @property CI_DB_query_builder db
 * @property CI_DB_forge dbforge
 * @property Settings_Model settings_model
 */
class Migration_Add_display_any_provider_setting extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        $this->db->insert('ea_settings', [
            'name' => 'display_any_provider',
            'value' => '1'
        ]);
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        $this->db->delete('ea_settings', [
            'name' => 'display_any_provider'
        ]);
    }
}
