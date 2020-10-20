<?php defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/API_V1_Controller.php';

use \EA\Engine\Api\V1\Response;
use \EA\Engine\Api\V1\Request;
use \EA\Engine\Types\NonEmptyText;

/**
 * Appointments Controller
 *
 * @package Controllers
 * @subpackage API
 */
class Appointments extends API_V1_Controller {
    /**
     * Appointments Resource Parser
     *
     * @var \EA\Engine\Api\V1\Parsers\Appointments
     */
    protected $parser;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('appointments_model');
        $this->parser = new \EA\Engine\Api\V1\Parsers\Appointments;
        $this->load->library('appointmentservice');
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
            $conditions = [
                'is_unavailable' => FALSE
            ];

            if ($id !== NULL)
            {
                $conditions['id'] = $id;
            }

            $appointments = $this->appointments_model->get_batch($conditions, array_key_exists('aggregates', $_GET));

            if ($id !== NULL && count($appointments) === 0)
            {
                $this->_throwRecordNotFound();
            }

            $response = new Response($appointments);

            $response->encode($this->parser)
                ->search()
                ->sort()
                ->paginate()
                ->minimize()
                ->singleEntry($id)
                ->output();

        }
        catch (\Exception $exception)
        {
            exit($this->_handleException($exception));
        }
    }

    /**
     * POST API Method
     */
    public function post()
    {
        try
        {
            $request = new Request();
            $appointment = $request->getBody();
            $this->parser->decode($appointment);

            if (isset($appointment['id']))
            {
                unset($appointment['id']);
            }

            // Check user provider exists
            if (!$this->appointmentservice->checkAppointment($appointment)){
                $this->_throwRecordNotFound();
            }

            if (!$this->appointmentservice->check_datetime_availability($appointment)){
                log_message('info', 'Not available');
                $this->_throwRecordAlreadyExists('Provider not available.');
            } 

            log_message('info', 'Everything seems available...');

            // Insert the appointment into the database. 
            $id = $this->appointments_model->add($appointment);

            // Fetch the new object from the database and return it to the client.
            $batch = $this->appointments_model->get_batch('id = ' . $id);
            $appointment['hash'] = $batch[0]['hash'];
            $appointment['id'] = $id;
            log_message('info', 'batch => ' . json_encode($appointment));
            log_message('info', 'batch => ' . json_encode($batch));
            log_message('info', 'hash => ' . $batch[0]['hash']);

            // Send notifications
            $provider = $this->providers_model->get_row($appointment['id_users_provider']);
            $service  = $this->services_model->get_row($appointment['id_services']);
            $customer = $this->customers_model->get_row($appointment['id_users_customer']);
            $this->appointmentservice->sendAppointmentNotification($appointment, $service, $provider, $customer, true);

            $response = new Response($batch);
            $status = new NonEmptyText('201 Created');
            $response->encode($this->parser)->singleEntry(TRUE)->output($status);
        }
        catch (\Exception $exception)
        {
            exit($this->_handleException($exception));
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
            log_message('info', 'Updating appointment');
            // Update the appointment record. 
            $batch = $this->appointments_model->get_batch('id = ' . $id);

            if ($id !== NULL && count($batch) === 0)
            {
                $this->_throwRecordNotFound();
            }

            $request = new Request();
            $updatedAppointment = $request->getBody();
            
            $baseAppointment = $batch[0];
            $this->parser->decode($updatedAppointment, $baseAppointment);
            $updatedAppointment['id'] = $id;
            // Check appointment
            if (!$this->appointmentservice->checkAppointment($updatedAppointment)){
                $this->_throwRecordNotFound();
            }
            $id = $this->appointments_model->add($updatedAppointment);
            // Check availability
            if (!$this->appointmentservice->check_datetime_availability($updatedAppointment)){
                log_message('info', 'Not available');
                $this->_throwRecordAlreadyExists('Provider not available.');
            }

            // Send notifications
            $provider = $this->providers_model->get_row($updatedAppointment['id_users_provider']);
            $service  = $this->services_model->get_row($updatedAppointment['id_services']);
            $customer = $this->customers_model->get_row($updatedAppointment['id_users_customer']);
            $this->appointmentservice->sendAppointmentNotification($updatedAppointment, $service, $provider, $customer, false);

            // Fetch the updated object from the database and return it to the client.
            $batch = $this->appointments_model->get_batch('id = ' . $id);
            $response = new Response($batch);
            $response->encode($this->parser)->singleEntry($id)->output();
        }
        catch (\Exception $exception)
        {
            exit($this->_handleException($exception));
        }
    }

    /**
     * DELETE API Method
     *
     * @param int $appointment_id The record ID to be cancelled.
     * @param string $reason Reason why the appointment is cancelled.
     */
    public function delete($appointment_id, $reason='')
    {
        try
        {
            $appointment = $this->appointments_model->get_row($appointment_id);
            if (count($appointment)  == 0){
                $this->_throwRecordNotFound();
            }
            // Note: Reason will not be available from the API
            $this->appointmentservice->cancel($appointment['hash'], $reason);

            $response = new Response([
                'code' => 200,
                'message' => 'Appointment was cancelled successfully!'
            ]);

            $response->output();
        }
        catch (\Exception $exception)
        {
            exit($this->_handleException($exception));
        }
    }
}
