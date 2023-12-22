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

class Migration_Drop_delete_datetime_column_from_all_tables extends EA_Migration
{
    /**
     * @var string[]
     */
    protected $tables = [
        'appointments',
        'categories',
        'consents',
        'roles',
        'services',
        'settings',
        'users',
        'webhooks',
    ];

    /**
     * Upgrade method.
     */
    public function up()
    {
        foreach ($this->tables as $table) {
            if ($this->db->field_exists('delete_datetime', $table)) {
                $this->dbforge->drop_column($table, 'delete_datetime');
            }
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        foreach ($this->tables as $table) {
            if (!$this->db->field_exists('delete_datetime', $table)) {
                $fields = [
                    'delete_datetime' => [
                        'type' => 'DATETIME',
                        'null' => true,
                        'after' => 'update_datetime',
                    ],
                ];

                $this->dbforge->add_column($table, $fields);
            }
        }
    }
}
