<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_tenants_and_add_tenant_id_columns extends EA_Migration
{
    public function up(): void
    {
        $this->create_tenants_table();
        $this->seed_default_tenant();

        $this->add_tenant_column('users');
        $this->add_tenant_column('services');
        $this->add_tenant_column('appointments');
        $this->add_tenant_column('customers');
        $this->add_tenant_column('blocked_periods');
        $this->add_tenant_column('consents');

        $this->db->query('UPDATE users SET tenant_id = 1 WHERE tenant_id IS NULL');
        $this->db->query('UPDATE services SET tenant_id = 1 WHERE tenant_id IS NULL');
        $this->db->query('UPDATE appointments SET tenant_id = 1 WHERE tenant_id IS NULL');
        $this->db->query('UPDATE customers SET tenant_id = 1 WHERE tenant_id IS NULL');
        $this->db->query('UPDATE blocked_periods SET tenant_id = 1 WHERE tenant_id IS NULL');
        $this->db->query('UPDATE consents SET tenant_id = 1 WHERE tenant_id IS NULL');
    }

    public function down(): void
    {
        $this->drop_tenant_column('users');
        $this->drop_tenant_column('services');
        $this->drop_tenant_column('appointments');
        $this->drop_tenant_column('customers');
        $this->drop_tenant_column('blocked_periods');
        $this->drop_tenant_column('consents');

        if ($this->db->table_exists('tenants')) {
            $this->dbforge->drop_table('tenants');
        }
    }

    private function create_tenants_table(): void
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

        $this->db->query('CREATE UNIQUE INDEX idx_tenants_slug ON tenants (slug)');
    }

    private function seed_default_tenant(): void
    {
        $default_tenant = $this->db->get_where('tenants', ['slug' => 'default'])->row_array();

        if (!empty($default_tenant)) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->insert('tenants', [
            'slug' => 'default',
            'name' => 'Default Tenant',
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function add_tenant_column(string $table): void
    {
        if (!$this->db->table_exists($table) || $this->db->field_exists('tenant_id', $table)) {
            return;
        }

        $fields = [
            'tenant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ];

        $this->dbforge->add_column($table, $fields);

        $index_name = 'idx_' . $table . '_tenant_id';
        $this->db->query('CREATE INDEX ' . $index_name . ' ON ' . $table . ' (tenant_id)');
    }

    private function drop_tenant_column(string $table): void
    {
        if (!$this->db->table_exists($table) || !$this->db->field_exists('tenant_id', $table)) {
            return;
        }

        $this->dbforge->drop_column($table, 'tenant_id');
    }
}
