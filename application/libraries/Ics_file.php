<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.0
 * ---------------------------------------------------------------------------- */

use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Model\Relationship\Organizer;
use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\CalendarExport;

/**
 * Class Ics_file
 *
 * An ICS file is a calendar file saved in a universal calendar format used by several email and calendar programs,
 * including Microsoft Outlook, Google Calendar, and Apple Calendar.
 *
 * Depends on the Jsvrcek\ICS composer package.
 */
class Ics_file {
    /**
     * Get the ICS file contents for the provided arguments.
     *
     * @param array $appointment Appointment.
     * @param array $service Service.
     * @param array $provider Provider.
     * @param array $customer Customer.
     *
     * @return string Returns the contents of the ICS file.
     */
    public function get_stream($appointment, $service, $provider, $customer)
    {
        $appointment_start = new DateTime($appointment['start_datetime']);
        $appointment_end = new DateTime($appointment['end_datetime']);

        // Setup the event.
        $event = new CalendarEvent();

        $event
            ->setStart($appointment_start)
            ->setEnd($appointment_end)
            ->setSummary($service['name'])
            ->setUid($appointment['id']);

        // Add the customer attendee.
        $attendee = new Attendee(new Formatter());

        if (isset($customer['email']) && ! empty($customer['email']))
        {
            $attendee->setValue($customer['email']);
        }


        $attendee->setName($customer['first_name'] . ' ' . $customer['last_name']);
        $event->addAttendee($attendee);

        // Set the organizer.
        $organizer = new Organizer(new Formatter());

        $organizer
            ->setValue($provider['email'])
            ->setName($provider['first_name'] . ' ' . $provider['last_name']);

        $event->setOrganizer($organizer);

        // Setup calendar.
        $calendar = new Calendar();

        $calendar
            ->setProdId('-//EasyAppointments//Open Source Web Scheduler//EN')
            ->setTimezone(new DateTimeZone(date_default_timezone_get()))
            ->addEvent($event);

        // Setup exporter.
        $calendarExport = new CalendarExport(new CalendarStream, new Formatter());
        $calendarExport->addCalendar($calendar);

        return $calendarExport->getStream();
    }
}
