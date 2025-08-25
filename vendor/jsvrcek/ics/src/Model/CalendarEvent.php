<?php

namespace Jsvrcek\ICS\Model;

use Jsvrcek\ICS\Model\Recurrence\RecurrenceRule;
use Jsvrcek\ICS\Model\Description\Conference;
use Jsvrcek\ICS\Model\Description\Geo;
use Jsvrcek\ICS\Model\Description\Location;
use Jsvrcek\ICS\Model\Relationship\Organizer;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Exception\CalendarEventException;

/**
 * @author justinsvrcek
 * http://tools.ietf.org/html/rfc5545#page-52
 */
class CalendarEvent
{
    /**
     * @var string
     */
    private $uid;

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @var RecurrenceRule
     */
    private $recurrenceRule;

    /**
     * array of dates to skip
     * https://tools.ietf.org/html/rfc5545#page-120
     *
     * @var \DateTime[]
     */
    private $exceptionDates = array();

    /**
     * @var string
     */
    private $class;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var string
     */
    private $description;

    /**
     * @var Geo
     */
    private $geo;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var Location[]
     */
    private $locations = array();

    /**
     * @var Organizer
     */
    private $organizer;

    /**
     * @var string
     */
    private $priority;

    /**
     * @var \DateTime
     */
    private $timestamp;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var array
     */
    private $image = [];

    /**
     * @todo add support in CalendarExport
     * @var string
     */
    private $recuringId;

    /**
     * @var integer
     */
    private $sequence;

    /**
     * @var Attendee[]
     */
    private $attendees = array();

    /**
     * @var CalendarAlarm[]
     */
    private $alarms = array();

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $transp;

    /**
     * @var boolean
     */
    private $allDay = false;

    /**
     * @var string
     */
    private $color;

    /**
     * @var array
     */
    private $customProperties = [];

    /**
     * @var Conference[]
     */
    private $conferences = [];

    /**
     * @return boolean
     */
    public function isAllDay()
    {
        return $this->allDay;
    }

    /**
     * @param boolean $allDay
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setAllDay($allDay)
    {
        $this->allDay = $allDay;
        return $this;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * also sets end time to 30 minutes after start as default<br>
     * - end time can be overridden with setEnd()
     *
     * @param \DateTime $start
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setStart(\DateTime $start)
    {
        $this->start = $start;
        $end = clone $start;
        $this->setEnd($end->add(\DateInterval::createFromDateString('30 minutes')));
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return CalendarEvent
     * @throws CalendarEventException
     */
    public function setEnd(\DateTime $end)
    {
        //check End is greater than Start
        if ($this->getStart() instanceof \DateTime) {
            if ($this->getStart() > $end) {
                throw new CalendarEventException('End DateTime must be greater than Start DateTime');
            }
        } else {
            throw new CalendarEventException('You must set the Start time before setting the End Time of a CalendarEvent');
        }

        $this->end = $end;
        return $this;
    }

    /**
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function getRecurrenceRule()
    {
        return $this->recurrenceRule;
    }

    /**
     * @param RecurrenceRule $recurrenceRule
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setRecurrenceRule(RecurrenceRule $recurrenceRule)
    {
        $this->recurrenceRule = $recurrenceRule;
        return $this;
    }

    /**
     * array of DateTime instances
     * @param array $dates
     * @return $this
     */
    public function setExceptionDates(array $dates)
    {
        $this->exceptionDates = $dates;
        return $this;
    }

    /**
     * @return array
     */
    public function getExceptionDates()
    {
        return $this->exceptionDates;
    }

    /**
     * @param \DateTime $date
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function addExceptionDate(\DateTime $date)
    {
        $this->exceptionDates[] = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return CalendarAlarm[]
     */
    public function getAlarms()
    {
        return $this->alarms;
    }

    /**
     * @param CalendarAlarm $alarm
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function addAlarm(CalendarAlarm $alarm)
    {
        $this->alarms[] = $alarm;
        return $this;
    }

    /**
     * @param array $alarms
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setAlarms(array $alarms)
    {
        $this->alarms = $alarms;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     *
     * @param string $class
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     *
     * @param \DateTime $created
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     *
     * @return Geo|null
     */
    public function getGeo()
    {
        return $this->geo;
    }

    /**
     *
     * @param Geo $geo
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setGeo(Geo $geo)
    {
        $this->geo = $geo;
        return $this;
    }

    /**
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     *
     * @param \DateTime $lastModified
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setLastModified(\DateTime $lastModified)
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    /**
     *
     * @return array $locations array of Location objects
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     *
     * @param array $locations array of Location objects
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setLocations(array $locations)
    {
        $this->locations = $locations;
        return $this;
    }

    /**
     *
     * @param Location $location
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function addLocation(Location $location)
    {
        $this->locations[] = $location;
        return $this;
    }

    /**
     *
     * @return Organizer
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     *
     * @param Organizer $organizer
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setOrganizer(Organizer $organizer)
    {
        $this->organizer = $organizer;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     *
     * @param string $priority
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     *
     * @param \DateTime $timestamp
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param string $status
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getRecuringId()
    {
        return $this->recuringId;
    }

    /**
     *
     * @param string $recuringId
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setRecuringId($recuringId)
    {
        $this->recuringId = $recuringId;
        return $this;
    }

    /**
     * @return integer
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param integer $sequence
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
        return $this;
    }

    /**
     *
     * @return array $attendees array of Attendee objects
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     *
     * @param array $attendees array of Attendee objects
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setAttendees(array $attendees)
    {
        $this->attendees = $attendees;
        return $this;
    }

    /**
     * @param Attendee $attendee
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function addAttendee(Attendee $attendee)
    {
        $this->attendees[] = $attendee;
        return $this;
    }

    /**
     * @param string $url
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getTransp()
    {
        return $this->transp;
    }

    /**
     * Possible values are 'TRANSPARENT' or 'OPAQUE'
     *
     * @param string $transp
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setTransp($transp)
    {
        $this->transp = $transp;
        return $this;
    }

    /**
     * @return array
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Images can come in one of two formats:
     *    1: URI - where a URI to the relevant image is provided
     *    2: BINARY - Where a Binary representation of the image is provided, normally Base64 Encoded.
     *
     * If sending a URI for the image, set the "VALUE" key to be "URI" and provide a URI key with the relevant URI.
     * IE:
     *     $calendar->setImage(
     *         'VALUE' => 'URL',
     *         'URI' => 'https://some.domain.com/path/to/image.jpg'
     *     );
     * It is optional to add a FMTTYPE key as well in the array, to indicate relevant mime type.
     * IE: 'FMTTYPE' => 'image/jpg'
     *
     * When sending Binary version, you must provide the encoding type of the image, as well as the encoded string.
     * IE:
     *    $calendar->setImage(
     *        'VALUE' => 'BINARY',
     *        'ENCODING' => 'BASE64',
     *        'BINARY' => $base64_encoded_string
     *    );
     * For Binary, it is RECOMMENDED to add the FMTTYPE as well, but still not REQUIRED
     *
     * @param array $image
     */
    public function setImage($image)
    {
        // Do some validation on provided data.
        if (array_key_exists('VALUE', $image) && in_array($image['VALUE'], ['URI', 'BINARY'])) {
            if ($image['VALUE'] == 'URI' && $image['URI']) {
                $new_image = [
                    'VALUE' => 'URI',
                    'URI' => $image['URI']
                ];

            } elseif ($image['VALUE'] == 'BINARY' && $image['ENCODING'] && $image['BINARY']) {
                $new_image = [
                    'VALUE' => 'BINARY',
                    'ENCODING' => $image['ENCODING'],
                    'BINARY' => $image['BINARY']
                ];
            } else {
                return $this;
            }
            $new_image['DISPLAY'] = isset($image['DISPLAY']) ? $image['DISPLAY'] : '';
            $new_image['FMTTYPE'] = isset($image['FMTTYPE']) ? $image['FMTTYPE'] : '';
            $this->image = $new_image;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }


    /**
     * Set color as CSS3 string
     *
     * @param string $color
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return array
     */
    public function getCustomProperties()
    {
        return $this->customProperties;
    }

    /**
     * use to add custom properties as array key-value pairs<br>
     * <strong>Example:</strong> $customProperties = array('X-WR-TIMEZONE' => 'America/New_York')
     *
     * @param array $customProperties
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function setCustomProperties(array $customProperties)
    {
        $this->customProperties = $customProperties;
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return \Jsvrcek\ICS\Model\CalendarEvent
     */
    public function addCustomProperty($key, $value)
    {
        $this->customProperties[$key] = $value;
        return $this;
    }

    /**
     * @param Conference[] $conferences
     * @return CalendarEvent
     */
    public function setConferences(array $conferences)
    {
        $this->conferences = $conferences;
        return $this;
    }

    /**
     * @param Conference $conference
     * @return $this
     */
    public function addConference(Conference $conference)
    {
        $this->conferences[] = $conference;
        return $this;
    }

    /**
     * @return Conference[]
     */
    public function getConferences()
    {
        return $this->conferences;
    }
}
