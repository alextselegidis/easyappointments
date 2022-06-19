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

class Migration_Create_webhooks_table extends EA_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ( ! $this->db->table_exists('webhooks'))
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
                'delete_datetime' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ],
                'name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '256',
                    'null' => TRUE,
                ],
                'url' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'actions' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'secret_token' => [
                    'type' => 'VARCHAR',
                    'constraint' => '512',
                    'null' => TRUE,
                ],
                'is_ssl_verified' => [
                    'type' => 'TINYINT',
                    'constraint' => '4',
                    'default' => TRUE,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
            ]);

            $this->dbforge->add_key('id', TRUE);

            $this->dbforge->create_table('webhooks', TRUE, ['engine' => 'InnoDB']);
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->table_exists('webhooks'))
        {
            $this->dbforge->drop_table('webhooks');
        }
    }
}
