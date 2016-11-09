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
 * Categories Parser 
 *
 * This class will handle the encoding and decoding from the API requests. 
 */
class Categories implements ParsersInterface {
    /**
     * Encode Response Array 
     * 
     * @param array &$response The response to be encoded.
     */
    public function encode(array &$response) {
        $encodedResponse = [
            'id' => $response['id'] !== null ? (int)$response['id'] : null,
            'name' => $response['name'],
            'description' => $response['description']
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

        if (!empty($request['description'])) {
            $decodedRequest['description'] = $request['description']; 
        }

        $request = $decodedRequest; 
    }
}
