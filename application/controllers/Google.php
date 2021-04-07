<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Google Controller
 *
 * This controller handles the Google Calendar synchronization operations.
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
        $this->load->model('providers_model');
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
            $CI = get_instance();

            // The user must be logged in.
            if ($CI->session->userdata('user_id') == FALSE && is_cli() === FALSE)
            {
                return;
            }

            if ($provider_id === NULL)
            {
                throw new Exception('Provider id not specified.');
            }

            $CI->load->model('appointments_model');
            $CI->load->model('providers_model');
            $CI->load->model('services_model');
            $CI->load->model('customers_model');
            $CI->load->model('settings_model');

            $provider = $CI->providers_model->get_row($provider_id);

            // Check whether the selected provider has google sync enabled.
            $google_sync = $CI->providers_model->get_setting('google_sync', $provider['id']);

            if ( ! $google_sync)
            {
                throw new Exception('The selected provider has not the google synchronization setting enabled.');
            }

            $google_token = json_decode($CI->providers_model->get_setting('google_token', $provider['id']));
            $CI->load->library('google_sync');
            $CI->google_sync->refresh_token($google_token->refresh_token);

            // Fetch provider's appointments that belong to the sync time period.
            $sync_past_days = $CI->providers_model->get_setting('sync_past_days', $provider['id']);
            $sync_future_days = $CI->providers_model->get_setting('sync_future_days', $provider['id']);
            $start = strtotime('-' . $sync_past_days . ' days', strtotime(date('Y-m-d')));
            $end = strtotime('+' . $sync_future_days . ' days', strtotime(date('Y-m-d')));

            $where_clause = [
                'start_datetime >=' => date('Y-m-d H:i:s', $start),
                'end_datetime <=' => date('Y-m-d H:i:s', $end),
                'id_users_provider' => $provider['id']
            ];

            $appointments = $CI->appointments_model->get_batch($where_clause);

            $company_settings = [
                'company_name' => $CI->settings_model->get_setting('company_name'),
                'company_link' => $CI->settings_model->get_setting('company_link'),
                'company_email' => $CI->settings_model->get_setting('company_email')
            ];

            $provider_timezone = new DateTimeZone($provider['timezone']);

            // Sync each appointment with Google Calendar by following the project's sync protocol (see documentation).
            foreach ($appointments as $appointment)
            {
                if ($appointment['is_unavailable'] == FALSE)
                {
                    $service = $CI->services_model->get_row($appointment['id_services']);
                    $customer = $CI->customers_model->get_row($appointment['id_users_customer']);
                }
                else
                {
                    $service = NULL;
                    $customer = NULL;
                }

                // If current appointment not synced yet, add to Google Calendar.
                if ($appointment['id_google_calendar'] == NULL)
                {
                    $google_event = $CI->google_sync->add_appointment($appointment, $provider,
                        $service, $customer, $company_settings);
                    $appointment['id_google_calendar'] = $google_event->id;
                    $CI->appointments_model->add($appointment); // Save the Google Calendar ID.
                }
                else
                {
                    // Appointment is synced with google calendar.
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
                        $appt_start = strtotime($appointment['start_datetime']);
                        $appt_end = strtotime($appointment['end_datetime']);
                        $event_start = new DateTime($google_event->getStart()->getDateTime());
                        $event_start->setTimezone($provider_timezone);
                        $event_end = new DateTime($google_event->getEnd()->getDateTime());
                        $event_end->setTimezone($provider_timezone);


                        if ($appt_start != $event_start->getTimestamp() || $appt_end != $event_end->getTimestamp()
                            || $appointment['notes'] !== $google_event->getDescription())
                        {
                            $is_different = TRUE;
                        }

                        if ($is_different)
                        {
                            $appointment['start_datetime'] = $event_start->format('Y-m-d H:i:s');
                            $appointment['end_datetime'] = $event_end->format('Y-m-d H:i:s');
                            $appointment['notes'] = $google_event->getDescription();
                            $CI->appointments_model->add($appointment);
                        }

                    }
                    catch (Exception $exception)
                    {
                        // Appointment not found on Google Calendar, delete from Easy!Appoinmtents.
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

                $results = $CI->appointments_model->get_batch(['id_google_calendar' => $google_event->getId()]);

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

                $CI->appointments_model->add($appointment);
            }

            $response = AJAX_SUCCESS;
        }
        catch (Exception $exception)
        {
            $CI->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $CI->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Authorize Google Calendar API usage for a specific provider.
     *
     * Since it is required to follow the web application flow, in order to retrieve a refresh token from the Google API
     * service, this method is going to authorize the given provider.
     *
     * @param int $provider_id The provider id, for whom the sync authorization is made.
     */
    public function oauth($provider_id)
    {
        // Store the provider id for use on the callback function.
        $this->session->set_userdata('oauth_provider_id', $provider_id);

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
        $code = $this->input->get('code');

        if (empty($code))
        {
            $this->output->set_output('Code authorization failed.');
            return;
        }

        $token = $this->google_sync->authenticate($code);

        if (empty($token))
        {
            $this->output->set_output('Token authorization failed.');
            return;
        }

        // Store the token into the database for future reference.
        $oauth_provider_id = $this->session->userdata('oauth_provider_id');

        if ($oauth_provider_id)
        {
            $this->providers_model->set_setting('google_sync', TRUE, $oauth_provider_id);
            $this->providers_model->set_setting('google_token', json_encode($token), $oauth_provider_id);
            $this->providers_model->set_setting('google_calendar', 'primary', $oauth_provider_id);
        }
        else
        {
            $this->output->set_output('Sync provider id not specified.');
        }
    }


}
