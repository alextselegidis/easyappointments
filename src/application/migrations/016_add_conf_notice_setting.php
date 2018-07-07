<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_conf_notice_setting extends CI_Migration {
    public function up()
    {
        $this->load->model('settings_model');
        try
        {
            $this->settings_model->get_setting('conf_notice');
        }
        catch (Exception $exception)
        {
            $this->settings_model->set_setting('conf_notice', '1');
        }
		
		if (!$this->db->field_exists('notifications', 'ea_users')) {			
			$this->load->dbforge();

			$fields = [
				'notifications' => [ 
					'type' => 'tinyint',
					'constraint' => '4',
					'default' => 1,
					'after' => 'id_cellcarrier'
				]
			]; 

			$this->dbforge->add_column('ea_users', $fields); 
			$this->db->update('ea_users', ['notifications' => 1]);
		}	
    }
	

    public function down()
    {
        $this->load->model('settings_model');
		$this->settings_model->remove_setting('conf_notice');
		
		if ($this->db->field_exists('notifications', 'ea_users')) {			
			$this->load->dbforge();
			$this->dbforge->drop_column('ea_users', 'notifications');
		}		
    }
}