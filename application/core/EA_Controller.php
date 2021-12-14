<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Easy!Appointments controller. 
 *
 * @property EA_Benchmark $benchmark
 * @property EA_Cache $cache
 * @property EA_Calendar $calendar
 * @property EA_Config $config
 * @property EA_DB_forge $dbforge
 * @property EA_DB_query_builder $db
 * @property EA_DB_utility $dbutil
 * @property EA_Email $email
 * @property EA_Encrypt $encrypt
 * @property EA_Encryption $encryption
 * @property EA_Exceptions $exceptions
 * @property EA_Hooks $hooks
 * @property EA_Input $input
 * @property EA_Lang $lang
 * @property EA_Loader $load
 * @property EA_Log $log
 * @property EA_Migration $migration
 * @property EA_Output $output
 * @property EA_Profiler $profiler
 * @property EA_Router $router
 * @property EA_Security $security
 * @property EA_Session $session
 * @property EA_Upload $upload
 * @property EA_URI $uri
 *
 * @property Admins_model $admins_model
 * @property Appointments_model $appointments_model
 * @property Consents_model $consents_model
 * @property Customers_model $customers_model
 * @property Providers_model $providers_model
 * @property Roles_model $roles_model
 * @property Secretaries_model $secretaries_model
 * @property Service_categories_model $service_categories_model
 * @property Services_model $services_model
 * @property Settings_model $settings_model
 * @property Unavailabilities_model $unavailabilities_model
 * @property Users_model $users_model
 *
 * @property Accounts $accounts
 * @property Api $api
 * @property Availability $availability
 * @property Email_messages $email_messages
 * @property Google_Sync $google_sync
 * @property Ics_file $ics_file
 * @property Instance $instance
 * @property Notifications $notifications
 * @property Synchronization $synchronization
 * @property Timezones $timezones
 */
class EA_Controller extends CI_Controller {
    /**
     * EA_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->configure_language();
        
        $this->load_common_js_config();
    }

    /**
     * Configure the language.
     */
    private function configure_language()
    {
        $session_language = session('language');

        if ($session_language)
        {
            config(['language' => $session_language]);
        }

        $this->lang->load('translations');
    }

    /**
     * Load common JS config for all requests. 
     */
    private function load_common_js_config()
    {
        js_config([
            'base_url' => config('base_url'),
            'index_page' => config('index_page'),
            'csrf_token' => config('csrf_token'),
        ]);
    }
}
