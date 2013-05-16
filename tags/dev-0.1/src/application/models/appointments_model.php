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
     * @expectedException ValidationException
     * @expectedException DatabaseException
     * 
     * @param array $appointment_data Associative array with the appointmet's 
     * data. Each key has the same name with the database fields.
     * @return int Returns the appointments id.
     */
    public function add($appointment_data) {
        // Validate the appointment data before doing anything.
        if (!$this->validate_data($appointment_data)) {
            throw new ValidationException('Appointment data are not valid');
        }

        // Insert or update the appointment data.
        if (!$this->exists($appointment_data)) {
            $appointment_data['id'] = $this->insert($appointment_data);
        } else {
            $appointment_data['id'] = $this->update($appointment_data);
        }

        return $appointment_data['id'];
    }
    
    /**
     * Check if a particular appointment record already exists.
     * 
     * This method checks wether the given appointment already exists  
     * in the database. It doesn't search with the id, but by using the 
     * following fields: "start_datetime", "end_datetime", "id_users_provider",
     * "id_users_customer", "id_services".
     * 
     * @expectedException InvalidArgumentException When the $appointment_data
     * array does not contain the necessary field.
     * 
     * @param array $appointment_data Associative array with the appointment's
     * data. Each key has the same name with the database fields.
     * @return bool Returns wether the record exists or not.
     */
    public function exists($appointment_data) {   
        if (!isset($appointment_data['start_datetime'])
                || !isset($appointment_data['end_datetime'])
                || !isset($appointment_data['id_users_provider'])
                || !isset($appointment_data['id_users_customer'])
                || !isset($appointment_data['id_services'])) {
            throw new InvalidArgumentException('Not all appointment field values are provided : ' . print_r($appointment_data, TRUE));
        }
        
        $num_rows = $this->db->get_where('ea_appointments', array(
                            'start_datetime' => $appointment_data['start_datetime'],
                            'end_datetime' => $appointment_data['end_datetime'],
                            'id_users_provider' => $appointment_data['id_users_provider'],
                            'id_users_customer' => $appointment_data['id_users_customer'],
                            'id_services' => $appointment_data['id_services'],
                        ))->num_rows();
        
        return ($num_rows > 0) ? TRUE : FALSE;
    }
    
    /**
     * Insert a new appointment record to the database.
     * 
     * @expectedException DatabaseException
     * 
     * @param array $appointment_data Associative array with the appointment's
     * data. Each key has the same name with the database fields.
     * @return int Returns the id of the new record.
     */
    private function insert($appointment_data) {
        if (!$this->db->insert('ea_appointments', $appointment_data)) {
            throw new DatabaseException('Could not insert appointment record.');
        }
        return intval($this->db->insert_id());
    }
    
    /**
     * Update an existing appointment record in the database.
     * 
     * The appointment data argument should already include the record
     * id in order to process the update operation.
     * 
     * @param array $appointment_data Associative array with the appointment's
     * data. Each key has the same name with the database fields.
     * @return int Returns the id of the updated record.
     */
    private function update($appointment_data) {
        if (!isset($appointment_data['id'])) {
            $appointment_data['id'] = $this->find_record_id($appointment_data);
        }
        
        $this->db->where('id', $appointment_data['id']);
        if (!$this->db->update('ea_appointments', $appointment_data)) {
            throw new DatabaseException('Could not update appointment record.');
        }
        
        return intval($appointment_data['id']);
    }
    
    /**
     * Find the database id of an appointment record. 
     * 
     * The appointment data should include the following fields in order to 
     * get the unique id from the database: start_datetime, end_datetime, 
     * id_users_provider, id_users_customer, id_services.
     * 
     * <strong>IMPORTANT!</strong> The record must already exists in the 
     * database, otherwise an exception is raised.
     * 
     * @expectedException DatabaseException
     * 
     * @param array $appointment_data Array with the appointment data. The 
     * keys of the array should have the same names as the db fields.
     * @return int Returns the id.
     */
    public function find_record_id($appointment_data) {
        $this->db->where(array(
            'start_datetime'    => $appointment_data['start_datetime'],
            'end_datetime'      => $appointment_data['end_datetime'],
            'id_users_provider' => $appointment_data['id_users_provider'],
            'id_users_customer' => $appointment_data['id_users_customer'],
            'id_services'       => $appointment_data['id_services']
        ));
        
        $result = $this->db->get('ea_appointments');

        if ($result->num_rows() == 0) {
            throw new DatabaseException('Could not find appointment record id.');
        }
        
        return $result->row()->id;
    }
    
    /**
     * Validate appointment data before the insert or 
     * update operation is executed.
     * 
     * @param array $appointment_data Contains the appointment data.
     * @return bool Returns the validation result.
     */
    public function validate_data($appointment_data) {
        $this->load->helper('data_validation');
        
        try {
            // Check if appointment dates are valid.
            if (!validate_mysql_datetime($appointment_data['start_datetime'])) {
                throw new Exception('Appointment start datetime is invalid.');    
            }
            
            if (!validate_mysql_datetime($appointment_data['end_datetime'])) {
                throw new Exception('Appointment end datetime is invalid.');
            }

            // Check if the provider's id is valid. 
            $num_rows = $this->db
                                ->select('*')
                                ->from('ea_users')
                                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                                ->where('ea_users.id', $appointment_data['id_users_provider'])
                                ->where('ea_roles.slug', DB_SLUG_PROVIDER)
                                ->get()->num_rows();
            if ($num_rows == 0) {
                throw new Exception('Appointment provider id is invalid.');
            }
            
            // Check if the customer's id is valid.
            $num_rows = $this->db
                                ->select('*')
                                ->from('ea_users')
                                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                                ->where('ea_users.id', $appointment_data['id_users_customer'])
                                ->where('ea_roles.slug', DB_SLUG_CUSTOMER)
                                ->get()->num_rows();
            if ($num_rows == 0) {
                throw new Exception('Appointment customer id is invalid.');
            }
            
            // Check if the service id is valid.
            $num_rows = $this->db->get_where('ea_services', array('id' => $appointment_data['id_services']))->num_rows();
            if ($num_rows == 0) {
                throw new Exception('Appointment customer id is invalid.');
            }
            
            return TRUE;
        } catch (Exception $exc) {
            return FALSE;
        }
    }
    
    /**
     * Delete an existing appointment record from the database.
     * 
     * @expectedException InvalidArgumentException Raises when the $appointment_id
     * is not an integer.
     * 
     * @param int $appointment_id The record id to be deleted.
     * @return bool Returns the delete operation result.
     */
    public function delete($appointment_id) {
        if (!is_int($appointment_id)) { 
            throw new InvalidArgumentException('Invalid argument type $appointment_id : ' . $appointment_id);
        }
        
        $num_rows = $this->db->get_where('ea_appointments', array('id' => $appointment_id))->num_rows();
        
        if ($num_rows == 0) {
            return FALSE; // Record does not exist.
        }
        
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
        if (!is_int($appointment_id)) {
            throw new InvalidArgumentException('Invalid argument given. Expected integer for the $appointment_id : ' . $appointment_id);
        }
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
        if (!is_int($appointment_id)) {
            throw new InvalidArgumentException('Invalid argument given, expected integer for the $appointment_id : ' . $appointment_id);
        }
        
        if (!is_string($field_name)) {
            throw new InvalidArgumentException('Invalid argument given, expected string for the $field_name : ' . $field_name);
        }
        
        if ($this->db->get_where('ea_appointments', array('id' => $appointment_id))->num_rows() == 0) {
            throw new InvalidArgumentException('The record with the provided id does not exist in the database : ' . $appointment_id);
        }
        
        $row_data = $this->db->get_where('ea_appointments', array('id' => $appointment_id))->row_array();
        
        if (!isset($row_data[$field_name])) {
            throw new InvalidArgumentException('The given field name does not exist in the database : ' . $field_name);
        }
        
        return $row_data[$field_name];
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