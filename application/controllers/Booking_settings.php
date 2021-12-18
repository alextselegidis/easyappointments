<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Booking settings controller.
 *
 * Handles booking settings related operations.
 *
 * @package Controllers
 */
class Booking_settings extends EA_Controller {
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
    }

    /**
     * Render the settings page.
     */
    public function index()
    {
        session(['dest_url' => site_url('booking_settings')]);

        if (cannot('view', PRIV_SYSTEM_SETTINGS))
        {
            abort(403, 'Forbidden');
        }

        $user_id = session('user_id');

        $role_slug = session('role_slug');

        html_vars([
            'page_title' => lang('settings'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
            'system_settings' => $this->settings_model->get(),
        ]);

        $this->load->view('pages/booking_settings', html_vars());
    }

    /**
     * Save general settings.
     */
    public function save()
    {
        try
        {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS))
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $settings = json_decode(request('settings', FALSE), TRUE);

            // Check if phone number settings are valid.

            $phone_number_required = setting('phone_number_required');

            $phone_number_shown = FALSE;

            foreach ($settings as $setting)
            {
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
