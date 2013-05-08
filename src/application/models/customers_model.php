<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Customers_Model extends CI_Model {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add a customer record to the database.
     * 
     * This method adds a customer to the database. If the customer
     * doesn't exists it is going to be inserted, otherwise the 
     * record is going to be updated.
     * 
     * @param array $customer_data Associative array with the customer's 
     * data. Each key has the same name with the database fields.
     * @return int Returns the customer id.
     */
    public function add($customer_data) {
        try {
            $customer_id = $this->exists($customer_data['email']);
            
            if (!$customer_id) {
                $customer_id = $this->insert($customer_data);
            } else {
                $customer_data['id'] = $customer_id;
                $this->update($customer_data);
            }
            
            return $customer_id;
            
        } catch(Exception $exc) {
            // Send some info for the exception back to the browser.
            echo $exc->getTraceAsString();
        }
    }
    
    /**
     * Check if a particular customer record already exists.
     * 
     * This method checks wether the given customer already exists in 
     * the database. This method does not search with the id, but witha
     * the email value of the customer. 
     * 
     * @param string $customer_email The email field value is used to 
     * distinguish customer records.
     * @return int|bool Returns the record id or FALSE if it doesn't exist.
     */
    public function exists($customer_email) {
        $this->db->where(array(
            'email'     => $customer_email,
            'id_roles'  => $this->get_customers_role_id()
        ));
        $result = $this->db->get('ea_users');
        return ($result->num_rows() > 0) ? $result->row()->id : FALSE;
    }
    
    /**
     * Insert a new customer record to the database.
     * 
     * @param array $customer_data Associative array with the customer's
     * data. Each key has the same name with the database fields.
     * @return int Returns the id of the new record.
     */
    private function insert($customer_data) {
        // Before inserting the customer we need to get the customer's role id
        // from the database and assign it to the new record as a foreign key.
        $this->db
            ->select('id')
            ->from('ea_roles')
            ->where('slug', DB_SLUG_CUSTOMER);
        
        $customer_role_id = $this->db->get()->row()->id;
        
        if ($customer_role_id !== NULL) {
            $customer_data['id_roles'] = $customer_role_id;
        }
        
        if (!$this->db->insert('ea_users', $customer_data)) {
            throw new Exception('Could not insert customer to the database.');
        }
        
        return $this->db->insert_id();
    }
    
    /**
     * Update an existing customer record in the database.
     * 
     * The customer data argument should already include the record
     * id in order to process the update operation.
     * 
     * @param array $customer_data Associative array with the customer's
     * data. Each key has the same name with the database fields.
     * @return int Returns the updated record id.
     */
    private function update($customer_data) {
        $this->db->where('id', $customer_data['id']);
        if (!$this->db->update('ea_users', $customer_data)) {
            throw new Exception('Could not update customer to the database.');
        }
        return $this->db->get_where('ea_users', array('email' => $customer_data['email']))->row()->id;
    }
    
    /**
     * Delete an existing customer record from the database.
     * 
     * @param int $customer_id The record id to be deleted.
     * @return bool Returns the delete operation result.
     */
    public function delete($customer_id) {
        $this->db->where('id', $customer_id);
        return $this->db->delete('ea_users');
    }
    
    /**
     * Get a specific row from the appointments table.
     * 
     * @param int $customer_id The record's id to be returned.
     * @return array Returns an associative array with the selected
     * record's data. Each key has the same name as the database 
     * field names.
     */
    public function get_row($customer_id) {
        return $this->db->get_where('ea_users', array('id' => $customer_id))->row_array();
    }
    
    /**
     * Get a specific field value from the database.
     * 
     * @param string $field_name The field name of the value to be
     * returned.
     * @param int $customer_id The selected record's id.
     * @return string Returns the records value from the database.
     */
    public function get_value($field_name, $customer_id) {
        return $this->db->get_where('ea_users', array('id' => $customer_id))->row_array()[$field_name];
    }
    
    /**
     * Get all, or specific records from appointment's table.
     * 
     * @example $this->Model->getBatch('id = ' . $recordId);
     * 
     * @param string $whereClause (OPTIONAL) The WHERE clause of  
     * the query to be executed. DO NOT INCLUDE 'WHERE' KEYWORD.
     * @return array Returns the rows from the database.
     */
    public function get_batch($where_clause = NULL) {
        $customers_role_id = $this->get_customers_role_id();
        
        if ($where_clause != NULL) {
            $this->db->where($where_clause);
        }
        
        $this->db->where('id_roles', $customers_role_id);
        
        return $this->db->get('ea_users')->result_array();
    }
    
    /**
     * Get the customers role id from the database.
     * 
     * @return int Returns the role id for the customer records.
     */
    public function get_customers_role_id() {
        return $this->db->get_where('ea_roles', array('slug' => DB_SLUG_CUSTOMER))->row()->id;
    }
}

/* End of file customers_model.php */
/* Location: ./application/models/customers_model.php */