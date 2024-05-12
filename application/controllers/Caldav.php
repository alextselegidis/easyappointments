<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Caldav controller.
 *
 * Handles the Caldav Calendar synchronization related operations.
 *
 * @package Controllers
 */
class Caldav extends EA_Controller
{
    /**
     * Caldav constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('caldav_sync');

        $this->load->model('appointments_model');
        $this->load->model('unavailabilities_model');
        $this->load->model('providers_model');
    }

    /**
     * Connect to the target CalDAV server
     *
     * @return void
     */
    public function connect_to_server(): void
    {
        try {
            $provider_id = request('provider_id');

            $user_id = session('user_id');

            if (cannot('edit', PRIV_USERS) && (int) $user_id !== (int) $provider_id) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $caldav_url = request('caldav_url');
            $caldav_username = request('caldav_username');
            $caldav_password = request('caldav_password');

            $default_caldav_calendar = $this->caldav_sync->get_default_calendar(
                $caldav_url,
                $caldav_username,
                $caldav_password,
            );

            if (!$default_caldav_calendar) {
                json_response([
                    'success' => false,
                ]);

                return;
            }

            $provider = $this->providers_model->find($provider_id);

            $provider['settings']['caldav_sync'] = true;
            $provider['settings']['caldav_url'] = $caldav_url;
            $provider['settings']['caldav_username'] = $caldav_username;
            $provider['settings']['caldav_password'] = $caldav_password;
            $provider['settings']['caldav_calendar'] = $default_caldav_calendar;

            $this->providers_model->save($provider);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Sync the provider events with the remote CalDAV calendar.
     *
     * @param string $provider_id Provider ID (String because this is used with HTTP and CLI)
     *
     * @return void
     *
     * @throws \Jsvrcek\ICS\Exception\CalendarEventException
     * @throws Exception
     * @throws Throwable
     */
    public static function sync(string $provider_id): void
    {
        /** @var EA_Controller $CI */
        $CI = get_instance();

        $CI->load->library('caldav_sync');

        // Load the libraries as this method is called statically from the CLI command

        $CI->load->model('appointments_model');
        $CI->load->model('unavailabilities_model');
        $CI->load->model('providers_model');
        $CI->load->model('services_model');
        $CI->load->model('customers_model');
        $CI->load->model('settings_model');

        $user_id = session('user_id');

        if (!$user_id && !is_cli()) {
            return;
        }

        if (empty($provider_id)) {
            throw new InvalidArgumentException('No provider ID provided.');
        }

        $provider = $CI->providers_model->find($provider_id);

        // Check whether the selected provider has the CalDAV Sync enabled.

        if (!$provider['settings']['caldav_sync']) {
            return; // The selected provider does not have the CalDAV Sync enabled.
        }

        // Fetch provider's appointments that belong to the sync time period.

        $sync_past_days = $provider['settings']['sync_past_days'];

        $sync_future_days = $provider['settings']['sync_future_days'];

        $start_date_time = date('Y-m-d H:i:s', strtotime('-' . $sync_past_days . ' days'));

        $end_date_time = date('Y-m-d H:i:s', strtotime('+' . $sync_future_days . ' days'));

        $where = [
            'start_datetime >=' => $start_date_time,
            'end_datetime <=' => $end_date_time,
            'id_users_provider' => $provider['id'],
        ];

        $appointments = $CI->appointments_model->get($where);

        $unavailabilities = $CI->unavailabilities_model->get($where);

        $local_events = [...$appointments, ...$unavailabilities];

        // Sync each appointment with CalDAV Calendar by following the project's sync protocol (see documentation).

        foreach ($local_events as $local_event) {
            if (!$local_event['is_unavailability']) {
                $service = $CI->services_model->find($local_event['id_services']);
                $customer = $CI->customers_model->find($local_event['id_users_customer']);
                $events_model = $CI->appointments_model;
            } else {
                $service = null;
                $customer = null;
                $events_model = $CI->unavailabilities_model;
            }

            // If current appointment not synced yet, add to Google Calendar.

            if (!$local_event['id_caldav_calendar']) {
                if (!$local_event['is_unavailability']) {
                    $caldav_event_id = $CI->caldav_sync->save_appointment($local_event, $service, $provider, $customer);
                } else {
                    $caldav_event_id = $CI->caldav_sync->save_unavailability($local_event, $provider);
                }

                $local_event['id_google_calendar'] = $caldav_event_id;

                $events_model->save($local_event); // Save the CalDAV Calendar ID.

                continue;
            }

            // Appointment is synced with Google Calendar.

            try {
                $caldav_event = $CI->caldav_sync->get_event($provider, $local_event['id_caldav_calendar']);

                if ($caldav_event['status'] === 'CANCELLED') {
                    throw new Exception('Event is cancelled, remove the record from Easy!Appointments.');
                }

                // If Google Calendar event is different from Easy!Appointments appointment then update Easy!Appointments record.
                $local_event_start = strtotime($local_event['start_datetime']);
                $local_event_end = strtotime($local_event['end_datetime']);

                $caldav_event_start = new DateTime($caldav_event['start_datetime']);
                $caldav_event_end = new DateTime($caldav_event['end_datetime']);

                $caldav_event_notes = $local_event['is_unavailability']
                    ? $caldav_event['summary'] . ' ' . $caldav_event['description']
                    : $caldav_event['description'];

                $is_different =
                    $local_event_start !== $caldav_event_start->getTimestamp() ||
                    $local_event_end !== $caldav_event_end->getTimestamp() ||
                    $local_event['notes'] !== $caldav_event_notes;

                if ($is_different) {
                    $local_event['start_datetime'] = $caldav_event_start->format('Y-m-d H:i:s');
                    $local_event['end_datetime'] = $caldav_event_end->format('Y-m-d H:i:s');
                    $local_event['notes'] = $caldav_event_notes;
                    $events_model->save($local_event);
                }
            } catch (Throwable) {
                // Appointment not found on Google Calendar, delete from Easy!Appointments.
                $events_model->delete($local_event['id']);

                $local_event['id_caldav_calendar'] = null;
            }
        }

        // Add CalDAV Calendar events that do not exist in Easy!Appointments.

        try {
            $caldav_events = $CI->caldav_sync->get_sync_events($provider, $start_date_time, $end_date_time);
        } catch (Throwable $e) {
            if ($e->getCode() === 404) {
                log_message('error', 'CalDAV - Remote Calendar not found for provider ID: ' . $provider_id);

                return; // The remote calendar was not found.
            } else {
                throw $e;
            }
        }

        foreach ($caldav_events as $caldav_event) {
            if ($caldav_event['status'] === 'CANCELLED') {
                continue;
            }

            if ($caldav_event['start_datetime'] === $caldav_event['end_datetime']) {
                continue; // Cannot sync events with the same start and end date time value
            }

            $appointment_results = $CI->appointments_model->get(['id_caldav_calendar' => $caldav_event['id']]);

            if (!empty($appointment_results)) {
                continue;
            }

            $unavailability_results = $CI->unavailabilities_model->get([
                'id_caldav_calendar' => $caldav_event['id'],
            ]);

            if (!empty($unavailability_results)) {
                continue;
            }

            // Record doesn't exist in the Easy!Appointments, so add the event now.

            $local_event = [
                'start_datetime' => $caldav_event['start_datetime'],
                'end_datetime' => $caldav_event['end_datetime'],
                'location' => $caldav_event['location'],
                'notes' => $caldav_event['summary'] . ' ' . $caldav_event['description'],
                'id_users_provider' => $provider_id,
                'id_google_calendar' => $caldav_event['id'],
            ];

            $CI->unavailabilities_model->save($local_event);
        }

        json_response([
            'success' => true,
        ]);
    }

    /**
     * Get CalDAV Calendars
     *
     * This method will return a list of the available CalDAV Calendars.
     *
     * @return void
     */
    public function get_caldav_calendars(): void
    {
        try {
            $provider_id = (int) request('provider_id');

            $user_id = session('user_id');

            if (cannot('edit', PRIV_USERS) && (int) $user_id !== (int) $provider_id) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $calendars = $this->caldav_sync->get_caldav_calendars($provider_id);

            json_response($calendars);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Select a specific caldav calendar for a provider.
     *
     * All the appointments will be synced with this particular calendar.
     *
     * @return void
     */
    public function select_caldav_calendar(): void
    {
        try {
            $provider_id = (int) request('provider_id');

            $user_id = session('user_id');

            if (cannot('edit', PRIV_USERS) && (int) $user_id !== (int) $provider_id) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $calendar_id = request('calendar_id');

            $this->providers_model->set_setting($provider_id, 'caldav_calendar', $calendar_id);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Disable a providers sync setting.
     *
     * This method resets the CalDAV related settings from the user_settings DB table.
     *
     * @return void
     */
    public function disable_provider_sync(): void
    {
        try {
            $provider_id = request('provider_id');

            if (!$provider_id) {
                throw new Exception('Provider id not specified.');
            }

            $user_id = session('user_id');

            if (cannot('edit', PRIV_USERS) && (int) $user_id !== (int) $provider_id) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $provider = $this->providers_model->find($provider_id);

            $provider['settings']['caldav_sync'] = false;
            $provider['settings']['caldav_url'] = null;
            $provider['settings']['caldav_username'] = null;
            $provider['settings']['caldav_password'] = null;
            $provider['settings']['caldav_calendar'] = null;

            $this->providers_model->save($provider);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function test(): void
    {
        $appointment = $this->appointments_model->find(5);
        $service = $this->services_model->find($appointment['id_services']);
        $provider = $this->providers_model->find($appointment['id_users_provider']);
        $customer = $this->customers_model->find($appointment['id_users_customer']);

        // Save example

        // $caldav_event_id = $this->caldav_sync->save_appointment($appointment, $service, $provider, $customer);
        // dd($caldav_event_id);

        // Delete example

        // $this->caldav_sync->delete_appointment($provider, $appointment['id_caldav_calendar']);

        // Get event example

        // $event = $this->caldav_sync->get_event($provider, $appointment['id_caldav_calendar']);
        // dd($event);

        // Get sync events example

        // $events = $this->caldav_sync->get_sync_events($provider, '2024-05-06 00:00:00', '2024-05-13 00:00:00');
        // dd($events);

        // Save unavailability

        // $unavailability = $this->unavailabilities_model->find(2);
        // $unavailability_provider = $this->providers_model->find($unavailability['id_users_provider']);
        //
        // $this->caldav_sync->save_unavailability($unavailability, $unavailability_provider);

        self::sync(2); // Sync jane
    }
}
