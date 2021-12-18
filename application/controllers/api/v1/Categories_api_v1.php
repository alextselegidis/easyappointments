<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
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

        $this->load->model('categories_model');

        $this->load->library('api');

        $this->api->auth();

        $this->api->model('categories_model');
    }

    /**
     * Get a category collection.
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

            $categories = empty($keyword)
                ? $this->categories_model->get(NULL, $limit, $offset, $order_by)
                : $this->categories_model->search($keyword, $limit, $offset, $order_by);

            foreach ($categories as &$category)
            {
                $this->categories_model->api_encode($category);

                if ( ! empty($fields))
                {
                    $this->categories_model->only($category, $fields);
                }

                if ( ! empty($with))
                {
                    $this->categories_model->load($category, $with);
                }
            }

            json_response($categories);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Get a single category.
     *
     * @param int|null $id Category ID.
     */
    public function show(int $id = NULL)
    {
        try
        {
            $fields = $this->api->request_fields();
            
            $with = $this->api->request_with();

            $category = $this->categories_model->find($id);

            $this->categories_model->api_encode($category);

            if ( ! empty($fields))
            {
                $this->categories_model->only($category, $fields);
            }

            if ( ! empty($with))
            {
                $this->categories_model->load($category, $with);
            }

            if ( ! $category)
            {
                response('', 404);

                return;
            }

            json_response($category);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create a category.
     */
    public function store()
    {
        try
        {
            $category = request();

            $this->categories_model->api_decode($category);

            if (array_key_exists('id', $category))
            {
                unset($category['id']);
            }

            $category_id = $this->categories_model->save($category);

            $created_category = $this->categories_model->find($category_id);

            $this->categories_model->api_encode($created_category);

            json_response($created_category, 201);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update a category.
     *
     * @param int $id Category ID.
     */
    public function update(int $id)
    {
        try
        {
            $occurrences = $this->categories_model->get(['id' => $id]);

            if (empty($occurrences))
            {
                response('', 404);

                return;
            }

            $original_category = $occurrences[0];

            $category = request();

            $this->categories_model->api_decode($category, $original_category);

            $category_id = $this->categories_model->save($category);

            $updated_category = $this->categories_model->find($category_id);

            $this->categories_model->api_encode($updated_category);

            json_response($updated_category);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Delete a category.
     *
     * @param int $id Category ID.
     */
    public function destroy(int $id)
    {
        try
        {
            $occurrences = $this->categories_model->get(['id' => $id]);

            if (empty($occurrences))
            {
                response('', 404);

                return;
            }

            $this->categories_model->delete($id);

            response('', 204);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
