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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Jsvrcek\ICS\Exception\CalendarEventException;
use Psr\Http\Message\ResponseInterface;
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
     *
     * @throws Exception If there is an issue with the initialization.
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
     * @throws CalendarEventException If there's an issue generating the ICS file.
     */
    public function save_appointment(array $appointment, array $service, array $provider, array $customer): ?string
    {
        try {
            $ics_file = $this->get_appointment_ics_file($appointment, $service, $provider, $customer);

            $client = $this->get_http_client_by_provider_id($provider['id']);

            $caldav_event_id =
                $appointment['id_caldav_calendar'] ?: $this->CI->ics_file->generate_uid($appointment['id']);

            $uri = $this->get_caldav_event_uri($provider['settings']['caldav_url'], $caldav_event_id);

            $client->request('PUT', $uri, [
                'headers' => [
                    'Content-Type' => 'text/calendar',
                ],
                'body' => $ics_file,
            ]);

            return $caldav_event_id;
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to save CalDAV appointment event');
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
     * @throws CalendarEventException If there's an issue generating the ICS file.
     */
    public function save_unavailability(array $unavailability, array $provider): ?string
    {
        try {
            if (str_contains((string) $unavailability['id_caldav_calendar'], 'RECURRENCE')) {
                return $unavailability['id_caldav_calendar'] ?? null; // Do not sync recurring unavailabilities
            }

            $ics_file = $this->get_unavailability_ics_file($unavailability, $provider);

            $client = $this->get_http_client_by_provider_id($provider['id']);

            $caldav_event_id =
                $unavailability['id_caldav_calendar'] ?: $this->CI->ics_file->generate_uid($unavailability['id']);

            $uri = $this->get_caldav_event_uri($provider['settings']['caldav_url'], $caldav_event_id);

            $client->request('PUT', $uri, [
                'headers' => [
                    'Content-Type' => 'text/calendar',
                ],
                'body' => $ics_file,
            ]);

            return $caldav_event_id;
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to save CalDAV unavailability event');
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

            $uri = $this->get_caldav_event_uri($provider['settings']['caldav_url'], $caldav_event_id);

            $client->request('DELETE', $uri);
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to delete CalDAV event');
        }
    }

    /**
     * Get a Caldav Calendar event.
     *
     * @param array $provider Provider Data.
     * @param string $caldav_event_id CalDAV calendar event ID.
     *
     * @return array|null
     * @throws Exception If there’s an issue parsing the ICS data.
     */
    public function get_event(array $provider, string $caldav_event_id): ?array
    {
        try {
            $client = $this->get_http_client_by_provider_id($provider['id']);

            $provider_timezone_object = new DateTimeZone($provider['timezone']);

            $uri = $this->get_caldav_event_uri($provider['settings']['caldav_url'], $caldav_event_id);

            $response = $client->request('GET', $uri);

            $ics_file = $response->getBody()->getContents();

            $vcalendar = Reader::read($ics_file);

            return $this->convert_caldav_event_to_array_event($vcalendar->VEVENT, $provider_timezone_object);
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to get CalDAV event');
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
     * @throws Exception If there's an issue with event fetching or parsing.
     */
    public function get_sync_events(array $provider, string $start_date_time, string $end_date_time): array
    {
        try {
            $client = $this->get_http_client_by_provider_id($provider['id']);
            $provider_timezone_object = new DateTimeZone($provider['timezone']);

            $response = $this->fetch_events($client, $start_date_time, $end_date_time);

            if (!$response->getBody()) {
                log_message('error', 'No response body from fetch_events' . PHP_EOL);
                return [];
            }

            $xml = new SimpleXMLElement($response->getBody(), 0, false, 'd', true);

            if ($xml->children('d', true)) {
                return $this->parse_xml_events($xml, $start_date_time, $end_date_time, $provider_timezone_object);
            }

            $ics_file_urls = $this->extract_ics_file_urls($response->getBody());
            return $this->fetch_and_parse_ics_files(
                $client,
                $ics_file_urls,
                $start_date_time,
                $end_date_time,
                $provider_timezone_object,
            );
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to get CalDAV sync events');
            return [];
        }
    }

    private function parse_xml_events(
        SimpleXMLElement $xml,
        string $start_date_time,
        string $end_date_time,
        DateTimeZone $timezone,
    ): array {
        $events = [];

        foreach ($xml->children('d', true) as $response) {
            $ics_contents = (string) $response->propstat->prop->children('cal', true);

            $events = array_merge(
                $events,
                $this->expand_ics_content($ics_contents, $start_date_time, $end_date_time, $timezone),
            );
        }

        return $events;
    }

    private function extract_ics_file_urls(string $body): array
    {
        $ics_files = [];
        $lines = explode("\n", $body);
        foreach ($lines as $line) {
            if (preg_match('/\/calendars\/.*?\.ics/', $line, $matches)) {
                $ics_files[] = $matches[0];
            }
        }
        return $ics_files;
    }

    /**
     * Fetch and parse the ICS files from the remote server
     *
     * @param Client $client
     * @param array $ics_file_urls
     * @param string $start_date_time
     * @param string $end_date_time
     * @param DateTimeZone $timezone_OBJECT
     *
     * @return array
     */
    private function fetch_and_parse_ics_files(
        Client $client,
        array $ics_file_urls,
        string $start_date_time,
        string $end_date_time,
        DateTimeZone $timezone_OBJECT,
    ): array {
        $events = [];

        foreach ($ics_file_urls as $ics_file_url) {
            try {
                $ics_response = $client->request('GET', $ics_file_url);

                $ics_contents = $ics_response->getBody()->getContents();

                if (empty($ics_contents)) {
                    log_message('error', 'ICS file data is empty for URL: ' . $ics_file_url . PHP_EOL);
                    continue;
                }

                $events = array_merge(
                    $events,
                    $this->expand_ics_content($ics_contents, $start_date_time, $end_date_time, $timezone_OBJECT),
                );
            } catch (GuzzleException $e) {
                log_message(
                    'error',
                    'Failed to fetch ICS content from ' . $ics_file_url . ': ' . $e->getMessage() . PHP_EOL,
                );
            }
        }

        return $events;
    }

    private function expand_ics_content(
        string $ics_contents,
        string $start_date_time,
        string $end_date_time,
        DateTimeZone $timezone_object,
    ): array {
        $events = [];

        try {
            $vcalendar = Reader::read($ics_contents);

            $expanded_vcalendar = $vcalendar->expand(new DateTime($start_date_time), new DateTime($end_date_time));

            foreach ($expanded_vcalendar->VEVENT as $event) {
                $events[] = $this->convert_caldav_event_to_array_event($event, $timezone_object);
            }
        } catch (Throwable $e) {
            log_message('error', 'Failed to parse or expand calendar data: ' . $e->getMessage() . PHP_EOL);
        }

        return $events;
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

    /**
     * @throws Exception If there is an invalid CalDAV URL or credentials.
     * @throws GuzzleException If there’s an issue with the HTTP request.
     */
    private function get_http_client(string $caldav_url, string $caldav_username, string $caldav_password): Client
    {
        if (!filter_var($caldav_url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid CalDAV URL provided: ' . $caldav_url);
        }

        if (!$caldav_username) {
            throw new InvalidArgumentException('Missing CalDAV username');
        }

        if (!$caldav_password) {
            throw new InvalidArgumentException('Missing CalDAV password');
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

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function test_connection(string $caldav_url, string $caldav_username, string $caldav_password): void
    {
        try {
            // Fetch some events to see if the connection is valid
            $client = $this->get_http_client($caldav_url, $caldav_username, $caldav_password);

            $start_date_time = date('Y-m-d 00:00:00');
            $end_date_time = date('Y-m-d 23:59:59');

            $this->fetch_events($client, $start_date_time, $end_date_time);
        } catch (GuzzleException $e) {
            $this->handle_guzzle_exception($e, 'Failed to test CalDAV connection');
            throw $e;
        }
    }

    /**
     * @throws GuzzleException
     */
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
        return $caldav_event_id ? rtrim($caldav_calendar, '/') . '/' . $caldav_event_id . '.ics' : '';
    }

    /**
     * @throws CalendarEventException
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
     * @throws CalendarEventException
     */
    private function get_unavailability_ics_file(array $unavailability, array $provider): string
    {
        $ics_file = $this->CI->ics_file->get_unavailability_stream($unavailability, $provider);

        return str_replace('METHOD:PUBLISH', '', $ics_file);
    }

    /**
     * Try to parse the CalDAV event date-time value with the right timezone.
     *
     * @throws DateMalformedStringException
     * @throws DateInvalidTimeZoneException
     */
    private function parse_date_time_object(string $caldav_date_time, DateTimeZone $default_timezone_object): DateTime
    {
        try {
            if (str_contains($caldav_date_time, 'TZID=')) {
                // Extract the TZID and use it
                preg_match('/TZID=([^:]+):/', $caldav_date_time, $matches);
                $parsed_timezone = $matches[1];
                $parsed_timezone_object = new DateTimeZone($parsed_timezone);
                $date_time = preg_replace('/TZID=[^:]+:/', '', $caldav_date_time);
                $date_time_object = new DateTime($date_time, $parsed_timezone_object);
            } elseif (str_ends_with($caldav_date_time, 'Z')) {
                // Handle UTC timestamps
                $date_time_object = new DateTime($caldav_date_time, new DateTimeZone('UTC'));
            } else {
                // Default to the provided timezone
                $date_time_object = new DateTime($caldav_date_time, $default_timezone_object);
            }

            return $date_time_object;
        } catch (Throwable $e) {
            error_log('Error parsing date-time value (' . $caldav_date_time . ') with timezone: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Convert the VEvent object to an associative array
     *
     * @link https://sabre.io/vobject/icalendar
     *
     * @param VEvent $vevent Holds the VEVENT information
     * @param DateTimeZone $timezone_object The date timezone values
     *
     * @return array
     *
     * @throws Throwable
     */
    private function convert_caldav_event_to_array_event(VEvent $vevent, DateTimeZone $timezone_object): array
    {
        try {
            $caldav_start_date_time = (string) $vevent->DTSTART;
            $start_date_time_object = $this->parse_date_time_object($caldav_start_date_time, $timezone_object);
            $start_date_time_object->setTimezone($timezone_object); // Convert to the provider timezone

            $caldav_end_date_time = (string) $vevent->DTEND;
            $end_date_time_object = $this->parse_date_time_object($caldav_end_date_time, $timezone_object);
            $end_date_time_object->setTimezone($timezone_object); // Convert to the provider timezone

            // Check if the event is recurring

            $is_recurring_event =
                isset($vevent->RRULE) ||
                isset($vevent->RDATE) ||
                isset($vevent->{'RECURRENCE-ID'}) ||
                isset($vevent->EXDATE);

            // Generate ID based on recurrence status

            $event_id = (string) $vevent->UID;

            if ($is_recurring_event) {
                $event_id .= '-RECURRENCE-' . random_string();
            }

            // Return the converted event

            return [
                'id' => $event_id,
                'summary' => (string) $vevent->SUMMARY ?? null ?: '',
                'start_datetime' => $start_date_time_object->format('Y-m-d H:i:s'),
                'end_datetime' => $end_date_time_object->format('Y-m-d H:i:s'),
                'description' => (string) $vevent->DESCRIPTION ?? null ?: '',
                'status' => (string) $vevent->STATUS ?? null ?: 'CONFIRMED',
                'location' => (string) $vevent->LOCATION ?? null ?: '',
            ];
        } catch (Throwable $e) {
            error_log('Error parsing CalDAV event object (' . var_export($vevent, true) . '): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    private function fetch_events(Client $client, string $start_date_time, string $end_date_time): ResponseInterface
    {
        $start_date_time_object = new DateTime($start_date_time);
        $formatted_start_date_time = $start_date_time_object->format('Ymd\THis\Z');
        $end_date_time_object = new DateTime($end_date_time);
        $formatted_end_date_time = $end_date_time_object->format('Ymd\THis\Z');

        return $client->request('REPORT', '', [
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
    }
}
