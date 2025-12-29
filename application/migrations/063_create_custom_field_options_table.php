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

class Migration_Create_custom_field_options_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->table_exists('custom_field_options')) {
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
                'option_value' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => false,
                ],
                'option_label' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => false,
                ],
                'sort_order' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                    'null' => false,
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
            $this->dbforge->create_table('custom_field_options', true, ['engine' => 'InnoDB']);

            // Add foreign key constraint
            $this->db->query(
                'ALTER TABLE `' .
                    $this->db->dbprefix('custom_field_options') .
                    '` ADD CONSTRAINT `custom_field_options_custom_fields` FOREIGN KEY (`id_custom_fields`) REFERENCES `' .
                    $this->db->dbprefix('custom_fields') .
                    '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
            );
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->table_exists('custom_field_options')) {
            $this->db->query(
                'ALTER TABLE `' .
                    $this->db->dbprefix('custom_field_options') .
                    '` DROP FOREIGN KEY `custom_field_options_custom_fields`',
            );
            $this->dbforge->drop_table('custom_field_options');
        }
    }
}
