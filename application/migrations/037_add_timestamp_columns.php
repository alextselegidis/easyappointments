<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_timestamp_columns extends EA_Migration
{
    /**
     * @var string[]
     */
    protected $tables = ['appointments', 'categories', 'consents', 'roles', 'services', 'settings', 'users'];

    /**
     * @var string[]
     */
    protected $columns = ['delete_datetime', 'update_datetime', 'create_datetime'];

    /**
     * Upgrade method.
     */
    public function up()
    {
        foreach ($this->tables as $table) {
            foreach ($this->columns as $column) {
                if (!$this->db->field_exists($column, $table)) {
                    $fields = [
                        $column => [
                            'type' => 'DATETIME',
                            'null' => true,
                            'after' => 'id',
                        ],
                    ];

                    $this->dbforge->add_column($table, $fields);
                }
            }
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        foreach ($this->tables as $table) {
            foreach ($this->columns as $column) {
                if ($this->db->field_exists($column, $table)) {
                    $this->dbforge->drop_column($table, $column);
                }
            }
        }
    }
}
