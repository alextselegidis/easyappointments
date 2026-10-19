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
 * Synchronization library.
 *
 * Handles external calendar synchronization functionality.
 *
 * @package Libraries
 */
class Synchronization
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * Synchronization constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('providers_model');
        $this->CI->load->model('appointments_model');
        $this->CI->load->model('unavailabilities_model');

        $this->CI->load->library('google_sync');
        $this->CI->load->library('caldav_sync');
    }

    /**
     * Synchronize changes made to the appointment with external calendars.
     *
     * @param array $appointment Appointment record.
     * @param array $service Service record.
     * @param array $provider Provider record.
     * @param array $customer Customer record.
     * @param array $settings Required settings for the notification content.
     */
    public function sync_appointment_saved(
        array &$appointment,
        array $service,
        array $provider,
        array $customer,
        array $settings,
    ): void {
        // Every integration gets its own try/catch, so that a failing one does not stop the rest from syncing.

        if ($provider['settings']['google_sync']) {
            try {
                $this->refresh_google_token($provider);

                if (empty($appointment['id_google_calendar'])) {
                    $google_event = $this->CI->google_sync->add_appointment(
                        $appointment,
                        $provider,
                        $service,
                        $customer,
                        $settings,
                    );

                    $appointment['id_google_calendar'] = $google_event->getId();

                    $this->CI->appointments_model->save($appointment);
                } else {
                    $this->CI->google_sync->update_appointment($appointment, $provider, $service, $customer, $settings);
                }
            } catch (Throwable $e) {
                $this->log_sync_failure('appointment', $appointment['id'] ?? null, 'Google', $e);
            }
        }

        if ($provider['settings']['caldav_sync']) {
            try {
                $appointment['id_caldav_calendar'] = $this->CI->caldav_sync->save_appointment(
                    $appointment,
                    $service,
                    $provider,
                    $customer,
                );

                $this->CI->appointments_model->save($appointment);
            } catch (Throwable $e) {
                $this->log_sync_failure('appointment', $appointment['id'] ?? null, 'CalDAV', $e);
            }
        }
    }

    /**
     * Synchronize removal of an appointment with external calendars.
     *
     * @param array $appointment Appointment record.
     * @param array $provider Provider record.
     */
    public function sync_appointment_deleted(array $appointment, array $provider): void
    {
        if ($provider['settings']['google_sync'] && !empty($appointment['id_google_calendar'])) {
            try {
                $this->refresh_google_token($provider);

                $this->CI->google_sync->delete_appointment($provider, $appointment['id_google_calendar']);
            } catch (Throwable $e) {
                $this->log_sync_failure('deleted appointment', $appointment['id'] ?? null, 'Google', $e);
            }
        }

        if ($provider['settings']['caldav_sync'] && !empty($appointment['id_caldav_calendar'])) {
            try {
                $this->CI->caldav_sync->delete_event($provider, $appointment['id_caldav_calendar']);
            } catch (Throwable $e) {
                $this->log_sync_failure('deleted appointment', $appointment['id'] ?? null, 'CalDAV', $e);
            }
        }
    }

    /**
     * Synchronize changes made to the unavailability with external calendars.
     *
     * @param array $unavailability Unavailability record.
     * @param array $provider Provider record.
     */
    public function sync_unavailability_saved(array $unavailability, array $provider): void
    {
        if ($provider['settings']['google_sync']) {
            try {
                $this->refresh_google_token($provider);

                if (empty($unavailability['id_google_calendar'])) {
                    $google_event = $this->CI->google_sync->add_unavailability($provider, $unavailability);

                    $unavailability['id_google_calendar'] = $google_event->getId();

                    $this->CI->unavailabilities_model->save($unavailability);
                } else {
                    $this->CI->google_sync->update_unavailability($provider, $unavailability);
                }
            } catch (Throwable $e) {
                $this->log_sync_failure('unavailability', $unavailability['id'] ?? null, 'Google', $e);
            }
        }

        if ($provider['settings']['caldav_sync']) {
            try {
                $unavailability['id_caldav_calendar'] = $this->CI->caldav_sync->save_unavailability(
                    $unavailability,
                    $provider,
                );

                $this->CI->unavailabilities_model->save($unavailability);
            } catch (Throwable $e) {
                $this->log_sync_failure('unavailability', $unavailability['id'] ?? null, 'CalDAV', $e);
            }
        }
    }

    /**
     * Synchronize removal of an unavailability with external calendars.
     *
     * @param array $unavailability Unavailability record.
     * @param array $provider Provider record.
     */
    public function sync_unavailability_deleted(array $unavailability, array $provider): void
    {
        if ($provider['settings']['google_sync'] && !empty($unavailability['id_google_calendar'])) {
            try {
                $this->refresh_google_token($provider);

                $this->CI->google_sync->delete_unavailability($provider, $unavailability['id_google_calendar']);
            } catch (Throwable $e) {
                $this->log_sync_failure('deleted unavailability', $unavailability['id'] ?? null, 'Google', $e);
            }
        }

        if ($provider['settings']['caldav_sync'] && !empty($unavailability['id_caldav_calendar'])) {
            try {
                $this->CI->caldav_sync->delete_event($provider, $unavailability['id_caldav_calendar']);
            } catch (Throwable $e) {
                $this->log_sync_failure('deleted unavailability', $unavailability['id'] ?? null, 'CalDAV', $e);
            }
        }
    }

    /**
     * Make sure a synced appointment is removed from Google/CalDAV Calendar, if its provider is changed.
     *
     * @param int|string $appointment_id Appointment ID.
     * @param int|string $new_provider_id Provider ID the appointment is about to be assigned to.
     *
     * @throws Exception
     */
    public function remove_appointment_on_provider_change($appointment_id, $new_provider_id): void
    {
        $existing_appointment = $this->CI->appointments_model->find($appointment_id);

        if (empty($existing_appointment['id_google_calendar']) && empty($existing_appointment['id_caldav_calendar'])) {
            return; // The appointment is not synced with any external calendar.
        }

        $existing_provider_id = (int) $existing_appointment['id_users_provider'];

        if ($existing_provider_id === (int) $new_provider_id) {
            return; // The provider stays the same, so the existing events remain valid.
        }

        $existing_provider = $this->CI->providers_model->find($existing_provider_id);

        if ($existing_provider['settings']['google_sync'] || $existing_provider['settings']['caldav_sync']) {
            $this->sync_appointment_deleted($existing_appointment, $existing_provider);
        }

        // Forget the event IDs of the previous provider's calendars, otherwise the next synchronization looks for
        // events that no longer exist and removes the local appointment instead.
        $existing_appointment['id_google_calendar'] = null;
        $existing_appointment['id_caldav_calendar'] = null;

        $this->CI->appointments_model->save($existing_appointment);
    }

    /**
     * Refresh the access token of the provider's Google Calendar account.
     *
     * @param array $provider Provider record.
     */
    private function refresh_google_token(array $provider): void
    {
        $google_token = json_decode((string) ($provider['settings']['google_token'] ?? ''), true);

        if (empty($google_token['refresh_token'])) {
            throw new RuntimeException('No google token available for the provider: ' . $provider['id']);
        }

        $this->CI->google_sync->refresh_token($google_token['refresh_token']);
    }

    /**
     * Log a synchronization failure of a single record and integration.
     *
     * @param string $record_type Record type, used in the log message.
     * @param int|string|null $record_id Record ID, used in the log message.
     * @param string $integration Integration name, used in the log message.
     * @param Throwable $e Exception that caused the failure.
     */
    private function log_sync_failure(string $record_type, $record_id, string $integration, Throwable $e): void
    {
        log_message(
            'error',
            'Synchronization - Could not sync ' .
                $record_type .
                ' (' .
                ($record_id ?: '-') .
                ') with ' .
                $integration .
                ': ' .
                $e->getMessage(),
        );

        log_message('error', $e->getTraceAsString());
    }
}
