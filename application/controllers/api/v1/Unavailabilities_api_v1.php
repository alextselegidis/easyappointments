<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Unavailabilities API v1 controller.
 *
 * @package Controllers
 */
class Unavailabilities_api_v1 extends EA_Controller {
    /**
     * Unavailabilities_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('unavailabilities_model');

        $this->load->library('api');

        $this->api->auth();

        $this->api->model('unavailabilities_model');
    }

    /**
     * Get an unavailability collection.
     */
    public function index()
    {
        try
        {
            $keyword = $this->api->request_keyword();

            $limit = $this->api->request_limit();

            $offset = $this->api->request_offset();

            $order_by = $this->api->request_order_by();

            $fields = $this->api->request_fields();

            $with = $this->api->request_with();

            $unavailabilities = empty($keyword)
                ? $this->unavailabilities_model->get(NULL, $limit, $offset, $order_by)
                : $this->unavailabilities_model->search($keyword, $limit, $offset, $order_by);

            foreach ($unavailabilities as &$unavailability)
            {
                $this->unavailabilities_model->api_encode($unavailability);

                if ( ! empty($fields))
                {
                    $this->unavailabilities_model->only($unavailability, $fields);
                }

                if ( ! empty($with))
                {
                    $this->unavailabilities_model->load($unavailability, $with);
                }
            }

            json_response($unavailabilities);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Get a single unavailability.
     *
     * @param int|null $id Unavailability ID.
     */
    public function show(int $id = NULL)
    {
        try
        {
            $fields = $this->api->request_fields();

            $with = $this->api->request_with();

            $unavailability = $this->unavailabilities_model->find($id);

            $this->unavailabilities_model->api_encode($unavailability);

            if ( ! empty($fields))
            {
                $this->unavailabilities_model->only($unavailability, $fields);
            }

            if ( ! empty($with))
            {
                $this->unavailabilities_model->load($unavailability, $with);
            }

            if ( ! $unavailability)
            {
                response('', 404);

                return;
            }

            json_response($unavailability);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create an unavailability.
     */
    public function store()
    {
        try
        {
            $unavailability = request();

            $this->unavailabilities_model->api_decode($unavailability);

            if (array_key_exists('id', $unavailability))
            {
                unset($unavailability['id']);
            }

            $unavailability_id = $this->unavailabilities_model->save($unavailability);

            $created_unavailability = $this->unavailabilities_model->find($unavailability_id);

            $this->unavailabilities_model->api_encode($created_unavailability);

            json_response($created_unavailability, 201);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update an unavailability.
     *
     * @param int $id Unavailability ID.
     */
    public function update(int $id)
    {
        try
        {
            $occurrences = $this->unavailabilities_model->get(['id' => $id]);

            if (empty($occurrences))
            {
                response('', 404);

                return;
            }

            $original_unavailability = $occurrences[0];

            $unavailability = request();

            $this->unavailabilities_model->api_decode($unavailability, $original_unavailability);

            $unavailability_id = $this->unavailabilities_model->save($unavailability);

            $updated_unavailability = $this->unavailabilities_model->find($unavailability_id);

            $this->unavailabilities_model->api_encode($updated_unavailability);

            json_response($updated_unavailability);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Delete an unavailability.
     *
     * @param int $id Unavailability ID.
     */
    public function destroy(int $id)
    {
        try
        {
            $occurrences = $this->unavailabilities_model->get(['id' => $id]);

            if (empty($occurrences))
            {
                response('', 404);

                return;
            }

            $this->unavailabilities_model->delete($id);

            response('', 204);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
