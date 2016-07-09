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

namespace EA\Engine\Api\V1\Parsers; 

class Appointments implements ParsersInterface {
    public function encode(array &$response) {
        $encodedResponse = [
            'id' => $response['id'],
            'book' => $response['book_datetime'],
            'start' => $response['start_datetime'],
            'end' => $response['end_datetime'],
            'hash' => $response['hash'],
            'notes' => $response['notes'],
            'customerId' => $response['id_users_customer'],
            'providerId' => $response['id_users_provider'],
            'serviceId' => $response['id_services'],
            'googleCalendarId' => $response['id_google_calendar']
        ];

        $response = $encodedResponse; 
    }

    public function decode(array &$request, array $base = null) {
        $decodedRequest = $base ?: []; 

        if (!empty($request['id'])) {
            $decodedRequest['id'] = $request['id']; 
        }

        if (!empty($request['book'])) {
            $decodedRequest['book_datetime'] = $request['book']; 
        }

        if (!empty($request['start'])) {
            $decodedRequest['start_datetime'] = $request['start']; 
        }

        if (!empty($request['end'])) {
            $decodedRequest['end_datetime'] = $request['end']; 
        }

        if (!empty($request['hash'])) {
            $decodedRequest['hash'] = $request['hash']; 
        }

        if (!empty($request['notes'])) {
            $decodedRequest['notes'] = $request['notes']; 
        }

        if (!empty($request['customerId'])) {
            $decodedRequest['id_users_customer'] = $request['customerId']; 
        }

        if (!empty($request['providerId'])) {
            $decodedRequest['id_users_provider'] = $request['providerId']; 
        }

        if (!empty($request['serviceId'])) {
            $decodedRequest['id_services'] = $request['serviceId']; 
        }

        if (!empty($request['googleCalendarId'])) {
            $decodedRequest['id_google_calendar'] = $request['googleCalendarId']; 
        }

        $decodedRequest['is_unavailable'] = false;

        $request = $decodedRequest; 
    }
}
