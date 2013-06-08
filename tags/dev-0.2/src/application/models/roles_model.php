<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Roles_Model extends CI_Model {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the record id of a particular role.
     * 
     * @param string $role_slug The selected role slug. Slugs are 
     * defined in the "application/config/constants.php" file.
     * @return int Returns the database id of the roles record.
     */
    public function get_role_id($role_slug) {
        return $this->db->get_where('ea_roles', array('slug' => $role_slug))->row()->id;
    }
}

/* End of file roles_model.php */
/* Location: ./application/models/roles_model.php */