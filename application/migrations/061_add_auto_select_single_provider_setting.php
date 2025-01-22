<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_auto_select_single_provider_setting extends EA_Migration {
    public function up() {
        if ($this->db->get_where('settings', ['name' => 'auto_select_single_provider'])->num_rows() === 0) {
            $this->db->insert('settings', [
                'name' => 'auto_select_single_provider',
                'value' => '0'
            ]);
        }
    }

    public function down() {
        if ($this->db->get_where('settings', ['name' => 'auto_select_single_provider'])->num_rows() > 0) {
            $this->db->delete('settings', ['name' => 'auto_select_single_provider']);
        }
    }
}