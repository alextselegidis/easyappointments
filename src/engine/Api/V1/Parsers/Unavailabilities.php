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

/**
 * Unavailabilities Parser 
 *
 * This class will handle the encoding and decoding from the API requests. 
 */
class Unavailabilities implements ParsersInterface {
    /**
     * Encode Response Array 
     * 
     * @param array &$response The response to be encoded.
     */
    public function encode(array &$response) {
        $encodedResponse = [
            'id' => $response['id'] !== null ? (int)$response['id'] : null,
            'book' => $response['book_datetime'],
            'start' => $response['start_datetime'],
            'end' => $response['end_datetime'],
            'notes' => $response['notes'],
            'providerId' => $response['id_users_provider'] !== null ? (int)$response['id_users_provider'] : null,
            'googleCalendarId' => $response['id_google_calendar'] !== null ? (int)$response['id_google_calendar'] : null
        ];

        $response = $encodedResponse; 
    }

    /**
     * Decode Request 
     * 
     * @param array &$request The request to be decoded. 
     * @param array $base Optional (null), if provided it will be used as a base array. 
     */
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

        if (!empty($request['notes'])) {
            $decodedRequest['notes'] = $request['notes']; 
        }

        if (!empty($request['providerId'])) {
            $decodedRequest['id_users_provider'] = $request['providerId']; 
        }

        if (!empty($request['googleCalendarId'])) {
            $decodedRequest['id_google_calendar'] = $request['googleCalendarId']; 
        }

        $decodedRequest['is_unavailable'] = true;

        $request = $decodedRequest; 
    }
}
