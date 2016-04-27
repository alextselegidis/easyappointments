<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

// Google API PHP Client is necessary to perform sync operations.
require_once __DIR__ . '/external/google-api-php-client/Google_Client.php';
require_once __DIR__ . '/external/google-api-php-client/contrib/Google_CalendarService.php';

/**
 * Google Synchronization Class
 *
 * This class implements all the core synchronization between the Google Calendar
 * and the Easy!Appointments system. Do not place any model handling inside this
 * library.
 *
 * @package Libraries
 */
class Google_Sync {
    /**
     * CodeIgniter Instance
     *
     * @var CodeIgniter
     */
    protected $CI;

    /**
     * Google API Client
     *
     * @var Google_Client
     */
    protected $client;

    /**
     * Google Calendar Service
     *
     * @var Google_CalendarService
     */
    protected $service;

    /**
     * Class Constructor
     *
     * This method initializes the Google client class and the Calendar service
     * class so that they can be used by the other methods.
     */
    public function __construct() {
        $this->CI =& get_instance();

        if (!isset($_SESSION)) {
            @session_start();
        }

        // Initialize google client and calendar service.
        $this->client = new Google_Client();
        $this->client->setUseObjects(true);

        $this->client->setApplicationName(Config::GOOGLE_PRODUCT_NAME);
        $this->client->setClientId(Config::GOOGLE_CLIENT_ID);
        $this->client->setClientSecret(Config::GOOGLE_CLIENT_SECRET);
        $this->client->setDeveloperKey(Config::GOOGLE_API_KEY);
        $this->client->setRedirectUri(site_url('google/oauth_callback'));

        $this->service = new Google_CalendarService($this->client);
    }

    /**
     * Get Google OAuth authorization url.
     *
     * This url must be used to redirect the user to the Google user consent page,
     * where the user grants access to his data for the Easy!Appointments app.
     */
    public function get_auth_url() {
    	// "max_auth_age" is needed because the user needs to always log in
    	// and not use an existing session.
    	return $this->client->createAuthUrl() . '&max_auth_age=0';
    }

    /**
     * Authenticate the Google API usage.
     *
     * When the user grants consent for his data usage, google is going to redirect
     * the browser back to the given redirect url. There a authentication code is
     * provided. Using this code, we can authenticate the API usage and store the
     * token information to the database.
     *
     * @see Google Controller
     */
    public function authenticate($auth_code) {
        $this->client->authenticate($auth_code);
        return $this->client->getAccessToken();
    }

    /**
     * Refresh the Google Client access token.
     *
     * This method must be executed every time we need to make actions on a
     * provider's Google Calendar account. A new token is necessary and the
     * only way to get it is to use the stored refresh token that was provided
     * when the provider granted consent to Easy!Appointments for use his
     * Google Calendar account.
     *
     * @param string $refresh_token The provider's refresh token. This value is
     * stored in the database and used every time we need to make actions to his
     * Google Caledar account.
     */
    public function refresh_token($refresh_token) {
    	$this->client->refreshToken($refresh_token);
    }

    /**
     * Add an appointment record to its providers Google Calendar account.
     *
     * This method checks whether the appointment's provider has enabled the Google
     * Sync utility of Easy!Appointments and the stored access token is still valid.
     * If yes, the selected appointment record is going to be added to the Google
     * Calendar account.
     *
     * @param array $appointment Contains the appointment record data.
     * @param array $provider Contains the provider record data.
     * @param array $service Contains the service record data.
     * @param array $customer Contains the customer recod data.
     * @parma array $company_settings Contains some company settings that are used
     * by this method. By the time the following values must be in the array:
     * 'company_name'.
     *
     * @return Google_Event Returns the Google_Event class object.
     */
    public function add_appointment($appointment, $provider, $service, $customer, $company_settings) {
        $this->CI->load->helper('general');

        $event = new Google_Event();
        $event->setSummary(($service != NULL) ? $service['name'] : 'Unavailable');
        $event->setLocation($company_settings['company_name']);

        $start = new Google_EventDateTime();
        $start->setDateTime(date3339(strtotime($appointment['start_datetime'])));
        $event->setStart($start);

        $end = new Google_EventDateTime();
        $end->setDateTime(date3339(strtotime($appointment['end_datetime'])));
        $event->setEnd($end);

        $event->attendees = array();

        $event_provider = new Google_EventAttendee();
        $event_provider->setDisplayName($provider['first_name'] . ' '
                . $provider['last_name']);
        $event_provider->setEmail($provider['email']);
        $event->attendees[] = $event_provider;

        if ($customer != NULL) {
            $event_customer = new Google_EventAttendee();
            $event_customer->setDisplayName($customer['first_name'] . ' '
                    . $customer['last_name']);
            $event_customer->setEmail($customer['email']);
            $event->attendees[] = $event_customer;
        }

        // Add the new event to the google calendar.
        $created_event = $this->service->events->insert($provider['settings']['google_calendar'], $event);

        return $created_event;
    }

    /**
     * Update an existing appointment that is already synced with Google Calendar.
     *
     * This method updates the google calendar event item that is connected with the
     * provided appointment record of Easy!Appointments.
     *
     * @param array $appointment Contains the appointment record data.
     * @param array $provider Contains the provider record data.
     * @param array $service Contains the service record data.
     * @param array $customer Contains the customer recod data.
     * @parma array $company_settings Contains some company settings that are used
     * by this method. By the time the following values must be in the array:
     * 'company_name'.
     *
     * @return Google_Event Returns the Google_Event class object.
     */
    public function update_appointment($appointment, $provider, $service, $customer, $company_settings) {
        $this->CI->load->helper('general');

        $event = $this->service->events->get($provider['settings']['google_calendar'], $appointment['id_google_calendar']);

        $event->setSummary($service['name']);
        $event->setLocation($company_settings['company_name']);

        $start = new Google_EventDateTime();
        $start->setDateTime(date3339(strtotime($appointment['start_datetime'])));
        $event->setStart($start);

        $end = new Google_EventDateTime();
        $end->setDateTime(date3339(strtotime($appointment['end_datetime'])));
        $event->setEnd($end);

        $event->attendees = array();

        $event_provider = new Google_EventAttendee();
        $event_provider->setDisplayName($provider['first_name'] . ' '
                . $provider['last_name']);
        $event_provider->setEmail($provider['email']);
        $event->attendees[] = $event_provider;

        if ($customer != NULL) {
            $event_customer = new Google_EventAttendee();
            $event_customer->setDisplayName($customer['first_name'] . ' '
                    . $customer['last_name']);
            $event_customer->setEmail($customer['email']);
            $event->attendees[] = $event_customer;
        }

        $updated_event = $this->service->events->update($provider['settings']['google_calendar'],
                $event->getId(), $event);

        return $updated_event;
    }

    /**
     * Delete an existing appointment from Google Calendar.
     *
     * @param array $provider Contains the provider record data.
     * @param string $google_event_id The Google Calendar event id to
     * be deleted.
     */
    public function delete_appointment($provider, $google_event_id) {
        $this->service->events->delete($provider['settings']['google_calendar'], $google_event_id);
    }

    /**
     * Add unavailable period event to Google Calendar.
     *
     * @param array $provider Contains the provider record data.
     * @param array $unavailable Contains unavailable period's data.
     *
     * @return Google_Event Returns the google event's object.
     */
    public function add_unavailable($provider, $unavailable) {
        $this->CI->load->helper('general');

        $event = new Google_Event();
        $event->setSummary('Unavailable');
        $event->setDescription($unavailable['notes']);

        $start = new Google_EventDateTime();
        $start->setDateTime(date3339(strtotime($unavailable['start_datetime'])));
        $event->setStart($start);

        $end = new Google_EventDateTime();
        $end->setDateTime(date3339(strtotime($unavailable['end_datetime'])));
        $event->setEnd($end);

        // Add the new event to the google calendar.
        $created_event = $this->service->events->insert($provider['settings']['google_calendar'], $event);

        return $created_event;

    }

    /**
     * Update Google Calendar unavailable period event.
     *
     * @param array $provider Contains the provider record data.
     * @param array $unavailable Contains the unavailable period data.
     *
     * @return Google_Event Returns the Google_Event object.
     */
    public function update_unavailable($provider, $unavailable) {
        $this->CI->load->helper('general');

        $event = $this->service->events->get($provider['settings']['google_calendar'], $unavailable['id_google_calendar']);
        $event->setDescription($unavailable['notes']);

        $start = new Google_EventDateTime();
        $start->setDateTime(date3339(strtotime($unavailable['start_datetime'])));
        $event->setStart($start);

        $end = new Google_EventDateTime();
        $end->setDateTime(date3339(strtotime($unavailable['end_datetime'])));
        $event->setEnd($end);

        $updated_event = $this->service->events->update($provider['settings']['google_calendar'],
                $event->getId(), $event);

        return $updated_event;
    }

    /**
     * Delete unavailable period event from Google Calendar.
     *
     * @param array $provider Contains the provider record data.
     * @param string $google_event_id Google Calendar event id to be deleted.
     */
    public function delete_unavailable($provider, $google_event_id) {
        $this->service->events->delete($provider['settings']['google_calendar'], $google_event_id);
    }

    /**
     * Get an event object from gcal
     *
     * @param array $provider Contains the provider record data.
     * @param string $google_event_id Id of the google calendar event.
     *
     * @return Google_Event Returns the google event object.
     */
    public function get_event($provider, $google_event_id) {
        return $this->service->events->get($provider['settings']['google_calendar'], $google_event_id);
    }

    /**
     * Get all the events between the sync period.
     *
     * @param string $google_calendar The name of the google calendar to be used.
     * @param date $start The start date of sync period.
     * @param date $end The end date of sync period.
     *
     * @return object Returns an array with Google_Event objects that belong on the given
     * sync period (start, end).
     */
    public function get_sync_events($google_calendar, $start, $end) {
        $this->CI->load->helper('general');

        $params = array(
            'timeMin' => date3339($start),
            'timeMax' => date3339($end)
        );

        return $this->service->events->listEvents($google_calendar, $params);
    }

    /**
     * Return available google calendars for specific user.
     *
     * The given user's token must already exist in db in order to get access to his
     * Google Calendar account.
     *
     * @param string $google_token The user's token will be used to grant access to google calendar.
     *
     * @return array Returns an array with the available calendars.
     */
    public function get_google_calendars() {
        $calendarList = $this->service->calendarList->listCalendarList();
        $calendars = array();
        foreach ($calendarList->items as $google_calendar) {
            $calendars[] = array(
                'id' => $google_calendar->id,
                'summary' => $google_calendar->summary
            );
        }
         return $calendars;
    }
}

/* End of file google_sync.php */
/* Location: ./application/libraries/google_sync.php */
