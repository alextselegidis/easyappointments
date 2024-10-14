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

/**
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Add_future_booking_limit_setting extends CI_Migration
{
    /**
     * Upgrade method.
     *
     * @throws Exception
     */
    public function up()
    {
        if (!$this->db->get_where('settings', ['name' => 'future_booking_limit'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'future_booking_limit',
                'value' => '90', // days
            ]);
        }
    }

    /**
     * Downgrade method.
     *
     * @throws Exception
     */
    public function down()
    {
        if ($this->db->get_where('settings', ['name' => 'future_booking_limit'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'future_booking_limit']);
        }
    }
}
