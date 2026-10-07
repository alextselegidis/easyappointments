<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_service_email_note extends EA_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('email_note', 'services')) {
            $fields = [
                'email_note' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
            ];
            $this->dbforge->add_column('services', $fields);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column('services', 'email_note');
    }
}