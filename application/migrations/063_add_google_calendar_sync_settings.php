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

class Migration_Add_google_calendar_sync_settings extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        // Check if settings already exist before migrating from config
        $google_sync_feature_exists =
            $this->db->get_where('settings', ['name' => 'google_sync_feature'])->num_rows() > 0;

        if (!$google_sync_feature_exists) {
            // Migrate google_sync_feature from config
            $google_sync_feature = defined('Config::GOOGLE_SYNC_FEATURE') ? Config::GOOGLE_SYNC_FEATURE : false;

            $this->db->insert('settings', [
                'name' => 'google_sync_feature',
                'value' => $google_sync_feature ? '1' : '0',
            ]);
        }

        $google_client_id_exists = $this->db->get_where('settings', ['name' => 'google_client_id'])->num_rows() > 0;

        if (!$google_client_id_exists) {
            // Migrate google_client_id from config
            $google_client_id = defined('Config::GOOGLE_CLIENT_ID') ? Config::GOOGLE_CLIENT_ID : '';

            $this->db->insert('settings', [
                'name' => 'google_client_id',
                'value' => $google_client_id,
            ]);
        }

        $google_client_secret_exists =
            $this->db->get_where('settings', ['name' => 'google_client_secret'])->num_rows() > 0;

        if (!$google_client_secret_exists) {
            // Migrate google_client_secret from config
            $google_client_secret = defined('Config::GOOGLE_CLIENT_SECRET') ? Config::GOOGLE_CLIENT_SECRET : '';

            $this->db->insert('settings', [
                'name' => 'google_client_secret',
                'value' => $google_client_secret,
            ]);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        $this->db->delete('settings', ['name' => 'google_sync_feature']);
        $this->db->delete('settings', ['name' => 'google_client_id']);
        $this->db->delete('settings', ['name' => 'google_client_secret']);
    }
}
