<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 * Add guests field settings
 * ---------------------------------------------------------------------------- */

class Migration_Add_guests_field_settings extends EA_Migration
{
	/**
	 * Upgrade method.
	 */
	public function up(): void
	{
		$settings = [
			'display_guests' => '1',
			'require_guests' => '0',
		];
		foreach ($settings as $name => $value) {
			if (!$this->db->get_where('settings', ['name' => $name])->num_rows()) {
				$this->db->insert('settings', [
					'name' => $name,
					'value' => $value,
				]);
			}
		}
	}

	/**
	 * Downgrade method.
	 */
	public function down(): void
	{
		$this->db->delete('settings', ['name' => 'display_guests']);
		$this->db->delete('settings', ['name' => 'require_guests']);
	}
}
