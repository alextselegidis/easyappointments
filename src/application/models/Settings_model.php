<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed.');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Settings Model
 *
 * @package Models
 */
class Settings_Model extends CI_Model {
    /**
     * Get setting value from database.
     *
     * This method returns a system setting from the database.
     *
     * @expectedException Exception
     *
     * @param string $name The database setting name.
     *
     * @return string Returns the database value for the selected setting.
     */
    public function get_setting($name) {
        if (!is_string($name)) { // Check argument type.
            throw new Exception('$name argument is not a string: ' . $name);
        }

        if ($this->db->get_where('ea_settings', array('name' => $name))->num_rows() == 0) { // Check if setting exists in db.
            throw new Exception('$name setting does not exist in database: ' . $name);
        }

        $query = $this->db->get_where('ea_settings', array('name' => $name));
        $setting = ($query->num_rows() > 0) ? $query->row() : '';
        return $setting->value;
    }

    /**
     * This method sets the value for a specific setting
     * on the database. If the setting doesn't exist, it
     * is going to be created, otherwise updated.
     *
     * @expectedException Exception
     *
     * @param string $name The setting name.
     * @param type $value The setting value.
     *
     * @return int Returns the setting database id.
     */
    public function set_setting($name, $value) {
        if (!is_string($name)) {
            throw new Exception('$name argument is not a string: ' . $name);
        }

        $query = $this->db->get_where('ea_settings', array('name' => $name));
        if ($query->num_rows() > 0) {
            // Update setting
            if (!$this->db->update('ea_settings', array('value' => $value), array('name' => $name))) {
                throw new Exception('Could not update database setting.');
            }
            $setting_id = intval($this->db->get_where('ea_settings', array('name' => $name))->row()->id);
        } else {
            // Insert setting
            $insert_data = array(
                'name'  => $name,
                'value' => $value
            );
            if (!$this->db->insert('ea_settings', $insert_data)) {
                throw new Exception('Could not insert database setting');
            }
            $setting_id = intval($this->db->insert_id());
        }

        return $setting_id;
    }

    /**
     * Remove a setting from the database.
     *
     * @expectedException Exception
     *
     * @param string $name The setting name to be removed.
     *
     * @return bool Returns the delete operation result.
     */
    public function remove_setting($name) {
        if (!is_string($name)) {
            throw new Exception('$name is not a string: ' . $name);
        }

        if ($this->db->get_where('ea_settings', array('name' => $name))->num_rows() == 0) {
            return FALSE; // There is no such setting.
        }

        return $this->db->delete('ea_settings', array('name' => $name));
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
    public function save_settings($settings) {
        if (!is_array($settings)) {
            throw new Exception('$settings argument is invalid: '. print_r($settings, TRUE));
        }

        foreach($settings as $setting) {
            $this->db->where('name', $setting['name']);
            if (!$this->db->update('ea_settings', array('value' => $setting['value']))) {
                throw new Exception('Could not save setting (' . $setting['name']
                        . ' - ' . $setting['value'] . ')');
            }
        }

        return TRUE;
    }

    /**
     * Returns all the system settings at once.
     *
     * @return array Array of all the system settings stored in the 'ea_settings' table.
     */
    public function get_settings() {
        return $this->db->get('ea_settings')->result_array();
    }
}

/* End of file settings_model.php */
/* Location: ./application/models/settings_model.php */
