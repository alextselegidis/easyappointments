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
 * Working_plan_exceptions model.
 *
 * Handles all the database operations of the working plan exception resource.
 *
 * @package Models
 */
class Working_plan_exceptions_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'id_users_provider' => 'integer',
    ];

    /**
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'startDate' => 'start_date',
        'endDate' => 'end_date',
        'startTime' => 'start_time',
        'endTime' => 'end_time',
        'breaks' => 'breaks',
        'providerId' => 'id_users_provider',
    ];

    /**
     * Save (insert or update) a working plan exception.
     *
     * @param array $working_plan_exception Associative array with the working plan exception data.
     *
     * @return int Returns the working plan exception ID.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function save(array $working_plan_exception): int
    {
        $this->validate($working_plan_exception);

        if (empty($working_plan_exception['id'])) {
            return $this->insert($working_plan_exception);
        } else {
            return $this->update($working_plan_exception);
        }
    }

    /**
     * Validate the working plan exception data.
     *
     * @param array $working_plan_exception Associative array with the working plan exception data.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function validate(array $working_plan_exception): void
    {
        // If a working plan exception ID is provided then check whether the record really exists in the database.
        if (!empty($working_plan_exception['id'])) {
            $count = $this->db
                ->get_where('working_plan_exceptions', ['id' => $working_plan_exception['id']])
                ->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided working plan exception ID does not exist in the database: ' .
                        $working_plan_exception['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($working_plan_exception['start_date']) ||
            empty($working_plan_exception['end_date']) ||
            empty($working_plan_exception['id_users_provider'])
        ) {
            throw new InvalidArgumentException(
                'Not all required fields are provided: ' . print_r($working_plan_exception, true),
            );
        }

        // Validate start_date format
        $start_date = DateTime::createFromFormat('Y-m-d', $working_plan_exception['start_date']);
        if (!$start_date || $start_date->format('Y-m-d') !== $working_plan_exception['start_date']) {
            throw new InvalidArgumentException('Invalid start_date format, expected Y-m-d');
        }

        // Validate end_date format
        $end_date = DateTime::createFromFormat('Y-m-d', $working_plan_exception['end_date']);
        if (!$end_date || $end_date->format('Y-m-d') !== $working_plan_exception['end_date']) {
            throw new InvalidArgumentException('Invalid end_date format, expected Y-m-d');
        }

        // Make sure start_date is before or equal to end_date
        if ($start_date > $end_date) {
            throw new InvalidArgumentException('Start date must be before or equal to end date.');
        }

        // If start_time and end_time are provided, make sure start_time is before end_time
        if (
            !empty($working_plan_exception['start_time']) &&
            !empty($working_plan_exception['end_time']) &&
            $working_plan_exception['start_time'] > $working_plan_exception['end_time']
        ) {
            throw new InvalidArgumentException('Start time must be before end time.');
        }
    }

    /**
     * Insert a new working plan exception into the database.
     *
     * @param array $working_plan_exception Associative array with the working plan exception data.
     *
     * @return int Returns the working plan exception ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $working_plan_exception): int
    {
        $working_plan_exception['create_datetime'] = date('Y-m-d H:i:s');
        $working_plan_exception['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('working_plan_exceptions', $working_plan_exception)) {
            throw new RuntimeException('Could not insert working plan exception.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing working plan exception.
     *
     * @param array $working_plan_exception Associative array with the working plan exception data.
     *
     * @return int Returns the working plan exception ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $working_plan_exception): int
    {
        $working_plan_exception['update_datetime'] = date('Y-m-d H:i:s');

        if (
            !$this->db->update('working_plan_exceptions', $working_plan_exception, [
                'id' => $working_plan_exception['id'],
            ])
        ) {
            throw new RuntimeException('Could not update working plan exception.');
        }

        return $working_plan_exception['id'];
    }

    /**
     * Remove an existing working plan exception from the database.
     *
     * @param int $working_plan_exception_id Working plan exception ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $working_plan_exception_id): void
    {
        $this->db->delete('working_plan_exceptions', ['id' => $working_plan_exception_id]);
    }

    /**
     * Get a specific working plan exception from the database.
     *
     * @param int $working_plan_exception_id The ID of the record to be returned.
     *
     * @return array Returns an array with the working plan exception data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $working_plan_exception_id): array
    {
        $working_plan_exception = $this->db
            ->get_where('working_plan_exceptions', ['id' => $working_plan_exception_id])
            ->row_array();

        if (!$working_plan_exception) {
            throw new InvalidArgumentException(
                'The provided working plan exception ID was not found in the database: ' . $working_plan_exception_id,
            );
        }

        $this->cast($working_plan_exception);

        return $working_plan_exception;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $working_plan_exception_id Working plan exception ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected working plan exception value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $working_plan_exception_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument cannot be empty.');
        }

        if (empty($working_plan_exception_id)) {
            throw new InvalidArgumentException('The working plan exception ID argument cannot be empty.');
        }

        // Check whether the record exists.
        $query = $this->db->get_where('working_plan_exceptions', ['id' => $working_plan_exception_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException(
                'The provided working plan exception ID was not found in the database: ' . $working_plan_exception_id,
            );
        }

        // Check if the required field is part of the data.
        $working_plan_exception = $query->row_array();

        $this->cast($working_plan_exception);

        if (!array_key_exists($field, $working_plan_exception)) {
            throw new InvalidArgumentException(
                'The requested field was not found in the working plan exception data: ' . $field,
            );
        }

        return $working_plan_exception[$field];
    }

    /**
     * Search working plan exceptions by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of working plan exceptions.
     */
    public function search(string $keyword, ?int $limit = null, ?int $offset = null, ?string $order_by = null): array
    {
        $working_plan_exceptions = $this->db
            ->select()
            ->from('working_plan_exceptions')
            ->group_start()
            ->like('start_date', $keyword)
            ->or_like('end_date', $keyword)
            ->group_end()
            ->limit($limit)
            ->offset($offset)
            ->order_by($this->quote_order_by($order_by))
            ->get()
            ->result_array();

        foreach ($working_plan_exceptions as &$working_plan_exception) {
            $this->cast($working_plan_exception);
        }

        return $working_plan_exceptions;
    }

    /**
     * Get all working plan exceptions that match the provided criteria.
     *
     * @param array|string|null $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of working plan exceptions.
     */
    public function get(
        array|string|null $where = null,
        ?int $limit = null,
        ?int $offset = null,
        ?string $order_by = null,
    ): array {
        if ($where !== null) {
            $this->db->where($where);
        }

        if ($order_by !== null) {
            $this->db->order_by($this->quote_order_by($order_by));
        }

        $working_plan_exceptions = $this->db->get('working_plan_exceptions', $limit, $offset)->result_array();

        foreach ($working_plan_exceptions as &$working_plan_exception) {
            $this->cast($working_plan_exception);
        }

        return $working_plan_exceptions;
    }

    /**
     * Load related resources to a working plan exception.
     *
     * @param array $working_plan_exception Associative array with the working plan exception data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$working_plan_exception, array $resources): void
    {
        // Working plan exceptions do not currently have any related resources.
    }

    /**
     * Convert the database working plan exception record to the equivalent API resource.
     *
     * @param array $working_plan_exception Working plan exception data.
     */
    public function api_encode(array &$working_plan_exception): void
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $working_plan_exception)
                ? (int) $working_plan_exception['id']
                : null,
            'startDate' => $working_plan_exception['start_date'] ?? null,
            'endDate' => $working_plan_exception['end_date'] ?? null,
            'startTime' => $working_plan_exception['start_time'] ?? null,
            'endTime' => $working_plan_exception['end_time'] ?? null,
            'breaks' => array_key_exists('breaks', $working_plan_exception) && $working_plan_exception['breaks']
                ? json_decode($working_plan_exception['breaks'], true)
                : [],
            'providerId' => array_key_exists('id_users_provider', $working_plan_exception)
                ? (int) $working_plan_exception['id_users_provider']
                : null,
        ];

        $working_plan_exception = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database working plan exception record.
     *
     * @param array $working_plan_exception API resource.
     * @param array|null $base Base working plan exception data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$working_plan_exception, ?array $base = null): void
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('id', $working_plan_exception)) {
            $decoded_resource['id'] = $working_plan_exception['id'];
        }

        if (array_key_exists('startDate', $working_plan_exception)) {
            $decoded_resource['start_date'] = $working_plan_exception['startDate'];
        }

        if (array_key_exists('endDate', $working_plan_exception)) {
            $decoded_resource['end_date'] = $working_plan_exception['endDate'];
        }

        if (array_key_exists('startTime', $working_plan_exception)) {
            $decoded_resource['start_time'] = $working_plan_exception['startTime'];
        }

        if (array_key_exists('endTime', $working_plan_exception)) {
            $decoded_resource['end_time'] = $working_plan_exception['endTime'];
        }

        if (array_key_exists('breaks', $working_plan_exception)) {
            $decoded_resource['breaks'] = json_encode($working_plan_exception['breaks']);
        }

        if (array_key_exists('providerId', $working_plan_exception)) {
            $decoded_resource['id_users_provider'] = $working_plan_exception['providerId'];
        }

        $working_plan_exception = $decoded_resource;
    }

    /**
     * Get all the working plan exceptions that are within the provided period for a provider.
     *
     * @param int $provider_id Provider ID.
     * @param string $start_date Start date (Y-m-d).
     * @param string $end_date End date (Y-m-d).
     *
     * @return array
     */
    public function get_for_period(int $provider_id, string $start_date, string $end_date): array
    {
        // Find exceptions that overlap with the given period
        return $this->db
            ->select()
            ->from('working_plan_exceptions')
            ->where('id_users_provider', $provider_id)
            ->group_start()
            // Exception starts within the period
            ->group_start()
            ->where('start_date >=', $start_date)
            ->where('start_date <=', $end_date)
            ->group_end()
            // Or exception ends within the period
            ->or_group_start()
            ->where('end_date >=', $start_date)
            ->where('end_date <=', $end_date)
            ->group_end()
            // Or exception spans the entire period
            ->or_group_start()
            ->where('start_date <=', $start_date)
            ->where('end_date >=', $end_date)
            ->group_end()
            ->group_end()
            ->order_by('start_date')
            ->get()
            ->result_array();
    }

    /**
     * Get all working plan exceptions for a provider as an associative array keyed by date.
     * This expands date ranges into individual dates for backward compatibility.
     *
     * @param int $provider_id Provider ID.
     *
     * @return array Associative array keyed by date.
     */
    public function get_by_provider(int $provider_id): array
    {
        $exceptions = $this->db
            ->select()
            ->from('working_plan_exceptions')
            ->where('id_users_provider', $provider_id)
            ->order_by('start_date')
            ->get()
            ->result_array();

        $result = [];

        foreach ($exceptions as $exception) {
            // Expand the date range into individual dates
            $start = new DateTime($exception['start_date']);
            $end = new DateTime($exception['end_date']);
            $end->modify('+1 day'); // Include end date

            $interval = new DateInterval('P1D');
            $period = new DatePeriod($start, $interval, $end);

            foreach ($period as $date) {
                $date_str = $date->format('Y-m-d');

                // If start_time is null, the exception represents a day off
                if (empty($exception['start_time'])) {
                    $result[$date_str] = null;
                } else {
                    $result[$date_str] = [
                        'start' => $exception['start_time'],
                        'end' => $exception['end_time'],
                        'breaks' => !empty($exception['breaks']) ? json_decode($exception['breaks'], true) : [],
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Get all working plan exceptions for a provider as an array with API format.
     * Returns array of exceptions with startDate, endDate, startTime, endTime, breaks, id.
     *
     * @param int $provider_id Provider ID.
     *
     * @return array Array of exception objects.
     */
    public function get_all_by_provider(int $provider_id): array
    {
        $exceptions = $this->db
            ->select()
            ->from('working_plan_exceptions')
            ->where('id_users_provider', $provider_id)
            ->order_by('start_date')
            ->get()
            ->result_array();

        $result = [];

        foreach ($exceptions as $exception) {
            $result[] = [
                'id' => (int) $exception['id'],
                'startDate' => $exception['start_date'],
                'endDate' => $exception['end_date'],
                'startTime' => $exception['start_time'],
                'endTime' => $exception['end_time'],
                'breaks' => !empty($exception['breaks']) ? json_decode($exception['breaks'], true) : [],
            ];
        }

        return $result;
    }

    /**
     * Get the query builder interface, configured for use with the working plan exceptions table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('working_plan_exceptions');
    }

    /**
     * Find a working plan exception by provider and date (checks if date falls within any exception range).
     *
     * @param int $provider_id Provider ID.
     * @param string $date Date (Y-m-d).
     *
     * @return array|null Returns the exception or null if not found.
     */
    public function find_by_provider_and_date(int $provider_id, string $date): ?array
    {
        $working_plan_exception = $this->db
            ->select()
            ->from('working_plan_exceptions')
            ->where('id_users_provider', $provider_id)
            ->where('start_date <=', $date)
            ->where('end_date >=', $date)
            ->get()
            ->row_array();

        if (!$working_plan_exception) {
            return null;
        }

        $this->cast($working_plan_exception);

        return $working_plan_exception;
    }

    /**
     * Delete working plan exceptions that contain the given date for a provider.
     *
     * @param int $provider_id Provider ID.
     * @param string $date Date (Y-m-d).
     */
    public function delete_by_provider_and_date(int $provider_id, string $date): void
    {
        // Delete any exception where the date falls within the range
        $this->db
            ->where('id_users_provider', $provider_id)
            ->where('start_date <=', $date)
            ->where('end_date >=', $date)
            ->delete('working_plan_exceptions');
    }
}
