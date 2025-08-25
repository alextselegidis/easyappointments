<?php

namespace Jsvrcek\ICS\Model\Recurrence\DataType;

use Jsvrcek\ICS\Exception\CalendarRecurrenceException;

/**
 * @author justinsvrcek
 * http://tools.ietf.org/html/rfc5545#page-38
 */
class Frequency
{
    const KEY = 'FREQ';
    
    const SECONDLY = 1;
    const MINUTELY = 2;
    const HOURLY = 3;
    const DAILY = 4;
    const WEEKLY = 5;
    const MONTHLY = 6;
    const YEARLY = 7;
    
    /**
     * @var integer
     */
    private $freq;
    
    public static $values = array(
                self::SECONDLY => 'SECONDLY',
                self::MINUTELY => 'MINUTELY',
                self::HOURLY => 'HOURLY',
                self::DAILY => 'DAILY',
                self::WEEKLY => 'WEEKLY',
                self::MONTHLY => 'MONTHLY',
                self::YEARLY => 'YEARLY'
            );
    
    /**
     * @param integer $frequency Frequency::SECONDLY, Frequency::MINUTELY,
     *                           Frequency::HOURLY, Frequency::DAILY, Frequency::WEEKLY,
     *                           Frequency::MONTHLY, Frequency::YEARLY
     */
    public function __construct($frequency)
    {
        $this->validateFrequency($frequency);
        
        $this->freq = $frequency;
    }

    /**
     * @return number
     */
    public function getFreq()
    {
        return $this->freq;
    }

    /**
     * @param integer $freq
     * @return \Jsvrcek\ICS\Model\Recurrence\DataType\Frequency
     */
    public function setFreq($freq)
    {
        $this->validateFrequency($freq);
        
        $this->freq = $freq;
        return $this;
    }
    
    /**
     * @param integer $frequency
     * @throws CalendarRecurrenceException
     */
    private function validateFrequency($frequency)
    {
        if (!is_int($frequency) || $frequency < 1 || $frequency > 7) {
            throw new CalendarRecurrenceException('You must pass a Frequency constant to the contructor.');
        }
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return self::KEY.'='.self::$values[$this->freq];
    }
}
