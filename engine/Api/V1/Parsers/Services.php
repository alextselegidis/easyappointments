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
            'id' => array_key_exists('id', $response) ? (int)$response['id'] : NULL,
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

        if (array_key_exists('id', $request))
        {
            $decoded_request['id'] = $request['id'];
        }

        if (array_key_exists('name', $request))
        {
            $decoded_request['name'] = $request['name'];
        }

        if (array_key_exists('duration', $request))
        {
            $decoded_request['duration'] = $request['duration'];
        }

        if (array_key_exists('price', $request))
        {
            $decoded_request['price'] = $request['price'];
        }

        if (array_key_exists('currency', $request))
        {
            $decoded_request['currency'] = $request['currency'];
        }

        if (array_key_exists('description', $request))
        {
            $decoded_request['description'] = $request['description'];
        }

        if (array_key_exists('location', $request))
        {
            $decoded_request['location'] = $request['location'];
        }

        if (array_key_exists('availabilitiesType', $request))
        {
            $decoded_request['availabilities_type'] = $request['availabilitiesType'];
        }

        if (array_key_exists('attendantsNumber', $request))
        {
            $decoded_request['attendants_number'] = $request['attendantsNumber'];
        }

        if (array_key_exists('categoryId', $request))
        {
            $decoded_request['id_service_categories'] = $request['categoryId'];
        }

        $request = $decoded_request;
    }
}
