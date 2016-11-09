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

class Migration_Add_service_attendants_number extends CI_Migration {
    public function up() {
        $this->load->dbforge();

        $fields = [
            'attendants_number' => [ 
                'type' => 'INT',
                'constraint' => '11', 
                'default' => '1', 
                'after' => 'availabilities_type'
            ]
        ]; 

        $this->dbforge->add_column('ea_services', $fields); 

        $this->db->update('ea_services', ['attendants_number' => '1']);
    }

    public function down() {
        $this->load->dbforge();
        $this->dbforge->drop_column('ea_services', 'attendants_number');
    }
}
