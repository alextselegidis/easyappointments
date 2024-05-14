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
 * Services model.
 *
 * Handles all the database operations of the service resource.
 *
 * @package Models
 */
class Services_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'price' => 'float',
        'attendants_number' => 'integer',
        'is_private' => 'boolean',
        'id_service_categories' => 'integer',
    ];

    /**
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'name' => 'name',
        'duration' => 'duration',
        'price' => 'price',
        'currency' => 'currency',
        'description' => 'description',
        'location' => 'location',
        'color' => 'color',
        'availabilitiesType' => 'availabilities_type',
        'attendantsNumber' => 'attendants_number',
        'isPrivate' => 'is_private',
        'serviceCategoryId' => 'id_service_categories',
    ];

    /**
     * Save (insert or update) a service.
     *
     * @param array $service Associative array with the service data.
     *
     * @return int Returns the service ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $service): int
    {
        $this->validate($service);

        if (empty($service['id'])) {
            return $this->insert($service);
        } else {
            return $this->update($service);
        }
    }

    /**
     * Validate the service data.
     *
     * @param array $service Associative array with the service data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $service): void
    {
        // If a service ID is provided then check whether the record really exists in the database.
        if (!empty($service['id'])) {
            $count = $this->db->get_where('services', ['id' => $service['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided service ID does not exist in the database: ' . $service['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (empty($service['name'])) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($service, true));
        }

        // If a category was provided then make sure it really exists in the database.
        if (!empty($service['id_service_categories'])) {
            $count = $this->db
                ->get_where('service_categories', ['id' => $service['id_service_categories']])
                ->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided category ID was not found in the database: ' . $service['id_service_categories'],
                );
            }
        }

        // Make sure the duration value is valid.
        if (!empty($service['duration'])) {
            if ((int) $service['duration'] < EVENT_MINIMUM_DURATION) {
                throw new InvalidArgumentException(
                    'The service duration cannot be less than ' . EVENT_MINIMUM_DURATION . ' minutes long.',
                );
            }
        }

        // Availabilities type must have the correct value.
        if (
            $service['availabilities_type'] !== null &&
            $service['availabilities_type'] !== AVAILABILITIES_TYPE_FLEXIBLE &&
            $service['availabilities_type'] !== AVAILABILITIES_TYPE_FIXED
        ) {
            throw new InvalidArgumentException(
                'Service availabilities type must be either ' .
                    AVAILABILITIES_TYPE_FLEXIBLE .
                    ' or ' .
                    AVAILABILITIES_TYPE_FIXED .
                    ' (given ' .
                    $service['availabilities_type'] .
                    ')',
            );
        }

        // Validate the availabilities type value.
        if (
            !empty($service['availabilities_type']) &&
            !in_array($service['availabilities_type'], [AVAILABILITIES_TYPE_FLEXIBLE, AVAILABILITIES_TYPE_FIXED])
        ) {
            throw new InvalidArgumentException(
                'The provided availabilities type is invalid: ' . $service['availabilities_type'],
            );
        }

        // Validate the attendants number value.
        if (empty($service['attendants_number']) || (int) $service['attendants_number'] < 1) {
            throw new InvalidArgumentException(
                'The provided attendants number is invalid: ' . $service['attendants_number'],
            );
        }
    }

    /**
     * Insert a new service into the database.
     *
     * @param array $service Associative array with the service data.
     *
     * @return int Returns the service ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $service): int
    {
        $service['create_datetime'] = date('Y-m-d H:i:s');
        $service['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('services', $service)) {
            throw new RuntimeException('Could not insert service.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing service.
     *
     * @param array $service Associative array with the service data.
     *
     * @return int Returns the service ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $service): int
    {
        $service['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('services', $service, ['id' => $service['id']])) {
            throw new RuntimeException('Could not update service.');
        }

        return $service['id'];
    }

    /**
     * Remove an existing service from the database.
     *
     * @param int $service_id Service ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $service_id): void
    {
        $this->db->delete('services', ['id' => $service_id]);
    }

    /**
     * Get a specific service from the database.
     *
     * @param int $service_id The ID of the record to be returned.
     *
     * @return array Returns an array with the service data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $service_id): array
    {
        $service = $this->db->get_where('services', ['id' => $service_id])->row_array();

        if (!$service) {
            throw new InvalidArgumentException('The provided service ID was not found in the database: ' . $service_id);
        }

        $this->cast($service);

        return $service;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $service_id Service ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected service value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $service_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($service_id)) {
            throw new InvalidArgumentException('The service ID argument cannot be empty.');
        }

        // Check whether the service exists.
        $query = $this->db->get_where('services', ['id' => $service_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException('The provided service ID was not found in the database: ' . $service_id);
        }

        // Check if the required field is part of the service data.
        $service = $query->row_array();

        $this->cast($service);

        if (!array_key_exists($field, $service)) {
            throw new InvalidArgumentException('The requested field was not found in the service data: ' . $field);
        }

        return $service[$field];
    }

    /**
     * Get all the service records that are assigned to at least one provider.
     *
     * @param bool $without_private Only include the public services.
     *
     * @return array Returns an array of services.
     */
    public function get_available_services(bool $without_private = false): array
    {
        if ($without_private) {
            $this->db->where('services.is_private', false);
        }

        $services = $this->db
            ->distinct()
            ->select(
                'services.*, service_categories.name AS service_category_name, service_categories.id AS service_category_id',
            )
            ->from('services')
            ->join('services_providers', 'services_providers.id_services = services.id', 'inner')
            ->join('service_categories', 'service_categories.id = services.id_service_categories', 'left')
            ->order_by('name ASC')
            ->get()
            ->result_array();

        foreach ($services as &$service) {
            $this->cast($service);
        }

        return $services;
    }

    /**
     * Get all services that match the provided criteria.
     *
     * @param array|string|null $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of services.
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

        $services = $this->db->get('services', $limit, $offset)->result_array();

        foreach ($services as &$service) {
            $this->cast($service);
        }

        return $services;
    }

    /**
     * Get the query builder interface, configured for use with the services table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('services');
    }

    /**
     * Search services by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of services.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null): array
    {
        $services = $this->db
            ->select()
            ->from('services')
            ->group_start()
            ->like('name', $keyword)
            ->or_like('description', $keyword)
            ->group_end()
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();

        foreach ($services as &$service) {
            $this->cast($service);
        }

        return $services;
    }

    /**
     * Load related resources to a service.
     *
     * @param array $service Associative array with the service data.
     * @param array $resources Resource names to be attached ("category" supported).
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$service, array $resources): void
    {
        if (empty($service) || empty($resources)) {
            return;
        }

        foreach ($resources as $resource) {
            $service['category'] = match ($resource) {
                'category' => $this->db
                    ->get_where('service_categories', [
                        'id' => $service['id_service_categories'] ?? ($service['serviceCategoryId'] ?? null),
                    ])
                    ->row_array(),
                default => throw new InvalidArgumentException(
                    'The requested appointment relation is not supported: ' . $resource,
                ),
            };
        }
    }

    /**
     * Convert the database service record to the equivalent API resource.
     *
     * @param array $service Service data.
     */
    public function api_encode(array &$service): void
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $service) ? (int) $service['id'] : null,
            'name' => $service['name'],
            'duration' => (int) $service['duration'],
            'price' => (float) $service['price'],
            'currency' => $service['currency'],
            'description' => $service['description'],
            'location' => $service['location'],
            'availabilitiesType' => $service['availabilities_type'],
            'attendantsNumber' => (int) $service['attendants_number'],
            'isPrivate' => (bool) $service['is_private'],
            'serviceCategoryId' =>
                $service['id_service_categories'] !== null ? (int) $service['id_service_categories'] : null,
        ];

        $service = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database service record.
     *
     * @param array $service API resource.
     * @param array|null $base Base service data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$service, array $base = null): void
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('id', $service)) {
            $decoded_resource['id'] = $service['id'];
        }

        if (array_key_exists('name', $service)) {
            $decoded_resource['name'] = $service['name'];
        }

        if (array_key_exists('duration', $service)) {
            $decoded_resource['duration'] = $service['duration'];
        }

        if (array_key_exists('price', $service)) {
            $decoded_resource['price'] = $service['price'];
        }

        if (array_key_exists('currency', $service)) {
            $decoded_resource['currency'] = $service['currency'];
        }

        if (array_key_exists('description', $service)) {
            $decoded_resource['description'] = $service['description'];
        }

        if (array_key_exists('location', $service)) {
            $decoded_resource['location'] = $service['location'];
        }

        if (array_key_exists('availabilitiesType', $service)) {
            $decoded_resource['availabilities_type'] = $service['availabilitiesType'];
        }

        if (array_key_exists('attendantsNumber', $service)) {
            $decoded_resource['attendants_number'] = $service['attendantsNumber'];
        }

        if (array_key_exists('serviceCategoryId', $service)) {
            $decoded_resource['id_service_categories'] = $service['serviceCategoryId'];
        }

        if (array_key_exists('isPrivate', $service)) {
            $decoded_resource['is_private'] = (bool) $service['isPrivate'];
        }

        $service = $decoded_resource;
    }
}
