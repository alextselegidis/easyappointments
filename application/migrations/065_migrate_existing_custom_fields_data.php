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

class Migration_Migrate_existing_custom_fields_data extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        // Migrate existing custom field configurations to the new custom_fields table
        for ($i = 1; $i <= 5; $i++) {
            $field_name = 'custom_field_' . $i;
            $display_setting = $this->db
                ->get_where('settings', ['name' => 'display_' . $field_name])
                ->row_array();
            $require_setting = $this->db
                ->get_where('settings', ['name' => 'require_' . $field_name])
                ->row_array();
            $label_setting = $this->db->get_where('settings', ['name' => 'label_' . $field_name])->row_array();

            // Only migrate if the field is displayed
            if ($display_setting && $display_setting['value'] == '1') {
                $label = $label_setting['value'] ?? 'Custom Field #' . $i;
                $required = ($require_setting['value'] ?? '0') == '1' ? 1 : 0;

                // Determine column based on field number (alternate between columns)
                $display_column = ($i % 2 == 1) ? 1 : 2;

                // Insert into custom_fields table
                $custom_field_data = [
                    'name' => $field_name,
                    'label' => $label,
                    'type' => 'text',
                    'required' => $required,
                    'display_column' => $display_column,
                    'sort_order' => $i,
                    'active' => 1,
                    'create_datetime' => date('Y-m-d H:i:s'),
                    'update_datetime' => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('custom_fields', $custom_field_data);
                $custom_field_id = $this->db->insert_id();

                // Migrate existing values from users table to custom_field_values table
                $users_with_values = $this->db
                    ->select("id, $field_name")
                    ->from('users')
                    ->where("$field_name IS NOT NULL")
                    ->where("$field_name !=", '')
                    ->get()
                    ->result_array();

                foreach ($users_with_values as $user) {
                    $value_data = [
                        'id_custom_fields' => $custom_field_id,
                        'id_users' => $user['id'],
                        'value' => $user[$field_name],
                        'create_datetime' => date('Y-m-d H:i:s'),
                        'update_datetime' => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('custom_field_values', $value_data);
                }
            }
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        // Remove migrated custom fields (those with names custom_field_1 through custom_field_5)
        for ($i = 1; $i <= 5; $i++) {
            $field_name = 'custom_field_' . $i;
            $this->db->where('name', $field_name)->delete('custom_fields');
        }

        // Note: We don't restore values back to users table as this would be destructive
        // The original columns still exist, so data is not lost
    }
}
