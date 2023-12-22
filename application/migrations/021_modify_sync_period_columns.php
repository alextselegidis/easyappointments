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

class Migration_Modify_sync_period_columns extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up()
    {
        $fields = [
            'sync_past_days' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
                'default' => '30',
            ],
            'sync_future_days' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
                'default' => '90',
            ],
        ];

        $this->dbforge->modify_column('user_settings', $fields);

        $this->db->update(
            'user_settings',
            [
                'sync_past_days' => '30',
            ],
            [
                'sync_past_days' => '5',
            ],
        );

        $this->db->update(
            'user_settings',
            [
                'sync_future_days' => '90',
            ],
            [
                'sync_future_days' => '5',
            ],
        );
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        $fields = [
            'sync_past_days' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
                'default' => '5',
            ],
            'sync_future_days' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
                'default' => '5',
            ],
        ];

        $this->dbforge->modify_column('user_settings', $fields);

        $this->db->update(
            'user_settings',
            [
                'sync_past_days' => '5',
            ],
            [
                'sync_past_days' => '30',
            ],
        );

        $this->db->update(
            'user_settings',
            [
                'sync_future_days' => '5',
            ],
            [
                'sync_future_days' => '90',
            ],
        );
    }
}
