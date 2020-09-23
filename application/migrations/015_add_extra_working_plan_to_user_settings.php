<?php defined('BASEPATH') or exit('No direct script access allowed');

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

/**
 * Class Migration_Add_custom_availability_periods_to_user_settings
 *
 * @property CI_DB_query_builder db
 * @property CI_DB_forge dbforge
 */
class Migration_Add_custom_availability_periods_to_user_settings extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ( ! $this->db->field_exists('custom_availability_periods', 'user_settings'))
        {
            $fields = [
                'custom_availability_periods' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                    'after' => 'working_plan'
                ]
            ];

            $this->dbforge->add_column('user_settings', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ( ! $this->db->field_exists('custom_availability_periods', 'user_settings'))
        {
            $this->dbforge->drop_column('user_settings', 'custom_availability_periods');
        }
    }
}
