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
        if (!is_numeric($service_id)) {
            throw new InvalidArgumentException('$service_id argument is not an integer : ' . $service_id);
        }
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
        if (!is_numeric($service_id)) {
            throw new InvalidArgumentException('Invalid argument provided as $service_id : ' . $service_id);
        }
        
        if (!is_string($field_name)) {
            throw new InvalidArgumentException('$field_name argument is not a string : ' . $field_name);
        }
        
        if ($this->db->get_where('ea_services', array('id' => $service_id))->num_rows() == 0) {
            throw new InvalidArgumentException('The record with the $service_id argument does not exist in the database : ' . $service_id);
        }
        
        $row_data = $this->db->get_where('ea_services', array('id' => $service_id))->row_array();
        if (!isset($row_data[$field_name])) {
            throw new InvalidArgumentException('The given $field_name argument does not exist in the database : ' . $field_name);
        }
        
        return $this->db->get_where('ea_services', array('id' => $service_id))->row_array()[$field_name];
    }
    
    /**
     * Get all, or specific records from service's table.
     * 
     * @example $this->Model->getBatch('id = ' . $recordId);
     * 
     * @param string $whereClause (OPTIONAL) The WHERE clause of  
     * the query to be executed. DO NOT INCLUDE 'WHERE' KEYWORD.
     * @return array Returns the rows from the database.
     */
    public function get_batch($where_clause = NULL) {
        if ($where_clause != NULL) {
            $this->db->where($where_clause);
        }
        
        return $this->db->get('ea_services')->result_array();
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