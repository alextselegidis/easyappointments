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

class Minimize implements ProcessorsInterface {
    public static function process(array &$response) {
        if (!isset($_GET['fields'])) {
            return; 
        }

        $fields = explode(',', $_GET['fields']);

        $temporaryResponse = []; 

        foreach ($response as &$entry) {
            $temporaryEntry = []; 
            
            foreach ($fields as $field) {
                if (isset($entry[$field])) {
                    $temporaryEntry[$field] = $entry[$field]; 
                }
            } 
               
            $temporaryResponse[] = $temporaryEntry;
        }

        $response = $temporaryResponse;
    }
}
