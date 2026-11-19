<?php

namespace Tests\Unit\Library;

use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\ConferenceRequestStatus;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\EntryPoint;
use Google\Service\Calendar\Event;
use Google_Service_Calendar;
use Google_sync;
use ReflectionClass;
use Tests\TestCase;

class GoogleSyncTest extends TestCase
{
    private function event(?ConferenceData $conference_data): Event
    {
        $event = new Event();
        $event->setId('event-id');
        if ($conference_data) {
            $event->setConferenceData($conference_data);
        }

        return $event;
    }

    private function conference_data(?string $uri, ?string $status_code): ConferenceData
    {
        $conference_data = new ConferenceData();

        if ($uri) {
            $entry_point = new EntryPoint();
            $entry_point->setEntryPointType('video');
            $entry_point->setUri($uri);
            $conference_data->setEntryPoints([$entry_point]);
        }

        if ($status_code) {
            $status = new ConferenceRequestStatus();
            $status->setStatusCode($status_code);
            $create_request = new CreateConferenceRequest();
            $create_request->setStatus($status);
            $conference_data->setCreateRequest($create_request);
        }

        return $conference_data;
    }

    /**
     * @param Event[] $events Events to be returned by the consecutive Google Calendar fetches.
     */
    private function get_meeting_link(Event $event, array $events = []): ?string
    {
        require_once APPPATH . 'libraries/Google_sync.php';

        // The constructor only loads CodeIgniter dependencies that this check does not need.
        $google_sync = (new ReflectionClass(Google_sync::class))->newInstanceWithoutConstructor();

        $service = (new ReflectionClass(Google_Service_Calendar::class))->newInstanceWithoutConstructor();

        $service->events = new class ($events) {
            public function __construct(private array $events) {}

            public function get($calendar_id, $event_id, $params = []): Event
            {
                return array_shift($this->events);
            }
        };

        $service_property = (new ReflectionClass(Google_sync::class))->getProperty('service');
        $service_property->setAccessible(true);
        $service_property->setValue($google_sync, $service);

        $method = (new ReflectionClass(Google_sync::class))->getMethod('get_meeting_link');
        $method->setAccessible(true);

        return $method->invoke($google_sync, $event, 'calendar-id');
    }

    public function testMeetingLinkIsReadFromTheResponse()
    {
        $event = $this->event($this->conference_data('https://meet.google.com/abc-defg-hij', 'success'));

        $this->assertEquals('https://meet.google.com/abc-defg-hij', $this->get_meeting_link($event));
    }

    public function testPendingConferenceIsFetchedAgain()
    {
        $event = $this->event($this->conference_data(null, 'pending'));

        $resolved_event = $this->event($this->conference_data('https://meet.google.com/abc-defg-hij', 'success'));

        $this->assertEquals('https://meet.google.com/abc-defg-hij', $this->get_meeting_link($event, [$resolved_event]));
    }

    public function testMissingConferenceIsNotFetchedAgain()
    {
        $this->assertNull($this->get_meeting_link($this->event(null)));
    }

    private function get_client_value(string $method): string
    {
        require_once APPPATH . 'libraries/Google_sync.php';

        $google_sync = (new ReflectionClass(Google_sync::class))->newInstanceWithoutConstructor();

        return (new ReflectionClass(Google_sync::class))->getMethod($method)->invoke($google_sync);
    }

    public function testClientCredentialsFromConfigTakePrecedenceOverTheStoredSettings(): void
    {
        $GLOBALS['ea_test_config'] = ['google_client_id' => 'config-id', 'google_client_secret' => 'config-secret'];
        $GLOBALS['ea_test_settings'] = ['google_client_id' => 'saved-id', 'google_client_secret' => 'saved-secret'];

        try {
            $this->assertSame('config-id', $this->get_client_value('get_client_id'));
            $this->assertSame('config-secret', $this->get_client_value('get_client_secret'));
        } finally {
            $GLOBALS['ea_test_config'] = [];
            $GLOBALS['ea_test_settings'] = [];
        }
    }

    public function testClientCredentialsFallBackToTheStoredSettingsWhenConfigIsEmpty(): void
    {
        $GLOBALS['ea_test_config'] = ['google_client_id' => '', 'google_client_secret' => ''];
        $GLOBALS['ea_test_settings'] = ['google_client_id' => 'saved-id', 'google_client_secret' => 'saved-secret'];

        try {
            $this->assertSame('saved-id', $this->get_client_value('get_client_id'));
            $this->assertSame('saved-secret', $this->get_client_value('get_client_secret'));
        } finally {
            $GLOBALS['ea_test_config'] = [];
            $GLOBALS['ea_test_settings'] = [];
        }
    }
}
