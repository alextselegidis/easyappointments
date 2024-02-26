<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/*
 * This file can only be used in a testing environment and only from the termninal.
 */

if (ENVIRONMENT !== 'testing' || !is_cli()) {
    show_404();
}

/**
 * Test controller.
 *
 * This controller does not have or need any logic, it is just used so that CI can be loaded properly during the test
 * execution.
 */
class Test extends EA_Controller
{
    /**
     * Placeholder callback.
     *
     * @return void
     */
    public function index(): void
    {
        //
    }
}
