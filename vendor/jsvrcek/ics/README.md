[![Build status](https://travis-ci.org/jasvrcek/ICS.svg?branch=master)](https://travis-ci.org/jasvrcek/ICS)

ICS
===

Object-oriented php library for creating (and eventually reading) .ics iCal files.

* This project does not yet support all functionality of the .ics format.

## 1. Basic Usage

```php
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Model\Relationship\Organizer;

use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\CalendarExport;

//setup an event
$eventOne = new CalendarEvent();
$eventOne->setStart(new \DateTime())
	->setSummary('Family reunion')
	->setUid('event-uid');
	
//add an Attendee
$attendee = new Attendee(new Formatter());
$attendee->setValue('moe@example.com')
	->setName('Moe Smith');
$eventOne->addAttendee($attendee);

//set the Organizer
$organizer = new Organizer(new Formatter());
$organizer->setValue('heidi@example.com')
	->setName('Heidi Merkell')
	->setLanguage('de');
$eventOne->setOrganizer($organizer);

//new event
$eventTwo = new CalendarEvent();
$eventTwo->setStart(new \DateTime())
	->setSummary('Dentist Appointment')
	->setUid('event-uid');

//setup calendar
$calendar = new Calendar();
$calendar->setProdId('-//My Company//Cool Calendar App//EN')
	->addEvent($eventOne)
	->addEvent($eventTwo);

//setup exporter
$calendarExport = new CalendarExport(new CalendarStream, new Formatter());
$calendarExport->addCalendar($calendar);

//output .ics formatted text
echo $calendarExport->getStream();
```

## 2. Batch Event Provider

The basic usage example will build the complete .ics string in memory and then echo it all
at once. This will use a lot of memory for a large calendar. The following example shows 
how to make CalendarExport::getStream() output each line of the ics file as it is generated, as well as how to set a provider
for building the event list of a calendar in batches during export. 

```php
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;

use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\CalendarExport;

//setup calendar
$calendar = new Calendar();
$calendar->setProdId('-//My Company//Cool Calendar App//EN');

//setup event provider to add events in batches during event iteration in $calendarExport->getStream()
$calendar->setEventsProvider(function ($startKey) use ($myDatabase) {

	//pseudo-code to get a batch of events from database
	$eventDataArray = $myDatabase->getEventsBatch($startKey);
	
	$events = array();
	
	foreach ($eventDataArray as $row) {
		$event = new CalendarEvent();
		$event->setStart($row['start_date'])
			->setSummary($row['summary'])
			->setUid($row['event_uid']);
		
		$events[] = $event;
	}
	
	return $events;
}); 

//setup exporter
$calendarExport = new CalendarExport(new CalendarStream, new Formatter());
$calendarExport->addCalendar($calendar);

//set exporter to send items directly to output instead of storing in memory
$calendarExport->getStreamObject()->setDoImmediateOutput(true);

//output .ics formatted text
echo $calendarExport->getStream();
```

## Todos

* Jsvrcek\ICS\Model\CalendarTodo

## Reference
 
 * http://tools.ietf.org/html/rfc5545

## Credits

 * Alex Balhatchet at [kaoru](https://github.com/kaoru) implemented CalendarAlarm.
 * Thijs Wijnmaalen at [thijsw](https://github.com/thijsw/ics-large) provided inspiration on the batch provider code.

## License

The MIT License (MIT)

Copyright (c) 2022 Justin Svrcek

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
