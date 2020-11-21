<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.0
 * ---------------------------------------------------------------------------- */

/**
 * Class Migration_Change_column_types
 *
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Change_column_types extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        // Drop table constraints.
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('appointments') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('appointments') . '_ibfk_2`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('appointments') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('appointments') . '_ibfk_3`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('appointments') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('appointments') . '_ibfk_4`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('secretaries_providers') . '` DROP FOREIGN KEY `fk_' . $this->db->dbprefix('secretaries_providers') . '_1`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('secretaries_providers') . '` DROP FOREIGN KEY `fk_' . $this->db->dbprefix('secretaries_providers') . '_2`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services_providers') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('services_providers') . '_ibfk_1`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services_providers') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('services_providers') . '_ibfk_2`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('services') . '_ibfk_1`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('users') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('users') . '_ibfk_1`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('user_settings') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('user_settings') . '_ibfk_1`');

        // Appointments
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'int',
                'constraint' => '11',
                'auto_increment' => TRUE
            ],
            'id_users_provider' => [
                'name' => 'id_users_provider',
                'type' => 'int',
                'constraint' => '11'
            ],
            'id_users_customer' => [
                'name' => 'id_users_customer',
                'type' => 'int',
                'constraint' => '11'
            ],
            'id_services' => [
                'name' => 'id_services',
                'type' => 'int',
                'constraint' => '11'
            ]
        ];

        $this->dbforge->modify_column('appointments', $fields);

        // Roles
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'int',
                'constraint' => '11',
                'auto_increment' => TRUE
            ],
            'appointments' => [
                'name' => 'appointments',
                'type' => 'int',
                'constraint' => '11'
            ],
            'customers' => [
                'name' => 'customers',
                'type' => 'int',
                'constraint' => '11'
            ],
            'services' => [
                'name' => 'services',
                'type' => 'int',
                'constraint' => '11'
            ],
            'users' => [
                'name' => 'users',
                'type' => 'int',
                'constraint' => '11'
            ],
            'system_settings' => [
                'name' => 'system_settings',
                'type' => 'int',
                'constraint' => '11'
            ],
            'user_settings' => [
                'name' => 'user_settings',
                'type' => 'int',
                'constraint' => '11'
            ]
        ];

        $this->dbforge->modify_column('roles', $fields);

        // Secretary Provider
        $fields = [
            'id_users_secretary' => [
                'name' => 'id_users_secretary',
                'type' => 'int',
                'constraint' => '11'
            ],
            'id_users_provider' => [
                'name' => 'id_users_provider',
                'type' => 'int',
                'constraint' => '11'
            ]
        ];

        $this->dbforge->modify_column('secretaries_providers', $fields);

        // Services
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'int',
                'constraint' => '11',
                'auto_increment' => TRUE
            ],
            'id_service_categories' => [
                'name' => 'id_service_categories',
                'type' => 'int',
                'constraint' => '11'
            ]
        ];

        $this->dbforge->modify_column('services', $fields);

        // Service Providers
        $fields = [
            'id_users' => [
                'name' => 'id_users',
                'type' => 'int',
                'constraint' => '11'
            ],
            'id_services' => [
                'name' => 'id_services',
                'type' => 'int',
                'constraint' => '11'
            ]
        ];

        $this->dbforge->modify_column('services_providers', $fields);

        // Service Categories
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'int',
                'constraint' => '11',
                'auto_increment' => TRUE
            ]
        ];

        $this->dbforge->modify_column('service_categories', $fields);

        // Settings
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'int',
                'constraint' => '11',
                'auto_increment' => TRUE
            ]
        ];

        $this->dbforge->modify_column('settings', $fields);

        // Users
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'int',
                'constraint' => '11',
                'auto_increment' => TRUE
            ],
            'id_roles' => [
                'name' => 'id_roles',
                'type' => 'int',
                'constraint' => '11'
            ]
        ];

        $this->dbforge->modify_column('users', $fields);

        // Users Settings
        $fields = [
            'id_users' => [
                'name' => 'id_users',
                'type' => 'int',
                'constraint' => '11'
            ]
        ];

        $this->dbforge->modify_column('user_settings', $fields);

        // Add table constraints again.
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('appointments') . '`
            ADD CONSTRAINT `' . $this->db->dbprefix('appointments') . '_ibfk_2` FOREIGN KEY (`id_users_customer`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `' . $this->db->dbprefix('appointments') . '_ibfk_3` FOREIGN KEY (`id_services`) REFERENCES `' . $this->db->dbprefix('services') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `' . $this->db->dbprefix('appointments') . '_ibfk_4` FOREIGN KEY (`id_users_provider`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('secretaries_providers') . '`
            ADD CONSTRAINT `fk_' . $this->db->dbprefix('secretaries_providers') . '_1` FOREIGN KEY (`id_users_secretary`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `fk_' . $this->db->dbprefix('secretaries_providers') . '_2` FOREIGN KEY (`id_users_provider`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services') . '`
            ADD CONSTRAINT `' . $this->db->dbprefix('services') . '_ibfk_1` FOREIGN KEY (`id_service_categories`) REFERENCES `' . $this->db->dbprefix('service_categories') . '` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services_providers') . '`
            ADD CONSTRAINT `' . $this->db->dbprefix('services_providers') . '_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `' . $this->db->dbprefix('services_providers') . '_ibfk_2` FOREIGN KEY (`id_services`) REFERENCES `' . $this->db->dbprefix('services') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('users') . '`
            ADD CONSTRAINT `' . $this->db->dbprefix('users') . '_ibfk_1` FOREIGN KEY (`id_roles`) REFERENCES `' . $this->db->dbprefix('roles') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('user_settings') . '`
            ADD CONSTRAINT `' . $this->db->dbprefix('user_settings') . '_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // Change charset of ' . $this->db->dbprefix('secretaries_providers') . ' table for databases created with EA! 1.2.1 version
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix('secretaries_providers') . ' CONVERT TO CHARACTER SET utf8');
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        // Drop table constraints.
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('appointments') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('appointments') . '_ibfk_2`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('appointments') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('appointments') . '_ibfk_3`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('appointments') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('appointments') . '_ibfk_4`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('secretaries_providers') . '` DROP FOREIGN KEY `fk_' . $this->db->dbprefix('secretaries_providers') . '_1`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('secretaries_providers') . '` DROP FOREIGN KEY `fk_' . $this->db->dbprefix('secretaries_providers') . '_2`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services_providers') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('services_providers') . '_ibfk_1`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services_providers') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('services_providers') . '_ibfk_2`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('services') . '_ibfk_1`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('users') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('users') . '_ibfk_1`');
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('user_settings') . '` DROP FOREIGN KEY `' . $this->db->dbprefix('user_settings') . '_ibfk_1`');

        // Appointments
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'bigint',
                'constraint' => '20',
                'auto_increment' => TRUE
            ],
            'id_users_provider' => [
                'name' => 'id_users_provider',
                'type' => 'bigint',
                'constraint' => '20'
            ],
            'id_users_customer' => [
                'name' => 'id_users_customer',
                'type' => 'bigint',
                'constraint' => '20'
            ],
            'id_services' => [
                'name' => 'id_services',
                'type' => 'bigint',
                'constraint' => '20'
            ]
        ];

        $this->dbforge->modify_column('appointments', $fields);

        // Roles
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'bigint',
                'constraint' => '20',
                'auto_increment' => TRUE
            ],
            'appointments' => [
                'name' => 'appointments',
                'type' => 'bigint',
                'constraint' => '20'
            ],
            'customers' => [
                'name' => 'customers',
                'type' => 'bigint',
                'constraint' => '20'
            ],
            'services' => [
                'name' => 'services',
                'type' => 'bigint',
                'constraint' => '20'
            ],
            'users' => [
                'name' => 'users',
                'type' => 'bigint',
                'constraint' => '20'
            ],
            'system_settings' => [
                'name' => 'system_settings',
                'type' => 'bigint',
                'constraint' => '20'
            ],
            'user_settings' => [
                'name' => 'user_settings',
                'type' => 'bigint',
                'constraint' => '20'
            ]
        ];

        $this->dbforge->modify_column('roles', $fields);

        // Secretary Provider
        $fields = [
            'id_users_secretary' => [
                'name' => 'id_users_secretary',
                'type' => 'bigint',
                'constraint' => '20'
            ],
            'id_users_provider' => [
                'name' => 'id_users_provider',
                'type' => 'bigint',
                'constraint' => '20'
            ]
        ];

        $this->dbforge->modify_column('secretaries_providers', $fields);

        // Services
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'bigint',
                'constraint' => '20',
                'auto_increment' => TRUE
            ],
            'id_service_categories' => [
                'name' => 'id_service_categories',
                'type' => 'bigint',
                'constraint' => '20'
            ]
        ];

        $this->dbforge->modify_column('services', $fields);

        // Service Providers
        $fields = [
            'id_users' => [
                'name' => 'id_users',
                'type' => 'bigint',
                'constraint' => '20'
            ],
            'id_services' => [
                'name' => 'id_services',
                'type' => 'bigint',
                'constraint' => '20'
            ]
        ];

        $this->dbforge->modify_column('services_providers', $fields);

        // Service Categories
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'bigint',
                'constraint' => '20',
                'auto_increment' => TRUE
            ]
        ];

        $this->dbforge->modify_column('service_categories', $fields);

        // Settings
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'bigint',
                'constraint' => '20',
                'auto_increment' => TRUE
            ]
        ];

        $this->dbforge->modify_column('settings', $fields);

        // Users
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'bigint',
                'constraint' => '20',
                'auto_increment' => TRUE
            ],
            'id_roles' => [
                'name' => 'id_roles',
                'type' => 'bigint',
                'constraint' => '20'
            ]
        ];

        $this->dbforge->modify_column('users', $fields);

        // Users Settings
        $fields = [
            'id_users' => [
                'name' => 'id_users',
                'type' => 'bigint',
                'constraint' => '20'
            ]
        ];

        $this->dbforge->modify_column('user_settings', $fields);

        // Add database constraints.
        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('appointments') . '`
            ADD CONSTRAINT `' . $this->db->dbprefix('appointments') . '_ibfk_2` FOREIGN KEY (`id_users_customer`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `' . $this->db->dbprefix('appointments') . '_ibfk_3` FOREIGN KEY (`id_services`) REFERENCES `' . $this->db->dbprefix('services') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `' . $this->db->dbprefix('appointments') . '_ibfk_4` FOREIGN KEY (`id_users_provider`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('secretaries_providers') . '`
            ADD CONSTRAINT `fk_' . $this->db->dbprefix('secretaries_providers') . '_1` FOREIGN KEY (`id_users_secretary`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `fk_' . $this->db->dbprefix('secretaries_providers') . '_2` FOREIGN KEY (`id_users_provider`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services') . '`
            ADD CONSTRAINT `' . $this->db->dbprefix('services') . '_ibfk_1` FOREIGN KEY (`id_service_categories`) REFERENCES `' . $this->db->dbprefix('service_categories') . '` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('services_providers') . '`
            ADD CONSTRAINT `' . $this->db->dbprefix('services_providers') . '_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `' . $this->db->dbprefix('services_providers') . '_ibfk_2` FOREIGN KEY (`id_services`) REFERENCES `' . $this->db->dbprefix('services') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('users') . '`
            ADD CONSTRAINT `' . $this->db->dbprefix('users') . '_ibfk_1` FOREIGN KEY (`id_roles`) REFERENCES `' . $this->db->dbprefix('roles') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `' . $this->db->dbprefix('user_settings') . '`
            ADD CONSTRAINT `' . $this->db->dbprefix('user_settings') . '_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
    }
}
