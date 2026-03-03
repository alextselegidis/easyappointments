<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_stripe_connect_and_payment_tracking extends EA_Migration
{
    public function up(): void
    {
        $this->add_tenant_stripe_columns();
        $this->create_stripe_payment_intents_table();
    }

    public function down(): void
    {
        if ($this->db->table_exists('stripe_payment_intents')) {
            $this->dbforge->drop_table('stripe_payment_intents');
        }

        $this->drop_tenant_stripe_columns();
    }

    private function add_tenant_stripe_columns(): void
    {
        if (!$this->db->table_exists('tenants')) {
            return;
        }

        $fields = [];

        if (!$this->db->field_exists('stripe_account_id', 'tenants')) {
            $fields['stripe_account_id'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ];
        }

        if (!$this->db->field_exists('stripe_onboarding_completed', 'tenants')) {
            $fields['stripe_onboarding_completed'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ];
        }

        if (!$this->db->field_exists('stripe_details_submitted', 'tenants')) {
            $fields['stripe_details_submitted'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ];
        }

        if (!$this->db->field_exists('stripe_charges_enabled', 'tenants')) {
            $fields['stripe_charges_enabled'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ];
        }

        if (!$this->db->field_exists('stripe_payouts_enabled', 'tenants')) {
            $fields['stripe_payouts_enabled'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ];
        }

        if (!$this->db->field_exists('stripe_updated_at', 'tenants')) {
            $fields['stripe_updated_at'] = [
                'type' => 'DATETIME',
                'null' => true,
            ];
        }

        if (!empty($fields)) {
            $this->dbforge->add_column('tenants', $fields);
        }

        if (!$this->index_exists('tenants', 'idx_tenants_stripe_account_id')) {
            $this->db->query('CREATE INDEX idx_tenants_stripe_account_id ON tenants (stripe_account_id)');
        }
    }

    private function drop_tenant_stripe_columns(): void
    {
        if (!$this->db->table_exists('tenants')) {
            return;
        }

        $columns = [
            'stripe_account_id',
            'stripe_onboarding_completed',
            'stripe_details_submitted',
            'stripe_charges_enabled',
            'stripe_payouts_enabled',
            'stripe_updated_at',
        ];

        foreach ($columns as $column) {
            if ($this->db->field_exists($column, 'tenants')) {
                $this->dbforge->drop_column('tenants', $column);
            }
        }
    }

    private function create_stripe_payment_intents_table(): void
    {
        if ($this->db->table_exists('stripe_payment_intents')) {
            return;
        }

        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tenant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'appointment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'stripe_payment_intent_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'currency' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'default' => 'eur',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => false,
                'default' => 'created',
            ],
            'is_split' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'stripe_account_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'stripe_event_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'raw_payload' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('stripe_payment_intents', true);

        if (!$this->index_exists('stripe_payment_intents', 'idx_spi_intent_id')) {
            $this->db->query('CREATE UNIQUE INDEX idx_spi_intent_id ON stripe_payment_intents (stripe_payment_intent_id)');
        }

        if (!$this->index_exists('stripe_payment_intents', 'idx_spi_tenant_id')) {
            $this->db->query('CREATE INDEX idx_spi_tenant_id ON stripe_payment_intents (tenant_id)');
        }

        if (!$this->index_exists('stripe_payment_intents', 'idx_spi_appointment_id')) {
            $this->db->query('CREATE INDEX idx_spi_appointment_id ON stripe_payment_intents (appointment_id)');
        }

        if (!$this->index_exists('stripe_payment_intents', 'idx_spi_status')) {
            $this->db->query('CREATE INDEX idx_spi_status ON stripe_payment_intents (status)');
        }
    }

    private function index_exists(string $table, string $index): bool
    {
        $result = $this->db->query('SHOW INDEX FROM ' . $table . ' WHERE Key_name = ' . $this->db->escape($index));

        return $result->num_rows() > 0;
    }
}
