<?php
class Customers_Model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * This method adds a customer to the database. If the customer
     * already exists then its data are going to be updated.
     * 
     * @param array $customerData The customer data we want to add.
     */
    public function add($customerData) {
        try {
            if (!$this->exists($customerData['email'])) {
                $this->insert($customerData);
            } else {
                $this->update($customerData);
            }
        } catch(Exception $exc) {
            // Send some info for the exception back to the browser.
        }
        
        return $this->db->insert_id();
    }
    
    /**
     * This method checks if a customer exists on the database. 
     * The unique identifier for a customer is his email. 
     * 
     * @param string $customerEmail The customers email. 
     * @return bool Returns the operation result.
     */
    public function exists($customerEmail) {
        return false; // @task This should be implemented soon.
    }
    
    /**
     * This method inserts a customer into the database. 
     * 
     * @param array $customerData Associative array with the customers
     * data. The key of the array should be the same as the database 
     * field names.
     */
    private function insert($customerData) {
        $this->db
            ->select('id')
            ->from('ea_roles')
            ->where('slug', 'customer');
        
        $customerRoleId = $this->db->get()->row()->id;
        
        if ($customerRoleId !== NULL) {
            $customerData['id_roles'] = $customerRoleId;
        }
        
        if (!$this->db->insert('ea_users', $customerData)) {
            throw new Exception('Could not insert customer to the database.');
        }
    }
    
    private function update($customerData) {
        $this->db->where('email', $customerData['email']);
        if (!$this->db->update('ea_users', $customerData)) {
            throw new Exception('Could not update customer to the database.');
        }
    }
    
    /**
     * This method deletes a customers from the database. 
     * All his appointmets will be deleted too (automatically
     * through the foreign key constraint).
     * 
     * @param int $customerId The id of the customer to be 
     * deleted.
     */
    public function delete($customerId) {
        
    }
}



?>
