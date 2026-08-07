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
 * @property Service_categories_model $service_categories_model
 * @property Consents_model $consents_model
 * @property Customers_model $customers_model
 * @property Providers_model $providers_model
 * @property Roles_model $roles_model
 * @property Secretaries_model $secretaries_model
 * @property Services_model $services_model
 * @property Settings_model $settings_model
 * @property Unavailabilities_model $unavailabilities_model
 * @property Users_model $users_model
 * @property Webhooks_model $webhooks_model
 * @property Blocked_periods_model $blocked_periods_model
 * @property Working_plan_exceptions_model $working_plan_exceptions_model
 *
 * @property Accounts $accounts
 * @property Api $api
 * @property Cleanup $cleanup
 * @property Availability $availability
 * @property Email_messages $email_messages
 * @property Google_Sync $google_sync
 * @property Caldav_Sync $caldav_sync
 * @property Ics_file $ics_file
 * @property Instance $instance
 * @property Ldap_client $ldap_client
 * @property Notifications $notifications
 * @property Permissions $permissions
 * @property Synchronization $synchronization
 * @property Timezones $timezones
 * @property Webhooks_client $webhooks_client
 */
class EA_Controller extends CI_Controller
{
    /**
     * EA_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('accounts');

        $this->check_storage_writable();
        $this->check_pending_migrations();
        $this->ensure_user_exists();
        $this->configure_timezone();
        $this->configure_language();
        $this->load_common_html_vars();
        $this->load_common_script_vars();

        rate_limit($this->input->ip_address());
    }

    private function ensure_user_exists()
    {
        $user_id = session('user_id');

        if (!$user_id || !$this->db->table_exists('users')) {
            return;
        }

        if (!$this->accounts->does_account_exist($user_id)) {
            session_destroy();

            abort(403, 'Forbidden');
        }
    }

    /**
     * Configure the language.
     */
    private function configure_language()
    {
        $session_language = session('language');
        $query_language = request('language');
        $available_languages = config('available_languages');

        // Priority: session > query param > default (english)
        $language = null;

        if ($session_language && in_array($session_language, $available_languages)) {
            $language = $session_language;
        } elseif ($query_language && in_array($query_language, $available_languages)) {
            $language = $query_language;
        }

        if ($language) {
            $language_codes = config('language_codes');

            config([
                'language' => $language,
                'language_code' => array_search($language, $language_codes) ?: 'en',
            ]);
        }

        $this->lang->load('translations');
    }

    /**
     * Load common script vars for all requests.
     */
    private function load_common_html_vars()
    {
        html_vars([
            'base_url' => config('base_url'),
            'index_page' => config('index_page'),
            'available_languages' => config('available_languages'),
            'language' => $this->lang->language,
            'csrf_token' => $this->security->get_csrf_hash(),
        ]);
    }

    /**
     * Load common script vars for all requests.
     */
    private function load_common_script_vars()
    {
        script_vars([
            'base_url' => config('base_url'),
            'index_page' => config('index_page'),
            'available_languages' => config('available_languages'),
            'csrf_token' => $this->security->get_csrf_hash(),
            'language' => config('language'),
            'language_code' => config('language_code'),
        ]);
    }

    /**
     * Set the default timezone of the app, based on the selected setting.
     */
    private function configure_timezone(): void
    {
        if (!$this->db->table_exists('settings')) {
            return;
        }

        $default_timezone = setting('default_timezone');

        if (!empty($default_timezone)) {
            date_default_timezone_set($default_timezone);
        }
    }

    /**
     * Redirect to the update page if there are pending database migrations.
     *
     * This prevents cryptic database errors (e.g. "Table doesn't exist") when a user
     * upgrades the application files without running the DB migration step.
     */
    private function check_pending_migrations(): void
    {
        $excluded_controllers = ['update', 'installation', 'console'];

        if (in_array($this->router->class, $excluded_controllers, true)) {
            return;
        }

        if (!$this->db->table_exists('migrations')) {
            return;
        }

        $this->load->library('migration');

        $migrations = $this->migration->find_migrations();

        if (empty($migrations)) {
            return;
        }

        $latest_version = (int) max(array_keys($migrations));
        $current_version = (int) $this->migration->current_version();

        if ($current_version < $latest_version) {
            show_error(
                'There are pending database migrations that must be run before using the application. ' .
                    'Please log in as an administrator and visit <a href="' .
                    site_url('update') .
                    '">' .
                    site_url('update') .
                    '</a> to apply them, or run <code>php index.php console migrate</code> from the terminal.',
                503,
                'Database Migration Required',
            );
        }
    }

    /**
     * Check if the storage folder is writable.
     */
    private function check_storage_writable(): void
    {
        $storage_path = APPPATH . '../storage';

        if (!is_dir($storage_path)) {
            show_error(
                'The storage folder does not exist: ' .
                    $storage_path .
                    '. ' .
                    'Please create this directory and ensure it is writable by the web server.',
                500,
                'Storage Configuration Error',
            );
        }

        if (!is_writable($storage_path)) {
            show_error(
                'The storage folder is not writable: ' .
                    $storage_path .
                    '. ' .
                    'Please ensure the web server has write permissions to this directory and its subdirectories (cache, logs, sessions, uploads).',
                500,
                'Storage Configuration Error',
            );
        }
    }
}
