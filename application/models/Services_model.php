<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Services model
 *
 * Handles all the database operations of the service resource.
 *
 * @package Models
 */
class Services_model extends EA_Model {
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

        if (empty($service['id']))
        {
            return $this->insert($service);
        }
        else
        {
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
        if ( ! empty($service['id']))
        {
            $count = $this->db->get_where('services', ['id' => $service['id']])->num_rows();

            if ( ! $count)
            {
                throw new InvalidArgumentException('The provided service ID does not exist in the database: ' . $service['id']);
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($service['name'])
        )
        {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($service, TRUE));
        }

        // If a category was provided then make sure it really exists in the database. 
        if ( ! empty($service['id_service_categories']))
        {
            $count = $this->db->get_where('service_categories', ['id' => $service['id_service_categories']])->num_rows();

            if ( ! $count)
            {
                throw new InvalidArgumentException('The provided category ID was not found in the database: ' . $service['id_service_categories']);
            }
        }

        // Make sure the duration value is valid. 
        if ( ! empty($service['duration']))
        {
            if ((int)$service['duration'] < EVENT_MINIMUM_DURATION)
            {
                throw new InvalidArgumentException('The service duration cannot be less than ' . EVENT_MINIMUM_DURATION . ' minutes long.');
            }
        }

        // Availabilities type must have the correct value.
        if ($service['availabilities_type'] !== NULL && $service['availabilities_type'] !== AVAILABILITIES_TYPE_FLEXIBLE
            && $service['availabilities_type'] !== AVAILABILITIES_TYPE_FIXED)
        {
            throw new Exception('Service availabilities type must be either ' . AVAILABILITIES_TYPE_FLEXIBLE
                . ' or ' . AVAILABILITIES_TYPE_FIXED . ' (given ' . $service['availabilities_type'] . ')');
        }

        // Validate the availabilities type value.
        if (
            ! empty($service['availabilities_type'])
            && ! in_array($service['availabilities_type'], [AVAILABILITIES_TYPE_FLEXIBLE, AVAILABILITIES_TYPE_FIXED])
        )
        {
            throw new InvalidArgumentException('The provided availabilities type is invalid: ' . $service['availabilities_type']);
        }

        // Validate the attendants number value. 
        if (empty($service['attendants_number']) || (int)$service['attendants_number'] < 1)
        {
            throw new InvalidArgumentException('The provided attendants number is invalid: ' . $service['attendants_number']);
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
        if ( ! $this->db->insert('services', $service))
        {
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
        if ( ! $this->db->update('services', $service, ['id' => $service['id']]))
        {
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
        if ( ! $this->db->delete('services', ['id' => $service_id]))
        {
            throw new RuntimeException('Could not delete service.');
        }
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
        if ( ! $this->db->get_where('services', ['id' => $service_id])->num_rows())
        {
            throw new InvalidArgumentException('The provided service ID was not found in the database: ' . $service_id);
        }

        return $this->db->get_where('services', ['id' => $service_id])->row_array();
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $service_id Service ID.
     * @param string $field Name of the value to be returned.
     *
     * @return string Returns the selected service value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $service_id, string $field): string
    {
        if (empty($field))
        {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($service_id))
        {
            throw new InvalidArgumentException('The service ID argument cannot be empty.');
        }

        // Check whether the service exists.
        $query = $this->db->get_where('services', ['id' => $service_id]);

        if ( ! $query->num_rows())
        {
            throw new InvalidArgumentException('The provided service ID was not found in the database: ' . $service_id);
        }

        // Check if the required field is part of the service data.
        $service = $query->row_array();

        if ( ! array_key_exists($field, $service))
        {
            throw new InvalidArgumentException('The requested field was not found in the service data: ' . $field);
        }

        return $service[$field];
    }

    /**
     * Get all services that match the provided criteria.
     *
     * @param array|string $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of services.
     */
    public function get($where = NULL, int $limit = NULL, int $offset = NULL, string $order_by = NULL): array
    {
        if ($where !== NULL)
        {
            $this->db->where($where);
        }

        if ($order_by !== NULL)
        {
            $this->db->order_by($order_by);
        }

        return $this->db->get('services', $limit, $offset)->result_array();
    }

    /**
     * Get all the service records that are assigned to at least one provider.
     *
     * @return array Returns an array of services.
     */
    public function get_available_services(): array
    {
        return $this
            ->db
            ->distinct()
            ->select('services.*, service_categories.name AS category_name, service_categories.id AS category_id')
            ->from('services')
            ->join('services_providers', 'services_providers.id_services = services.id', 'inner')
            ->join('service_categories', 'service_categories.id = services.id_service_categories', 'left')
            ->order_by('name ASC')
            ->get()
            ->result_array();
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
    public function search(string $keyword, int $limit = NULL, int $offset = NULL, string $order_by = NULL): array
    {
        return $this
            ->db
            ->select()
            ->from('services')
            ->like('name', $keyword)
            ->or_like('description', $keyword)
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();
    }

    /**
     * Attach related resources to a service.
     *
     * @param array $service Associative array with the service data.
     * @param array $resources Resource names to be attached ("service_category" supported).
     *
     * @throws InvalidArgumentException
     */
    public function attach(array &$service, array $resources): void
    {
        if (empty($service) || empty($resources))
        {
            return;
        }

        foreach ($resources as $resource)
        {
            switch ($resource)
            {
                case 'service_category':
                    $service['service_category'] = $this
                        ->db
                        ->get_where('service_categories', [
                            'id' => $service['id_service_categories']
                        ])
                        ->row_array();
                    break;

                default:
                    throw new InvalidArgumentException('The requested appointment relation is not supported: ' . $resource);
            }
        }
    }
}
