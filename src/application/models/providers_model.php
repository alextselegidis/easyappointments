<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed.'); 

class Providers_Model extends CI_Model {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
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