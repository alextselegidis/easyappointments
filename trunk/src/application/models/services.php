<?php
class Services extends CI_Model {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * This method returns all the services from the database.
     * 
     * @return array Returns an object array with all the 
     * database services.
     */
    function getAvailableServices() {
        return $this->db->get('ea_services')->result_array();
    }
}
?>
