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

use \EA\Engine\Types\NonEmptyString;

/**
 * API V1 Controller 
 *
 * Parent controller class for the API v1 resources. Extend this class instead of the CI_Controller
 * and call the parent constructor.
 *
 * @package Controllers
 * @subpackage API
 */
class API_V1_Controller extends CI_Controller {
    /**
     * Class Constructor 
     *
     * This constructor will handle the common operations of each API call. 
     *
     * Important: Do not forget to call the this constructor from the child classes.
     *
     * Notice: At the time being only the basic authentication is supported. Make sure 
     * that you use the API through SSL/TLS for security.
     */
    public function __construct() {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return $this->_requestAuthentication();
        }

        $username = new NonEmptyString($_SERVER['PHP_AUTH_USER']);
        $password = new NonEmptyString($_SERVER['PHP_AUTH_PW']);
        $authorization = new \EA\Engine\Api\V1\Authorization($this); 
        $authorization->basic($username, $password); 
        parent::__construct();
    }

    /**
     * Sets request authentication headers.
     */
    protected function _requestAuthentication() {
        header('WWW-Authenticate: Basic realm="Easy!Appointments"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You are not authorized to use the API.';
    }
}

/* End of file API_V1_Controller.php */
/* Location: ./application/controllers/API_V1_Controller.php */
