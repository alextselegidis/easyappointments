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
     * Appointments Resource Parser
     * 
     * @var \EA\Engine\Api\V1\Parsers\Appointments
     */
    protected $parser; 

    /**
     * Class Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('appointments_model');
        $this->parser = new \EA\Engine\Api\V1\Parsers\Appointments;
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
        $response->encode($this->parser)->search()->sort()->paginate()->minimize();

        if ($id !== null) {
            $response->singleEntry();
        }

        $response->output();
    }

    /**
     * POST API Method 
     */
    public function post() {
        $request = json_decode(file_get_contents('php://input'), true); 
        $this->parser->decode($request); 
        $id = $this->appointments_model->add($request);
        $appointments = $this->appointments_model->get_batch('id = ' . $id); 
        $response = new Response($appointments); 
        $status = new NonEmptyString('201 Created');
        $response->encode($this->parser)->singleEntry()->output($status); 
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
