<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

/**
 * Class Privacy
 *
 * @property CI_Session $session
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Config $config
 * @property CI_Lang $lang
 * @property CI_Cache $cache
 * @property CI_DB_query_builder $db
 * @property CI_Security $security
 * @property Google_Sync $google_sync
 * @property Ics_file $ics_file
 * @property Appointments_Model $appointments_model
 * @property Providers_Model $providers_model
 * @property Services_Model $services_model
 * @property Customers_Model $customers_model
 * @property Settings_Model $settings_model
 * @property Timezones $timezones
 * @property Roles_Model $roles_model
 * @property Secretaries_Model $secretaries_model
 * @property Admins_Model $admins_model
 * @property User_Model $user_model
 *
 * @package Controllers
 */
class Privacy extends CI_Controller {
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

            $this->load->driver('cache', ['adapter' => 'file']);

            $customer_id = $this->cache->get('customer-token-' . $customer_token);

            if (empty($customer_id))
            {
                throw new InvalidArgumentException('Customer ID could not be found, please reload the page '
                    . 'and try again.');
            }

            $this->load->model('customers_model');

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
