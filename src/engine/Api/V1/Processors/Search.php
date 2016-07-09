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

class Search implements ProcessorsInterface {
    public static function process(array &$response) {
        if (!isset($_GET['q'])) {
            return;
        }

        $searchedResponse = []; 
        $keyword = (string)$_GET['q']; 

        foreach ($response as $entry) {
            if (self::_recursiveArraySearch($entry, $keyword) !== false) {
                $searchedResponse[] = $entry;
            }
        }

        $response = $searchedResponse;
    }

    protected static function _recursiveArraySearch(array $haystack, $needle) {
        foreach ($haystack as $key => $value) {
            $currentKey = $key;

            if ($needle === $value || (is_array($value) && self::_recursiveArraySearch($value, $needle) !== false)) {
                return $currentKey;
            }
        }

        return false;
    }
}
