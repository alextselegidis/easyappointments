<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_service_availabilities_type extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up()
    {
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
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->field_exists('availabilities_type', 'services')) {
            $this->dbforge->drop_column('services', 'availabilities_type');
        }
    }
}
