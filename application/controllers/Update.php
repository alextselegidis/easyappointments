<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
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
class Update extends EA_Controller
{
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
     * Display the update confirmation page.
     *
     * The migration itself runs in the apply() method, which only answers to POST requests. Updating the database
     * schema is not something a plain GET may do, as that can be triggered from another website by pointing an image
     * or a link of it at this URL.
     */
    public function index(): void
    {
        if (!$this->authorize()) {
            return;
        }

        html_vars([
            'page_title' => lang('update'),
            'confirmation' => true,
        ]);

        $this->load->view('pages/update');
    }

    /**
     * This method will update the instance to the latest available version in the server.
     *
     * IMPORTANT: The code files must exist in the server, this method will not fetch any new files but will update
     * the database schema.
     *
     * Submit the form of the update page to reach this method, or post to it directly with a valid CSRF token.
     */
    public function apply(): void
    {
        if (!$this->authorize()) {
            return;
        }

        try {
            method('post');

            $this->instance->migrate();

            $view = ['success' => true];
        } catch (Throwable $e) {
            $view = ['success' => false, 'exception' => $e->getMessage()];
        }

        $view['page_title'] = lang('update');

        html_vars($view);

        $this->load->view('pages/update');
    }

    /**
     * Make sure the requesting user is allowed to update the instance.
     *
     * @return bool True when the request may continue, false when a response was already sent.
     */
    private function authorize(): bool
    {
        if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
            if (session('user_id')) {
                abort(403, 'Forbidden');
            }

            redirect('login');

            return false;
        }

        return true;
    }
}
