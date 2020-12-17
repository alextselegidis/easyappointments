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
 * Appointments Parser
 *
 * This class will handle the encoding and decoding from the API requests.
 *
 * @deprecated
 */
class Appointments implements ParsersInterface {
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
            'hash' => $response['hash'],
            'location' => $response['location'],
            'notes' => $response['notes'],
            'customerId' => $response['id_users_customer'] !== NULL ? (int)$response['id_users_customer'] : NULL,
            'providerId' => $response['id_users_provider'] !== NULL ? (int)$response['id_users_provider'] : NULL,
            'serviceId' => $response['id_services'] !== NULL ? (int)$response['id_services'] : NULL,
            'googleCalendarId' => $response['id_google_calendar'] !== NULL ? (int)$response['id_google_calendar'] : NULL
        ];

        if (isset($response['provider']))
        {
            $provider_parser = new Providers();
            $provider_parser->encode($response['provider']);
            $encoded_response['provider'] = $response['provider'];
        }

        if (isset($response['customer']))
        {
            $customer_parser = new Customers();
            $customer_parser->encode($response['customer']);
            $encoded_response['customer'] = $response['customer'];
        }

        if (isset($response['service']))
        {
            $service_parser = new Services();
            $service_parser->encode($response['service']);
            $encoded_response['service'] = $response['service'];
        }

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
        $decoded_request = $base ?: [];

        if (array_key_exists('id', $request))
        {
            $decoded_request['id'] = $request['id'];
        }

        if (array_key_exists('book', $request))
        {
            $decoded_request['book_datetime'] = $request['book'];
        }

        if (array_key_exists('start', $request))
        {
            $decoded_request['start_datetime'] = $request['start'];
        }

        if (array_key_exists('end', $request))
        {
            $decoded_request['end_datetime'] = $request['end'];
        }

        if (array_key_exists('hash', $request))
        {
            $decoded_request['hash'] = $request['hash'];
        }

        if (array_key_exists('location', $request))
        {
            $decoded_request['location'] = $request['location'];
        }

        if (array_key_exists('notes', $request))
        {
            $decoded_request['notes'] = $request['notes'];
        }

        if (array_key_exists('customerId', $request))
        {
            $decoded_request['id_users_customer'] = $request['customerId'];
        }

        if (array_key_exists('providerId', $request))
        {
            $decoded_request['id_users_provider'] = $request['providerId'];
        }

        if (array_key_exists('serviceId', $request))
        {
            $decoded_request['id_services'] = $request['serviceId'];
        }

        if (array_key_exists('googleCalendarId', $request))
        {
            $decoded_request['id_google_calendar'] = $request['googleCalendarId'];
        }

        $decoded_request['is_unavailable'] = FALSE;

        $request = $decoded_request;
    }
}
