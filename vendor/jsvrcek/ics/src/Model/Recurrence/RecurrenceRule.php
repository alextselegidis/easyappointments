<?php

namespace Jsvrcek\ICS\Model\Recurrence;

use Jsvrcek\ICS\Utility\Formatter;

use Jsvrcek\ICS\Model\Recurrence\DataType\Frequency;
use Jsvrcek\ICS\Model\Recurrence\DataType\Weekday;
use Jsvrcek\ICS\Model\Recurrence\DataType\WeekdayNum;
use Jsvrcek\ICS\Exception\CalendarRecurrenceException;

/**
 * @todo BYSECOND, BYMINUTE, BYHOUR, BYMONTHDAY, BYYEARDAY, BYWEEKNO, BYMONTH, BYSETPOS, and WKST
 * are not implemented in the __toString function yet
 *
 * @author justinsvrcek
 * http://tools.ietf.org/html/rfc5545#page-37
 */
class RecurrenceRule
{
    const KEY = 'RRULE:';
    
    /**
     * @var Frequency
     */
    private $frequency;
    
    /**
     * @var \DateTime
     */
    private $until;
    
    /**
     * @var integer
     */
    private $count;
    
    /**
     * @var integer
     */
    private $interval;
    
    /**
     * @var array
     */
    private $bySecondList = array();
    
    /**
     * @var array
     */
    private $byMinuteList = array();
    
    /**
     * @var array
     */
    private $byHourList = array();
    
    /**
     * @var WeekdayNum[]
     */
    private $byDayList = array();
    
    /**
     * @todo need to add to RecurrenceRule::__toString()
     *
     * @var array
     */
    private $byMonthDayList = array();
    
    /**
     * @todo need to add to RecurrenceRule::__toString()
     *
     * @var array
     */
    private $byYearDayList = array();
    
    /**
     * @todo need to add to RecurrenceRule::__toString()
     *
     * @var array
     */
    private $byWeekNumberList = array();
    
    /**
     * @var array
     */
    private $byMonthList = array();
    
    /**
     * @todo need to add to RecurrenceRule::__toString()
     *
     * @var array
     */
    private $bySetPosYearDayList = array();
    
    /**
     * @todo need to add to RecurrenceRule::__toString()
     *
     * @var Weekday
     */
    private $weekStart;
    
    /**
     * @var Formatter
     */
    private $formatter;
    
    /**
     * @param Formatter $formatter
     */
    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @return \Jsvrcek\ICS\Model\Recurrence\DataType\Frequency
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param Frequency $frequency
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setFrequency(Frequency $frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }

    /**
     *
     * @return \DateTime
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * Setting UNTIL will remove a COUNT if set - you cannot have both
     *
     * @param \DateTime $until = null
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setUntil(\DateTime $until = null)
    {
        $this->until = $until;
        $this->count = null;
        return $this;
    }

    /**
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    
    /**
     * Setting a COUNT will remove the UNTIL date if set - you cannot have both
     * 
     * @param integer $count
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setCount($count)
    {
        $this->validateInteger($count);
        
        $this->count = $count;
        $this->until = null;
        return $this;
    }

    /**
     *
     * @return integer
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param integer $interval
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setInterval($interval)
    {
        $this->validateInteger($interval);
        
        $this->interval = $interval;
        return $this;
    }

    /**
     * @return array
     */
    public function getBySecondList()
    {
        return $this->bySecondList;
    }

    /**
     * @param array $bySecondList
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setBySecondList(array $bySecondList)
    {
        $this->bySecondList = $bySecondList;
        return $this;
    }
    
    /**
     * @param integer $integer 0-60
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function addBySecond($integer)
    {
        $this->validateInteger($integer);
        
        $this->bySecondList[] = $integer;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getByMinuteList()
    {
        return $this->byMinuteList;
    }

    /**
     * @param array $byMinuteList
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setByMinuteList(array $byMinuteList)
    {
        $this->byMinuteList = $byMinuteList;
        return $this;
    }
    
    /**
     * @param integer $integer 0-59
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function addByMinute($integer)
    {
        $this->validateInteger($integer);
    
        $this->byMinuteList[] = $integer;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getByHourList()
    {
        return $this->byHourList;
    }

    /**
     * @param array $byHourList
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setByHourList(array $byHourList)
    {
        $this->byHourList = $byHourList;
        return $this;
    }
    
    /**
     * @param integer $integer 0-23
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function addByHour($integer)
    {
        $this->validateInteger($integer);
    
        $this->byHourList[] = $integer;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getByDayList()
    {
        return $this->byDayList;
    }

    /**
     * @param array $byDayList
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setByDayList(array $byDayList)
    {
        $this->byDayList = $byDayList;
        return $this;
    }
    
    /**
     * @param WeekdayNum $weekdaynum
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function addByDay(WeekdayNum $weekdaynum)
    {
        $this->byDayList[] = $weekdaynum;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getByMonthDayList()
    {
        return $this->byMonthDayList;
    }

    /**
     * @param array $byMonthDayList
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setByMonthDayList(array $byMonthDayList)
    {
        $this->byMonthDayList = $byMonthDayList;
        return $this;
    }
    
    /**
     * @param integer $integer
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function addByMonthDay($integer)
    {
        $this->validateInteger($integer);
    
        $this->byMonthDayList[] = $integer;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getByYearDayList()
    {
        return $this->byYearDayList;
    }

    /**
     * @param array $byYearDayList
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setByYearDayList(array $byYearDayList)
    {
        $this->byYearDayList = $byYearDayList;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getByWeekNumberList()
    {
        return $this->byWeekNumberList;
    }

    /**
     * @param array $byWeekNumberList
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setByWeekNumberList(array $byWeekNumberList)
    {
        $this->byWeekNumberList = $byWeekNumberList;
        return $this;
    }
    
    /**
      * @param integer $integer
      * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
      */
    public function addByMonth($integer)
    {
        $this->validateInteger($integer);
    
        $this->byMonthList[] = $integer;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getByMonthList()
    {
        return $this->byMonthList;
    }

    /**
     * @param array $byMonthList
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setByMonthList(array $byMonthList)
    {
        $this->byMonthList = $byMonthList;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getBySetPosYearDayList()
    {
        return $this->bySetPosYearDayList;
    }

    /**
     * @param array $bySetPosYearDayList
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setBySetPosYearDayList(array $bySetPosYearDayList)
    {
        $this->bySetPosYearDayList = $bySetPosYearDayList;
        return $this;
    }

    /**
     *
     * @return Weekday
     */
    public function getWeekStart()
    {
        return $this->weekStart;
    }

    /**
     * @param Weekday $weekStart
     * @return \Jsvrcek\ICS\Model\Recurrence\RecurrenceRule
     */
    public function setWeekStart(Weekday $weekStart)
    {
        $this->weekStart = $weekStart;
        return $this;
    }

    /**
     * parses an RRULE string, hydrates self with values
     *
     * @param string $rRuleString
     * @return RecurrenceRule
     * @throws CalendarRecurrenceException
     */
    public function parse($rRuleString)
    {
        //remove RRULE:
        $string = str_replace(self::KEY, null, $rRuleString);
        
        $attributes = explode(';', $string);
        
        foreach ($attributes as $attribute) {
            list($key, $value) = explode('=', $attribute);
            
            switch ($key) {
                case Frequency::KEY:
                    if ($valueStringKey = array_search($value, Frequency::$values)) {
                        $this->setFrequency(new Frequency($valueStringKey));
                    } else {
                        throw new CalendarRecurrenceException('Unsupported FREQ value in Recurrence Rule (RRULE) string: '.$value);
                    }
                    break;
                    
                case 'INTERVAL':
                    $this->setInterval((int)$value);
                    break;
                
                case 'UNTIL':
                    $untilDate = new \DateTime(str_replace('Z', '', $value), new \DateTimeZone('UTC'));
                    $this->setUntil($untilDate);
                    break;

                case 'COUNT':
                    $this->setCount(intval($value));
                    break;
                    
                default:
                    throw new CalendarRecurrenceException('Unsupported attribute in Recurrence Rule (RRULE) string: '.$key);
            }
        }
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        $items = array($this->getFrequency()->__toString());
        
        if ($this->interval) {
            $items[] = 'INTERVAL='.$this->interval;
        }
        
        if ($this->until) {
            $items[] = 'UNTIL='.$this->formatter->getFormattedUTCDateTime($this->until);
        }
        
        if ($this->count) {
            $items[] = 'COUNT='.$this->count;
        }
        
        if ($this->byDayList) {
            $items[] = 'BYDAY='.implode(',', $this->byDayList);
        }
        
        if ($this->byMonthList) {
            $items[] = 'BYMONTH='.implode(',', $this->byMonthList);
        }
      
        if ($this->byMonthDayList) {
            $items[] = 'BYMONTHDAY='.implode(',', $this->byMonthDayList);
        }
        
        return self::KEY.implode(';', $items);
    }
    
    /**
     * @param integer $integer
     * @throws CalendarRecurrenceException
     */
    private function validateInteger($integer)
    {
        if (!is_int($integer)) {
            throw new CalendarRecurrenceException('Value must be an integer');
        }
    }
}
