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
     * @param string $name The database setting name.
     * @return string Returns the database value for 
     * the selected setting.
     */
    function get_setting($name) {
        $query = $this->db->get_where('ea_settings', array('name' => $name));
        $setting = ($query->num_rows() > 0) ? $query->row() : '';
        return $setting->value;
    }
    
    /**
     * This method sets the value for a specific setting 
     * on the database. If the setting doesn't exist, it
     * is going to be created, otherwise updated.
     * 
     * @param string $name The setting name.
     * @param type $value The setting value.
     * @return bool Returns the operation success - failure 
     * result.
     */
    function set_setting($name, $value) {
        $query = $this->db->get_where('ea_settings', array('name' => $name));
        if ($query->num_rows() > 0) {
            // Update setting
            $update_data = array('value' => $value);
            $this->db->where('name', $name);
            $result = $this->db->update('ea_settings', $update_data);
        } else {
            // Insert setting
            $insert_data = array(
                'name'  => $name,
                'value' => $value
            );
            $result = $this->db->insert('ea_settings', $insert_data);
        }
        
        return $result;
    }
}


/* End of file settings_model.php */
/* Location: ./application/models/settings_model.php */