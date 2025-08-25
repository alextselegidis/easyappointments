<?php

namespace Jsvrcek\ICS;

use Jsvrcek\ICS\Model\CalendarAlarm;
use Jsvrcek\ICS\Model\Recurrence\RecurrenceRule;
use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Description\Location;

class CalendarExport
{
    /**
     * @var Calendar[]
     */
    private $calendars = array();

    /**
     * @var CalendarStream;
     */
    private $stream;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var string;
     */
    private $dateTimeFormat = 'local';

    public function __construct(CalendarStream $stream, Formatter $formatter)
    {
        $this->stream = $stream;
        $this->formatter = $formatter;
    }

    /**
     * @return CalendarStream
     */
    public function getStreamObject()
    {
        return $this->stream;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getStream()
    {
        $this->stream->reset();

        /* @var $cal Calendar */
        foreach ($this->getCalendars() as $cal) {
            //start calendar
            $this->stream->addItem('BEGIN:VCALENDAR')
                ->addItem('VERSION:'.$cal->getVersion())
                ->addItem('PRODID:'.$cal->getProdId())
                ->addItem('CALSCALE:'.$cal->getCalendarScale())
                ->addItem('METHOD:'.$cal->getMethod());

            if ($cal->getName()) {
                $this->stream->addItem('NAME:'.$cal->getName());
            }

            if ($cal->getColor()) {
                $this->stream->addItem('COLOR:'.$cal->getColor());
            }

            if ($cal->getImage()) {
                $this->stream->addItem($this->formatter->getFormattedImageString($cal->getImage()));
            }

            //custom headers
            foreach ($cal->getCustomHeaders() as $key => $value) {
                $this->stream->addItem($key.':'.$value);
            }

            //timezone
            $this->stream->addItem('BEGIN:VTIMEZONE');

            $tz = $cal->getTimezone();

            $calEvents = $cal->getEvents();

            // Fallback to current year
            $startYear = $endYear = date('Y');
            if ($calEvents->first() !== false) {
                // Take first event as reference for timezone transitions
                $firstEvent = $calEvents->first();
                $startYear = $firstEvent->getStart()->format('Y');
                $endYear = $firstEvent->getEnd()->format('Y');
            }

            $transitions = $tz->getTransitions(strtotime($startYear . '-01-01'), strtotime($endYear . '-12-31'));

            $daylightSavings = array(
                        'exists' => false,
                        'start' => '',
                        'offsetTo' => '',
                        'offsetFrom' => ''
                    );

            $standard = array(
                        'start' => '',
                        'offsetTo' => '',
                        'offsetFrom' => ''
                    );

            foreach ($transitions as $transition) {
                $varName = ($transition['isdst']) ? 'daylightSavings' : 'standard';

                ${$varName}['exists'] = true;
                if ($this->dateTimeFormat === 'local') {
                    ${$varName}['start'] = ':' . $this->formatter->getFormattedDateTime(new \DateTime($transition['time']));
                } else if ($this->dateTimeFormat === 'utc') {
                    ${$varName}['start'] = ':' . $this->formatter->getFormattedUTCDateTime(new \DateTime($transition['time']));
                } else if ($this->dateTimeFormat == 'local-tz') {
                    ${$varName}['start'] = ';' . $this->formatter->getFormattedDateTimeWithTimeZone(new \DateTime($transition['time']));
                }

                ${$varName}['offsetTo'] = $this->formatter->getFormattedTimeOffset($transition['offset']);

                //get previous offset
                $previousTimezoneObservance = $transition['ts'] - 100;
                $tzDate = new \DateTime('now', $tz);
                $tzDate->setTimestamp($previousTimezoneObservance);
                $offset = $tzDate->getOffset();

                ${$varName}['offsetFrom'] = $this->formatter->getFormattedTimeOffset($offset);
            }

            $this->stream->addItem('TZID:'.$tz->getName());

            $this->stream->addItem('BEGIN:STANDARD')
                    ->addItem('DTSTART'.$standard['start'])
                    ->addItem('TZOFFSETTO:'.$standard['offsetTo'])
                    ->addItem('TZOFFSETFROM:'.$standard['offsetFrom']);

            if ($daylightSavings['exists']) {
                $this->stream->addItem('RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU');
            }
            $this->stream->addItem('END:STANDARD');

            if ($daylightSavings['exists']) {
                $this->stream->addItem('BEGIN:DAYLIGHT')
                        ->addItem('DTSTART'.$daylightSavings['start'])
                        ->addItem('TZOFFSETTO:'.$daylightSavings['offsetTo'])
                        ->addItem('TZOFFSETFROM:'.$daylightSavings['offsetFrom'])
                        ->addItem('RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU')
                    ->addItem('END:DAYLIGHT');
            }

            $this->stream->addItem('END:VTIMEZONE');

            //add events
            /* @var $event CalendarEvent */
            foreach ($cal->getEvents() as $event) {

                if ($event->isAllDay()) {
                    $dtStart = ':' . $this->formatter->getFormattedDate($event->getStart());
                    $dtEnd = ':' . $this->formatter->getFormattedDate($event->getEnd());
                } else if ($this->dateTimeFormat === 'local') {
                    $dtStart = ':' . $this->formatter->getFormattedDateTime($event->getStart());
                    $dtEnd = ':' . $this->formatter->getFormattedDateTime($event->getEnd());
                } else if ($this->dateTimeFormat === 'utc') {
                    $dtStart = ':' . $this->formatter->getFormattedUTCDateTime($event->getStart());
                    $dtEnd = ':' . $this->formatter->getFormattedUTCDateTime($event->getEnd());
                } else if ($this->dateTimeFormat === 'local-tz'){
                    $dtStart = ';' . $this->formatter->getFormattedDateTimeWithTimeZone($event->getStart());
                    $dtEnd = ';' . $this->formatter->getFormattedDateTimeWithTimeZone($event->getEnd());
                }

                $this->stream->addItem('BEGIN:VEVENT')
                    ->addItem('UID:'.$event->getUid())
                    ->addItem('DTSTART'. $dtStart)
                    ->addItem('DTEND'. $dtEnd);

                if ($event->getRecurrenceRule() instanceof RecurrenceRule) {
                    $this->stream->addItem($event->getRecurrenceRule()->__toString());
                }

                foreach ($event->getExceptionDates() as $date) {
                    $this->stream->addItem('EXDATE:'.$this->formatter->getFormattedDateTime($date));
                }

                if ($event->getSequence()) {
                    $this->stream->addItem('SEQUENCE:'.$event->getSequence());
                }

                if ($event->getTransp()) {
                    $this->stream->addItem('TRANSP:'.$event->getTransp());
                }

                if ($event->getColor()) {
                    $this->stream->addItem('COLOR:'.$event->getColor());
                }

                if ($event->getImage()) {
                    $this->stream->addItem($this->formatter->getFormattedImageString($event->getImage()));
                }

                if ($event->getStatus()) {
                    $this->stream->addItem('STATUS:'.$event->getStatus());
                }

                $this->stream
                    ->addItem('SUMMARY:'.$event->getSummary())
                    ->addItem('DESCRIPTION:'.$this->formatter->getEscapedText($event->getDescription()));

                if ($event->getClass()) {
                    $this->stream->addItem('CLASS:'.$event->getClass());
                }

                /* @var $location Location */
                foreach ($event->getLocations() as $location) {
                    $this->stream
                            ->addItem('LOCATION'.$location->getUri().$location->getLanguage().':'.$this->formatter->getEscapedText($location->getName()));
                }

                if ($event->getPriority() > 0 && $event->getPriority() <= 9) {
                    $this->stream->addItem('PRIORITY:'.$event->getPriority());
                }

                if ($event->getGeo()) {
                    $this->stream->addItem('GEO:'.$event->getGeo()->getLatitude().';'.$event->getGeo()->getLongitude());
                }

                if ($event->getUrl()) {
                    $this->stream->addItem('URL:'.$event->getUrl());
                }


                if ($event->getTimestamp()) {
                    $this->stream->addItem('DTSTAMP:'.$this->formatter->getFormattedUTCDateTime($event->getTimestamp()));
                } else {
                    $this->stream->addItem('DTSTAMP:'.$this->formatter->getFormattedUTCDateTime(new \DateTime()));
                }

                if ($event->getCreated()) {
                    $this->stream->addItem('CREATED:'.$this->formatter->getFormattedUTCDateTime($event->getCreated()));
                }

                if ($event->getLastModified()) {
                    $this->stream->addItem('LAST-MODIFIED:'.$this->formatter->getFormattedUTCDateTime($event->getLastModified()));
                }

                foreach ($event->getAttendees() as $attendee) {
                    $this->stream->addItem((string)$attendee);
                }

                if ($event->getOrganizer()) {
                    $this->stream->addItem($event->getOrganizer()->__toString());
                }

                foreach ($event->getCustomProperties() as $key => $value) {
                    $this->stream->addItem($key.':'.$value);
                }

                /** @var CalendarAlarm $alarm */
                foreach ($event->getAlarms() as $alarm) {
                    //basic requirements for all types of alarm
                    $this->stream->addItem('BEGIN:VALARM')
                            ->addItem($this->formatTrigger($alarm->getTrigger()))
                            ->addItem('ACTION:'.$alarm->getAction());

                    //only handle repeats if both repeat and duration are set
                    if ($alarm->getRepeat() && $alarm->getDuration()) {
                        $this->stream->addItem('REPEAT:'.$alarm->getRepeat())
                                ->addItem('DURATION:'.$this->formatter->getFormattedDateInterval($alarm->getDuration()));
                    }

                    //action specific logic
                    switch ($alarm->getAction()) {
                            case 'AUDIO':
                                $attachments = $alarm->getAttachments();
                                $this->stream->addItem('ATTACH;'.$attachments[0]);
                                break;
                            case 'DISPLAY':
                                $this->stream->addItem('DESCRIPTION:'.$alarm->getDescription());
                                break;
                            case 'EMAIL':
                                $this->stream->addItem('SUMMARY:'.$alarm->getSummary())
                                    ->addItem('DESCRIPTION:'.$alarm->getDescription());

                                foreach ($alarm->getAttendees() as $attendee) {
                                    $this->stream->addItem((string)$attendee);
                                }
                                foreach ($alarm->getAttachments() as $attachment) {
                                    $this->stream->addItem('ATTACH;'.$attachment);
                                }
                                break;
                            default:
                                throw new \Exception("Unknown ALARM action: '{$alarm->getAction()}'");
                                break;
                        }

                    $this->stream->addItem('END:VALARM');
                }

                foreach ($event->getConferences() as $conference) {
                    $this->stream->addItem($this->formatter->getConferenceText($conference));
                }

                $this->stream->addItem('END:VEVENT');
            }

            //end calendar
            $this->stream->addItem('END:VCALENDAR');
        }

        return $this->stream->getStream();
    }


    /**
     * @return Calendar[]
     */
    public function getCalendars()
    {
        return $this->calendars;
    }

    /**
     * @param array $calendars
     * @return CalendarExport
     */
    public function setCalendars(array $calendars)
    {
        $this->calendars = $calendars;
        return $this;
    }

    /**
     * @param string $format
     * @return $this
     * @throws \Exception
     */
    public function setDateTimeFormat($format)
    {
        if (in_array($format, ['local', 'local-tz', 'utc'])) {
            $this->dateTimeFormat = $format;
        } else {
            throw new \Exception('Invalid Format Chosen');
        }
        return $this;

    }

    /**
     * @param Calendar $cal
     * @return CalendarExport
     */
    public function addCalendar(Calendar $cal)
    {
        $this->calendars[] = $cal;
        return $this;
    }

    private function formatTrigger($trigger) {
        if ($trigger instanceof \DateInterval) {
            return 'TRIGGER:-' . $this->formatter->getFormattedDateInterval($trigger);
        } else {
            return 'TRIGGER;VALUE=DATE-TIME:'.$this->formatter->getFormattedUTCDateTime($trigger);
        }
    }
}
