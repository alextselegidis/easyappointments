<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Captcha Controller
 *
 * @property CI_Session $session
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Config $config
 * @property CI_Lang $lang
 * @property CI_Cache $cache
 * @property CI_DB_query_builder $db
 * @property CI_Security $security
 * @property Google_Sync $google_sync
 * @property Ics_file $ics_file
 * @property Appointments_model $appointments_model
 * @property Providers_model $providers_model
 * @property Services_model $services_model
 * @property Customers_model $customers_model
 * @property Settings_model $settings_model
 * @property Timezones $timezones
 * @property Roles_model $roles_model
 * @property Secretaries_model $secretaries_model
 * @property Admins_model $admins_model
 * @property User_model $user_model
 *
 * @package Controllers
 */
class Captcha extends CI_Controller {
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }

    /**
     * Make a request to this method to get a captcha image.
     */
    public function index()
    {
        header('Content-type: image/jpeg');
        $builder = new Gregwar\Captcha\CaptchaBuilder;
        $builder->build();
        $this->session->set_userdata('captcha_phrase', $builder->getPhrase());
        $builder->output();
    }
}
