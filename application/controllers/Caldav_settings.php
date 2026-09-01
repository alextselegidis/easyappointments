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
 * Caldav_settings controller.
 *
 * Handles CalDAV integration settings.
 *
 * @package Controllers
 */
class Caldav_settings extends EA_Controller
{
    /**
     * Caldav_settings constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('roles_model');

        if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
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

        $caldav_settings = [
            [
                'name' => 'caldav_allowed_hosts',
                'value' => setting('caldav_allowed_hosts', ''),
            ],
        ];

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
            'caldav_settings' => $caldav_settings,
        ]);

        html_vars([
            'page_title' => lang('settings'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
        ]);

        $this->load->view('pages/caldav_settings');
    }

    /**
     * Save the CalDAV settings.
     */
    public function save(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                abort(403, 'Forbidden');
            }

            check('caldav_settings', 'array|null');

            $caldav_settings = request('caldav_settings', []);

            foreach ($caldav_settings as $caldav_setting) {
                setting([
                    $caldav_setting['name'] => $caldav_setting['value'],
                ]);
            }

            response();
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
