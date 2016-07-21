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
 * Services Parser 
 *
 * This class will handle the encoding and decoding from the API requests. 
 */
class Services implements ParsersInterface {
    /**
     * Encode Response Array 
     * 
     * @param array &$response The response to be encoded.
     */
    public function encode(array &$response) {
        $encodedResponse = [
            'id' => $response['id'] !== null ? (int)$response['id'] : null,
            'name' => $response['name'],
            'duration' => (int)$response['duration'],
            'price' => (float)$response['price'],
            'currency' => $response['currency'],
            'description' => $response['description'],
            'availabilitiesType' => $response['availabilities_type'],
            'attendantsNumber' => (int)$response['attendants_number'],
            'categoryId' => $response['id_service_categories'] !== null ? (int)$response['id_service_categories'] : null
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

        if (!empty($request['name'])) {
            $decodedRequest['name'] = $request['name']; 
        }

        if (!empty($request['duration'])) {
            $decodedRequest['duration'] = $request['duration']; 
        }

        if (!empty($request['price'])) {
            $decodedRequest['price'] = $request['price']; 
        }

        if (!empty($request['currency'])) {
            $decodedRequest['currency'] = $request['currency']; 
        }

        if (!empty($request['description'])) {
            $decodedRequest['description'] = $request['description']; 
        }

        if (!empty($request['availabilitiesType'])) {
            $decodedRequest['availabilities_type'] = $request['availabilitiesType']; 
        }

        if (!empty($request['attendantsNumber'])) {
            $decodedRequest['attendants_number'] = $request['attendantsNumber']; 
        }

        if (!empty($request['categoryId'])) {
            $decodedRequest['id_service_categories'] = $request['categoryId']; 
        }

        $request = $decodedRequest; 
    }
}
