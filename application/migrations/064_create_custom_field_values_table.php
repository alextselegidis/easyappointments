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

class Migration_Create_custom_field_values_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->table_exists('custom_field_values')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                ],
                'id_custom_fields' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                ],
                'id_users' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                ],
                'value' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'create_datetime' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'update_datetime' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('id_custom_fields');
            $this->dbforge->add_key('id_users');
            $this->dbforge->create_table('custom_field_values', true, ['engine' => 'InnoDB']);

            // Add foreign key constraints
            $this->db->query(
                'ALTER TABLE `' .
                    $this->db->dbprefix('custom_field_values') .
                    '` ADD CONSTRAINT `custom_field_values_custom_fields` FOREIGN KEY (`id_custom_fields`) REFERENCES `' .
                    $this->db->dbprefix('custom_fields') .
                    '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
            );

            $this->db->query(
                'ALTER TABLE `' .
                    $this->db->dbprefix('custom_field_values') .
                    '` ADD CONSTRAINT `custom_field_values_users` FOREIGN KEY (`id_users`) REFERENCES `' .
                    $this->db->dbprefix('users') .
                    '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
            );

            // Add unique constraint to prevent duplicate field values for same user
            $this->db->query(
                'ALTER TABLE `' .
                    $this->db->dbprefix('custom_field_values') .
                    '` ADD UNIQUE KEY `unique_user_field` (`id_users`, `id_custom_fields`)',
            );
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->table_exists('custom_field_values')) {
            $this->db->query(
                'ALTER TABLE `' .
                    $this->db->dbprefix('custom_field_values') .
                    '` DROP FOREIGN KEY `custom_field_values_custom_fields`',
            );
            $this->db->query(
                'ALTER TABLE `' .
                    $this->db->dbprefix('custom_field_values') .
                    '` DROP FOREIGN KEY `custom_field_values_users`',
            );
            $this->dbforge->drop_table('custom_field_values');
        }
    }
}
