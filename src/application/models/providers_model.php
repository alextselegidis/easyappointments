<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed.'); 

class Providers_Model extends CI_Model {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add (insert/update) a service provider record. 
     * 
     * If the record already exists (id value provided) then it is going to be updated,
     * otherwise inserted into the database.
     * 
     * @param array $provider Contains the service provider data.
     * @return int Returns the record id.
     * @throws Exception When the record data validation fails.
     */
    public function add($provider) {
        if (!$this->validate($provider)) {
            throw new Exception('Provider data are not valid :' . print_r($provider, TRUE));
        }
        
        if ($this->exists($provider) && !isset($provider['id'])) {
            $provider['id'] = $this->find_record_id($provider);
        }
        
        if (!isset($provider['id'])) {
            $provider['id'] = $this->insert($provider);
        } else {
            $this->update($provider);
        }
        
        return $provider['id'];
    }
    
    /**
     * Check whether a particular provider record already exists in the database.
     *  
     * @param array $provider Contains the provider data. The 'email' value is required 
     * in order to check for a provider.
     * @return bool Returns whether the provider record exists or not.
     * @throws Exception When the 'email' value is not provided.
     */
    public function exists($provider) {
        if (!isset($provider['email'])) {
            throw new Exception('Provider email is not provided :' . print_r($provider, TRUE));
        }
        
        // This method shouldn't depend on another method of this class.
        $num_rows = $this->db
                ->select('*')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_users.email', $provider['email'])
                ->where('ea_roles.slug', DB_SLUG_PROVIDER)
                ->get()->num_rows();
        
        return ($num_rows > 0) ? TRUE : FALSE;
    }
    
    /**
     * Insert a new provider record into the database.
     *  
     * @param array $provider Contains the provider data (must be already validated).
     * @return int Returns the new record id.
     * @throws Exception When the insert operation fails.
     */
    public function insert($provider) {
        // Get provider's role id. 
        $provider['id_roles'] = $this->get_providers_role_id();
        
        if (!$this->db->insert('ea_users', $provider)) {
            throw new Exception('Could not insert provider into the database');
        }
        
        return intval($this->db->insert_id());
    }   
    
    /**
     * Update an existing provider record in the database.
     * 
     * @param array $provider Contains the provider data.
     * @return int Returns the record id.
     * @throws Exception When the update operation fails.
     */
    public function update($provider) {
        $this->db->where('id', $provider['id']);
        
        if (!$this->db->update('ea_users', $provider)) {
            throw new Exception('Could not update provider record.');
        }
        
        return intval($provider['id']);
    }
    
    /**
     * Find the database record id of a provider.
     * 
     * @param array $provider Contains the provider data. The 'email' value is required
     * in order to find the record id.
     * @return int Returns the record id.
     * @throws Exception When the provider's email value is not provided.
     */
    public function find_record_id($provider) {
        if (!isset($provider['email'])) {
            throw new Exception('Provider email was not provided :' . print_r($provider, TRUE));
        }
        
        // Get customer's role id
        $result = $this->db
                ->select('ea_users.id')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_users.email', $provider['email'])
                ->where('ea_roles.slug', DB_SLUG_PROVIDER)
                ->get();
        
        if ($result->num_rows() == 0) {
            throw new Exception('Could not find provider record id.');
        }
        
        return $result->row()->id;
    }
    
    /**
     * Validate provider data before the insert or  update operation is executed.
     * 
     * @param array $provider Contains the provider data.
     * @return bool Returns the validation result.
     */
    public function validate($provider) {
        $this->load->helper('data_validation');
        
        try {
            // If a customer id is provided, check whether the record
            // exist in the database.
            if (isset($provider['id'])) {
                $num_rows = $this->db->get_where('ea_users', 
                        array('id' => $provider['id']))->num_rows();
                if ($num_rows == 0) {
                    throw new Exception('Provided record id does not exist in the database.');
                }
            }
            // Validate required fields
            if (!isset($provider['last_name'])
                    || !isset($provider['email'])
                    || !isset($provider['phone_number'])) { 
                throw new Exception('Not all required fields are provided : ' . print_r($provider, TRUE));
            }
            
            // Validate email address
            if (!filter_var($provider['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address provided : ' . $provider['email']);
            }
            
            return TRUE;
        } catch (Exception $exc) {
            return FALSE;
        }
    }
    
    /**
     * Delete an existing provider record from the database.
     * 
     * @param numeric $customer_id The record id to be deleted.
     * @return bool Returns the delete operation result.
     * @throws Exception When the provider id value is not numeric.
     */
    public function delete($provider_id) {
        if (!is_numeric($provider_id)) {
            throw new Exception('Invalid argument type $customer_id : ' . $provider_id);
        }
        
        $num_rows = $this->db->get_where('ea_users', array('id' => $provider_id))->num_rows();
        if ($num_rows == 0) {
            return FALSE; // record does not exist
        }
        
        return $this->db->delete('ea_users', array('id' => $provider_id));
    }
    
    /**
     * Get a specific row from the providers table.
     * 
     * @param int $provider_id The id of the record to be returned.
     * @return array Returns an associative array with the selected
     * record's data. Each key has the same name as the database 
     * field names.
     */
    public function get_row($provider_id) {
        if (!is_numeric($provider_id)) {
            throw new Exception('$provider_id argument is not an integer : ' . $provider_id);
        }
        return $this->db->get_where('ea_users', array('id' => $provider_id))->row_array();
    }
    
    /**
     * Get a specific field value from the database.
     * 
     * @param string $field_name The field name of the value to be
     * returned.
     * @param int $provider_id The selected record's id.
     * @return string Returns the records value from the database.
     */
    public function get_value($field_name, $provider_id) {
        if (!is_numeric($provider_id)) {
            throw new Exception('Invalid argument provided as $customer_id : ' 
                    . $provider_id);
        }
        
        if (!is_string($field_name)) {
            throw new Exception('$field_name argument is not a string : ' 
                    . $field_name);
        }
        
        if ($this->db->get_where('ea_users', array('id' => $provider_id))->num_rows() == 0) {
            throw new Exception('The record with the $provider_id argument does not exist in '
                    . 'the database : ' . $provider_id);
        }
        
        $row_data = $this->db->get_where('ea_users', array('id' => $provider_id))->row_array();
        if (!isset($row_data[$field_name])) {
            throw new Exception('The given $field_name argument does not exist in the '
                    . 'database : ' . $field_name);
        }
        
        $provider = $this->db->get_where('ea_users', array('id' => $provider_id))->row_array();
        
        return $provider[$field_name];
    }
    
    /**
     * Get all, or specific records from provider's table.
     * 
     * @example $this->Model->getBatch('id = ' . $recordId);
     * 
     * @param string $whereClause (OPTIONAL) The WHERE clause of  
     * the query to be executed. DO NOT INCLUDE 'WHERE' KEYWORD.
     * @return array Returns the rows from the database.
     */
    public function get_batch($where_clause = '') {
        $providers_role_id = $this->get_providers_role_id();
        
        if ($where_clause != '') {
            $this->db->where($where_clause);
        }
        
        $this->db->where('id_roles', $providers_role_id);
        
        return $this->db->get('ea_users')->result_array();
    }
    
    /**
     * Get the available system providers.
     * 
     * This method returns the available providers and the services that can 
     * provide.
     * 
     * @return array Returns an array with the providers data.
     */
    public function get_available_providers() {
        $this->db
                ->select('ea_users.*')
                ->from('ea_users')  
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_roles.slug', 'provider');
        
        $providers = $this->db->get()->result_array();
        
        // :: GET PROVIDER SERVICES
        // Return also an array with the services that each provider can provide
        // to the customers.
        foreach($providers as &$provider) {
            $this->db
                    ->select('id_services')
                    ->from('ea_services_providers')
                    ->where('id_users', $provider['id']);
            
            $provider_services = $this->db->get()->result_array();
            
            if (!isset($provider['services'])) {
                $provider['services'] = array();
            }
            
            foreach($provider_services as $service) {
                $provider['services'][] = $service['id_services'];
            }
        }
        
        // :: GET PROVIDER SETTINGS
        foreach($providers as &$provider) {
            $this->db  
                    ->select('*')
                    ->from('ea_user_settings')
                    ->where('id_users', $provider['id']);
            $provider['settings'] = $this->db->get()->row_array();
            unset($provider['settings']['id_users']); // Do not need it.
        }
        
        return $providers;
    }
    
    /**
     * Get the providers role id from the database.
     * 
     * @return int Returns the role id for the customer records.
     */
    public function get_providers_role_id() {
        return $this->db->get_where('ea_roles', array('slug' => DB_SLUG_PROVIDER))->row()->id;
    }
    
    /**
     * Get a providers setting from the database.
     * 
     * @param string $setting_name The setting name that is going to be
     * returned.
     * @param int $provider_id The selected provider id.
     * @return string Returs the value of the selected user setting.
     */
    public function get_setting($setting_name, $provider_id) {
        $provider_settings = $this->db->get_where('ea_user_settings', 
                array('id_users' => $provider_id))->row_array();
        return $provider_settings[$setting_name];
    }
    
    /**
     * Set a provider's setting value in the database. 
     * 
     * @param string $setting_name The setting's name.
     * @param string $value The setting's value.
     * @param numeric $provider_id The selected provider id.
     */
    public function set_setting($setting_name, $value, $provider_id) {
        $this->db->where(array('id_users' => $provider_id));
        return $this->db->update('ea_user_settings', array($setting_name => $value));
    }
}

/* End of file providers_model.php */
/* Location: ./application/models/providers_model.php */