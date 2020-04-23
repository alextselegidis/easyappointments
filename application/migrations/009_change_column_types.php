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
 * @property CI_DB_query_builder db
 * @property CI_DB_forge dbforge
 */
class Migration_Change_column_types extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        // Drop table constraints.
        $this->db->query('ALTER TABLE `ea_appointments` DROP FOREIGN KEY `ea_appointments_ibfk_2`');
        $this->db->query('ALTER TABLE `ea_appointments` DROP FOREIGN KEY `ea_appointments_ibfk_3`');
        $this->db->query('ALTER TABLE `ea_appointments` DROP FOREIGN KEY `ea_appointments_ibfk_4`');
        $this->db->query('ALTER TABLE `ea_secretaries_providers` DROP FOREIGN KEY `fk_ea_secretaries_providers_1`');
        $this->db->query('ALTER TABLE `ea_secretaries_providers` DROP FOREIGN KEY `fk_ea_secretaries_providers_2`');
        $this->db->query('ALTER TABLE `ea_services_providers` DROP FOREIGN KEY `ea_services_providers_ibfk_1`');
        $this->db->query('ALTER TABLE `ea_services_providers` DROP FOREIGN KEY `ea_services_providers_ibfk_2`');
        $this->db->query('ALTER TABLE `ea_services` DROP FOREIGN KEY `ea_services_ibfk_1`');
        $this->db->query('ALTER TABLE `ea_users` DROP FOREIGN KEY `ea_users_ibfk_1`');
        $this->db->query('ALTER TABLE `ea_user_settings` DROP FOREIGN KEY `ea_user_settings_ibfk_1`');

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
        $this->db->query('ALTER TABLE `ea_appointments`
            ADD CONSTRAINT `ea_appointments_ibfk_2` FOREIGN KEY (`id_users_customer`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `ea_appointments_ibfk_3` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `ea_appointments_ibfk_4` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_secretaries_providers`
            ADD CONSTRAINT `fk_ea_secretaries_providers_1` FOREIGN KEY (`id_users_secretary`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `fk_ea_secretaries_providers_2` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_services`
            ADD CONSTRAINT `ea_services_ibfk_1` FOREIGN KEY (`id_service_categories`) REFERENCES `ea_service_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_services_providers`
            ADD CONSTRAINT `ea_services_providers_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `ea_services_providers_ibfk_2` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_users`
            ADD CONSTRAINT `ea_users_ibfk_1` FOREIGN KEY (`id_roles`) REFERENCES `ea_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_user_settings`
            ADD CONSTRAINT `ea_user_settings_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // Change charset of ea_secretaries_providers table for databases created with EA! 1.2.1 version
        $this->db->query('ALTER TABLE ea_secretaries_providers CONVERT TO CHARACTER SET utf8');
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        // Drop table constraints.
        $this->db->query('ALTER TABLE `ea_appointments` DROP FOREIGN KEY `ea_appointments_ibfk_2`');
        $this->db->query('ALTER TABLE `ea_appointments` DROP FOREIGN KEY `ea_appointments_ibfk_3`');
        $this->db->query('ALTER TABLE `ea_appointments` DROP FOREIGN KEY `ea_appointments_ibfk_4`');
        $this->db->query('ALTER TABLE `ea_secretaries_providers` DROP FOREIGN KEY `fk_ea_secretaries_providers_1`');
        $this->db->query('ALTER TABLE `ea_secretaries_providers` DROP FOREIGN KEY `fk_ea_secretaries_providers_2`');
        $this->db->query('ALTER TABLE `ea_services_providers` DROP FOREIGN KEY `ea_services_providers_ibfk_1`');
        $this->db->query('ALTER TABLE `ea_services_providers` DROP FOREIGN KEY `ea_services_providers_ibfk_2`');
        $this->db->query('ALTER TABLE `ea_services` DROP FOREIGN KEY `ea_services_ibfk_1`');
        $this->db->query('ALTER TABLE `ea_users` DROP FOREIGN KEY `ea_users_ibfk_1`');
        $this->db->query('ALTER TABLE `ea_user_settings` DROP FOREIGN KEY `ea_user_settings_ibfk_1`');

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
        $this->db->query('ALTER TABLE `ea_appointments`
            ADD CONSTRAINT `ea_appointments_ibfk_2` FOREIGN KEY (`id_users_customer`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `ea_appointments_ibfk_3` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `ea_appointments_ibfk_4` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_secretaries_providers`
            ADD CONSTRAINT `fk_ea_secretaries_providers_1` FOREIGN KEY (`id_users_secretary`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `fk_ea_secretaries_providers_2` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_services`
            ADD CONSTRAINT `ea_services_ibfk_1` FOREIGN KEY (`id_service_categories`) REFERENCES `ea_service_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_services_providers`
            ADD CONSTRAINT `ea_services_providers_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `ea_services_providers_ibfk_2` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_users`
            ADD CONSTRAINT `ea_users_ibfk_1` FOREIGN KEY (`id_roles`) REFERENCES `ea_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_user_settings`
            ADD CONSTRAINT `ea_user_settings_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
    }
}
