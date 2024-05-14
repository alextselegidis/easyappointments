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

class Migration_Add_caldav_columns_to_user_settings_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up()
    {
        if (!$this->db->field_exists('caldav_sync', 'user_settings')) {
            $fields = [
                'caldav_sync' => [
                    'type' => 'TINYINT',
                    'constraint' => '4',
                    'default' => false,
                    'after' => 'google_calendar',
                ],
            ];

            $this->dbforge->add_column('user_settings', $fields);
        }

        if (!$this->db->field_exists('caldav_url', 'user_settings')) {
            $fields = [
                'caldav_url' => [
                    'type' => 'VARCHAR',
                    'constraint' => '512',
                    'null' => true,
                    'after' => 'caldav_sync',
                ],
            ];

            $this->dbforge->add_column('user_settings', $fields);
        }

        if (!$this->db->field_exists('caldav_username', 'user_settings')) {
            $fields = [
                'caldav_username' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'null' => true,
                    'after' => 'caldav_url',
                ],
            ];

            $this->dbforge->add_column('user_settings', $fields);
        }

        if (!$this->db->field_exists('caldav_password', 'user_settings')) {
            $fields = [
                'caldav_password' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'null' => true,
                    'after' => 'caldav_username',
                ],
            ];

            $this->dbforge->add_column('user_settings', $fields);
        }

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

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->field_exists('caldav_sync', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'caldav_sync');
        }

        if ($this->db->field_exists('caldav_url', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'caldav_url');
        }

        if ($this->db->field_exists('caldav_username', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'caldav_username');
        }

        if ($this->db->field_exists('caldav_password', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'caldav_password');
        }

        if ($this->db->field_exists('caldav_calendar', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'caldav_calendar');
        }
    }
}
