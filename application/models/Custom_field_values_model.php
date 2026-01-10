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
 * Custom field values model.
 *
 * Handles all the database operations of the custom field values resource.
 *
 * @package Models
 */
class Custom_field_values_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'id_custom_fields' => 'integer',
        'id_users' => 'integer',
    ];

    /**
     * Custom_field_values_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Save (insert or update) a field value.
     *
     * @param array $field_value Associative array with the field value data.
     *
     * @return int Returns the field value ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $field_value): int
    {
        $this->validate($field_value);

        if (empty($field_value['id'])) {
            return $this->insert($field_value);
        } else {
            return $this->update($field_value);
        }
    }

    /**
     * Validate the field value data.
     *
     * @param array $field_value Associative array with the field value data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $field_value): void
    {
        // If a value ID is provided then check whether the record exists in the database.
        if (!empty($field_value['id'])) {
            $count = $this->db->get_where('custom_field_values', ['id' => $field_value['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException('The provided field value ID does not exist in the database: ' . $field_value['id']);
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($field_value['id_custom_fields']) ||
            empty($field_value['id_users'])
        ) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($field_value, true));
        }
    }

    /**
     * Insert a new field value into the database.
     *
     * @param array $field_value Associative array with the field value data.
     *
     * @return int Returns the field value ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $field_value): int
    {
        $field_value['create_datetime'] = date('Y-m-d H:i:s');
        $field_value['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('custom_field_values', $field_value)) {
            throw new RuntimeException('Could not insert custom field value.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing field value.
     *
     * @param array $field_value Associative array with the field value data.
     *
     * @return int Returns the field value ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $field_value): int
    {
        $field_value['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('custom_field_values', $field_value, ['id' => $field_value['id']])) {
            throw new RuntimeException('Could not update custom field value.');
        }

        return $field_value['id'];
    }

    /**
     * Remove an existing field value from the database.
     *
     * @param int $field_value_id Field value ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $field_value_id): void
    {
        $this->db->delete('custom_field_values', ['id' => $field_value_id]);
    }

    /**
     * Get a specific field value record from the database.
     *
     * @param array|string|null $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of field values.
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
            $this->db->order_by($order_by);
        }

        $field_values = $this->db->get('custom_field_values', $limit, $offset)->result_array();

        foreach ($field_values as &$field_value) {
            $this->cast($field_value);
        }

        return $field_values;
    }

    /**
     * Get field values for a specific user.
     *
     * @param int $user_id User ID.
     *
     * @return array Returns an array of field values.
     */
    public function get_by_user(int $user_id): array
    {
        return $this->get(['id_users' => $user_id]);
    }

    /**
     * Get the query builder interface, configured for use with the custom field values table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('custom_field_values');
    }
}
