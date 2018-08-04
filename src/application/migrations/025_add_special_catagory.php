<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_special_catagory extends CI_Migration {
	
	
    public function up() {
		if (!$this->db->field_exists('specialcat', 'ea_service_categories')) {			
			$this->load->dbforge();

			$fields = [
				'specialcat' => [ 
                    'type' => 'VARCHAR',
                    'constraint' => '32',
                    'default' => '',
                    'after' => 'description'
				]
			]; 

			$this->dbforge->add_column('ea_service_categories', $fields); 
			$this->db->update('ea_service_categories', ['specialcat' => '']);
		}

		if (!$this->db->field_exists('specialcat', 'ea_users')) {			
			$this->load->dbforge();

			$fields = [
				'specialcat' => [ 
                    'type' => 'VARCHAR',
                    'constraint' => '32',
                    'default' => '',
				]
			]; 

			$this->dbforge->add_column('ea_users', $fields); 
			$this->db->update('ea_users', ['specialcat' => '']);
		}
		
		
	}
	
	

    public function down() {
		if ($this->db->field_exists('specialcat', 'ea_service_categories')) {			
			$this->load->dbforge();
			$this->dbforge->drop_column('ea_service_categories', 'specialcat');
		}
		
		if ($this->db->field_exists('specialcat', 'ea_users')) {			
			$this->load->dbforge();
			$this->dbforge->drop_column('ea_users', 'specialcat');
		}		
    }
}
