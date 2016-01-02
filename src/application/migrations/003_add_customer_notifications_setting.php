<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_customer_notifications_setting extends CI_Migration {
    public function up() {
        $this->load->model('settings_model');
        $this->settings_model->set_setting('customer_notifications', '1');
    }

    public function down() {
        $this->load->model('settings_model');
        $this->settings_model->remove_setting('customer_notifications');
    }
}
