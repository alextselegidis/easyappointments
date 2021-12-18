<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
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
class Google extends EA_Controller {
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
     *
     * @param int $provider_id Provider record to be synced.
     */
    public static function sync($provider_id = NULL)
    {
        try
        {
            /** @var EA_Controller $CI */
            $CI = get_instance();

            $CI->load->library('google_sync');

            $CI->load->model('appointments_model');
            $CI->load->model('providers_model');
            $CI->load->model('services_model');
            $CI->load->model('customers_model');
            $CI->load->model('settings_model');

            $user_id = session('user_id');

            if ( ! $user_id && ! is_cli())
            {
                return;
            }

            if ( ! $provider_id)
            {
                throw new InvalidArgumentException('No provider ID provided.');
            }

            $provider = $CI->providers_model->find($provider_id);

            // Check whether the selected provider has the Google Sync enabled.
            $google_sync = $CI->providers_model->get_setting($provider['id'], 'google_sync');

            if ( ! $google_sync)
            {
                throw new Exception('The selected provider has not the google synchronization setting enabled.');
            }

            $google_token = json_decode($CI->providers_model->get_setting($provider['id'], 'google_token'));


            $CI->google_sync->refresh_token($google_token->refresh_token);

            // Fetch provider's appointments that belong to the sync time period.
            $sync_past_days = $CI->providers_model->get_setting($provider['id'], 'sync_past_days');

            $sync_future_days = $CI->providers_model->get_setting($provider['id'], 'sync_future_days');

            $start = strtotime('-' . $sync_past_days . ' days', strtotime(date('Y-m-d')));

            $end = strtotime('+' . $sync_future_days . ' days', strtotime(date('Y-m-d')));

            $where = [
                'start_datetime >=' => date('Y-m-d H:i:s', $start),
                'end_datetime <=' => date('Y-m-d H:i:s', $end),
                'id_users_provider' => $provider['id']
            ];

            $appointments = $CI->appointments_model->get($where);

            $settings = [
                'company_name' => setting('company_name'),
                'company_link' => setting('company_link'),
                'company_email' => setting('company_email')
            ];

            $provider_timezone = new DateTimeZone($provider['timezone']);

            // Sync each appointment with Google Calendar by following the project's sync protocol (see documentation).
            foreach ($appointments as $appointment)
            {
                if ($appointment['is_unavailable'] == FALSE)
                {
                    $service = $CI->services_model->find($appointment['id_services']);
                    $customer = $CI->customers_model->find($appointment['id_users_customer']);
                }
                else
                {
                    $service = NULL;
                    $customer = NULL;
                }

                // If current appointment not synced yet, add to Google Calendar.
                if ($appointment['id_google_calendar'] == NULL)
                {
                    $google_event = $CI->google_sync->add_appointment($appointment, $provider, $service, $customer, $settings);

                    $appointment['id_google_calendar'] = $google_event->id;

                    $CI->appointments_model->save($appointment); // Save the Google Calendar ID.
                }
                else
                {
                    // Appointment is synced with Google Calendar.
                    try
                    {
                        $google_event = $CI->google_sync->get_event($provider, $appointment['id_google_calendar']);

                        if ($google_event->status == 'cancelled')
                        {
                            throw new Exception('Event is cancelled, remove the record from Easy!Appointments.');
                        }

                        // If Google Calendar event is different from Easy!Appointments appointment then update
                        // Easy!Appointments record.
                        $is_different = FALSE;
                        $appointment_start = strtotime($appointment['start_datetime']);
                        $appointment_end = strtotime($appointment['end_datetime']);
                        $event_start = new DateTime($google_event->getStart()->getDateTime());
                        $event_start->setTimezone($provider_timezone);
                        $event_end = new DateTime($google_event->getEnd()->getDateTime());
                        $event_end->setTimezone($provider_timezone);

                        $is_different = $appointment_start !== $event_start->getTimestamp()
                            || $appointment_end !== $event_end->getTimestamp()
                            || $appointment['notes'] !== $google_event->getDescription();

                        if ($is_different)
                        {
                            $appointment['start_datetime'] = $event_start->format('Y-m-d H:i:s');
                            $appointment['end_datetime'] = $event_end->format('Y-m-d H:i:s');
                            $appointment['notes'] = $google_event->getDescription();
                            $CI->appointments_model->save($appointment);
                        }

                    }
                    catch (Throwable $e)
                    {
                        // Appointment not found on Google Calendar, delete from Easy!Appointments.
                        $CI->appointments_model->delete($appointment['id']);

                        $appointment['id_google_calendar'] = NULL;
                    }
                }
            }

            // Add Google Calendar events that do not exist in Easy!Appointments.
            $google_calendar = $provider['settings']['google_calendar'];

            $google_events = $CI->google_sync->get_sync_events($google_calendar, $start, $end);

            foreach ($google_events->getItems() as $google_event)
            {
                if ($google_event->getStatus() === 'cancelled')
                {
                    continue;
                }

                if ($google_event->getStart() === NULL || $google_event->getEnd() === NULL)
                {
                    continue;
                }

                if ($google_event->getStart()->getDateTime() === $google_event->getEnd()->getDateTime())
                {
                    $event_start = new DateTime($google_event->getStart()->getDate());
                    $event_start->setTimezone($provider_timezone);
                    $event_end = new DateTime($google_event->getEnd()->getDate());
                    $event_end->setTimezone($provider_timezone);
                }
                else
                {
                    $event_start = new DateTime($google_event->getStart()->getDateTime());
                    $event_start->setTimezone($provider_timezone);
                    $event_end = new DateTime($google_event->getEnd()->getDateTime());
                    $event_end->setTimezone($provider_timezone);
                }

                $results = $CI->appointments_model->get(['id_google_calendar' => $google_event->getId()]);

                if ( ! empty($results))
                {
                    continue;
                }

                // Record doesn't exist in the Easy!Appointments, so add the event now.
                $appointment = [
                    'start_datetime' => $event_start->format('Y-m-d H:i:s'),
                    'end_datetime' => $event_end->format('Y-m-d H:i:s'),
                    'is_unavailable' => TRUE,
                    'location' => $google_event->getLocation(),
                    'notes' => $google_event->getSummary() . ' ' . $google_event->getDescription(),
                    'id_users_provider' => $provider_id,
                    'id_google_calendar' => $google_event->getId(),
                    'id_users_customer' => NULL,
                    'id_services' => NULL,
                ];

                $CI->appointments_model->save($appointment);
            }

            json_response([
                'success' => TRUE
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Authorize Google Calendar API usage for a specific provider.
     *
     * Since it is required to follow the web application flow, in order to retrieve a refresh token from the Google API
     * service, this method is going to authorize the given provider.
     *
     * @param int $provider_id The provider id, for whom the sync authorization is made.
     */
    public function oauth(int $provider_id)
    {
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
     * api console account, the "http://path-to-Easy!Appointments/google/oauth_callback" should be included in an allowed redirect URL.
     */
    public function oauth_callback()
    {
        $code = request('code');

        if (empty($code))
        {
            response('Code authorization failed.');

            return;
        }

        $token = $this->google_sync->authenticate($code);

        if (empty($token))
        {
            response('Token authorization failed.');

            return;
        }

        // Store the token into the database for future reference.
        $oauth_provider_id = session('oauth_provider_id');

        if ($oauth_provider_id)
        {
            $this->providers_model->set_setting($oauth_provider_id, 'google_sync', TRUE);
            $this->providers_model->set_setting($oauth_provider_id, 'google_token', json_encode($token));
            $this->providers_model->set_setting($oauth_provider_id, 'google_calendar', 'primary');
        }
        else
        {
            response('Sync provider id not specified.');
        }
    }

    /**
     * This method will return a list of the available Google Calendars.
     *
     * The user will need to select a specific calendar from this list to sync his appointments with. Google access must
     * be already granted for the specific provider.
     */
    public function ajax_get_google_calendars()
    {
        try
        {
            if ( ! request('provider_id'))
            {
                throw new Exception('Provider id is required in order to fetch the google calendars.');
            }

            // Check if selected provider has sync enabled.
            $provider_id = request('provider_id');

            $google_sync = $this->providers_model->get_setting($provider_id, 'google_sync');

            if ( ! $google_sync)
            {
                json_response([
                    'success' => FALSE
                ]);

                return;
            }

            $google_token = json_decode($this->providers_model->get_setting($provider_id, 'google_token'));

            $this->google_sync->refresh_token($google_token->refresh_token);

            $calendars = $this->google_sync->get_google_calendars();

            json_response($calendars);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Select a specific google calendar for a provider.
     *
     * All the appointments will be synced with this particular calendar.
     */
    public function ajax_select_google_calendar()
    {
        try
        {
            $provider_id = request('provider_id');

            $user_id = session('user_id');

            if (cannot('edit', PRIV_USERS) && (int)$user_id !== (int)$provider_id)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $calendar_id = request('calendar_id');

            $this->providers_model->set_setting($provider_id, 'google_calendar', $calendar_id);

            json_response([
                'success' => TRUE
            ]);
        }
        catch (Throwable $e)
        {
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
    public function ajax_disable_provider_sync()
    {
        try
        {
            $provider_id = request('provider_id');

            if ( ! $provider_id)
            {
                throw new Exception('Provider id not specified.');
            }

            $user_id = session('user_id');

            if (
                cannot('edit', PRIV_USERS)
                && (int)$user_id !== (int)$provider_id)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $this->providers_model->set_setting($provider_id, 'google_sync', FALSE);

            $this->providers_model->set_setting($provider_id, 'google_token', NULL);

            $this->appointments_model->clear_google_sync_ids($provider_id);

            json_response([
                'success' => TRUE,
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
