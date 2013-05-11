<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Unit_tests_appointments_model extends CI_Driver {
    private $CI;
    
    private $provider_id;
    private $customer_id;
    private $service_id;
    
    /**
     * Class Constructor
     */
    public function __construct() {
        $this->CI =& get_instance();
        
        $this->CI->load->library('Unit_test');
        $this->CI->load->model('Appointments_Model');
        
        // Get some sample data from the database (will be needed in the 
        // testing methods).
        $this->provider_id = $this->CI->db
                                        ->select('ea_users.id')
                                        ->from('ea_users')
                                        ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                                        ->where('ea_roles.slug', DB_SLUG_PROVIDER)
                                        ->get()->row()->id;   
        $this->service_id = $this->CI->db
                                        ->select('ea_services.id')
                                        ->from('ea_services')
                                        ->join('ea_services_providers', 'ea_services_providers.id_services = ea_services.id', 'inner')
                                        ->where('ea_services_providers.id_users', $this->provider_id)
                                        ->get()->row()->id;
        $this->customer_id = $this->CI->db
                                        ->select('ea_users.id')
                                        ->from('ea_users')
                                        ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                                        ->where('ea_roles.slug', DB_SLUG_CUSTOMER)
                                        ->get()->row()->id;  
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
            if (strpos($method_name, 'test_') !== FALSE) {
                call_user_func(array($this, $method_name));
            }
        }
        
        // Create a report on the browser.
        $this->CI->output->append_output($this->CI->unit->report());
    }
    
    /////////////////////////////////////////////////////////////////////////
    // UNIT TESTS
    /////////////////////////////////////////////////////////////////////////    
    /**
     * Test the appointment add method - insert new record.
     */
    private function test_add_appointment_insert() {
        // Add - insert new appointment record to the database.
        $appointment_data = array(
                               'start_datetime' => '2013-05-01 12:30:00',
                               'end_datetime' => '2013-05-01 13:00:00',
                               'notes' => 'Some notes right here...',
                               'id_users_provider' => $this->provider_id,
                               'id_users_customer' => $this->customer_id,
                               'id_services' => $this->service_id
                           );
        $appointment_data['id'] = $this->CI->Appointments_Model->add($appointment_data);
        $this->CI->unit->run($appointment_data['id'], 'is_int', 'Test if add() appointment (insert operation) returned the db row id.');
        
        // Check if the record is the one that was inserted.
        $db_data = $this->CI->db->get_where('ea_appointments', array('id' => $appointment_data['id']))->row_array();
        $this->CI->unit->run($appointment_data, $db_data, 'Test if add() appointment (insert operation) has successfully inserted a record.');

        // Delete inserted record.
        $this->CI->db->delete('ea_appointments', array('id' => $appointment_data['id']));
    }
    
    /**
     * Test the appointment add method - update existing record.
     */
    private function test_add_appointment_update() {
        // Insert new appointment (this row will be updated later).
        $appointment_data = array(
                               'start_datetime' => '2013-05-01 12:30:00',
                               'end_datetime' => '2013-05-01 13:00:00',
                               'notes' => 'Some notes right here...',
                               'id_users_provider' => $this->provider_id,
                               'id_users_customer' => $this->customer_id,
                               'id_services' => $this->service_id
                           );
        $this->CI->db->insert('ea_appointments', $appointment_data);
        $appointment_data['id'] = $this->CI->db->insert_id();
        
        // Perform the update operation and check if the record is update.
        $changed_notes = 'Some CHANGED notes right here ...';
        $appointment_data['notes'] = $changed_notes;
        
        $update_result = $this->CI->Appointments_Model->add($appointment_data);
        $this->CI->unit->run($update_result, 'is_int', 'Test if add() appointment (update operation) has returned the row id.');
        
        $db_notes = $this->CI->db->get_where('ea_appointments', array('id' => $update_result))->row()->notes;
        $this->CI->unit->run($changed_notes, $db_notes, 'Test add() appointment (update operation) has successfully updated record.');
        
        // Delete inserted record.
        $this->CI->db->delete('ea_appointments', array('id' => $appointment_data['id']));
    }
    
    /**
     * Test appointment data insertion with wrong date 
     * format for the corresponding datetime db fields.
     */
    private function test_add_appointment_wrong_date_format() {
        // Insert new appointment with no foreign keys.
        $appointment_data = array(
                                'start_datetime' => '2013-0WRONG5-01 12:30WRONG:00',
                                'end_datetime' => '2013-0WRONG5-01WRONG 13:00WRONG:00',
                                'notes' => 'Some notes right here...',
                                'id_users_provider' => $this->provider_id,
                                'id_users_customer' => $this->customer_id,
                                'id_services' => $this->service_id
                             );
        
        $hasThrownException = FALSE; // This method must throw a validation exception.
        try {
            $this->CI->Appointments_Model->add($appointment_data);
        } catch(ValidationException $valExc) {
            $hasThrownException = TRUE;
        }
        
        $this->CI->unit->run($hasThrownException, TRUE, 'Test add() appointment with wrong date format.', 'A validation exception must be thrown.');
    }
    
    /**
     * Test the exists() method. 
     * 
     * Insert a new appointment and test if it exists.
     */
    private function test_appointment_exists() {
        // Insert new appointment (this row will be checked later).
        $appointment_data = array(
                               'start_datetime' => '2013-05-01 12:30:00',
                               'end_datetime' => '2013-05-01 13:00:00',
                               'notes' => 'Some notes right here...',
                               'id_users_provider' => $this->provider_id,
                               'id_users_customer' => $this->customer_id,
                               'id_services' => $this->service_id
                           );
        $this->CI->db->insert('ea_appointments', $appointment_data);
        $appointment_data['id'] = $this->CI->db->insert_id();
        
        // Test the exists() method
        $this->CI->unit->run($this->CI->Appointments_Model->exists($appointment_data), TRUE, 'Test exists() method with an inserted record.');
        
        // Delete inserted record.
        $this->CI->db->delete('ea_appointments', array('id' => $appointment_data['id']));
    }
    
    private function test_appointment_does_not_exist() {
        // Create random appointmnet data that doesn't exist in the database.
        $appointment_data = array(
                               'start_datetime' => '2013-05-01 08:33:45',
                               'end_datetime' => '2013-05-02 13:13:13',
                               'notes' => 'This is totally random!',
                               'id_users_provider' => '198678',
                               'id_users_customer' => '194702',
                               'id_services' => '8766293'
                           );
        
        $this->CI->unit->run($this->CI->Appointments_Model->exists($appointment_data), FALSE, 'Test exists() method with an appointment that does not exist');
    }
    
    private function test_appointment_exists_wrong_data() {
        // Create random appointmnet data that doesn't exist in the database.
        $appointment_data = array(
                               'start_datetime' => '2WRONG013-05-01 0WRONG8:33:45',
                               'end_datetime' => '2013-0WRONG5-02 13:13:WRONG13',
                               'notes' => 'This is totally random!',
                               'id_users_provider' => '1986WRONG78',
                               'id_users_customer' => '1WRONG94702',
                               'id_services' => '876WRONG6293'
                           );
        
        $this->CI->unit->run($this->CI->Appointments_Model->exists($appointment_data), FALSE, 'Test exists() method with wrong appointment data.');
    }
    
    // @task Test find_record_id
    // @task Test delete 
    // @task Test get_batch
    // @task Test get_row
    // @task Test get_value
    // @task Test validate_data
}