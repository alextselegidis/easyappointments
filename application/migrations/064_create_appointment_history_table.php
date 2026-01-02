<?php

/**
 * Local variables.
 *
 * @var EA_Migration $this
 */
class Migration_Create_appointment_history_table extends EA_Migration
{
    /**
     * Upgrade method.
     *
     * @throws Exception
     */
    public function up(): void
    {
        if (!$this->db->table_exists('appointment_history')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                ],
                'create_datetime' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'id_appointments' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                ],
                'id_user_subscriptions' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
                'action' => [
                    'type' => 'VARCHAR',
                    'constraint' => '50',
                    'null' => false,
                    'comment' => 'created, cancelled, quota_refunded, quota_not_refunded',
                ],
                'quota_change' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'default' => 0,
                    'comment' => 'Positive or negative change to quota usage',
                ],
                'cancellation_hours_before' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => true,
                    'comment' => 'Hours before appointment when cancelled',
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);

            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('id_appointments');
            $this->dbforge->add_key('id_user_subscriptions');

            $this->dbforge->create_table('appointment_history', true, ['engine' => 'InnoDB']);

            // Add foreign keys
            $this->db->query(
                'ALTER TABLE ' .
                    $this->db->dbprefix('appointment_history') .
                    '
                ADD CONSTRAINT fk_appointment_history_appointments
                FOREIGN KEY (id_appointments) REFERENCES ' .
                    $this->db->dbprefix('appointments') .
                    '(id)
                ON DELETE CASCADE ON UPDATE CASCADE'
            );

            $this->db->query(
                'ALTER TABLE ' .
                    $this->db->dbprefix('appointment_history') .
                    '
                ADD CONSTRAINT fk_appointment_history_user_subscriptions
                FOREIGN KEY (id_user_subscriptions) REFERENCES ' .
                    $this->db->dbprefix('user_subscriptions') .
                    '(id)
                ON DELETE SET NULL ON UPDATE CASCADE'
            );
        }
    }

    /**
     * Downgrade method.
     *
     * @throws Exception
     */
    public function down(): void
    {
        if ($this->db->table_exists('appointment_history')) {
            // Drop foreign keys first
            $this->db->query(
                'ALTER TABLE ' .
                    $this->db->dbprefix('appointment_history') .
                    ' DROP FOREIGN KEY fk_appointment_history_appointments'
            );
            $this->db->query(
                'ALTER TABLE ' .
                    $this->db->dbprefix('appointment_history') .
                    ' DROP FOREIGN KEY fk_appointment_history_user_subscriptions'
            );

            $this->dbforge->drop_table('appointment_history');
        }
    }
}
