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
 * Quota reset controller.
 *
 * Handles monthly quota reset for user subscriptions.
 * This controller should be called by a cron job on the 1st of each month.
 *
 * Example cron job (runs at 00:01 on the 1st of each month):
 * 1 0 1 * * /usr/bin/php /path/to/easyappointments/index.php quota_reset reset
 *
 * @package Controllers
 */
class Quota_reset extends EA_Controller
{
    /**
     * Quota_reset constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('user_subscriptions_model');
    }

    /**
     * Reset monthly quotas for all active subscriptions.
     *
     * This method resets the appointments_used counter for all active subscriptions.
     * It should be called automatically via cron job on the 1st of each month.
     */
    public function reset(): void
    {
        try {
            // Security: Only allow execution from CLI or with a secret token
            if (!$this->input->is_cli_request()) {
                $token = $this->input->get('token');
                $expected_token = config('quota_reset_token') ?? setting('quota_reset_token');

                if (empty($expected_token) || $token !== $expected_token) {
                    show_error('Unauthorized access', 403, '403 Forbidden');
                    return;
                }
            }

            $reset_count = $this->user_subscriptions_model->reset_monthly_quotas();

            $message = "Successfully reset quotas for {$reset_count} subscription(s).";

            if ($this->input->is_cli_request()) {
                echo $message . PHP_EOL;
            } else {
                json_response([
                    'success' => true,
                    'message' => $message,
                    'reset_count' => $reset_count,
                ]);
            }

            // Log the reset
            log_message('info', $message);
        } catch (Throwable $e) {
            $error_message = 'Error resetting quotas: ' . $e->getMessage();

            log_message('error', $error_message);

            if ($this->input->is_cli_request()) {
                echo $error_message . PHP_EOL;
            } else {
                json_exception($e);
            }
        }
    }

    /**
     * Get quota reset status and information.
     *
     * Returns information about when quotas were last reset and how many active subscriptions exist.
     */
    public function status(): void
    {
        try {
            // Security check
            if (!$this->input->is_cli_request()) {
                $token = $this->input->get('token');
                $expected_token = config('quota_reset_token') ?? setting('quota_reset_token');

                if (empty($expected_token) || $token !== $expected_token) {
                    show_error('Unauthorized access', 403, '403 Forbidden');
                    return;
                }
            }

            $active_subscriptions = $this->user_subscriptions_model->get([
                'is_active' => 1,
                'delete_datetime' => null,
            ]);

            $needs_reset = 0;
            $already_reset = 0;

            $current_month_start = date('Y-m-01');

            foreach ($active_subscriptions as $subscription) {
                if (
                    empty($subscription['last_quota_reset_date']) ||
                    $subscription['last_quota_reset_date'] < $current_month_start
                ) {
                    $needs_reset++;
                } else {
                    $already_reset++;
                }
            }

            $status_info = [
                'total_active_subscriptions' => count($active_subscriptions),
                'subscriptions_needing_reset' => $needs_reset,
                'subscriptions_already_reset_this_month' => $already_reset,
                'current_month' => date('F Y'),
                'current_month_start' => $current_month_start,
            ];

            if ($this->input->is_cli_request()) {
                echo 'Quota Reset Status:' . PHP_EOL;
                echo '===================' . PHP_EOL;
                foreach ($status_info as $key => $value) {
                    echo ucwords(str_replace('_', ' ', $key)) . ': ' . $value . PHP_EOL;
                }
            } else {
                json_response($status_info);
            }
        } catch (Throwable $e) {
            $error_message = 'Error getting quota reset status: ' . $e->getMessage();

            log_message('error', $error_message);

            if ($this->input->is_cli_request()) {
                echo $error_message . PHP_EOL;
            } else {
                json_exception($e);
            }
        }
    }
}
