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
 * General controller.
 *
 * Handles general settings related operations.
 *
 * @package Controllers
 */
class General extends EA_Controller {
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

        $this->load->view('pages/settings/general/general_page', [
            'page_title' => lang('settings'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
            'system_settings' => $this->settings_model->get(),
        ]);
    }

    /**
     * Save general settings.
     */
    public function save()
    {
        try
        {
            if ($this->permissions[PRIV_SYSTEM_SETTINGS]['edit'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $settings = json_decode(request('settings', FALSE), TRUE);

            // Check if phone number settings are valid.

            $phone_number_required = FALSE;

            $phone_number_shown = FALSE;

            foreach ($settings as $setting)
            {
                if ($setting['name'] === 'require_phone_number')
                {
                    $phone_number_required = $setting['value'];
                }

                if ($setting['name'] === 'show_phone_number')
                {
                    $phone_number_shown = $setting['value'];
                }
            }

            if ($phone_number_required && ! $phone_number_shown)
            {
                throw new RuntimeException('You cannot hide the phone number in the booking form while it\'s also required!');
            }

            foreach ($settings as $setting)
            {
                $existing_setting = $this->settings_model->query()->where('name', $setting['name'])->get()->row_array();

                if ( ! empty($existing_setting))
                {
                    $setting['id'] = $existing_setting['id'];
                }

                $this->settings_model->save($setting);
            }

            response();
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
