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
 * Settings controller. 
 * 
 * Handles settings related operations.
 *
 * @package Controllers 
 */
class Settings extends EA_Controller {
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
     * Save a setting or multiple settings in the database.
     */
    public function ajax_save_settings()
    {
        try
        {
            $type = request('type');

            if ($type == SETTINGS_SYSTEM)
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
            }
            else if ($type == SETTINGS_USER)
            {
                if ($this->permissions[PRIV_USER_SETTINGS]['edit'] == FALSE)
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
            }

            response();
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * This method checks whether the username already exists in the database.
     */
    public function ajax_validate_username()
    {
        try
        {
            // We will only use the function in the admins_model because it is sufficient for the rest user types for
            // now (providers, secretaries).

            $username = request('username');

            $user_id = request('user_id');

            $is_valid = $this->admins_model->validate_username($username, $user_id);

            json_response([
                'is_valid' => $is_valid,
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Apply global working plan to all providers.
     */
    public function ajax_apply_global_working_plan()
    {
        try
        {
            if ($this->permissions[PRIV_SYSTEM_SETTINGS]['edit'] == FALSE)
            {
                throw new Exception('You do not have the required permissions for this task.');
            }

            $working_plan = request('working_plan');

            $providers = $this->providers_model->get();

            foreach ($providers as $provider)
            {
                $this->providers_model->set_setting($provider['id'], 'working_plan', $working_plan);
            }

            response();
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
