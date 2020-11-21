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
 * Search Processor
 *
 * This class will search the response with the "q" GET parameter and only provide the entries
 * that match the keyword. Make sure that the response parameter is a sequential array and not
 * a single entry by the time this processor is executed.
 *
 * @deprecated
 */
class Search implements ProcessorsInterface {
    /**
     * Process Response Array
     *
     * @param array &$response The response array to be processed.
     */
    public static function process(array &$response)
    {
        if ( ! isset($_GET['q']) || empty($response))
        {
            return;
        }

        $searched_response = [];
        $keyword = (string)$_GET['q'];

        foreach ($response as $entry)
        {
            if (self::recursive_array_search($entry, $keyword) !== FALSE)
            {
                $searched_response[] = $entry;
            }
        }

        $response = $searched_response;
    }

    /**
     * Recursive Array Search
     *
     * @param array $haystack Array to search in.
     * @param string $needle Keyword to be searched.
     *
     * @return int|bool Returns the index of the search occurrence or false it nothing was found.
     */
    protected static function recursive_array_search(array $haystack, $needle)
    {
        foreach ($haystack as $key => $value)
        {
            $currentKey = $key;

            if (is_array($value) && self::recursive_array_search($value, $needle) !== FALSE)
            {
                return $currentKey;
            }

            if (is_string($value) && stripos($value, $needle) !== FALSE)
            {
                return $currentKey;
            }
        }

        return FALSE;
    }
}
