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

class Migration_Replace_availabilities_type_with_slot_interval extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        // Add the new slot_interval column
        if (!$this->db->field_exists('slot_interval', 'services')) {
            $fields = [
                'slot_interval' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 15,
                    'after' => 'description',
                ],
            ];

            $this->dbforge->add_column('services', $fields);

            // Set default value for all existing services
            $this->db->update('services', ['slot_interval' => 15]);
        }

        // Drop the old availabilities_type column
        if ($this->db->field_exists('availabilities_type', 'services')) {
            $this->dbforge->drop_column('services', 'availabilities_type');
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        // Re-add the availabilities_type column
        if (!$this->db->field_exists('availabilities_type', 'services')) {
            $fields = [
                'availabilities_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => '32',
                    'default' => 'flexible',
                    'after' => 'description',
                ],
            ];

            $this->dbforge->add_column('services', $fields);

            $this->db->update('services', ['availabilities_type' => 'flexible']);
        }

        // Drop the slot_interval column
        if ($this->db->field_exists('slot_interval', 'services')) {
            $this->dbforge->drop_column('services', 'slot_interval');
        }
    }
}
