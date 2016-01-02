<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 * 
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3 
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Secretaries Model Unit Tests 
 *
 * @package Libraries
 * @subpackage Tests
 */
class Unit_tests_secretaries_model extends CI_Driver {
    private $ci;
    private $secretary_role_id;
    private $default_secretary; // does not contain an 'id' value
    private $default_settings;
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library('Unit_test');
        $this->ci->load->model('secretaries_model');
        
        $this->secretary_role_id = $this->ci->db->get_where('ea_roles', 
                array('slug' => DB_SLUG_SECRETARY))->row()->id;
        
        $this->default_secretary = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'mobile_number' => '2340982039',
            'phone_number' => '9098091234',
            'address' => 'Some Street 80',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test secretary user.',
            'id_roles' => $this->secretary_role_id
        );
        
        $this->default_settings = array(
            'username' => 'test_secretary',
            'password' => 'test_pswd',
            'working_plan' => NULL,
            'notifications' => FALSE,
            'google_sync' => 0, 
            'google_token' => NULL,
            'sync_past_days' => NULL,
            'sync_future_days' => NULL
        );
    }
    
    /**
     * Run all the available tests
     */
    public function run_all() {
        // All the methods whose names start with "test" are going to be             
        // executed. If you want a method to not be executed remove the 
        // "test" keyword from the beginning.
        $class_methods = get_class_methods('Unit_tests_secretaries_model');
        foreach ($class_methods as $method_name) {
            if (substr($method_name, 0, 5) === 'test_') {
                call_user_func(array($this, $method_name));
            }
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    // UNIT TESTS
    ///////////////////////////////////////////////////////////////////////// 
    // TEST ADD METHOD ------------------------------------------------------
    private function test_add_insert() {
        $secretary = $this->default_secretary;
        $secretary['providers'] = array();
        $secretary['settings'] = $this->default_settings;
        $secretary['id'] = $this->ci->secretaries_model->add($secretary);
        
        $this->ci->unit->run($secretary['id'], 'is_int', 'Test if add() - insert operation - '
                . 'has returned and integer value.');
        
        $db_secretary = $this->ci->db->get_where('ea_users', array('id' => $secretary['id']))->row_array();
        
        $db_secretary['providers'] = array();
        $db_secretary['settings'] = $this->ci->db->get_where('ea_user_settings', 
                array('id_users' => $secretary['id']))->row_array();
        unset($db_secretary['settings']['id_users'], $db_secretary['settings']['salt'],
                $secretary['settings']['password'], $db_secretary['settings']['password']); // not needed
        
        $this->ci->unit->run($secretary, $db_secretary, 'Test if add() - insert operation - '
                . 'has successfully inserted a new record.');
        
        $this->ci->db->delete('ea_users', array('id' => $secretary['id']));
    }
    
    private function test_add_update() {
        $secretary = $this->default_secretary;
        $this->ci->db->insert('ea_users', $secretary);
        $secretary['id'] = intval($this->ci->db->insert_id());
        
        $secretary['settings'] = $this->default_settings;
        $secretary['settings']['id_users'] = $secretary['id'];
        $this->ci->db->insert('ea_user_settings', $secretary['settings']);
        unset($secretary['settings']['id_users']);
        
        $secretary['first_name'] = 'value changed';
        $secretary['last_name'] = 'value changed';
        $secretary['email'] = 'value@changed.com';
        $secretary['mobile_number'] = 'value changed';
        $secretary['phone_number'] = 'value changed';
        $secretary['address'] = 'value changed';
        $secretary['city'] = 'value changed';
        $secretary['state'] = 'value changed';
        $secretary['zip_code'] = 'value changed';
        $secretary['notes'] = 'value changed';
        
        $secretary['providers'] = array();
        
        $update_result = $this->ci->secretaries_model->add($secretary);
        $this->ci->unit->run($update_result, 'is_int', 'Test if add() - update operation - has '
                . 'returned an integer value.');
        
        $db_secretary = $this->ci->db->get_where('ea_users', array('id' => $secretary['id']))->row_array();
        $db_secretary['providers'] = array();
        $db_secretary['settings'] = $this->ci->db->get_where('ea_user_settings', 
                array('id_users' => $secretary['id']))->row_array();
        unset($db_secretary['settings']['id_users'], $db_secretary['settings']['salt'],
                $secretary['settings']['password'], $db_secretary['settings']['password']); // not needed
        
        $this->ci->unit->run($secretary, $db_secretary, 'Test if add() - update operation - has '
                . 'successfully updated the secretary record.');
        
        $this->ci->db->delete('ea_users', array('id' => $secretary['id']));
    }
    
    private function test_add_invalid_data() {
        $secretary = $this->default_secretary;
        $secretary['email'] = 'this value is invalid';
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->add($secretary);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if add() has thrown exception ' 
                . 'on invalid data.');
    }
    
    private function disabled_test_add_using_find_record_id() {
        $secretary = $this->default_secretary; 
        $this->ci->db->insert('ea_users', $secretary);
        $secretary_id = intval($this->ci->db->insert_id());
        
        $secretary['settings'] = $this->default_settings;
        $secretary['settings']['id_users'] = $secretary_id;
        $this->ci->db->insert('ea_user_settings', $secretary['settings']);
        unset($secretary['settings']['id_users']);
        
        // Since $secretary array does not contain an 'id' value but 
        // exists in the database, the find_record_id() method is going 
        // to be used inside the add() method to find the secretary id.
        
        $secretary['last_name'] = 'updated value';
        $secretary['providers'] = array();
        $update_result = $this->ci->secretaries_model->add($secretary);
        $this->ci->unit->run($update_result, 'is_int', 'Test if add() - update operation - has '
                . 'returned and integer value.');
        
        $db_secretary = $this->ci->db->get_where('ea_users', array('id' => $secretary_id))->row_array();
        $db_secretary['providers'] = array();
        unset($db_secretary['id']);
        
        $db_secretary['settings'] = $this->ci->db->get_where('ea_user_settings', 
                array('id_users' => $secretary_id))->row_array();
        unset($db_secretary['settings']['id_users'], $db_secretary['settings']['salt'],
                $secretary['settings']['password'], $db_secretary['settings']['password']); // not needed
        
        $this->ci->unit->run($secretary, $db_secretary, 'Test if add() - update operation - has '
                . 'successfully updated the secretary record using find_record_id() method ' 
                . 'internally.');
        
        $this->ci->db->delete('ea_users', array('id' => $secretary_id));
    }
    
    // TEST EXISTS METHOD -----------------------------------------------------
    private function test_exists_record_exists() {
        $secretary = $this->default_secretary;
        $this->ci->db->insert('ea_users', $secretary);
        $secretary['id'] = intval($this->ci->db->insert_id());
        
        $exists = $this->ci->secretaries_model->exists($secretary);
        $this->ci->unit->run($exists, TRUE, 'Test if exists() has returned TRUE with record '
                . 'that exists.');
        
        $this->ci->db->delete('ea_users', array('id' => $secretary['id']));
    }
    
    private function test_exists_record_does_not_exist() {
        $secretary = $this->default_secretary;
        $exists = $this->ci->secretaries_model->exists($secretary);
        $this->ci->unit->run($exists, FALSE, 'Test if exists() has returned FALSE with record '
                . 'that does not exists.');
    }
    
    private function test_exists_invalid_argument() {
        $secretary = $this->default_secretary;
        unset($secretary['email']);
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->exists($secretary);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if exists() has thrown exception with '
                . 'invalid argument (missing email).');
    }
    
    // TEST FIND RECORD ID METHOD ---------------------------------------------
    private function test_find_record_id_record_exists() {
        $secretary = $this->default_secretary;
        $this->ci->db->insert('ea_users', $secretary);
        $secretary['id'] = intval($this->ci->db->insert_id());
        
        $model_id = $this->ci->secretaries_model->find_record_id($secretary);
        
        $this->ci->unit->run($model_id, 'is_int', 'Test if find_record_id() has returned '
                . 'an integer valuel.');
        
        $this->ci->unit->run($secretary['id'], $model_id, 'Test if find_record_id() has '
                . 'successfully found the selected record id');
        
        $this->ci->db->delete('ea_users', array('id' => $model_id));
    }
    
    private function test_find_record_id_record_does_not_exist() {
        $secretary = $this->default_secretary;
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->find_record_id($secretary);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if find_record_id() has thrown '
                . 'exception on record that does not exist.');
    }
    
    private function test_find_record_id_invalid_argument() {
        $secretary = $this->default_secretary;
        unset($secretary['email']);
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->find_record_id($secretary);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if find_record_id() has thrown '
                . 'exception on invalid argument given (missing email value).');
    }
    
    // TEST VALIDATE METHOD ---------------------------------------------------
    private function test_validate() {
        $secretary = $this->default_secretary;
        $validation_result = $this->ci->secretaries_model->validate($secretary);
        $this->ci->unit->run($validation_result, TRUE, 'Test if validate() has returned TRUE '
                . 'on valid secretary data.');
        
    }
    
    private function test_validate_provided_id_does_not_exist() {
        $secretary = $this->default_secretary;
        $secretary['id'] = 'This id does not exist in database.';
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->validate($secretary);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with invalid data (provided id does not exists in db).');
    }
    
    private function test_validate_invalid_users_value_data_type() {
        $secretary = $this->default_secretary;
        $secretary['providers'] = 'This is not an array';
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->validate($secretary);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with invalid data (users value is not an array).');
    }
    
    private function test_validate_missing_required_field_values() {
        $secretary = $this->default_secretary;
        unset($secretary['last_name']);
        unset($secretary['email']);
        unset($secretary['phone_number']);
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->validate($secretary);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with invalid data (missing required field values).');
    }
    
    private function test_validate_invalid_email_address() {
        $secretary = $this->default_secretary;
        $secretary['email'] = 'This is invalid.';
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->validate($secretary);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with invalid data (invalid email address).');
    }
    
    // TEST DELETE METHOD -----------------------------------------------------
    private function test_delete() {
        $secretary = $this->default_secretary;
        $this->ci->db->insert('ea_users', $secretary);
        $secretary['id'] = intval($this->ci->db->insert_id());
        
        $delete_result = $this->ci->secretaries_model->delete($secretary['id']);
        $this->ci->unit->run($delete_result, TRUE, 'Test if delete() method has returned TRUE '
                . 'successfull deletion.');
        
        $num_rows = $this->ci->db->get_where('ea_users', array('id' => $secretary['id']))->num_rows();
        $this->ci->unit->run($num_rows, 0, 'Test if delete() method has successfully deleted '
                . 'the secretary record.');
        
        if ($num_rows > 0) {
            $this->ci->db->delete('ea_users', array('id' => $secretary['id']));
        }
    }
    
    private function test_delete_invalid_argument() {
        $invalid_id = 'This is invalid';
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->delete($invalid_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if delete() has thrown exception on '
                . 'invalid argument given.');
    }
    
    private function test_delete_record_does_not_exist() {
        $random_id = 23420930923; // no record exists with this id
        $delete_result = $this->ci->secretaries_model->delete($random_id);
        $this->ci->unit->run($delete_result, FALSE, 'Test if delete() method has returned FALSE '
                . 'when trying to delete a record that does not exist.');
    }
    
    // TEST GET ROW METHOD ----------------------------------------------------
    private function test_get_row() {
        $secretary = $this->default_secretary;
        $this->ci->db->insert('ea_users', $secretary);
        $secretary['id'] = intval($this->ci->db->insert_id());
        
        $secretary['providers'] = array();
        
        $secretary['settings'] = $this->default_settings;
        $secretary['settings']['id_users'] = $secretary['id'];
        $this->ci->db->insert('ea_user_settings', $secretary['settings']);
        
        unset($secretary['settings']['id_users'], $secretary['settings']['password']);
        
        $model_secretary = $this->ci->secretaries_model->get_row($secretary['id']);
        unset($model_secretary['settings']['password']);
        
        $this->ci->unit->run($secretary, $model_secretary, 'Test if get_row() has successfully '
                . 'returned the secretary record.');
        
        $this->ci->db->delete('ea_users', array('id' => $secretary['id']));
    }
    
    private function test_get_row_invalid_argument() {
        $invalid_id = 'this is invalid';
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->get_row($invalid_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_row() has thrown exception '
                . 'on invalid argument given.');
    }
    
    private function test_get_row_record_does_not_exist() {
        $random_id = 2309203923; // no record exists with this id.
        $has_thrown_exc = FALSE;
       try {
            $this->ci->secretaries_model->get_row($random_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_row() has thrown an exception '
                . 'when trying to get a record that does not exist in the database.');
    }
    
    // TEST GET VALUE METHOD --------------------------------------------------
    private function test_get_value() {
        $secretary = $this->default_secretary;
        $this->ci->db->insert('ea_users', $secretary);
        $secretary['id'] = intval($this->ci->db->insert_id());
        
        $field_name = 'last_name';
        
        $last_name = $this->ci->secretaries_model->get_value($field_name, $secretary['id']);
        $this->ci->unit->run($secretary['last_name'], $last_name, 'Test if get_value() has '
                . 'successfully returned the correct value.');
        
        $this->ci->db->delete('ea_users', array('id' => $secretary['id']));
    }
    
    private function test_get_value_invalid_field_name() {
        $field_name = 23423452342; // this is invalid
        $secretary_id = 1; // random pick
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->get_value($field_name, $secretary_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_value() has thrown exception '
                . 'on invalid field name.');
    }
    
    private function test_get_value_invalid_record_id() {
        $field_name = 'last_name'; // random pick
        $secretary_id = 'this is invalid';
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->get_value($field_name, $secretary_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_value() has thrown exception '
                . 'on invalid record id.');
    }
    
    private function test_get_value_record_does_not_exist() {
        $field_name = 'last_name';
        $secretary_id = 23092093233; // this record does not exist
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->get_value($field_name, $secretary_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_value() has thrown exception '
                . 'on record does not exist.');
    }
    
    private function test_get_value_field_does_not_exist() {
        $field_name = 'this field name does not exist';
        $secretary_id = 23; // random pick
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->secretaries_model->get_value($field_name, $secretary_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_value() has thrown exception '
                . 'on field name does not exist.');
    }
    
    // TEST GET BATCH METHOD --------------------------------------------------
    private function test_get_batch() {
        $model_batch = $this->ci->secretaries_model->get_batch();
        
        $no_model_batch = $this->ci->db->get_where('ea_users', 
                array('id_roles' => $this->secretary_role_id))->result_array();
        
        foreach($no_model_batch as &$secretary) {
            $providers = $this->ci->db->get_where('ea_secretaries_providers', 
                    array('id_users_secretary' => $secretary['id']))->result_array();
            $secretary['providers'] = array();
            foreach($providers as $provider) {
                $secretary['providers'][] = $provider['id_users_provider'];
            }
            
            $secretary['settings'] = $this->ci->db->get_where('ea_user_settings', 
                    array('id_users' => $secretary['id']))->row_array();
            unset($secretary['settings']['id_users']);
        }
        
        $this->ci->unit->run($model_batch, $no_model_batch, 'Test if get_batch() has '
                . 'returned the correct results.');
    }
    
    private function test_get_batch_where_clause() {
        $secretary = $this->default_secretary;
        $this->ci->db->insert('ea_users', $secretary);
        $secretary['id'] = intval($this->ci->db->insert_id());
        
        $secretary['settings'] = $this->default_settings;
        $secretary['settings']['id_users'] = $secretary['id'];
        $this->ci->db->insert('ea_user_settings', $secretary['settings']);
        unset($secretary['settings']['id_users']);
        
        $model_batch = $this->ci->secretaries_model->get_batch(array('id' => $secretary['id']));
        
        $no_model_batch = $this->ci->db->get_where('ea_users', array('id' => $secretary['id']))->result_array();
        foreach($no_model_batch as &$secretary) {
            $providers = $this->ci->db->get_where('ea_secretaries_providers', 
                    array('id_users_secretary' => $secretary['id']))->result_array();
            $secretary['providers'] = array();
            foreach($providers as $provider) {
                $secretary['providers'][] = $provider['id_users_provider'];
            }
            
            $secretary['settings'] = $this->ci->db->get_where('ea_user_settings', 
                    array('id_users' => $secretary['id']))->row_array();
            unset($secretary['settings']['id_users']);
        }
        
        $this->ci->unit->run($model_batch, $no_model_batch, 'Test if get_batch() with where clause '
                . 'has returned the correct results.');
        
        $this->ci->db->delete('ea_users', array('id' => $secretary['id']));
    }
}


/* End of file Unit_tests_secretaries_model.php */
/* Location: ./application/libraries/Unit_tests/drivers/Unit_tests_secretaries_model.php */