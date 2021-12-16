<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

/**
 * Update controller.
 *
 * Handles the update related operations.
 *
 * @package Controllers
 */
class Update extends EA_Controller {
    /**
     * Update constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('admins_model');
        $this->load->model('settings_model');
        $this->load->model('services_model');
        $this->load->model('providers_model');
        $this->load->model('customers_model');
        
        $this->load->library('instance');
    }

    /**
     * This method will update the instance to the latest available version in the server.
     *
     * IMPORTANT: The code files must exist in the server, this method will not fetch any new files but will update
     * the database schema.
     *
     * This method can be used either by loading the page in the browser or by an ajax request. But it will answer with
     * JSON encoded data.
     */
    public function index()
    {
        try
        {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS))
            {
                show_error('Forbidden', 403);
            }

            $this->instance->migrate();

            $view = ['success' => TRUE];
        }
        catch (Throwable $e)
        {
            $view = ['success' => FALSE, 'exception' => $e->getMessage()];
        }

        $this->load->view('pages/update', $view);
    }
}
