<?php

namespace Tests\Unit\Library;

use Caldav_sync;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use ReflectionClass;
use Tests\TestCase;

class CaldavSyncTest extends TestCase
{
    private const CALENDAR_RESPONSE = '<?xml version="1.0"?>
        <d:multistatus xmlns:d="DAV:" xmlns:cal="urn:ietf:params:xml:ns:caldav">
            <d:response><d:href>/dav.php/calendars/testuser/default/</d:href><d:propstat><d:prop>
                <d:resourcetype><d:collection/><cal:calendar/></d:resourcetype>
            </d:prop><d:status>HTTP/1.1 200 OK</d:status></d:propstat></d:response>
        </d:multistatus>';

    private const COLLECTION_RESPONSE = '<?xml version="1.0"?>
        <d:multistatus xmlns:d="DAV:" xmlns:cal="urn:ietf:params:xml:ns:caldav">
            <d:response><d:href>/dav.php/calendars/testuser/</d:href><d:propstat><d:prop>
                <d:resourcetype><d:collection/></d:resourcetype>
            </d:prop><d:status>HTTP/1.1 200 OK</d:status></d:propstat></d:response>
        </d:multistatus>';

    private const PRINCIPAL_RESPONSE = '<?xml version="1.0"?>
        <d:multistatus xmlns:d="DAV:" xmlns:cal="urn:ietf:params:xml:ns:caldav">
            <d:response><d:href>/dav.php/principals/testuser/</d:href><d:propstat><d:prop>
                <d:resourcetype><d:collection/><d:principal/></d:resourcetype>
                <cal:calendar-home-set><d:href>/dav.php/calendars/testuser/</d:href></cal:calendar-home-set>
            </d:prop><d:status>HTTP/1.1 200 OK</d:status></d:propstat></d:response>
        </d:multistatus>';

    private function assert_calendar_collection(string $body): void
    {
        require_once APPPATH . 'libraries/Caldav_sync.php';

        $handler = HandlerStack::create(new MockHandler([new Response(207, [], $body)]));

        $client = new Client(['base_uri' => 'https://example.org/dav/', 'handler' => $handler]);

        // The constructor only loads CodeIgniter dependencies that this check does not need.
        $caldav_sync = (new ReflectionClass(Caldav_sync::class))->newInstanceWithoutConstructor();

        $method = (new ReflectionClass(Caldav_sync::class))->getMethod('assert_caldav_calendar_collection');
        $method->setAccessible(true);
        $method->invoke($caldav_sync, $client);
    }

    public function testCalendarCollectionIsAccepted()
    {
        $this->assert_calendar_collection(self::CALENDAR_RESPONSE);

        $this->assertTrue(true); // No exception thrown.
    }

    public function testPlainCollectionIsRejected()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->assert_calendar_collection(self::COLLECTION_RESPONSE);
    }

    public function testPrincipalWithCalendarHomeSetIsRejected()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->assert_calendar_collection(self::PRINCIPAL_RESPONSE);
    }
}
