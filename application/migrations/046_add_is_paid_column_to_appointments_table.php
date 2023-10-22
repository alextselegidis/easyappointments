<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      F.González <frnd@tuta.io>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Add_is_paid_column_to_appointments_table extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ( ! $this->db->field_exists('is_paid', 'appointments'))
        {
            $fields = [
                'is_paid' => [
                    'type' => 'TINYINT',
                    'constraint' => '4',
                    'default' => '0'
                ]
            ];

            $this->dbforge->add_column('appointments', $fields);

        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ($this->db->field_exists('is_paid', 'appointments'))
        {
            $this->dbforge->drop_column('appointments', 'is_paid');
        }
    }
}
