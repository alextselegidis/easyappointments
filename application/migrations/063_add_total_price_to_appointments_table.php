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

class Migration_Add_total_price_to_appointments_table extends EA_Migration 
{
    /**
     * Upgrade method.
     */
	public function up(): void {
        if (!$this->db->field_exists('total_price', 'appointments')) {
            $fields = [
                'total_price' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => true,
                    'after' => 'id_caldav_calendar',
                ],
            ];

            $this->dbforge->add_column('appointments', $fields);
        }
	}

    /**
     * Downgrade method.
     */
	public function down(): void {
        if ($this->db->field_exists('total_price', 'appointments')) {
            $this->dbforge->drop_column('appointments', 'total_price');
        }
	}

}