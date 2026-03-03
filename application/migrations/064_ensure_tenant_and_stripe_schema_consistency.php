<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Ensure_tenant_and_stripe_schema_consistency extends EA_Migration
{
    public function up(): void
    {
        $this->ensure_tenants_table();
        $this->ensure_default_tenant();

        $tenant_scoped_tables = [
            'users',
            'services',
            'appointments',
            'blocked_periods',
            'consents',
            'customers',
        ];

        foreach ($tenant_scoped_tables as $table) {
            $this->ensure_tenant_column($table);
            $this->backfill_tenant_column($table);
            $this->ensure_tenant_index($table);
        }

        $this->ensure_tenant_stripe_columns();
        $this->ensure_stripe_payment_intents_table();
    }

    public function down(): void
    {
    }

    private function ensure_tenants_table(): void
    {
        if ($this->db->table_exists('tenants')) {
            return;
        }

        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('tenants', true);

        if (!$this->index_exists('tenants', 'idx_tenants_slug')) {
            $this->create_index('tenants', 'idx_tenants_slug', 'slug', true);
        }
    }

    private function ensure_default_tenant(): void
    {
        if (!$this->db->table_exists('tenants')) {
            return;
        }

        $existing = $this->db->get_where('tenants', ['slug' => 'default'])->row_array();

        if (!empty($existing)) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->insert('tenants', [
            'id' => 1,
            'slug' => 'default',
            'name' => 'Default Tenant',
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function ensure_tenant_column(string $table): void
    {
        if (!$this->db->table_exists($table) || $this->db->field_exists('tenant_id', $table)) {
            return;
        }

        $this->dbforge->add_column($table, [
            'tenant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);
    }

    private function backfill_tenant_column(string $table): void
    {
        if (!$this->db->table_exists($table) || !$this->db->field_exists('tenant_id', $table)) {
            return;
        }

        $table_name = $this->table_name($table);
        $this->db->query('UPDATE ' . $table_name . ' SET tenant_id = 1 WHERE tenant_id IS NULL');
    }

    private function ensure_tenant_index(string $table): void
    {
        if (!$this->db->table_exists($table) || !$this->db->field_exists('tenant_id', $table)) {
            return;
        }

        $index_name = 'idx_' . $table . '_tenant_id';

        if ($this->index_exists($table, $index_name)) {
            return;
        }

        $this->create_index($table, $index_name, 'tenant_id');
    }

    private function ensure_tenant_stripe_columns(): void
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
            $this->create_index('tenants', 'idx_tenants_stripe_account_id', 'stripe_account_id');
        }
    }

    private function ensure_stripe_payment_intents_table(): void
    {
        if (!$this->db->table_exists('stripe_payment_intents')) {
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
        }

        if (!$this->index_exists('stripe_payment_intents', 'idx_spi_intent_id')) {
            $this->create_index('stripe_payment_intents', 'idx_spi_intent_id', 'stripe_payment_intent_id', true);
        }

        if (!$this->index_exists('stripe_payment_intents', 'idx_spi_tenant_id')) {
            $this->create_index('stripe_payment_intents', 'idx_spi_tenant_id', 'tenant_id');
        }

        if (!$this->index_exists('stripe_payment_intents', 'idx_spi_appointment_id')) {
            $this->create_index('stripe_payment_intents', 'idx_spi_appointment_id', 'appointment_id');
        }

        if (!$this->index_exists('stripe_payment_intents', 'idx_spi_status')) {
            $this->create_index('stripe_payment_intents', 'idx_spi_status', 'status');
        }
    }

    private function table_name(string $table): string
    {
        return '`' . $this->db->dbprefix($table) . '`';
    }

    private function create_index(string $table, string $index, string $column, bool $unique = false): void
    {
        $unique_sql = $unique ? 'UNIQUE ' : '';
        $sql = 'CREATE ' . $unique_sql . 'INDEX ' . $index . ' ON ' . $this->table_name($table) . ' (' . $column . ')';
        $this->db->query($sql);
    }

    private function index_exists(string $table, string $index): bool
    {
        $sql = 'SHOW INDEX FROM ' . $this->table_name($table) . ' WHERE Key_name = ' . $this->db->escape($index);
        $result = $this->db->query($sql);

        return $result->num_rows() > 0;
    }
}
