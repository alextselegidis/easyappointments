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
 * Class Migration_Add_service_attendants_number
 *
 * @property CI_Loader load
 * @property CI_DB_query_builder db
 * @property CI_DB_forge dbforge
 * @property Settings_Model settings_model
 */
class Migration_Add_service_attendants_number extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ( ! $this->db->field_exists('attendants_number', 'ea_services'))
        {
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
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->field_exists('attendants_number', 'ea_services'))
        {
            $this->dbforge->drop_column('ea_services', 'attendants_number');
        }
    }
}
