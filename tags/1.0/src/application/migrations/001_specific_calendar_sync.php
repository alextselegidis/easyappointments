<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Specific_calendar_sync extends CI_Migration {

	public function up() {
		$fields = array(
			'google_calendar' => array(
				'type' => 'VARCHAR',
				'constraint' => '128',
				'null' => TRUE)
		);
		
		$this->dbforge->add_column('ea_user_settings', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('ea_user_settings', 'google_calendar');
	}
}