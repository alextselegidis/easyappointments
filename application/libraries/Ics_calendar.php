<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.3
 * ---------------------------------------------------------------------------- */

use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\CalendarFreeBusy;
use Jsvrcek\ICS\Model\CalendarTodo;
use Jsvrcek\ICS\Utility\Provider;

/**
 * Class Ics_calendar
 *
 * This class replaces the Jsvrcek\ICS\Model\Calendar so that it uses the new Ics_provider instances, which is
 * compatible to PHP 8.1.
 *
 * There is no other change to the original file.
 */
class Ics_calendar extends Calendar
{
    /**
     * @var string
     */
    private $version = '2.0';

    /**
     * @var string
     */
    private $prodId = '';

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $calendarScale = 'GREGORIAN';

    /**
     * @var string
     */
    private $method = 'PUBLISH';

    /**
     * @var array
     */
    private $image = [];

    /**
     * @var array
     */
    private $customHeaders = [];

    /**
     * @var DateTimeZone
     */
    private $timezone;

    /**
     * @var Provider
     */
    private $events;

    /**
     * @var Provider
     */
    private $todos;

    /**
     * @var Provider
     */
    private $freeBusy;

    /**
     * @var string
     */
    private $color;

    /**
     * Calendar constructor.
     */
    public function __construct()
    {
        $this->timezone = new DateTimeZone('America/New_York');
        $this->events = new Ics_provider();
        $this->todos = new Ics_provider();
        $this->freeBusy = new Ics_provider();
    }

    /**
     * For use if you want CalendarExport::getStream to get events in batches from a database during
     * the output of the ics feed, instead of adding all events to the Calendar object before outputting
     * the ics feed.
     * - CalendarExport::getStream iterates through the Calendar::$events internal data array. The $eventsProvider
     *     closure will be called every time this data array reaches its end during iteration, and the closure should
     *     return the next batch of events
     * - A $startKey argument with the current key of the data array will be passed to the $eventsProvider closure
     * - The $eventsProvider must return an array of CalendarEvent objects
     *
     *  Example: Calendar::setEventsProvider(function($startKey){
     *      //get database rows starting with $startKey
     *      //return an array of CalendarEvent objects
     *  })
     *
     * @param Closure $eventsProvider
     * @return Calendar
     */
    public function setEventsProvider(Closure $eventsProvider)
    {
        $this->events = new Ics_provider($eventsProvider);
        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return Calendar
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getProdId()
    {
        return $this->prodId;
    }

    /**
     * @param string $prodId
     * @return Calendar
     */
    public function setProdId($prodId)
    {
        $this->prodId = $prodId;
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
     * Sets the RFC-7986 "Name" field for the calendar
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCalendarScale()
    {
        return $this->calendarScale;
    }

    /**
     * @param string $calendarScale
     * @return Calendar
     */
    public function setCalendarScale($calendarScale)
    {
        $this->calendarScale = $calendarScale;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return Calendar
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Images can come in one of two formats:
     *    1: URI - where a URI to the relevant image is provided
     *    2: BINARY - Where a Binary representation of the image is provided, normally Base64 Encoded.
     *
     * If sending a URI for the image, set the "VALUE" key to be "URI" and provide a URI key with the relevant URI.
     * IE:
     *     $calendar->setImage(
     *         'VALUE' => 'URL',
     *         'URI' => 'https://some.domain.com/path/to/image.jpg'
     *     );
     * It is optional to add a FMTTYPE key as well in the array, to indicate relevant mime type.
     * IE: 'FMTTYPE' => 'image/jpg'
     *
     * When sending Binary version, you must provide the encoding type of the image, as well as the encoded string.
     * IE:
     *    $calendar->setImage(
     *        'VALUE' => 'BINARY',
     *        'ENCODING' => 'BASE64',
     *        'BINARY' => $base64_encoded_string
     *    );
     * For Binary, it is RECOMMENDED to add the FMTTYPE as well, but still not REQUIRED
     *
     * @param array $image
     */
    public function setImage($image)
    {
        // Do some validation on provided data.
        if (array_key_exists('VALUE', $image) && in_array($image['VALUE'], ['URI', 'BINARY'])) {
            if ($image['VALUE'] == 'URI' && $image['URI']) {
                $new_image = [
                    'VALUE' => 'URI',
                    'URI' => $image['URI'],
                ];
            } elseif ($image['VALUE'] == 'BINARY' && $image['ENCODING'] && $image['BINARY']) {
                $new_image = [
                    'VALUE' => 'BINARY',
                    'ENCODING' => $image['ENCODING'],
                    'BINARY' => $image['BINARY'],
                ];
            } else {
                return;
            }
            $new_image['DISPLAY'] = isset($image['DISPLAY']) ? $image['DISPLAY'] : '';
            $new_image['FMTTYPE'] = isset($image['FMTTYPE']) ? $image['FMTTYPE'] : '';
            $this->image = $new_image;
        }
    }

    /**
     * @return array
     */
    public function getCustomHeaders()
    {
        return $this->customHeaders;
    }

    /**
     * use to add custom headers as array key-value pairs<br>
     * <strong>Example:</strong> $customHeaders = array('X-WR-TIMEZONE' => 'America/New_York')
     *
     * @param array $customHeaders
     * @return Calendar
     */
    public function setCustomHeaders(array $customHeaders)
    {
        $this->customHeaders = $customHeaders;
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return Calendar
     */
    public function addCustomHeader($key, $value)
    {
        $this->customHeaders[$key] = $value;
        return $this;
    }

    /**
     * @return DateTimeZone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param DateTimeZone $timezone
     * @return Calendar
     */
    public function setTimezone(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @return Provider
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param CalendarEvent $event
     * @return Calendar
     */
    public function addEvent(CalendarEvent $event)
    {
        $this->events->add($event);
        return $this;
    }

    /**
     * @return Provider returs array of CalendarTodo objects
     */
    public function getTodos()
    {
        return $this->todos;
    }

    /**
     * @param array $todos
     * @return Calendar
     */
    public function setTodos(array $todos)
    {
        $this->todos = $todos;
        return $this;
    }

    /**
     * @param CalendarTodo $todo
     * @return Calendar
     */
    public function addTodo(CalendarTodo $todo)
    {
        $this->todos[] = $todo;
        return $this;
    }

    /**
     * @return Provider returs array of CalendarFreeBusy objects
     */
    public function getFreeBusy()
    {
        return $this->freeBusy;
    }

    /**
     * @param array $freeBusy
     * @return Calendar
     */
    public function setFreeBusy(array $freeBusy)
    {
        $this->freeBusy = $freeBusy;
        return $this;
    }

    /**
     * @param CalendarFreeBusy $todo
     * @return Calendar
     */
    public function addFreeBusy(CalendarFreeBusy $todo)
    {
        $this->freeBusy[] = $todo;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set color as CSS3 string
     *
     * @param string $color
     * @return Calendar
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }
}
