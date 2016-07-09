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

class Paginate implements ProcessorsInterface {
    public static function process(array &$response) {
        if (!isset($_GET['page'])) {
            return; 
        }

        $page = (int)$_GET['page']; 
        $length = isset($_GET['length']) ? (int)$_GET['length'] : 20; 

        $chunks = array_chunk($response, $length); 

        $response = isset($chunks[$page]) ? $chunks[$page] : [];
    }
}
