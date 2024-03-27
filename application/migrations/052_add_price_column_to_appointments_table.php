<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_price_column_to_appointments_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up()
    {
        if (!$this->db->field_exists('price', 'appointments')) {
            $fields = [
                'price' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => true,
                    'after' => 'status',
                ],
                'currency' => [
                    'type' => 'VARCHAR',
                    'constraint' => '32',
                    'null' => true,
                ],
            ];

            $this->dbforge->add_column('appointments', $fields);

            // Update existing records
            $sql = "UPDATE `" . $this->db->dbprefix('appointments') . "` AS `appointments`
            JOIN `" . $this->db->dbprefix('services') . "` AS `services` ON `appointments`.`id_services` = `services`.`id`
            SET `appointments`.`price` = `services`.`price`,
                `appointments`.`currency` = `services`.`currency`";

            $this->db->query($sql);

        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->field_exists('price', 'appointments')) {
            $this->dbforge->drop_column('appointments', 'price');
            $this->dbforge->drop_column('appointments', 'currency');
        }
    }
}
