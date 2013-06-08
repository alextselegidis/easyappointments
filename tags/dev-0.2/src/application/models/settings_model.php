<?php
class Settings_Model extends CI_Model {
    /**
     * Class Constructor
     */
    function __construct() {
        parent::__construct();
    }
    
    /**
     * Get setting value from database.
     * 
     * This method returns a system setting from the 
     * database.
     * 
     * @expectedException InvalidArgumentException Raises whenever the 
     * $name argument is not a string, or does not exist in the database.
     * 
     * @param string $name The database setting name.
     * @return string Returns the database value for 
     * the selected setting.
     */
    function get_setting($name) {
        if (!is_string($name)) { // Check argument type.
            throw new InvalidArgumentException('$name argument is not a string : ' . $name);
        }
        
        if ($this->db->get_where('ea_settings', array('name' => $name))->num_rows() == 0) { // Check if setting exists in db.
            throw new InvalidArgumentException('$name setting does not exist in database : ' . $name);
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
     * @expectedException DatabaseException Raises whenever an error 
     * occures during the insert or the update operation.
     * @expectedException InvalidArgumentException Raises whenever 
     * the $name argument is not a string.
     * 
     * @param string $name The setting name.
     * @param type $value The setting value.
     * @return int Returns the setting database id.
     */
    function set_setting($name, $value) {
        if (!is_string($name)) {
            throw new InvalidArgumentException('$name argument is not a string : ' . $name);
        }            
        
        $query = $this->db->get_where('ea_settings', array('name' => $name));
        if ($query->num_rows() > 0) {
            // Update setting
            if (!$this->db->update('ea_settings', array('value' => $value), array('name' => $name))) {
                throw new DatabaseException('Could not update database setting.');
            }
            $setting_id = intval($this->db->get_where('ea_settings', array('name' => $name))->row()->id);
        } else {
            // Insert setting
            $insert_data = array(
                'name'  => $name,
                'value' => $value
            );
            if (!$this->db->insert('ea_settings', $insert_data)) {
                throw new DatabaseException('Could not insert database setting');
            }
            $setting_id = intval($this->db->insert_id());
        }
        
        return $setting_id;
    }
    
    /**
     * Remove a setting from the database.
     * 
     * @expectedException InvalidArgumentException Raises whenever 
     * the $name parameter is not a string.
     * 
     * @param string $name The setting name to be removed.
     * @return bool Returns the delete operation result.
     */
    function remove_setting($name) {
        if (!is_string($name)) {
            throw new InvalidArgumentException('$name is not a string : ' . $name);
        }
        
        if ($this->db->get_where('ea_settings', array('name' => $name))->num_rows() == 0) {
            return FALSE; // There is no such setting.
        }
        
        return $this->db->delete('ea_settings', array('name' => $name));
    }
}

/* End of file settings_model.php */
/* Location: ./application/models/settings_model.php */