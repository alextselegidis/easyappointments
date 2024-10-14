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

class Migration_Drop_caldav_calendar_column_from_user_settings_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ($this->db->field_exists('caldav_calendar', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'caldav_calendar');
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if (!$this->db->field_exists('caldav_calendar', 'user_settings')) {
            $fields = [
                'caldav_calendar' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'null' => true,
                    'after' => 'caldav_password',
                ],
            ];

            $this->dbforge->add_column('user_settings', $fields);
        }
    }
}
