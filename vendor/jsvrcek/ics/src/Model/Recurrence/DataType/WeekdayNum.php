<?php

namespace Jsvrcek\ICS\Model\Recurrence\DataType;

use Jsvrcek\ICS\Exception\CalendarRecurrenceException;

/**
 * @author justinsvrcek
 * http://tools.ietf.org/html/rfc5545#page-38
 */
class WeekdayNum extends Weekday
{
    const COUNT_FROM_START = null;
    const COUNT_FROM_END = '-';
    
    /**
     * @var integer 1-53
     */
    private $weekdaynum;
    
    /**
     * @var mixed
     */
    private $countFromStartOrEnd;
    
    /**
     * WeekdayNum(Weekday::MONDAY, 2, WeekdayNum:COUNT_FROM_END) will convert to the
     * string "-2MO", or "second from last Monday"
     *
     * @param integer $weekday
     * @param integer $weekdaynum
     * @param string $countFromStartOrEnd
     */
    public function __construct($weekday, $weekdaynum = null, $countFromStartOrEnd = null)
    {
        parent::__construct($weekday);
        
        $this->validateWeekdayNum($weekdaynum);
        $this->validateCountFromStartOrEnd($countFromStartOrEnd);
        
        $this->weekdaynum = $weekdaynum;
        $this->countFromStartOrEnd = $countFromStartOrEnd;
    }
    
    /* (non-PHPdoc)
     * @see Jsvrcek\ICS\Model\Recurrence\DataType.Weekday::__toString()
     */
    public function __toString()
    {
        return $this->countFromStartOrEnd.$this->weekdaynum.parent::__toString();
    }
    
    /**
     * @param mixed $countFromStartOrEnd
     * @throws CalendarRecurrenceException
     */
    private function validateCountFromStartOrEnd($countFromStartOrEnd)
    {
        if ($countFromStartOrEnd !== self::COUNT_FROM_START && $countFromStartOrEnd !== self::COUNT_FROM_END) {
            throw new CalendarRecurrenceException('You must use WeekdayNum::COUNT_FROM_START or WeekdayNum::COUNT_FROM_END');
        }
    }
    
    /**
     * @param integer $weekdaynum
     * @throws CalendarRecurrenceException
     */
    private function validateWeekdayNum($weekdaynum)
    {
        if (!is_int($weekdaynum) || $weekdaynum < 1 || $weekdaynum > 53) {
            throw new CalendarRecurrenceException('$weekdaynum must be an integer between 1 and 53');
        }
    }
}
