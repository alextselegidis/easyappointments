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

class Migration_Create_appointment_location_column extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up()
    {
        if (!$this->db->field_exists('location', 'appointments')) {
            $fields = [
                'location' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'end_datetime',
                ],
            ];

            $this->dbforge->add_column('appointments', $fields);
        }

        if (!$this->db->field_exists('location', 'services')) {
            $fields = [
                'location' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'description',
                ],
            ];

            $this->dbforge->add_column('services', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->field_exists('location', 'appointments')) {
            $this->dbforge->drop_column('appointments', 'location');
        }

        if ($this->db->field_exists('location', 'services')) {
            $this->dbforge->drop_column('services', 'location');
        }
    }
}
