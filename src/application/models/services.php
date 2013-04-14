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
    function getAllServices() {
        $query = $this->db->query('SELECT *');
        
        return $this->db->get('ea_services');
    }
}
?>
