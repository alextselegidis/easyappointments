<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Unit_tests_providers_model extends CI_Driver {
    private $ci;
    private $provider_role_id;
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library('Unit_test');
        $this->ci->load->model('providers_model');
        
        $this->provider_role_id = $this->ci->db->get_where('ea_roles', 
                array('slug' => DB_SLUG_PROVIDER))->row()->id;
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
    // TEST ADD METHOD
    private function test_add_insert() {
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id
        );
        
        $provider['id'] = $this->ci->providers_model->add($provider);
        $this->ci->unit->run($provider['id'], 'is_int', 'Check if add (insert) result is integer.');
        
        $db_record = $this->ci->db->get_where('ea_users', array('id' => $provider['id']))->row_array();
        $this->ci->unit->run($provider, $db_record, 'Check if add(insert) has successfully '
                . 'inserted a provider record.');
        
        $this->ci->db->delete('ea_users', array('id' => $provider['id']));
    }
    
    private function test_add_update() {
        
    }
    
    private function test_add_with_invalid_data() {
        
    }
    
    // TEST EXISTS METHOD
    private function test_exists_with_record_that_exists() {
        
    }
    
    private function test_exists_with_invalid_data() {
        
    }
    
    private function test_exists_with_record_that_does_not_exist() {
        
    }
    
    // TEST FIND RECORD ID METHOD
    private function test_find_record_id() {
        
    }
    
    private function test_find_record_id_with_invalid_data() {
        
    }
    
    private function test_find_record_id_with_record_that_does_not_exist() {
        
    }
    
    // TEST VALIDATE RECORD METHOD
    private function test_validate() {
        
    }
    
    private function test_validate_with_record_that_does_not_exist() {
        
    }
    
    private function test_validate_with_missing_required_fields() {
        
    }
    
    private function test_validate_with_invalid_email_address() {
        
    }
    
    // TEST DELETE RECORD METHOD
    private function test_delete() {
        
    }
    
    private function test_delete_with_invalid_record_id() {
        
    }
    
    private function test_delete_with_record_that_does_not_exist() {
        
    }
    
    // TEST GET ROW METHOD      ---------------------------------------------
    private function test_get_row() {
        // Insert a new customer record. 
        $provider = array(
            'last_name' => 'Doe',
            'first_name' => 'John',
            'email' => 'alextselegidis@gmail.com',
            'phone_number' => '0123456789',
            'address' => 'Abbey Road 18',
            'city' => 'London',
            'zip_code' => '12345',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $provider['id'] = intval($this->ci->db->insert_id());
        
        // Get the new customer record from db.
        $no_model_data = $this->ci->db->get_where('ea_users', array('id' => $provider['id']))
                ->row_array();
        $model_data = $this->ci->providers_model->get_row($provider['id']);
        
        // Check that the row is the correct one.
        $this->ci->unit->run($no_model_data, $model_data, 'Test get_row() method');
        
        // Delete inserted customer record.
        $this->ci->db->delete('ea_users', array('id' => $provider['id']));
    }
    
    private function test_get_row_that_does_not_exist() {
        $random_record_id = 486868412;
        $row_data = $this->ci->providers_model->get_row($random_record_id);
        $this->ci->unit->run($row_data, NULL, 'Test get_row() with record id that does ' 
                . 'not exist in the database.');
    }
    
    private function test_get_row_with_invalid_argument() {
        $invalid_id = 'THIS IS NOT AN INTEGER';
        
        $has_thrown_exception = FALSE;
        try {
            $this->ci->providers_model->get_row($invalid_id);        
        } catch (Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test get_row() with wrong argument.');
    }
    
    // TEST GET VALUE METHOD    ---------------------------------------------
    private function test_get_value() {
        // Insert new customer record.
        $provider = array(
            'last_name' => 'Doe',
            'first_name' => 'John',
            'email' => 'alextselegidis@gmail.com',
            'phone_number' => '0123456789',
            'address' => 'Abbey Road 18',
            'city' => 'London',
            'zip_code' => '12345',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $provider['id'] = intval($this->ci->db->insert_id());
        
        // Get a specific value from the database.
        $model_value = $this->ci->providers_model->get_value('email', $provider['id']);
        
        // Check if the value was correctly fetched from the database.
        $this->ci->unit->run($model_value, $provider['email'], 'Test get_value() method.');
        
        // Delete inserted appointment record.
        $this->ci->db->delete('ea_users', array('id' => $provider['id']));
    }
    
    private function test_get_value_record_does_not_exist() {
        $random_record_id = 843521368768;
        
        $has_thrown_exception = FALSE;
        
        try {
            $this->ci->providers_model->get_value('email', $random_record_id);
        } catch (Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test get_value() with record id that '
                . 'does not exist.');
    }
    
    private function test_get_value_field_does_not_exist() {
        // Insert new customer record.
        $provider = array(
            'last_name' => 'Doe',
            'first_name' => 'John',
            'email' => 'alextselegidis@gmail.com',
            'phone_number' => '0123456789',
            'address' => 'Abbey Road 18',
            'city' => 'London',
            'zip_code' => '12345',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $provider['id'] = intval($this->ci->db->insert_id());
        
        // Try to get record value with wrong field name.
        $wrong_field_name = 'THIS IS WRONG';
        $has_thrown_exception = FALSE;
        
        try {
            $this->ci->providers_model->get_value($wrong_field_name, $provider['id']);
        } catch (Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test get_value() with record id that '
                . 'does not exist.');
        
        // Delete inserted appointment record.
        $this->ci->db->delete('ea_users', array('id' => $provider['id']));
    }
    
    // TEST GET BATCH METHOD    ---------------------------------------------
    private function test_get_batch() {
        // Get all the customer rows without using the model.
        $db_data = $this->ci->db->get_where('ea_users', 
                array('id_roles' => $this->provider_role_id))->result_array();
        // Get all the customer rows by using the model.
        $model_data = $this->ci->providers_model->get_batch();
        // Check that the two arrays are the same.
        $this->ci->unit->run($db_data, $model_data, 'Test get_batch() method.');
    }
    
    private function test_get_batch_with_where_clause() {
        // Insert new customer record.
        $provider = array(
            'last_name' => 'Doe',
            'first_name' => 'John',
            'email' => 'alextselegidis@gmail.com',
            'phone_number' => '0123456789',
            'address' => 'Abbey Road 18',
            'city' => 'London',
            'zip_code' => '12345',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $provider['id'] = intval($this->ci->db->insert_id());
        
        // Get data without using the model.
        $no_model_data = $this->ci->db->get_where('ea_users', array('id' => $provider['id']))
                ->result_array();
        
        // Get data by using the model. 
        $model_data = $this->ci->providers_model->get_batch(array('id' => $provider['id']));
        
        // Check that the data arrays are the same.
        $this->ci->unit->run($no_model_data, $model_data, 'Test get_batch() with where clause.');
        
        // Delete inserted record from database.
        $this->ci->db->delete('ea_users', array('id' => $provider['id']));
    }

    private function unabled_test_get_batch_with_invalid_where_clause() {
        // CodeIgniter auto raises an exception if the where section is invalid.
    }
}

/* End of file Unit_tests_providers_model.php */
/* Location: ./application/libraries/Unit_tests/drivers/Unit_tests_providers_model.php */
