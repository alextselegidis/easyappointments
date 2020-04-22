<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.1.0
 * ---------------------------------------------------------------------------- */

/**
 * Installation Controller
 *
 * This controller will handle the installation procedure of Easy!Appointments.
 *
 * @property CI_Session session
 * @property CI_Loader load
 * @property CI_Input input
 * @property CI_Output output
 * @property CI_Config config
 * @property CI_Lang lang
 * @property CI_Cache cache
 * @property CI_DB_query_builder db
 * @property CI_Security security
 * @property Google_Sync google_sync
 * @property Ics_file ics_file
 * @property Appointments_Model appointments_model
 * @property Providers_Model providers_model
 * @property Services_Model services_model
 * @property Customers_Model customers_model
 * @property Settings_Model settings_model
 * @property Timezones_Model timezones_model
 * @property Roles_Model roles_model
 * @property Secretaries_Model secretaries_model
 * @property Admins_Model admins_model
 * @property User_Model user_model
 *
 * @package Controllers
 */
class Installation extends CI_Controller {
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('installation');
        $this->load->library('session');

        if ($this->session->userdata('language'))
        {
            // Set user's selected language.
            $this->config->set_item('language', $this->session->userdata('language'));
            $this->lang->load('translations', $this->session->userdata('language'));
        }
        else
        {
            // Set the default language.
            $this->lang->load('translations', $this->config->item('language'));
        }
    }

    /**
     * Display the installation page.
     */
    public function index()
    {
        if (is_ea_installed())
        {
            redirect('appointments/index');
            return;
        }

        $this->load->view('general/installation', [
            'base_url' => $this->config->item('base_url')
        ]);
    }

    /**
     * [AJAX] Installs Easy!Appointments on the server.
     */
    public function ajax_install()
    {
        try
        {
            if (is_ea_installed())
            {
                return;
            }

            $this->load->model('admins_model');
            $this->load->model('settings_model');
            $this->load->model('services_model');
            $this->load->model('providers_model');
            $this->load->library('session');

            $admin = $this->input->post('admin');
            $company = $this->input->post('company');


            // Create the Easy!Appointments database structure.
            $file_contents = file_get_contents(__DIR__ . '/../../assets/sql/structure.sql');
            $sql_queries = explode(';', $file_contents);
            array_pop($sql_queries);
            foreach ($sql_queries as $query)
            {
                $this->db->query($query);
            }

            // Insert default Easy!Appointments entries into the database.
            $file_contents = file_get_contents(__DIR__ . '/../../assets/sql/data.sql');
            $sql_queries = explode(';', $file_contents);
            array_pop($sql_queries);
            foreach ($sql_queries as $query)
            {
                $this->db->query($query);
            }

            // Insert admin
            $admin['settings']['username'] = $admin['username'];
            $admin['settings']['password'] = $admin['password'];
            $admin['settings']['calendar_view'] = CALENDAR_VIEW_DEFAULT;
            unset($admin['username'], $admin['password']);
            $admin['id'] = $this->admins_model->add($admin);

            $this->session->set_userdata('user_id', $admin['id']);
            $this->session->set_userdata('user_email', $admin['email']);
            $this->session->set_userdata('role_slug', DB_SLUG_ADMIN);
            $this->session->set_userdata('username', $admin['settings']['username']);

            // Save company settings
            $this->settings_model->set_setting('company_name', $company['company_name']);
            $this->settings_model->set_setting('company_email', $company['company_email']);
            $this->settings_model->set_setting('company_link', $company['company_link']);

            // Create sample records.
            $sample_service = get_sample_service();
            $sample_service['id'] = $this->services_model->add($sample_service);
            $sample_provider = get_sample_provider();
            $sample_provider['services'][] = $sample_service['id'];
            $this->providers_model->add($sample_provider);

            $response = AJAX_SUCCESS;
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
