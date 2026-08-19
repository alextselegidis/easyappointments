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
 * Settings API v1 controller.
 *
 * @package Controllers
 */
class Settings_api_v1 extends EA_Controller
{
    /**
     * Settings that hold credentials and must never be read from or written to through the API.
     *
     * @var string[]
     */
    private const PROTECTED_SETTINGS = ['api_token', 'google_client_secret', 'ldap_password', 'altcha_hmac_key'];

    /**
     * Settings_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');

        $this->api->auth();

        $this->api->model('settings_model');
    }

    /**
     * Get a setting collection.
     */
    public function index(): void
    {
        try {
            $keyword = $this->api->request_keyword();

            $limit = $this->api->request_limit();

            $offset = $this->api->request_offset();

            $order_by = $this->api->request_order_by();

            $fields = $this->api->request_fields();

            $settings = empty($keyword)
                ? $this->settings_model->get(null, $limit, $offset, $order_by)
                : $this->settings_model->search($keyword, $limit, $offset, $order_by);

            $settings = array_values(
                array_filter($settings, fn($setting) => !in_array($setting['name'], self::PROTECTED_SETTINGS, true)),
            );

            foreach ($settings as &$setting) {
                $this->settings_model->api_encode($setting);

                if (!empty($fields)) {
                    $this->settings_model->only($setting, $fields);
                }
            }

            json_response($settings);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get a setting value by name.
     *
     * @param string $name Setting name.
     */
    public function show(string $name): void
    {
        try {
            if (in_array($name, self::PROTECTED_SETTINGS, true)) {
                abort(403, 'This setting cannot be read through the API.');
            }

            $value = setting($name);

            json_response([
                'name' => $name,
                'value' => $value,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Set a setting value by name.
     *
     * @param string $name Setting name.
     */
    public function update(string $name): void
    {
        try {
            if (in_array($name, self::PROTECTED_SETTINGS, true)) {
                abort(403, 'This setting cannot be changed through the API.');
            }

            $value = request('value');

            setting([$name => $value]);

            json_response([
                'name' => $name,
                'value' => $value,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
