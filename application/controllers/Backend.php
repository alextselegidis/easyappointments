<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Backend Controller
 *
 * @property CI_Session $session
 *
 * @package Controllers
 */
class Backend extends EA_Controller {
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('providers_model');
        $this->load->model('services_model');
        $this->load->model('customers_model');
        $this->load->model('settings_model');
        $this->load->model('roles_model');
        $this->load->model('user_model');
        $this->load->model('secretaries_model');
        $this->load->model('admins_model');
        $this->load->library('timezones');
        $this->load->library('migration');
    }

    /**
     * Display the main backend page.
     *
     * This method displays the main backend page. All users login permission can view this page which displays a
     * calendar with the events of the selected provider or service. If a user has more privileges he will see more
     * menus at the top of the page.
     *
     * @param string $appointment_hash Appointment edit dialog will appear when the page loads (default '').
     *
     * @throws Exception
     */
    public function index($appointment_hash = '')
    {
        $this->session->set_userdata('dest_url', site_url('backend/index' . (! empty($appointment_hash) ? '/' . $appointment_hash : '')));

        if ( ! $this->has_privileges(PRIV_APPOINTMENTS))
        {
            return;
        }

        $calendar_view_query_param = $this->input->get('view');

        $user_id = $this->session->userdata('user_id');

        $user = $this->user_model->get_user($user_id);

        $view['base_url'] = config('base_url');
        $view['page_title'] = lang('calendar');
        $view['user_display_name'] = $this->user_model->get_user_display_name($this->session->userdata('user_id'));
        $view['active_menu'] = PRIV_APPOINTMENTS;
        $view['date_format'] = $this->settings_model->get_setting('date_format');
        $view['time_format'] = $this->settings_model->get_setting('time_format');
        $view['first_weekday'] = $this->settings_model->get_setting('first_weekday');
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['require_phone_number'] = $this->settings_model->get_setting('require_phone_number');
        $view['available_providers'] = $this->providers_model->get_available_providers();
        $view['available_services'] = $this->services_model->get_available_services();
        $view['customers'] = $this->customers_model->get_batch();
        $view['calendar_view'] = ! empty($calendar_view_query_param) ? $calendar_view_query_param : $user['settings']['calendar_view'];
        $view['timezones'] = $this->timezones->to_array();
        $this->set_user_data($view);

        if ($this->session->userdata('role_slug') === DB_SLUG_SECRETARY)
        {
            $secretary = $this->secretaries_model->get_row($this->session->userdata('user_id'));
            $view['secretary_providers'] = $secretary['providers'];
        }
        else
        {
            $view['secretary_providers'] = [];
        }

        $results = $this->appointments_model->get_batch(['hash' => $appointment_hash]);

        if ($appointment_hash !== '' && count($results) > 0)
        {
            $appointment = $results[0];
            $appointment['customer'] = $this->customers_model->get_row($appointment['id_users_customer']);
            $view['edit_appointment'] = $appointment; // This will display the appointment edit dialog on page load.
        }
        else
        {
            $view['edit_appointment'] = NULL;
        }

        $this->load->view('backend/header', $view);
        $this->load->view('backend/calendar', $view);
        $this->load->view('backend/footer', $view);
    }

    /**
     * Check whether current user is logged in and has the required privileges to view a page.
     *
     * The backend page requires different privileges from the users to display pages. Not all pages are available to
     * all users. For example secretaries should not be able to edit the system users.
     *
     * @param string $page This argument must match the roles field names of each section (eg "appointments", "users"
     * ...).
     * @param bool $redirect If the user has not the required privileges (either not logged in or insufficient role
     * privileges) then the user will be redirected to another page. Set this argument to FALSE when using ajax (default
     * true).
     *
     * @return bool Returns whether the user has the required privileges to view the page or not. If the user is not
     * logged in then he will be prompted to log in. If he hasn't the required privileges then an info message will be
     * displayed.
     */
    protected function has_privileges($page, $redirect = TRUE)
    {
        // Check if user is logged in.
        $user_id = $this->session->userdata('user_id');

        if ($user_id == FALSE)
        {
            // User not logged in, display the login view.
            if ($redirect)
            {
                header('Location: ' . site_url('user/login'));
            }
            return FALSE;
        }

        // Check if the user has the required privileges for viewing the selected page.
        $role_slug = $this->session->userdata('role_slug');

        $role_privileges = $this->db->get_where('roles', ['slug' => $role_slug])->row_array();

        if ($role_privileges[$page] < PRIV_VIEW)
        {
            // User does not have the permission to view the page.
            if ($redirect)
            {
                header('Location: ' . site_url('user/no_privileges'));
            }
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Set the user data in order to be available at the view and js code.
     *
     * @param array $view Contains the view data.
     */
    protected function set_user_data(&$view)
    {
        $view['user_id'] = $this->session->userdata('user_id');
        $view['user_email'] = $this->session->userdata('user_email');
        $view['timezone'] = $this->session->userdata('timezone');
        $view['role_slug'] = $this->session->userdata('role_slug');
        $view['privileges'] = $this->roles_model->get_privileges($this->session->userdata('role_slug'));
    }

    /**
     * Display the backend customers page.
     *
     * In this page the user can manage all the customer records of the system.
     */
    public function customers()
    {
        $this->session->set_userdata('dest_url', site_url('backend/customers'));

        if ( ! $this->has_privileges(PRIV_CUSTOMERS))
        {
            return;
        }

        $view['base_url'] = config('base_url');
        $view['page_title'] = lang('customers');
        $view['user_display_name'] = $this->user_model->get_user_display_name($this->session->userdata('user_id'));
        $view['active_menu'] = PRIV_CUSTOMERS;
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['date_format'] = $this->settings_model->get_setting('date_format');
        $view['time_format'] = $this->settings_model->get_setting('time_format');
        $view['first_weekday'] = $this->settings_model->get_setting('first_weekday');
        $view['require_phone_number'] = $this->settings_model->get_setting('require_phone_number');
        $view['customers'] = $this->customers_model->get_batch();
        $view['available_providers'] = $this->providers_model->get_available_providers();
        $view['available_services'] = $this->services_model->get_available_services();
        $view['timezones'] = $this->timezones->to_array();

        if ($this->session->userdata('role_slug') === DB_SLUG_SECRETARY)
        {
            $secretary = $this->secretaries_model->get_row($this->session->userdata('user_id'));
            $view['secretary_providers'] = $secretary['providers'];
        }
        else
        {
            $view['secretary_providers'] = [];
        }

        $this->set_user_data($view);

        $this->load->view('backend/header', $view);
        $this->load->view('backend/customers', $view);
        $this->load->view('backend/footer', $view);
    }

    /**
     * Displays the backend services page.
     *
     * Here the admin user will be able to organize and create the services that the user will be able to book
     * appointments in frontend.
     *
     * NOTICE: The services that each provider is able to service is managed from the backend services page.
     */
    public function services()
    {
        $this->session->set_userdata('dest_url', site_url('backend/services'));

        if ( ! $this->has_privileges(PRIV_SERVICES))
        {
            return;
        }

        $view['base_url'] = config('base_url');
        $view['page_title'] = lang('services');
        $view['user_display_name'] = $this->user_model->get_user_display_name($this->session->userdata('user_id'));
        $view['active_menu'] = PRIV_SERVICES;
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['date_format'] = $this->settings_model->get_setting('date_format');
        $view['time_format'] = $this->settings_model->get_setting('time_format');
        $view['first_weekday'] = $this->settings_model->get_setting('first_weekday');
        $view['services'] = $this->services_model->get_batch();
        $view['categories'] = $this->services_model->get_all_categories();
        $view['timezones'] = $this->timezones->to_array();
        $this->set_user_data($view);

        $this->load->view('backend/header', $view);
        $this->load->view('backend/services', $view);
        $this->load->view('backend/footer', $view);
    }

    /**
     * Display the backend users page.
     *
     * In this page the admin user will be able to manage the system users. By this, we mean the provider, secretary and
     * admin users. This is also the page where the admin defines which service can each provider provide.
     */
    public function users()
    {
        $this->session->set_userdata('dest_url', site_url('backend/users'));

        if ( ! $this->has_privileges(PRIV_USERS))
        {
            return;
        }

        $view['base_url'] = config('base_url');
        $view['page_title'] = lang('users');
        $view['user_display_name'] = $this->user_model->get_user_display_name($this->session->userdata('user_id'));
        $view['active_menu'] = PRIV_USERS;
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['date_format'] = $this->settings_model->get_setting('date_format');
        $view['time_format'] = $this->settings_model->get_setting('time_format');
        $view['first_weekday'] = $this->settings_model->get_setting('first_weekday');
        $view['admins'] = $this->admins_model->get_batch();
        $view['providers'] = $this->providers_model->get_batch();
        $view['secretaries'] = $this->secretaries_model->get_batch();
        $view['services'] = $this->services_model->get_batch();
        $view['working_plan'] = $this->settings_model->get_setting('company_working_plan');
        $view['timezones'] = $this->timezones->to_array();
        $view['working_plan_exceptions'] = '{}';
        $this->set_user_data($view);

        $this->load->view('backend/header', $view);
        $this->load->view('backend/users', $view);
        $this->load->view('backend/footer', $view);
    }

    /**
     * Display the user/system settings.
     *
     * This page will display the user settings (name, password etc). If current user is an administrator, then he will
     * be able to make change to the current Easy!Appointment installation (core settings like company name, book
     * timeout etc).
     */
    public function settings()
    {
        $this->session->set_userdata('dest_url', site_url('backend/settings'));
        if ( ! $this->has_privileges(PRIV_SYSTEM_SETTINGS, FALSE)
            && ! $this->has_privileges(PRIV_USER_SETTINGS))
        {
            return;
        }

        $user_id = $this->session->userdata('user_id');

        $view['base_url'] = config('base_url');
        $view['page_title'] = lang('settings');
        $view['user_display_name'] = $this->user_model->get_user_display_name($user_id);
        $view['active_menu'] = PRIV_SYSTEM_SETTINGS;
        $view['company_name'] = $this->settings_model->get_setting('company_name');
        $view['date_format'] = $this->settings_model->get_setting('date_format');
        $view['first_weekday'] = $this->settings_model->get_setting('first_weekday');
        $view['time_format'] = $this->settings_model->get_setting('time_format');
        $view['role_slug'] = $this->session->userdata('role_slug');
        $view['system_settings'] = $this->settings_model->get_settings();
        $view['user_settings'] = $this->user_model->get_user($user_id);
        $view['timezones'] = $this->timezones->to_array();

        // book_advance_timeout preview
        $book_advance_timeout = $this->settings_model->get_setting('book_advance_timeout');
        $hours = floor($book_advance_timeout / 60);
        $minutes = $book_advance_timeout % 60;
        $view['book_advance_timeout_preview'] = sprintf('%02d:%02d', $hours, $minutes);

        $this->set_user_data($view);

        $this->load->view('backend/header', $view);
        $this->load->view('backend/settings', $view);
        $this->load->view('backend/footer', $view);
    }

    /**
     * This method will update the installation to the latest available version in the server.
     *
     * IMPORTANT: The code files must exist in the server, this method will not fetch any new files but will update
     * the database schema.
     *
     * This method can be used either by loading the page in the browser or by an ajax request. But it will answer with
     * JSON encoded data.
     */
    public function update()
    {
        try
        {
            if ( ! $this->has_privileges(PRIV_SYSTEM_SETTINGS, TRUE))
            {
                throw new Exception('You do not have the required privileges for this task!');
            }

            if ( ! $this->migration->current())
            {
                throw new Exception($this->migration->error_string());
            }

            $view = ['success' => TRUE];
        }
        catch (Exception $exception)
        {
            $view = ['success' => FALSE, 'exception' => $exception->getMessage()];
        }

        $this->load->view('general/update', $view);
    }
}
