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

class Migration_Add_service_sorting_setting extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('position', 'services')) {
            $this->dbforge->add_column('services', [
                'position' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'default' => 0,
                    'after' => 'id_service_categories',
                ],
            ]);
        }

        if (!$this->db->field_exists('position', 'service_categories')) {
            $this->dbforge->add_column('service_categories', [
                'position' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'default' => 0,
                    'after' => 'description',
                ],
            ]);
        }

        $this->seed_positions('services');
        $this->seed_positions('service_categories');

        if (!$this->db->get_where('settings', ['name' => 'sort_services_and_categories'])->row_array()) {
            $this->db->insert('settings', [
                'name' => 'sort_services_and_categories',
                'value' => '0',
            ]);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        $this->db->delete('settings', ['name' => 'sort_services_and_categories']);

        if ($this->db->field_exists('position', 'services')) {
            $this->dbforge->drop_column('services', 'position');
        }

        if ($this->db->field_exists('position', 'service_categories')) {
            $this->dbforge->drop_column('service_categories', 'position');
        }
    }

    /**
     * Seed existing rows with deterministic positions.
     */
    private function seed_positions(string $table): void
    {
        $rows = $this->db
            ->select('id')
            ->from($table)
            ->order_by('update_datetime DESC, id ASC')
            ->get()
            ->result_array();

        foreach ($rows as $index => $row) {
            $this->db->update($table, ['position' => $index + 1], ['id' => $row['id']]);
        }
    }
}
