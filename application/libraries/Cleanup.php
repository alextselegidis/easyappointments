<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Cleanup library.
 *
 * Handles the cleanup of expired sessions, old logs, cache files, and customer data based on retention policies.
 *
 * @package Libraries
 */
class Cleanup
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * Cleanup constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('customers_model');
    }

    /**
     * Run all cleanup tasks.
     *
     * @throws Exception
     */
    public function run(): void
    {
        $this->cleanup_sessions();
        $this->cleanup_logs();
        $this->cleanup_cache();
        $this->cleanup_customer_data();
    }

    /**
     * Clean up expired session files (older than retention period).
     */
    public function cleanup_sessions(): void
    {
        $session_path = APPPATH . '../storage/sessions';
        $max_age_seconds = STORAGE_RETENTION_DAYS * 86400;

        if (!is_dir($session_path)) {
            response(PHP_EOL . '⇾ Session directory not found.' . PHP_EOL);
            return;
        }

        $deleted_count = 0;
        $cutoff_time = time() - $max_age_seconds;

        foreach (glob($session_path . '/ea_session*') as $file) {
            if (is_file($file) && filemtime($file) < $cutoff_time) {
                if (unlink($file)) {
                    $deleted_count++;
                }
            }
        }

        response(PHP_EOL . "⇾ Session cleanup completed. Deleted {$deleted_count} expired session file(s)." . PHP_EOL);
    }

    /**
     * Clean up old log files (older than retention period).
     */
    public function cleanup_logs(): void
    {
        $log_path = APPPATH . '../storage/logs';

        if (!is_dir($log_path)) {
            response('⇾ Log directory not found.' . PHP_EOL);
            return;
        }

        $deleted_count = 0;
        $cutoff_time = time() - (STORAGE_RETENTION_DAYS * 86400);

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
     * Clean up old cache files (older than retention period).
     */
    public function cleanup_cache(): void
    {
        $cache_path = APPPATH . '../storage/cache';

        if (!is_dir($cache_path)) {
            response('⇾ Cache directory not found.' . PHP_EOL);
            return;
        }

        $deleted_count = 0;
        $cutoff_time = time() - (STORAGE_RETENTION_DAYS * 86400);

        $files = glob($cache_path . '/*');

        foreach ($files as $file) {
            if (is_file($file) && !in_array(basename($file), ['index.html', '.gitkeep', '.htaccess'])) {
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
     *
     * @throws Exception
     */
    public function cleanup_customer_data(): void
    {
        $data_retention_days = (int) setting('data_retention_days');

        if ($data_retention_days <= 0) {
            response('⇾ Data retention is disabled (set to 0 days).' . PHP_EOL . PHP_EOL);
            return;
        }

        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$data_retention_days} days"));

        $db = $this->CI->db;

        $query = $db->query("
            SELECT DISTINCT c.id, c.email
            FROM `" . $db->dbprefix('users') . "` c
            INNER JOIN `" . $db->dbprefix('roles') . "` r ON r.id = c.id_roles AND r.slug = 'customer'
            WHERE c.id NOT IN (
                SELECT DISTINCT id_users_customer
                FROM `" . $db->dbprefix('appointments') . "`
                WHERE end_datetime >= ?
            )
            AND c.create_datetime < ?
        ", [$cutoff_date, $cutoff_date]);

        $customers_to_delete = $query->result_array();
        $deleted_count = 0;

        foreach ($customers_to_delete as $customer) {
            try {
                $this->CI->customers_model->delete((int) $customer['id']);
                $deleted_count++;
                response('⇾ Deleted customer ID: ' . $customer['id'] . PHP_EOL);
            } catch (Exception $e) {
                response('⇾ Failed to delete customer ID: ' . $customer['id'] . ' - ' . $e->getMessage() . PHP_EOL);
            }
        }

        response(PHP_EOL . "⇾ Data retention cleanup completed. Deleted {$deleted_count} customer(s)." . PHP_EOL . PHP_EOL);
    }
}
