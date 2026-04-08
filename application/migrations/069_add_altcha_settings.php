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

class Migration_Add_altcha_settings extends CI_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        $settings = [
            [
                'name' => 'altcha_enabled',
                'value' => '0',
            ],
            [
                'name' => 'altcha_hmac_key',
                'value' => '',
            ],
            [
                'name' => 'altcha_max_number',
                'value' => '100000',
            ],
            [
                'name' => 'altcha_expires',
                'value' => '300',
            ],
        ];

        foreach ($settings as $setting) {
            $existing = $this->db->get_where('settings', ['name' => $setting['name']])->row_array();

            if (empty($existing)) {
                $this->db->insert('settings', $setting);
            }
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        $setting_names = ['altcha_enabled', 'altcha_hmac_key', 'altcha_max_number', 'altcha_expires'];

        foreach ($setting_names as $name) {
            $this->db->delete('settings', ['name' => $name]);
        }
    }
}
