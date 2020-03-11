<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_user_extra_working_plan extends CI_Migration {
    public function up()
    {
        if (!$this->db->field_exists('extra_working_plan', 'ea_user_settings')) {
            $fields = [
                'extra_working_plan' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                    'default' => '',
                    'after' => 'working_plan'
                ]
            ];

            $this->dbforge->add_column('ea_user_settings', $fields);
        }
    }

    public function down()
    {
        if (!$this->db->field_exists('extra_working_plan', 'ea_user_settings')) {
            $this->dbforge->drop_column('ea_user_settings', 'extra_working_plan');
        }
    }
}
