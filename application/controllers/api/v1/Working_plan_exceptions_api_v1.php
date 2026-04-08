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
 * Working_plan_exceptions API v1 controller.
 *
 * @package Controllers
 */
class Working_plan_exceptions_api_v1 extends EA_Controller
{
    /**
     * Working_plan_exceptions_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');

        $this->api->auth();

        $this->api->model('working_plan_exceptions_model');
    }

    /**
     * Get a working plan exceptions collection.
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

            // Provider ID query param.
            $provider_id = request('providerId');

            if (!empty($provider_id)) {
                $where['id_users_provider'] = (int) $provider_id;
            }

            // Date query param - find exceptions that contain this date.
            $date = request('date');

            if (!empty($date)) {
                $formatted_date = (new DateTime($date))->format('Y-m-d');
                $where['start_date <='] = $formatted_date;
                $where['end_date >='] = $formatted_date;
            }

            // From query param - find exceptions that end on or after this date.
            $from = request('from');

            if (!empty($from)) {
                $where['end_date >='] = (new DateTime($from))->format('Y-m-d');
            }

            // Till query param - find exceptions that start on or before this date.
            $till = request('till');

            if (!empty($till)) {
                $where['start_date <='] = (new DateTime($till))->format('Y-m-d');
            }

            $working_plan_exceptions = empty($keyword)
                ? $this->working_plan_exceptions_model->get($where, $limit, $offset, $order_by)
                : $this->working_plan_exceptions_model->search($keyword, $limit, $offset, $order_by);

            foreach ($working_plan_exceptions as &$working_plan_exception) {
                $this->working_plan_exceptions_model->api_encode($working_plan_exception);

                if (!empty($fields)) {
                    $this->working_plan_exceptions_model->only($working_plan_exception, $fields);
                }

                if (!empty($with)) {
                    $this->working_plan_exceptions_model->load($working_plan_exception, $with);
                }
            }

            json_response($working_plan_exceptions);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get a single working plan exception.
     *
     * @param int|null $id Working plan exception ID.
     */
    public function show(?int $id = null): void
    {
        try {
            $occurrences = $this->working_plan_exceptions_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $fields = $this->api->request_fields();

            $with = $this->api->request_with();

            $working_plan_exception = $this->working_plan_exceptions_model->find($id);

            $this->working_plan_exceptions_model->api_encode($working_plan_exception);

            if (!empty($fields)) {
                $this->working_plan_exceptions_model->only($working_plan_exception, $fields);
            }

            if (!empty($with)) {
                $this->working_plan_exceptions_model->load($working_plan_exception, $with);
            }

            json_response($working_plan_exception);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new working plan exception.
     */
    public function store(): void
    {
        try {
            $working_plan_exception = request();

            $this->working_plan_exceptions_model->api_decode($working_plan_exception);

            if (array_key_exists('id', $working_plan_exception)) {
                unset($working_plan_exception['id']);
            }

            $working_plan_exception_id = $this->working_plan_exceptions_model->save($working_plan_exception);

            $created_working_plan_exception = $this->working_plan_exceptions_model->find($working_plan_exception_id);

            $this->working_plan_exceptions_model->api_encode($created_working_plan_exception);

            json_response($created_working_plan_exception, 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a working plan exception.
     *
     * @param int $id Working plan exception ID.
     */
    public function update(int $id): void
    {
        try {
            $occurrences = $this->working_plan_exceptions_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $original_working_plan_exception = $occurrences[0];

            $working_plan_exception = request();

            $this->working_plan_exceptions_model->api_decode($working_plan_exception, $original_working_plan_exception);

            $working_plan_exception_id = $this->working_plan_exceptions_model->save($working_plan_exception);

            $updated_working_plan_exception = $this->working_plan_exceptions_model->find($working_plan_exception_id);

            $this->working_plan_exceptions_model->api_encode($updated_working_plan_exception);

            json_response($updated_working_plan_exception);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Delete a working plan exception.
     *
     * @param int $id Working plan exception ID.
     */
    public function destroy(int $id): void
    {
        try {
            $occurrences = $this->working_plan_exceptions_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $this->working_plan_exceptions_model->delete($id);

            response('', 204);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
