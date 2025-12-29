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
 * Custom field options model.
 *
 * Handles all the database operations of the custom field options resource.
 *
 * @package Models
 */
class Custom_field_options_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'id_custom_fields' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'customFieldId' => 'id_custom_fields',
        'optionValue' => 'option_value',
        'optionLabel' => 'option_label',
        'sortOrder' => 'sort_order',
    ];

    /**
     * Save (insert or update) a custom field option.
     *
     * @param array $option Associative array with the option data.
     *
     * @return int Returns the option ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $option): int
    {
        $this->validate($option);

        if (empty($option['id'])) {
            return $this->insert($option);
        } else {
            return $this->update($option);
        }
    }

    /**
     * Validate the option data.
     *
     * @param array $option Associative array with the option data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $option): void
    {
        // If an option ID is provided then check whether the record really exists in the database.
        if (!empty($option['id'])) {
            $count = $this->db->get_where('custom_field_options', ['id' => $option['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided option ID does not exist in the database: ' . $option['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (empty($option['id_custom_fields']) || empty($option['option_value']) || empty($option['option_label'])) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($option, true));
        }

        // Verify that the custom field exists
        $custom_field_exists = $this->db
            ->get_where('custom_fields', ['id' => $option['id_custom_fields']])
            ->num_rows();

        if (!$custom_field_exists) {
            throw new InvalidArgumentException(
                'The provided custom field ID does not exist: ' . $option['id_custom_fields'],
            );
        }
    }

    /**
     * Insert a new option into the database.
     *
     * @param array $option Associative array with the option data.
     *
     * @return int Returns the option ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $option): int
    {
        $option['create_datetime'] = date('Y-m-d H:i:s');
        $option['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('custom_field_options', $option)) {
            throw new RuntimeException('Could not insert custom field option.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing option.
     *
     * @param array $option Associative array with the option data.
     *
     * @return int Returns the option ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $option): int
    {
        $option['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('custom_field_options', $option, ['id' => $option['id']])) {
            throw new RuntimeException('Could not update custom field option.');
        }

        return $option['id'];
    }

    /**
     * Remove an existing option from the database.
     *
     * @param int $option_id Option ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $option_id): void
    {
        $this->db->delete('custom_field_options', ['id' => $option_id]);
    }

    /**
     * Get a specific option from the database.
     *
     * @param int $option_id The ID of the record to be returned.
     *
     * @return array Returns an array with the option data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $option_id): array
    {
        $option = $this->db->get_where('custom_field_options', ['id' => $option_id])->row_array();

        if (!$option) {
            throw new InvalidArgumentException('The provided option ID was not found in the database: ' . $option_id);
        }

        $this->cast($option);

        return $option;
    }

    /**
     * Get all options that match the provided criteria.
     *
     * @param array|string|null $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of options.
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
        } else {
            $this->db->order_by('sort_order ASC');
        }

        $options = $this->db->get('custom_field_options', $limit, $offset)->result_array();

        foreach ($options as &$option) {
            $this->cast($option);
        }

        return $options;
    }

    /**
     * Get all options for a specific custom field.
     *
     * @param int $custom_field_id Custom field ID.
     *
     * @return array Returns an array of options.
     */
    public function get_by_field(int $custom_field_id): array
    {
        return $this->get(['id_custom_fields' => $custom_field_id], null, null, 'sort_order ASC');
    }

    /**
     * Get the query builder interface, configured for use with the custom field options table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('custom_field_options');
    }

    /**
     * Update the sort order of multiple options.
     *
     * @param array $options Array of options with id and sort_order.
     *
     * @return void
     */
    public function update_sort_order(array $options): void
    {
        foreach ($options as $option) {
            if (isset($option['id']) && isset($option['sort_order'])) {
                $this->db->where('id', $option['id']);
                $this->db->update('custom_field_options', [
                    'sort_order' => $option['sort_order'],
                    'update_datetime' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    /**
     * Delete all options for a specific custom field.
     *
     * @param int $custom_field_id Custom field ID.
     *
     * @return void
     */
    public function delete_by_field(int $custom_field_id): void
    {
        $this->db->delete('custom_field_options', ['id_custom_fields' => $custom_field_id]);
    }
}
