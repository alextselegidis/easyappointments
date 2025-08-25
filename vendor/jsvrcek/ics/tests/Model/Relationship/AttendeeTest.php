<?php

namespace Jsvrcek\ICS\Tests\Model\Relationship;

use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use PHPUnit\Framework\TestCase;

class AttendeeTest extends TestCase
{
    /**
     * @covers Jsvrcek\ICS\Model\Attendee::__toString()
     */
    public function testToString()
    {
        $object = new Attendee(new Formatter());

        $object
            ->setValue('joe-smith@example.com')
            ->setName('Joe Smith')
            ->setCalendarUserType('INDIVIDUAL')
            ->addCalendarMember('list@example.com')
            ->addDelegatedFrom('mary@example.com')
            ->addDelegatedTo('sue@example.com')
            ->addDelegatedTo('jane@example.com')
            ->setParticipationStatus('ACCEPTED')
            ->setRole('REQ-PARTICIPANT')
            ->setRsvp('TRUE')
            ->setSentBy('jack@example.com')
            ->setLanguage('en');
        
        $expected = 'ATTENDEE;CUTYPE=INDIVIDUAL;MEMBER="mailto:list@example.com";ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;RSVP=TRUE;DELEGATED-TO="mailto:sue@example.com","mailto:jane@example.com";DELEGATED-FROM="mailto:mary@example.com";SENT-BY="jack@example.com";CN=Joe Smith;LANGUAGE=en:mailto:joe-smith@example.com';
        
        $this->assertEquals($expected, $object->__toString());
    }
}
