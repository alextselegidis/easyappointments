<?php
class AppointmentsModelTest extends PHPUnit_Framework_TestCase {
    private $CI;
    private $id_users_provider;
    private $id_users_customer;
    private $id_services;

    public function setUp() {
        $this->CI =& get_instance();
        $this->CI->load->model('Appointments_Model'); // This model will be used in all test methods.
        
        $this->CI->load->model('Providers_Model');
        $providers = $this->Providers_Model->get_row(2);
//        if (count($providers) > 0) {
//            $this->id_users_provider = $providers[0]['id'];
//        } else {
//            throw new Exception('There are no provider records ' 
//                    . 'in the database. Add at least one provider '
//                    . 'record before proceeding with the test.');
//        }
//        
//        $this->CI->load->model('Customers_Model');
//        $customers = $this->Customers_Model->get_batch();
//        if (count($customers) > 0) {
//            $this->id_users_customer = $customers[0]['id'];
//        } else {
//            throw new Exception('There are no customer records ' 
//                    . 'in the database. Add at least one customer '
//                    . 'record before proceeding with the test.');
//        }
//        
//        $this->CI->load->model('Services_Model');
//        $services = $this->Services_Model->get_batch();
//        if (count($services) > 0) {
//            $this->id_services = $services[0]['id'];
//        } else {
//            throw new Exception('There are no customer records ' 
//                    . 'in the database. Add at least one customer '
//                    . 'record before proceeding with the test.');
//        }
    }
    
    public function test_sample() {
        //$this->assertEquals(1,1);
    }
    
    /////////////////////////////////////////////////////////////////////////////////
    // ADD RECORD METHOD TESTS 
    /////////////////////////////////////////////////////////////////////////////////
//    public function test_add_appointment_insert() {
//        // To trigger the insert method no record id needs to be 
//        // provided.
//        $appointment_data = array(
//                                'start_datetime' => '2013-05-01 12:30:00',
//                                'end_datetime' => '2013-05-01 13:00:00',
//                                'notes' => 'Some notes right here...',
//                                'id_users_provider' => $this->id_users_provider,
//                                'id_users_customer' => $this->id_users_customer,
//                                'id_services' => $this->id_services
//                            );
//        
//        $appointment_id = $this->Appointments_Model->add($appointment_data);
//        
//        // Check if the record is the one that was inserted.
//        $db_data = $this->Appointments_Model->get_row($appointment_id);
//        
//        $this->assertEquals($appointment_data['start_datetime'], $db_data['start_datetime']);
//        $this->assertEquals($appointment_data['end_datetime'], $db_data['end_datetime']);
//        $this->assertEquals($appointment_data['notes'], $db_data['notes']);
//        $this->assertEquals($appointment_data['id_users_provider'], $db_data['id_users_provider']);
//        $this->assertEquals($appointment_data['id_users_customer'], $db_data['id_users_customer']);
//        $this->assertEquals($appointment_data['id_services'], $db_data['id_services']);
//        
//        // Delete inserted record.
//        $this->Appointments_Model->delete($appointment_id);
//    }
//    
//    public function test_add_appointment_update() {
//        // Insert new record.
//        $appointment_data = array(
//                                'start_datetime' => '2013-05-01 12:30:00',
//                                'end_datetime' => '2013-05-01 13:00:00',
//                                'notes' => 'Some notes right here...',
//                                'id_users_provider' => $this->id_users_provider,
//                                'id_users_customer' => $this->id_users_customer,
//                                'id_services' => $this->id_services
//                            );
//        
//        $appointment_id = $this->Appointments_Model->add($appointment_data);
//        
//        // Update new record.
//        $new_notes_content = 'Some OTHER notes here ...';
//        $appointment_data['notes'] = $new_notes_content;
//        $this->Appointments_Model->add($appointment_data);
//        
//        // Check if the record was successfully updated.
//        $db_data = $this->Appointments_Model->get_row($appointment_id);
//        
//        $this->assertEquals($appointment_data['start_datetime'], $db_data['start_datetime']);
//        $this->assertEquals($appointment_data['end_datetime'], $db_data['end_datetime']);
//        $this->assertEquals($appointment_data['notes'], $db_data['notes']);
//        $this->assertEquals($appointment_data['id_users_provider'], $db_data['id_users_provider']);
//        $this->assertEquals($appointment_data['id_users_customer'], $db_data['id_users_customer']);
//        $this->assertEquals($appointment_data['id_services'], $db_data['id_services']);
//        
//        // Delete inserted record.
//        $this->Appointments_Model->delete($appointment_id);
//    }
//    
//    public function test_add_appointment_no_foreign_keys() {
//    
//        
//    }
//    
//    /////////////////////////////////////////////////////////////////////////////////
//    // RECORD EXISTS METHOD TESTS
//    /////////////////////////////////////////////////////////////////////////////////
//    public function test_exists_true() {
//        
//    }
//    
//    public function test_exists_false() {
//        
//    }
//    
//    public function test_exists_wrong_data() {
//        
//    }
}
    
/* End of file appointments_modelTest.php */
/* Location: ../test/appointments_modelTest.php */