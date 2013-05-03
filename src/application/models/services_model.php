<?php
class Services_Model extends CI_Model {
    /**
     * Class Constructor
     */
    function __construct() {
        parent::__construct();
    }
    
    /**
     * Get a specific row from the services db table.
     * 
     * @param int $service_id The record's id to be returned.
     * @return array Returns an associative array with the selected
     * record's data. Each key has the same name as the database 
     * field names.
     */
    public function get_row($service_id) {
        return $this->db->get_where('ea_services', array('id' => $service_id))->row_array();
    }
    
    /**
     * Get a specific field value from the database.
     * 
     * @param string $field_name The field name of the value to be
     * returned.
     * @param int $service_id The selected record's id.
     * @return string Returns the records value from the database.
     */
    public function get_value($field_name, $service_id) {
        return $this->db->get_where('ea_services', array('id' => $service_id))->row_array()[$field_name];
    }
    
    /**
     * This method returns all the services from the database.
     * 
     * @return array Returns an object array with all the 
     * database services.
     */
    function get_available_services() {
        return $this->db->get('ea_services')->result_array();
    }
}


/* End of file services_model.php */
/* Location: ./application/models/services_model.php */