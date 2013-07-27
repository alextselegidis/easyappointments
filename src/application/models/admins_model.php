<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed.'); 

/**
 * Admins_Model Class
 * 
 * Handles the database actions for admin users management.
 * 
 * Data Structure: 
 *      'fist_name'
 *      'last_name' (required)
 *      'email' (required)
 *      'mobile_number'
 *      'phone_number' (required)
 *      'address'
 *      'city'
 *      'state'
 *      'zip_code'
 *      'notes'
 *      'id_roles'
 */
class Admins_Model extends CI_Model {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add (insert or update) a admin user record into database.
     * 
     * @param array $admin Contains the admin user data.
     * @return int Returns the record id.
     * @throws Exception When the admin data are invalid (see validate() method).
     */
    public function add($admin) {
        if (!$this->validate($admin)) {
            throw new Exception('Admin data are invalid: ' . print_r($admin, TRUE));
        }
        
        if ($this->exists($admin) && !isset($admin['id'])) {
            $admin['id'] = $this->find_record_id($admin);
        }
        
        if (!isset($admin['id'])) {
            $admin['id'] = $this->insert($admin);
        } else {
            $admin['id'] = $this->update($admin);
        }
        
        return intval($admin);
    }
    
    /**
     * Check whether a particular admin record exists in the database.
     * 
     * @param array $admin Contains the admin data. The 'email' value is required to be present
     * at the moment.
     * @return bool Returns whether the record exists or not.
     * @throws Exception When the 'email' value is not present on the $admin argument.
     */
    public function exists($admin) {
        if (!isset($admin['email'])) {
            throw new Exception('Admin email is not provided: ' . print_r($admin, TRUE));
        }
        
        // This method shouldn't depend on another method of this class.
        $num_rows = $this->db
                ->select('*')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_users.email', $provider['email'])
                ->where('ea_roles.slug', DB_SLUG_ADMIN)
                ->get()->num_rows();
        
        return ($num_rows > 0) ? TRUE : FALSE;
    }
    
    /**
     * Insert a new admin record into the database.
     * 
     * @param array $admin Contains the admin data.
     * @return int Returns the new record id.
     * @throws Exception When the insert operation fails.
     */
    public function insert($admin) {
        $admin['id_roles'] = $this->get_admin_role_id();
        
        if (!$this->db->insert('ea_users', $admin)) {
            throw new Exception('Could not insert admin into the database.');
        }
        
        return intval($this->db->insert_id());
    }   
    
    /**
     * Update an existing admin record in the database.
     * 
     * @param array $admin Contains the admin record data.
     * @return int Retuns the record id.
     * @throws Exception When the update operation fails.
     */
    public function update($admin) {
        $this->db->where('id', $admin['id']);
        if (!$this->db->update('ea_users', $admin)){
            throw new Exception('Could not update admin record.');
        }
        
        return intval($admin['id']);
    }
    
    /**
     * Find the database record id of a provider.
     * 
     * @param array $admin Contains the admin data. The 'email value is required in order to 
     * find the record id.
     * @return int Returns the record id
     * @throws Exception When the 'email' value is not present on the $admin array.
     */
    public function find_record_id($admin) {
        if (!isset($admin['email'])) {
            throw new Exception('Admin email was not provided: ' . print_r($admin, TRUE));
        }
        
        $result = $this->db
                ->select('ea_users.id')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_users.email', $admin['email'])
                ->where('ea_roles.slug', DB_SLUG_ADMIN)
                ->get();
        
        if ($result->num_rows() == 0) {
            throw new Exception('Could not find admin record id.');
        }
        
        return intval($result->row()->id);
    }
    
    /**
     * Validate admin user data before add() operation is executed.
     * 
     * @param array $admin Contains the admin user data.
     * @return bool Returns the validation result.
     */
    public function validate($admin) {
        $this->load->helper('data_validation');
        
        try {
            // If a record id is provided then check whether the record exists in the database.
            if (isset($admin['id'])) {
                $num_rows = $this->db->get_where('ea_users', array('id' => $admin['id']))
                        ->num_rows();
                if ($num_rows == 0) {
                    throw new Exception('Given admin id does not exist in database: ' . $admin['id']);
                }
            }
            
            // Validate required fields integrity.
            if (!isset($admin['last_name'])
                    || !isset($admin['email'])
                    || !isset($admin['phone_number'])) { 
                throw new Exception('Not all required fields are provided : ' . print_r($admin, TRUE));
            }
            
            // Validate admin email address.
            if (!filter_var($admin['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address provided : ' . $admin['email']);
            }
            
            return TRUE;
        } catch (Exception $exc) {
            return FALSE;
        }
    }
    
    /**
     * Delete an existing admin record from the database.
     * 
     * @param numeric $admin_id The admin record id to be deleted.
     * @return bool Returns the delete operation result.
     * @throws Exception When the $admin_id is not a valid numeric value.
     * @throws Exception When the record to be deleted is the only one admin user left on 
     * the system.
     */
    public function delete($admin_id) {
        if (!is_numeric($admin_id)) {
            throw new Exception('Invalid argument type $admin_id : ' . $admin_id);
        }
        
        // There must be always at least one admin user. If this is the only admin
        // the system, it cannot be deleted.
        $admin_count = $this->db->get_where('ea_users', 
                array('id_roles' => $this->get_admin_roles_id()))->num_rows();
        if ($admin_count == 1) {
            throw new Exception('Record could not be deleted. The system requires at least '
                    . 'one admin user.');
        }
        
        $num_rows = $this->db->get_where('ea_users', array('id' => $admin_id))->num_rows();
        if ($num_rows == 0) {
            return FALSE; // Record does not exist in database.
        }
        
        return $this->db->delete('ea_users', array('id' => $admin_id));
    }
    
    /**
     * Get a specific admin record from the database.
     * 
     * @param numeric $admin_id The id of the record to be returned.
     * @return array Returns an array with the admin user data.
     * @throws Exception When the $admin_id is not a valid numeric value.
     */
    public function get_row($admin_id) {
        if (!is_numeric($admin_id)) {
            throw new Exception('$admin_id argument is not a valid numeric value: ' . $admin_id);
        }
        
        $admin = $this->db->get_where('ea_users', array('id' => $admin_id))->row_array();
        return $admin;
    }
    
    /**
     * Get a specific field value from the database.
     * 
     * @param string $field_name The field name of the value to be returned.
     * @param numeric $admin_id Record id of the value to be returned.
     * @return string Returns the selected record value from the database.
     * @throws Exception When the $field_name argument is not a valid string.
     * @throws Exception When the $admin_id is not a valid numeric.
     * @throws Exception When the admin record does not exist in the database.
     * @throws Exception When the selected field value is not present on database.
     */
    public function get_value($field_name, $admin_id) {
        if (!is_string($field_name)) {
            throw new Exception('$field_name argument is not a string : ' . $field_name);
        }
        
        if (!is_numeric($admin_id)) {
            throw new Exception('$admin_id argument is not a valid numeric value: ' . $admin_id);
        }
        
        // Check whether the admin record exists. 
        $result = $this->db->get_where('ea_users', array('id' => $admin_id));
        if ($result->num_rows() == 0) {
            throw new Exception('The record with the given id does not exist in the '
                    . 'database : ' . $admin_id);
        }
        
        // Check if the required field name exist in database.
        $provider = $result->row_array();
        if (!isset($provider[$field_name])) {
            throw new Exception('The given $field_name argument does not exist in the ' 
                    . 'database: ' . $field_name);
        }
        
        return $provider[$field_name];
    }
    
    /**
     * Get all, or specific admin records from database.
     * 
     * @param string|array $where_clause (OPTIONAL) The WHERE clause of the query to be executed. 
     * Use this to get specific admin records.
     * @return array Returns an array with admin records.
     */
    public function get_batch($where_clause = '') {
        if ($where_clause != '') {
            $this->db->where($where_clause);
        }
        
        $this->db->where('id_roles', $this->get_admin_role_id());
        
        $batch = $this->get('ea_users')->result_array();
        return $batch;
    }
    
    /**
     * Get the admin users role id. 
     * 
     * @return int Returns the role record id. 
     */
    public function get_admin_role_id() {
        return intval($this->db->get_where('ea_roles', array('slug' => DB_SLUG_ADMIN))->row()->id);
    }
}

/* End of file admins_model.php */
/* Location: ./application/models/admins_model.php */