<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Specific_calendar_sync extends CI_Migration {

	public function up() {
		$fields = array(
			'google_calendar' => array(
				'type' => 'VARCHAR',
				'constraint' => '128',
				'default' => 'null')
		);
		
		$this->dbforge->add_column('ea_user_settings', $fields);
	}

	public function down() {
		$this->dbforge->drop_column('ea_user_settings', 'google_calendar');
	}
}

/*
 * This migration class adds the "google_calendar" field to the database 
 * "user_settings" table in order to be able to select a specific calendar 
 * for google synchronization. 
 */