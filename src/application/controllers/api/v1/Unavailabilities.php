<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

require_once __DIR__ . '/API_V1_Controller.php';

/**
 * Unavailabilities Controller
 *
 * @package Controllers
 * @subpackage API
 */
class Unavailabilities extends API_V1_Controller {
    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * GET API Method 
     * 
     * @param int $id Optional (null), the record ID to be returned.
     * 
     * @return \EA\Engine\Api\V1\Response Returns data response. 
     */
    public function get($id = null) {
        
    }

    /**
     * POST API Method 
     * 
     * @return @return \EA\Engine\Api\V1\Response Returns data response. 
     */
    public function post() {
        
    }

    /**
     * PUT API Method 
     *
     * @param int $id The record ID to be updated.
     * 
     * @return @return \EA\Engine\Api\V1\Response Returns data response. 
     */
    public function put($id) {

    }

    /**
     * DELETE API Method 
     *
     * @param int $id The record ID to be deleted.
     * 
     * @return @return \EA\Engine\Api\V1\Response Returns data response. 
     */
    public function delete($id) {

    }
}

/* End of file Unavailabilities.php */
/* Location: ./application/controllers/api/v1/Unavailabilities.php */
