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
 * Class Migration_Remove_prefix_from_fkey_constraints
 *
 * @property CI_Loader load
 * @property CI_DB_query_builder db
 * @property CI_DB_forge dbforge
 * @property Settings_Model settings_model
 */
class Migration_Remove_prefix_from_fkey_constraints extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        // Drop table constraints.
        $this->db->query('ALTER TABLE ea_appointments DROP FOREIGN KEY ea_appointments_ibfk_2');
        $this->db->query('ALTER TABLE ea_appointments DROP FOREIGN KEY ea_appointments_ibfk_3');
        $this->db->query('ALTER TABLE ea_appointments DROP FOREIGN KEY ea_appointments_ibfk_4');
        $this->db->query('ALTER TABLE ea_secretaries_providers DROP FOREIGN KEY fk_ea_secretaries_providers_1');
        $this->db->query('ALTER TABLE ea_secretaries_providers DROP FOREIGN KEY fk_ea_secretaries_providers_2');
        $this->db->query('ALTER TABLE ea_services_providers DROP FOREIGN KEY ea_services_providers_ibfk_1');
        $this->db->query('ALTER TABLE ea_services_providers DROP FOREIGN KEY ea_services_providers_ibfk_2');
        $this->db->query('ALTER TABLE ea_services DROP FOREIGN KEY ea_services_ibfk_1');
        $this->db->query('ALTER TABLE ea_users DROP FOREIGN KEY ea_users_ibfk_1');
        $this->db->query('ALTER TABLE ea_user_settings DROP FOREIGN KEY ea_user_settings_ibfk_1');

        // Add table constraints again without the "ea" prefix.
        $this->db->query('ALTER TABLE `ea_appointments`
            ADD CONSTRAINT `appointments_users_customer` FOREIGN KEY (`id_users_customer`) REFERENCES `ea_users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD CONSTRAINT `appointments_services` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD CONSTRAINT `appointments_users_provider` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_secretaries_providers`
            ADD CONSTRAINT `secretaries_users_secretary` FOREIGN KEY (`id_users_secretary`) REFERENCES `ea_users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD CONSTRAINT `secretaries_users_provider` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_services`
            ADD CONSTRAINT `services_service_categories` FOREIGN KEY (`id_service_categories`) REFERENCES `ea_service_categories` (`id`)
            ON DELETE SET NULL
            ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_services_providers`
            ADD CONSTRAINT `services_providers_users_provider` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD CONSTRAINT `services_providers_services` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_users`
            ADD CONSTRAINT `users_roles` FOREIGN KEY (`id_roles`) REFERENCES `ea_roles` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE');

        $this->db->query('ALTER TABLE `ea_user_settings`
            ADD CONSTRAINT `user_settings_users` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE');
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        // Drop table constraints.
        $this->db->query('ALTER TABLE ea_appointments DROP FOREIGN KEY appointments_services');
        $this->db->query('ALTER TABLE ea_appointments DROP FOREIGN KEY appointments_users_customer');
        $this->db->query('ALTER TABLE ea_appointments DROP FOREIGN KEY appointments_users_provider');
        $this->db->query('ALTER TABLE ea_secretaries_providers DROP FOREIGN KEY secretaries_users_secretary');
        $this->db->query('ALTER TABLE ea_secretaries_providers DROP FOREIGN KEY secretaries_users_provider');
        $this->db->query('ALTER TABLE ea_services_providers DROP FOREIGN KEY services_providers_users_provider');
        $this->db->query('ALTER TABLE ea_services_providers DROP FOREIGN KEY services_providers_services');
        $this->db->query('ALTER TABLE ea_services DROP FOREIGN KEY services_service_categories');
        $this->db->query('ALTER TABLE ea_users DROP FOREIGN KEY users_roles');
        $this->db->query('ALTER TABLE ea_user_settings DROP FOREIGN KEY user_settings_users');

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
    }
}
