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

namespace EA\Engine\Api\V1; 

/**
 * Request Class 
 *
 * This class handles the common request handling before the data are manipulated and 
 * returned back with the Response class. 
 */
class Request {
    /**
     * Get request body as an associative array. 
     * 
     * @return array
     */
    public function getBody() {
        return json_decode(file_get_contents('php://input'), true);
    }
}
