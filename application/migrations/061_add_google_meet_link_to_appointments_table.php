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

class Migration_Add_google_meet_link_to_appointments_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('google_meet_link', 'appointments')) {
            $this->dbforge->add_column('appointments', [
                'google_meet_link' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'id_google_calendar',
                ],
            ]);
        }

        if (!$this->db->get_where('settings', ['name' => 'google_calendar_add_video_meet'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'google_calendar_add_video_meet',
                'value' => '1',
            ]);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('google_meet_link', 'appointments')) {
            $this->dbforge->drop_column('appointments', 'google_meet_link');
        }

        if ($this->db->get_where('settings', ['name' => 'google_calendar_add_video_meet'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'google_calendar_add_video_meet']);
        }
    }
}
