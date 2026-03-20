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
        'display_column' => 'boolean',
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
        'columnPosition' => 'column_position',
    ];

    /**
     * Custom_fields_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

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
                throw new InvalidArgumentException('The provided custom field ID does not exist in the database: ' . $custom_field['id']);
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($custom_field['name']) ||
            empty($custom_field['label']) ||
            empty($custom_field['type'])
        ) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($custom_field, true));
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
        // Use transaction to ensure all deletes succeed or fail together
        $this->db->trans_start();

        $this->db->delete('custom_fields', ['id' => $custom_field_id]);
        $this->db->delete('custom_field_options', ['id_custom_fields' => $custom_field_id]);
        $this->db->delete('custom_field_values', ['id_custom_fields' => $custom_field_id]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            throw new RuntimeException('Could not delete custom field.');
        }
    }

    /**
     * Get a specific custom field from the database.
     *
     * @param int $custom_field_id The ID of the record to be returned.
     * @param bool $with_options Include options.
     *
     * @return array Returns an array with the custom field data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $custom_field_id, bool $with_options = false): array
    {
        $custom_field = $this->db->get_where('custom_fields', ['id' => $custom_field_id])->row_array();

        if (!$custom_field) {
            throw new InvalidArgumentException('The provided custom field ID was not found in the database: ' . $custom_field_id);
        }

        $this->cast($custom_field);

        if ($with_options) {
            $custom_field['options'] = $this->db
                ->order_by('sort_order', 'ASC')
                ->get_where('custom_field_options', ['id_custom_fields' => $custom_field_id])
                ->result_array();
        }

        return $custom_field;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param string|null $field Name of the value to be returned.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array with custom fields.
     */
    public function get(?string $field = null, ?int $limit = null, ?int $offset = null, ?string $order_by = null): array
    {
        if ($field) {
            $this->db->where($field);
        }

        if ($order_by) {
            $this->db->order_by($order_by);
        } else {
            $this->db->order_by('sort_order', 'ASC');
        }

        if ($limit) {
            $this->db->limit($limit);
        }

        if ($offset) {
            $this->db->offset($offset);
        }

        $custom_fields = $this->db->get('custom_fields')->result_array();

        foreach ($custom_fields as &$custom_field) {
            $this->cast($custom_field);
        }

        return $custom_fields;
    }

    /**
     * Search custom fields.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of custom fields.
     */
    public function search(string $keyword, ?int $limit = null, ?int $offset = null, ?string $order_by = null): array
    {
        $this->db->group_start()
            ->like('name', $keyword)
            ->or_like('label', $keyword)
            ->group_end();

        if ($order_by) {
            $this->db->order_by($order_by);
        } else {
            $this->db->order_by('sort_order', 'ASC');
        }

        if ($limit) {
            $this->db->limit($limit);
        }

        if ($offset) {
            $this->db->offset($offset);
        }

        $custom_fields = $this->db->get('custom_fields')->result_array();

        foreach ($custom_fields as &$custom_field) {
            $this->cast($custom_field);
        }

        return $custom_fields;
    }

    /**
     * Update the sort order of custom fields.
     *
     * @param array $fields Array with field IDs and their new sort order.
     */
    public function update_sort_order(array $fields): void
    {
        foreach ($fields as $field) {
            $this->db->update('custom_fields', ['sort_order' => $field['sort_order']], ['id' => $field['id']]);
        }
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
}
