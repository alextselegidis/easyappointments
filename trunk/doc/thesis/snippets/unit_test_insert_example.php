<?php
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
    $appointment_data['id'] = $this->CI->Appointments_Model
            ->add($appointment_data);
    $this->CI->unit->run($appointment_data['id'], 'is_int', 
             'Test if add() appointment (insert operation) '
			 . 'returned the db row id.');
        
    // Check if the record is the one that was inserted.
    $db_data = $this->CI->db->get_where('ea_appointments', 
            array('id' => $appointment_data['id']))->row_array();
    $this->CI->unit->run($appointment_data, $db_data, 'Test if add() '
			. 'appointment (insert operation) has successfully '
			. 'inserted a record.');

    // Delete inserted record.
    $this->CI->db->delete('ea_appointments', 
            array('id' => $appointment_data['id']));
}