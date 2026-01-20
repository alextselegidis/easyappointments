<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.6.0
 * ---------------------------------------------------------------------------- */

/**
 * Configuration settings controller.
 *
 * Handles configuration settings related operations.
 *
 * @package Controllers
 */
class Configuration_settings extends EA_Controller
{
    /**
     * Summary of __construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('configs_model');

        $this->load->library('accounts');
    }

    /**
     * Render the configs page.
     */
    public function index(): void
    {
        session(['dest_url' => site_url('configuration_settings')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_CONFIG_SETTINGS)) {
            if ($user_id) {
                abort(403, 'Forbidden');
            }

            redirect('login');

            return;
        }

        $role_slug = session('role_slug');

        $available_theme_files = glob(__DIR__ . '/../../assets/css/themes/*.min.css');

        $available_themes = array_map(function ($available_theme_file) {
            return str_replace('.min.css', '', basename($available_theme_file));
        }, $available_theme_files);

        $cfs = $this->configs_model->get();
        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
            'timezones' => $this->timezones->to_array(),
            'config_settings' => $this->configs_model->get(),
        ]);

        html_vars([
            'page_title' => lang('configuration'),
            'active_menu' => PRIV_CONFIG_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'grouped_timezones' => $this->timezones->to_grouped_array(),
            'available_themes' => $available_themes,
        ]);

        $this->load->view('pages/configuration_settings');
    }

    /**
     * Save config settings.
     */
    public function save(): void
    {
        try {
            if (cannot('edit', PRIV_CONFIG_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $settings = request('config_settings', []);

            foreach ($settings as $setting) {
                $existing_setting = $this->configs_model
                    ->query()
                    ->where('name', $setting['name'])
                    ->get()
                    ->row_array();

                if (!empty($existing_setting)) {
                    $setting['id'] = $existing_setting['id'];
                }

                $this->configs_model->save($setting);
            }

            response();
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function delete(): void 
    {
        try {
            if (cannot('edit', PRIV_CONFIG_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $settings = request('config_settings', []);

            foreach ($settings as $setting) {
                if (!empty($setting['id'])) {
                    $this->configs_model->delete($setting['id']);
                }
            }

            response();
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}