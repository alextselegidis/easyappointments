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
 * Custom fields model.
 *
 * Handles all the database operations of the custom fields resource.
 *
 * @package Models
 */
class Custom_fields_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'required' => 'boolean',
        'display_column' => 'integer',
        'sort_order' => 'integer',
        'active' => 'boolean',
    ];

    /**
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'name' => 'name',
        'label' => 'label',
        'type' => 'type',
        'required' => 'required',
        'displayColumn' => 'display_column',
        'sortOrder' => 'sort_order',
        'active' => 'active',
    ];

    /**
     * Save (insert or update) a custom field.
     *
     * @param array $custom_field Associative array with the custom field data.
     *
     * @return int Returns the custom field ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $custom_field): int
    {
        $this->validate($custom_field);

        if (empty($custom_field['id'])) {
            return $this->insert($custom_field);
        } else {
            return $this->update($custom_field);
        }
    }

    /**
     * Validate the custom field data.
     *
     * @param array $custom_field Associative array with the custom field data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $custom_field): void
    {
        // If a custom field ID is provided then check whether the record really exists in the database.
        if (!empty($custom_field['id'])) {
            $count = $this->db->get_where('custom_fields', ['id' => $custom_field['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided custom field ID does not exist in the database: ' . $custom_field['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (empty($custom_field['name']) || empty($custom_field['label'])) {
            throw new InvalidArgumentException(
                'Not all required fields are provided: ' . print_r($custom_field, true),
            );
        }

        // Validate type
        if (!in_array($custom_field['type'] ?? '', ['text', 'textarea', 'select'])) {
            throw new InvalidArgumentException('Invalid field type: ' . ($custom_field['type'] ?? 'null'));
        }
    }

    /**
     * Insert a new custom field into the database.
     *
     * @param array $custom_field Associative array with the custom field data.
     *
     * @return int Returns the custom field ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $custom_field): int
    {
        $custom_field['create_datetime'] = date('Y-m-d H:i:s');
        $custom_field['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('custom_fields', $custom_field)) {
            throw new RuntimeException('Could not insert custom field.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing custom field.
     *
     * @param array $custom_field Associative array with the custom field data.
     *
     * @return int Returns the custom field ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $custom_field): int
    {
        $custom_field['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('custom_fields', $custom_field, ['id' => $custom_field['id']])) {
            throw new RuntimeException('Could not update custom field.');
        }

        return $custom_field['id'];
    }

    /**
     * Remove an existing custom field from the database.
     *
     * @param int $custom_field_id Custom field ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $custom_field_id): void
    {
        $this->db->delete('custom_fields', ['id' => $custom_field_id]);
    }

    /**
     * Get a specific custom field from the database.
     *
     * @param int $custom_field_id The ID of the record to be returned.
     * @param bool $with_trashed
     *
     * @return array Returns an array with the custom field data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $custom_field_id, bool $with_trashed = false): array
    {
        if (!$with_trashed) {
            $this->db->where('active', true);
        }

        $custom_field = $this->db->get_where('custom_fields', ['id' => $custom_field_id])->row_array();

        if (!$custom_field) {
            throw new InvalidArgumentException('The provided custom field ID was not found in the database: ' . $custom_field_id);
        }

        $this->cast($custom_field);

        return $custom_field;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $custom_field_id Custom field ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected custom field value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $custom_field_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($custom_field_id)) {
            throw new InvalidArgumentException('The custom field ID argument cannot be empty.');
        }

        // Check whether the custom field exists.
        $query = $this->db->get_where('custom_fields', ['id' => $custom_field_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException('The provided custom field ID was not found in the database: ' . $custom_field_id);
        }

        // Get the selected field value.
        $row = $query->row_array();

        if (!array_key_exists($field, $row)) {
            throw new InvalidArgumentException('The requested field was not found in the database: ' . $field);
        }

        return $row[$field];
    }

    /**
     * Get all custom fields that match the provided criteria.
     *
     * @param array|string|null $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     * @param bool $with_trashed
     *
     * @return array Returns an array of custom fields.
     */
    public function get(
        array|string $where = null,
        int $limit = null,
        int $offset = null,
        string $order_by = null,
        bool $with_trashed = false,
    ): array {
        if (!$with_trashed) {
            $this->db->where('active', true);
        }

        if ($where !== null) {
            $this->db->where($where);
        }

        if ($order_by) {
            $this->db->order_by($order_by);
        } else {
            $this->db->order_by('display_column ASC, sort_order ASC');
        }

        $custom_fields = $this->db->get('custom_fields', $limit, $offset)->result_array();

        foreach ($custom_fields as &$custom_field) {
            $this->cast($custom_field);
        }

        return $custom_fields;
    }

    /**
     * Get all custom fields grouped by column.
     *
     * @return array Returns an array with two keys: 'column_1' and 'column_2', each containing the respective fields.
     */
    public function get_grouped_by_column(): array
    {
        $all_fields = $this->get(['active' => 1], null, null, 'display_column ASC, sort_order ASC');

        $grouped = [
            'column_1' => [],
            'column_2' => [],
        ];

        foreach ($all_fields as $field) {
            $column_key = 'column_' . $field['display_column'];
            $grouped[$column_key][] = $field;
        }

        return $grouped;
    }

    /**
     * Get the query builder interface, configured for use with the custom fields table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('custom_fields');
    }

    /**
     * Search custom fields by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of custom fields.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null): array
    {
        $this->db
            ->where('active', true)
            ->group_start()
            ->like('name', $keyword)
            ->or_like('label', $keyword)
            ->group_end();

        if ($order_by) {
            $this->db->order_by($order_by);
        } else {
            $this->db->order_by('display_column ASC, sort_order ASC');
        }

        $custom_fields = $this->db->get('custom_fields', $limit, $offset)->result_array();

        foreach ($custom_fields as &$custom_field) {
            $this->cast($custom_field);
        }

        return $custom_fields;
    }

    /**
     * Update the sort order of multiple custom fields.
     *
     * @param array $fields Array of fields with id and sort_order.
     *
     * @return void
     */
    public function update_sort_order(array $fields): void
    {
        foreach ($fields as $field) {
            if (isset($field['id']) && isset($field['sort_order'])) {
                $this->db->where('id', $field['id']);
                $this->db->update('custom_fields', [
                    'sort_order' => $field['sort_order'],
                    'display_column' => $field['display_column'] ?? 1,
                    'update_datetime' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
