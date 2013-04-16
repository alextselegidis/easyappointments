<?php
class Providers extends CI_Model {
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
        $sql = '
            SELECT ea_users.* 
            FROM ea_users 
            INNER JOIN ea_roles 
                ON ea_users.id_roles = ea_roles.id 
            WHERE ea_roles.slug = "provider"';
        
        $providers = $this->db->query($sql)->result_array();
        
        foreach($providers as &$provider) {
            $sql = '
                SELECT id_services
                FROM ea_services_providers
                WHERE id_users = ' . $this->db->escape($provider['id']);
            
            $providerServices = $this->db->query($sql)->result_array();
            
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