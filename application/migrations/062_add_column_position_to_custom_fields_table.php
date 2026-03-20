<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_column_position_to_custom_fields_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        if (!$this->db->field_exists('column_position', 'custom_fields')) {
            $fields = [
                'column_position' => [
                    'type' => 'VARCHAR',
                    'constraint' => '10',
                    'default' => 'left',
                    'after' => 'sort_order',
                ],
            ];

            $this->dbforge->add_column('custom_fields', $fields);
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        if ($this->db->field_exists('column_position', 'custom_fields')) {
            $this->dbforge->drop_column('custom_fields', 'column_position');
        }
    }
}
