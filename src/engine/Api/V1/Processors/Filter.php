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

namespace EA\Engine\Api\V1\Processors;

/**
 * Filter Processor
 *
 * This class will handle custom filters upon the response array. In some specific cases it might be
 * easier to apply some custom filtering in order to get the required results.
 *
 * @todo Implement this processor class.
 */
class Filter implements ProcessorsInterface
{
    /**
     * Process Response Array
     *
     * @param array &$response The response array to be processed.
     */
    public static function process(array &$response) {
        
        if ( empty($_GET) || empty($response)) {
            return;
        }

        // Get a list of columns in the table ea_appointments.
        $ci =& get_instance();
        $ci->load->database();
        $results = $ci->db->list_fields('ea_appointments');

        // copy all the values to keys for searching, remove everything after the _ in the table name
        foreach ($results as $value) {
            $value = ( strpos($value, '_') > 0 ) ? substr($value, 0, strpos($value, '_')) : $value;
            $table_columns[$value]=0;
        }

        // Get a list of matches
        $search_columns = array_intersect_key($table_columns, $_GET);

        $filteredResponse = [];

        // Search all keys for values
        foreach ($search_columns as $key => $value) {

            $column = (string)$key;
            $keyword = (string)strtolower(urldecode($_GET["$key"]));

            foreach ($response as $entry) {
                if (self::_recursiveArraySearch($entry, $keyword, $column) !== false) {
                    $filteredResponse[] = $entry;
                }
            }

        }

        $response = $filteredResponse;
    }

    /**
     * Recursive Array Search 
     * 
     * @param array $haystack Array to search in. 
     * @param string $needle Keyword to be searched.
     * 
     * @return int|bool Returns the index of the search occurrence or false it nothing was found.
     */
    protected static function _recursiveArraySearch(array $haystack, $needle, $column) {
        foreach ($haystack as $key => $value) {
            $currentKey = $key;

            if ( strcmp($currentKey, $column) === false || strpos(strtolower($value), $needle) !== false || (is_array($value) && self::_recursiveArraySearch($value, $needle, $column) !== false)) {
                return $currentKey;
            }
        }

        return false;
    }
}
