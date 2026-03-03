<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_reminder_sent_at_to_appointments extends EA_Migration
{
    public function up(): void
    {
        if (!$this->db->table_exists('appointments') || $this->db->field_exists('reminder_sent_at', 'appointments')) {
            return;
        }

        $this->dbforge->add_column('appointments', [
            'reminder_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        if (!$this->index_exists('appointments', 'idx_appointments_reminder_sent_at')) {
            $this->db->query('CREATE INDEX idx_appointments_reminder_sent_at ON appointments (reminder_sent_at)');
        }
    }

    public function down(): void
    {
        if (!$this->db->table_exists('appointments') || !$this->db->field_exists('reminder_sent_at', 'appointments')) {
            return;
        }

        $this->dbforge->drop_column('appointments', 'reminder_sent_at');
    }

    private function index_exists(string $table, string $index): bool
    {
        $result = $this->db->query('SHOW INDEX FROM ' . $table . ' WHERE Key_name = ' . $this->db->escape($index));

        return $result->num_rows() > 0;
    }
}
