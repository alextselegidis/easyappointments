<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Errors controller.
 *
 * Handles the app error related operations.
 *
 * @package Controllers
 */
class Errors extends EA_Controller {
    /**
     * Errors constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('settings_model');
    }

    /**
     * Display the 404 error page.
     */
    public function index()
    {
        $this->error404();
    }

    /**
     * Display the 404 error page.
     */
    public function error404()
    {
        $this->load->view('general/error404', [
            'company_name' => setting('company_name')
        ]);
    }
}
