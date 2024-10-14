<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_custom_fields_columns_to_users_table extends EA_Migration
{
    /**
     * @var int
     */
    private const FIELD_NUMBER = 5;

    /**
     * Upgrade method.
     */
    public function up()
    {
        for ($i = self::FIELD_NUMBER; $i > 0; $i--) {
            $field_name = 'custom_field_' . $i;

            if (!$this->db->field_exists($field_name, 'users')) {
                $fields = [
                    $field_name => [
                        'type' => 'TEXT',
                        'null' => true,
                        'after' => 'language',
                    ],
                ];

                $this->dbforge->add_column('users', $fields);
            }
        }
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        for ($i = self::FIELD_NUMBER; $i > 0; $i--) {
            $field_name = 'custom_fields_' . $i;

            if ($this->db->field_exists($field_name, 'users')) {
                $this->dbforge->drop_column('users', $field_name);
            }
        }
    }
}
