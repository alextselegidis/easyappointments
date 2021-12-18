<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

/**
 * Consents controller.
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
     * Save (insert or update) the consent
     */
    public function ajax_save_consent()
    {
        try
        {
            $consent = request('consent');

            $consent['ip'] = $this->input->ip_address();

            $consent['id'] = $this->consents_model->save($consent);

            json_response([
                'success' => TRUE,
                'id' => $consent['id']
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
