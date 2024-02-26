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
 * Secretaries API v1 controller.
 *
 * @package Controllers
 */
class Secretaries_api_v1 extends EA_Controller
{
    /**
     * Secretaries_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');

        $this->api->auth();

        $this->api->model('secretaries_model');
    }

    /**
     * Get a secretary collection.
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

            $secretaries = empty($keyword)
                ? $this->secretaries_model->get(null, $limit, $offset, $order_by)
                : $this->secretaries_model->search($keyword, $limit, $offset, $order_by);

            foreach ($secretaries as &$secretary) {
                $this->secretaries_model->api_encode($secretary);

                if (!empty($fields)) {
                    $this->secretaries_model->only($secretary, $fields);
                }

                if (!empty($with)) {
                    $this->secretaries_model->load($secretary, $with);
                }
            }

            json_response($secretaries);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get a single secretary.
     *
     * @param int|null $id Secretary ID.
     */
    public function show(int $id = null): void
    {
        try {
            $occurrences = $this->secretaries_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $fields = $this->api->request_fields();

            $secretary = $this->secretaries_model->find($id);

            $this->secretaries_model->api_encode($secretary);

            if (!empty($fields)) {
                $this->secretaries_model->only($secretary, $fields);
            }

            json_response($secretary);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new secretary.
     */
    public function store(): void
    {
        try {
            $secretary = request();

            $this->secretaries_model->api_decode($secretary);

            if (array_key_exists('id', $secretary)) {
                unset($secretary['id']);
            }

            if (!array_key_exists('providers', $secretary)) {
                throw new InvalidArgumentException('No providers property provided.');
            }

            if (!array_key_exists('settings', $secretary)) {
                throw new InvalidArgumentException('No settings property provided.');
            }

            $secretary_id = $this->secretaries_model->save($secretary);

            $created_secretary = $this->secretaries_model->find($secretary_id);

            $this->secretaries_model->api_encode($created_secretary);

            json_response($created_secretary, 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a secretary.
     *
     * @param int $id Secretary ID.
     */
    public function update(int $id): void
    {
        try {
            $occurrences = $this->secretaries_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $original_secretary = $occurrences[0];

            $secretary = request();

            $this->secretaries_model->api_decode($secretary, $original_secretary);

            $secretary_id = $this->secretaries_model->save($secretary);

            $updated_secretary = $this->secretaries_model->find($secretary_id);

            $this->secretaries_model->api_encode($updated_secretary);

            json_response($updated_secretary);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Delete a secretary.
     *
     * @param int $id Secretary ID.
     */
    public function destroy(int $id): void
    {
        try {
            $occurrences = $this->secretaries_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $this->secretaries_model->delete($id);

            response('', 204);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
