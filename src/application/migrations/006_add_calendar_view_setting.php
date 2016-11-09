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

class Migration_Add_calendar_view_setting extends CI_Migration {
    public function up() {
        $this->load->dbforge();

        $fields = [
            'calendar_view' => [ 
                'type' => 'VARCHAR',
                'constraint' => '32', 
                'default' => 'default'
            ]
        ]; 

        $this->dbforge->add_column('ea_user_settings', $fields); 

        $this->db->update('ea_user_settings', ['calendar_view' => 'default']);
    }

    public function down() {
        $this->load->dbforge();
        $this->dbforge->drop_column('ea_user_settings', 'calendar_view');
    }
}
