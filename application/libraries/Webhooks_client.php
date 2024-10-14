<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

use GuzzleHttp\Client;

/**
 * Webhooks client library.
 *
 * Handles the webhook HTTP related functionality.
 *
 * @package Libraries
 */
class Webhooks_client
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * Webhook client constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('providers_model');
        $this->CI->load->model('secretaries_model');
        $this->CI->load->model('secretaries_model');
        $this->CI->load->model('admins_model');
        $this->CI->load->model('appointments_model');
        $this->CI->load->model('settings_model');
        $this->CI->load->model('webhooks_model');
    }

    /**
     * Trigger the registered webhooks for the provided action.
     *
     * @param string $action Webhook action.
     * @param array $payload Payload data.
     *
     * @return void|null
     */
    public function trigger(string $action, array $payload)
    {
        $webhooks = $this->CI->webhooks_model->get();

        foreach ($webhooks as $webhook) {
            if (str_contains($webhook['actions'], $action)) {
                $this->call($webhook, $action, $payload);
            }
        }
    }

    /**
     * Call the provided webhook.
     *
     * @param array $webhook
     * @param string $action
     * @param array $payload
     */
    private function call(array $webhook, string $action, array $payload): void
    {
        try {
            $client = new Client();

            $client->post($webhook['url'], [
                'verify' => $webhook['is_ssl_verified'],
                'json' => [
                    'action' => $action,
                    'payload' => $payload,
                ],
            ]);
        } catch (Throwable $e) {
            log_message(
                'error',
                'Webhooks Client - The webhook (' .
                    ($webhook['id'] ?? null) .
                    ') request received an unexpected exception: ' .
                    $e->getMessage(),
            );
            log_message('error', $e->getTraceAsString());
        }
    }
}
