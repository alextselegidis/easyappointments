<?php
class Providers_Model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * This method returns the available providers and 
     * the services that can provide.
     * 
     * @return array Returns an array with the providers
     * data.
     */
    public function getAvailableProviders() {
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
            
            $providerServices = $this->db->get()->result_array();
            
            if (!isset($provider['services'])) {
                $provider['services'] = array();
            }
            
            foreach($providerServices as $providerService) {
                $provider['services'][] = $providerService['id_services'];
            }
        }
        
        return $providers;
    }
}
?>