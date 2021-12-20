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
     * Booking_settings constructor.
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

        script_vars([
            'booking_settings' => $this->settings_model->get_batch()
        ]);

        html_vars([
            'page_title' => lang('settings'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
        ]);

        $this->load->view('pages/booking_settings', html_vars());
    }

    /**
     * Save booking settings.
     */
    public function save()
    {
        try
        {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS))
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $settings = request('booking_settings', []);

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
