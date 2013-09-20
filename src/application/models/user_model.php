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
        $user = $this->db->get_where('ea_users', array('id' => $user_id))->row_array();
        $user['settings'] = $this->db->get_where('ea_user_settings', array('id_users' => $user_id))->row_array();
        unset($user['settings']['id_users']);
        return $user;
    }
    
    /**
     * This method saves the user settings into the database.
     * 
     * @param array $user Contains the current users settings.
     * @return bool Returns the operation result.
     */
    public function save_settings($user) {
        $user_settings = $user['settings'];
        $user_settings['id_users'] = $user['id'];
        unset($user['settings']);
        
        if (!$this->db->update('ea_users', $user, array('id' => $user['id']))) {
            return FALSE;
        }
        
        if (!$this->db->update('ea_user_settings', $user_settings, array('id_users' => $user['id']))) {
            return FALSE;
        }
        
        return TRUE;
    }
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */