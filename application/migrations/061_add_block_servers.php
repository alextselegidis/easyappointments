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


class Migration_Add_block_servers extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->table_exists('caldav_block_servers')) {
            $this->dbforge->add_field( [
                'id' => [
                    'type' => 'INT',
                    'auto_increment' => true
                ],
                'user_id' => [
                    'type' => 'INT',
                    'null' => false
                ],
                'caldav_url' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => false
                ],
                'caldav_username' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => false
                ],
                'caldav_password' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => false
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => false,
                    'default' => 'CURRENT_TIMESTAMP'
                ]
            ]);

            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('user_id');
            $this->dbforge->create_table('caldav_block_servers', true,['engine' => 'InnoDB']);

            $this->db->query('ALTER TABLE ' . $this->db->dbprefix('caldav_block_servers') . ' ADD CONSTRAINT fk_caldav_block_servers_user_id FOREIGN KEY (user_id) REFERENCES ' . $this->db->dbprefix('users') . '(id) ON DELETE CASCADE');
        }

        if (!$this->db->field_exists('read_only', 'appointments')) {
            $fields = [
                'read_only' => [
                    'type' => 'BOOLEAN',
                    'null' => false,
                    'default' => 0
                ]
            ];
            $this->dbforge->add_column('appointments', $fields);
        }

        if (!$this->db->field_exists('id_caldav_block_server', 'appointments')) {
            $fields = [
                'id_caldav_block_server' => [
                    'type' => 'INT',
                    'null' => true,
                    'after' => 'read_only'
                ]
            ];
            $this->dbforge->add_column('appointments', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('id_caldav_block_server', 'appointments')) {
            $this->dbforge->drop_column('appointments', 'id_caldav_block_server');
        }

        if ($this->db->field_exists('read_only', 'appointments')) {
            $this->dbforge->drop_column('appointments', 'read_only');
        }

        if ($this->db->table_exists('caldav_block_servers')) {
            $this->dbforge->drop_table('caldav_block_servers');
        }
    }
}