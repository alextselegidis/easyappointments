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
     * Custom_field_options_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Save (insert or update) an option.
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
     * Save multiple options in a single transaction (batch operation).
     *
     * @param int $custom_field_id The custom field ID.
     * @param array $options Array of options to save.
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function save_batch(int $custom_field_id, array $options): void
    {
        // Start transaction
        $this->db->trans_start();

        // Delete all existing options for this custom field
        $this->db->delete('custom_field_options', ['id_custom_fields' => $custom_field_id]);

        // Insert all new options
        foreach ($options as $option) {
            if (!empty($option['option_value']) && !empty($option['option_label'])) {
                $option['id_custom_fields'] = $custom_field_id;
                $option['create_datetime'] = date('Y-m-d H:i:s');
                $option['update_datetime'] = date('Y-m-d H:i:s');

                // Remove id if present (we're inserting new records)
                unset($option['id']);

                $this->db->insert('custom_field_options', $option);
            }
        }

        // Complete transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            throw new RuntimeException('Could not save custom field options in batch.');
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
        // If an option ID is provided then check whether the record exists in the database.
        if (!empty($option['id'])) {
            $count = $this->db->get_where('custom_field_options', ['id' => $option['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException('The provided option ID does not exist in the database: ' . $option['id']);
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($option['id_custom_fields']) ||
            empty($option['option_value']) ||
            empty($option['option_label'])
        ) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($option, true));
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
     * Get options for a specific custom field.
     *
     * @param int $custom_field_id Custom field ID.
     *
     * @return array Returns an array of options.
     */
    public function get_by_field(int $custom_field_id): array
    {
        $options = $this->db
            ->order_by('sort_order', 'ASC')
            ->get_where('custom_field_options', ['id_custom_fields' => $custom_field_id])
            ->result_array();

        foreach ($options as &$option) {
            $this->cast($option);
        }

        return $options;
    }

    /**
     * Update the sort order of options.
     *
     * @param array $options Array with option IDs and their new sort order.
     */
    public function update_sort_order(array $options): void
    {
        foreach ($options as $option) {
            $this->db->update('custom_field_options', ['sort_order' => $option['sort_order']], ['id' => $option['id']]);
        }
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
}
