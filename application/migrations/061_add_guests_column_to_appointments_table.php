<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 * Add guests field to appointments table
 * ---------------------------------------------------------------------------- */

class Migration_Add_guests_column_to_appointments_table extends EA_Migration
{
	/**
	 * Upgrade method.
	 */
	public function up(): void
	{
		$this->dbforge->add_column('appointments', [
			'guests' => [
				'type' => 'int',
				'constraint' => 11,
				'default' => 1,
				'null' => false,
				'after' => 'notes',
			],
		]);
	}

	/**
	 * Downgrade method.
	 */
	public function down(): void
	{
		$this->dbforge->drop_column('appointments', 'guests');
	}
}
