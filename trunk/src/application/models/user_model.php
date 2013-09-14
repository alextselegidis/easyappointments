<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed.'); 

/**
 * Contains current user's methods.
 */
class User_Model extends CI_Model {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Returns the user settings from the database.
     * 
     * @param numeric $user_id User record id of which the settings will be returned.
     * @return array Returns an array with user settings.
     */
    public function get_settings($user_id) {
        $settings = $this->db->get_where('ea_user_settings', array('id_users' => $user_id))->row_array();
        unset($settings['id_users']);
        return $settings;
    }
    
    /**
     * This method saves the user settings into the database.
     * 
     * @param array $settings Contains the current users settings.
     * @param numeric $user_id User record id of the settings.
     * @return bool Returns the operation result.
     */
    public function save_settings($settings, $user_id) {
        $settings['id_users'] = $user_id;
        $this->db->where('id_users', $user_id);
        return $this->db->update('ea_user_settings', $settings);
    }
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */