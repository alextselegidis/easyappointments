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

class Migration_Create_blocked_periods_table extends EA_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ( ! $this->db->table_exists('blocked_periods'))
        {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ],
                'create_datetime' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'update_datetime' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'null' => TRUE,
                ],
                'start_datetime' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'end_datetime' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
            ]);

            $this->dbforge->add_key('id', TRUE);

            $this->dbforge->create_table('blocked_periods', TRUE, ['engine' => 'InnoDB']);
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->table_exists('blocked_periods'))
        {
            $this->dbforge->drop_table('blocked_periods');
        }
    }
}
