<?php

/**
 * Local variables.
 *
 * @var EA_Migration $this
 */
class Migration_Create_user_subscriptions_table extends EA_Migration
{
    /**
     * Upgrade method.
     *
     * @throws Exception
     */
    public function up(): void
    {
        if (!$this->db->table_exists('user_subscriptions')) {
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
                'update_datetime' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'delete_datetime' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'id_users_customer' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                ],
                'id_subscription_plans' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                ],
                'start_date' => [
                    'type' => 'DATE',
                    'null' => false,
                ],
                'end_date' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'appointments_quota' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'default' => 0,
                ],
                'appointments_used' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'default' => 0,
                ],
                'last_quota_reset_date' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'is_active' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'null' => false,
                    'default' => 1,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);

            $this->dbforge->add_key('id', true);
            $this->dbforge->add_key('id_users_customer');
            $this->dbforge->add_key('id_subscription_plans');

            $this->dbforge->create_table('user_subscriptions', true, ['engine' => 'InnoDB']);

            // Add foreign keys
            $this->db->query(
                'ALTER TABLE ' .
                    $this->db->dbprefix('user_subscriptions') .
                    '
                ADD CONSTRAINT fk_user_subscriptions_users
                FOREIGN KEY (id_users_customer) REFERENCES ' .
                    $this->db->dbprefix('users') .
                    '(id)
                ON DELETE CASCADE ON UPDATE CASCADE'
            );

            $this->db->query(
                'ALTER TABLE ' .
                    $this->db->dbprefix('user_subscriptions') .
                    '
                ADD CONSTRAINT fk_user_subscriptions_plans
                FOREIGN KEY (id_subscription_plans) REFERENCES ' .
                    $this->db->dbprefix('subscription_plans') .
                    '(id)
                ON DELETE CASCADE ON UPDATE CASCADE'
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
        if ($this->db->table_exists('user_subscriptions')) {
            // Drop foreign keys first
            $this->db->query(
                'ALTER TABLE ' . $this->db->dbprefix('user_subscriptions') . ' DROP FOREIGN KEY fk_user_subscriptions_users'
            );
            $this->db->query(
                'ALTER TABLE ' . $this->db->dbprefix('user_subscriptions') . ' DROP FOREIGN KEY fk_user_subscriptions_plans'
            );

            $this->dbforge->drop_table('user_subscriptions');
        }
    }
}
