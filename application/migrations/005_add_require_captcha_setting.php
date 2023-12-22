<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

class Migration_Add_require_captcha_setting extends EA_Migration
{
    /**
     * Upgrade method.
     *
     * @throws Exception
     */
    public function up()
    {
        if (!$this->db->get_where('settings', ['name' => 'require_captcha'])->num_rows()) {
            $this->db->insert('settings', [
                'name' => 'require_captcha',
                'value' => '0',
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
        if ($this->db->get_where('settings', ['name' => 'require_captcha'])->num_rows()) {
            $this->db->delete('settings', ['name' => 'require_captcha']);
        }
    }
}
