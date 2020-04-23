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
 * Class Migration_Add_attendants_number_to_services_table
 *
 * @property CI_DB_query_builder db
 * @property CI_DB_forge dbforge
 */
class Migration_Add_attendants_number_to_services_table extends CI_Migration {
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
