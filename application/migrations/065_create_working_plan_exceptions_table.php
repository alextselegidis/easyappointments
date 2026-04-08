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

class Migration_Create_working_plan_exceptions_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->table_exists('working_plan_exceptions')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                ],
                'start_date' => [
                    'type' => 'DATE',
                    'null' => false,
                ],
                'end_date' => [
                    'type' => 'DATE',
                    'null' => false,
                ],
                'start_time' => [
                    'type' => 'TIME',
                    'null' => true,
                ],
                'end_time' => [
                    'type' => 'TIME',
                    'null' => true,
                ],
                'breaks' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'id_users_provider' => [
                    'type' => 'INT',
                    'constraint' => 11,
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

            $this->dbforge->add_key('start_date');

            $this->dbforge->add_key('end_date');

            $this->dbforge->add_key('id_users_provider');

            $this->dbforge->create_table('working_plan_exceptions', true, ['engine' => 'InnoDB']);

            $this->db->query(
                '
                ALTER TABLE `' .
                    $this->db->dbprefix('working_plan_exceptions') .
                    '`
                    ADD CONSTRAINT `working_plan_exceptions_users_provider` FOREIGN KEY (`id_users_provider`) REFERENCES `' .
                    $this->db->dbprefix('users') .
                    '` (`id`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ',
            );
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->table_exists('working_plan_exceptions')) {
            $this->db->query(
                'ALTER TABLE `' .
                    $this->db->dbprefix('working_plan_exceptions') .
                    '` DROP FOREIGN KEY `working_plan_exceptions_users_provider`',
            );

            $this->dbforge->drop_table('working_plan_exceptions');
        }
    }
}
