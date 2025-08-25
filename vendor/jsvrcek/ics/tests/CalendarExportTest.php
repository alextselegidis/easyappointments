<?php

namespace Jsvrcek\ICS\Tests;

use Jsvrcek\ICS\Model\CalendarAlarm;
use Jsvrcek\ICS\Model\Description\Conference;
use Jsvrcek\ICS\Model\Recurrence\DataType\Weekday;

use Jsvrcek\ICS\Model\Recurrence\DataType\WeekdayNum;

use Jsvrcek\ICS\Model\Recurrence\DataType\Frequency;

use Jsvrcek\ICS\Model\Recurrence\RecurrenceRule;

use Jsvrcek\ICS\Model\Relationship\Organizer;

use Jsvrcek\ICS\Utility\Formatter;

use Jsvrcek\ICS\CalendarStream;

use Jsvrcek\ICS\Model\Relationship\Attendee;

use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use PHPUnit\Framework\TestCase;

class CalendarExportTest extends TestCase
{
    /**
     * @covers Jsvrcek\ICS\CalendarExport::getStream
     */
    public function testGetStream()
    {
        $timezone = new \DateTimeZone('Antarctica/McMurdo');

        $organizer = new Organizer(new Formatter());
        $organizer->setValue('sue@example.com')
            ->setName('Sue Jones')
            ->setSentBy('mary@example.com')
            ->setLanguage('en');

        $attendee = new Attendee(new Formatter());
        $attendee->setName('Jane Smith')
            ->setCalendarUserType('INDIVIDUAL')
            ->setParticipationStatus('ACCEPTED')
            ->setRole('REQ-PARTICIPANT')
            ->setSentBy('joe@example')
            ->addCalendarMember('list@example.com')
            ->setValue('jane-smith@example.com');

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
        $conferenceDetails = [
            $conference1,
            $conference2
        ];

        $event = new CalendarEvent();
        $event->setUid('lLKjd89283oja89282lkjd8@example.com')
            ->setStart(new \DateTime('4 October 2013 12:00:00', $timezone))
            ->setEnd(new \DateTime('4 October 2013 22:00:00', $timezone))
            ->setSummary('Poker night at the South Pole')
            ->addAttendee($attendee)
            ->setOrganizer($organizer)
            ->setSequence(3)
            ->setTransp('TRANSPARENT')
            ->setColor('red')
            ->setTimestamp(new \DateTime('1 September 2013', $timezone))
            ->setConferences($conferenceDetails)
            ->addConference($conference3);


        $rrule = new RecurrenceRule(new Formatter());
        $rrule->setFrequency(new Frequency(Frequency::MONTHLY))
            ->setInterval(2)
            ->setCount(40)
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 1))
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 2))
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 3))
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 4))
            ->addByDay(new WeekdayNum(Weekday::FRIDAY, 1));
        $event->setRecurrenceRule($rrule);

        //add an alarms to this event
        $alarmAudio = new CalendarAlarm();
        $alarmAudio->setAction("audio");
        $alarmAudio->setTrigger($event->getStart());
        $alarmAudio->addAttachment("FMTTYPE=audio/basic:ftp://example.com/pub/sounds/bell-01.aud");
        $event->addAlarm($alarmAudio);

        $alarmDisplay = new CalendarAlarm();
        $alarmDisplay->setAction("display");
        $alarmDisplay->setTrigger($event->getStart());
        $alarmDisplay->setRepeat(3);
        $alarmDisplay->setDuration(new \DateInterval('PT15M'));
        $alarmDisplay->setDescription("DESCRIPTION");
        $event->addAlarm($alarmDisplay);

        $alarmEmail = new CalendarAlarm();
        $alarmEmail->setAction('email');
        $alarmEmail->setTrigger($event->getStart());
        $alarmEmail->addAttendee($attendee);
        $alarmEmail->setSummary("EMAIL SUBJECT");
        $alarmEmail->setDescription("EMAIL BODY");
        $alarmEmail->addAttachment("FMTTYPE=application/msword:http://example.com/agenda.docx");
        $alarmEmail->addAttachment("FMTTYPE=application/pdf:http://example.com/agenda.pdf");
        $event->addAlarm($alarmEmail);

        //test exception dates
        $eventTwo = new CalendarEvent();
        $eventTwo->setUid('eventtwo@example.com')
            ->setStart(new \DateTime('2 October 2013', $timezone))
            ->setSummary('Every Wednesday event')
            ->addCustomProperty('FOO', 'bar')
            ->addCustomProperty('BAR', 'baz')
            ->setTimestamp(new \DateTime('1 September 2013', $timezone));

        $rrule = new RecurrenceRule(new Formatter());
        $rrule->setFrequency(new Frequency(Frequency::WEEKLY));

        $eventTwo->setRecurrenceRule($rrule);

        //add exception dates to the event recurrence
        $eventTwo->addExceptionDate(new \DateTime('16 October 2013', $timezone))
            ->addExceptionDate(new \DateTime('30 October 2013', $timezone));

        $cal = new Calendar();
        $cal->setProdId('-//Jsvrcek//ICS//EN')
            ->setTimezone($timezone)
            ->addEvent($event)
            ->addEvent($eventTwo);

        //create second calendar using batch event provider
        $timezone = new \DateTimeZone('Arctic/Longyearbyen');
        $calTwo = new Calendar();
        $calTwo->setProdId('-//Jsvrcek//ICS//EN2')
            ->setColor('green')
            ->setTimezone($timezone);

        $calTwo->setEventsProvider(function ($start) use ($timezone) {
            $eventOne = new CalendarEvent();
            $eventOne->setUid('asdfasdf@example.com')
                ->setStart(new \DateTime('2016-01-01 01:01:01', $timezone))
                ->setEnd(new \DateTime('2016-01-02 01:01:01', $timezone))
                ->setSummary('A long day')
                ->setTimestamp(new \DateTime('1 September 2013', $timezone));

            $eventTwo = new CalendarEvent();
            $eventTwo->setUid('asdfasdf@example.com')
                ->setStart(new \DateTime('2016-01-02 01:01:01', $timezone))
                ->setEnd(new \DateTime('2016-01-03 01:01:01', $timezone))
                ->setSummary('Another long day')
                ->setTimestamp(new \DateTime('1 September 2013', $timezone));

            return ($start > 0) ? array() : array($eventOne, $eventTwo);
        });

        $ce = new CalendarExport(new CalendarStream(), new Formatter());
        $ce->addCalendar($cal)
            ->addCalendar($calTwo);

        $stream = $ce->getStream();

        $expected = file_get_contents(__DIR__ . '/test-local.ics');

        $this->assertEquals($expected, $stream);
    }

    public function testGetStreamUTC()
    {
        $timezone = new \DateTimeZone('Antarctica/McMurdo');

        $organizer = new Organizer(new Formatter());
        $organizer->setValue('sue@example.com')
            ->setName('Sue Jones')
            ->setSentBy('mary@example.com')
            ->setLanguage('en');

        $attendee = new Attendee(new Formatter());
        $attendee->setName('Jane Smith')
            ->setCalendarUserType('INDIVIDUAL')
            ->setParticipationStatus('ACCEPTED')
            ->setRole('REQ-PARTICIPANT')
            ->setSentBy('joe@example')
            ->addCalendarMember('list@example.com')
            ->setValue('jane-smith@example.com');

        $event = new CalendarEvent();
        $event->setUid('lLKjd89283oja89282lkjd8@example.com')
            ->setStart(new \DateTime('4 October 2013 12:00:00', $timezone))
            ->setEnd(new \DateTime('4 October 2013 22:00:00', $timezone))
            ->setSummary('Poker night at the South Pole')
            ->setStatus('CONFIRMED')
            ->addAttendee($attendee)
            ->setOrganizer($organizer)
            ->setSequence(3)
            ->setTransp('TRANSPARENT')
            ->setTimestamp(new \DateTime('1 September 2013', $timezone));

        $rrule = new RecurrenceRule(new Formatter());
        $rrule->setFrequency(new Frequency(Frequency::MONTHLY))
            ->setInterval(2)
            ->setCount(40)
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 1))
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 2))
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 3))
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 4))
            ->addByDay(new WeekdayNum(Weekday::FRIDAY, 1));
        $event->setRecurrenceRule($rrule);

        //add an alarms to this event
        $alarmAudio = new CalendarAlarm();
        $alarmAudio->setAction("audio");
        $alarmAudio->setTrigger($event->getStart());
        $alarmAudio->addAttachment("FMTTYPE=audio/basic:ftp://example.com/pub/sounds/bell-01.aud");
        $event->addAlarm($alarmAudio);

        $alarmDisplay = new CalendarAlarm();
        $alarmDisplay->setAction("display");
        $alarmDisplay->setTrigger($event->getStart());
        $alarmDisplay->setRepeat(3);
        $alarmDisplay->setDuration(new \DateInterval('PT15M'));
        $alarmDisplay->setDescription("DESCRIPTION");
        $event->addAlarm($alarmDisplay);

        $alarmEmail = new CalendarAlarm();
        $alarmEmail->setAction('email');
        $alarmEmail->setTrigger($event->getStart());
        $alarmEmail->addAttendee($attendee);
        $alarmEmail->setSummary("EMAIL SUBJECT");
        $alarmEmail->setDescription("EMAIL BODY");
        $alarmEmail->addAttachment("FMTTYPE=application/msword:http://example.com/agenda.docx");
        $alarmEmail->addAttachment("FMTTYPE=application/pdf:http://example.com/agenda.pdf");
        $event->addAlarm($alarmEmail);

        //test exception dates
        $eventTwo = new CalendarEvent();
        $eventTwo->setUid('eventtwo@example.com')
            ->setStart(new \DateTime('2 October 2013', $timezone))
            ->setSummary('Every Wednesday event')
            ->setTimestamp(new \DateTime('1 September 2013', $timezone));

        $rrule = new RecurrenceRule(new Formatter());
        $rrule->setFrequency(new Frequency(Frequency::WEEKLY));

        $eventTwo->setRecurrenceRule($rrule);

        //add exception dates to the event recurrence
        $eventTwo->addExceptionDate(new \DateTime('16 October 2013', $timezone))
            ->addExceptionDate(new \DateTime('30 October 2013', $timezone));

        $cal = new Calendar();
        $cal->setProdId('-//Jsvrcek//ICS//EN')
            ->setTimezone($timezone)
            ->addEvent($event)
            ->addEvent($eventTwo);

        //create second calendar using batch event provider
        $timezone = new \DateTimeZone('Arctic/Longyearbyen');
        $calTwo = new Calendar();
        $calTwo->setProdId('-//Jsvrcek//ICS//EN2')
            ->setTimezone($timezone);

        $calTwo->setEventsProvider(function ($start) use ($timezone) {
            $eventOne = new CalendarEvent();
            $eventOne->setUid('asdfasdf@example.com')
                ->setStart(new \DateTime('2016-01-01 01:01:01', $timezone))
                ->setEnd(new \DateTime('2016-01-02 01:01:01', $timezone))
                ->setSummary('A long day')
                ->setTimestamp(new \DateTime('1 September 2013', $timezone));

            $eventTwo = new CalendarEvent();
            $eventTwo->setUid('asdfasdf@example.com')
                ->setStart(new \DateTime('2016-01-02 01:01:01', $timezone))
                ->setEnd(new \DateTime('2016-01-03 01:01:01', $timezone))
                ->setSummary('Another long day')
                ->setTimestamp(new \DateTime('1 September 2013', $timezone));

            return ($start > 0) ? array() : array($eventOne, $eventTwo);
        });

        $ce = new CalendarExport(new CalendarStream(), new Formatter());
        $ce->addCalendar($cal)
            ->addCalendar($calTwo)
            ->setDateTimeFormat('utc');

        $stream = $ce->getStream();

        $expected = file_get_contents(__DIR__ . '/test-utc.ics');

        $this->assertEquals($expected, $stream);
    }

    public function testGetStreamTZAndLocal()
    {
        $timezone = new \DateTimeZone('Antarctica/McMurdo');

        $organizer = new Organizer(new Formatter());
        $organizer->setValue('sue@example.com')
            ->setName('Sue Jones')
            ->setSentBy('mary@example.com')
            ->setLanguage('en');

        $attendee = new Attendee(new Formatter());
        $attendee->setName('Jane Smith')
            ->setCalendarUserType('INDIVIDUAL')
            ->setParticipationStatus('ACCEPTED')
            ->setRole('REQ-PARTICIPANT')
            ->setSentBy('joe@example')
            ->addCalendarMember('list@example.com')
            ->setValue('jane-smith@example.com');

        $event = new CalendarEvent();
        $event->setUid('lLKjd89283oja89282lkjd8@example.com')
            ->setStart(new \DateTime('4 October 2013 12:00:00', $timezone))
            ->setEnd(new \DateTime('4 October 2013 22:00:00', $timezone))
            ->setSummary('Poker night at the South Pole')
            ->addAttendee($attendee)
            ->setOrganizer($organizer)
            ->setSequence(3)
            ->setTransp('TRANSPARENT')
            ->setTimestamp(new \DateTime('1 September 2013', $timezone));

        $rrule = new RecurrenceRule(new Formatter());
        $rrule->setFrequency(new Frequency(Frequency::MONTHLY))
            ->setInterval(2)
            ->setCount(40)
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 1))
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 2))
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 3))
            ->addByDay(new WeekdayNum(Weekday::SATURDAY, 4))
            ->addByDay(new WeekdayNum(Weekday::FRIDAY, 1));
        $event->setRecurrenceRule($rrule);

        //add an alarms to this event
        $alarmAudio = new CalendarAlarm();
        $alarmAudio->setAction("audio");
        $alarmAudio->setTrigger($event->getStart());
        $alarmAudio->addAttachment("FMTTYPE=audio/basic:ftp://example.com/pub/sounds/bell-01.aud");
        $event->addAlarm($alarmAudio);

        $alarmDisplay = new CalendarAlarm();
        $alarmDisplay->setAction("display");
        $alarmDisplay->setTrigger($event->getStart());
        $alarmDisplay->setRepeat(3);
        $alarmDisplay->setDuration(new \DateInterval('PT15M'));
        $alarmDisplay->setDescription("DESCRIPTION");
        $event->addAlarm($alarmDisplay);

        $alarmEmail = new CalendarAlarm();
        $alarmEmail->setAction('email');
        $alarmEmail->setTrigger($event->getStart());
        $alarmEmail->addAttendee($attendee);
        $alarmEmail->setSummary("EMAIL SUBJECT");
        $alarmEmail->setDescription("EMAIL BODY");
        $alarmEmail->addAttachment("FMTTYPE=application/msword:http://example.com/agenda.docx");
        $alarmEmail->addAttachment("FMTTYPE=application/pdf:http://example.com/agenda.pdf");
        $event->addAlarm($alarmEmail);

        //test exception dates
        $eventTwo = new CalendarEvent();
        $eventTwo->setUid('eventtwo@example.com')
            ->setStart(new \DateTime('2 October 2013', $timezone))
            ->setSummary('Every Wednesday event')
            ->setTimestamp(new \DateTime('1 September 2013', $timezone));

        $rrule = new RecurrenceRule(new Formatter());
        $rrule->setFrequency(new Frequency(Frequency::WEEKLY));

        $eventTwo->setRecurrenceRule($rrule);

        //add exception dates to the event recurrence
        $eventTwo->addExceptionDate(new \DateTime('16 October 2013', $timezone))
            ->addExceptionDate(new \DateTime('30 October 2013', $timezone));

        $cal = new Calendar();
        $cal->setProdId('-//Jsvrcek//ICS//EN')
            ->setTimezone($timezone)
            ->addEvent($event)
            ->addEvent($eventTwo);

        //create second calendar using batch event provider
        $timezone = new \DateTimeZone('Arctic/Longyearbyen');
        $calTwo = new Calendar();
        $calTwo->setProdId('-//Jsvrcek//ICS//EN2')
            ->setTimezone($timezone);

        $calTwo->setEventsProvider(function ($start) use ($timezone) {
            $eventOne = new CalendarEvent();
            $eventOne->setUid('asdfasdf@example.com')
                ->setStart(new \DateTime('2016-01-01 01:01:01', $timezone))
                ->setEnd(new \DateTime('2016-01-02 01:01:01', $timezone))
                ->setSummary('A long day')
                ->setTimestamp(new \DateTime('1 September 2013', $timezone));

            $eventTwo = new CalendarEvent();
            $eventTwo->setUid('asdfasdf@example.com')
                ->setStart(new \DateTime('2016-01-02 01:01:01', $timezone))
                ->setEnd(new \DateTime('2016-01-03 01:01:01', $timezone))
                ->setSummary('Another long day')
                ->setTimestamp(new \DateTime('1 September 2013', $timezone));

            return ($start > 0) ? array() : array($eventOne, $eventTwo);
        });

        $ce = new CalendarExport(new CalendarStream(), new Formatter());
        $ce->addCalendar($cal)
            ->addCalendar($calTwo)
            ->setDateTimeFormat('local-tz');

        $stream = $ce->getStream();

        $expected = file_get_contents(__DIR__ . '/test-local-tz.ics');

        $this->assertEquals($expected, $stream);
    }
}
