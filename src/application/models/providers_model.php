<?php
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
     * @param int $provider_id The record's id to be returned.
     * @return array Returns an associative array with the selected
     * record's data. Each key has the same name as the database 
     * field names.
     */
    public function get_row($provider_id) {
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
        return $this->db->get_where('ea_users', array('id' => $provider_id))->row_array()[$field_name];
    }
    
    /**
     * This method returns the available providers and 
     * the services that can provide.
     * 
     * @return array Returns an array with the providers
     * data.
     */
    public function get_available_providers() {
        $this->db
            ->select('ea_users.*')
            ->from('ea_users')  
            ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
            ->where('ea_roles.slug', 'provider');
        
        $providers = $this->db->get()->result_array();
        
        foreach($providers as &$provider) {
            $this->db
                ->select('id_services')
                ->from('ea_services_providers')
                ->where('id_users', $provider['id']);
            
            $provider_services = $this->db->get()->result_array();
            
            if (!isset($provider['services'])) {
                $provider['services'] = array();
            }
            
            foreach($provider_services as $providerService) {
                $provider['services'][] = $providerService['id_services'];
            }
        }
        
        return $providers;
    }
}


/* End of file providers_model.php */
/* Location: ./application/models/providers_model.php */