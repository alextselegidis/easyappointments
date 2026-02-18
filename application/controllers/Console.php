<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

use Jsvrcek\ICS\Exception\CalendarEventException;

require_once __DIR__ . '/Google.php';
require_once __DIR__ . '/Caldav.php';

/**
 * Console controller.
 *
 * Handles all the Console related operations.
 */
class Console extends EA_Controller
{
    /**
     * Console constructor.
     */
    public function __construct()
    {
        if (!is_cli()) {
            exit('No direct script access allowed');
        }

        parent::__construct();

        $this->load->dbutil();

        $this->load->library('instance');

        $this->load->model('admins_model');
        $this->load->model('customers_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('settings_model');
    }

    /**
     * Perform a console installation.
     *
     * Use this method to install Easy!Appointments directly from the terminal.
     *
     * Usage:
     *
     * php index.php console install
     *
     * @throws Exception
     */
    public function install(): void
    {
        $this->instance->migrate('fresh');

        $password = $this->instance->seed();

        response(
            PHP_EOL . '⇾ Installation completed, login with "administrator" / "' . $password . '".' . PHP_EOL . PHP_EOL,
        );
    }

    /**
     * Migrate the database to the latest state.
     *
     * Use this method to upgrade an Easy!Appointments instance to the latest database state.
     *
     * Notice:
     *
     * Do not use this method to install the app as it will not seed the database with the initial entries (admin,
     * provider, service, settings etc.).
     *
     * Usage:
     *
     * php index.php console migrate
     *
     * php index.php console migrate fresh
     *
     * @param string $type
     */
    public function migrate(string $type = ''): void
    {
        $this->instance->migrate($type);
    }

    /**
     * Seed the database with test data.
     *
     * Use this method to add test data to your database
     *
     * Usage:
     *
     * php index.php console seed
     * @throws Exception
     */
    public function seed(): void
    {
        $this->instance->seed();
    }

    /**
     * Create a database backup file.
     *
     * Use this method to back up your Easy!Appointments data.
     *
     * Usage:
     *
     * php index.php console backup
     *
     * php index.php console backup /path/to/backup/folder
     *
     * @throws Exception
     */
    public function backup(): void
    {
        $this->instance->backup($GLOBALS['argv'][3] ?? null);
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
     *
     * @throws CalendarEventException
     * @throws Exception
     * @throws Throwable
     */
    public function sync(): void
    {
        $providers = $this->providers_model->get();

        foreach ($providers as $provider) {
            if (filter_var($provider['settings']['google_sync'], FILTER_VALIDATE_BOOLEAN)) {
                Google::sync((string) $provider['id']);
            }

            if (filter_var($provider['settings']['caldav_sync'], FILTER_VALIDATE_BOOLEAN)) {
                Caldav::sync((string) $provider['id']);
            }
        }
    }

    /**
     * Clean up old customer data based on data retention settings.
     *
     * Use this method in a cronjob to automatically delete customer data older than the configured retention period.
     *
     * Usage:
     *
     * php index.php console cleanup
     *
     * @throws Exception
     */
    public function cleanup(): void
    {
        // Clean up old session files
        $this->cleanup_sessions();

        // Clean up old log files
        $this->cleanup_logs();

        // Clean up cache files
        $this->cleanup_cache();

        // Clean up customer data based on retention policy
        $this->cleanup_customer_data();
    }

    /**
     * Clean up expired session files.
     */
    private function cleanup_sessions(): void
    {
        $session_path = APPPATH . '../storage/sessions';
        $session_expiration = config_item('sess_expiration') ?: 7200;

        if (!is_dir($session_path)) {
            response(PHP_EOL . '⇾ Session directory not found.' . PHP_EOL);
            return;
        }

        $deleted_count = 0;
        $now = time();

        foreach (glob($session_path . '/ci_session*') as $file) {
            if (is_file($file) && ($now - filemtime($file)) > $session_expiration) {
                if (unlink($file)) {
                    $deleted_count++;
                }
            }
        }

        response(PHP_EOL . "⇾ Session cleanup completed. Deleted {$deleted_count} expired session file(s)." . PHP_EOL);
    }

    /**
     * Clean up old log files (older than 30 days).
     */
    private function cleanup_logs(): void
    {
        $log_path = APPPATH . '../storage/logs';
        $max_age_days = 30;

        if (!is_dir($log_path)) {
            response('⇾ Log directory not found.' . PHP_EOL);
            return;
        }

        $deleted_count = 0;
        $cutoff_time = time() - ($max_age_days * 86400);

        foreach (glob($log_path . '/log-*.php') as $file) {
            if (is_file($file) && filemtime($file) < $cutoff_time) {
                if (unlink($file)) {
                    $deleted_count++;
                }
            }
        }

        response("⇾ Log cleanup completed. Deleted {$deleted_count} old log file(s)." . PHP_EOL);
    }

    /**
     * Clean up old cache files (older than 7 days).
     */
    private function cleanup_cache(): void
    {
        $cache_path = APPPATH . '../storage/cache';
        $max_age_days = 7;

        if (!is_dir($cache_path)) {
            response('⇾ Cache directory not found.' . PHP_EOL);
            return;
        }

        $deleted_count = 0;
        $cutoff_time = time() - ($max_age_days * 86400);

        $files = glob($cache_path . '/*');

        foreach ($files as $file) {
            // Skip index.html and .gitkeep files
            if (is_file($file) && !in_array(basename($file), ['index.html', '.gitkeep'])) {
                if (filemtime($file) < $cutoff_time) {
                    if (unlink($file)) {
                        $deleted_count++;
                    }
                }
            }
        }

        response("⇾ Cache cleanup completed. Deleted {$deleted_count} old cache file(s)." . PHP_EOL);
    }

    /**
     * Clean up customer data based on retention policy.
     */
    private function cleanup_customer_data(): void
    {
        $data_retention_days = (int) setting('data_retention_days');

        if ($data_retention_days <= 0) {
            response('⇾ Data retention is disabled (set to 0 days).' . PHP_EOL . PHP_EOL);
            return;
        }

        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$data_retention_days} days"));

        // Find customers whose last appointment is older than the retention period
        $query = $this->db->query("
            SELECT DISTINCT c.id, c.email
            FROM `" . $this->db->dbprefix('users') . "` c
            INNER JOIN `" . $this->db->dbprefix('roles') . "` r ON r.id = c.id_roles AND r.slug = 'customer'
            WHERE c.id NOT IN (
                SELECT DISTINCT id_users_customer
                FROM `" . $this->db->dbprefix('appointments') . "`
                WHERE end_datetime >= ?
            )
            AND c.create_datetime < ?
        ", [$cutoff_date, $cutoff_date]);

        $customers_to_delete = $query->result_array();
        $deleted_count = 0;

        foreach ($customers_to_delete as $customer) {
            try {
                $this->customers_model->delete((int) $customer['id']);
                $deleted_count++;
                response('⇾ Deleted customer ID: ' . $customer['id'] . PHP_EOL);
            } catch (Exception $e) {
                response('⇾ Failed to delete customer ID: ' . $customer['id'] . ' - ' . $e->getMessage() . PHP_EOL);
            }
        }

        response(PHP_EOL . "⇾ Data retention cleanup completed. Deleted {$deleted_count} customer(s)." . PHP_EOL . PHP_EOL);
    }

    /**
     * Show help information about the console capabilities.
     *
     * Use this method to see the available commands.
     *
     * Usage:
     *
     * php index.php console help
     */
    public function help(): void
    {
        $help = [
            '',
            'Easy!Appointments ' . config('version'),
            '',
            'Usage:',
            '',
            '⇾ php index.php console [command] [arguments]',
            '',
            'Commands:',
            '',
            '⇾ php index.php console migrate',
            '⇾ php index.php console migrate fresh',
            '⇾ php index.php console migrate up',
            '⇾ php index.php console migrate down',
            '⇾ php index.php console seed',
            '⇾ php index.php console install',
            '⇾ php index.php console backup',
            '⇾ php index.php console sync',
            '⇾ php index.php console cleanup    (cleans sessions, logs, cache, and customer data)',
            '',
            '',
        ];

        response(implode(PHP_EOL, $help));
    }
}
