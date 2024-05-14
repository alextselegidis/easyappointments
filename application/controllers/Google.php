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
 * Google controller.
 *
 * Handles the Google Calendar synchronization related operations.
 *
 * @package Controllers
 */
class Google extends EA_Controller
{
    /**
     * Google constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('google_sync');

        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('roles_model');
    }

    /**
     * Complete synchronization of appointments between Google Calendar and Easy!Appointments.
     *
     * This method will completely sync the appointments of a provider with his Google Calendar account. The sync period
     * needs to be relatively small, because a lot of API calls might be necessary and this will lead to consuming the
     * Google limit for the Calendar API usage.
     */
    public static function sync(string $provider_id = null): void
    {
        try {
            /** @var EA_Controller $CI */
            $CI = get_instance();

            $CI->load->library('google_sync');

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

            if (!$provider_id) {
                throw new InvalidArgumentException('No provider ID provided.');
            }

            $provider = $CI->providers_model->find($provider_id);

            // Check whether the selected provider has the Google Sync enabled.
            $google_sync = $CI->providers_model->get_setting($provider['id'], 'google_sync');

            if (!$google_sync) {
                return; // The selected provider does not have the Google Sync enabled.
            }

            $google_token = json_decode($provider['settings']['google_token'], true);

            $CI->google_sync->refresh_token($google_token['refresh_token']);

            // Fetch provider's appointments that belong to the sync time period.
            $sync_past_days = $provider['settings']['sync_past_days'];

            $sync_future_days = $provider['settings']['sync_future_days'];

            $start = strtotime('-' . $sync_past_days . ' days', strtotime(date('Y-m-d')));

            $end = strtotime('+' . $sync_future_days . ' days', strtotime(date('Y-m-d')));

            $where = [
                'start_datetime >=' => date('Y-m-d H:i:s', $start),
                'end_datetime <=' => date('Y-m-d H:i:s', $end),
                'id_users_provider' => $provider['id'],
            ];

            $appointments = $CI->appointments_model->get($where);

            $unavailabilities = $CI->unavailabilities_model->get($where);

            $local_events = [...$appointments, ...$unavailabilities];

            $settings = [
                'company_name' => setting('company_name'),
                'company_link' => setting('company_link'),
                'company_email' => setting('company_email'),
            ];

            $provider_timezone = new DateTimeZone($provider['timezone']);

            // Sync each appointment with Google Calendar by following the project's sync protocol (see documentation).
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
                if (!$local_event['id_google_calendar']) {
                    if (!$local_event['is_unavailability']) {
                        $google_event = $CI->google_sync->add_appointment(
                            $local_event,
                            $provider,
                            $service,
                            $customer,
                            $settings,
                        );
                    } else {
                        $google_event = $CI->google_sync->add_unavailability($provider, $local_event);
                    }

                    $local_event['id_google_calendar'] = $google_event->getId();

                    $events_model->save($local_event); // Save the Google Calendar ID.

                    continue;
                }

                // Appointment is synced with Google Calendar.

                try {
                    $google_event = $CI->google_sync->get_event($provider, $local_event['id_google_calendar']);

                    if ($google_event->getStatus() == 'cancelled') {
                        throw new Exception('Event is cancelled, remove the record from Easy!Appointments.');
                    }

                    // If Google Calendar event is different from Easy!Appointments appointment then update Easy!Appointments record.
                    $local_event_start = strtotime($local_event['start_datetime']);
                    $local_event_end = strtotime($local_event['end_datetime']);
                    $google_event_start = new DateTime(
                        $google_event->getStart()->getDateTime() ?? $google_event->getEnd()->getDate(),
                    );
                    $google_event_start->setTimezone($provider_timezone);
                    $google_event_end = new DateTime(
                        $google_event->getEnd()->getDateTime() ?? $google_event->getEnd()->getDate(),
                    );
                    $google_event_end->setTimezone($provider_timezone);

                    $google_event_notes = $local_event['is_unavailability']
                        ? $google_event->getSummary() . ' ' . $google_event->getDescription()
                        : $google_event->getDescription();

                    $is_different =
                        $local_event_start !== $google_event_start->getTimestamp() ||
                        $local_event_end !== $google_event_end->getTimestamp() ||
                        $local_event['notes'] !== $google_event_notes;

                    if ($is_different) {
                        $local_event['start_datetime'] = $google_event_start->format('Y-m-d H:i:s');
                        $local_event['end_datetime'] = $google_event_end->format('Y-m-d H:i:s');
                        $local_event['notes'] = $google_event_notes;
                        $events_model->save($local_event);
                    }
                } catch (Throwable) {
                    // Appointment not found on Google Calendar, delete from Easy!Appointments.
                    $events_model->delete($local_event['id']);

                    $local_event['id_google_calendar'] = null;
                }
            }

            // Add Google Calendar events that do not exist in Easy!Appointments.
            $google_calendar = $provider['settings']['google_calendar'];

            try {
                $google_events = $CI->google_sync->get_sync_events($google_calendar, $start, $end);
            } catch (Throwable $e) {
                if ($e->getCode() === 404) {
                    log_message('error', 'Google - Remote Calendar not found for provider ID: ' . $provider_id);

                    return; // The remote calendar was not found.
                } else {
                    throw $e;
                }
            }

            foreach ($google_events->getItems() as $google_event) {
                if ($google_event->getStatus() === 'cancelled') {
                    continue;
                }

                if ($google_event->getStart() === null || $google_event->getEnd() === null) {
                    continue;
                }

                if ($google_event->getStart()->getDateTime() === $google_event->getEnd()->getDateTime()) {
                    continue;
                }

                $google_event_start = new DateTime($google_event->getStart()->getDateTime());
                $google_event_start->setTimezone($provider_timezone);
                $google_event_end = new DateTime($google_event->getEnd()->getDateTime());
                $google_event_end->setTimezone($provider_timezone);

                $appointment_results = $CI->appointments_model->get(['id_google_calendar' => $google_event->getId()]);

                if (!empty($appointment_results)) {
                    continue;
                }

                $unavailability_results = $CI->unavailabilities_model->get([
                    'id_google_calendar' => $google_event->getId(),
                ]);

                if (!empty($unavailability_results)) {
                    continue;
                }

                // Record doesn't exist in the Easy!Appointments, so add the event now.
                $local_event = [
                    'start_datetime' => $google_event_start->format('Y-m-d H:i:s'),
                    'end_datetime' => $google_event_end->format('Y-m-d H:i:s'),
                    'is_unavailability' => true,
                    'location' => $google_event->getLocation(),
                    'notes' => $google_event->getSummary() . ' ' . $google_event->getDescription(),
                    'id_users_provider' => $provider_id,
                    'id_google_calendar' => $google_event->getId(),
                    'id_users_customer' => null,
                    'id_services' => null,
                ];

                $CI->unavailabilities_model->save($local_event);
            }

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            log_message(
                'error',
                'Google - Sync completed with an error (provider ID "' . $provider_id . '"): ' . $e->getMessage(),
            );

            json_exception($e);
        }
    }

    /**
     * Authorize Google Calendar API usage for a specific provider.
     *
     * Since it is required to follow the web application flow, in order to retrieve a refresh token from the Google API
     * service, this method is going to authorize the given provider.
     *
     * @param string $provider_id The provider id, for whom the sync authorization is made.
     */
    public function oauth(string $provider_id): void
    {
        if (!$this->session->userdata('user_id')) {
            show_error('Forbidden', 403);
        }

        // Store the provider id for use on the callback function.
        session(['oauth_provider_id' => $provider_id]);

        // Redirect browser to google user content page.
        header('Location: ' . $this->google_sync->get_auth_url());
    }

    /**
     * Callback method for the Google Calendar API authorization process.
     *
     * Once the user grants consent with his Google Calendar data usage, the Google OAuth service will redirect him back
     * in this page. Here we are going to store the refresh token, because this is what will be used to generate access
     * tokens in the future.
     *
     * IMPORTANT: Because it is necessary to authorize the application using the web server flow (see official
     * documentation of OAuth), every Easy!Appointments installation should use its own calendar api key. So in every
     * api console account, the "http://path-to-Easy!Appointments/google/oauth_callback" should be included in an
     * allowed redirect URL.
     *
     * @throws Exception
     */
    public function oauth_callback(): void
    {
        if (!session('user_id')) {
            abort(403, 'Forbidden');
        }

        $code = request('code');

        if (empty($code)) {
            response('Code authorization failed.');

            return;
        }

        $token = $this->google_sync->authenticate($code);

        if (empty($token)) {
            response('Token authorization failed.');

            return;
        }

        // Store the token into the database for future reference.
        $oauth_provider_id = session('oauth_provider_id');

        if ($oauth_provider_id) {
            $this->providers_model->set_setting($oauth_provider_id, 'google_sync', true);
            $this->providers_model->set_setting($oauth_provider_id, 'google_token', json_encode($token));
            $this->providers_model->set_setting($oauth_provider_id, 'google_calendar', 'primary');
        } else {
            response('Sync provider id not specified.');
        }
    }

    /**
     * This method will return a list of the available Google Calendars.
     *
     * The user will need to select a specific calendar from this list to sync his appointments with. Google access must
     * be already granted for the specific provider.
     */
    public function get_google_calendars(): void
    {
        try {
            $provider_id = (int) request('provider_id');

            if (empty($provider_id)) {
                throw new Exception('Provider id is required in order to fetch the google calendars.');
            }

            // Check if selected provider has sync enabled.
            $google_sync = $this->providers_model->get_setting($provider_id, 'google_sync');

            if (!$google_sync) {
                json_response([
                    'success' => false,
                ]);

                return;
            }

            $google_token = json_decode($this->providers_model->get_setting($provider_id, 'google_token'), true);

            $this->google_sync->refresh_token($google_token['refresh_token']);

            $calendars = $this->google_sync->get_google_calendars();

            json_response($calendars);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Select a specific google calendar for a provider.
     *
     * All the appointments will be synced with this particular calendar.
     */
    public function select_google_calendar(): void
    {
        try {
            $provider_id = request('provider_id');

            $user_id = session('user_id');

            if (cannot('edit', PRIV_USERS) && (int) $user_id !== (int) $provider_id) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $calendar_id = request('calendar_id');

            $this->providers_model->set_setting($provider_id, 'google_calendar', $calendar_id);

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
     * This method deletes the "google_sync" and "google_token" settings from the database.
     *
     * After that the provider's appointments will be no longer synced with Google Calendar.
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

            $this->providers_model->set_setting($provider_id, 'google_sync', false);

            $this->providers_model->set_setting($provider_id, 'google_token');

            $this->appointments_model->clear_google_sync_ids($provider_id);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
