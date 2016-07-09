<?php 

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Api\V1\Formatters; 

class Appointments implements FormattersInterface {
    public function format(array &$response) {
        $temporaryResponse = []; 

        foreach ($response as $entry) {
            $temporaryResponse[] = [
                'id' => $entry['id'],
                'book' => $entry['book_datetime'],
                'start' => $entry['start_datetime'],
                'end' => $entry['end_datetime'],
                'hash' => $entry['hash'],
                'notes' => $entry['notes'],
                'customerId' => $entry['id_users_customer'],
                'providerId' => $entry['id_users_provider'],
                'serviceId' => $entry['id_services'],
                'googleCalendarId' => $entry['id_google_calendar']
            ];
        }

        $response = $temporaryResponse; 
    }
}
