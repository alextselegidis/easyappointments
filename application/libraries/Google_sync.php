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

use Google\Service\Calendar\Event;
use Google\Service\Calendar\Events;

/**
 * Google sync library.
 *
 * Handles Google Calendar API related functionality.
 *
 * @package Libraries
 */
class Google_sync
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * @var Google_Client
     */
    protected Google_Client $client;

    /**
     * @var Google_Service_Calendar
     */
    protected Google_Service_Calendar $service;

    /**
     * Google_sync constructor.
     *
     * This method initializes the Google client class and the Calendar service class so that they can be used by the
     * other methods.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('appointments_model');
        $this->CI->load->model('customers_model');
        $this->CI->load->model('providers_model');
        $this->CI->load->model('services_model');

        $this->initialize_clients();
    }

    /**
     * Initialize the client, so that existing execution errors are not passed from one provider to another.
     */
    public function initialize_clients(): void
    {
        $http = new GuzzleHttp\Client([
            'verify' => false,
        ]);

        $this->client = new Google_Client();
        $this->client->setHttpClient($http);
        $this->client->setApplicationName('Easy!Appointments');
        $this->client->setClientId(config('google_client_id'));
        $this->client->setClientSecret(config('google_client_secret'));
        $this->client->setRedirectUri(site_url('google/oauth_callback'));
        $this->client->setPrompt('consent');
        $this->client->setAccessType('offline');
        $this->client->addScope([Google_Service_Calendar::CALENDAR]);

        $this->service = new Google_Service_Calendar($this->client);
    }

    /**
     * Get Google OAuth authorization url.
     *
     * This url must be used to redirect the user to the Google user consent page,
     * where the user grants access to his data for the Easy!Appointments app.
     */
    public function get_auth_url(): string
    {
        // The "max_auth_age" is needed because the user needs to always log in and not use an existing session.
        return $this->client->createAuthUrl() . '&max_auth_age=0';
    }

    /**
     * Authenticate the Google API usage.
     *
     * When the user grants consent for his data usage, Google is going to redirect the browser back to the given
     * redirect URL. There an authentication code is provided. Using this code, we can authenticate the API usage and
     * store the token information to the database.
     *
     * @param string $code
     *
     * @return array
     *
     * @throws Exception
     */
    public function authenticate(string $code): array
    {
        $response = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($response['error'])) {
            throw new RuntimeException(
                'Google Authentication Error (' . $response['error'] . '): ' . $response['error_description'],
            );
        }

        return $response;
    }

    /**
     * Refresh the Google Client access token.
     *
     * This method must be executed every time we need to make actions on a provider's Google Calendar account. A new
     * token is necessary and the only way to get it is to use the stored refresh token that was provided when the
     * provider granted consent to Easy!Appointments for use his Google Calendar account.
     *
     * @param string $refresh_token The provider's refresh token. This value is stored in the database and used every
     * time we need to make actions to his Google Calendar account.
     */
    public function refresh_token(string $refresh_token): void
    {
        $this->initialize_clients();

        $this->client->refreshToken($refresh_token);
    }

    /**
     * Add an appointment record to its providers Google Calendar account.
     *
     * This method checks whether the appointment's provider has enabled the Google Sync utility of Easy!Appointments
     * and the stored access token is still valid. If yes, the selected appointment record is going to be added to the
     * Google Calendar account.
     *
     * @param array $appointment Appointment data.
     * @param array $provider Provider data.
     * @param array $service Service data.
     * @param array $customer Customer data.
     * @param array $settings Required settings.
     *
     * @return Event Returns the Google_Event class object.
     *
     * @throws Exception
     */
    public function add_appointment(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings,
    ): Event {
        $event = new Google_Service_Calendar_Event();
        $event->setSummary(!empty($service) ? $service['name'] : 'Unavailable');
        $event->setDescription($appointment['notes']);
        $event->setLocation($appointment['location'] ?? $settings['company_name']);

        $timezone = new DateTimeZone($provider['timezone']);

        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime(
            (new DateTime($appointment['start_datetime'], $timezone))->format(DateTimeInterface::RFC3339),
        );
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime((new DateTime($appointment['end_datetime'], $timezone))->format(DateTimeInterface::RFC3339));
        $event->setEnd($end);

        $event->attendees = [];

        $event_provider = new Google_Service_Calendar_EventAttendee();
        $event_provider->setDisplayName($provider['first_name'] . ' ' . $provider['last_name']);
        $event_provider->setEmail($provider['email']);
        $event->attendees[] = $event_provider;

        if (!empty($customer['first_name']) && !empty($customer['last_name']) && !empty($customer['email'])) {
            $event_customer = new Google_Service_Calendar_EventAttendee();
            $event_customer->setDisplayName($customer['first_name'] . ' ' . $customer['last_name']);
            $event_customer->setEmail($customer['email']);
            $event->attendees[] = $event_customer;
        }

        // Add the new event to the Google Calendar.
        return $this->service->events->insert($provider['settings']['google_calendar'], $event);
    }

    /**
     * Update an existing appointment that is already synced with Google Calendar.
     *
     * This method updates the Google Calendar event item that is connected with the provided appointment record of
     * Easy!Appointments.
     *
     * @param array $appointment Appointment data.
     * @param array $provider Provider data.
     * @param array $service Service data.
     * @param array $customer Customer data.
     * @parma array $settings Required settings.
     *
     * @return Event Returns the Google_Service_Calendar_Event class object.
     *
     * @throws Exception
     */
    public function update_appointment(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings,
    ): Event {
        $event = $this->service->events->get(
            $provider['settings']['google_calendar'],
            $appointment['id_google_calendar'],
        );

        $event->setSummary($service['name']);
        $event->setDescription($appointment['notes']);
        $event->setLocation($appointment['location'] ?? $settings['company_name']);

        $timezone = new DateTimeZone($provider['timezone']);

        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime(
            (new DateTime($appointment['start_datetime'], $timezone))->format(DateTimeInterface::RFC3339),
        );
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime((new DateTime($appointment['end_datetime'], $timezone))->format(DateTimeInterface::RFC3339));
        $event->setEnd($end);

        $event->attendees = [];

        $event_provider = new Google_Service_Calendar_EventAttendee();
        $event_provider->setDisplayName($provider['first_name'] . ' ' . $provider['last_name']);
        $event_provider->setEmail($provider['email']);
        $event->attendees[] = $event_provider;

        if (!empty($customer['first_name']) && !empty($customer['last_name']) && !empty($customer['email'])) {
            $event_customer = new Google_Service_Calendar_EventAttendee();
            $event_customer->setDisplayName($customer['first_name'] . ' ' . $customer['last_name']);
            $event_customer->setEmail($customer['email']);
            $event->attendees[] = $event_customer;
        }

        return $this->service->events->update($provider['settings']['google_calendar'], $event->getId(), $event);
    }

    /**
     * Delete an existing appointment from Google Calendar.
     *
     * @param array $provider Provider data.
     * @param string $google_event_id The Google Calendar event ID to be removed.
     */
    public function delete_appointment(array $provider, string $google_event_id): void
    {
        $this->service->events->delete($provider['settings']['google_calendar'], $google_event_id);
    }

    /**
     * Add unavailability period event to Google Calendar.
     *
     * @param array $provider Provider data.
     * @param array $unavailability Unavailable data.
     *
     * @return Google_Service_Calendar_Event Returns the Google event.
     *
     * @throws Exception
     */
    public function add_unavailability(array $provider, array $unavailability): Google_Service_Calendar_Event
    {
        $event = new Google_Service_Calendar_Event();
        $event->setSummary('Unavailable');
        $event->setDescription($unavailability['notes']);

        $timezone = new DateTimeZone($provider['timezone']);

        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime(
            (new DateTime($unavailability['start_datetime'], $timezone))->format(DateTimeInterface::RFC3339),
        );
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime(
            (new DateTime($unavailability['end_datetime'], $timezone))->format(DateTimeInterface::RFC3339),
        );
        $event->setEnd($end);

        // Add the new event to the Google Calendar.
        return $this->service->events->insert($provider['settings']['google_calendar'], $event);
    }

    /**
     * Update Google Calendar unavailability period event.
     *
     * @param array $provider Provider data.
     * @param array $unavailability Unavailability data.
     *
     * @return Google_Service_Calendar_Event Returns the Google_Service_Calendar_Event object.
     *
     * @throws Exception
     */
    public function update_unavailability(array $provider, array $unavailability): Google_Service_Calendar_Event
    {
        $event = $this->service->events->get(
            $provider['settings']['google_calendar'],
            $unavailability['id_google_calendar'],
        );

        $event->setSummary('Unavailable');
        $event->setDescription($unavailability['notes']);

        $timezone = new DateTimeZone($provider['timezone']);

        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime(
            (new DateTime($unavailability['start_datetime'], $timezone))->format(DateTimeInterface::RFC3339),
        );
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime(
            (new DateTime($unavailability['end_datetime'], $timezone))->format(DateTimeInterface::RFC3339),
        );
        $event->setEnd($end);

        return $this->service->events->update($provider['settings']['google_calendar'], $event->getId(), $event);
    }

    /**
     * Delete unavailability period event from Google Calendar.
     *
     * @param array $provider Provider data.
     * @param string $google_event_id Google Calendar event ID to be removed.
     */
    public function delete_unavailability(array $provider, string $google_event_id): void
    {
        $this->service->events->delete($provider['settings']['google_calendar'], $google_event_id);
    }

    /**
     * Get a Google Calendar event.
     *
     * @param array $provider Provider data.
     * @param string $google_event_id Google Calendar event ID.
     *
     * @return Event Returns the Google Calendar event.
     */
    public function get_event(array $provider, string $google_event_id): Event
    {
        return $this->service->events->get($provider['settings']['google_calendar'], $google_event_id);
    }

    /**
     * Get all the events between the sync period.
     *
     * @param string $google_calendar The name of the Google Calendar to be used.
     * @param string $start The start date of sync period.
     * @param string $end The end date of sync period.
     *
     * @return Events Returns a collection of events.
     */
    public function get_sync_events(string $google_calendar, string $start, string $end): Events
    {
        $params = [
            'timeMin' => date(DateTimeInterface::RFC3339, $start),
            'timeMax' => date(DateTimeInterface::RFC3339, $end),
            'singleEvents' => true,
        ];

        return $this->service->events->listEvents($google_calendar, $params);
    }

    /**
     * Return available Google Calendars for specific user.
     *
     * The given user's token must already exist in db in order to get access to his
     * Google Calendar account.
     *
     * @return array Returns an array with the available calendars.
     */
    public function get_google_calendars(): array
    {
        $calendar_list = $this->service->calendarList->listCalendarList();

        $calendars = [];

        foreach ($calendar_list->getItems() as $google_calendar) {
            if ($google_calendar->getAccessRole() === 'reader') {
                continue;
            }

            $calendars[] = [
                'id' => $google_calendar->getId(),
                'summary' => $google_calendar->getSummary(),
            ];
        }

        return $calendars;
    }

    /**
     * Get the Add-To-Google-URL, that can be used by anyone to quickly add the event to Google Calendar (no API needed).
     *
     * @param int $appointment_id
     *
     * @return string
     *
     * @throws Exception
     */
    public function get_add_to_google_url(int $appointment_id): string
    {
        $appointment = $this->CI->appointments_model->find($appointment_id);

        $service = $this->CI->services_model->find($appointment['id_services']);

        $provider = $this->CI->providers_model->find($appointment['id_users_provider']);

        $customer = $this->CI->customers_model->find($appointment['id_users_customer']);

        $provider_timezone_instance = new DateTimeZone($provider['timezone']);

        $utc_timezone_instance = new DateTimeZone('UTC');

        $appointment_start_instance = new DateTime($appointment['start_datetime'], $provider_timezone_instance);

        $appointment_start_instance->setTimezone($utc_timezone_instance);

        $appointment_end_instance = new DateTime($appointment['end_datetime'], $provider_timezone_instance);

        $appointment_end_instance->setTimezone($utc_timezone_instance);

        $add = [$provider['email']];

        if (!empty($customer['email'])) {
            $add[] = $customer['email'];
        }

        $add_to_google_url_params = [
            'action' => 'TEMPLATE',
            'text' => $service['name'],
            'dates' =>
                $appointment_start_instance->format('Ymd\THis\Z') .
                '/' .
                $appointment_end_instance->format('Ymd\THis\Z'),
            'location' => setting('company_name'),
            'details' => 'View/Change Appointment: ' . site_url('appointments/index/' . $appointment['hash']),
            'add' => implode(', ', $add),
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($add_to_google_url_params);
    }
}
