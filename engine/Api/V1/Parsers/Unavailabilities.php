<?php

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Api\V1\Parsers;

/**
 * Unavailabilities Parser
 *
 * This class will handle the encoding and decoding from the API requests.
 *
 * @deprecated
 */
class Unavailabilities implements ParsersInterface {
    /**
     * Encode Response Array
     *
     * @param array &$response The response to be encoded.
     */
    public function encode(array &$response)
    {
        $encoded_response = [
            'id' => array_key_exists('id', $response) ? (int)$response['id'] : NULL,
            'book' => $response['book_datetime'],
            'start' => $response['start_datetime'],
            'end' => $response['end_datetime'],
            'notes' => $response['notes'],
            'providerId' => array_key_exists('id_users_provider', $response)
                ? (int)$response['id_users_provider']
                : NULL,
            'googleCalendarId' => array_key_exists('id_google_calendar', $response)
                ? (int)$response['id_google_calendar']
                : NULL
        ];

        $response = $encoded_response;
    }

    /**
     * Decode Request
     *
     * @param array &$request The request to be decoded.
     * @param array $base Optional (null), if provided it will be used as a base array.
     */
    public function decode(array &$request, array $base = NULL)
    {
        $decodedRequest = $base ?: [];

        if (array_key_exists('id', $request))
        {
            $decodedRequest['id'] = $request['id'];
        }

        if (array_key_exists('book', $request))
        {
            $decodedRequest['book_datetime'] = $request['book'];
        }

        if (array_key_exists('start', $request))
        {
            $decodedRequest['start_datetime'] = $request['start'];
        }

        if (array_key_exists('end', $request))
        {
            $decodedRequest['end_datetime'] = $request['end'];
        }

        if (array_key_exists('notes', $request))
        {
            $decodedRequest['notes'] = $request['notes'];
        }

        if (array_key_exists('providerId', $request))
        {
            $decodedRequest['id_users_provider'] = $request['providerId'];
        }

        if (array_key_exists('googleCalendarId', $request))
        {
            $decodedRequest['id_google_calendar'] = $request['googleCalendarId'];
        }

        $decodedRequest['is_unavailable'] = TRUE;

        $request = $decodedRequest;
    }
}
