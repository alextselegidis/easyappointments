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
 * Services categories model
 *
 * Handles all the database operations of the service category resource.
 *
 * @package Models
 */
class Service_categories_model extends EA_Model {
    /**
     * Save (insert or update) a service category.
     *
     * @param array $service_category Associative array with the service category data.
     *
     * @return int Returns the service category ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $service_category): int
    {
        $this->validate($service_category);

        if (empty($service_category['id']))
        {
            return $this->insert($service_category);
        }
        else
        {
            return $this->update($service_category);
        }
    }

    /**
     * Validate the service category data.
     *
     * @param array $service_category Associative array with the service category data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $service_category)
    {
        // If a service category ID is provided then check whether the record really exists in the database.
        if ( ! empty($service_category['id']))
        {
            $count = $this->db->get_where('service_categories', ['id' => $service_category['id']])->num_rows();

            if ( ! $count)
            {
                throw new InvalidArgumentException('The provided service category ID does not exist in the database: ' . $service_category['id']);
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($service_category['name'])
        )
        {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($service_category, TRUE));
        }
    }

    /**
     * Insert a new service category into the database.
     *
     * @param array $service_category Associative array with the service category data.
     *
     * @return int Returns the service category ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $service_category): int
    {
        if ( ! $this->db->insert('service_categories', $service_category))
        {
            throw new RuntimeException('Could not insert service category.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing service category.
     *
     * @param array $service_category Associative array with the service category data.
     *
     * @return int Returns the service category ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $service_category): int
    {
        if ( ! $this->db->update('service_categories', $service_category, ['id' => $service_category['id']]))
        {
            throw new RuntimeException('Could not update service categories.');
        }

        return $service_category['id'];
    }

    /**
     * Remove an existing service category from the database.
     *
     * @param int $service_category_id Service category ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $service_category_id)
    {
        if ( ! $this->db->delete('service_categories', ['id' => $service_category_id]))
        {
            throw new RuntimeException('Could not delete service categories.');
        }
    }

    /**
     * Get a specific service category from the database.
     *
     * @param int $service_category_id The ID of the record to be returned.
     *
     * @return array Returns an array with the service category data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $service_category_id): array
    {
        if ( ! $this->db->get_where('service_categories', ['id' => $service_category_id])->num_rows())
        {
            throw new InvalidArgumentException('The provided service category ID was not found in the database: ' . $service_category_id);
        }

        return $this->db->get_where('service_categories', ['id' => $service_category_id])->row_array();
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $service_category_id Service category ID.
     * @param string $field Name of the value to be returned.
     *
     * @return string Returns the selected service category value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $service_category_id, string $field): string
    {
        if (empty($field))
        {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($service_category_id))
        {
            throw new InvalidArgumentException('The service category ID argument cannot be empty.');
        }

        // Check whether the service exists.
        $query = $this->db->get_where('service_categories', ['id' => $service_category_id]);

        if ( ! $query->num_rows())
        {
            throw new InvalidArgumentException('The provided service category ID was not found in the database: ' . $service_category_id);
        }

        // Check if the required field is part of the service category data.
        $service_category = $query->row_array();

        if ( ! array_key_exists($field, $service_category))
        {
            throw new InvalidArgumentException('The requested field was not found in the service category data: ' . $field);
        }

        return $service_category[$field];
    }

    /**
     * Get all services that match the provided criteria.
     *
     * @param array|string $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of service categories.
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

        return $this->db->get('service_categories', $limit, $offset)->result_array();
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
    public function search(string $keyword, int $limit = NULL, int $offset = NULL, string $order_by = NULL): array
    {
        return $this
            ->db
            ->select()
            ->from('service_categories')
            ->like('name', $keyword)
            ->or_like('description', $keyword)
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();
    }

    /**
     * Attach related resources to a service category.
     *
     * @param array $service_category Associative array with the service category data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function attach(array &$service_category, array $resources)
    {
        // Service categories do not currently have any related resources. 
    }
}
