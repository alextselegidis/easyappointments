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
 * @property CI_Session $session
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Config $config
 * @property CI_Lang $lang
 * @property CI_Cache $cache
 * @property CI_DB_query_builder $db
 * @property CI_Security $security
 * @property CI_Migration migration
 * @property Google_Sync $google_sync
 * @property Ics_file $ics_file
 * @property Appointments_model $appointments_model
 * @property Providers_model $providers_model
 * @property Services_model $services_model
 * @property Customers_model $customers_model
 * @property Settings_model $settings_model
 * @property Timezones $timezones
 * @property Roles_model $roles_model
 * @property Secretaries_model $secretaries_model
 * @property Admins_model $admins_model
 * @property User_model $user_model
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
        if (is_app_installed())
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
            if (is_app_installed())
            {
                return;
            }

            $this->load->model('admins_model');
            $this->load->model('settings_model');
            $this->load->model('services_model');
            $this->load->model('providers_model');
            $this->load->library('session');
            $this->load->library('migration');
            $this->load->helper('string');

            $admin = $this->input->post('admin');
            $company = $this->input->post('company');

            if ( ! $this->migration->current())
            {
                throw new Exception($this->migration->error_string());
            }

            // Insert admin
            $admin['timezone'] = 'UTC';
            $admin['settings']['username'] = $admin['username'];
            $admin['settings']['password'] = $admin['password'];
            $admin['settings']['notifications'] = true;
            $admin['settings']['calendar_view'] = CALENDAR_VIEW_DEFAULT;
            unset($admin['username'], $admin['password']);
            $admin['id'] = $this->admins_model->add($admin);

            $this->session->set_userdata('user_id', $admin['id']);
            $this->session->set_userdata('user_email', $admin['email']);
            $this->session->set_userdata('role_slug', DB_SLUG_ADMIN);
            $this->session->set_userdata('timezone', $admin['timezone']);
            $this->session->set_userdata('username', $admin['settings']['username']);

            // Save company settings
            $this->settings_model->set_setting('company_name', $company['company_name']);
            $this->settings_model->set_setting('company_email', $company['company_email']);
            $this->settings_model->set_setting('company_link', $company['company_link']);

            // Create sample records.
            $services = [
                'name' => 'Test Service',
                'duration' => 30,
                'price' => 50.0,
                'currency' => 'EUR',
                'description' => 'This is a test service automatically inserted by the installer.',
                'availabilities_type' => 'flexible',
                'attendants_number' => 1,
                'id_service_categories' => NULL
            ];
            $services['id'] = $this->services_model->add($services);

            $salt = generate_salt();
            $password = random_string('alnum', 12);

            $sample_provider = [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.org',
                'phone_number' => '0123456789',
                'services' => [
                    $services['id']
                ],
                'settings' => [
                    'username' => 'johndoe',
                    'password' =>   hash_password($salt, $password),
                    'salt' => $salt,
                    'working_plan' => '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"saturday":null,"sunday":null}',
                    'notifications' => FALSE,
                    'google_sync' => FALSE,
                    'sync_past_days' => 5,
                    'sync_future_days' => 5,
                    'calendar_view' => CALENDAR_VIEW_DEFAULT
                ]
            ];

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
