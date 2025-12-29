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

class Migration_Add_white_label_settings extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->get_where('settings', ['name' => 'app_name'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'app_name',
                'value' => 'EASY!APPOINTMENTS',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'app_tagline'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'app_tagline',
                'value' => 'Online Appointment Scheduler',
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'app_url'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'app_url',
                'value' => 'https://easyappointments.org',
            ]);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->get_where('settings', ['name' => 'app_name'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'app_name']);
        }

        if ($this->db->get_where('settings', ['name' => 'app_tagline'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'app_tagline']);
        }

        if ($this->db->get_where('settings', ['name' => 'app_url'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'app_url']);
        }
    }
}
