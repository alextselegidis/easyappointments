<?php

namespace Jsvrcek\ICS\Model\Relationship;

use Jsvrcek\ICS\Utility\Formatter;

class Attendee
{
    /**
     * @var Formatter
     */
    private $formatter;
    
    /**
     * RFC 5545 cal-address
     * @var string
     */
    private $value;
    
    /**
     * RFC 5545 cutypeparam
     * @var string
     */
    private $calendarUserType;

    /**
     * RFC 5545 memberparam
     * @var array of uri values
     */
    private $calendarMembers = array();

    /**
     * RFC 5545 roleparam
     * @var string
     */
    private $role;

    /**
     * RFC 5545 partstatparam
     * @var string
     */
    private $participationStatus;

    /**
     * RFC 5545 rsvpparam
     * @var string
     */
    private $rsvp;

    /**
     * RFC 5545 deltoparam
     * @var array of uri values
     */
    private $delegatedTo = array();

    /**
     * RFC 5545 delfromparam
     * @var array of uri values
     */
    private $delegatedFrom = array();

    /**
     * RFC 5545 sentbyparam
     * @var string
     */
    private $sentBy;

    /**
     * RFC 5545 cnparam
     * @var string
     */
    private $name;

    /**
     * RFC 5545 dirparam
     * @var string
     */
    private $directory;

    /**
     * RFC 5545 languageparam
     * @var string
     */
    private $language;
    
    /**
     * @param Formatter $formatter
     */
    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }
    
    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * RFC 5545 cal-address http://tools.ietf.org/html/rfc5545#section-3.3.3
     * @param string $uri
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setValue($uri)
    {
        $this->value = $this->formatter->getFormattedUri($uri);
        return $this;
    }

    /**
     * @return string
     */
    public function getCalendarUserType()
    {
        return $this->calendarUserType;
    }

    /**
     * RFC 5545 cutypeparam http://tools.ietf.org/html/rfc5545#section-3.2.3
     *
     * @param string $calendarUserType
     *     "INDIVIDUAL"   ; An individual<br>
     *     "GROUP"        ; A group of individuals<br>
     *     "RESOURCE"     ; A physical resource<br>
     *     "ROOM"         ; A room resource<br>
     *     "UNKNOWN"      ; Otherwise not known<br>
     *     x-name         ; Experimental type<br>
     *     iana-token)    ; Other IANA-registered type
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setCalendarUserType($calendarUserType)
    {
        $this->calendarUserType = $calendarUserType;
        return $this;
    }

    /**
     * @return array
     */
    public function getCalendarMembers()
    {
        return $this->calendarMembers;
    }

    /**
     * RFC 5545 memberparam http://tools.ietf.org/html/rfc5545#section-3.2.11
     * @param array $calendarMemberUris array of uri values for calendar users ex. array('sue@example.com', 'joe@example.com')
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setCalendarMembers($calendarMemberUris)
    {
        foreach ($calendarMemberUris as &$uri) {
            $uri = $this->formatter->getFormattedUri($uri);
        }
        
        $this->calendarMembers = $calendarMemberUris;
        return $this;
    }
    
    /**
     * RFC 5545 memberparam http://tools.ietf.org/html/rfc5545#section-3.2.11
     * @param string $uri
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function addCalendarMember($uri)
    {
        $this->calendarMembers[] = $this->formatter->getFormattedUri($uri);
        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * RFC 5545 roleparam http://tools.ietf.org/html/rfc5545#section-3.2.16
     * @param string $role
     *     "CHAIR"             ; Indicates chair of the calendar entity <br>
     *     "REQ-PARTICIPANT"   ; Indicates a participant whose participation is required <br>
     *     "OPT-PARTICIPANT"   ; Indicates a participant whose participation is optional <br>
     *     "NON-PARTICIPANT"   ; Indicates a participant who is copied for information purposes only <br>
     *     x-name              ; Experimental role
     *     iana-token         ; Other IANA role
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return string
     */
    public function getParticipationStatus()
    {
        return $this->participationStatus;
    }

    /**
     * RFC 5545 partstatparam http://tools.ietf.org/html/rfc5545#section-3.2.12
     * @param string $participationStatus
     *     Example values for an Event: <br>
     *     "NEEDS-ACTION"     ; Event needs action <br>
     *     "ACCEPTED"         ; Event accepted <br>
     *     "DECLINED"         ; Event declined <br>
     *     "TENTATIVE"        ; Event tentatively accepted <br>
     *     "DELEGATED"        ; Event delegated
     *
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setParticipationStatus($participationStatus)
    {
        $this->participationStatus = $participationStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getRsvp()
    {
        return $this->rsvp;
    }

    /**
     * RFC 5545 rsvpparam http://tools.ietf.org/html/rfc5545#section-3.2.17
     * @param string $rsvp "TRUE" or "FALSE"
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setRsvp($rsvp)
    {
        $this->rsvp = $rsvp;
        return $this;
    }

    /**
     * @return array
     */
    public function getDelegatedTo()
    {
        return $this->delegatedTo;
    }

    /**
     * RFC 5545 deltoparam http://tools.ietf.org/html/rfc5545#section-3.2.5
     * @param array $delegatedToUris array of uri values for calendar users ex. array('sue@example.com', 'joe@example.com')
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setDelegatedTo(array $delegatedToUris)
    {
        foreach ($delegatedToUris as &$uri) {
            $uri = $this->formatter->getFormattedUri($uri);
        }
            
        $this->delegatedTo = $delegatedToUris;
        return $this;
    }
    
    /**
     * @param string $uri uri value for calendar users ex. 'mary@example.com'
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function addDelegatedTo($uri)
    {
        $this->delegatedTo[] = $this->formatter->getFormattedUri($uri);
        return $this;
    }

    /**
     * @return array
     */
    public function getDelegatedFrom()
    {
        return $this->delegatedFrom;
    }

    /**
     * RFC 5545 delfromparam http://tools.ietf.org/html/rfc5545#section-3.2.4
     * @param array $delegatedFromUris array of uri values for calendar users ex. array('sue@example.com', 'joe@example.com')
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setDelegatedFrom(array $delegatedFromUris)
    {
        foreach ($delegatedFromUris as &$uri) {
            $uri = $this->formatter->getFormattedUri($uri);
        }
        
        $this->delegatedFrom = $delegatedFromUris;
        return $this;
    }
    
    /**
     * @param string $uri uri value for calendar users ex. 'mary@example.com'
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function addDelegatedFrom($uri)
    {
        $this->delegatedFrom[] = $this->formatter->getFormattedUri($uri);
        return $this;
    }

    /**
     * @return string
     */
    public function getSentBy()
    {
        return $this->sentBy;
    }

    /**
     * RFC 5545 sentbyparam http://tools.ietf.org/html/rfc5545#section-3.2.18
     * @param string $sentBy email address
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setSentBy($sentBy)
    {
        $this->sentBy = $sentBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * RFC 5545 dirparam http://tools.ietf.org/html/rfc5545#section-3.2.6
     * @param string $directory uri directory entry associated with the calendar user
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language RFC 1766 language identifier
     * @return \Jsvrcek\ICS\Model\Relationship\Attendee
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        $string = 'ATTENDEE';
        
        if ($this->calendarUserType) {
            $string .= ';CUTYPE='.$this->calendarUserType;
        }
        
        if (count($this->calendarMembers)) {
            $string .= ';MEMBER="'.implode('","', $this->calendarMembers).'"';
        }
        
        if ($this->role) {
            $string .= ';ROLE='.$this->role;
        }
        
        if ($this->participationStatus) {
            $string .= ';PARTSTAT='.$this->participationStatus;
        }
        
        if ($this->rsvp) {
            $string .= ';RSVP='.$this->rsvp;
        }
        
        if (count($this->delegatedTo)) {
            $string .= ';DELEGATED-TO="'.implode('","', $this->delegatedTo).'"';
        }
        
        if (count($this->delegatedFrom)) {
            $string .= ';DELEGATED-FROM="'.implode('","', $this->delegatedFrom).'"';
        }
        
        if ($this->sentBy) {
            $string .= ';SENT-BY="'.$this->sentBy.'"';
        }
        
        if ($this->name) {
            $string .= ';CN='.$this->name;
        }
        
        if ($this->directory) {
            $string .= ';DIR="'.$this->directory.'"';
        }
        
        if ($this->language) {
            $string .= ';LANGUAGE='.$this->language;
        }
        
        $string .= ':'.$this->value;
        
        return $string;
    }
}
