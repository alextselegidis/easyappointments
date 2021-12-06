<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * About controller.
 *
 * Handles about settings related operations.
 *
 * @package Controllers
 */
class About extends EA_Controller {
    /**
     * @var array
     */
    protected $permissions;

    /**
     * Calendar constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('customers_model');
        $this->load->model('services_model');
        $this->load->model('providers_model');
        $this->load->model('roles_model');
        $this->load->model('settings_model');

        $this->load->library('accounts');
        $this->load->library('google_sync');
        $this->load->library('notifications');
        $this->load->library('synchronization');
        $this->load->library('timezones');

        $role_slug = session('role_slug');

        if ($role_slug)
        {
            $this->permissions = $this->roles_model->get_permissions_by_slug($role_slug);
        }
    }

    /**
     * Render the settings page.
     */
    public function index()
    {
        session(['dest_url' => site_url('services')]);

        if (cannot('view', 'services'))
        {
            show_error('Forbidden', 403);
        }

        $user_id = session('user_id');

        $role_slug = session('role_slug');

        $this->load->view('pages/settings/about_page', [
            'page_title' => lang('settings'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);
    }
}
