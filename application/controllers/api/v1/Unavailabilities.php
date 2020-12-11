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
 * Unavailabilities Controller
 *
 * @package Controllers
 */
class Unavailabilities extends API_V1_Controller {
    /**
     * Unavailabilities Resource Parser
     *
     * @var \EA\Engine\Api\V1\Parsers\Unavailabilities
     */
    protected $parser;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('appointments_model');
        $this->parser = new \EA\Engine\Api\V1\Parsers\Unavailabilities;
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
            $where = $id !== NULL ? ['id' => $id] : ['is_unavailable' => TRUE];

            $unavailabilities = $this->appointments_model->get_batch($where);

            if ($id !== NULL && count($unavailabilities) === 0)
            {
                $this->throw_record_not_found();
            }

            $response = new Response($unavailabilities);

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
            // Insert the appointment to the database.
            $request = new Request();
            $unavailability = $request->get_body();
            $this->parser->decode($unavailability);

            if (isset($unavailability['id']))
            {
                unset($unavailability['id']);
            }

            $id = $this->appointments_model->add_unavailable($unavailability);

            // Fetch the new object from the database and return it to the client.
            $batch = $this->appointments_model->get_batch(['id' => $id]);
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
            // Update the appointment record.
            $batch = $this->appointments_model->get_batch(['id' => $id]);

            if ($id !== NULL && count($batch) === 0)
            {
                $this->throw_record_not_found();
            }

            $request = new Request();
            $updatedUnavailability = $request->get_body();
            $baseUnavailability = $batch[0];
            $this->parser->decode($updatedUnavailability, $baseUnavailability);
            $updatedUnavailability['id'] = $id;
            $id = $this->appointments_model->add_unavailable($updatedUnavailability);

            // Fetch the updated object from the database and return it to the client.
            $batch = $this->appointments_model->get_batch(['id' => $id]);
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
            $result = $this->appointments_model->delete_unavailable($id);

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
