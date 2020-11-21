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

namespace EA\Engine\Api\V1\Processors;

/**
 * Minimize Processor
 *
 * This processor will check for the "fields" GET parameters and provide only the required fields in
 * every response entry. This might come in handy when the client needs specific information and not
 * the whole objects.
 *
 * Make sure that the response parameter is a sequential array and not a single entry by the time this
 * processor is executed.
 *
 * @deprecated
 */
class Minimize implements ProcessorsInterface {
    /**
     * Process Response Array
     *
     * Example:
     *   http://ea-installation.com/api/v1/appointments?fields=id,book,start,end
     *
     *
     * @param array &$response The response array to be processed.
     */
    public static function process(array &$response)
    {
        if ( ! isset($_GET['fields']) || empty($response))
        {
            return;
        }

        $fields = explode(',', $_GET['fields']);

        $temporary_response = [];

        foreach ($response as &$entry)
        {
            $temporary_entry = [];

            foreach ($fields as $field)
            {
                $field = trim($field);
                if (isset($entry[$field]))
                {
                    $temporary_entry[$field] = $entry[$field];
                }
            }

            $temporary_response[] = $temporary_entry;
        }

        $response = $temporary_response;
    }
}
