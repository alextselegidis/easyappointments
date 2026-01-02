<?php

/**
 * Local variables.
 *
 * @var EA_Migration $this
 */
class Migration_Add_user_subscription_id_to_appointments_table extends EA_Migration
{
    /**
     * Upgrade method.
     *
     * @throws Exception
     */
    public function up(): void
    {
        if (!$this->db->field_exists('id_user_subscriptions', 'appointments')) {
            $fields = [
                'id_user_subscriptions' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'after' => 'id_users_customer',
                ],
            ];

            $this->dbforge->add_column('appointments', $fields);

            // Add index
            $this->db->query(
                'ALTER TABLE ' . $this->db->dbprefix('appointments') . ' ADD INDEX idx_user_subscriptions (id_user_subscriptions)'
            );

            // Add foreign key
            $this->db->query(
                'ALTER TABLE ' .
                    $this->db->dbprefix('appointments') .
                    '
                ADD CONSTRAINT fk_appointments_user_subscriptions
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
        if ($this->db->field_exists('id_user_subscriptions', 'appointments')) {
            // Drop foreign key first
            $this->db->query(
                'ALTER TABLE ' . $this->db->dbprefix('appointments') . ' DROP FOREIGN KEY fk_appointments_user_subscriptions'
            );

            // Drop index
            $this->db->query('ALTER TABLE ' . $this->db->dbprefix('appointments') . ' DROP INDEX idx_user_subscriptions');

            // Drop column
            $this->dbforge->drop_column('appointments', 'id_user_subscriptions');
        }
    }
}
