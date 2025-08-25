<?php

namespace Jsvrcek\ICS\Model;

use Jsvrcek\ICS\Model\Relationship\Attendee;

/**
 * See http://icalendar.org/iCalendar-RFC-5545/3-6-6-alarm-component.html
 *
 * Class CalendarAlarm
 * @package Jsvrcek\ICS\Model
 */
class CalendarAlarm
{
    /**
     * @var string
     */
    private $action;

    /**
     * RFC 5545 supports triggers relative to the parent VEVENT or VTODO, but Jsvrcek\ICS does not.
     * Only absolute trigger times are supported.
     * @todo Support RELATED, DTSTART, and DTEND.
     *
     * @var \DateTime|\DateInterval
     */
    private $trigger;

    /**
     * For AUDIO and EMAIL actions only.
     * For AUDIO there must be exactly one attachment, which must point to a sound resource.
     * For EMAIL there can be any number of attachments, including zero.
     *
     * Should be a string including a path and a file type, like so:
     *
     *     "FMTTYPE=application/msword:http://example.com/agenda.doc"
     *
     * @var array
     */
    private $attachments = array();

    /**
     * For DISPLAY and EMAIL actions only. For EMAIL this is the body.
     *
     * @var string
     */
    private $description;

    /**
     * For EMAIL action only. This is the email subject.
     *
     * @var string
     */
    private $summary;

    /**
     * For EMAIL action only. This is the email recipients.
     *
     * @var Attendee[]
     */
    private $attendees = array();

    /**
     * For all actions. The number of times to repeat the alarm.
     * If REPEAT is set then DURATION must also be set.
     *
     * @var integer
     */
    private $repeat;

    /**
     * For all actions. The duration of the alarm.
     * If DURATION is set then REPEAT must also be set.
     *
     * @var \DateInterval
     */
    private $duration;

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return \Jsvrcek\ICS\Model\CalendarAlarm
     */
    public function setAction($action)
    {
        $action = strtoupper($action);
        $this->action = $action;
        return $this;
    }

    /**
     * @return \DateTime|\DateInterval
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * @param \DateTime|\DateInterval $trigger
     * @return \Jsvrcek\ICS\Model\CalendarAlarm
     */
    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param array $attachments
     * @return \Jsvrcek\ICS\Model\CalendarAlarm
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * @param string $attachment
     * @return \Jsvrcek\ICS\Model\CalendarAlarm
     */
    public function addAttachment($attachment)
    {
        $this->attachments[] = $attachment;
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
     * @return \Jsvrcek\ICS\Model\CalendarAlarm
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return \Jsvrcek\ICS\Model\CalendarAlarm
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return array $attendees array of Attendee objects
     */
    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * @param array $attendees array of Attendee objects
     * @return \Jsvrcek\ICS\Model\CalendarAlarm
     */
    public function setAttendees(array $attendees)
    {
        $this->attendees = $attendees;
        return $this;
    }

    /**
     * @param Attendee $attendee
     * @return \Jsvrcek\ICS\Model\CalendarAlarm
     */
    public function addAttendee(Attendee $attendee)
    {
        $this->attendees[] = $attendee;
        return $this;
    }

    /**
     * @return int
     */
    public function getRepeat()
    {
        return $this->repeat;
    }

    /**
     * @param int $repeat
     * @return \Jsvrcek\ICS\Model\CalendarAlarm
     */
    public function setRepeat($repeat)
    {
        $this->repeat = $repeat;
        return $this;
    }

    /**
     * @return \DateInterval
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param \DateInterval $duration
     * @return \Jsvrcek\ICS\Model\CalendarAlarm
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }
}
