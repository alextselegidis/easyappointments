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
 * Webhooks API v1 controller.
 *
 * @package Controllers
 */
class Webhooks_api_v1 extends EA_Controller
{
    /**
     * Webhooks_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');

        $this->api->auth();

        $this->api->model('webhooks_model');
    }

    /**
     * Get a webhook collection.
     */
    public function index(): void
    {
        try {
            $keyword = $this->api->request_keyword();

            $limit = $this->api->request_limit();

            $offset = $this->api->request_offset();

            $order_by = $this->api->request_order_by();

            $fields = $this->api->request_fields();

            $with = $this->api->request_with();

            $webhooks = empty($keyword)
                ? $this->webhooks_model->get(null, $limit, $offset, $order_by)
                : $this->webhooks_model->search($keyword, $limit, $offset, $order_by);

            foreach ($webhooks as &$webhook) {
                $this->webhooks_model->api_encode($webhook);

                if (!empty($fields)) {
                    $this->webhooks_model->only($webhook, $fields);
                }

                if (!empty($with)) {
                    $this->webhooks_model->load($webhook, $with);
                }
            }

            json_response($webhooks);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get a single webhook.
     *
     * @param int|null $id Webhook ID.
     */
    public function show(int $id = null): void
    {
        try {
            $occurrences = $this->webhooks_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $fields = $this->api->request_fields();

            $with = $this->api->request_with();

            $webhook = $this->webhooks_model->find($id);

            $this->webhooks_model->api_encode($webhook);

            if (!empty($fields)) {
                $this->webhooks_model->only($webhook, $fields);
            }

            if (!empty($with)) {
                $this->webhooks_model->load($webhook, $with);
            }

            json_response($webhook);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new webhook.
     */
    public function store(): void
    {
        try {
            $webhook = request();

            $this->webhooks_model->api_decode($webhook);

            if (array_key_exists('id', $webhook)) {
                unset($webhook['id']);
            }

            $webhook_id = $this->webhooks_model->save($webhook);

            $created_webhook = $this->webhooks_model->find($webhook_id);

            $this->webhooks_model->api_encode($created_webhook);

            json_response($created_webhook, 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a webhook.
     *
     * @param int $id Webhook ID.
     */
    public function update(int $id): void
    {
        try {
            $occurrences = $this->webhooks_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $original_webhook = $occurrences[0];

            $webhook = request();

            $this->webhooks_model->api_decode($webhook, $original_webhook);

            $webhook_id = $this->webhooks_model->save($webhook);

            $updated_webhook = $this->webhooks_model->find($webhook_id);

            $this->webhooks_model->api_encode($updated_webhook);

            json_response($updated_webhook);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Delete a webhook.
     *
     * @param int $id Webhook ID.
     */
    public function destroy(int $id): void
    {
        try {
            $occurrences = $this->webhooks_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $this->webhooks_model->delete($id);

            response('', 204);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
