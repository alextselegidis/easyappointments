<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Logout controller.
 *
 * Handles the logout page functionality.
 *
 * @package Controllers
 */
class Logout extends EA_Controller
{
    /**
     * Render the logout page.
     */
    public function index(): void
    {
        $this->session->sess_destroy();

        $company_name = setting('company_name');

        html_vars([
            'page_title' => lang('log_out'),
            'company_name' => $company_name,
        ]);

        $this->load->view('pages/logout');
    }
}
