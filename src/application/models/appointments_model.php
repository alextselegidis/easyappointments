<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Appointments_Model extends CI_Model {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add an appointment record to the database.
     * 
     * This method adds a new appointment to the database. If the 
     * appointment doesn't exists it is going to be inserted, otherwise 
     * the record is going to be updated.
     * 
     * @param array $appointment_data Associative array with the appointmet's 
     * data. Each key has the same name with the database fields.
     * @return int Returns the appointments id.
     */
    public function add($appointment_data) {
        try {
            $appointment_id = $this->exists($appointment_data);
            
            if (!$appointment_id) {
                $appointment_id = $this->insert($appointment_data);
            } else {
                $appointment_data['id'] = $appointment_id;
                $this->update($appointment_data);
            }
            
            return $appointment_id;
            
        } catch (Exception $exc) {
            echo $exc->getMessage() . '<br/><pre>' . $exc->getTraceAsString() . '</pre>';
        }
    }
    
    /**
     * Check if a particular appointment record already exists.
     * 
     * This method checks wether the given appointment already exists in 
     * the database. This method does not search with the id, but with a
     * combination of the appointments field values.
     * 
     * @param array $appointment_data Associative array with the appointment's
     * data. Each key has the same name with the database fields.
     * @return int|bool Returns the record id or FALSE if it doesn't exist.
     */
    public function exists($appointment_data) {
        $this->db->where(array(
            'start_datetime'    => $appointment_data['start_datetime'],
            'end_datetime'      => $appointment_data['end_datetime'],
            'id_users_provider' => $appointment_data['id_users_provider'],
            'id_users_customer' => $appointment_data['id_users_customer'],
            'id_services'       => $appointment_data['id_services']
        ));
        
        $result = $this->db->get('ea_appointments');
        
        return ($result->num_rows() > 0) ? $result->row()->id : FALSE;
    }
    
    /**
     * Insert a new appointment record to the database.
     * 
     * @param array $appointment_data Associative array with the appointment's
     * data. Each key has the same name with the database fields.
     * @return int Returns the id of the new record.
     */
    private function insert($appointment_data) {
        if (!$this->db->insert('ea_appointments', $appointment_data)) {
            throw new Exception('Could not insert new appointment record.');
        }
        return $this->db->insert_id();
    }
    
    /**
     * Update an existing appointment record in the database.
     * 
     * The appointment data argument should already include the record
     * id in order to process the update operation.
     * 
     * @param array $appointment_data Associative array with the appointment's
     * data. Each key has the same name with the database fields.
     * @return @return int Returns the id of the updated record.
     */
    private function update($appointment_data) {
        $this->db->where('id', $appointment_data['id']);
        if (!$this->db->update('ea_appointments', $appointment_data)) {
            throw new Exception('Could not update appointment record.');
        }
    }
    
    /**
     * Delete an existing appointment record from the database.
     * 
     * @param int $appointment_id The record id to be deleted.
     * @return bool Returns the delete operation result.
     */
    public function delete($appointment_id) {
        $this->db->where('id', $appointment_id);
        return $this->db->delete('ea_appointments');
    }
    
    /**
     * Get a specific row from the appointments table.
     * 
     * @param int $appointment_id The record's id to be returned.
     * @return array Returns an associative array with the selected
     * record's data. Each key has the same name as the database 
     * field names.
     */
    public function get_row($appointment_id) {
        return $this->db->get_where('ea_appointments', array('id' => $appointment_id))->row_array();
    }
    
    /**
     * Get a specific field value from the database.
     * 
     * @param string $field_name The field name of the value to be
     * returned.
     * @param int $appointment_id The selected record's id.
     * @return string Returns the records value from the database.
     */
    public function get_value($field_name, $appointment_id) {
        return $this->db->get_where('ea_appointments', array('id' => $appointment_id))->row_array()[$field_name];
    }
    
    /**
     * Get all, or specific records from appointment's table.
     * 
     * @example $this->Model->getBatch('id = ' . $recordId);
     * 
     * @param string $where_clause (OPTIONAL) The WHERE clause of  
     * the query to be executed. DO NOT INCLUDE 'WHERE' KEYWORD.
     * @return array Returns the rows from the database.
     */
    public function get_batch($where_clause = '') {
        if ($where_clause != '') {
            $this->db->where($where_clause);
        }
        
        return $this->db->get('ea_appointments')->result_array();
    }
}

/* End of file appointments_model.php */
/* Location: ./application/models/appointments_model.php */