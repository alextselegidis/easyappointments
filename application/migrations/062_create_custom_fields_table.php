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

class Migration_Create_custom_fields_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->table_exists('custom_fields')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                ],
                'name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => false,
                ],
                'label' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => false,
                ],
                'type' => [
                    'type' => 'ENUM',
                    'constraint' => ['text', 'textarea', 'select'],
                    'default' => 'text',
                    'null' => false,
                ],
                'required' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'null' => false,
                ],
                'display_column' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                    'null' => false,
                    'comment' => 'Column number (1 or 2) for display layout',
                ],
                'sort_order' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 0,
                    'null' => false,
                ],
                'active' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
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
            $this->dbforge->create_table('custom_fields', true, ['engine' => 'InnoDB']);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->table_exists('custom_fields')) {
            $this->dbforge->drop_table('custom_fields');
        }
    }
}
