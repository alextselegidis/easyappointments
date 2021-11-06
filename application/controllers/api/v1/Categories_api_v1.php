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
 * Categories API v1 controller.
 *
 * @package Controllers
 */
class Categories_api_v1 extends EA_Controller {
    /**
     * Categories_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('service_categories_model');

        $this->load->library('api');

        $this->api->cors();

        $this->api->auth();

        $this->api->model('service_categories_model');
    }

    /**
     * Get a service category collection.
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

            $service_categories = empty($keyword)
                ? $this->service_categories_model->get(NULL, $limit, $offset, $order_by)
                : $this->service_categories_model->search($keyword, $limit, $offset, $order_by);

            foreach ($service_categories as &$service_category)
            {
                $this->service_categories_model->api_encode($service_category);

                if ( ! empty($fields))
                {
                    $this->service_categories_model->only($service_category, $fields);
                }

                if ( ! empty($with))
                {
                    $this->service_categories_model->load($service_category, $with);
                }
            }

            json_response($service_categories);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Get a single service category.
     *
     * @param int|null $id Service category ID.
     */
    public function show(int $id = NULL)
    {
        try
        {
            $fields = $this->api->request_fields();
            
            $with = $this->api->request_with();

            $service_category = $this->service_categories_model->find($id);

            $this->service_categories_model->api_encode($service_category);

            if ( ! empty($fields))
            {
                $this->service_categories_model->only($service_category, $fields);
            }

            if ( ! empty($with))
            {
                $this->service_categories_model->load($service_category, $with);
            }

            if ( ! $service_category)
            {
                response('', 404);

                return;
            }

            json_response($service_category);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create a service category.
     */
    public function store()
    {
        try
        {
            $service_category = request();

            $this->service_categories_model->api_decode($service_category);

            if (array_key_exists('id', $service_category))
            {
                unset($service_category['id']);
            }

            $service_category_id = $this->service_categories_model->save($service_category);

            $created_service_category = $this->service_categories_model->find($service_category_id);

            $this->service_categories_model->api_encode($created_service_category);

            json_response($created_service_category, 201);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update a service category.
     *
     * @param int $id Service category ID.
     */
    public function update(int $id)
    {
        try
        {
            $occurrences = $this->service_categories_model->get(['id' => $id]);

            if (empty($occurrences))
            {
                response('', 404);

                return;
            }

            $original_service_category = $occurrences[0];

            $service_category = request();

            $this->service_categories_model->api_decode($service_category, $original_service_category);

            $service_category_id = $this->service_categories_model->save($service_category);

            $updated_service_category = $this->service_categories_model->find($service_category_id);

            $this->service_categories_model->api_encode($updated_service_category);

            json_response($updated_service_category);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Delete a service category.
     *
     * @param int $id Service category ID.
     */
    public function destroy(int $id)
    {
        try
        {
            $occurrences = $this->service_categories_model->get(['id' => $id]);

            if (empty($occurrences))
            {
                response('', 404);

                return;
            }

            $this->service_categories_model->delete($id);

            response('', 204);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
