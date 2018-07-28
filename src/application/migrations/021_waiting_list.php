<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

class Migration_Waiting_list extends CI_Migration {
    public function up()
    {
        $this->db->insert('ea_settings', ['name' => 'show_waiting_list', 'value' => '0']);
        $this->db->insert('ea_settings', ['name' => 'waiting_list_content', 'value' => '<h3>Waiting List</h3><p>By registering here you will be sent daily email or email and text notices for 30 days regarding any availability I have over the next 60 days. You can renew your place on the waiting list as often as you like.  To be removed from the list click the link on your email/text message for removal.</p><h4>Regarding Text Notification:</h4><p>If you select to be notified by text message be aware that these messages can be lengthy  if the calendar has many days of availability.</p>']);
    }

    public function down()
    {
        $this->db->delete('ea_settings', ['name' => 'show_waiting_list']);
        $this->db->delete('ea_settings', ['name' => 'waiting_list_content']);
    }
}
