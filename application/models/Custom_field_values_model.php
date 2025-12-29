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
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'customFieldId' => 'id_custom_fields',
        'userId' => 'id_users',
        'value' => 'value',
    ];

    /**
     * Save (insert or update) a custom field value.
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

        // Check if a value already exists for this user and field
        $existing = $this->db
            ->get_where('custom_field_values', [
                'id_custom_fields' => $field_value['id_custom_fields'],
                'id_users' => $field_value['id_users'],
            ])
            ->row_array();

        if ($existing) {
            $field_value['id'] = $existing['id'];
            return $this->update($field_value);
        } else {
            return $this->insert($field_value);
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
        // If a field value ID is provided then check whether the record really exists in the database.
        if (!empty($field_value['id'])) {
            $count = $this->db->get_where('custom_field_values', ['id' => $field_value['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided field value ID does not exist in the database: ' . $field_value['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (empty($field_value['id_custom_fields']) || empty($field_value['id_users'])) {
            throw new InvalidArgumentException(
                'Not all required fields are provided: ' . print_r($field_value, true),
            );
        }

        // Verify that the custom field exists
        $custom_field_exists = $this->db
            ->get_where('custom_fields', ['id' => $field_value['id_custom_fields']])
            ->num_rows();

        if (!$custom_field_exists) {
            throw new InvalidArgumentException(
                'The provided custom field ID does not exist: ' . $field_value['id_custom_fields'],
            );
        }

        // Verify that the user exists
        $user_exists = $this->db->get_where('users', ['id' => $field_value['id_users']])->num_rows();

        if (!$user_exists) {
            throw new InvalidArgumentException('The provided user ID does not exist: ' . $field_value['id_users']);
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
     * Get a specific field value from the database.
     *
     * @param int $field_value_id The ID of the record to be returned.
     *
     * @return array Returns an array with the field value data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $field_value_id): array
    {
        $field_value = $this->db->get_where('custom_field_values', ['id' => $field_value_id])->row_array();

        if (!$field_value) {
            throw new InvalidArgumentException(
                'The provided field value ID was not found in the database: ' . $field_value_id,
            );
        }

        $this->cast($field_value);

        return $field_value;
    }

    /**
     * Get all field values that match the provided criteria.
     *
     * @param array|string|null $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of field values.
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

        if ($order_by) {
            $this->db->order_by($order_by);
        }

        $field_values = $this->db->get('custom_field_values', $limit, $offset)->result_array();

        foreach ($field_values as &$field_value) {
            $this->cast($field_value);
        }

        return $field_values;
    }

    /**
     * Get all field values for a specific user.
     *
     * @param int $user_id User ID.
     *
     * @return array Returns an associative array with field_name => value pairs.
     */
    public function get_by_user(int $user_id): array
    {
        $values = $this->db
            ->select('custom_field_values.*, custom_fields.name, custom_fields.label, custom_fields.type')
            ->from('custom_field_values')
            ->join('custom_fields', 'custom_fields.id = custom_field_values.id_custom_fields')
            ->where('custom_field_values.id_users', $user_id)
            ->get()
            ->result_array();

        $formatted = [];
        foreach ($values as $value) {
            $formatted[$value['name']] = [
                'value' => $value['value'],
                'label' => $value['label'],
                'type' => $value['type'],
            ];
        }

        return $formatted;
    }

    /**
     * Save multiple field values for a user at once.
     *
     * @param int $user_id User ID.
     * @param array $field_values Array of field_id => value pairs.
     *
     * @return void
     */
    public function save_for_user(int $user_id, array $field_values): void
    {
        foreach ($field_values as $field_id => $value) {
            // Skip empty values unless it's explicitly a 0 or "0"
            if ($value === '' || $value === null) {
                // Delete the value if it exists
                $this->db->delete('custom_field_values', [
                    'id_custom_fields' => $field_id,
                    'id_users' => $user_id,
                ]);
                continue;
            }

            $this->save([
                'id_custom_fields' => $field_id,
                'id_users' => $user_id,
                'value' => $value,
            ]);
        }
    }

    /**
     * Delete all field values for a specific user.
     *
     * @param int $user_id User ID.
     *
     * @return void
     */
    public function delete_by_user(int $user_id): void
    {
        $this->db->delete('custom_field_values', ['id_users' => $user_id]);
    }

    /**
     * Delete all values for a specific custom field.
     *
     * @param int $custom_field_id Custom field ID.
     *
     * @return void
     */
    public function delete_by_field(int $custom_field_id): void
    {
        $this->db->delete('custom_field_values', ['id_custom_fields' => $custom_field_id]);
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
