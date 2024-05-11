<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
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
class Appointments extends EA_Controller
{
    public array $allowed_appointment_fields = [
        'id',
        'start_datetime',
        'end_datetime',
        'location',
        'notes',
        'color',
        'is_unavailability',
        'id_users_provider',
        'id_users_customer',
        'id_services',
    ];

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
        $this->load->library('webhooks_client');
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
    public function search(): void
    {
        try {
            if (cannot('view', PRIV_APPOINTMENTS)) {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = request('order_by', 'update_datetime DESC');

            $limit = request('limit', 1000);

            $offset = (int) request('offset', '0');

            $appointments = $this->appointments_model->search($keyword, $limit, $offset, $order_by);

            json_response($appointments);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new appointment.
     */
    public function store(): void
    {
        try {
            if (cannot('add', PRIV_APPOINTMENTS)) {
                abort(403, 'Forbidden');
            }

            $appointment = json_decode(request('appointment'), true);

            $this->appointments_model->only($appointment, $this->allowed_appointment_fields);

            $appointment_id = $this->appointments_model->save($appointment);

            $appointment = $this->appointments_model->find($appointment);

            $this->webhooks_client->trigger(WEBHOOK_APPOINTMENT_SAVE, $appointment);

            json_response([
                'success' => true,
                'id' => $appointment_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Find an appointment.
     */
    public function find(): void
    {
        try {
            if (cannot('view', PRIV_APPOINTMENTS)) {
                abort(403, 'Forbidden');
            }

            $appointment_id = request('appointment_id');

            $appointment = $this->appointments_model->find($appointment_id);

            json_response($appointment);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a appointment.
     */
    public function update(): void
    {
        try {
            if (cannot('edit', PRIV_APPOINTMENTS)) {
                abort(403, 'Forbidden');
            }

            $appointment = json_decode(request('appointment'), true);

            $this->appointments_model->only($appointment, $this->allowed_appointment_fields);

            $appointment_id = $this->appointments_model->save($appointment);

            json_response([
                'success' => true,
                'id' => $appointment_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Remove a appointment.
     */
    public function destroy(): void
    {
        try {
            if (cannot('delete', PRIV_APPOINTMENTS)) {
                abort(403, 'Forbidden');
            }

            $appointment_id = request('appointment_id');

            $appointment = $this->appointments_model->find($appointment_id);

            $this->appointments_model->delete($appointment_id);

            $this->webhooks_client->trigger(WEBHOOK_APPOINTMENT_DELETE, $appointment);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
