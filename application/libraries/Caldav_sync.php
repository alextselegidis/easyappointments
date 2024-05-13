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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Sabre\VObject\Component\VEvent;
use Sabre\VObject\Reader;

/**
 * Caldav sync library.
 *
 * Handles CalDAV related operations using Guzzle.
 *
 * @package Libraries
 */
class Caldav_sync
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * Caldav_sync constructor.
     *
     * This method initializes the Caldav client class and the Calendar service class so that they can be used by the
     * other methods.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('appointments_model');
        $this->CI->load->model('customers_model');
        $this->CI->load->model('providers_model');
        $this->CI->load->model('services_model');

        $this->CI->load->library('ics_file');
    }

    /**
     * Add an appointment record to the connected CalDAV calendar.
     *
     * @param array $appointment Appointment record.
     * @param array $service Service record.
     * @param array $provider Provider record.
     * @param array $customer Customer record.
     *
     * @return string|null Returns the event ID
     *
     * @throws \Jsvrcek\ICS\Exception\CalendarEventException
     */
    public function save_appointment(array $appointment, array $service, array $provider, array $customer): ?string
    {
        try {
            $ics_file = $this->get_appointment_ics_file($appointment, $service, $provider, $customer);

            $client = $this->get_http_client_by_provider_id($provider['id']);

            $caldav_calendar = $provider['settings']['caldav_calendar'];

            $caldav_event_id =
                $appointment['id_caldav_calendar'] ?: $this->CI->ics_file->generate_uid($appointment['id']);

            $uri = $this->get_caldav_event_uri($caldav_calendar, $caldav_event_id);

            $client->request('PUT', $uri, [
                'headers' => [
                    'Content-Type' => 'text/calendar',
                ],
                'body' => $ics_file,
            ]);

            return $caldav_event_id;
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to save CalDAV event');
            return null;
        }
    }

    /**
     * Add an unavailability record to the connected CalDAV calendar.
     *
     * @param array $unavailability Appointment record.
     * @param array $provider Provider record.
     *
     * @return string|null Returns the event ID
     *
     * @throws \Jsvrcek\ICS\Exception\CalendarEventException
     */
    public function save_unavailability(array $unavailability, array $provider): ?string
    {
        try {
            $ics_file = $this->get_unavailability_ics_file($unavailability, $provider);

            $client = $this->get_http_client_by_provider_id($provider['id']);

            $caldav_calendar = $provider['settings']['caldav_calendar'];

            $caldav_event_id =
                $unavailability['id_caldav_calendar'] ?: $this->CI->ics_file->generate_uid($unavailability['id']);

            $uri = $this->get_caldav_event_uri($caldav_calendar, $caldav_event_id);

            $client->request('PUT', $uri, [
                'headers' => [
                    'Content-Type' => 'text/calendar',
                ],
                'body' => $ics_file,
            ]);

            return $caldav_event_id;
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to save CalDAV event');
            return null;
        }
    }

    /**
     * Delete an existing appointment from Caldav Calendar.
     *
     * @param array $provider Provider data.
     * @param string $caldav_event_id The Caldav Calendar event ID to be removed.
     */
    public function delete_event(array $provider, string $caldav_event_id): void
    {
        try {
            $client = $this->get_http_client_by_provider_id($provider['id']);

            $caldav_calendar = $provider['settings']['caldav_calendar'];

            $uri = $this->get_caldav_event_uri($caldav_calendar, $caldav_event_id);

            $client->request('DELETE', $uri);
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to save CalDAV event');
        }
    }

    /**
     * Get a Caldav Calendar event.
     *
     * @param array $provider Provider Data.
     * @param string $caldav_event_id CalDAV calendar event ID.
     *
     * @return array|null
     * @throws Exception
     */
    public function get_event(array $provider, string $caldav_event_id): ?array
    {
        try {
            $client = $this->get_http_client_by_provider_id($provider['id']);

            $caldav_calendar = $provider['settings']['caldav_calendar'];

            $uri = $this->get_caldav_event_uri($caldav_calendar, $caldav_event_id);

            $response = $client->request('GET', $uri);

            $ics_file = $response->getBody()->getContents();

            $vcalendar = Reader::read($ics_file);

            return $this->convert_caldav_event_to_array_event($vcalendar->VEVENT);
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to save CalDAV event');
            return null;
        }
    }

    /**
     * Get all the events between the sync period.
     *
     * @param array $provider Provider data.
     * @param string $start_date_time The start date of sync period.
     * @param string $end_date_time The end date of sync period.
     *
     * @return array
     * @throws Exception
     */
    public function get_sync_events(array $provider, string $start_date_time, string $end_date_time): array
    {
        try {
            $client = $this->get_http_client_by_provider_id($provider['id']);

            $caldav_calendar = $provider['settings']['caldav_calendar'];

            $uri = $this->get_caldav_event_uri($caldav_calendar);

            $start_date_time_object = new DateTime($start_date_time);
            $formatted_start_date_time = $start_date_time_object->format('Ymd\THis\Z');
            $end_date_time_object = new DateTime($end_date_time);
            $formatted_end_date_time = $end_date_time_object->format('Ymd\THis\Z');

            $response = $client->request('REPORT', $uri, [
                'headers' => [
                    'Content-Type' => 'application/xml',
                    'Depth' => '1',
                ],
                'body' =>
                    '
                <c:calendar-query xmlns:d="DAV:" xmlns:c="urn:ietf:params:xml:ns:caldav">
                    <d:prop>
                        <d:getetag />
                        <c:calendar-data />
                    </d:prop>
                    <c:filter>
                        <c:comp-filter name="VCALENDAR">
                            <c:comp-filter name="VEVENT">
                                <c:time-range start="' .
                    $formatted_start_date_time .
                    '" end="' .
                    $formatted_end_date_time .
                    '"/>	
                            </c:comp-filter>
                        </c:comp-filter>
                    </c:filter>
                </c:calendar-query>
            ',
            ]);

            $xml = new SimpleXMLElement($response->getBody(), 0, false, 'd', true);

            $events = [];

            foreach ($xml->children('d', true) as $response) {
                $ics_file = (string) $response->propstat->prop->children('cal', true);

                $vcalendar = Reader::read($ics_file);

                $events[] = $this->convert_caldav_event_to_array_event($vcalendar->VEVENT);
            }

            return $events;
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to save CalDAV event');
            return [];
        }
    }

    /**
     * Return available Caldav Calendars for specific user.
     *
     * The given user's token must already exist in db in order to get access to his
     * Caldav Calendar account.
     *
     * @return array Returns an array with the available calendars.
     *
     * @throws Exception
     */
    public function get_caldav_calendars(int $provider_id): array
    {
        try {
            $provider = $this->CI->providers_model->find($provider_id);

            if (!$provider['settings']['caldav_sync']) {
                throw new RuntimeException(
                    'The selected provider does not have the CalDAV sync enabled: ' . $provider_id,
                );
            }

            $caldav_url = $provider['settings']['caldav_url'];
            $caldav_username = $provider['settings']['caldav_username'];
            $caldav_password = $provider['settings']['caldav_password'];

            $client = $this->get_http_client($caldav_url, $caldav_username, $caldav_password);

            $caldav_calendars = [];

            $response = $client->request('PROPFIND', 'calendars/' . $caldav_username);

            $xml = new SimpleXMLElement($response->getBody(), 0, false, 'd', true);

            foreach ($xml->children('d', true) as $response) {
                $resource_type = $response->propstat->prop->resourcetype->children('cal', true)->getName();

                if ($resource_type !== 'calendar') {
                    continue;
                }

                $caldav_calendars[] = [
                    'id' => $this->sanitize_href($caldav_url, $response->href),
                    'summary' => (string) $response->propstat->prop->displayname,
                ];
            }

            return $caldav_calendars;
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to get the CalDAV calendars');
            return [];
        }
    }

    /**
     * Connect to the remote CalDAV server and get the default calendar URL.
     *
     * @param string $caldav_url
     * @param string $caldav_username
     * @param string $caldav_password
     *
     * @return string|null
     *
     * @throws Exception
     */
    public function get_default_calendar(string $caldav_url, string $caldav_username, string $caldav_password): ?string
    {
        try {
            $client = $this->get_http_client($caldav_url, $caldav_username, $caldav_password);

            $response = $client->request('PROPFIND', 'calendars/' . $caldav_username);

            $xml = new SimpleXMLElement($response->getBody(), 0, false, 'd', true);

            foreach ($xml->children('d', true) as $response) {
                $resource_type = $response->propstat->prop->resourcetype->children('cal', true)->getName();

                if ($resource_type === 'calendar') {
                    return $this->sanitize_href($caldav_url, $response->href); // Use this response as the default calendar
                }
            }

            return null;
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to check CalDAV credentials');
            return null;
        }
    }

    /**
     * Common error handling for the CalDAV requests.
     *
     * @param GuzzleException $e
     * @param string $message
     *
     * @return void
     */
    private function handle_guzzle_exception(GuzzleException $e, string $message): void
    {
        // Guzzle throws a RequestException for HTTP errors

        if ($e instanceof RequestException && $e->hasResponse()) {
            // Handle HTTP error response

            $response = $e->getResponse();

            $status_code = $response->getStatusCode();

            $guzzle_info = '(Status Code: ' . $status_code . '): ' . $response->getBody()->getContents() . PHP_EOL;
        } else {
            // Handle other request errors

            $guzzle_info = $e->getMessage();
        }

        log_message('error', $message . ' ' . $guzzle_info);
    }

    private function get_http_client(string $caldav_url, string $caldav_username, string $caldav_password): Client
    {
        if (!filter_var($caldav_url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid CalDAV URL provided: ' . $caldav_url);
        }

        if (!$caldav_username) {
            throw new InvalidArgumentException('Invalid CalDAV username provided: ' . $caldav_username);
        }

        if (!$caldav_password) {
            throw new InvalidArgumentException('Invalid CalDAV password provided: ' . $caldav_password);
        }

        return new Client([
            'base_uri' => rtrim($caldav_url, '/') . '/',
            'connect_timeout' => 15,
            'headers' => [
                'Content-Type' => 'text/xml',
            ],
            'auth' => [$caldav_username, $caldav_password],
        ]);
    }

    private function sanitize_href(string $caldav_url, string $href): string
    {
        $parts = parse_url($caldav_url);

        return str_replace($parts['path'], '', $href);
    }

    private function get_http_client_by_provider_id(int $provider_id): Client
    {
        $provider = $this->CI->providers_model->find($provider_id);

        if (!$provider['settings']['caldav_sync']) {
            throw new RuntimeException('The selected provider does not have the CalDAV sync enabled: ' . $provider_id);
        }

        $caldav_url = $provider['settings']['caldav_url'];
        $caldav_username = $provider['settings']['caldav_username'];
        $caldav_password = $provider['settings']['caldav_password'];

        return $this->get_http_client($caldav_url, $caldav_username, $caldav_password);
    }

    /**
     * Generate the event URI, used in various requests.
     *
     * @param string $caldav_calendar
     * @param string|null $caldav_event_id
     *
     * @return string
     */
    private function get_caldav_event_uri(string $caldav_calendar, ?string $caldav_event_id = null): string
    {
        return trim($caldav_calendar, '/') . ($caldav_event_id ? '/' . $caldav_event_id . '.ics' : '');
    }

    /**
     * @throws \Jsvrcek\ICS\Exception\CalendarEventException
     */
    private function get_appointment_ics_file(
        array $appointment,
        array $service,
        array $provider,
        array $customer,
    ): string {
        $ics_file = $this->CI->ics_file->get_stream($appointment, $service, $provider, $customer);

        return str_replace('METHOD:PUBLISH', '', $ics_file);
    }

    /**
     * @throws \Jsvrcek\ICS\Exception\CalendarEventException
     */
    private function get_unavailability_ics_file(array $unavailability, array $provider): string
    {
        $ics_file = $this->CI->ics_file->get_unavailability_stream($unavailability, $provider);

        return str_replace('METHOD:PUBLISH', '', $ics_file);
    }

    /**
     * Convert the VEvent object to an associative array
     *
     * @link https://sabre.io/vobject/icalendar
     *
     * @param VEvent $vevent Holds the VEVENT information
     *
     * @return array
     *
     * @throws Exception
     */
    private function convert_caldav_event_to_array_event(VEvent $vevent): array
    {
        $start_date_time_object = new DateTime((string) $vevent->DTSTART);
        $end_date_time_object = new DateTime((string) $vevent->DTEND);

        return [
            'id' => (string) $vevent->UID,
            'summary' => (string) $vevent->SUMMARY,
            'start_datetime' => $start_date_time_object->format('Y-m-d H:i:s'),
            'end_datetime' => $end_date_time_object->format('Y-m-d H:i:s'),
            'description' => (string) $vevent->DESCRIPTION,
            'status' => (string) $vevent->STATUS,
            'location' => (string) $vevent->LOCATION,
        ];
    }
}
