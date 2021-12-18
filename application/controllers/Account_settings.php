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
 * Account settings controller.
 *
 * Handles current user settings related operations.
 *
 * @package Controllers
 */
class Account_settings extends EA_Controller {
    /**
     * @var array
     */
    protected array $permissions;

    /**
     * Current_user constructor.
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
    }

    /**
     * Render the settings page.
     */
    public function index()
    {
        session(['dest_url' => site_url('account_settings')]);

        if (cannot('view', PRIV_USER_SETTINGS))
        {
            abort(403,'Forbidden');
        }

        $user_id = session('user_id');

        $role_slug = session('role_slug');

        html_vars([
            'page_title' => lang('settings'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
            'user_settings' => $this->users_model->find($user_id),
        ]);

        $this->load->view('pages/account_settings', html_vars());
    }

    /**
     * Save general settings.
     */
    public function save()
    {
        try
        {
            if (cannot('edit', PRIV_USER_SETTINGS))
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $settings = json_decode(request('settings'), TRUE);

            $this->users_model->save($settings);

            session([
                'user_email' => $settings['email'],
                'username' => $settings['settings']['username'],
                'timezone' => $settings['timezone'],
            ]);

            response();
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
