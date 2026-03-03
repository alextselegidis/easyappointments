<?php defined('BASEPATH') or exit('No direct script access allowed');

class Tenant_context
{
    protected EA_Controller|CI_Controller $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function resolve_tenant_id(): int
    {
        if (!$this->CI->db->table_exists('tenants')) {
            return 1;
        }

        $host = $this->CI->input->server('HTTP_HOST') ?: '';
        $host_without_port = explode(':', $host)[0] ?? '';

        if (in_array($host_without_port, ['127.0.0.1', 'localhost'], true) || empty($host_without_port)) {
            return 1;
        }

        $tokens = explode('.', $host_without_port);
        $slug = $tokens[0] ?? 'default';

        $tenant = $this->CI->db->get_where('tenants', ['slug' => $slug, 'is_active' => 1])->row_array();

        if (empty($tenant['id'])) {
            $tenant = $this->CI->db->get_where('tenants', ['slug' => 'default', 'is_active' => 1])->row_array();
        }

        return (int) ($tenant['id'] ?? 1);
    }

    public function table_has_tenant_column(string $table): bool
    {
        return $this->CI->db->table_exists($table) && $this->CI->db->field_exists('tenant_id', $table);
    }

    public function with_tenant_where(string $table, ?array $where = null): ?array
    {
        if (!$this->table_has_tenant_column($table)) {
            return $where;
        }

        $tenant_id = $this->resolve_tenant_id();
        $where = $where ?? [];
        $where['tenant_id'] = $tenant_id;

        return $where;
    }

    public function belongs_to_current_tenant(array $resource): bool
    {
        if (!array_key_exists('tenant_id', $resource) || $resource['tenant_id'] === null) {
            return true;
        }

        return (int) $resource['tenant_id'] === $this->resolve_tenant_id();
    }
}
