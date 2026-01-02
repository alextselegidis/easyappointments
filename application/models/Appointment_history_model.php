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
 * Appointment history model.
 *
 * Handles tracking of appointment quota changes.
 *
 * @package Models
 */
class Appointment_history_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'id_appointments' => 'integer',
        'id_user_subscriptions' => 'integer',
        'quota_change' => 'integer',
        'cancellation_hours_before' => 'float',
    ];

    /**
     * Log an appointment action.
     *
     * @param int $appointment_id Appointment ID.
     * @param int|null $subscription_id User subscription ID (null if no subscription).
     * @param string $action Action type (created, cancelled, quota_refunded, quota_not_refunded).
     * @param int $quota_change Quota change amount (positive or negative).
     * @param float|null $cancellation_hours_before Hours before appointment when cancelled.
     * @param string|null $notes Additional notes.
     *
     * @return int Returns the history record ID.
     *
     * @throws RuntimeException
     */
    public function log_action(
        int $appointment_id,
        ?int $subscription_id,
        string $action,
        int $quota_change = 0,
        ?float $cancellation_hours_before = null,
        ?string $notes = null
    ): int {
        $history = [
            'create_datetime' => date('Y-m-d H:i:s'),
            'id_appointments' => $appointment_id,
            'id_user_subscriptions' => $subscription_id,
            'action' => $action,
            'quota_change' => $quota_change,
            'cancellation_hours_before' => $cancellation_hours_before,
            'notes' => $notes,
        ];

        if (!$this->db->insert('appointment_history', $history)) {
            throw new RuntimeException('Could not insert appointment history.');
        }

        return (int) $this->db->insert_id();
    }

    /**
     * Get history for an appointment.
     *
     * @param int $appointment_id Appointment ID.
     *
     * @return array Returns an array of history records.
     */
    public function get_by_appointment(int $appointment_id): array
    {
        $history = $this->db
            ->get_where('appointment_history', ['id_appointments' => $appointment_id])
            ->result_array();

        foreach ($history as &$record) {
            $this->cast($record);
        }

        return $history;
    }

    /**
     * Get history for a subscription.
     *
     * @param int $subscription_id User subscription ID.
     *
     * @return array Returns an array of history records.
     */
    public function get_by_subscription(int $subscription_id): array
    {
        $history = $this->db
            ->get_where('appointment_history', ['id_user_subscriptions' => $subscription_id])
            ->result_array();

        foreach ($history as &$record) {
            $this->cast($record);
        }

        return $history;
    }
}
