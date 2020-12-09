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
 * Class Migration_Modify_sync_period_columns
 *
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Modify_sync_period_columns extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        $fields = [
            'sync_past_days' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
                'default' => '30'
            ],
            'sync_future_days' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
                'default' => '90'
            ],
        ];

        $this->dbforge->modify_column('user_settings', $fields);

        $this->db->update(
            'user_settings',
            [
                'sync_past_days' => '30'
            ],
            [
                'sync_past_days' => '5'
            ]
        );

        $this->db->update(
            'user_settings',
            [
                'sync_future_days' => '90'
            ],
            [
                'sync_future_days' => '5'
            ]
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
                'null' => TRUE,
                'default' => '5'
            ],
            'sync_future_days' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
                'default' => '5'
            ],
        ];

        $this->dbforge->modify_column('user_settings', $fields);

        $this->db->update(
            'user_settings',
            [
                'sync_past_days' => '5'
            ],
            [
                'sync_past_days' => '30'
            ]
        );

        $this->db->update(
            'user_settings',
            [
                'sync_future_days' => '5'
            ],
            [
                'sync_future_days' => '90'
            ]
        );
    }
}
