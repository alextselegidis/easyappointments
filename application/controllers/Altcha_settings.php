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
 * Altcha_settings controller.
 *
 * Handles ALTCHA integration settings.
 *
 * @package Controllers
 */
class Altcha_settings extends EA_Controller
{
    /**
     * Altcha_settings constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('roles_model');

        $required_permissions = can('edit', PRIV_SYSTEM_SETTINGS);

        if ($required_permissions === false) {
            show_error('Forbidden', 403);
        }
    }

    /**
     * Render the settings page.
     */
    public function index(): void
    {
        $user_id = session('user_id');

        $role_slug = session('role_slug');

        $altcha_settings = [
            [
                'name' => 'altcha_enabled',
                'value' => setting('altcha_enabled', '0'),
            ],
            [
                'name' => 'altcha_hmac_key',
                'value' => setting('altcha_hmac_key', ''),
            ],
            [
                'name' => 'altcha_max_number',
                'value' => setting('altcha_max_number', '100000'),
            ],
            [
                'name' => 'altcha_expires',
                'value' => setting('altcha_expires', '300'),
            ],
        ];

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
            'altcha_settings' => $altcha_settings,
        ]);

        html_vars([
            'page_title' => lang('settings'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
        ]);

        $this->load->view('pages/altcha_settings');
    }

    /**
     * Save the ALTCHA settings.
     */
    public function save(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                abort(403, 'Forbidden');
            }

            check('altcha_settings', 'array|null');

            $altcha_settings = request('altcha_settings', []);

            foreach ($altcha_settings as $altcha_setting) {
                setting([
                    $altcha_setting['name'] => $altcha_setting['value'],
                ]);
            }

            response();
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Generate a new HMAC key.
     */
    public function generate_key(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                abort(403, 'Forbidden');
            }

            $hmac_key = bin2hex(random_bytes(32));

            json_response([
                'hmac_key' => $hmac_key,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
