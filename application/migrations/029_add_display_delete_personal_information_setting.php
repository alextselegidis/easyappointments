<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_display_delete_personal_information_setting extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up()
    {
        if (!$this->db->get_where('settings', ['name' => 'display_delete_personal_information'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'display_delete_personal_information',
                'value' => '0',
            ]);
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->get_where('settings', ['name' => 'display_delete_personal_information'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'display_delete_personal_information']);
        }
    }
}
