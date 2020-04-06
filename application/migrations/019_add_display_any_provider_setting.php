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

class Migration_Add_display_any_provider_setting extends CI_Migration {
    public function up()
    {
        $this->db->insert('ea_settings', [
            'name' => 'display_any_provider',
            'value' => '1'
        ]);
    }

    public function down()
    {
        $this->db->delete('ea_settings', [
            'name' => 'display_any_provider'
        ]);
    }
}
