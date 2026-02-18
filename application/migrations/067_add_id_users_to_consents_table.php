<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Migration: Add id_users column to consents table.
 *
 * Links consents to users by ID instead of storing personal data directly,
 * improving GDPR compliance by allowing proper data anonymization on deletion.
 */
class Migration_Add_id_users_to_consents_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('id_users', 'consents')) {
            $this->dbforge->add_column('consents', [
                'id_users' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                    'after' => 'id',
                ],
            ]);

            $this->db->query('
                ALTER TABLE `' . $this->db->dbprefix('consents') . '`
                ADD CONSTRAINT `consents_users` FOREIGN KEY (`id_users`) REFERENCES `' . $this->db->dbprefix('users') . '` (`id`)
                ON DELETE SET NULL
            ');
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('id_users', 'consents')) {
            $this->db->query('ALTER TABLE `' . $this->db->dbprefix('consents') . '` DROP FOREIGN KEY `consents_users`');
            $this->dbforge->drop_column('consents', 'id_users');
        }
    }
}
