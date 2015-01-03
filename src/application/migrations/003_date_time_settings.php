<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Date_time_settings extends CI_Migration {

	public function up() {

		$settings = array(
			array(
			'name' => 'date_format',
			'value' => 'DMY'),
			array(
			'name' => 'time_format',
			'value' => '24'),
			array(
			'name' => 'first_day_of_week',
			'value' => '1'),
			array(
			'name' => 'day_start_time',
			'value' => '00:00'),
			array(
			'name' => 'day_end_time',
			'value' => '23:00'),
			array(
			'name' => 'time_slot_interval',
			'value' => '30'),
		);

		foreach ($settings as $setting)
		{
			$query = $this->db->select('name')
				->from("ea_settings")
				->where("name", $setting['name'])
				->get();
			if (0 == $query->num_rows())
			{
				$this->db->insert("ea_settings", $setting);
			}

		}


	}

	public function down() {
	}
}

?>
