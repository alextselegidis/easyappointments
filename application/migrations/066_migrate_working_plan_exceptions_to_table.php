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

class Migration_Migrate_working_plan_exceptions_to_table extends EA_Migration
{
    /**
     * Upgrade method.
     */
    public function up(): void
    {
        // Get all user settings that have working plan exceptions
        $user_settings = $this->db
            ->select('id_users, working_plan_exceptions')
            ->from('user_settings')
            ->where('working_plan_exceptions IS NOT NULL')
            ->where("working_plan_exceptions != ''")
            ->where("working_plan_exceptions != '{}'")
            ->get()
            ->result_array();

        foreach ($user_settings as $user_setting) {
            $provider_id = $user_setting['id_users'];
            $working_plan_exceptions = json_decode($user_setting['working_plan_exceptions'], true);

            if (empty($working_plan_exceptions) || !is_array($working_plan_exceptions)) {
                continue;
            }

            foreach ($working_plan_exceptions as $date => $exception) {
                // Skip invalid dates
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                    continue;
                }

                // Check if this exception already exists (same provider, same start_date and end_date)
                $existing = $this->db
                    ->where('start_date', $date)
                    ->where('end_date', $date)
                    ->where('id_users_provider', $provider_id)
                    ->get('working_plan_exceptions')
                    ->row();

                if ($existing) {
                    continue;
                }

                // Insert the exception into the new table
                // For migrated data, start_date and end_date are the same (single day)
                $data = [
                    'start_date' => $date,
                    'end_date' => $date,
                    'id_users_provider' => $provider_id,
                    'create_datetime' => date('Y-m-d H:i:s'),
                    'update_datetime' => date('Y-m-d H:i:s'),
                ];

                // Exception can be null (day off) or have start/end times
                if (is_array($exception)) {
                    $data['start_time'] = !empty($exception['start']) ? $exception['start'] : null;
                    $data['end_time'] = !empty($exception['end']) ? $exception['end'] : null;
                    $data['breaks'] = !empty($exception['breaks']) ? json_encode($exception['breaks']) : null;
                }

                $this->db->insert('working_plan_exceptions', $data);
            }
        }
    }

    /**
     * Downgrade method.
     */
    public function down(): void
    {
        // Get all working plan exceptions from the table grouped by provider
        // For downgrade, we expand date ranges back into individual dates
        $exceptions = $this->db
            ->select('id_users_provider, start_date, end_date, start_time, end_time, breaks')
            ->from('working_plan_exceptions')
            ->order_by('id_users_provider, start_date')
            ->get()
            ->result_array();

        $providers = [];

        foreach ($exceptions as $exception) {
            $provider_id = $exception['id_users_provider'];

            if (!isset($providers[$provider_id])) {
                $providers[$provider_id] = [];
            }

            $exception_data = null;

            if (!empty($exception['start_time']) || !empty($exception['end_time'])) {
                $exception_data = [
                    'start' => $exception['start_time'],
                    'end' => $exception['end_time'],
                    'breaks' => !empty($exception['breaks']) ? json_decode($exception['breaks'], true) : [],
                ];
            }

            // Expand date range into individual dates for JSON storage
            $start = new DateTime($exception['start_date']);
            $end = new DateTime($exception['end_date']);
            $end->modify('+1 day'); // Include end date

            $interval = new DateInterval('P1D');
            $period = new DatePeriod($start, $interval, $end);

            foreach ($period as $date) {
                $providers[$provider_id][$date->format('Y-m-d')] = $exception_data;
            }
        }

        // Update user settings with the combined exceptions
        foreach ($providers as $provider_id => $working_plan_exceptions) {
            $json = !empty($working_plan_exceptions) ? json_encode($working_plan_exceptions) : '{}';

            $this->db->where('id_users', $provider_id)->update('user_settings', [
                'working_plan_exceptions' => $json,
            ]);
        }
    }
}
