<?php

namespace Jsvrcek\ICS\Utility;

use Jsvrcek\ICS\Model\Description\Conference;

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
    public function getFormattedDateTimeWithTimeZone(\DateTime $dateTime) {
        return 'TZID=' . $dateTime->getTimezone()->getName() . ':' . self::getFormattedDateTime($dateTime);
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
        if (strpos($uri, '@') && stripos($uri, 'mailto:') === false) {
            $uri = 'mailto:'.$uri;
        }

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

        if ($interval->y) {
            $format .= '%yY';
        }
        if ($interval->m) {
            $format .= '%mM';
        }
        if ($interval->d) {
            $format .= '%dD';
        }

        if ($interval->h || $interval->i || $interval->s) {
            $format .= "T";
        }

        if ($interval->h) {
            $format .= '%hH';
        }
        if ($interval->i) {
            $format .= '%iM';
        }
        if ($interval->s) {
            $format .= '%sS';
        }

        return $interval->format($format);
    }

    /**
     * converts an image array into a correctly formatted string.
     * @param array $image
     * @return string
     */
    public function getFormattedImageString(array $image)
    {
        $imageString = 'IMAGE;VALUE='.$image['VALUE'];
        if ($image['VALUE'] == 'BINARY'){
            $imageString .= ';ENCODING=' . $image['ENCODING'];
        }

        if (isset($image['DISPLAY'])) {
            $imageString .= ';DISPLAY=' . $image['DISPLAY'];
        }
        if (isset($image['FMTTYPE'])) {
            $imageString .= ';FMTTYPE=' . $image['FMTTYPE'];
        }
        if ($image['VALUE'] == 'URI') {
            $imageString .= ':' . $image['URI'];
        } else {
            $imageString .= ':' . $image['BINARY'];
        }
        return $imageString;

    }

    /**
     * Escapes , and ; characters in text type fields.
     *
     * @param string $text The text to escape
     * @return string
     */
    public function getEscapedText($text)
    {
        if ($text)
        {
            $text = preg_replace('/((?<!\\\),|(?<!\\\);)/', '\\\$1', $text);
            $text = preg_replace('/((?<!\\\)\\\(?!,|;|n|N|\\\))/', '\\\\$1',$text);
        }

        return $text;
    }

    public function getConferenceText(Conference $conference)
    {
        $text = 'CONFERENCE;VALUE=URI';
        if ($conference->getFeature()) {
            $text .= ';FEATURE=' . $conference->getFeature();
        }
        if ($conference->getLabel()) {
            $text .= ';LABEL=' . $this->getEscapedText($conference->getLabel());
        }
        if ($conference->getLanguage()) {
            $text .= ';LANGUAGE=' . $this->getEscapedText($conference->getLanguage());
        }
        $text .= ':' . $conference->getUri();
        return $text;
    }
}
