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
 * Admins Parser
 *
 * This class will handle the encoding and decoding from the API requests.
 *
 * @deprecated
 */
class Admins implements ParsersInterface {
    /**
     * Encode Response Array
     *
     * @param array &$response The response to be encoded.
     */
    public function encode(array &$response)
    {
        $encoded_response = [
            'id' => $response['id'] !== NULL ? (int)$response['id'] : NULL,
            'firstName' => $response['first_name'],
            'lastName' => $response['last_name'],
            'email' => $response['email'],
            'mobile' => $response['mobile_number'],
            'phone' => $response['phone_number'],
            'address' => $response['address'],
            'city' => $response['city'],
            'state' => $response['state'],
            'zip' => $response['zip_code'],
            'notes' => $response['notes'],
            'settings' => [
                'username' => $response['settings']['username'],
                'notifications' => filter_var($response['settings']['notifications'], FILTER_VALIDATE_BOOLEAN),
                'calendarView' => $response['settings']['calendar_view']
            ]
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

        if ( ! empty($request['firstName']))
        {
            $decoded_request['first_name'] = $request['firstName'];
        }

        if ( ! empty($request['lastName']))
        {
            $decoded_request['last_name'] = $request['lastName'];
        }

        if ( ! empty($request['email']))
        {
            $decoded_request['email'] = $request['email'];
        }

        if ( ! empty($request['mobile']))
        {
            $decoded_request['mobile_number'] = $request['mobile'];
        }

        if ( ! empty($request['phone']))
        {
            $decoded_request['phone_number'] = $request['phone'];
        }

        if ( ! empty($request['address']))
        {
            $decoded_request['address'] = $request['address'];
        }

        if ( ! empty($request['city']))
        {
            $decoded_request['city'] = $request['city'];
        }

        if ( ! empty($request['state']))
        {
            $decoded_request['state'] = $request['state'];
        }

        if ( ! empty($request['zip']))
        {
            $decoded_request['zip_code'] = $request['zip'];
        }

        if ( ! empty($request['notes']))
        {
            $decoded_request['notes'] = $request['notes'];
        }

        if ( ! empty($request['settings']))
        {
            if (empty($decoded_request['settings']))
            {
                $decoded_request['settings'] = [];
            }

            if ( ! empty($request['settings']['username']))
            {
                $decoded_request['settings']['username'] = $request['settings']['username'];
            }

            if ( ! empty($request['settings']['password']))
            {
                $decoded_request['settings']['password'] = $request['settings']['password'];
            }

            if ($request['settings']['notifications'] !== NULL)
            {
                $decoded_request['settings']['notifications'] = filter_var($request['settings']['notifications'],
                    FILTER_VALIDATE_BOOLEAN);
            }

            if ( ! empty($request['settings']['calendarView']))
            {
                $decoded_request['settings']['calendar_view'] = $request['settings']['calendarView'];
            }
        }

        $request = $decoded_request;
    }
}
