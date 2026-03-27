<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

if (!function_exists('appointment_virtual_meeting_text')) {
    /**
     * Combined virtual-meeting text for emails and ICS: Google Meet link (if synced) plus provider notes.
     *
     * @param array $appointment Must include optional google_meet_link.
     * @param array $provider Provider row; optional notes (e.g. manual Zoom link).
     */
    function appointment_virtual_meeting_text(array $appointment, array $provider): string
    {
        $parts = array_filter(
            [
                trim((string) ($appointment['google_meet_link'] ?? '')),
                trim((string) ($provider['notes'] ?? '')),
            ],
            static fn(string $s): bool => $s !== '',
        );

        return implode("\n\n", $parts);
    }
}
