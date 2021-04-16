<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Class Migration_add_slug_to_services_and_users
 *
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_add_slug_to_services_and_users extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        $fields = [
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE
            ]
        ];

        $this->dbforge->add_column('users', $fields);

        $fields = [
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '256',
                'null' => TRUE
            ]
        ];

        $this->dbforge->add_column('services', $fields);

        $this->db->insert('settings', [
            'name' => 'aways_edit_customer',
            'value' => '1'
        ]);
        $this->db->insert('settings', [
            'name' => 'replace_home',
            'value' => null
        ]);
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        $this->dbforge->drop_column('users', 'slug');
        $this->dbforge->drop_column('services', 'slug');
        $this->db->delete('settings', ['name' => 'aways_edit_customer']);
        $this->db->delete('settings', ['name' => 'replace_home']);
    }
}
