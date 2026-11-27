<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.7.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_attendants_number_column_to_services_providers_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('attendants_number', 'services_providers')) {
            $fields = [
                'attendants_number' => [
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => true,
                ],
            ];

            $this->dbforge->add_column('services_providers', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('attendants_number', 'services_providers')) {
            $this->dbforge->drop_column('services_providers', 'attendants_number');
        }
    }
}
