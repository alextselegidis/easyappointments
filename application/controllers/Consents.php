<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

/**
 * Class Consent
 *
 * Handles user consent related operations.
 *
 * @package Controllers
 */
class Consents extends EA_Controller {
    /**
     * Consents constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('consents_model');
    }

    /**
     * Save the user's consent.
     */
    public function ajax_save_consent()
    {
        try
        {
            $consent = $this->input->post('consent');

            $consent['ip'] = $this->input->ip_address();

            $consent['id'] = $this->consents_model->add($consent);

            $response = [
                'success' => TRUE,
                'id' => $consent['id']
            ];
        }
        catch (Exception $exception)
        {
            $this->output->set_status_header(500);

            $response = [
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
