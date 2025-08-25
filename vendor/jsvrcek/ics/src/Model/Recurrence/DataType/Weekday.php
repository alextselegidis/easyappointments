<?php

namespace Jsvrcek\ICS\Model\Recurrence\DataType;

use Jsvrcek\ICS\Exception\CalendarRecurrenceException;

/**
 * @author justinsvrcek
 * http://tools.ietf.org/html/rfc5545#page-38
 */
class Weekday
{
    const SUNDAY = 1;
    const MONDAY = 2;
    const TUESDAY = 3;
    const WEDNESDAY = 4;
    const THURSDAY = 5;
    const FRIDAY = 6;
    const SATURDAY = 7;
    
    /**
     * @var integer
     */
    private $weekday;
    
    /**
     * @var array
     */
    protected $values = array(
                self::SUNDAY => 'SU',
                self::MONDAY => 'MO',
                self::TUESDAY => 'TU',
                self::WEDNESDAY => 'WE',
                self::THURSDAY => 'TH',
                self::FRIDAY => 'FR',
                self::SATURDAY => 'SA'
            );
    
    /**
     * @param integer $weekday Weekday::SUNDAY, Weekday::MONDAY,
     *                           Weekday::TUESDAY, Weekday::WEDNESDAY, Weekday::THURSDAY,
     *                           Weekday::FRIDAY, Weekday::SATURDAY
     */
    public function __construct($weekday)
    {
        $this->validateWeekday($weekday);
        
        $this->weekday = $weekday;
    }

    /**
     * @return number
     */
    public function getWeekday()
    {
        return $this->weekday;
    }

    /**
     * @param integer $weekday
     * @return \Jsvrcek\ICS\Model\Recurrence\DataType\Weekday
     */
    public function setWeekday($weekday)
    {
        $this->validateWeekday($weekday);
        
        $this->weekday = $weekday;
        return $this;
    }
    
    /**
     * @param integer $weekday
     * @throws CalendarRecurrenceException
     */
    private function validateWeekday($weekday)
    {
        if (!is_int($weekday) || $weekday < 1 || $weekday > 7) {
            throw new CalendarRecurrenceException('You must pass a Weekday constant to the contructor.');
        }
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->values[$this->weekday];
    }
}
