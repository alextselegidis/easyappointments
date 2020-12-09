<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Settings Model
 *
 * @package Models
 */
class Settings_model extends EA_Model {
    /**
     * Get setting value from database.
     *
     * This method returns a system setting from the database.
     *
     * @param string $name The database setting name.
     *
     * @return string Returns the database value for the selected setting.
     *
     * @throws Exception If the $name argument is invalid.
     * @throws Exception If the requested $name setting does not exist in the database.
     */
    public function get_setting($name)
    {
        if ( ! is_string($name))
        {
            // Check argument type.
            throw new Exception('$name argument is not a string: ' . $name);
        }

        if ($this->db->get_where('settings', ['name' => $name])->num_rows() == 0)
        {
            // Check if setting exists in db.
            throw new Exception('$name setting does not exist in database: ' . $name);
        }

        $query = $this->db->get_where('settings', ['name' => $name]);
        $setting = $query->num_rows() > 0 ? $query->row() : '';
        return $setting->value;
    }

    /**
     * This method sets the value for a specific setting on the database.
     *
     * If the setting doesn't exist, it is going to be created, otherwise updated.
     *
     * @param string $name The setting name.
     * @param string $value The setting value.
     *
     * @return int Returns the setting database id.
     *
     * @throws Exception If $name argument is invalid.
     * @throws Exception If the save operation fails.
     */
    public function set_setting($name, $value)
    {
        if ( ! is_string($name))
        {
            throw new Exception('$name argument is not a string: ' . $name);
        }

        $query = $this->db->get_where('settings', ['name' => $name]);

        if ($query->num_rows() > 0)
        {
            // Update setting
            if ( ! $this->db->update('settings', ['value' => $value], ['name' => $name]))
            {
                throw new Exception('Could not update database setting.');
            }
            $setting_id = (int)$this->db->get_where('settings', ['name' => $name])->row()->id;
        }
        else
        {
            // Insert setting
            $insert_data = [
                'name' => $name,
                'value' => $value
            ];

            if ( ! $this->db->insert('settings', $insert_data))
            {
                throw new Exception('Could not insert database setting');
            }

            $setting_id = (int)$this->db->insert_id();
        }

        return $setting_id;
    }

    /**
     * Remove a setting from the database.
     *
     * @param string $name The setting name to be removed.
     *
     * @return bool Returns the delete operation result.
     *
     * @throws Exception If the $name argument is invalid.
     */
    public function remove_setting($name)
    {
        if ( ! is_string($name))
        {
            throw new Exception('$name is not a string: ' . $name);
        }

        if ($this->db->get_where('settings', ['name' => $name])->num_rows() == 0)
        {
            return FALSE; // There is no such setting.
        }

        return $this->db->delete('settings', ['name' => $name]);
    }

    /**
     * Saves all the system settings into the database.
     *
     * This method is useful when trying to save all the system settings at once instead of
     * saving them one by one.
     *
     * @param array $settings Contains all the system settings.
     *
     * @return bool Returns the save operation result.
     *
     * @throws Exception When the update operation won't work for a specific setting.
     */
    public function save_settings($settings)
    {
        if ( ! is_array($settings))
        {
            throw new Exception('$settings argument is invalid: ' . print_r($settings, TRUE));
        }

        foreach ($settings as $setting)
        {
            $this->db->where('name', $setting['name']);
            if ( ! $this->db->update('settings', ['value' => $setting['value']]))
            {
                throw new Exception('Could not save setting (' . $setting['name']
                    . ' - ' . $setting['value'] . ')');
            }
        }

        return TRUE;
    }

    /**
     * Returns all the system settings at once.
     *
     * @return array Array of all the system settings stored in the 'settings' table.
     */
    public function get_settings()
    {
        return $this->db->get('settings')->result_array();
    }
}
