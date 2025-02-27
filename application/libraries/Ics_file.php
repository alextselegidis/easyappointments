<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.3.0
 * ---------------------------------------------------------------------------- */

use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Exception\CalendarEventException;
use Jsvrcek\ICS\Model\CalendarAlarm;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Description\Location;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Model\Relationship\Organizer;
use Jsvrcek\ICS\Utility\Formatter;

/**
 * Ics file library.
 *
 * Handle ICS related functionality.
 *
 * An ICS file is a calendar file saved in a universal calendar format used by many email and calendar programs,
 * including Microsoft Outlook, Google Calendar, and Apple Calendar.
 *
 * @package Libraries
 */
class Ics_file
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * Availability constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->library('ics_provider');
        $this->CI->load->library('ics_calendar');
    }

    /**
     * Get the ICS file contents for the provided arguments.
     *
     * @param array $appointment Appointment data.
     * @param array $service Service data.
     * @param array $provider Provider data.
     * @param array $customer Customer data.
     *
     * @return string Returns the contents of the ICS file.
     *
     * @throws CalendarEventException
     * @throws Exception
     */
    public function get_stream(array $appointment, array $service, array $provider, array $customer): string
    {
        $appointment_timezone = new DateTimeZone($provider['timezone']);

        $appointment_start = new DateTime($appointment['start_datetime'], $appointment_timezone);

        $appointment_end = new DateTime($appointment['end_datetime'], $appointment_timezone);

        // Set up the event.
        $event = new CalendarEvent();

        $event
            ->setStart($appointment_start)
            ->setEnd($appointment_end)
            ->setStatus('CONFIRMED')
            ->setSummary($service['name'])
            ->setUid($appointment['id_caldav_calendar'] ?: $this->generate_uid($appointment['id']));

        if (!empty($service['location'])) {
            $location = new Location();
            $location->setName((string) $service['location']);
            $event->addLocation($location);
        }

        $description = [
            '',
            lang('provider'),
            '',
            lang('name') . ': ' . $provider['first_name'] . ' ' . $provider['last_name'],
            lang('email') . ': ' . $provider['email'],
            lang('phone_number') . ': ' . $provider['phone_number'],
            lang('address') . ': ' . $provider['address'],
            lang('city') . ': ' . $provider['city'],
            lang('zip_code') . ': ' . $provider['zip_code'],
            '',
            lang('customer'),
            '',
            lang('name') . ': ' . $customer['first_name'] . ' ' . $customer['last_name'],
            lang('email') . ': ' . $customer['email'],
            lang('phone_number') . ': ' . ($customer['phone_number'] ?? '-'),
            lang('address') . ': ' . $customer['address'],
            lang('city') . ': ' . $customer['city'],
            lang('zip_code') . ': ' . $customer['zip_code'],
            '',
            lang('notes'),
            '',
            $appointment['notes'],
        ];

        $event->setDescription(implode("\\n", $description));

        $attendee = new Attendee(new Formatter());

        if (isset($customer['email']) && !empty($customer['email'])) {
            $attendee->setValue($customer['email']);
        }

        // Add the event attendees.
        $attendee->setName($customer['first_name'] . ' ' . $customer['last_name']);
        $attendee
            ->setCalendarUserType('INDIVIDUAL')
            ->setRole('REQ-PARTICIPANT')
            ->setParticipationStatus('NEEDS-ACTION')
            ->setRsvp('TRUE');
        $event->addAttendee($attendee);

        $alarm = new CalendarAlarm();
        $alarm_datetime = clone $appointment_start;
        $alarm->setTrigger($alarm_datetime->modify('-15 minutes'));
        $alarm->setSummary('Alarm notification');
        $alarm->setDescription('This is an event reminder');
        $alarm->setAction('EMAIL');
        $alarm->addAttendee($attendee);
        $event->addAlarm($alarm);

        $alarm = new CalendarAlarm();
        $alarm_datetime = clone $appointment_start;
        $alarm->setTrigger($alarm_datetime->modify('-60 minutes'));
        $alarm->setSummary('Alarm notification');
        $alarm->setDescription('This is an event reminder');
        $alarm->setAction('EMAIL');
        $alarm->addAttendee($attendee);
        $event->addAlarm($alarm);

        $attendee = new Attendee(new Formatter());

        if (isset($provider['email']) && !empty($provider['email'])) {
            $attendee->setValue($provider['email']);
        }

        $attendee->setName($provider['first_name'] . ' ' . $provider['last_name']);
        $attendee
            ->setCalendarUserType('INDIVIDUAL')
            ->setRole('REQ-PARTICIPANT')
            ->setParticipationStatus('ACCEPTED')
            ->setRsvp('FALSE');
        $event->addAttendee($attendee);

        // Set the organizer.
        $organizer = new Organizer(new Formatter());

        $organizer->setValue($provider['email'])->setName($provider['first_name'] . ' ' . $provider['last_name']);

        $event->setOrganizer($organizer);

        // Setup calendar.
        $calendar = new Ics_calendar();

        $calendar
            ->setProdId('-//EasyAppointments//Open Source Web Scheduler//EN')
            ->setTimezone(new DateTimeZone($provider['timezone']))
            ->addEvent($event);

        // Setup exporter.
        $calendarExport = new CalendarExport(new CalendarStream(), new Formatter());
        $calendarExport->addCalendar($calendar);

        return $calendarExport->getStream();
    }

    public function get_unavailability_stream(array $unavailability, array $provider): string
    {
        $unavailability_timezone = new DateTimeZone($provider['timezone']);

        $unavailability_start = new DateTime($unavailability['start_datetime'], $unavailability_timezone);

        $unavailability_end = new DateTime($unavailability['end_datetime'], $unavailability_timezone);

        // Set up the event.
        $event = new CalendarEvent();

        $event
            ->setStart($unavailability_start)
            ->setEnd($unavailability_end)
            ->setStatus('CONFIRMED')
            ->setSummary('Unavailability')
            ->setUid($unavailability['id_caldav_calendar'] ?: $this->generate_uid($unavailability['id']));

        $event->setDescription(str_replace("\n", "\\n", (string) $unavailability['notes']));

        // Set the organizer.
        $organizer = new Organizer(new Formatter());

        $organizer->setValue($provider['email'])->setName($provider['first_name'] . ' ' . $provider['last_name']);

        $event->setOrganizer($organizer);

        // Setup calendar.
        $calendar = new Ics_calendar();

        $calendar
            ->setProdId('-//EasyAppointments//Open Source Web Scheduler//EN')
            ->setTimezone(new DateTimeZone($provider['timezone']))
            ->addEvent($event);

        // Setup exporter.
        $calendarExport = new CalendarExport(new CalendarStream(), new Formatter());
        $calendarExport->addCalendar($calendar);

        return $calendarExport->getStream();
    }

    public function generate_uid(int $db_record_id): string
    {
        return 'ea-' . md5($db_record_id);
    }
}
