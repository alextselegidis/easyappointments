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
 * Class Privacy
 *
 * @package Controllers
 */
class Privacy extends EA_Controller {
    /**
     * Privacy constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', ['adapter' => 'file']);
        $this->load->model('customers_model');
    }

    /**
     * Remove all customer data (including appointments from the system).
     */
    public function ajax_delete_personal_information()
    {
        try
        {
            $customer_token = $this->input->post('customer_token');

            if (empty($customer_token))
            {
                throw new InvalidArgumentException('Invalid customer token value provided.');
            }

            $customer_id = $this->cache->get('customer-token-' . $customer_token);

            if (empty($customer_id))
            {
                throw new InvalidArgumentException('Customer ID could not be found, please reload the page '
                    . 'and try again.');
            }

            $this->customers_model->delete($customer_id);

            $response = [
                'success' => TRUE
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
