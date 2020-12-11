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
            'id' => array_key_exists('id', $response) ? (int)$response['id'] : NULL,
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
                'googleSync' => array_key_exists('google_sync', $response['settings'])
                    ? filter_var($response['settings']['google_sync'], FILTER_VALIDATE_BOOLEAN)
                    : NULL,
                'googleCalendar' => array_key_exists('google_calendar', $response['settings'])
                    ? $response['settings']['google_calendar']
                    : NULL,
                'googleToken' => array_key_exists('google_token', $response['settings'])
                    ? $response['settings']['google_token']
                    : NULL,
                'syncFutureDays' => array_key_exists('sync_future_days', $response['settings'])
                    ? (int)$response['settings']['sync_future_days']
                    : NULL,
                'syncPastDays' => array_key_exists('sync_past_days', $response['settings'])
                    ? (int)$response['settings']['sync_past_days']
                    : NULL,
                'workingPlan' => array_key_exists('working_plan', $response['settings'])
                    ? json_decode($response['settings']['working_plan'], TRUE)
                    : NULL,
                'workingPlanExceptions' => array_key_exists('working_plan_exceptions', $response['settings'])
                    ? json_decode($response['settings']['working_plan_exceptions'], TRUE)
                    : NULL,
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

        if (array_key_exists('id', $request))
        {
            $decoded_request['id'] = $request['id'];
        }

        if (array_key_exists('firstName', $request))
        {
            $decoded_request['first_name'] = $request['firstName'];
        }

        if (array_key_exists('lastName', $request))
        {
            $decoded_request['last_name'] = $request['lastName'];
        }

        if (array_key_exists('email', $request))
        {
            $decoded_request['email'] = $request['email'];
        }

        if (array_key_exists('mobile', $request))
        {
            $decoded_request['mobile_number'] = $request['mobile'];
        }

        if (array_key_exists('phone', $request))
        {
            $decoded_request['phone_number'] = $request['phone'];
        }

        if (array_key_exists('address', $request))
        {
            $decoded_request['address'] = $request['address'];
        }

        if (array_key_exists('city', $request))
        {
            $decoded_request['city'] = $request['city'];
        }

        if (array_key_exists('state', $request))
        {
            $decoded_request['state'] = $request['state'];
        }

        if (array_key_exists('zip', $request))
        {
            $decoded_request['zip_code'] = $request['zip'];
        }

        if (array_key_exists('notes', $request))
        {
            $decoded_request['notes'] = $request['notes'];
        }

        if (array_key_exists('timezone', $request))
        {
            $decoded_request['timezone'] = $request['timezone'];
        }

        if (array_key_exists('services', $request))
        {
            $decoded_request['services'] = $request['services'];
        }

        if (array_key_exists('settings', $request))
        {
            if (empty($decoded_request['settings']))
            {
                $decoded_request['settings'] = [];
            }

            if (array_key_exists('username', $request['settings']))
            {
                $decoded_request['settings']['username'] = $request['settings']['username'];
            }

            if (array_key_exists('password', $request['settings']))
            {
                $decoded_request['settings']['password'] = $request['settings']['password'];
            }

            if (array_key_exists('calendarView', $request['settings']))
            {
                $decoded_request['settings']['calendar_view'] = $request['settings']['calendarView'];
            }

            if (array_key_exists('notifications', $request['settings']))
            {
                $decoded_request['settings']['notifications'] = filter_var($request['settings']['notifications'],
                    FILTER_VALIDATE_BOOLEAN);
            }

            if (array_key_exists('googleSync', $request['settings']))
            {
                $decoded_request['settings']['google_sync'] = filter_var($request['settings']['googleSync'],
                    FILTER_VALIDATE_BOOLEAN);
            }

            if (array_key_exists('googleCalendar', $request['settings']))
            {
                $decoded_request['settings']['google_calendar'] = $request['settings']['googleCalendar'];
            }

            if (array_key_exists('googleToken', $request['settings']))
            {
                $decoded_request['settings']['google_token'] = $request['settings']['googleToken'];
            }

            if (array_key_exists('syncFutureDays', $request['settings']))
            {
                $decoded_request['settings']['sync_future_days'] = $request['settings']['syncFutureDays'];
            }

            if (array_key_exists('syncPastDays', $request['settings']))
            {
                $decoded_request['settings']['sync_past_days'] = $request['settings']['syncPastDays'];
            }

            if (array_key_exists('workingPlan', $request['settings']))
            {
                $decoded_request['settings']['working_plan'] = json_encode($request['settings']['workingPlan']);
            }

            if (array_key_exists('workingPlanExceptions', $request['settings']))
            {
                $decoded_request['settings']['working_plan_exceptions'] = json_encode($request['settings']['workingPlanExceptions']);
            }
        }

        $request = $decoded_request;
    }
}
