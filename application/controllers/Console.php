<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

require_once __DIR__ .'/Google.php';

/**
 * Class Console
 *
 * CLI commands of Easy!Appointments, can only be executed from a terminal and not with a direct request.
 *
 * @property CI_Migration $migration
 * @property Providers_model $providers_model
 */
class Console extends CI_Controller {
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
        $this->load->model('providers_model');
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
     */
    public function migrate()
    {
        if ($this->migration->current() === FALSE)
        {
            show_error($this->migration->error_string());
        }
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
