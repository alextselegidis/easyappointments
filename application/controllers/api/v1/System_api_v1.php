<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.6.1
 * ---------------------------------------------------------------------------- */

/**
 * Settings API v1 controller.
 *
 * @package Controllers
 */
class System_api_v1 extends EA_Controller
{
    /**
     * System_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');

        $this->api->auth();
    }

    /**
     * Get a system collection.
     */
    public function index(): void
    {
        try {
            $response = [
                'version' => config('version')
            ];

            json_response($response);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
