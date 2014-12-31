<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_appointment_notified extends CI_Migration {

	public function up() {
		if (!$this->db->field_exists('notified', 'ea_appointments'))
		{
			$fields = array(
				'notified' => array(
					'type' => 'BOOLEAN',
					'null' => FALSE,
					'DEFAULT' => 0,
					'AFTER' => 'end_datetime')
			);
			
			$this->dbforge->add_column('ea_appointments', $fields);
		}
	}

	public function down() {
		if ($this->db->field_exists('notified', 'ea_appointments'))
		{
			$this->dbforge->drop_column('ea_appointments', 'notified');
		}
	}
}

?>
