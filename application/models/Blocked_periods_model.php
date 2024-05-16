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
 * Blocked-Periods model.
 *
 * Handles all the database operations of the blocked-period resource.
 *
 * @package Models
 */
class Blocked_periods_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
    ];

    /**
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'name' => 'name',
        'start' => 'start_datetime',
        'end' => 'end_datetime',
        'notes' => 'notes',
    ];

    /**
     * Save (insert or update) a blocked-period.
     *
     * @param array $blocked_period Associative array with the blocked-period data.
     *
     * @return int Returns the blocked-period ID.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function save(array $blocked_period): int
    {
        $this->validate($blocked_period);

        if (empty($blocked_period['id'])) {
            return $this->insert($blocked_period);
        } else {
            return $this->update($blocked_period);
        }
    }

    /**
     * Validate the blocked-period data.
     *
     * @param array $blocked_period Associative array with the blocked-period data.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function validate(array $blocked_period): void
    {
        // If a blocked-period ID is provided then check whether the record really exists in the database.
        if (!empty($blocked_period['id'])) {
            $count = $this->db->get_where('blocked_periods', ['id' => $blocked_period['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided blocked-period ID does not exist in the database: ' . $blocked_period['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($blocked_period['name']) ||
            empty($blocked_period['start_datetime']) ||
            empty($blocked_period['end_datetime'])
        ) {
            throw new InvalidArgumentException(
                'Not all required fields are provided: ' . print_r($blocked_period, true),
            );
        }

        // Make sure that the start date time is before the end.
        $start_date_time_object = new DateTime($blocked_period['start_datetime']);
        $end_date_time_object = new DateTime($blocked_period['end_datetime']);

        if ($start_date_time_object >= $end_date_time_object) {
            throw new InvalidArgumentException('The start must be before the end date time value.');
        }
    }

    /**
     * Insert a new blocked-period into the database.
     *
     * @param array $blocked_period Associative array with the blocked-period data.
     *
     * @return int Returns the blocked-period ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $blocked_period): int
    {
        $blocked_period['create_datetime'] = date('Y-m-d H:i:s');
        $blocked_period['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('blocked_periods', $blocked_period)) {
            throw new RuntimeException('Could not insert blocked-period.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing blocked-period.
     *
     * @param array $blocked_period Associative array with the blocked-period data.
     *
     * @return int Returns the blocked-period ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $blocked_period): int
    {
        $blocked_period['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('blocked_periods', $blocked_period, ['id' => $blocked_period['id']])) {
            throw new RuntimeException('Could not update blocked periods.');
        }

        return $blocked_period['id'];
    }

    /**
     * Remove an existing blocked-period from the database.
     *
     * @param int $blocked_period_id Blocked period ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $blocked_period_id): void
    {
        $this->db->delete('blocked_periods', ['id' => $blocked_period_id]);
    }

    /**
     * Get a specific blocked-period from the database.
     *
     * @param int $blocked_period_id The ID of the record to be returned.
     *
     * @return array Returns an array with the blocked-period data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $blocked_period_id): array
    {
        $blocked_period = $this->db->get_where('blocked_periods', ['id' => $blocked_period_id])->row_array();

        if (!$blocked_period) {
            throw new InvalidArgumentException(
                'The provided blocked-period ID was not found in the database: ' . $blocked_period_id,
            );
        }

        $this->cast($blocked_period);

        return $blocked_period;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $blocked_period_id Blocked period ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected blocked-period value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $blocked_period_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($blocked_period_id)) {
            throw new InvalidArgumentException('The blocked-period ID argument cannot be empty.');
        }

        // Check whether the service exists.
        $query = $this->db->get_where('blocked_periods', ['id' => $blocked_period_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException(
                'The provided blocked-period ID was not found in the database: ' . $blocked_period_id,
            );
        }

        // Check if the required field is part of the blocked-period data.
        $blocked_period = $query->row_array();

        $this->cast($blocked_period);

        if (!array_key_exists($field, $blocked_period)) {
            throw new InvalidArgumentException(
                'The requested field was not found in the blocked-period data: ' . $field,
            );
        }

        return $blocked_period[$field];
    }

    /**
     * Search blocked periods by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of blocked periods.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null): array
    {
        $blocked_periods = $this->db
            ->select()
            ->from('blocked_periods')
            ->group_start()
            ->like('name', $keyword)
            ->or_like('notes', $keyword)
            ->group_end()
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();

        foreach ($blocked_periods as &$blocked_period) {
            $this->cast($blocked_period);
        }

        return $blocked_periods;
    }

    /**
     * Get all services that match the provided criteria.
     *
     * @param array|string|null $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of blocked periods.
     */
    public function get(
        array|string $where = null,
        int $limit = null,
        int $offset = null,
        string $order_by = null,
    ): array {
        if ($where !== null) {
            $this->db->where($where);
        }

        if ($order_by !== null) {
            $this->db->order_by($order_by);
        }

        $blocked_periods = $this->db->get('blocked_periods', $limit, $offset)->result_array();

        foreach ($blocked_periods as &$blocked_period) {
            $this->cast($blocked_period);
        }

        return $blocked_periods;
    }

    /**
     * Load related resources to a blocked-period.
     *
     * @param array $blocked_period Associative array with the blocked-period data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$blocked_period, array $resources)
    {
        // Blocked periods do not currently have any related resources.
    }

    /**
     * Convert the database blocked-period record to the equivalent API resource.
     *
     * @param array $blocked_period Blocked period data.
     */
    public function api_encode(array &$blocked_period): void
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $blocked_period) ? (int) $blocked_period['id'] : null,
            'name' => $blocked_period['name'],
            'start' => array_key_exists('start_datetime', $blocked_period) ? $blocked_period['start_datetime'] : null,
            'end' => array_key_exists('end_datetime', $blocked_period) ? $blocked_period['end_datetime'] : null,
            'notes' => array_key_exists('notes', $blocked_period) ? $blocked_period['notes'] : null,
        ];

        $blocked_period = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database blocked-period record.
     *
     * @param array $blocked_period API resource.
     * @param array|null $base Base blocked-period data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$blocked_period, array $base = null): void
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('id', $blocked_period)) {
            $decoded_resource['id'] = $blocked_period['id'];
        }

        if (array_key_exists('name', $blocked_period)) {
            $decoded_resource['name'] = $blocked_period['name'];
        }

        if (array_key_exists('start', $blocked_period)) {
            $decoded_resource['start_datetime'] = $blocked_period['start'];
        }

        if (array_key_exists('end', $blocked_period)) {
            $decoded_resource['end_datetime'] = $blocked_period['end'];
        }

        if (array_key_exists('notes', $blocked_period)) {
            $decoded_resource['notes'] = $blocked_period['notes'];
        }

        $blocked_period = $decoded_resource;
    }

    /**
     * Get all the blocked periods that are within the provided period.
     *
     * @param string $start_date
     * @param string $end_date
     *
     * @return array
     */
    public function get_for_period(string $start_date, string $end_date): array
    {
        return $this->query()
            //
            ->group_start()
            ->where('DATE(start_datetime) <=', $start_date)
            ->where('DATE(end_datetime) >=', $end_date)
            ->group_end()
            //
            ->or_group_start()
            ->where('DATE(start_datetime) >=', $start_date)
            ->where('DATE(end_datetime) <=', $end_date)
            ->group_end()
            //
            ->or_group_start()
            ->where('DATE(end_datetime) >', $start_date)
            ->where('DATE(end_datetime) <', $end_date)
            ->group_end()
            //
            ->or_group_start()
            ->where('DATE(start_datetime) >', $start_date)
            ->where('DATE(start_datetime) <', $end_date)
            ->group_end()
            //
            ->get()
            ->result_array();
    }

    /**
     * Get the query builder interface, configured for use with the blocked periods table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('blocked_periods');
    }

    /**
     * Check if a date is blocked by a blocked period.
     *
     * @param string $date
     *
     * @return bool
     */
    public function is_entire_date_blocked(string $date): bool
    {
        return $this->query()
            ->where('DATE(start_datetime) <=', $date)
            ->where('DATE(end_datetime) >=', $date)
            ->get()
            ->num_rows() > 1;
    }
}
