<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Unit_tests_providers_model extends CI_Driver {
    private $CI;
    private $provider_role_id;
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('Unit_test');
        $this->CI->load->model('Providers_Model');
        
        $this->provider_role_id = $this->CI->db->get_where('ea_roles', array('slug' => DB_SLUG_PROVIDER))->row()->id;
    }
    
    /**
     * Run all the available tests
     */
    public function run_all() {
        // All the methods whose names start with "test" are going to be 
        // executed. If you want a method to not be executed remove the 
        // "test" keyword from the beginning.
        $class_methods = get_class_methods('Unit_tests_providers_model');
        foreach ($class_methods as $method_name) {
            if (substr($method_name, 0, 5) === 'test_') {
                call_user_func(array($this, $method_name));
            }
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    // UNIT TESTS
    ///////////////////////////////////////////////////////////////////////// 
    
    // TEST GET ROW METHOD      ---------------------------------------------
    private function test_get_row() {
        // Insert a new customer record. 
        $provider_data = array(
                            'last_name' => 'Doe',
                            'first_name' => 'John',
                            'email' => 'alextselegidis@gmail.com',
                            'phone_number' => '0123456789',
                            'address' => 'Abbey Road 18',
                            'city' => 'London',
                            'zip_code' => '12345',
                            'id_roles' => $this->provider_role_id
                        );
        $this->CI->db->insert('ea_users', $provider_data);
        $provider_data['id'] = intval($this->CI->db->insert_id());
        
        // Get the new customer record from db.
        $no_model_data = $this->CI->db->get_where('ea_users', array('id' => $provider_data['id']))->row_array();
        $model_data = $this->CI->Providers_Model->get_row($provider_data['id']);
        
        // Check that the row is the correct one.
        $this->CI->unit->run($no_model_data, $model_data, 'Test get_row() method');
        
        // Delete inserted customer record.
        $this->CI->db->delete('ea_users', array('id' => $provider_data['id']));
    }
    
    private function test_get_row_that_does_not_exist() {
        $random_record_id = 486868412;
        $row_data = $this->CI->Providers_Model->get_row($random_record_id);
        $this->CI->unit->run($row_data, NULL, 'Test get_row() with record id that does ' 
                . 'not exist in the database.');
    }
    
    private function test_get_row_with_invalid_argument() {
        $invalid_id = 'THIS IS NOT AN INTEGER';
        
        $has_thrown_exception = FALSE;
        try {
            $this->CI->Providers_Model->get_row($invalid_id);        
        } catch (InvalidArgumentException $ia_exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->CI->unit->run($has_thrown_exception, TRUE, 'Test get_row() with wrong argument.');
    }
    
    // TEST GET VALUE METHOD    ---------------------------------------------
    private function test_get_value() {
        // Insert new customer record.
        $provider_data = array(
                            'last_name' => 'Doe',
                            'first_name' => 'John',
                            'email' => 'alextselegidis@gmail.com',
                            'phone_number' => '0123456789',
                            'address' => 'Abbey Road 18',
                            'city' => 'London',
                            'zip_code' => '12345',
                            'id_roles' => $this->provider_role_id
                        );
        $this->CI->db->insert('ea_users', $provider_data);
        $provider_data['id'] = intval($this->CI->db->insert_id());
        
        // Get a specific value from the database.
        $model_value = $this->CI->Providers_Model->get_value('email', $provider_data['id']);
        
        // Check if the value was correctly fetched from the database.
        $this->CI->unit->run($model_value, $provider_data['email'], 'Test get_value() method.');
        
        // Delete inserted appointment record.
        $this->CI->db->delete('ea_users', array('id' => $provider_data['id']));
    }
    
    private function test_get_value_record_does_not_exist() {
        $random_record_id = 843521368768;
        
        $has_thrown_exception = FALSE;
        
        try {
            $this->CI->Providers_Model->get_value('email', $random_record_id);
        } catch (InvalidArgumentException $db_exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->CI->unit->run($has_thrown_exception, TRUE, 'Test get_value() with record id that does not exist.');
    }
    
    private function test_get_value_field_does_not_exist() {
        // Insert new customer record.
        $provider_data = array(
                            'last_name' => 'Doe',
                            'first_name' => 'John',
                            'email' => 'alextselegidis@gmail.com',
                            'phone_number' => '0123456789',
                            'address' => 'Abbey Road 18',
                            'city' => 'London',
                            'zip_code' => '12345',
                            'id_roles' => $this->provider_role_id
                        );
        $this->CI->db->insert('ea_users', $provider_data);
        $provider_data['id'] = intval($this->CI->db->insert_id());
        
        // Try to get record value with wrong field name.
        $wrong_field_name = 'THIS IS WRONG';
        $has_thrown_exception = FALSE;
        
        try {
            $this->CI->Providers_Model->get_value($wrong_field_name, $provider_data['id']);
        } catch (InvalidArgumentException $db_exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->CI->unit->run($has_thrown_exception, TRUE, 'Test get_value() with record id that does not exist.');
        
        // Delete inserted appointment record.
        $this->CI->db->delete('ea_users', array('id' => $provider_data['id']));
    }
    
    // TEST GET BATCH METHOD    ---------------------------------------------
    private function test_get_batch() {
        // Get all the customer rows without using the model.
        $db_data = $this->CI->db->get_where('ea_users', array('id_roles' => $this->provider_role_id))->result_array();
        // Get all the customer rows by using the model.
        $model_data = $this->CI->Providers_Model->get_batch();
        // Check that the two arrays are the same.
        $this->CI->unit->run($db_data, $model_data, 'Test get_batch() method.');
    }
    
    private function test_get_batch_with_where_clause() {
        // Insert new customer record.
        $provider_data = array(
                            'last_name' => 'Doe',
                            'first_name' => 'John',
                            'email' => 'alextselegidis@gmail.com',
                            'phone_number' => '0123456789',
                            'address' => 'Abbey Road 18',
                            'city' => 'London',
                            'zip_code' => '12345',
                            'id_roles' => $this->provider_role_id
                        );
        $this->CI->db->insert('ea_users', $provider_data);
        $provider_data['id'] = intval($this->CI->db->insert_id());
        
        // Get data without using the model.
        $no_model_data = $this->CI->db->get_where('ea_users', array('id' => $provider_data['id']))->result_array();
        
        // Get data by using the model. 
        $model_data = $this->CI->Providers_Model->get_batch(array('id' => $provider_data['id']));
        
        // Check that the data arrays are the same.
        $this->CI->unit->run($no_model_data, $model_data, 'Test get_batch() with where clause.');
        
        // Delete inserted record from database.
        $this->CI->db->delete('ea_users', array('id' => $provider_data['id']));
    }

    private function unabled_test_get_batch_with_invalid_where_clause() {
        // CodeIgniter auto raises an exception if the where section is invalid.
    }
}

/* End of file Unit_tests_providers_model.php */
/* Location: ./application/libraries/Unit_tests/drivers/Unit_tests_providers_model.php */
