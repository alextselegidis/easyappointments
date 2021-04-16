<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

/**
 * Class Migration_Create_consents_table
 *
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Legal_contents extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        $this->db->insert('settings', [
            'name' => 'display_cookie_notice',
            'value' => '0'
        ]);

        $this->db->insert('settings', [
            'name' => 'cookie_notice_content',
            'value' => 'Cookie notice content.'
        ]);

        $this->db->insert('settings', [
            'name' => 'display_terms_and_conditions',
            'value' => '0'
        ]);

        $this->db->insert('settings', [
            'name' => 'terms_and_conditions_content',
            'value' => 'Terms and conditions content.'
        ]);

        $this->db->insert('settings', [
            'name' => 'display_privacy_policy',
            'value' => '0'
        ]);

        $this->db->insert('settings', [
            'name' => 'privacy_policy_content',
            'value' => 'Privacy policy content.'
        ]);

        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ],
            'created' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'modified' => [
                'type' => 'TIMESTAMP',
                'null' => TRUE
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => TRUE,
            ],
            'ip' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE,
            ],
        ]);

        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table('consents', TRUE, ['engine' => 'InnoDB']);
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        $this->db->delete('settings', [
            'name' => 'display_cookie_notice'
        ]);

        $this->db->delete('settings', [
            'name' => 'cookie_notice_content'
        ]);

        $this->db->delete('settings', [
            'name' => 'display_terms_and_conditions'
        ]);

        $this->db->delete('settings', [
            'name' => 'terms_and_conditions_content'
        ]);

        $this->db->delete('settings', [
            'name' => 'display_privacy_policy'
        ]);

        $this->db->delete('settings', [
            'name' => 'privacy_policy_content'
        ]);

        $this->dbforge->drop_table('consents');
    }
}
