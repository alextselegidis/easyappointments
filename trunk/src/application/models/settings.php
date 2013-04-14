<?php
class Settings extends CI_Model {
    function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * This method returns a system setting from the 
     * database.
     * 
     * @param string $name The database setting name.
     * @return string Returns the database value for 
     * the selected setting.
     */
    function getSetting($name) 
    {
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
     * 
     * @return bool Returns the operation success - failure 
     * result.
     */
    function setSetting($name, $value)
    {
        $query = $this->db->get_where('ea_settings', array('name' => $name));
        if ($query->num_rows() > 0) {
            // Update setting
            $updateData = array('value' => $value);
            $this->db->where('name', $name);
            $result = $this->db->update('ea_settings', $updateData);
        } else {
            // Insert setting
            $insertData = array(
                'name' => $name,
                'value' => $value
            );
            $result = $this->db->insert('ea_settings', $insertData);
        }
        
        return $result;
    }
}
?>