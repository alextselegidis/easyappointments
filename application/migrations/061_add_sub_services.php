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

class Migration_Add_Sub_Services extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->table_exists('subservices')) {
            $this->dbforge->add_field([
                'create_datetime' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'update_datetime' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'subservice' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
                'service' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
            ]);

            $this->dbforge->add_key('subservice', true);
            $this->dbforge->add_key('service', true);
            // Also create seperate indexes
            // $this->dbforge->add_key('subservice');
            // $this->dbforge->add_key('service');

            $this->dbforge->create_table('subservices', true, ['engine' => 'InnoDB']);

            // $this->db->query(
            //     'ALTER TABLE `' .
            //         $this->db->dbprefix('subservices') .
            //         '`
            //     ADD CONSTRAINT `subservice_subservice` FOREIGN KEY (`subservice`) REFERENCES `' .
            //         $this->db->dbprefix('services') .
            //         '` (`id`)
            //     ON DELETE CASCADE
            //     ON UPDATE CASCADE,

            //     ADD CONSTRAINT `subservice_service` FOREIGN KEY (`service`) REFERENCES `' .
            //         $this->db->dbprefix('services') .
            //         '` (`id`)
            //     ON DELETE CASCADE
            //     ON UPDATE CASCADE',
            // );
        }

        if (!$this->db->table_exists('appointments_subservices')) {
            $this->dbforge->add_field([
                'create_datetime' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'update_datetime' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'appointment' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
                'subservice' => [
                    'type' => 'INT',
                    'constraint' => 11,
                ],
            ]);

            $this->dbforge->add_key('appointment', true);
            $this->dbforge->add_key('subservice', true);
            // Also create seperate indexes
            // $this->dbforge->add_key('appointment');
            // $this->dbforge->add_key('subservice');

            $this->dbforge->create_table('appointments_subservices', true, ['engine' => 'InnoDB']);

            // $this->db->query(
            //     'ALTER TABLE `' .
            //         $this->db->dbprefix('appointments_subservices') .
            //         '`
            //     ADD CONSTRAINT `subservice_service` FOREIGN KEY (`subservice`) REFERENCES `' .
            //         $this->db->dbprefix('services') .
            //         '` (`id`)
            //     ON DELETE CASCADE
            //     ON UPDATE CASCADE,

            //     ADD CONSTRAINT `appointment_appointments` FOREIGN KEY (`appointment`) REFERENCES `' .
            //         $this->db->dbprefix('appointments') .
            //         '` (`id`)
            //     ON DELETE CASCADE
            //     ON UPDATE CASCADE',
            // );
        }

    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        
        if ($this->db->table_exists('subservices')) {

            $this->db->query(
                'ALTER TABLE `' . $this->db->dbprefix('subservices') . '` DROP FOREIGN KEY `subservice_subservice`',
            );
            $this->db->query(
                'ALTER TABLE `' . $this->db->dbprefix('subservices') . '` DROP FOREIGN KEY `subservice_service`',
            );
            
            $this->dbforge->drop_table('subservices');
        }

        if ($this->db->table_exists('appointments_subservices')) {

            $this->db->query(
                'ALTER TABLE `' . $this->db->dbprefix('appointments_subservices') . '` DROP FOREIGN KEY `appointment_appointments`',
            );
            $this->db->query(
                'ALTER TABLE `' . $this->db->dbprefix('appointments_subservices') . '` DROP FOREIGN KEY `subservice_service`',
            );
            
            $this->dbforge->drop_table('appointments_subservices');
        }
        
    }
}
