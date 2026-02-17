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
 * Working_plan_exceptions controller.
 *
 * Handles the working plan exception related operations.
 *
 * @package Controllers
 */
class Working_plan_exceptions extends EA_Controller
{
    public array $allowed_working_plan_exception_fields = [
        'id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'breaks',
        'id_users_provider',
    ];

    public array $optional_working_plan_exception_fields = [
        'start_time',
        'end_time',
        'breaks',
    ];

    /**
     * Working_plan_exceptions constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('working_plan_exceptions_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
    }

    /**
     * Filter working plan exceptions by the provided keyword.
     */
    public function search(): void
    {
        try {
            method('post');

            if (cannot('view', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            check('keyword', 'string|null');
            check('order_by', 'string|null');
            check('limit', 'numeric|null');
            check('offset', 'numeric|null');
            check('provider_id', 'numeric|null');

            $keyword = request('keyword', '');

            $order_by = request('order_by', 'date ASC');

            $limit = request('limit', 1000);

            $offset = (int) request('offset', '0');

            $provider_id = request('provider_id');

            $where = null;

            if (!empty($provider_id)) {
                $where = ['id_users_provider' => $provider_id];
            }

            $working_plan_exceptions = empty($keyword)
                ? $this->working_plan_exceptions_model->get($where, $limit, $offset, $order_by)
                : $this->working_plan_exceptions_model->search($keyword, $limit, $offset, $order_by);

            json_response($working_plan_exceptions);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new working plan exception.
     */
    public function store(): void
    {
        try {
            method('post');

            if (cannot('add', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            check('working_plan_exception', 'array');

            $working_plan_exception = request('working_plan_exception');

            $this->working_plan_exceptions_model->only(
                $working_plan_exception,
                $this->allowed_working_plan_exception_fields,
            );

            $this->working_plan_exceptions_model->optional(
                $working_plan_exception,
                $this->optional_working_plan_exception_fields,
            );

            // Encode breaks as JSON if provided as array
            if (isset($working_plan_exception['breaks']) && is_array($working_plan_exception['breaks'])) {
                $working_plan_exception['breaks'] = json_encode($working_plan_exception['breaks']);
            }

            $working_plan_exception_id = $this->working_plan_exceptions_model->save($working_plan_exception);

            $working_plan_exception = $this->working_plan_exceptions_model->find($working_plan_exception_id);

            json_response([
                'success' => true,
                'id' => $working_plan_exception_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Find a working plan exception.
     */
    public function find(): void
    {
        try {
            method('get');

            if (cannot('view', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            check('working_plan_exception_id', 'numeric');

            $working_plan_exception_id = request('working_plan_exception_id');

            // Validate ID is a positive integer
            if (
                empty($working_plan_exception_id) ||
                !filter_var($working_plan_exception_id, FILTER_VALIDATE_INT) ||
                $working_plan_exception_id <= 0
            ) {
                throw new InvalidArgumentException('Invalid working plan exception ID provided.');
            }

            $working_plan_exception = $this->working_plan_exceptions_model->find($working_plan_exception_id);

            // Decode breaks JSON
            if (!empty($working_plan_exception['breaks'])) {
                $working_plan_exception['breaks'] = json_decode($working_plan_exception['breaks'], true);
            }

            json_response($working_plan_exception);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a working plan exception.
     */
    public function update(): void
    {
        try {
            method('post');

            if (cannot('edit', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            check('working_plan_exception', 'array');

            $working_plan_exception = request('working_plan_exception');

            $this->working_plan_exceptions_model->only(
                $working_plan_exception,
                $this->allowed_working_plan_exception_fields,
            );

            $this->working_plan_exceptions_model->optional(
                $working_plan_exception,
                $this->optional_working_plan_exception_fields,
            );

            // Encode breaks as JSON if provided as array
            if (isset($working_plan_exception['breaks']) && is_array($working_plan_exception['breaks'])) {
                $working_plan_exception['breaks'] = json_encode($working_plan_exception['breaks']);
            }

            $working_plan_exception_id = $this->working_plan_exceptions_model->save($working_plan_exception);

            $working_plan_exception = $this->working_plan_exceptions_model->find($working_plan_exception_id);

            json_response([
                'success' => true,
                'id' => $working_plan_exception_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Remove a working plan exception.
     */
    public function destroy(): void
    {
        try {
            method('post');

            if (cannot('delete', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            check('working_plan_exception_id', 'numeric');

            $working_plan_exception_id = request('working_plan_exception_id');

            // Validate ID is a positive integer
            if (
                empty($working_plan_exception_id) ||
                !filter_var($working_plan_exception_id, FILTER_VALIDATE_INT) ||
                $working_plan_exception_id <= 0
            ) {
                throw new InvalidArgumentException('Invalid working plan exception ID provided.');
            }

            $this->working_plan_exceptions_model->delete($working_plan_exception_id);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get working plan exceptions for a specific provider.
     */
    public function get_by_provider(): void
    {
        try {
            method('get');

            if (cannot('view', PRIV_USERS)) {
                abort(403, 'Forbidden');
            }

            check('provider_id', 'numeric');

            $provider_id = request('provider_id');

            // Validate provider_id is a positive integer
            if (
                empty($provider_id) ||
                !filter_var($provider_id, FILTER_VALIDATE_INT) ||
                $provider_id <= 0
            ) {
                throw new InvalidArgumentException('Invalid provider ID provided.');
            }

            $working_plan_exceptions = $this->working_plan_exceptions_model->get_by_provider((int) $provider_id);

            json_response($working_plan_exceptions);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
