<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.2
 * ---------------------------------------------------------------------------- */

/**
 * Blocked_periods API v1 controller.
 *
 * @package Controllers
 */
class Blocked_periods_api_v1 extends EA_Controller
{
    /**
     * Blocked_periods_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');
        $this->load->library('webhooks_client');

        $this->api->auth();

        $this->api->model('blocked_periods_model');
    }

    /**
     * Get an blocked_periods collection.
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

            $where = null;

            // Date query param.

            $date = request('date');

            if (!empty($date)) {
                $where['DATE(start_datetime)'] = (new DateTime($date))->format('Y-m-d');
            }

            // From query param.

            $from = request('from');

            if (!empty($from)) {
                $where['DATE(start_datetime) >='] = (new DateTime($from))->format('Y-m-d');
            }

            // Till query param.

            $till = request('till');

            if (!empty($till)) {
                $where['DATE(end_datetime) <='] = (new DateTime($till))->format('Y-m-d');
            }

            $blockedperiods = empty($keyword)
                ? $this->blocked_periods_model->get($where, $limit, $offset, $order_by)
                : $this->blocked_periods_model->search($keyword, $limit, $offset, $order_by);

            foreach ($blockedperiods as &$blockedperiod) {
                $this->blocked_periods_model->api_encode($blockedperiod);

                if (!empty($fields)) {
                    $this->blocked_periods_model->only($blockedperiod, $fields);
                }

                if (!empty($with)) {
                    $this->blocked_periods_model->load($blockedperiod, $with);
                }
            }

            json_response($blockedperiods);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get a single blocked_period.
     *
     * @param int|null $id blocked_period ID.
     */
    public function show(?int $id = null): void
    {
        try {
            $occurrences = $this->blocked_periods_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $fields = $this->api->request_fields();

            $with = $this->api->request_with();

            $blocked_period = $this->blocked_periods_model->find($id);

            $this->blocked_periods_model->api_encode($blocked_period);

            if (!empty($fields)) {
                $this->blocked_periods_model->only($blocked_period, $fields);
            }

            if (!empty($with)) {
                $this->blocked_periods_model->load($blocked_period, $with);
            }

            json_response($blocked_period);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new blocked_period.
     */
    public function store(): void
    {
        try {
            $blocked_period = request();

            $this->blocked_periods_model->api_decode($blocked_period);

            if (array_key_exists('id', $blocked_period)) {
                unset($blocked_period['id']);
            }

            $blocked_period_id = $this->blocked_periods_model->save($blocked_period);

            $created_blocked_period = $this->blocked_periods_model->find($blocked_period_id);

            $this->webhooks_client->trigger(WEBHOOK_BLOCKED_PERIOD_SAVE, $created_blocked_period);

            $this->blocked_periods_model->api_encode($created_blocked_period);

            json_response($created_blocked_period, 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update an blocked_period.
     *
     * @param int $id blocked_period ID.
     */
    public function update(int $id): void
    {
        try {
            $occurrences = $this->blocked_periods_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $original_blocked_period = $occurrences[0];

            $blocked_period = request();

            $this->blocked_periods_model->api_decode($blocked_period, $original_blocked_period);

            $blocked_period_id = $this->blocked_periods_model->save($blocked_period);

            $updated_blocked_period = $this->blocked_periods_model->find($blocked_period_id);

            $this->webhooks_client->trigger(WEBHOOK_BLOCKED_PERIOD_SAVE, $updated_blocked_period);

            $this->blocked_periods_model->api_encode($updated_blocked_period);

            json_response($updated_blocked_period);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Delete an blocked_period.
     *
     * @param int $id blocked_period ID.
     */
    public function destroy(int $id): void
    {
        try {
            $occurrences = $this->blocked_periods_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $deleted_blocked_period = $occurrences[0];

            $this->blocked_periods_model->delete($id);

            $this->webhooks_client->trigger(WEBHOOK_BLOCKED_PERIOD_DELETE, $deleted_blocked_period);

            response('', 204);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
