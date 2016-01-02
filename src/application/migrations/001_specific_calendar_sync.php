<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

class Migration_Specific_calendar_sync extends CI_Migration {

	public function up() {
        if (!$this->db->field_exists('google_calendar', 'ea_user_settings')) {
            $fields = array(
                'google_calendar' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '128',
                    'null' => TRUE
                )
            );
            $this->dbforge->add_column('ea_user_settings', $fields);
        }
	}

	public function down() {
        if ($this->db->field_exists('google_calendar', 'ea_user_settings')) {
		  $this->dbforge->drop_column('ea_user_settings', 'google_calendar');
        }
	}
}
