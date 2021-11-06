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
 * Providers API v1 controller.
 *
 * @package Controllers
 */
class Providers_api_v1 extends EA_Controller {
    /**
     * Providers_api_v1 constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('providers_model');

        $this->load->library('api');

        $this->api->cors();

        $this->api->auth();

        $this->api->model('providers_model');
    }

    /**
     * Get a provider collection.
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
            
            $attach = $this->api->request_attach();

            $providers = empty($keyword)
                ? $this->providers_model->get(NULL, $limit, $offset, $order_by)
                : $this->providers_model->search($keyword, $limit, $offset, $order_by);

            foreach ($providers as &$provider)
            {
                $this->providers_model->api_encode($provider);

                if ( ! empty($fields))
                {
                    $this->providers_model->only($provider, $fields);
                }

                if ( ! empty($attach))
                {
                    $this->providers_model->attach($provider, $attach);
                }
            }

            json_response($providers);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Get a single provider.
     *
     * @param int|null $id Provider ID.
     */
    public function show(int $id = NULL)
    {
        try
        {
            $fields = $this->api->request_fields();
            
            $attach = $this->api->request_attach();

            $provider = $this->providers_model->find($id);

            $this->providers_model->api_encode($provider);

            if ( ! empty($fields))
            {
                $this->providers_model->only($provider, $fields);
            }

            if ( ! empty($attach))
            {
                $this->providers_model->attach($provider, $attach);
            }

            if ( ! $provider)
            {
                response('', 404);

                return;
            }

            json_response($provider);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create a provider.
     */
    public function store()
    {
        try
        {
            $provider = request();

            $this->providers_model->api_decode($provider);

            if (array_key_exists('id', $provider))
            {
                unset($provider['id']);
            }

            if ( ! array_key_exists('services', $provider))
            {
                throw new Exception('No services property provided.');
            }

            if ( ! array_key_exists('settings', $provider))
            {
                throw new Exception('No settings property provided.');
            }

            if ( ! array_key_exists('working_plan', $provider['settings']))
            {
                $provider['settings']['working_plan'] = setting('company_working_plan');
            }

            $provider_id = $this->providers_model->save($provider);

            $created_provider = $this->providers_model->find($provider_id);

            $this->providers_model->api_encode($created_provider);

            json_response($created_provider, 201);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update a provider.
     *
     * @param int $id Provider ID.
     */
    public function update(int $id)
    {
        try
        {
            $occurrences = $this->providers_model->get(['id' => $id]);

            if (empty($occurrences))
            {
                response('', 404);

                return;
            }

            $original_provider = $occurrences[0];

            $provider = request();

            $this->providers_model->api_decode($provider, $original_provider);

            $provider_id = $this->providers_model->save($provider);

            $updated_provider = $this->providers_model->find($provider_id);

            $this->providers_model->api_encode($updated_provider);

            json_response($updated_provider);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Delete a provider.
     *
     * @param int $id Provider ID.
     */
    public function destroy(int $id)
    {
        try
        {
            $occurrences = $this->providers_model->get(['id' => $id]);

            if (empty($occurrences))
            {
                response('', 404);

                return;
            }

            $this->providers_model->delete($id);

            response('', 204);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
