<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Class Migration_Specific_calendar_sync
 *
 * @property CI_DB_query_builder db
 * @property CI_DB_forge dbforge
 */
class Migration_Specific_calendar_sync extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ( ! $this->db->field_exists('google_calendar', 'ea_user_settings'))
        {
            $fields = [
                'google_calendar' => [
                    'type' => 'VARCHAR',
                    'constraint' => '128',
                    'null' => TRUE
                ]
            ];
            $this->dbforge->add_column('ea_user_settings', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->field_exists('google_calendar', 'ea_user_settings'))
        {
            $this->dbforge->drop_column('ea_user_settings', 'google_calendar');
        }
    }
}
