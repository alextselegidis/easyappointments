<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

/**
 * Class Migration_Add_date_format_setting
 *
 * @property CI_Loader load
 * @property CI_DB_query_builder db
 * @property CI_DB_forge dbforge
 * @property Settings_Model settings_model
 */
class Migration_Add_date_format_setting extends CI_Migration {
    /**
     * Migration_Add_date_format_setting constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->load->model('settings_model');
    }


    /**
     * Upgrade method.
     *
     * @throws Exception
     */
    public function up()
    {
        try
        {
            $this->settings_model->get_setting('date_format');
        }
        catch (Exception $exception)
        {
            $this->settings_model->set_setting('date_format', DATE_FORMAT_DMY);
        }
    }

    /**
     * Downgrade method.
     *
     * @throws Exception
     */
    public function down()
    {
        $this->settings_model->remove_setting('date_format');
    }
}
