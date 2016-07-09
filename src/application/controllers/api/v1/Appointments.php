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

use \EA\Engine\Api\V1\Response;
use \EA\Engine\Types\NonEmptyString; 

/**
 * Appointments Controller
 *
 * @package Controllers
 * @subpackage API
 */
class Appointments extends API_V1_Controller {
    /**
     * Appointments Resource Formatter
     * 
     * @var \EA\Engine\Api\V1\Formatters\Appointments
     */
    protected $formatter; 

    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('appointments_model');
        $this->formatter = new \EA\Engine\Api\V1\Formatters\Appointments;
    }

    /**
     * GET API Method 
     * 
     * @param int $id Optional (null), the record ID to be returned.
     */
    public function get($id = null) {
        $condition = $id !== null ? 'id = ' . $id : null;
        
        $appointments = $this->appointments_model->get_batch($condition); 
        
        $response = new Response($appointments); 

        $response->format($this->formatter)->search()->sort()->paginate()->minimize();

        if ($id !== null) {
            $response->singleEntry();
        }

        $response->output();
    }

    /**
     * POST API Method 
     */
    public function post() {
        
    }

    /**
     * PUT API Method 
     *
     * @param int $id The record ID to be updated.
     */
    public function put($id) {

    }

    /**
     * DELETE API Method 
     *
     * @param int $id The record ID to be deleted.
     */
    public function delete($id) {

    }
}

/* End of file Appointments.php */
/* Location: ./application/controllers/api/v1/Appointments.php */
