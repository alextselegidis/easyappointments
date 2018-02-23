<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.0
 * ---------------------------------------------------------------------------- */

class Migration_Change_column_types extends CI_Migration {
    public function up()
    {
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

        $this->dbforge->modify_column('ea_appointments', $fields);

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

        $this->dbforge->modify_column('ea_roles', $fields);

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

        $this->dbforge->modify_column('ea_roles', $fields);

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

        $this->dbforge->modify_column('ea_services', $fields);

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

        $this->dbforge->modify_column('ea_services_providers', $fields);

        // Service Categories
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'int',
                'constraint' => '11',
                'auto_increment' => TRUE
            ]
        ];

        $this->dbforge->modify_column('ea_service_categories', $fields);

        // Settings
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'int',
                'constraint' => '11',
                'auto_increment' => TRUE
            ]
        ];

        $this->dbforge->modify_column('ea_settings', $fields);

        // Users
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'int',
                'constraint' => '11',
                'auto_increment' => TRUE
            ],
            'id_roles' => [
                'name' => 'id',
                'type' => 'int',
                'constraint' => '11'
            ]
        ];

        $this->dbforge->modify_column('ea_users', $fields);

        // Users Settings
        $fields = [
            'id_users' => [
                'name' => 'id_users',
                'type' => 'int',
                'constraint' => '11'
            ]
        ];

        $this->dbforge->modify_column('ea_user_settings', $fields);
    }

    public function down()
    {
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

        $this->dbforge->modify_column('ea_appointments', $fields);

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

        $this->dbforge->modify_column('ea_roles', $fields);

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

        $this->dbforge->modify_column('ea_roles', $fields);

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

        $this->dbforge->modify_column('ea_services', $fields);

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

        $this->dbforge->modify_column('ea_services_providers', $fields);

        // Service Categories
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'bigint',
                'constraint' => '20',
                'auto_increment' => TRUE
            ]
        ];

        $this->dbforge->modify_column('ea_service_categories', $fields);

        // Settings
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'bigint',
                'constraint' => '20',
                'auto_increment' => TRUE
            ]
        ];

        $this->dbforge->modify_column('ea_settings', $fields);

        // Users
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => 'bigint',
                'constraint' => '20',
                'auto_increment' => TRUE
            ],
            'id_roles' => [
                'name' => 'id',
                'type' => 'bigint',
                'constraint' => '20'
            ]
        ];

        $this->dbforge->modify_column('ea_users', $fields);

        // Users Settings
        $fields = [
            'id_users' => [
                'name' => 'id_users',
                'type' => 'bigint',
                'constraint' => '20'
            ]
        ];

        $this->dbforge->modify_column('ea_user_settings', $fields);
    }
}
