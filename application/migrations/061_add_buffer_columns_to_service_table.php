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

class Migration_Add_buffer_columns_to_service_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('buffer_before', 'services')) {
            $this->dbforge->add_column('services', [
                'buffer_before' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
            ]);
        }

        if (!$this->db->field_exists('buffer_after', 'services')) {
            $this->dbforge->add_column('services', [
                'buffer_after' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
            ]);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('buffer_before', 'services')) {
            $this->dbforge->drop_column('services', 'buffer_before');
        }

        if ($this->db->field_exists('buffer_after', 'services')) {
            $this->dbforge->drop_column('services', 'buffer_after');
        }
    }
}