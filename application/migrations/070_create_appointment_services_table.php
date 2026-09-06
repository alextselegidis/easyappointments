<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.6.0
 * ---------------------------------------------------------------------------- */

/**
 * Create the appointment_services join table.
 *
 * This table links one appointment to many services, enabling multi-service
 * (stacked) bookings. Each row maps an appointment to a single service, so an
 * appointment can have multiple rows. The appointment's total duration is the
 * sum of all linked services' durations (plus any configured gap between them).
 */
class Migration_Create_appointment_services_table extends CI_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->table_exists('appointment_services')) {
            $this->db->query('
                CREATE TABLE `ea_appointment_services` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `id_appointments` INT NOT NULL,
                    `id_services` INT NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `id_appointments` (`id_appointments`),
                    KEY `id_services` (`id_services`),
                    CONSTRAINT `ea_appointment_services_ibfk_1`
                        FOREIGN KEY (`id_appointments`) REFERENCES `ea_appointments` (`id`)
                        ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `ea_appointment_services_ibfk_2`
                        FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`)
                        ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ');
        }

        // Add the configurable gap (in minutes) between stacked services.
        $existing = $this->db->get_where('settings', ['name' => 'appointment_service_gap'])->row_array();

        if (empty($existing)) {
            $this->db->insert('settings', [
                'name' => 'appointment_service_gap',
                'value' => '15',
            ]);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `ea_appointment_services`');
    }
}
