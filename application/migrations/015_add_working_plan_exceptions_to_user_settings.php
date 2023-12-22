<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_working_plan_exceptions_to_user_settings extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up()
    {
        if (!$this->db->field_exists('working_plan_exceptions', 'user_settings')) {
            $fields = [
                'working_plan_exceptions' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'working_plan',
                ],
            ];

            $this->dbforge->add_column('user_settings', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->field_exists('working_plan_exceptions', 'user_settings')) {
            $this->dbforge->drop_column('user_settings', 'working_plan_exceptions');
        }
    }
}
