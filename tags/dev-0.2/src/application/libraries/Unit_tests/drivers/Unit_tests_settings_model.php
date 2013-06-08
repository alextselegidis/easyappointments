<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Unit_tests_settings_model extends CI_Driver {
    private $CI;
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('Unit_test');
        $this->CI->load->model('Settings_Model');
    }
    
    /**
     * Run all the available tests
     */
    public function run_all() {
        // All the methods whose names start with "test" are going to be 
        // executed. If you want a method to not be executed remove the 
        // "test" keyword from the beginning.
        $class_methods = get_class_methods('Unit_tests_settings_model');
        foreach ($class_methods as $method_name) {
            if (substr($method_name, 0, 5) === 'test_') {
                call_user_func(array($this, $method_name));
            }
        }
    }
   
    /////////////////////////////////////////////////////////////////////////
    // UNIT TESTS
    ///////////////////////////////////////////////////////////////////////// 
    
    // TEST GET SETTING METHOD
    private function test_get_setting() {
        // Insert new setting to database.
        $setting_data = array(
                            'name' => 'test_name',
                            'value' => 'test_value'
                        );
        $this->CI->db->insert('ea_settings', $setting_data);
        $setting_data['id'] = intval($this->CI->db->insert_id());
        
        // Try to get the setting value by using the model.
        $model_value = $this->CI->Settings_Model->get_setting('test_name');
        $this->CI->unit->run($model_value, $setting_data['value'], 'Test get_setting() method.');
        
        // Delete inserted setting. 
        $this->CI->db->delete('ea_settings', array('id' => $setting_data['id']));
    }
    
    private function test_get_setting_invalid_argument() {
        $invalid_argument = 564658765;
        $has_thrown_exception = FALSE;
        try {
            $this->CI->Settings_Model->get_setting($invalid_argument);
        } catch (InvalidArgumentException $ia_exc) {
            $has_thrown_exception = TRUE;
        }
        $this->CI->unit->run($has_thrown_exception, TRUE, 'Test get_setting() with invalid method argument.');
    }
    
    private function test_get_setting_that_does_not_exist() {
        $setting_name = 'THIS NAME DOES NOT EXIST IN DB';
        $has_thrown_exception = FALSE;
        try {
            $this->CI->Settings_Model->get_setting($setting_name);
        } catch (InvalidArgumentException $ia_exc) {
            $has_thrown_exception = TRUE;
        }
        $this->CI->unit->run($has_thrown_exception, TRUE, 'Test get_setting() with a name that does not exist in database.');
    }
    
    // TEST SET SETTING METHOD
    private function test_set_setting_insert() {
        $setting_data = array(
                            'name' => 'test_setting',
                            'value' => 'test_value'
                        );
        
        // Insert setting by using the model.
        $setting_data['id'] = $this->CI->Settings_Model->set_setting($setting_data['name'], $setting_data['value']);
        $this->CI->unit->run($setting_data['id'], 'is_int', 'Test that set_setting() method (insert operation) has returned the setting database id.');
        
        // Check that the setting has been successfully inserted.
        $db_data = $this->CI->db->get_where('ea_settings', array('id' => $setting_data['id']))->row_array();
        $this->CI->unit->run($setting_data, $db_data, 'Test set_setting() method has successfully inserted the setting into the database.');
        
        // Delete inserted setting.
        $this->CI->db->delete('ea_settings', array('id' => $setting_data['id']));
    }
    
    private function test_set_setting_update() {
        // Insert new setting into database.
        $setting_data = array(
                            'name' => 'test_name',
                            'value' => 'test_value'
                        );
        $this->CI->db->insert('ea_settings', $setting_data);
        $setting_data['id'] = intval($this->CI->db->insert_id());
        
        // Update the inserted setting.
        $new_setting_value = 'new_test_value';
        $set_setting_result = $this->CI->Settings_Model->set_setting($setting_data['name'], $new_setting_value);
        $this->CI->unit->run($set_setting_result, 'is_int', 'Test that set_setting() method (update operation) has returned the setting database id.');
        
        // Check if the update operation was completed successfully.
        $db_setting_value = $this->CI->db->get_where('ea_settings', array('id' => $setting_data['id']))->row()->value;
        $this->CI->unit->run($db_setting_value, $new_setting_value, 'Test set_setting() method has successfully updated a setting value.');
        
        // Delete inserted record.
        $this->CI->db->delete('ea_settings', array('id' => $setting_data['id']));
    }
    
    private function test_set_setting_invalid_setting_name() {
        $invalid_setting_name = 1219087912;
        $has_thrown_exception = FALSE;
        try {
            $this->CI->Settings_Model->set_setting($invalid_setting_name, 'test_value');
        } catch (InvalidArgumentException $id_exc) {
            $has_thrown_exception = TRUE;
        }
        $this->CI->unit->run($has_thrown_exception, TRUE, 'Test set_setting() method with invalid argument.');
    }
    
    // TEST REMOVE SETTING METHOD
    private function test_remove_setting() {
        // Insert new setting
        $setting_data = array(
                            'name' => 'test_name',
                            'value' => 'test_value'
                        );
        $this->CI->db->insert('ea_settings', $setting_data);
        $setting_data['id'] = intval($this->CI->db->insert_id());
        
        // Remove setting
        $remove_setting_result = $this->CI->Settings_Model->remove_setting($setting_data['name']);
        $this->CI->unit->run($remove_setting_result, TRUE, 'Test remove_setting() return value.');
        
        // Check if setting is removed
        $num_rows = $this->CI->db->get_where('ea_settings', array('id' => $setting_data['id']))->num_rows();
        $this->CI->unit->run($num_rows, 0, 'Test if remove_setting() method has successfully removed the setting from the database.');
        if ($num_rows > 0) {
            $this->CI->db->delete('ea_settings', array('id' => $setting_data['id']));
        }
    }
    
    private function test_remove_setting_record_does_not_exist() {
        $random_setting_name = 'THIS IS TOTALLY RANDOM';
        $remove_setting_result = $this->CI->Settings_Model->remove_setting($random_setting_name);
        $this->CI->unit->run($remove_setting_result, FALSE, 'Test remove_setting() with record that does not exist.');
    }
    
    private function test_remove_setting_invalid_setting_name() {
        $invalid_setting_name = 12092130968;
        $has_thrown_exception = FALSE;
        try {
            $this->CI->Settings_Model->remove_setting($invalid_setting_name);
        } catch (InvalidArgumentException $ia_exc) {
            $has_thrown_exception = TRUE;
        }
        $this->CI->unit->run($has_thrown_exception, TRUE, 'Test remove_setting() with invalid setting name.');
    }
}

/* End of file Unit_tests_settings_model.php */
/* Location: ./application/libraries/Unit_tests/drivers/Unit_tests_settings_model.php */