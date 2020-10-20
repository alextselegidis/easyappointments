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
 * Filter Processor
 *
 * This class will handle custom filters upon the response array. In some specific cases it might be
 * easier to apply some custom filtering in order to get the required results.
 *
 * @todo Implement this processor class.
 *
 * @deprecated
 */
class Filter implements ProcessorsInterface {
    /**
     * Process Response Array
     *
     * @param array &$response The response array to be processed.
     */
    public static function process(array &$response)
    {
        // Not implemented yet.
    }
}
