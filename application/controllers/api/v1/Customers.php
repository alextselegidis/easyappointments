<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/API_V1_Controller.php';

use EA\Engine\Api\V1\Request;
use EA\Engine\Api\V1\Response;
use EA\Engine\Types\NonEmptyText;

/**
 * Customers Controller
 *
 * @package Controllers
 */
class Customers extends API_V1_Controller {
    /**
     * Customers Resource Parser
     *
     * @var \EA\Engine\Api\V1\Parsers\Customers
     */
    protected $parser;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('customers_model');
        $this->parser = new \EA\Engine\Api\V1\Parsers\Customers;
    }

    /**
     * GET API Method
     *
     * @param int $id Optional (null), the record ID to be returned.
     */
    public function get($id = NULL)
    {
        try
        {
            $conditions = $id !== NULL ? ['id' => $id] : NULL;

            $customers = $this->customers_model->get_batch($conditions);

            if ($id !== NULL && count($customers) === 0)
            {
                $this->throw_record_not_found();
            }

            $response = new Response($customers);

            $response->encode($this->parser)
                ->search()
                ->sort()
                ->paginate()
                ->minimize()
                ->singleEntry($id)
                ->output();

        }
        catch (Exception $exception)
        {
            $this->handle_exception($exception);
        }
    }

    /**
     * POST API Method
     */
    public function post()
    {
        try
        {
            // Insert the customer to the database.
            $request = new Request();
            $customer = $request->get_body();
            $this->parser->decode($customer);

            if (isset($customer['id']))
            {
                unset($customer['id']);
            }

            $id = $this->customers_model->add($customer);

            // Fetch the new object from the database and return it to the client.
            $batch = $this->customers_model->get_batch(['id' => $id]);
            $response = new Response($batch);
            $status = new NonEmptyText('201 Created');
            $response->encode($this->parser)->singleEntry(TRUE)->output($status);
        }
        catch (Exception $exception)
        {
            $this->handle_exception($exception);
        }
    }

    /**
     * PUT API Method
     *
     * @param int $id The record ID to be updated.
     */
    public function put($id)
    {
        try
        {
            // Update the customer record.
            $batch = $this->customers_model->get_batch(['id' => $id]);

            if ($id !== NULL && count($batch) === 0)
            {
                $this->throw_record_not_found();
            }

            $request = new Request();
            $updated_customer = $request->get_body();
            $base_customer = $batch[0];
            $this->parser->decode($updated_customer, $base_customer);
            $updated_customer['id'] = $id;
            $id = $this->customers_model->add($updated_customer);

            // Fetch the updated object from the database and return it to the client.
            $batch = $this->customers_model->get_batch(['id' => $id]);
            $response = new Response($batch);
            $response->encode($this->parser)->singleEntry($id)->output();
        }
        catch (Exception $exception)
        {
            $this->handle_exception($exception);
        }
    }

    /**
     * DELETE API Method
     *
     * @param int $id The record ID to be deleted.
     */
    public function delete($id)
    {
        try
        {
            $result = $this->customers_model->delete($id);

            $response = new Response([
                'code' => 200,
                'message' => 'Record was deleted successfully!'
            ]);

            $response->output();
        }
        catch (Exception $exception)
        {
            $this->handle_exception($exception);
        }
    }
}
