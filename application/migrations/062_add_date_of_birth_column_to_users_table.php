<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_date_of_birth_column_to_users_table extends EA_Migration
{

    private const SETTINGS = [
        'display' => '0',
        'require' => '0',
        'label' => '',
    ];

    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('date_of_birth', 'users')) {
            $fields = [
                'date_of_birth' => [
                    'type' => 'DATE',
                    'null' => true,
                    'after' => 'id_roles',
                ],
            ];

            $this->dbforge->add_column('users', $fields);
        }

        foreach (self::SETTINGS as $name => $default_value) {
            $setting_name = $name . '_date_of_birth';

            if (!$this->db->get_where('settings', ['name' => $setting_name])->num_rows()) {
                $this->db->insert('settings', [
                    'name' => $setting_name,
                    'value' => $default_value,
                ]);
            }
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('date_of_birth', 'users')) {
            $this->dbforge->drop_column('users', 'date_of_birth');
        }

        foreach (self::SETTINGS as $name => $default_value) {
            $setting_name = $name . '_date_of_birth';

            if ($this->db->get_where('settings', ['name' => $setting_name])->num_rows()) {
                $this->db->delete('settings', ['name' => $setting_name]);
            }
        }
    }
}
