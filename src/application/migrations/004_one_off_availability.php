<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_One_off_availability extends CI_Migration {

	public function up() {
		if ($this->db->field_exists('is_unavailable', 'ea_appointments'))
		{
			$fields = array(
				'is_unavailable' => array(
					'name' => 'type',
					'type' => 'tinyint(4)',
					'DEFAULT' => 0,
					'NULL' => true
					)
			);
			
			$this->dbforge->modify_column('ea_appointments', $fields);
		}
	}

	public function down() {
		if ($this->db->field_exists('type', 'ea_appointments'))
		{
			$fields = array(
				'type' => array(
					'name' => 'is_unavailable',
					'type' => 'tinyint(4)',
					'DEFAULT' => 0,
					'NULL' => true
					)
			);
			
			$this->dbforge->modify_column('ea_appointments', $fields);
		}
	}
}

?>
