<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/Google.php';

/**
 * Class Console
 *
 * CLI commands of Easy!Appointments, can only be executed from a terminal and not with a direct request.
 */
class Console extends EA_Controller {
    /**
     * Console constructor.
     */
    public function __construct()
    {
        if ( ! is_cli())
        {
            exit('No direct script access allowed');
        }

        parent::__construct();

        $this->load->library('migration');
        $this->load->model('admins_model');
        $this->load->model('customers_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
    }

    /**
     * Migrate the database to the latest state.
     *
     * Use this method to upgrade an existing installation to the latest database state.
     *
     * Notice:
     *
     * Do not use this method to install the app as it will not seed the database with the initial entries (admin,
     * provider, service, settings etc). Use the UI installation page for this.
     *
     * Usage:
     *
     * php index.php console migrate
     *
     * php index.php console migrate fresh
     *
     * @param string $type
     */
    public function migrate($type = '')
    {
        if ($type === 'fresh' && $this->migration->version(0) === FALSE)
        {
            show_error($this->migration->error_string());
        }

        if ($this->migration->current() === FALSE)
        {
            show_error($this->migration->error_string());
        }
    }

    /**
     * Seed the database with test data.
     *
     * Use this method to add test data to your database
     *
     * Usage:
     *
     * php index.php console seed
     */
    public function seed()
    {
        // Settings
        $this->settings_model->set_setting('company_name', 'Company Name');
        $this->settings_model->set_setting('company_email', 'info@example.org');
        $this->settings_model->set_setting('company_link', 'https://example.org');

        // Admin
        $this->admins_model->add([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.org',
            'phone_number' => '+1 (000) 000-0000',
            'settings' => [
                'username' => 'administrator',
                'password' => 'administrator',
                'notifications' => FALSE,
                'calendar_view' => CALENDAR_VIEW_DEFAULT
            ],
        ]);

        // Service
        $service_id = $this->services_model->add([
            'name' => 'Service',
            'duration' => '30',
            'price' => '0',
            'currency' => '',
            'availabilities_type' => 'flexible',
            'attendants_number' => '1'
        ]);

        // Provider
        $this->providers_model->add([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.org',
            'phone_number' => '+1 (000) 000-0000',
            'services' => [
                $service_id
            ],
            'settings' => [
                'username' => 'janedoe',
                'password' => 'janedoe',
                'working_plan' => $this->settings_model->get_setting('company_working_plan'),
                'notifications' => FALSE,
                'google_sync' => FALSE,
                'sync_past_days' => 5,
                'sync_future_days' => 5,
                'calendar_view' => CALENDAR_VIEW_DEFAULT
            ],
        ]);

        // Customer
        $this->customers_model->add([
            'first_name' => 'James',
            'last_name' => 'Doe',
            'email' => 'james@example.org',
            'phone_number' => '+1 (000) 000-0000',
        ]);
    }

    /**
     * Perform a console installation.
     *
     * Use this method to install Easy!Appointments directly from the terminal.
     *
     * Usage:
     *
     * php index.php console install
     */
    public function install()
    {
        $this->migrate('fresh');
        $this->seed();
        $this->output->set_output(PHP_EOL . '⇾ Installation completed, login with "administrator" / "administrator".' . PHP_EOL . PHP_EOL);
    }

    /**
     * Trigger the synchronization of all provider calendars with Google Calendar.
     *
     * Use this method in a cronjob to automatically sync events between Easy!Appointments and Google Calendar.
     *
     * Notice:
     *
     * Google syncing must first be enabled for each individual provider from inside the backend calendar page.
     *
     * Usage:
     *
     * php index.php console sync
     */
    public function sync()
    {
        $providers = $this->providers_model->get_batch();

        foreach ($providers as $provider)
        {
            if ( ! filter_var($provider['settings']['google_sync'], FILTER_VALIDATE_BOOLEAN))
            {
                continue;
            }

            Google::sync($provider['id']);
        }
    }
}
