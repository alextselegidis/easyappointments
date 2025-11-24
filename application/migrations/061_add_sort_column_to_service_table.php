<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      Eero Jääskeläinen <eero.jaaskelainen@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_sort_column_to_service_table extends EA_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        if ( ! $this->db->field_exists('row_order', 'services'))
        {
            $fields = [
                'row_order' => [
                    'type' => 'INT',
                    'constraint' => '11',
                    'default' => '0',
                    'after' => 'id'
                ]
            ];

            $this->dbforge->add_column('services', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        if ( ! $this->db->field_exists('row_order', 'services'))
        {
            $this->dbforge->drop_column('services', 'row_order');
        }
    }
}
