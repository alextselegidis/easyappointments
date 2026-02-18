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
 * Migration: Add data retention days setting.
 *
 * Adds a setting for automatic personal data erasure after X days.
 * When set to 0, automatic erasure is disabled.
 */
class Migration_Add_data_retention_days_setting extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        $this->db->insert('settings', [
            'name' => 'data_retention_days',
            'value' => '0',
        ]);
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        $this->db->delete('settings', ['name' => 'data_retention_days']);
    }
}
