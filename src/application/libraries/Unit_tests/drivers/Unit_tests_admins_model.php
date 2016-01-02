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
 * Admins Model Unit Tests
 *
 * @package Libraries
 * @subpackage Tests
 */
class Unit_tests_admins_model extends CI_Driver {
    private $ci;
    private $admin_role_id;
    private $default_admin; // does not contain an 'id' value
    private $default_settings; // does not contain 'id_users' value
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library('Unit_test');
        $this->ci->load->model('admins_model');
        
        $this->admin_role_id = $this->ci->db->get_where('ea_roles', 
                array('slug' => DB_SLUG_ADMIN))->row()->id;
        
        $this->default_admin = array(
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'mobile_number' => '2340982039',
            'phone_number' => '9098091234',
            'address' => 'Some Street 80',
            'city' => 'Some City',
            'state' => 'Some State',
            'zip_code' => '12345',
            'notes' => 'This is a test admin user.',
            'id_roles' => $this->admin_role_id
        );
        
        $this->default_settings = array(
            'username' => 'test_admin',
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
        $class_methods = get_class_methods('Unit_tests_admins_model');
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
        $admin = $this->default_admin;
        $admin['settings'] = $this->default_settings;
        
        $admin['id'] = $this->ci->admins_model->add($admin);
        $this->ci->unit->run($admin['id'], 'is_int', 'Test if add() - insert operation - has ' 
                . 'has returned an integer value.');
        
        $db_record = $this->ci->db->get_where('ea_users', array('id' => $admin['id']))->row_array();
        
        $db_record['settings'] = $this->ci->db->get_where('ea_user_settings', 
                array('id_users' => $admin['id']))->row_array();
        unset($db_record['settings']['id_users'], $db_record['settings']['salt'],
                $admin['settings']['password'], $db_record['settings']['password']); // not needed
        $this->ci->unit->run($admin, $db_record, 'Test if add() - insert operation - has '
                . 'successfully inserted a new admin record.');
        
        $this->ci->db->delete('ea_users', array('id' => $admin['id']));
    }
    
    private function test_add_update() {
        // Insert an admin record first and then update it.
        $admin = $this->default_admin;
        $this->ci->db->insert('ea_users', $admin);
        $admin['id'] = intval($this->ci->db->insert_id());
        $admin['settings'] = $this->default_settings;
        $admin['settings']['id_users'] = $admin['id'];
        $this->ci->db->insert('ea_user_settings', $admin['settings']);
        unset($admin['settings']['id_users']);
        
        $admin['first_name'] = 'First Name Changed';
        $admin['last_name'] = 'Last Name Changed';
        $admin['email'] = 'email@changed.com';
        $admin['mobile_number'] = 'Mobile Number Changed';
        $admin['phone_number'] = 'Phone Number Changed';
        $admin['address'] = 'Address Changed';
        $admin['city'] = 'City Changed';
        $admin['zip_code'] = 'Zip Code Changed';
        $admin['notes'] = 'Notes Changed';
        
        $update_result = $this->ci->admins_model->add($admin); // updates record
        $this->ci->unit->run($update_result, 'is_int', 'Test if add() - update operation - has '
                . 'returned a valid integer value.');
        
        $db_record = $this->ci->db->get_where('ea_users', array('id' => $admin['id']))->row_array();
        $db_record['settings'] = $this->ci->db->get_where('ea_user_settings', array('id_users' => $admin['id']))->row_array();
        unset($db_record['settings']['id_users'], $db_record['settings']['salt'],
                $admin['settings']['password'], $db_record['settings']['password']); // not needed

        $this->ci->unit->run($admin, $db_record, 'Test if add() - update operation - has '
                . 'successfully updated an existing admin record.');
        
        $this->ci->db->delete('ea_users', array('id' => $admin['id']));
    }
    
    private function test_add_with_invalid_data() {
        $admin = $this->default_admin;
        $admin['email'] = 'Invalid Email Value';
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->add($admin);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if add() has thrown an exception with '
                . 'invalid data.');
    }
    
    
    // TEST EXITS METHOD ----------------------------------------------------
    private function test_exists_with_record_that_exists() {
        $admin = $this->default_admin;
        $this->ci->db->insert('ea_users', $admin);
        $admin['id'] = intval($this->ci->db->insert_id());
        
        $exists = $this->ci->admins_model->exists($admin);
        $this->ci->unit->run($exists, TRUE, 'Test if exists() has returned TRUE on record '
                . 'that exists.');
        
        $this->ci->db->delete('ea_users', array('id' => $admin['id']));
    }
    
    private function test_exists_with_invalid_data() {
        $admin = $this->default_admin;
        unset($admin['email']); // Email is mandatory for the function
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->exists($admin);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if exists() has thrown an exception '
                . 'on invalid admin data.');
    }
    
    private function test_exists_with_record_that_does_not_exist() {
        $admin = $this->default_admin;
        $exists = $this->ci->admins_model->exists($admin);
        $this->ci->unit->run($exists, FALSE, 'Test if exists() returned FALSE with record '
                . 'that does not exist.');
    }
    
    // TEST DELETE METHOD ---------------------------------------------------
    private function test_delete() {
        $admin = $this->default_admin;
        $this->ci->db->insert('ea_users', $admin);
        $admin['id'] = intval($this->ci->db->insert_id());
        
        $delete_result = $this->ci->admins_model->delete($admin['id']);
        $this->ci->unit->run($delete_result, TRUE, 'Test if delete() has returned TRUE on '
                . 'successfull deletion.');
        
        $num_rows = $this->ci->db->get_where('ea_users', array('id' => $admin['id']))->num_rows();
        $this->ci->unit->run($num_rows, 0, 'Test if delete() has successfully removed the '
                . 'admin record from the database.');
        
        if ($num_rows > 0) {
            $this->ci->db->delete('ea_users', array('id' => $admin['id']));
        }
    }
    
    private function test_delete_with_record_that_does_not_exist() {
        $random_id = 2340923234;
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->delete($random_id);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if delete() has thrown exception '
                . 'on record that does not exist.');
    }
    
    private function test_delete_with_invalid_id_argument() {
        $invalid_id = 'Not Numeric Value';
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->delete($invalid_id);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if delete() has thrown exception '
                . 'on invalid id argument.');
    }
    
    private function test_delete_when_only_one_admin_user_left() {
        // E!A must always have at least one admin user in the system
        // This unit test requires only one admin record present in the database.
        $admin_id = $this->ci->db->get_where('ea_users', array('id_roles' => $this->admin_role_id))
                ->row()->id;
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->delete($admin_id);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if delete() has thrown exception '
                . 'when trying to delete the only one left admin record.');
    }
    
    // TEST FIND RECORD ID METHOD -------------------------------------------
    private function test_find_record_id() {
        $admin = $this->default_admin;
        $this->ci->db->insert('ea_users', $admin);
        $admin['id'] = intval($this->ci->db->insert_id());
        
        $result = $this->ci->admins_model->find_record_id($admin);
        $this->ci->unit->run($result, $admin['id'], 'Test if find_record_id() has successfully '
                . 'found the correct record id.');
        
        $this->ci->db->delete('ea_users', array('id' => $admin['id']));
    }
    
    private function test_find_record_id_with_no_email_provided() {
        $admin = $this->default_admin;
        unset($admin['email']);
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->find_record_id($admin);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if find_record_id() has thrown '
                . 'an exception when trying to find a record id without email provided.');
    }
    
    private function test_find_record_id_with_record_that_does_not_exist() {
        $admin = $this->default_admin;
        $has_thrown_ecx = FALSE;
        try {
            $this->ci->admins_model->find_record_id($admin);
        } catch (Exception $exc) {
            $has_thrown_ecx = TRUE;
        }
        $this->ci->unit->run($has_thrown_ecx, TRUE, 'Test if find_record_id() has thrown '
                . 'exception when trying to find record that does not exist.');
    }
    
    
    // TEST GET BATCH METHOD ------------------------------------------------
    private function test_get_batch() {
        $model_batch = $this->ci->admins_model->get_batch();
        
        $db_batch = $this->ci->db->get_where('ea_users', 
                array('id_roles' => $this->admin_role_id))->result_array();
        
        foreach($db_batch as &$admin) {  // Get each admin settings
            $admin['settings'] = $this->ci->db->get_where('ea_user_settings', 
                    array('id_users' => $admin['id']))->row_array();
            unset($admin['settings']['id_users']);
        }
        
        
        $this->ci->unit->run($model_batch, $db_batch, 'Test if get_batch() has successfully '
                . 'returned an array of admin users.');
    }
    
    private function test_get_batch_with_where_clause() {
        $admin = $this->default_admin;
        $this->ci->db->insert('ea_users', $admin);
        $admin['id'] = intval($this->ci->db->insert_id());
        
        $model_batch = $this->ci->admins_model->get_batch(array('id' => $admin['id']));
        $db_batch = $this->ci->db->get_where('ea_users', array('id' => $admin['id']))->result_array();
        foreach($db_batch as &$admin) {
            $admin['settings'] = array();
        }
        
        $this->ci->unit->run($model_batch, $db_batch, 'Test if get_batch() with where clause ' 
                . 'has successfully returned the correct results.');
        
        $this->ci->db->delete('ea_users', array('id' => $admin['id']));
    }
    
    private function test_get_batch_with_invalid_where_clause() {
        // CI raises by itself an exception so this test case can be ignored.
    }
    
    // TEST GET ROW METHOD --------------------------------------------------
    private function test_get_row() {
        $admin = $this->default_admin;
        $this->ci->db->insert('ea_users', $admin);
        $admin['id'] = intval($this->ci->db->insert_id());
        $admin['settings'] = array();
        
        $model_admin = $this->ci->admins_model->get_row($admin['id']);
        $this->ci->unit->run($model_admin, $admin, 'Test if get_row() has successfully '
                . 'returned the data of the selected row.');
        
        $this->ci->db->delete('ea_users', array('id' => $admin['id']));
    }
    
    private function test_get_row_invalid_argument() {
        $invalid_id = 'This is not numeric.';
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->get_row($invalid_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_row() has thrown exception with '
                . 'invalid argument.');
    }
    
    private function test_get_row_record_does_not_exist() {
        $random_id = 2390482039;
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->get_row($random_id);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_row() has thrown exception '
                . 'with record that does not exist.');
    }
    
    // TEST GET VALUE METHOD ------------------------------------------------
    private function test_get_value() {
        $admin = $this->default_admin;
        $this->ci->db->insert('ea_users', $admin);
        $admin['id'] = intval($this->ci->db->insert_id());
        
        $last_name = $this->ci->admins_model->get_value('last_name', $admin['id']);
        $this->ci->unit->run($last_name, $admin['last_name'], 'Test if get_value() has successfully '
                . 'returned the correct value.');
        
        $this->ci->db->delete('ea_users', array('id' => $admin['id']));
    }
    
    private function test_get_value_invalid_field_name() {
        $field_name = 230982039; // invalid: not string
        $admin_id = 23; // random pick
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->get_value($field_name, $admin_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_value() has thrown an exception '
                . 'with invalid field name argument.');
    }
    
    private function test_get_value_invalid_admin_id() {
        $field_name = 'last_name'; // random pick
        $admin_id = 'This is invalid'; // not numerical
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->get_value($field_name, $admin_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_value() has thrown an exception '
                . 'with invalid admin id argument.');
    }
    
    private function test_get_value_record_does_not_exist() {
        $field_name = 'last_name'; // random pick
        $admin_id = 239409283092; // this id does not exist
        
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->get_value($field_name, $admin_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_value() has thrown an exception '
                . 'with record that does not exist.');
    }
    
    private function tes_get_value_field_name_does_not_exist() {
        $field_name = 'this does not exist';
        $admin_id = 23; // random pick
        
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->get_value($field_name, $admin_id);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_value() has thrown an exception '
                . 'with field name that does not exist.');
    }
    
    // TEST VALIDATE METHOD -------------------------------------------------
    private function test_validate() {
        $admin = $this->default_admin;
        $validation_result = $this->ci->admins_model->validate($admin);
        $this->ci->unit->run($validation_result, TRUE, 'Test if validate() has returned TRUE on '
                . 'valid admin data.');
    }
    
    private function test_validate_record_does_not_exist() {
        $admin = $this->default_admin;
        $admin['id'] = 234092830; // record does not exist
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->validate($admin);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with record that does not exist.');
    }
    
    private function test_validate_missing_required_fields() {
        // required fields: last_name, email, phone_number
        
        $admin = $this->default_admin;
        unset($admin['last_name']);
        unset($admin['email']);
        unset($admin['phone_number']);
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->validate($admin);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with missing required field values.');
    }
    
    private function test_validate_invalid_email() {
        $admin = $this->default_admin;
        $admin['email'] = 'This is invalid';
        $has_thrown_exc = FALSE;
        try {
            $this->ci->admins_model->validate($admin);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with invalid email address.');
    }
}

/* End of file Unit_tests_admins_model.php */
/* Location: ./application/libraries/Unit_tests/drivers/Unit_tests_admins_model.php */