<?php

namespace Jsvrcek\ICS\Tests\Model\Recurrence;

use Jsvrcek\ICS\Model\Recurrence\DataType\Weekday;
use Jsvrcek\ICS\Model\Recurrence\DataType\WeekdayNum;
use Jsvrcek\ICS\Model\Recurrence\DataType\Frequency;

use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\Model\Recurrence\RecurrenceRule;
use PHPUnit\Framework\TestCase;

class RecurrenceRuleTest extends TestCase
{
    /**
     * @covers Jsvrcek\ICS\Model\Recurrence\RecurrentRule::__toString()
     */
    public function testToString()
    {
        $object = new RecurrenceRule(new Formatter());

        $object
            ->setFrequency(new Frequency(Frequency::YEARLY))
            ->setInterval(2)
            ->setCount(10)
            ->addByDay(new WeekdayNum(Weekday::MONDAY, 10, WeekdayNum::COUNT_FROM_START))
            ->addByDay(new WeekdayNum(Weekday::TUESDAY, 10, WeekdayNum::COUNT_FROM_END))
            ->setUntil(new \DateTime('2050-01-01 00:00:00', new \DateTimeZone('UTC')))
        ;
        
        $expected = 'RRULE:FREQ=YEARLY;INTERVAL=2;UNTIL=20500101T000000Z;BYDAY=10MO,-10TU';
        
        $this->assertEquals($expected, $object->__toString());

        //get a new object, test byMonth
        $object = new RecurrenceRule(new Formatter());

        $object
            ->setFrequency(new Frequency(Frequency::WEEKLY))
            ->setInterval(1)
            ->addByMonth(2)
            ->addByMonth(3)
            ->addByMonth(4)
        ;

        $expected = 'RRULE:FREQ=WEEKLY;INTERVAL=1;BYMONTH=2,3,4';
        
        $this->assertEquals($expected, $object->__toString());
    }
    
    /**
     * @covers Jsvrcek\ICS\Model\Recurrence\RecurrentRule::parse()
     * @depends testToString
     */
    public function testParse()
    {
        $object = new RecurrenceRule(new Formatter());
        $rRuleString = 'RRULE:FREQ=YEARLY;INTERVAL=2';
        $object->parse($rRuleString);
        $this->assertEquals($rRuleString, $object->__toString());

        $object = new RecurrenceRule(new Formatter());
        $rRuleString = 'RRULE:FREQ=WEEKLY;INTERVAL=4';
        $object->parse($rRuleString);
        $this->assertEquals($rRuleString, $object->__toString());
        
        $object = new RecurrenceRule(new Formatter());
        $rRuleString = 'RRULE:FREQ=WEEKLY;INTERVAL=4;UNTIL=20500101T000000Z';
        $object->parse($rRuleString);
        $this->assertEquals($rRuleString, $object->__toString());
        
        $object = new RecurrenceRule(new Formatter());
        $rRuleString = 'RRULE:FREQ=YEARLY;INTERVAL=2;COUNT=10';
        $object->parse($rRuleString);
        $this->assertEquals(10, $object->getCount());
        $this->assertEquals($rRuleString, $object->__toString());

        /*
        $rRuleString = 'RRULE:FREQ=YEARLY;INTERVAL=2;COUNT=10;BYDAY=10MO,-10TU';
        $object->parse($rRuleString);
        $this->assertEquals($rRuleString, $object->__toString());
        */
    }
}
