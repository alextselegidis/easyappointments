<?php

namespace Jsvrcek\ICS;

use Jsvrcek\ICS\Model\CalendarAlarm;
use Jsvrcek\ICS\Model\Recurrence\RecurrenceRule;

use Jsvrcek\ICS\Utility\Formatter;

use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Description\Location;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Model\Relationship\Organizer;

use Jsvrcek\ICS\CalendarStream;

class CalendarExport
{
    /**
     * 
     * @var array
     */
    private $calendars = array();
    
    /**
     * 
     * @var CalendarStream;
     */
    private $stream;
    
    /**
     * 
     * @var Formatter
     */
    private $formatter;
    
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
     */
    public function getStream()
    {
        $this->stream->reset();
        
        /* @var $cal Calendar */
        foreach ($this->getCalendars() as $cal)
        {
            //start calendar
            $this->stream->addItem('BEGIN:VCALENDAR')
                ->addItem('VERSION:'.$cal->getVersion())
                ->addItem('PRODID:'.$cal->getProdId())
                ->addItem('CALSCALE:'.$cal->getCalendarScale())
                ->addItem('METHOD:'.$cal->getMethod());
            
            //custom headers
            foreach ($cal->getCustomHeaders() as $key => $value)
            {
                $this->stream->addItem($key.':'.$value);
            }
            
            //timezone
            $this->stream->addItem('BEGIN:VTIMEZONE');
            
                $tz = $cal->getTimezone();
                $transitions = $tz->getTransitions(strtotime('1970-01-01'), strtotime('1970-12-31'));
                
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
                
                foreach ($transitions as $transition)
                {
                    $varName = ($transition['isdst']) ? 'daylightSavings' : 'standard';
                    
                    ${$varName}['exists'] = true;
                    ${$varName}['start'] = $this->formatter->getFormattedDateTime(new \DateTime($transition['time']));
                    
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
                    ->addItem('DTSTART:'.$standard['start'])
                    ->addItem('TZOFFSETTO:'.$standard['offsetTo'])
                    ->addItem('TZOFFSETFROM:'.$standard['offsetFrom']);
                    
                    if ($daylightSavings['exists'])
                    {
                        $this->stream->addItem('RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU');
                    }
                $this->stream->addItem('END:STANDARD');
                
                if ($daylightSavings['exists'])
                {
                    $this->stream->addItem('BEGIN:DAYLIGHT')
                        ->addItem('DTSTART:'.$daylightSavings['start'])
                        ->addItem('TZOFFSETTO:'.$daylightSavings['offsetTo'])
                        ->addItem('TZOFFSETFROM:'.$daylightSavings['offsetFrom'])
                        ->addItem('RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU')
                    ->addItem('END:DAYLIGHT');
                }
            
            $this->stream->addItem('END:VTIMEZONE');
            
            //add events
            /* @var $event CalendarEvent */
            foreach ($cal->getEvents() as $event)
            {
                $dtStart = $event->isAllDay() ?
                    $this->formatter->getFormattedDate($event->getStart()) : 
                    $this->formatter->getFormattedDateTime($event->getStart());

                $dtEnd   = $event->isAllDay() ?
                    $this->formatter->getFormattedDate($event->getEnd()) :
                    $this->formatter->getFormattedDateTime($event->getEnd());

                $this->stream->addItem('BEGIN:VEVENT')
                    ->addItem('UID:'.$event->getUid())
                    ->addItem('DTSTART:'. $dtStart)
                    ->addItem('DTEND:'. $dtEnd);
                
                    if ($event->getRecurrenceRule() instanceof RecurrenceRule)
                        $this->stream->addItem($event->getRecurrenceRule()->__toString());
                    
                    foreach ($event->getExceptionDates() as $date)
                    {
                        $this->stream->addItem('EXDATE:'.$this->formatter->getFormattedDateTime($date));
                    }
                    
                    if ($event->getSequence())
                        $this->stream->addItem('SEQUENCE:'.$event->getSequence());
                    
                $this->stream->addItem('STATUS:'.$event->getStatus())
                    ->addItem('SUMMARY:'.$event->getSummary())
                    ->addItem('DESCRIPTION:'.$event->getDescription());
                
                    if ($event->getClass())
                        $this->stream->addItem('CLASS:'.$event->getClass());
                
                    /* @var $location Location */
                    foreach ($event->getLocations() as $location)
                    {
                        $this->stream
                            ->addItem('LOCATION'.$location->getUri().$location->getLanguage().':'.$location->getName());
                    }
                    
                    if ($event->getPriority() > 0 && $event->getPriority() <= 9)
                        $this->stream->addItem('PRIORITY:'.$event->getPriority());

                    if ($event->getGeo())
                        $this->stream->addItem('GEO:'.$event->getGeo()->getLatitude().';'.$event->getGeo()->getLongitude());

                    if ($event->getUrl())
                        $this->stream->addItem('URL:'.$event->getUrl());
                    

                    if ($event->getTimestamp())
                    {
                        $this->stream->addItem('DTSTAMP:'.$this->formatter->getFormattedUTCDateTime($event->getTimestamp()));
                    }
                    else
                    {
                        $this->stream->addItem('DTSTAMP:'.$this->formatter->getFormattedUTCDateTime(new \DateTime()));
                    }

                    if ($event->getCreated())
                        $this->stream->addItem('CREATED:'.$this->formatter->getFormattedUTCDateTime($event->getCreated()));
                    
                    if ($event->getLastModified())
                        $this->stream->addItem('LAST-MODIFIED:'.$this->formatter->getFormattedUTCDateTime($event->getLastModified()));
                    
                    foreach ($event->getAttendees() as $attendee)
                    {
                        $this->stream->addItem($attendee->__toString());
                    }
                    
                    if ($event->getOrganizer())
                        $this->stream->addItem($event->getOrganizer()->__toString());

                    /** @var CalendarAlarm $alarm */
                    foreach ($event->getAlarms() as $alarm)
                    {
                        //basic requirements for all types of alarm
                        $this->stream->addItem('BEGIN:VALARM')
                            ->addItem('TRIGGER;VALUE=DATE-TIME:'.$this->formatter->getFormattedUTCDateTime($alarm->getTrigger()))
                            ->addItem('ACTION:'.$alarm->getAction());

                        //only handle repeats if both repeat and duration are set
                        if ($alarm->getRepeat() && $alarm->getDuration()) {
                            $this->stream->addItem('REPEAT:'.$alarm->getRepeat())
                                ->addItem('DURATION:'.$this->formatter->getFormattedDateInterval($alarm->getDuration()));
                        }

                        //action specific logic
                        switch ($alarm->getAction())
                        {
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

                                foreach ($alarm->getAttendees() as $attendee)
                                {
                                    $this->stream->addItem($attendee->__toString());
                                }
                                foreach ($alarm->getAttachments() as $attachment)
                                {
                                    $this->stream->addItem('ATTACH;'.$attachment);
                                }
                                break;
                            default:
                                throw new \Exception("Unknown ALARM action: '{$alarm->getAction()}'");
                                break;
                        }

                        $this->stream->addItem('END:VALARM');
                    }
                
                $this->stream->addItem('END:VEVENT');
            }
            
            //end calendar
            $this->stream->addItem('END:VCALENDAR');
        }
        
        return $this->stream->getStream();
    }
    

    /**
     * @return array
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
     * @param Calendar $cal
     * @return CalendarExport
     */
    public function addCalendar(Calendar $cal)
    {
        $this->calendars[] = $cal;
        return $this;
    }
}
