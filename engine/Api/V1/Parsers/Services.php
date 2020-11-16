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
 * Services Parser
 *
 * This class will handle the encoding and decoding from the API requests.
 *
 * @deprecated
 */
class Services implements ParsersInterface {
    /**
     * Encode Response Array
     *
     * @param array &$response The response to be encoded.
     */
    public function encode(array &$response)
    {
        $encoded_response = [
            'id' => $response['id'] !== NULL ? (int)$response['id'] : NULL,
            'name' => $response['name'],
            'duration' => (int)$response['duration'],
            'price' => (float)$response['price'],
            'currency' => $response['currency'],
            'description' => $response['description'],
            'location' => $response['location'],
            'availabilitiesType' => $response['availabilities_type'],
            'attendantsNumber' => (int)$response['attendants_number'],
            'categoryId' => $response['id_service_categories'] !== NULL ? (int)$response['id_service_categories'] : NULL
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
        $decoded_request = $base ?: [];

        if ( ! empty($request['id']))
        {
            $decoded_request['id'] = $request['id'];
        }

        if ( ! empty($request['name']))
        {
            $decoded_request['name'] = $request['name'];
        }

        if ( ! empty($request['duration']))
        {
            $decoded_request['duration'] = $request['duration'];
        }

        if ( ! empty($request['price']))
        {
            $decoded_request['price'] = $request['price'];
        }

        if ( ! empty($request['currency']))
        {
            $decoded_request['currency'] = $request['currency'];
        }

        if ( ! empty($request['description']))
        {
            $decoded_request['description'] = $request['description'];
        }

        if ( ! empty($request['location']))
        {
            $decoded_request['location'] = $request['location'];
        }

        if ( ! empty($request['availabilitiesType']))
        {
            $decoded_request['availabilities_type'] = $request['availabilitiesType'];
        }

        if ( ! empty($request['attendantsNumber']))
        {
            $decoded_request['attendants_number'] = $request['attendantsNumber'];
        }

        if ( ! empty($request['categoryId']))
        {
            $decoded_request['id_service_categories'] = $request['categoryId'];
        }

        $request = $decoded_request;
    }
}
