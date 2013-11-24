<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Unit_tests_appointments_model extends CI_Driver {
    private $ci;
    
    private $provider_id;
    private $customer_id;
    private $service_id;
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->ci =& get_instance();
        
        $this->ci->load->library('Unit_test');
        $this->ci->load->model('appointments_model');
        
        // Add some sample data from the database (will be needed in the 
        // testing methods).
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
            'id_roles' => $this->ci->db->get_where('ea_roles', 
                                array('slug' => DB_SLUG_PROVIDER))->row()->id
        );
        $this->ci->db->insert('ea_users', $provider);
        $this->provider_id = $this->ci->db->insert_id();
        
        $service = array(
            'name' => 'Test Service',
            'duration' => 30,
            'price' => '20.00',
            'currency' => 'Euro',
            'description' => 'Some description here ...',
            'id_service_categories' => NULL
        );
        $this->ci->db->insert('ea_services', $service);
        $this->service_id = $this->ci->db->insert_id();
        
        $customer = array(
            'last_name' => 'Doe',
            'first_name' => 'John',
            'email' => 'alextselegidis@gmail.com',
            'phone_number' => '0123456789',
            'address' => 'Abbey Road 18',
            'city' => 'London',
            'zip_code' => '12345',
            'id_roles' => $this->ci->db->get_where('ea_roles', 
                                 array('slug' => DB_SLUG_CUSTOMER))->row()->id
        );
        $this->ci->db->insert('ea_users', $customer);
        $this->customer_id = $this->ci->db->insert_id();
    }
    
    /**
     * Run all the available tests
     */
    public function run_all() {
        // All the methods whose names start with "test" are going to be 
        // executed. If you want a method to not be executed remove the 
        // "test" keyword from the beginning.
        $class_methods = get_class_methods('Unit_tests_appointments_model');
        foreach ($class_methods as $method_name) {
            if (substr($method_name, 0, 5) === 'test_') {
                call_user_func(array($this, $method_name));
            }
        }
        
        $this->ci->db->delete('ea_users', array('id' => $this->customer_id));
        $this->ci->db->delete('ea_users', array('id' => $this->provider_id));
        $this->ci->db->delete('ea_services', array('id' => $this->service_id));
    }
    
    /////////////////////////////////////////////////////////////////////////
    // UNIT TESTS
    /////////////////////////////////////////////////////////////////////////    
    /**
     * Test the appointment add method - insert new record.
     */
    private function test_add_appointment_insert() {
        // Add - insert new appointment record to the database.
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        $appointment['id'] = $this->ci->appointments_model->add($appointment);
        $this->ci->unit->run($appointment['id'], 'is_int', 'Test if add() appointment (insert '
                . 'operation) returned the db row id.');
        
        // Check if the record is the one that was inserted.
        $db_data = $this->ci->db->get_where('ea_appointments', array('id' => $appointment['id']))
                ->row_array();
        
        // These should not be included because they are generated when the 
        // record is inserted.
        unset($db_data['hash']); 
        unset($db_data['book_datetime']);
        unset($db_data['id_google_calendar']);
        
        $this->ci->unit->run($appointment, $db_data, 'Test if add() appointment (insert '
                . 'operation) has successfully inserted a record.');
        
        // Delete inserted record.
        $this->ci->db->delete('ea_appointments', array('id' => $appointment['id']));
    }
    
    /**
     * Test the appointment add method - update existing record.
     */
    private function test_add_appointment_update() {
        // Insert new appointment (this row will be updated later).
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        $this->ci->db->insert('ea_appointments', $appointment);
        $appointment['id'] = $this->ci->db->insert_id();
        
        // Perform the update operation and check if the record is update.
        $changed_notes = 'Some CHANGED notes right here ...';
        $appointment['notes'] = $changed_notes;
        
        $update_result = $this->ci->appointments_model->add($appointment);
        $this->ci->unit->run($update_result, 'is_int', 'Test if add() appointment (update '
                . 'operation) has returned the row id.');
        
        $db_notes = $this->ci->db->get_where('ea_appointments', array('id' => $update_result))
                ->row()->notes;
        $this->ci->unit->run($changed_notes, $db_notes, 'Test add() appointment (update '
                . 'operation) has successfully updated record.');
        
        // Delete inserted record.
        $this->ci->db->delete('ea_appointments', array('id' => $appointment['id']));
    }
    
    /**
     * Test appointment data insertion with wrong date 
     * format for the corresponding datetime db fields.
     */
    private function test_add_appointment_wrong_date_format() {
        // Insert new appointment with no foreign keys.
        $appointment = array(
            'start_datetime' => '2013-0WRONG5-01 12:30WRONG:00',
            'end_datetime' => '2013-0WRONG5-01WRONG 13:00WRONG:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
         );
        
        $has_thrown_exception = FALSE; // This method must throw a validation exception.
        try {
            $this->ci->appointments_model->add($appointment);
        } catch(Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test add() appointment with wrong '
                . 'date format.', 'A validation exception must be thrown.');
    }
    
    /**
     * Test the exists() method. 
     * 
     * Insert a new appointment and test if it exists.
     */
    private function test_exists() {
        // Insert new appointment (this row will be checked later).
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        $this->ci->db->insert('ea_appointments', $appointment);
        $appointment['id'] = $this->ci->db->insert_id();
        
        // Test the exists() method
        $this->ci->unit->run($this->ci->appointments_model->exists($appointment), TRUE, 
                'Test exists() method with an inserted record.');
        
        // Delete inserted record.
        $this->ci->db->delete('ea_appointments', array('id' => $appointment['id']));
    }
    
    private function test_exists_record_does_not_exist() {
        // Create random appointment data that doesn't exist in the database.
        $appointment = array(
            'start_datetime' => '2013-05-01 08:33:45',
            'end_datetime' => '2013-05-02 13:13:13',
            'notes' => 'This is totally random!',
            'is_unavailable' => FALSE,
            'id_users_provider' => '198678',
            'id_users_customer' => '194702',
            'id_services' => '8766293'
        );
        
        $this->ci->unit->run($this->ci->appointments_model->exists($appointment), FALSE, 
                'Test exists() method with an appointment that does not exist');
    }
    
    private function test_exists_with_wrong_data() {
        // Create random appointment data that doesn't exist in the database.
        $appointment = array(
            'start_datetime' => '2WRONG013-05-01 0WRONG8:33:45',
            'end_datetime' => '2013-0WRONG5-02 13:13:WRONG13',
            'notes' => 'This is totally random!',
            'is_unavailable' => FALSE,
            'id_users_provider' => '1986WRONG78',
            'id_users_customer' => '1WRONG94702',
            'id_services' => '876WRONG6293'
        );
        
        $this->ci->unit->run($this->ci->appointments_model->exists($appointment), FALSE, 
                'Test exists() method with wrong appointment data.');
    }
    
    /**
     * Test the find record id method with a record that already 
     * exists in the database.
     */
    private function test_find_record_id() {
        // Create a new appointment record.
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        $this->ci->db->insert('ea_appointments', $appointment);
        $appointment['id'] = $this->ci->db->insert_id();
        
        // Find record id of the new appointment record.
        $method_result_id = $this->ci->appointments_model->find_record_id($appointment);
        
        $this->ci->unit->run($method_result_id, $appointment['id'], 'Test find_record_id() '
                . 'successfully returned the correct record id.');
        
        // Delete appointment record.
        $this->ci->db->delete('ea_appointments', array('id' => $method_result_id));
    }
    
    /**
     * Try to find the record id of an appointment that doesn't
     * exist in the database.
     * 
     * A database exception is expected to be raised.
     */
    private function test_find_record_id_appointment_does_not_exist() {
        // Define appointment data. These data shouldn't exist in the database.
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        
        // Load the appointments model and execute the find record id method.
        $has_thrown_exception = FALSE;
        
        try {
            $this->ci->appointments_model->find_record_id($appointment);
        } catch(Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test find_record_id() with appointment ' 
                . 'data that does not exist in the database.', 'A database exception is expected '
                . 'to be raised.');
    }
    
    /**
     * Test the find_record_id() method by providing wrong
     * appointment data.
     * 
     * A database exception is expected to be raised.
     */
    private function test_find_record_id_wrong_data() {
        // Define appointment data array with wrong values.
        $appointment = array(
            'start_datetime' => '2013WRONG-05-0WRONG1 12:WRONG30:00',
            'end_datetime' => '2013-05-01 13:00:00WRONG',
            'notes' => 'Some notes righWRONGt here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => 'WRONG',
            'id_users_customer' => 'WRONG',
            'id_services' => 'WRONG'
        );
        
        // Try to find the appointment's record id. A database 
        // exception should be raised.
        $has_thrown_exception = FALSE;
        
        try {
            $this->ci->appointments_model->find_record_id($appointment);
        } catch(Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test find_record_id() with appointment ' 
                . 'data array with wrong values.', 'A database exception is expected to be raised.');
    } 
    
    /**
     * Test the normal flow of deleting an appointment record.
     */
    private function test_delete() {
        // Create a new appointment record.
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        $this->ci->db->insert('ea_appointments', $appointment);
        $appointment['id'] = $this->ci->db->insert_id();
        
        // Delete new record
        $delete_result = $this->ci->appointments_model->delete($appointment['id']);
        $this->ci->unit->run($delete_result, TRUE, 'Test delete() method result (should be TRUE).');
        
        // Check if the record has been successfully deleted.
        $num_rows = $this->ci->db->get_where('ea_appointments', array('id' => $appointment['id']))
                ->num_rows();
        $this->ci->unit->run($num_rows, 0, 'Test if the record was successfully deleted.');
    }
    
    /**
     * Test the delete method with a record that doesn't exist
     * in the database.
     */
    private function test_delete_record_does_not_exist() {
        $random_record_id = 1233265;
        $delete_result = $this->ci->appointments_model->delete($random_record_id);
        $this->ci->unit->run($delete_result, FALSE, 'Test delete() method with a record id' 
                . ' that does not exist');
    }
    
    /**
     * Test the delete method by providing a wrong argument 
     * (string and not integer).
     */
    private function test_delete_record_wrong_parameter_given() {
        $wrong_record_id = 'not_numeric';
        
        $has_thrown_exception = FALSE;
        
        try {
            $this->ci->appointments_model->delete($wrong_record_id);
        } catch(Exception $exc) {
            $has_thrown_exception = TRUE;
        }

        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test delete() method with argument '
                . 'that is not an numeric.');
    }
    
    /**
     * Test the get_batch() method of the appointments model.
     */
    private function test_get_batch() {
        // Get all the appointment records (without using the model).
        $db_data = $this->ci->db->get('ea_appointments')->result_array();
        
        // Get all the appointment records (by using the model).
        $model_data = $this->ci->appointments_model->get_batch();
        
        // Check that the two arrays are the same.
        $this->ci->unit->run($model_data, $db_data, 'Test get_batch() method.');
    }
    
    /**
     * Test the get_batch() method of the appointments model
     * with a where clause.
     */
    private function test_get_batch_where_clause() {
        // Insert new appointment.
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',  
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        $this->ci->db->insert('ea_appointments', $appointment);
        $appointment['id'] = $this->ci->db->insert_id();
        
        // Get filtered appointment records without using the model.
        $db_data = $this->ci->db->get_where('ea_appointments', array('id' => $appointment['id']))
                ->result_array();
        
        // Get filtered appointment records by using the model.
        $model_data = $this->ci->appointments_model->get_batch(array('id' => $appointment['id']));
        
        // Check that the two arrays are the same.
        $this->ci->unit->run($model_data, $db_data, 'Test get_batch() method.');
        
        // Delete appointment record.
        $this->ci->db->delete('ea_appointments', array('id' => $appointment['id']));
    }
    
    /**
     * Test the get_batch() method of the appointments model
     * with a wrong where clause. 
     * 
     * A database exception is expected to be raised.
     * 
     * <strong>IMPORTANT!</strong> This test is unabled because code
     * igniter handles itself wrong queries.
     */
    private function unabled_test_get_batch_wrong_where_clause() {
        $has_thrown_exception = FALSE;
        
        try {
            $this->ci->appointments_model->get_batch('WRONG QUERY HERE');
        } catch(Exception $db_exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test get_batch() with wrong where clause.',
                'A database exception is expected to be thrown.');
    }
    
    /**
     * Test get_row() method.
     */
    private function test_get_row() {
        // Insert new appointment record.
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'hash' => '91de2d31f5cbb6d26a5b1b3e710d38d1',
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        $this->ci->db->insert('ea_appointments', $appointment);
        $appointment['id'] = $this->ci->db->insert_id();
        
        // Get the appointment row from the database.
        $db_data = $this->ci->appointments_model->get_row($appointment['id']);
        unset($db_data['book_datetime']);
        unset($db_data['id_google_calendar']);
        
        // Check if this is the record we seek.
        $this->ci->unit->run($db_data, $appointment, 'Test get_row() method.');
        
        // Delete appointment record.
        $this->ci->db->delete('ea_appointments', array('id' => $appointment['id']));
    }
    
    /**
     * Test get_row() with a record that doesn't exist in the db.
     */
    private function test_get_row_that_does_not_exist() {
        $random_record_id = 789453486;
        
        $row_data = $this->ci->appointments_model->get_row($random_record_id);
        
        $this->ci->unit->run($row_data, NULL, 'Test get_row() with record id that does ' 
                . 'not exist in the database.');
    }
    
    /**
     * Test get_row() method with wrong argument.
     * 
     * A database exception is expected.
     */
    private function test_get_row_with_invalid_argument() {
        $invalid_id = 'THIS IS NOT NUMERIC';
        
        $has_thrown_exception = FALSE;
        try {
            $this->ci->appointments_model->get_row($invalid_id);        
        } catch(Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test get_row() with wrong argument.');
    }
    
    /**
     * Test the get field value method.
     */
    private function test_get_value() {
        // Insert new appointment record.
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        $this->ci->db->insert('ea_appointments', $appointment);
        $appointment['id'] = $this->ci->db->insert_id();
        
        // Get a specific value from the database.
        $db_value = $this->ci->appointments_model->get_value('start_datetime', $appointment['id']);
        
        // Check if the value was correctly fetched from the database.
        $this->ci->unit->run($db_value, $appointment['start_datetime'], 'Test get_value() method.');
        
        // Delete inserted appointment record.
        $this->ci->db->delete('ea_appointments', array('id' => $appointment['id']));
    }
    
    /**
     * Test the get field value method with a record id that
     * doesn't exist in the db.
     * 
     * A database exception is expected.
     */
    private function test_get_value_record_does_not_exist() {
        $random_record_id = 843521368768;
        
        $has_thrown_exception = FALSE;
        
        try {
            $this->ci->appointments_model->get_value('start_datetime', $random_record_id);
        } catch(Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test get_value() with record id that '
                . 'does not exist.');
    }
    
    /**
     * Test the get_value() method with a field that does 
     * not exist in the db.
     * 
     * A database exception is expected.
     */
    private function test_get_value_field_does_not_exist() {
        // Insert new appointment record.
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        $this->ci->db->insert('ea_appointments', $appointment);
        $appointment['id'] = $this->ci->db->insert_id();
        
        // Try to get record value with wrong field name.
        $wrong_field_name = 'THIS IS WRONG';
        $has_thrown_exception = FALSE;
        
        try {
            $this->ci->appointments_model->get_value($wrong_field_name, $appointment['id']);
        } catch(Exception $exc) {
            $has_thrown_exception = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exception, TRUE, 'Test get_value() with record id that '
                . 'does not exist.');
        
        // Delete inserted record.
        $this->ci->db->delete('ea_appointments', array('id' => $appointment['id']));
    }
    
    private function test_validate_data() {
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        $validation_result = $this->ci->appointments_model->validate($appointment);
        $this->ci->unit->run($validation_result, TRUE, 'Test validate() method.');
    }
    
    private function test_validate_data_wrong_date_format() {
        $appointment = array(
            'start_datetime' => '20WRONG13-05-01 12WRONG:30:00',
            'end_datetime' => '2013-05WRONG-01 13:00WRONG:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->appointments_model->validate($appointment);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() method with '
                . 'wrong date formats has thrown exception.');
    }
    
    private function test_validate_data_invalid_provider_id() {
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => 'THIS IS WRONG',
            'id_users_customer' => $this->customer_id,
            'id_services' => $this->service_id
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->appointments_model->validate($appointment);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() method with '
                . 'invalid provider id has thrown exception.');
    }
    
    private function test_validate_data_invalid_customer_id() {
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => 'THIS IS WRONG',
            'id_services' => $this->service_id
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->appointments_model->validate($appointment);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() method with '
                . 'invalid customer id has thrown exception.');
    }
    
    private function test_validate_data_invalid_service_id() {
        $appointment = array(
            'start_datetime' => '2013-05-01 12:30:00',
            'end_datetime' => '2013-05-01 13:00:00',
            'notes' => 'Some notes right here...',
            'is_unavailable' => FALSE,
            'id_users_provider' => $this->provider_id,
            'id_users_customer' => $this->customer_id,
            'id_services' => 'THIS IS WRONG'
        );
        
        $has_thrown_exc = FALSE;
        try {
            $this->ci->appointments_model->validate($appointment);
        } catch(Exception $exc) {
            $has_thrown_exc = TRUE;
        }
        
        $this->ci->unit->run($has_thrown_exc, TRUE, 'Test if validate() method with '
                . 'invalid service id has thrown exception.');
    }
}

/* End of file Unit_tests_appointments_model.php */
/* Location: ./application/libraries/Unit_tests/drivers/Unit_tests_appointments_model.php */