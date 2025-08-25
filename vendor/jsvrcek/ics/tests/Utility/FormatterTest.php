<?php

namespace Jsvrcek\ICS\Tests\Utility;

use Jsvrcek\ICS\Model\Description\Conference;
use Jsvrcek\ICS\Utility\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    /**
     * @covers Jsvrcek\ICS\Formatter::getFormattedDateTime
     */
    public function testGetFormattedDateTime()
    {
        $ce = new Formatter();

        $dateTime = new \DateTime('1998-01-18 23:00:00');
        $expected = '19980118T230000';
        $actual = $ce->getFormattedDateTime($dateTime);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Jsvrcek\ICS\Formatter::getFormattedTimeOffset
     */
    public function testGetFormattedTimeOffset()
    {
        $ce = new Formatter();

        $offset = -18000;
        $expected = '-0500';
        $actual = $ce->getFormattedTimeOffset($offset);
        $this->assertEquals($expected, $actual);

        $offset = -14400;
        $expected = '-0400';
        $actual = $ce->getFormattedTimeOffset($offset);
        $this->assertEquals($expected, $actual);

        $offset = 14400;
        $expected = '+0400';
        $actual = $ce->getFormattedTimeOffset($offset);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Jsvrcek\ICS\Formatter::getFormattedUTCDateTime
     */
    public function testGetFormattedUTCDateTime()
    {
        $ce = new Formatter();

        $dateTime = new \DateTime('1998-01-18 23:00:00', new \DateTimeZone('America/New_York'));
        $expected = '19980119T040000Z';
        $actual = $ce->getFormattedUTCDateTime($dateTime);
        $this->assertEquals($expected, $actual);
        $ce = new Formatter();

        $dateTime = new \DateTime('1998-01-18 11:00:00', new \DateTimeZone('America/New_York'));
        $expected = '19980118T160000Z';
        $actual = $ce->getFormattedUTCDateTime($dateTime);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers \Jsvrcek\ICS\Utility\Formatter::getFormattedDateTimeWithTimeZone
     */
    public function testGetFormattedLocalDateTimeWithTimeZone()
    {
        $ce = new Formatter();

        $dateTime = new \DateTime('1998-01-18 23:00:00', new \DateTimeZone('America/New_York'));
        $expected = 'TZID=America/New_York:19980118T230000';
        $actual = $ce->getFormattedDateTimeWithTimeZone($dateTime);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Jsvrcek\ICS\Formatter::getFormattedUri
     */
    public function testGetFormattedUri()
    {
        $ce = new Formatter();

        $expected = 'mailto:test@example.com';
        $actual = $ce->getFormattedUri('test@example.com');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Jsvrcek\ICS\Formatter::getFormattedDateInterval
     */
    public function testGetFormattedDateInterval()
    {
        $ce = new Formatter();

        $tests = array(
            "PT15M",
            "PT1H",
            "P345D",
            "P1Y6M29DT4H34M23S"
        );

        foreach ($tests as $test) {
            $this->assertEquals(
                $test,
                $ce->getFormattedDateInterval(new \DateInterval($test)),
                $test
            );
        }
    }

    /**
     * @covers Jsvrcek\ICS\\Formatter::getFormattedImageString
     */
    public function testGetFormattedImageString()
    {
        $ce = new Formatter();

        $tests = [
            [
                'expected' => 'IMAGE;VALUE=URI;DISPLAY=BADGE;FMTTYPE=image/png:http://example.com/images/party.png',
                'image' => [
                    'VALUE' => 'URI',
                    'URI' => 'http://example.com/images/party.png',
                    'DISPLAY' => 'BADGE',
                    'FMTTYPE' => 'image/png'
                ]
            ],
            [
                'expected' => 'IMAGE;VALUE=BINARY;ENCODING=BASE64;DISPLAY=BADGE;FMTTYPE=image/png:VGhpcyBpcyBteSBhbWF6aW5nIGltYWdl',
                'image' => [
                    'VALUE' => 'BINARY',
                    'ENCODING' => 'BASE64',
                    'BINARY' => 'VGhpcyBpcyBteSBhbWF6aW5nIGltYWdl',
                    'DISPLAY' => 'BADGE',
                    'FMTTYPE' => 'image/png'
                ]
            ]
        ];

        foreach ($tests as $test) {
            $this->assertEquals(
                $test['expected'],
                $ce->getFormattedImageString($test['image'])
            );
        }
    }

    /**
     * @covers Jsvrcek\ICS\Formatter::getEscapedText
     */
    public function testgetEscapedText()
    {
        $ce = new Formatter();

        $strings = [
            [
                'original' => '14 Main St, Capital City',
                'expected' => '14 Main St\, Capital City',
                'name' => 'a comma'
            ],
            [
                'original' => 'A comma and a dot; Semi-colon',
                'expected' => 'A comma and a dot\; Semi-colon',
                'name' => 'a semi-colon'
            ],
            [
                'original' => 'Here is a comma, and; a semi-colon',
                'expected' => 'Here is a comma\, and\; a semi-colon',
                'name' => 'both comma and semi-colon'
            ],
            [
                'original' => 'This comma\, is pre-escaped',
                'expected' => 'This comma\, is pre-escaped',
                'name' => 'a pre-escaped comma'
            ],
            [
                'original' => 'Pre-escaped\; This Semi-colon is',
                'expected' => 'Pre-escaped\; This Semi-colon is',
                'name' => 'a pre-escaped semi-colon'
            ],
            [
                'original' => 'Pre-escaped\; This Semi-colon is\, and so was that comma',
                'expected' => 'Pre-escaped\; This Semi-colon is\, and so was that comma',
                'name' => 'both a pre-escaped comma and a pre-escaped semi-colon'
            ],
            [
                'original' => 'This comma\, was pre-escaped while this one, is not',
                'expected' => 'This comma\, was pre-escaped while this one\, is not',
                'name' => 'a pre-escaped comma and an unescaped comma'
            ],
            [
                'original' => 'First\; we pre-escape. Then; we forget to',
                'expected' => 'First\; we pre-escape. Then\; we forget to',
                'name' => 'a pre-escaped semi-colon and an unescaped semi-colon'
            ],
            [
                'original' => 'How many, ducks\; Is a question\, This; is not',
                'expected' => 'How many\, ducks\; Is a question\, This\; is not',
                'name' => 'both pre-escaped comma and semi-colon, and unescaped comma and semi-colon'
            ],
            [
                'original' => 'It appears that backslashes (\) must also be escaped',
                'expected' => 'It appears that backslashes (\\\) must also be escaped',
                'name' => 'un-escaped backslash'
            ],
            [
                'original' => 'It appears that backslashes (\\\) are now escaped',
                'expected' => 'It appears that backslashes (\\\) are now escaped',
                'name' => 'escaped backslash'
            ],
            [
                'original' => "But we can't be escaping...\nnew-lines!",
                'expected' => "But we can't be escaping...\nnew-lines!",
                'name' => 'new-line character'
            ],
        ];

        foreach ($strings as $string) {
            $this->assertEquals(
                $string['expected'],
                $ce->getEscapedText($string['original']),
                'Failed on escaping string including ' . $string['name']
            );
        }
    }

    /**
     * @covers \Jsvrcek\ICS\Utility\Formatter::getConferenceText
     */
    public function testgetConferenceText()
    {
        $ce = new Formatter();

        $conference1 = new Conference('tel:+1-412-555-0123,,,654321');
        $conference2 = new Conference(
            'https://chat.example.com/audio?id=123456',
            [
                'feature' => ['AUDIO', 'VIDEO'],
                'label' => 'Attendee dial-in'
            ]
        );
        $conference3 = new Conference(
            'xmpp:chat-123@conference.example.com',
            [
                'feature' => 'CHAT',
                'label' => 'Chat Room',
                'language' => 'en:Germany'
            ]
        );

        $expected = [
            'CONFERENCE;VALUE=URI:tel:+1-412-555-0123,,,654321',
            'CONFERENCE;VALUE=URI;FEATURE=AUDIO,VIDEO;LABEL=Attendee dial-in:https://chat.example.com/audio?id=123456',
            'CONFERENCE;VALUE=URI;FEATURE=CHAT;LABEL=Chat Room;LANGUAGE=en:Germany:xmpp:chat-123@conference.example.com'
        ];
        $results = [
            $ce->getConferenceText($conference1),
            $ce->getConferenceText($conference2),
            $ce->getConferenceText($conference3)
        ];

        $this->assertEquals($expected[0], $results[0]);
        $this->assertEquals($expected[1], $results[1]);
        $this->assertEquals($expected[2], $results[2]);
    }
}
