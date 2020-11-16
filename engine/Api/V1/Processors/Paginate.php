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
 * Paginate Processor
 *
 * This class will handle the pagination GET parameters ("page", "length"). The "page" parameter
 * is required in order for the pagination to work. The "length" is optional and the default value
 * is 20 entries per page.
 *
 * Make sure that the response parameter is a sequential array and not a single entry by the time this
 * processor is executed.
 *
 * @deprecated
 */
class Paginate implements ProcessorsInterface {
    /**
     * Process Response Array
     *
     * Example:
     *   http://example.org/api/v1/appointments?page=3&length=30
     *
     * @param array &$response The response array to be processed.
     */
    public static function process(array &$response)
    {
        if ( ! isset($_GET['page']) || empty($response))
        {
            return;
        }

        $page = (int)$_GET['page'];
        $length = isset($_GET['length']) ? (int)$_GET['length'] : 20;

        $chunks = array_chunk($response, $length);

        $response = isset($chunks[$page - 1]) ? $chunks[$page - 1] : [];
    }
}
