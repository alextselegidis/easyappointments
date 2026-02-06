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

class Migration_Add_password_reset_columns_to_user_settings_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('password_reset_token', 'user_settings')) {
            $fields = [
                'password_reset_token' => [
                    'type' => 'VARCHAR',
                    'constraint' => '64',
                    'null' => true,
                    'after' => 'password',
                ],
            ];

            $this->dbforge->add_column('user_settings', $fields);
        }

        if (!$this->db->field_exists('password_reset_expires', 'user_settings')) {
            $fields = [
                'password_reset_expires' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'password_reset_token',
                ],
            ];
            $this->dbforge->add_column('user_settings', $fields);
        }
    }
    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('password_reset_token', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'password_reset_token');
        }

        if ($this->db->field_exists('password_reset_expires', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'password_reset_expires');
        }
    }
}
