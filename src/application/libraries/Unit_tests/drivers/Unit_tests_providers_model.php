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
 * Providers Model Unit Tests
 *
 * @package Libraries
 * @subpackage Tests
 */
class Unit_tests_providers_model extends CI_Driver {
    private $ci;
    private $provider_role_id;
    private $default_working_plan;
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library('Unit_test');
        $this->ci->load->model('providers_model');
        
        $this->provider_role_id = $this->ci->db->get_where('ea_roles', 
                array('slug' => DB_SLUG_PROVIDER))->row()->id;
        
        $this->default_working_plan = $this->ci->db->get_where('ea_settings', 
                array('name' => 'company_working_plan'))->row()->value;
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
            'email' => 'test@test.com',
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id,
            'services' => array(),
            'settings' => array(
                'username' => 'test_prov',
                'password' => 'test_prov',
                'working_plan' => $this->default_working_plan,
                'notifications' => TRUE,
                'google_sync' => 0,
                'google_token' => NULL,
                'google_calendar' => NULL, 
                'sync_past_days' => '5',
                'sync_future_days' => '5'
            )
        );
        
        // Insert new provider.
        $provider['id'] = $this->ci->providers_model->add($provider);
        $this->ci->unit->run($provider['id'], 'is_int', 'Check if add (insert) result is integer.');
        
        // Check if record was inserted successfully.
        $db_provider = $this->ci->db
                ->get_where('ea_users', array('id' => $provider['id']))
                ->row_array();
        $db_provider['services'] = array();
        $db_provider['settings'] = $this->ci->db
                ->get_where('ea_user_settings', array('id_users' => $provider['id']))
                ->row_array();
        unset($db_provider['settings']['id_users'], $db_provider['settings']['salt'],
                $provider['settings']['password'], $db_provider['settings']['password']); // not needed
        
        $this->ci->unit->run($provider, $db_provider, 'Check if add(insert) has successfully '
                . 'inserted a provider record.');
        
        // Delete provider record (the relative ea_user_settings record will be deleted  
        // automatically because of the cascade on delete setting.
        if ($this->ci->db->get_where('ea_users', array('id' => $provider['id']))->num_rows() > 0) {
            $this->ci->db->delete('ea_users', array('id' => $provider['id']));
        }
    }
    
    private function test_add_update() {
        // Insert new provider.
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@test.com',
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $provider['id'] = $this->ci->db->insert_id();
        
        // Insert provider settings.
        $settings = array(
            'id_users' => $provider['id'],
            'username' => 'test_prov',
            'password' => 'test_prov',
            'working_plan' => $this->default_working_plan,
            'notifications' => TRUE,
            'google_sync' => 0,
            'google_token' => NULL,
            'google_calendar' => NULL,
            'sync_past_days' => '5',
            'sync_future_days' => '5'
        );
        $this->ci->db->insert('ea_user_settings', $settings);
        
        // Update provider and check whether the operation completed successfully.
        $provider = array(
            'id' => $provider['id'], // Keep the old ID, everything else changes.
            'first_name' => 'CHANGED',
            'last_name' => 'CHANGED',
            'email' => 'changed@changed.com',
            'mobile_number' => 'CHANGED',
            'phone_number' => 'CHANGED',
            'address' => 'CHANGED',
            'city' => 'CHANGED',
            'state' => 'CHANGED',
            'zip_code' => 'CHANGED',
            'notes' => 'CHANGED',
            'id_roles' => $this->provider_role_id,
            'services' => array(),
            'settings' => array(
                'username' => 'CHANGED',
                'password' => 'CHANGED',
                'working_plan' => $this->default_working_plan,
                'notifications' => TRUE,
                'google_sync' => 0,
                'google_token' => NULL,
                'google_calendar' => NULL,
                'sync_past_days' => '9', // changed
                'sync_future_days' => '8' // changed
            )
        );
        
        $update_result = $this->ci->providers_model->add($provider);
        $this->ci->unit->run($update_result, 'is_int', 'Check if add (update) result is integer.');
        
        // Check if record was updated successfully
        $db_provider = $this->ci->db
                ->get_where('ea_users', array('id' => $provider['id']))
                ->row_array();
        $db_provider['services'] = array();
        $db_provider['settings'] = $this->ci->db
                ->get_where('ea_user_settings', array('id_users' => $provider['id']))
                ->row_array();
        unset($db_provider['settings']['id_users'], $db_provider['settings']['salt'],
                $provider['settings']['password'], $db_provider['settings']['password']); // not needed
        
        $this->ci->unit->run($provider, $db_provider, 'Check if add(update) has successfully '
                . 'updated a provider record.');
        
        // Delete provider record (the relative ea_user_settings record will be deleted  
        // automatically because of the cascade on delete setting.
        if ($this->ci->db->get_where('ea_users', array('id' => $provider['id']))->num_rows() > 0) {
            $this->ci->db->delete('ea_users', array('id' => $provider['id']));
        }
    }
    
    private function test_add_with_invalid_data() {
        // Provider's data are missing required fields. That means that an excpetion
        // is expected.
        $provider = array(
            'first_name' => 'some name',
            'last_name' => 'some name',
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->providers_model->add($provider);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if add() with invalid data has ' 
                . 'thrown an exception');
    }
    
    // TEST EXISTS METHOD
    private function test_exists_with_record_that_exists() {
        // Insert new provider record.
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@test.com',
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $provider['id'] = $this->ci->db->insert_id();
        
        // Check whether exists() detects the record.
        $exists = $this->ci->providers_model->exists($provider);
        $this->ci->unit->run($exists, TRUE, 'Test exists() with record that exists.');
        
        // Delete provider record.
        if ($this->ci->db->get_where('ea_users', array('id' => $provider['id']))->num_rows() > 0) {
            $this->ci->db->delete('ea_users', array('id' => $provider['id']));
        }
    }
    
    private function test_exists_with_invalid_data() {
        // Insert new provider record.
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@test.com', 
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $provider['id'] = $this->ci->db->insert_id();
        
        // Email is not provided, an exception is expected.
        $has_thrown_exc = FALSE;
        try {
            unset($provider['email']);
            $this->ci->providers_model->exists($provider);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test exists() with invalid record data.');
        
        // Delete provider record.
        if ($this->ci->db->get_where('ea_users', array('id' => $provider['id']))->num_rows() > 0) {
            $this->ci->db->delete('ea_users', array('id' => $provider['id']));
        }
    }
    
    private function test_exists_with_record_that_does_not_exist() {
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@test.com', 
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id
        );
        
        $exists = $this->ci->providers_model->exists($provider);
        $this->ci->unit->run($exists, FALSE, 'Test exists() with record that does not exist.');
    }
    
    // TEST FIND RECORD ID METHOD
    private function test_find_record_id() {
        // Insert new provider record.
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@test.com', 
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $insert_id = $this->ci->db->insert_id();
        
        // Check if the record id will be retrieved.
        $provider['id'] = $this->ci->providers_model->find_record_id($provider);
        $this->ci->unit->run($provider['id'], $insert_id, 'Test if find_record_id() has '
                . 'successfully found the inserted record id.');
        
        // Delete provider record.
        if ($this->ci->db->get_where('ea_users', array('id' => $insert_id))->num_rows() > 0) {
            $this->ci->db->delete('ea_users', array('id' => $insert_id));
        }
    }
    
    private function test_find_record_id_with_invalid_data() {
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            //'email' => 'test@test.com',  // Email is not provided
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id
        );
        
        // An exception is expected.
        $has_thrown_exc = FALSE;
        try {
            $this->ci->providers_model->find_record_id($provider);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if find_record_id() has thrown ' 
                . 'exception on invalid data.');
    }
    
    private function test_find_record_id_with_record_that_does_not_exist() {
        // The following provider record does not exist on database. Therefore an
        // exception is expected.
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@test.com',  
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->providers_model->find_record_id($provider);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if find_record_id() has thrown ' 
                . 'exception on record that does not exist.');
    }
    
    // TEST VALIDATE RECORD METHOD
    private function test_validate() {
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@test.com',
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id,
            'services' => array(),
            'settings' => array(
                'username' => 'test_prov',
                'password' => 'test_prov',
                'working_plan' => $this->default_working_plan,
                'notifications' => TRUE,
                'google_sync' => FALSE,
                'google_token' => NULL,
                'sync_past_days' => '5',
                'sync_future_days' => '5'
            )
        );
        
        $validation_result = $this->ci->providers_model->validate($provider);
        $this->ci->unit->run($validation_result, TRUE, 'Test if validate() returned TRUE '
                . 'with valid data');
    }
    
    private function test_validate_with_record_that_does_not_exist() {
        $provider = array(
            'id' => '23209348092', // This id does not exists in db.
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@test.com',
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id,
            'services' => array(),
            'settings' => array(
                'username' => 'test_prov',
                'password' => 'test_prov',
                'working_plan' => $this->default_working_plan,
                'notifications' => TRUE,
                'google_sync' => FALSE,
                'google_token' => NULL,
                'sync_past_days' => '5',
                'sync_future_days' => '5'
            )
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->providers_model->validate($provider);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with record that does not exist.');
    }
    
    private function test_validate_with_missing_required_fields() {
        $provider = array(
            'first_name' => 'John',
            //'last_name' => 'Doe',
            //'email' => 'test@test.com',
            'mobile_number' => '000000',
            //'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id,
            'services' => array(),
            'settings' => array(
                'username' => 'test_prov',
                'password' => 'test_prov',
                'working_plan' => $this->default_working_plan,
                'notifications' => TRUE,
                'google_sync' => FALSE,
                'google_token' => NULL,
                'sync_past_days' => '5',
                'sync_future_days' => '5'
            )
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->providers_model->validate($provider);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with data that is missing required fields.');
    }
    
    private function test_validate_with_invalid_email_address() {
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'THIS IS INVALID',
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id,
            'services' => array(),
            'settings' => array(
                'username' => 'test_prov',
                'password' => 'test_prov',
                'working_plan' => $this->default_working_plan,
                'notifications' => TRUE,
                'google_sync' => FALSE,
                'google_token' => NULL,
                'sync_past_days' => '5',
                'sync_future_days' => '5'
            )
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->providers_model->validate($provider);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with data that has invalid email.');
    }
    
    private function test_validate_with_no_settings() {
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'THIS IS INVALID',
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->providers_model->validate($provider);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with data that do not contain the provider settings.');
    }
    
    // TEST DELETE RECORD METHOD
    private function test_delete() {
        // Insert new provider record with settings.
        $provider = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'THIS IS INVALID',
            'mobile_number' => '000000',
            'phone_number' => '111111',
            'address' => 'Some Str',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test provider',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $provider['id'] = intval($this->ci->db->insert_id());
        
        $provider['services'] = array();
        
        $provider['settings'] = array(
            'id_users' => $provider['id'],
            'username' => 'test-prov',
            'password' => 'test-prov',
            'notifications' => FALSE,
            'working_plan' => $this->default_working_plan,
            'google_sync' => FALSE,
            'google_token' => NULL,
            'sync_past_days' => '5',
            'sync_future_days' => 5
        );
        $this->ci->db->insert('ea_user_settings', $provider['settings']);
        
        // Delete provider record from database. 
        $result = $this->ci->providers_model->delete($provider['id']);
        $this->ci->unit->run($result, TRUE, 'Test if delete() returned TRUE as result');
        
        // Check if the record has successfully been deleted.
        $provider_num_rows = $this->ci->db->get_where('ea_users', 
                array('id' => $provider['id']))->num_rows();
        $this->ci->unit->run($provider_num_rows, 0, 'Test if delete() has successfully '
                . 'deleted provider record.');
        
        $settings_num_rows = $this->ci->db->get_where('ea_user_settings', 
                array('id_users' => $provider['id']))->num_rows();
        $this->ci->unit->run($settings_num_rows, 0, 'Test if delete() has successfully '
                . 'deleted provider settings record.');
        
        // Delete records (if needed)
        if ($provider_num_rows > 0) { 
            $this->ci->db->delete('ea_users', array('id' => $provider['id']));
        }
        
        if ($settings_num_rows > 0) {
            $this->ci->db->delete('ea_user_settings', array('id_users' => $provider['id']));
        }
    }
    
    private function test_delete_with_invalid_record_id() {
        $invalid_id = 'this is invalid';
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->providers_model->delete($invalid_id);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if delete() with invalid id argument '
                . 'has thrown an exception.');
    }
    
    private function test_delete_with_record_that_does_not_exist() {
        $provider_id = '234082390'; // This record id does not exist in db.
        $result = $this->ci->providers_model->delete($provider_id);
        $this->ci->unit->run($result, FALSE, 'Test if delete() with record id that does '
                . 'not exist in database, has returned FALSE as result.');
    }
    
    // TEST GET ROW METHOD      
    private function test_get_row() {
        // Insert a new provider record with settings. 
        $provider = array(
            'last_name' => 'Doe',
            'first_name' => 'John',
            'email' => 'john@doe.com',
            'phone_number' => '0123456789',
            'address' => 'Abbey Road 18',
            'city' => 'London',
            'zip_code' => '12345',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $provider['id'] = $this->ci->db->insert_id();
        
        $provider['services'] = array();
        
        $provider['settings'] = array(
            'id_users' => $provider['id'],
            'username' => 'testprov',
            'password' => 'testprov',
            'notifications' => FALSE,
            'working_plan' => $this->default_working_plan,
            'google_sync' => FALSE,
            'google_token' => NULL,
            'sync_past_days' => '5',
            'sync_future_days' => '5'
        );
        $this->ci->db->insert('ea_user_settings', $provider['settings']);
        unset($provider['settings']['id_users']);
        
        // Check if get_row() will successfully return the record.
        $no_model_provider = $this->ci->db
                ->get_where('ea_users', array('id' => $provider['id']))
                ->row_array();
        $no_model_provider['services'] = array();
        $no_model_provider['settings'] = $this->ci->db
                ->get_where('ea_user_settings', array('id_users' => $provider['id']))
                ->row_array();
        unset($no_model_provider['settings']['id_users']);
        
        $model_provider = $this->ci->providers_model->get_row($provider['id']);
        
        $this->ci->unit->run($no_model_provider, $model_provider, 'Test get_row() method successfully ' 
                . 'returned provider record.');
        
        // Delete inserted provider record.
        $this->ci->db->delete('ea_users', array('id' => $provider['id']));
    }
    
    private function test_get_row_that_does_not_exist() {
        $random_record_id = 486868412;
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->providers_model->get_row($random_record_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_row() with record id that does ' 
                . 'not exist in the database has thrown an exception.');
    }
    
    private function test_get_row_with_invalid_argument() {
        $invalid_id = 'THIS IS NOT AN INTEGER';
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->providers_model->get_row($invalid_id);        
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test get_row() with wrong argument.');
    }
    
    // TEST GET VALUE METHOD    ---------------------------------------------
    private function test_get_value() {
        // Insert new provider record.
        $provider = array(
            'last_name' => 'Doe',
            'first_name' => 'John',
            'email' => 'john@doe.com',
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
        // Insert new provider record.
        $provider = array(
            'last_name' => 'Doe',
            'first_name' => 'John',
            'email' => 'john@doe.com',
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
    
    // TEST GET BATCH METHOD    
    private function test_get_batch() {
        // Get all the provider rows without using the model.
        $db_data = $this->ci->db
                ->get_where('ea_users', array('id_roles' => $this->provider_role_id))
                ->result_array();
        
        foreach($db_data as &$db_provider) {
            $services = $this->ci->db
                    ->get_where('ea_services_providers', array('id_users' => $db_provider['id']))
                    ->result_array();
            $db_provider['services'] = array();
            foreach($services as $service) {
                $db_provider['services'][] = $service['id_services'];
            }
            
            
            $db_provider['settings'] = $this->ci->db
                    ->get_where('ea_user_settings', array('id_users' => $db_provider['id']))
                    ->row_array();
            unset($db_provider['settings']['id_users']);
        }
        
        // Get all the provider rows by using the model.
        $model_data = $this->ci->providers_model->get_batch();
        
        // Check that the two arrays are the same.
        $this->ci->unit->run($db_data, $model_data, 'Test get_batch() method.');
    }
    
    private function test_get_batch_with_where_clause() {
        // Insert new provider record and try to fetch it.
        $provider = array(
            'last_name' => 'Doe',
            'first_name' => 'John',
            'email' => 'john@doe.com',
            'phone_number' => '0123456789',
            'address' => 'Abbey Road 18',
            'city' => 'London',
            'zip_code' => '12345',
            'id_roles' => $this->provider_role_id
        );
        $this->ci->db->insert('ea_users', $provider);
        $provider['id'] = intval($this->ci->db->insert_id());
        
        $settings = array(
            'id_users' => $provider['id'],
            'username' => 'testprov',
            'password' => 'testprov', 
            'notifications' => FALSE,
            'working_plan' => $this->default_working_plan,
            'google_sync' => FALSE,
            'google_token' => NULL,
            'sync_past_days' => '5',
            'sync_future_days' => '5' 
        );
        $this->ci->db->insert('ea_user_settings', $settings);
        
        // Get provider records without using the model.
        $no_model_batch = $this->ci->db
                ->get_where('ea_users', array('id' => $provider['id']))
                ->result_array();
        
        foreach($no_model_batch as &$no_model_provider) {
            $services = $this->ci->db
                    ->get_where('ea_services_providers', array('id_users' => $provider['id']))
                    ->result_array();
            $no_model_provider['services'] = array();
            foreach($services as $service) {
                $no_model_provider['services'][] = $service['id_services'];
            }
            
            
            $no_model_provider['settings'] = $this->ci->db
                    ->get_where('ea_user_settings', array('id_users' => $no_model_provider['id']))
                    ->row_array();
            unset($no_model_provider['settings']['id_users']);
        }
        
        // Get data by using the model. 
        $model_batch = $this->ci->providers_model->get_batch(array('id' => $provider['id']));
        
        // Check that the data arrays are the same.
        $this->ci->unit->run($no_model_batch, $model_batch, 'Test get_batch() with where clause.');
        
        // Delete inserted record from database.
        $this->ci->db->delete('ea_users', array('id' => $provider['id']));
    }

    private function unabled_test_get_batch_with_invalid_where_clause() {
        // CodeIgniter auto raises an exception if the where clause is invalid.
    }
}

/* End of file Unit_tests_providers_model.php */
/* Location: ./application/libraries/Unit_tests/drivers/Unit_tests_providers_model.php */
