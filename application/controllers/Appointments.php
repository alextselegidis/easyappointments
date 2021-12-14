<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Appointments controller.
 *
 * Handles the appointments related operations.
 * 
 * Notice: This file used to have the booking page related code which since v1.5 has now moved to the Booking.php
 * controller for improved consistency.
 *
 * @package Controllers
 */
class Appointments extends EA_Controller {
    /**
     * Appointments constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
    }

    /**
     * Support backwards compatibility for appointment links that still point to this URL. 
     * 
     * @param string $appointment_hash
     * 
     * @deprecated Since 1.5
     */
    public function index(string $appointment_hash = '')
    {
        redirect('booking/' . $appointment_hash);
    }

    /**
     * Filter appointments by the provided keyword.
     */
    public function search()
    {
        try
        {
            if (cannot('view', 'appointments'))
            {
                show_error('Forbidden', 403);
            }

            $keyword = request('keyword', '');

            $order_by = 'name ASC';

            $limit = request('limit', 1000);
            
            $offset = 0;

            $appointments = $this->appointments_model->search($keyword, $limit, $offset, $order_by);

            json_response($appointments);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create a appointment.
     */
    public function create()
    {
        try
        {
            $appointment = json_decode(request('appointment'), TRUE);

            if (cannot('add', 'appointments'))
            {
                show_error('Forbidden', 403);
            }

            $appointment_id = $this->appointments_model->save($appointment);

            json_response([
                'success' => TRUE,
                'id' => $appointment_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update a appointment.
     */
    public function update()
    {
        try
        {
            $appointment = json_decode(request('appointment'), TRUE);

            if (cannot('edit', 'appointments'))
            {
                show_error('Forbidden', 403);
            }

            $appointment_id = $this->appointments_model->save($appointment);

            json_response([
                'success' => TRUE,
                'id' => $appointment_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Remove a appointment.
     */
    public function destroy()
    {
        try
        {
            if (cannot('delete', 'appointments'))
            {
                show_error('Forbidden', 403);
            }

            $appointment_id = request('appointment_id');

            $this->appointments_model->delete($appointment_id);

            json_response([
                'success' => TRUE,
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Find an appointment.
     */
    public function find()
    {
        try
        {
            if (cannot('delete', PRIV_APPOINTMENTS))
            {
                show_error('Forbidden', 403);
            }

            $appointment_id = request('appointment_id');

            $appointment = $this->appointments_model->find($appointment_id);

            json_response($appointment);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
