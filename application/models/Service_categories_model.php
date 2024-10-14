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
 * Service-Categories model.
 *
 * Handles all the database operations of the service-category resource.
 *
 * @package Models
 */
class Service_categories_model extends EA_Model
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
        'description' => 'description',
    ];

    /**
     * Save (insert or update) a service-category.
     *
     * @param array $service_category Associative array with the service-category data.
     *
     * @return int Returns the service-category ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $service_category): int
    {
        $this->validate($service_category);

        if (empty($service_category['id'])) {
            return $this->insert($service_category);
        } else {
            return $this->update($service_category);
        }
    }

    /**
     * Validate the service-category data.
     *
     * @param array $service_category Associative array with the service-category data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $service_category): void
    {
        // If a service-category ID is provided then check whether the record really exists in the database.
        if (!empty($service_category['id'])) {
            $count = $this->db->get_where('service_categories', ['id' => $service_category['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided service-category ID does not exist in the database: ' . $service_category['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (empty($service_category['name'])) {
            throw new InvalidArgumentException(
                'Not all required fields are provided: ' . print_r($service_category, true),
            );
        }
    }

    /**
     * Insert a new service-category into the database.
     *
     * @param array $service_category Associative array with the service-category data.
     *
     * @return int Returns the service-category ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $service_category): int
    {
        $service_category['create_datetime'] = date('Y-m-d H:i:s');
        $service_category['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('service_categories', $service_category)) {
            throw new RuntimeException('Could not insert service-category.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing service-category.
     *
     * @param array $service_category Associative array with the service-category data.
     *
     * @return int Returns the service-category ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $service_category): int
    {
        $service_category['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('service_categories', $service_category, ['id' => $service_category['id']])) {
            throw new RuntimeException('Could not update service categories.');
        }

        return $service_category['id'];
    }

    /**
     * Remove an existing service-category from the database.
     *
     * @param int $service_category_id Service-Category ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $service_category_id): void
    {
        $this->db->delete('service_categories', ['id' => $service_category_id]);
    }

    /**
     * Get a specific service-category from the database.
     *
     * @param int $service_category_id The ID of the record to be returned.
     *
     * @return array Returns an array with the service-category data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $service_category_id): array
    {
        $service_category = $this->db->get_where('service_categories', ['id' => $service_category_id])->row_array();

        if (!$service_category) {
            throw new InvalidArgumentException(
                'The provided service-category ID was not found in the database: ' . $service_category_id,
            );
        }

        $this->cast($service_category);

        return $service_category;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $service_category_id Service-Category ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected service-category value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $service_category_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($service_category_id)) {
            throw new InvalidArgumentException('The service-category ID argument cannot be empty.');
        }

        // Check whether the service exists.
        $query = $this->db->get_where('service_categories', ['id' => $service_category_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException(
                'The provided service-category ID was not found in the database: ' . $service_category_id,
            );
        }

        // Check if the required field is part of the service-category data.
        $service_category = $query->row_array();

        $this->cast($service_category);

        if (!array_key_exists($field, $service_category)) {
            throw new InvalidArgumentException(
                'The requested field was not found in the service-category data: ' . $field,
            );
        }

        return $service_category[$field];
    }

    /**
     * Get the query builder interface, configured for use with the service categories table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('service_categories');
    }

    /**
     * Search service categories by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of service categories.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null): array
    {
        $service_categories = $this->db
            ->select()
            ->from('service_categories')
            ->group_start()
            ->like('name', $keyword)
            ->or_like('description', $keyword)
            ->group_end()
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();

        foreach ($service_categories as &$service_category) {
            $this->cast($service_category);
        }

        return $service_categories;
    }

    /**
     * Get all services that match the provided criteria.
     *
     * @param array|string|null $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of service categories.
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

        $service_categories = $this->db->get('service_categories', $limit, $offset)->result_array();

        foreach ($service_categories as &$service_category) {
            $this->cast($service_category);
        }

        return $service_categories;
    }

    /**
     * Load related resources to a service-category.
     *
     * @param array $service_category Associative array with the service-category data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$service_category, array $resources)
    {
        // Service categories do not currently have any related resources.
    }

    /**
     * Convert the database service-category record to the equivalent API resource.
     *
     * @param array $service_category Category data.
     */
    public function api_encode(array &$service_category): void
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $service_category) ? (int) $service_category['id'] : null,
            'name' => $service_category['name'],
            'description' => array_key_exists('description', $service_category)
                ? $service_category['description']
                : null,
        ];

        $service_category = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database service-category record.
     *
     * @param array $service_category API resource.
     * @param array|null $base Base service-category data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$service_category, array $base = null): void
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('id', $service_category)) {
            $decoded_resource['id'] = $service_category['id'];
        }

        if (array_key_exists('name', $service_category)) {
            $decoded_resource['name'] = $service_category['name'];
        }

        if (array_key_exists('description', $service_category)) {
            $decoded_resource['description'] = $service_category['description'];
        }

        $service_category = $decoded_resource;
    }
}
