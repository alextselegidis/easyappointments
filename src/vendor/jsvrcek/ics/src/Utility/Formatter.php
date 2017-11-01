<?php

namespace Jsvrcek\ICS\Utility;

class Formatter
{
    const DATE_TIME = 'Ymd\THis';
    const DATE_TIME_UTC = 'Ymd\THis\Z';
    const DATE = 'Ymd';
    
    /**
     * @param \DateTime $dateTime
     * @return string
     */
    public function getFormattedDateTime(\DateTime $dateTime)
    {
        return $dateTime->format(self::DATE_TIME);
    }
    
    /**
     * @param int $offset
     * @return string
     */
    public function getFormattedTimeOffset($offset)
    {
        $prefix = ($offset < 0) ? '-' : '+';
        
        return $prefix.gmdate('Hi', abs($offset));
    }
    
    /**
     * @param \DateTime $dateTime
     * @return string
     */
    public function getFormattedUTCDateTime(\DateTime $dateTime)
    {
        return $dateTime->setTimezone(new \DateTimeZone('UTC'))
                    ->format(self::DATE_TIME_UTC);
    }

    /**
     * @param \DateTime $dateTime
     * @return string
     */
    public function getFormattedDate(\DateTime $dateTime)
    {
        return $dateTime->format(self::DATE);
    }
    
    /**
     * converts email addresses into mailto: uri
     * @param string $uri
     * @return string
     */
    public function getFormattedUri($uri)
    {
        if (strpos($uri, '@') && stripos($uri, 'mailto:') === false)
            $uri = 'mailto:'.$uri;
        
        return $uri;
    }

    /**
     * converts DateInterval object to string that can be used for a VALARM DURATION
     * @param \DateInterval $interval
     * @return string
     */
    public function getFormattedDateInterval(\DateInterval $interval)
    {
        $format = "P";

        if ($interval->y) { $format .= '%yY'; }
        if ($interval->m) { $format .= '%mM'; }
        if ($interval->d) { $format .= '%dD'; }

        if ($interval->h || $interval->i || $interval->s) {
            $format .= "T";
        }

        if ($interval->h) { $format .= '%hH'; }
        if ($interval->i) { $format .= '%iM'; }
        if ($interval->s) { $format .= '%sS'; }

        return $interval->format($format);
    }
}