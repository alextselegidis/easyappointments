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
 * Providers Parser
 *
 * This class will handle the encoding and decoding from the API requests.
 *
 * @deprecated
 */
class Providers implements ParsersInterface {
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
            'timezone' => $response['timezone'],
        ];

        if (array_key_exists('services', $response))
        {
            $encoded_response['services'] = $response['services'];
        }

        if (array_key_exists('settings', $response))
        {
            $encoded_response['settings'] = [
                'username' => $response['settings']['username'],
                'notifications' => filter_var($response['settings']['notifications'], FILTER_VALIDATE_BOOLEAN),
                'calendarView' => $response['settings']['calendar_view'],
                'googleSync' => filter_var($response['settings']['google_sync'], FILTER_VALIDATE_BOOLEAN),
                'googleCalendar' => $response['settings']['google_calendar'],
                'googleToken' => $response['settings']['google_token'],
                'syncFutureDays' => $response['settings']['sync_future_days'] !== NULL ? (int)$response['settings']['sync_future_days'] : NULL,
                'syncPastDays' => $response['settings']['sync_past_days'] !== NULL ? (int)$response['settings']['sync_past_days'] : NULL,
                'workingPlan' => json_decode($response['settings']['working_plan'], TRUE),
                'workingPlanExceptions' => json_decode($response['settings']['working_plan_exceptions'], TRUE),
            ];
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

        if ( ! empty($request['timezone']))
        {
            $decoded_request['timezone'] = $request['timezone'];
        }

        if ( ! empty($request['services']))
        {
            $decoded_request['services'] = $request['services'];
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

            if ( ! empty($request['settings']['calendarView']))
            {
                $decoded_request['settings']['calendar_view'] = $request['settings']['calendarView'];
            }

            if ($request['settings']['notifications'] !== NULL)
            {
                $decoded_request['settings']['notifications'] = filter_var($request['settings']['notifications'],
                    FILTER_VALIDATE_BOOLEAN);
            }

            if ($request['settings']['googleSync'] !== NULL)
            {
                $decoded_request['settings']['google_sync'] = filter_var($request['settings']['googleSync'],
                    FILTER_VALIDATE_BOOLEAN);
            }

            if ( ! empty($request['settings']['googleCalendar']))
            {
                $decoded_request['settings']['google_calendar'] = $request['settings']['googleCalendar'];
            }

            if ( ! empty($request['settings']['googleToken']))
            {
                $decoded_request['settings']['google_token'] = $request['settings']['googleToken'];
            }

            if ( ! empty($request['settings']['syncFutureDays']))
            {
                $decoded_request['settings']['sync_future_days'] = $request['settings']['syncFutureDays'];
            }

            if ( ! empty($request['settings']['syncPastDays']))
            {
                $decoded_request['settings']['sync_past_days'] = $request['settings']['syncPastDays'];
            }

            if ( ! empty($request['settings']['workingPlan']))
            {
                $decoded_request['settings']['working_plan'] = json_encode($request['settings']['workingPlan']);
            }

            if ( ! empty($request['settings']['workingPlanExceptions']))
            {
                $decoded_request['settings']['working_plan_exceptions'] = json_encode($request['settings']['workingPlanExceptions']);
            }
        }

        $request = $decoded_request;
    }
}
