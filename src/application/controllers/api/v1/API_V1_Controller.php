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

use \EA\Engine\Types\NonEmptyText;

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

        parent::__construct();

        try {
            $username = new NonEmptyText($_SERVER['PHP_AUTH_USER']);
            $password = new NonEmptyText($_SERVER['PHP_AUTH_PW']);
            $authorization = new \EA\Engine\Api\V1\Authorization($this); 
            $authorization->basic($username, $password); 
        } catch(\Exception $exception) {
            exit($this->_handleException($exception)); 
        }
    }

    /**
     * Sets request authentication headers.
     */
    protected function _requestAuthentication() {
        header('WWW-Authenticate: Basic realm="Easy!Appointments"');
        header('HTTP/1.0 401 Unauthorized');
        exit('You are not authorized to use the API.');
    }

    /**
     * Outputs the required headers and messages for exception handling.
     *
     * Call this method from catch blocks of child controller callbacks.
     * 
     * @param \Exception $exception Thrown exception to be outputted.
     */
    protected function _handleException(\Exception $exception) {
        $error = [
            'code' => $exception->getCode() ?: 500,
            'message'=> $exception->getMessage(), 
        ];   

        $header = $exception instanceof \EA\Engine\Api\V1\Exception 
            ? $exception->getCode() . ' ' . $exception->getHeader() 
            : '500 Internal Server Error';

        header('HTTP/1.0 ' . $header);
        header('Content-Type: application/json');

        echo json_encode($error, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }

    /**
     * Throw an API exception stating that the requested record was not found. 
     * 
     * @throws \EA\Engine\Api\V1\Exception
     */
    protected function _throwRecordNotFound() {
        throw new \EA\Engine\Api\V1\Exception('The requested record was not found!', 404,  'Not Found');
    }
}

/* End of file API_V1_Controller.php */
/* Location: ./application/controllers/API_V1_Controller.php */
