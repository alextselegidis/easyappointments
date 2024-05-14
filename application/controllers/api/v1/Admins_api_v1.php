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
 * Admins API v1 controller.
 *
 * @package Controllers
 */
class Admins_api_v1 extends EA_Controller
{
    /**
     * Admins_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('admins_model');

        $this->load->library('api');

        $this->api->auth();

        $this->api->model('admins_model');
    }

    /**
     * Get an admin collection.
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

            $admins = empty($keyword)
                ? $this->admins_model->get(null, $limit, $offset, $order_by)
                : $this->admins_model->search($keyword, $limit, $offset, $order_by);

            foreach ($admins as &$admin) {
                $this->admins_model->api_encode($admin);

                if (!empty($fields)) {
                    $this->admins_model->only($admin, $fields);
                }

                if (!empty($with)) {
                    $this->admins_model->load($admin, $with);
                }
            }

            json_response($admins);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get a single admin.
     *
     * @param int|null $id Admin ID.
     */
    public function show(int $id = null): void
    {
        try {
            $occurrences = $this->admins_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $fields = $this->api->request_fields();

            $with = $this->api->request_with();

            $admin = $this->admins_model->find($id);

            $this->admins_model->api_encode($admin);

            if (!empty($fields)) {
                $this->admins_model->only($admin, $fields);
            }

            if (!empty($with)) {
                $this->admins_model->load($admin, $with);
            }

            json_response($admin);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new admin.
     */
    public function store(): void
    {
        try {
            $admin = request();

            $this->admins_model->api_decode($admin);

            if (array_key_exists('id', $admin)) {
                unset($admin['id']);
            }

            if (!array_key_exists('settings', $admin)) {
                throw new InvalidArgumentException('No settings property provided.');
            }

            $admin_id = $this->admins_model->save($admin);

            $created_admin = $this->admins_model->find($admin_id);

            $this->admins_model->api_encode($created_admin);

            json_response($created_admin, 201);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update an admin.
     *
     * @param int $id Admin ID.
     */
    public function update(int $id): void
    {
        try {
            $occurrences = $this->admins_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $original_admin = $occurrences[0];

            $admin = request();

            $this->admins_model->api_decode($admin, $original_admin);

            $admin_id = $this->admins_model->save($admin);

            $updated_admin = $this->admins_model->find($admin_id);

            $this->admins_model->api_encode($updated_admin);

            json_response($updated_admin);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Delete an admin.
     *
     * @param int $id Admin ID.
     */
    public function destroy(int $id): void
    {
        try {
            $occurrences = $this->admins_model->get(['id' => $id]);

            if (empty($occurrences)) {
                response('', 404);

                return;
            }

            $this->admins_model->delete($id);

            response('', 204);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
