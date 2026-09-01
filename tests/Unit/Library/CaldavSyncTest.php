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
    protected function setUp(): void
    {
        $GLOBALS['ea_test_settings'] = [];
    }

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

    private function assert_safe_caldav_url(string $caldav_url): void
    {
        require_once APPPATH . 'libraries/Caldav_sync.php';

        // The constructor only loads CodeIgniter dependencies that this check does not need.
        $caldav_sync = (new ReflectionClass(Caldav_sync::class))->newInstanceWithoutConstructor();

        $method = (new ReflectionClass(Caldav_sync::class))->getMethod('assert_safe_caldav_url');
        $method->setAccessible(true);
        $method->invoke($caldav_sync, $caldav_url);
    }

    public function testPublicHostIsAccepted()
    {
        $this->assert_safe_caldav_url('https://93.184.216.34/dav.php/calendars/testuser/default/');

        $this->assertTrue(true); // No exception thrown.
    }

    public function testPrivateHostIsRejected()
    {
        $this->expectException(InvalidArgumentException::class);

        // The rejected host belongs in the message, so that the administrator knows what to allow.
        $this->expectExceptionMessage('127.0.0.1');

        $this->assert_safe_caldav_url('http://127.0.0.1/dav.php/calendars/testuser/default/');
    }

    public function testLinkLocalMetadataHostIsRejected()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('169.254.169.254');

        $this->assert_safe_caldav_url('http://169.254.169.254/latest/meta-data/');
    }

    public function testAllowedHostIsAccepted()
    {
        $GLOBALS['ea_test_settings']['caldav_allowed_hosts'] = "baikal\ncaldav.internal";

        $this->assert_safe_caldav_url('http://baikal/dav.php/calendars/testuser/default/');

        $this->assertTrue(true); // No exception thrown.
    }

    public function testAllowedConnectionUrlIsAccepted()
    {
        // Administrators paste the connection URL they use, so the host has to be read out of it.
        $GLOBALS['ea_test_settings']['caldav_allowed_hosts'] = 'http://baikal/dav.php/calendars/testuser/default/';

        $this->assert_safe_caldav_url('http://baikal/dav.php/calendars/testuser/default/');

        $this->assertTrue(true); // No exception thrown.
    }

    public function testHostOutsideTheAllowlistIsStillRejected()
    {
        $GLOBALS['ea_test_settings']['caldav_allowed_hosts'] = 'baikal';

        $this->expectException(InvalidArgumentException::class);

        $this->assert_safe_caldav_url('http://169.254.169.254/latest/meta-data/');
    }

    private function caldav_sync(): Caldav_sync
    {
        require_once APPPATH . 'libraries/Caldav_sync.php';

        // The constructor only loads CodeIgniter dependencies that these checks do not need.
        return (new ReflectionClass(Caldav_sync::class))->newInstanceWithoutConstructor();
    }

    public function testBlockedHostIsReportedForAPrivateAddress()
    {
        $this->assertSame(
            '192.168.1.50',
            $this->caldav_sync()->get_blocked_host('http://192.168.1.50/dav.php/calendars/testuser/default/'),
        );
    }

    public function testBlockedHostIsNullForAPublicAddress()
    {
        $this->assertNull($this->caldav_sync()->get_blocked_host('https://93.184.216.34/dav.php/'));
    }

    public function testAllowHostRecordsTheHostAndUnblocksTheUrl()
    {
        $caldav_url = 'http://192.168.1.50/dav.php/calendars/testuser/default/';

        $caldav_sync = $this->caldav_sync();

        $caldav_sync->allow_host($caldav_url);

        $this->assertSame('192.168.1.50', $GLOBALS['ea_test_settings']['caldav_allowed_hosts']);
        $this->assertNull($caldav_sync->get_blocked_host($caldav_url));
    }

    public function testAllowHostAppendsWithoutDuplicating()
    {
        $GLOBALS['ea_test_settings']['caldav_allowed_hosts'] = 'baikal';

        $caldav_sync = $this->caldav_sync();

        $caldav_sync->allow_host('http://192.168.1.50/dav.php/');
        $caldav_sync->allow_host('http://192.168.1.50/dav.php/');
        $caldav_sync->allow_host('http://BAIKAL/dav.php/');

        $this->assertSame("baikal\n192.168.1.50", $GLOBALS['ea_test_settings']['caldav_allowed_hosts']);
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
