<?php 
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
        $this->CI->Appointments_Model
				->get_value('start_datetime', $random_record_id);
    } catch (InvalidArgumentException $db_exc) {
        $has_thrown_exception = TRUE;
    }
        
    $this->CI->unit->run($has_thrown_exception, TRUE, 
			'Test get_value() with record id that does not exist.');
}