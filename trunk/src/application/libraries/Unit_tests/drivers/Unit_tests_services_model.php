<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Unit_tests_services_model extends CI_Driver {
    private $ci;
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->library('Unit_test');
        $this->ci->load->model('services_model');
    }
    
    /**
     * Run all the available tests
     */
    public function run_all() {
        // All the methods whose names start with "test" are going to be 
        // executed. If you want a method to not be executed remove the 
        // "test" keyword from the beginning.
        $class_methods = get_class_methods('Unit_tests_services_model');
        foreach ($class_methods as $method_name) {
            if (substr($method_name, 0, 5) === 'test_') {
                call_user_func(array($this, $method_name));
            }
        }
    }
    
    /////////////////////////////////////////////////////////////////////////
    // UNIT TESTS
    ///////////////////////////////////////////////////////////////////////// 
    
    // TEST ADD METHOD -----------------------------------------------------
    private function test_add_insert() {
        $service = array(
            'name' => 'Test Service',
            'duration' => 30,
            'price' => '20.00',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => NULL
        );
        
        $service['id'] = $this->ci->services_model->add($service);
        $this->ci->unit->run($service['id'], 'is_int', 'Test if add() - insert operation has '
                . 'returned a valid record id.');
        
        $db_record = $this->ci->db->get_where('ea_services', array('id' => $service['id']))->row_array();
        $this->ci->unit->run($service, $db_record, 'Test if add() - insert operation, has '
                . 'successfully inserted a new record.');
        
        $this->ci->db->delete('ea_services', array('id' => $service['id']));
    }
    
    private function test_add_update() {
        $service = array(
            'name' => 'Test Service',
            'duration' => 30,
            'price' => '20.00',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => NULL
        );

        $this->ci->db->insert('ea_services', $service);
        $service['id'] = $this->ci->db->insert_id();
        
        // Update record data
        $service['name'] = 'Updated Name';
        $service['duration'] = 60;
        $service['price'] = '60.45';
        $service['description'] = 'Updated description ...';
        
        $update_result = $this->ci->services_model->add($service);
        $this->ci->unit->run($update_result, 'is_int', 'Test if add() - update operation, has '
                . 'returned the record id.');
        
        $db_record = $this->ci->db->get_where('ea_services', array('id' => $service['id']))->row_array();
        $this->ci->unit->run($service, $db_record, 'Test if add() - update operation, has '
                . 'successfully updated a record.');
        
        $this->ci->db->delete('ea_services', array('id' => $service['id']));   
    }
    
    private function test_add_invalid_data() {
        $service = array(
            'name' => '', // invalid
            'duration' => 'invalid',
            'price' => 'invalid',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => NULL
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->services_model->add($service);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Check if add() with invalid data has '
                . 'thrown exception.');
    }
    
    // TEST EXISTS METHOD --------------------------------------------------
    private function test_exists_record_exists() {
        // insert record
        $service = array(
            'name' => 'Test Service',
            'duration' => 30,
            'price' => '20.00',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => NULL
        );

        $this->ci->db->insert('ea_services', $service);
        $service['id'] = $this->ci->db->insert_id();
        
        // check exists
        $exists = $this->ci->services_model->exists($service);
        $this->ci->unit->run($exists, TRUE, 'Check if exists() returned true on existing record.');
        
        // delete record
        $this->ci->db->delete('ea_services', array('id' => $service['id'])); 
    }
    
    private function test_exists_record_does_not_exist() {
        $service = array(
            'name' => 'Random Name',
            'duration' => 'Random Duration',
            'price' => 'Random Price'
        );
        
        $exists = $this->ci->services_model->exists($service);
        $this->ci->unit->run($exists, FALSE, 'Check if exists() returned false on non-existing record.');
    }
    
    private function test_exists_invalid_arguments() {
        $invalid_arg = '';
        
        $has_thrown_exc = FALSE;
        try {
            $exists = $this->ci->services_model->exists($invalid_arg);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Check if exists() with invalid argument has '
                . 'thrown an exception.');
    }
    
    // TEST VALIDATE METHOD -------------------------------------------------
    private function test_validate() {
        $service = array(
            'name' => 'Test Service',
            'duration' => 30,
            'price' => '20.00',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => NULL
        );
        
        $is_valid = $this->ci->services_model->validate($service);
        $this->ci->unit->run($is_valid, TRUE, 'Test validate() method with valid data.');
    }
    
    private function test_validate_invalid_record_id() {
        $service = array(
            'id' => 'THIS IS INVALID',
            'name' => 'Test Service',
            'duration' => 30,
            'price' => '20.00',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => NULL
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->services_model->validate($service);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with invalid record id.');
    }
    
    private function test_validate_invalid_service_category_id() {
        $service = array(
            'name' => 'Test Service',
            'duration' => 30,
            'price' => '20.00',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => 'THIS IS INVALID'
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->services_model->validate($service);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with invalid service category id.');
    }
    
    private function test_validate_invalid_service_name() {
        $service = array(
            'name' => '', // invalid
            'duration' => 30,
            'price' => '20.00',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => 'THIS IS INVALID'
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->services_model->validate($service);
        } catch (Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() has thrown an exception '
                . 'with invalid service service name.');
    }
    
    // TEST FIND RECORD ID --------------------------------------------------
    private function test_find_record_id() {
        // insert record
        $service = array(
            'name' => 'Test Service',
            'duration' => 30,
            'price' => '20.00',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => NULL
        );

        $this->ci->db->insert('ea_services', $service);
        $service['id'] = intval($this->ci->db->insert_id());
        
        // find record id
        $service_id = $this->ci->services_model->find_record_id($service);
        $this->ci->unit->run($service_id, $service['id'], 'Test find_record_id() with record '
                . 'that exist.');
        
        // delete record
        $this->ci->db->delete('ea_services', array('id' => $service['id'])); 
    }
    
    private function test_find_record_id_invalid_service_data() {
        $service = array(
            // name, duration and price are not provided
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => NULL
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->services_model->find_record_id($service);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test find_record_id() with invalid service data.');
    }
    
    private function test_find_record_id_record_does_not_exist() {
        $service = array(
            'name' => 'Does not exist',
            'duration' => 'Does not exist',
            'price' => 'Does not exist'
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->services_model->find_record_id($service);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test find_record_id() with record that '
                . 'does not exist.');
    }
    
    // TEST DELETE METHOD ---------------------------------------------------
    private function test_delete() {
        // insert record
        $service = array(
            'name' => 'Test Service',
            'duration' => 30,
            'price' => '20.00',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => NULL
        );
        
        $this->ci->db->insert('ea_services', $service);
        $service['id'] = intval($this->ci->db->insert_id());
        
        // delete record
        $delete_result = $this->ci->services_model->delete($service['id']);
        $this->ci->unit->run($delete_result, TRUE, 'Test delete() method result.');
        
        // check if record was actually deleted
        $num_rows = $this->ci->db->get_where('ea_services', array('name' => $service['name']))
                ->num_rows();
        $this->ci->unit->run($num_rows, 0, 'Test if delete() method has actually deleted the record.');
    }
    
    private function test_delete_invalid_argument() {
        $invalid_arg = 'THIS IS INVALID';
        $has_thrown_exc = FALSE;
        try {
            $this->ci->services_model->delete($invalid_arg);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if delete() method has thrown '
                . 'exception on invalid argument.');
    }
    
    private function test_delete_record_does_not_exist() {
        $random_id = 1029800987;
        $has_thrown_exc = FALSE;
        $delete_result = $this->ci->services_model->delete($random_id);
        $this->ci->unit->run($delete_result, FALSE, 'Test if delete() method result on record '
                . 'that does not exist.');
    }
    
    
    // TEST GET BATCH METHOD        -----------------------------------------
    private function test_get_batch() {
        // Get all the service rows without using the model.
        $db_data = $this->ci->db->get('ea_services')->result_array();
        // Get all the service rows by using the model.
        $model_data = $this->ci->services_model->get_batch();
        // Check that the two arrays are the same.
        $this->ci->unit->run($db_data, $model_data, 'Test get_batch() method.');
    }
    
    private function test_get_batch_with_where_clause() {
        // Insert new service record.
        $service = array(
            'name' => 'General Examination',
            'duration' => 30,
            'price' => 50.00,
            'currency' => 'euro',
            'description' => 'This is some service description.'
        );
        
        $this->ci->db->insert('ea_services', $service);
        $service['id'] = intval($this->ci->db->insert_id());
        
        // Get data without using the model.
        $no_model_data = $this->ci->db->get_where('ea_services', array('id' => $service['id']))
                ->result_array();
        
        // Get data by using the model. 
        $model_data = $this->ci->services_model->get_batch(array('id' => $service['id']));
        
        // Check that the data arrays are the same.
        $this->ci->unit->run($no_model_data, $model_data, 'Test get_batch() with where clause.');
        
        // Delete inserted record from database.
        $this->ci->db->delete('ea_services', array('id' => $service['id']));
    }

    private function unabled_test_get_batch_with_invalid_where_clause() {
        // CodeIgniter auto raises an exception if the where section is invalid.
    }
    
    // TEST GET ROW METHOD          -----------------------------------------
    private function test_get_row() {
        // Insert a new service record. 
        $service = array(
            'name' => 'General Examination',
            'duration' => 30,
            'price' => 50.00,
            'currency' => 'euro',
            'description' => 'This is some service description.'
        );
        
        $this->ci->db->insert('ea_services', $service);
        $service['id'] = intval($this->ci->db->insert_id());
        
        // Get the new service record from db.
        $no_model_data = $this->ci->db->get_where('ea_services', array('id' => $service['id']))
                ->row_array();
        $model_data = $this->ci->services_model->get_row($service['id']);
        
        // Check that the row is the correct one.
        $this->ci->unit->run($no_model_data, $model_data, 'Test get_row() method');
        
        // Delete inserted service record.
        $this->ci->db->delete('ea_services', array('id' => $service['id']));
    }
    
    private function test_get_row_that_does_not_exist() {
        $random_record_id = 486868412;
        $row_data = $this->ci->services_model->get_row($random_record_id);
        $this->ci->unit->run($row_data, NULL, 'Test get_row() with record id that does ' 
                . 'not exist in the database.');
    }
    
    private function test_get_row_with_invalid_argument() {
        $invalid_id = 'THIS IS NOT AN INTEGER';
        
        $has_thrown_exception = FALSE;
        try {
            $this->ci->services_model->get_row($invalid_id);        
        } catch (Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test get_row() with wrong argument.');
    }
    
    // TEST GET VALUE METHOD        -----------------------------------------
    private function test_get_value() {
        // Insert new service record.
        $service = array(
            'name' => 'General Examination',
            'duration' => 30,
            'price' => 50.00,
            'currency' => 'euro',
            'description' => 'This is some service description.'
        );
        $this->ci->db->insert('ea_services', $service);
        $service['id'] = intval($this->ci->db->insert_id());
        
        // Get a specific value from the database.
        $model_value = $this->ci->services_model->get_value('name', $service['id']);
        
        // Check if the value was correctly fetched from the database.
        $this->ci->unit->run($model_value, $service['name'], 'Test get_value() method.');
        
        // Delete inserted appointment record.
        $this->ci->db->delete('ea_services', array('id' => $service['id']));
    }
    
    private function test_get_value_record_does_not_exist() {
        $random_record_id = 843521368768;
        
        $has_thrown_exception = FALSE;
        
        try {
            $this->ci->services_model->get_value('name', $random_record_id);
        } catch (Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test get_value() with record id that '
                . 'does not exist.');
    }
    
    private function test_get_value_field_does_not_exist() {
        // Insert new service record.
        $service = array(
            'name' => 'General Examination',
            'duration' => 30,
            'price' => 50.00,
            'currency' => 'euro',
            'description' => 'This is some service description.'
        );
        $this->ci->db->insert('ea_services', $service);
        $service['id'] = intval($this->ci->db->insert_id());
        
        // Try to get record value with wrong field name.
        $wrong_field_name = 'THIS IS WRONG';
        $has_thrown_exception = FALSE;
        
        try {
            $this->ci->services_model->get_value($wrong_field_name, $service['id']);
        } catch (Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test get_value() with record id that '
                . 'does not exist.');
        
        // Delete inserted service record.
        $this->ci->db->delete('ea_services', array('id' => $service['id']));
    }
    
    // TEST ADD SERVICE CATEGORY METHOD ---------------------------------------
    private function test_add_category_insert() {
        $category = array(
            'name' => 'Test Category',
            'description' => 'Test Description ...'
        );
        
        $category['id'] = $this->ci->services_model->add_category($category);
        $this->ci->unit->run($category['id'], 'is_int', 'Test if add_category() - insert '
                . 'operation has returned a valid record id');
        
        $db_record = $this->ci->db->get_where('ea_service_categories', array('id' => $category['id']))->row_array();
        $this->ci->unit->run($category, $db_record, 'Test if add_category() - insert '
                . 'operation has successfully inserted the record.');
        
        $this->ci->db->delete('ea_service_categories', array('id' => $category['id']));
    }
    
    private function test_add_category_update() {
        $category = array(
            'name' => 'Test Category',
            'description' => 'Test Description ...'
        );
        
        $this->ci->db->insert('ea_service_categories', $category);
        $category['id'] = intval($this->ci->db->insert_id());
        
        // update record
        $category['name'] = 'new name';
        $category['description'] = 'new description';
        
        $update_result = $this->ci->services_model->add_category($category);
        $this->ci->unit->run($update_result, 'is_int', 'Check if add_category() - update '
                . 'operation has returned a valid record id.');
        
        $db_record = $this->ci->db->get_where('ea_service_categories', 
                array('id' => $category['id']))->row_array();
        $this->ci->unit->run($category, $db_record, 'Test if add_category() - update operation '
                . 'has successfully updated a record.');
        
        $this->ci->db->delete('ea_service_categories', array('id' => $category['id']));
    }        
    
    private function test_add_category_invalid_data() {
        $category = array(
            'name' => '', // invalid
            'descrption' => ''
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->services_model->add_category($category);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if add_category() with invalid data '
                . 'has thrown an exception.');
    }
    
    // TEST DELETE SERVICE CATEGORY METHOD -------------------------------------
    private function test_delete_category() {
        // insert record
        $category = array(
            'name' => 'Test Category',
            'description' => 'Test Description ...'
        );
        
        $this->ci->db->insert('ea_service_categories', $category);
        $category['id'] = intval($this->ci->db->insert_id());
        
        // delete record
        $delete_result = $this->ci->services_model->delete_category($category['id']);
        $this->ci->unit->run($delete_result, TRUE, 'Test if delete_category() method has '
                . 'returned a valid result.');
        
        // check if record was actually deleted.
        $num_rows = $this->ci->db->get_where('ea_service_categories', 
                array('id' => $category['id']))->num_rows();
        $this->ci->unit->run($num_rows, 0, 'Check if delete_category() has actually deleted '
                . 'the record.');
        
        if ($num_rows > 0) {
            $this->ci->db->delete('ea_service_categories', array('id' => $category['id']));
        }
    }
    
    private function test_delete_category_invalid_argument() {
        $invalid_arg = 'This is invalid';
        $has_thrown_exc = TRUE;
        try {
            $this->ci->services_model->delete_category($invalid_arg);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if delete_category() with invalid '
                . 'argument has thrown an exception.');
    }
    
    private function test_delete_category_record_does_not_exist() {
        $random_id = 09182093;
        $has_thrown_exc = TRUE;
        try {
            $this->ci->services_model->delete_category($random_id);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if delete_category() with random id '
                . 'has thrown an exception.');
    }
    
    // TEST GET SERVICE CATEGORY METHOD ---------------------------------------
    private function test_get_category() {
        // insert record
        $category = array(
            'name' => 'Test Category',
            'description' => 'Test Description ...'
        );
        
        $this->ci->db->insert('ea_service_categories', $category);
        $category['id'] = intval($this->ci->db->insert_id());
        
        // get record
        $db_record = $this->ci->services_model->get_category($category['id']);
        $this->ci->unit->run($db_record, $category, 'Test if get_category() has successfully '
                . 'returned the record.');
        
        $this->ci->db->delete('ea_service_categories', array('id' => $category['id']));
    }
    
    private function test_get_category_invalid_argument() {
        $invalid_arg = 'THIS IS INVALID';
        $has_thrown_exc = FALSE;
        try {
            $this->ci->services_model->get_category($invalid_arg);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if get_category() with invalid argument '
                . 'has thrown an exception.');
    }
    
    private function test_get_category_record_does_not_exist() {
        $random_id = 123412343;
        $has_thrown_exc = TRUE;
        try {
            $this->ci->services_model->get_category($random_id);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if delete_category() with random id '
                . 'has thrown an exception.');
    }
    
    // TEST GET ALL SERVICE CATEGORIES METHOD ----------------------------------
    private function test_get_all_categories() {
        $all_categories = $this->ci->services_model->get_all_categories();
        $db_categories = $this->ci->db->get('ea_service_categories')->result_array();
        $this->ci->unit->run($all_categories, $db_categories, 'Test if get_all_categories() method '
                . 'has successfully returned all the services categories.');
    } 
    
    // TEST VALIDATE SERVICE CATEGORY METHOD --------------------------------
    private function test_validate_category() {
        $category = array(
            'name' => 'Test Name',
            'description' => 'Test Description ...'
        );
        
        $is_valid = $this->ci->services_model->validate_category($category);
        $this->ci->unit->run($is_valid, TRUE, 'Test if validate_category() has returned true '
                . 'with valid data.');
    }
    
    private function test_validate_category_invalid_name() {
        $category = array(
            'name' => '', // invalid
            'description' => 'Test Description ...'
        );
        
        $is_valid = $this->ci->services_model->validate_category($category);
        $this->ci->unit->run($is_valid, FALSE, 'Test if validate_category() has returned false '
                . 'with invalid data.');
    }
}

/* End of file Unit_tests_services_model.php */
/* Location: ./application/libraries/Unit_tests/drivers/Unit_tests_services_model.php */
