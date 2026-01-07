<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * General settings controller.
 *
 * Handles general settings related operations.
 *
 * @package Controllers
 */
class General_settings extends EA_Controller
{
    /**
     * Calendar constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('settings_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
    }

    /**
     * Render the settings page.
     */
    public function index(): void
    {
        method('get');

        session(['dest_url' => site_url('general_settings')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_SYSTEM_SETTINGS)) {
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

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
            'timezones' => $this->timezones->to_array(),
            'general_settings' => $this->settings_model->get(),
        ]);

        html_vars([
            'page_title' => lang('settings'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'grouped_timezones' => $this->timezones->to_grouped_array(),
            'available_themes' => $available_themes,
        ]);

        $this->load->view('pages/general_settings');
    }

    /**
     * Allowed setting names that can be modified via this controller.
     */
    private array $allowed_settings = [
        'company_name',
        'company_email',
        'company_link',
        'company_logo',
        'company_color',
        'company_working_plan',
        'book_advance_timeout',
        'default_timezone',
        'default_language',
        'theme',
        'date_format',
        'time_format',
        'first_weekday',
        'require_phone_number',
        'display_cookie_notice',
        'cookie_notice_content',
        'display_terms_and_conditions',
        'terms_and_conditions_content',
        'display_privacy_policy',
        'privacy_policy_content',
    ];

    /**
     * Save general settings.
     */
    public function save(): void
    {
        try {
            method('post');

            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            check('general_settings', 'array|null');

            $settings = request('general_settings', []);

            // Validate settings is an array
            if (!is_array($settings)) {
                throw new InvalidArgumentException('Invalid settings data format.');
            }

            foreach ($settings as $setting) {
                // Validate each setting has required fields
                if (!isset($setting['name']) || !is_string($setting['name'])) {
                    continue;
                }

                // Only allow whitelisted settings to be modified
                if (!in_array($setting['name'], $this->allowed_settings, true)) {
                    log_message('warning', 'Attempt to modify unauthorized setting: ' . $setting['name']);
                    continue;
                }

                $existing_setting = $this->settings_model->query()->where('name', $setting['name'])->get()->row_array();

                if (!empty($existing_setting)) {
                    $setting['id'] = $existing_setting['id'];
                }

                $this->settings_model->save($setting);
            }

            response();
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
