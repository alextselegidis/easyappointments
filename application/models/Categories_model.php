<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Categories model.
 *
 * Handles all the database operations of the category resource.
 *
 * @package Models
 */
class Categories_model extends EA_Model {
    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * @var array
     */
    protected $api_resource = [
        'id' => 'id',
        'name' => 'name',
        'description' => 'description',
    ];

    /**
     * Save (insert or update) a category.
     *
     * @param array $category Associative array with the category data.
     *
     * @return int Returns the category ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $category): int
    {
        $this->validate($category);

        if (empty($category['id']))
        {
            return $this->insert($category);
        }
        else
        {
            return $this->update($category);
        }
    }

    /**
     * Validate the category data.
     *
     * @param array $category Associative array with the category data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $category)
    {
        // If a category ID is provided then check whether the record really exists in the database.
        if ( ! empty($category['id']))
        {
            $count = $this->db->get_where('categories', ['id' => $category['id']])->num_rows();

            if ( ! $count)
            {
                throw new InvalidArgumentException('The provided category ID does not exist in the database: ' . $category['id']);
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($category['name'])
        )
        {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($category, TRUE));
        }
    }

    /**
     * Insert a new category into the database.
     *
     * @param array $category Associative array with the category data.
     *
     * @return int Returns the category ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $category): int
    {
        if ( ! $this->db->insert('categories', $category))
        {
            throw new RuntimeException('Could not insert category.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing category.
     *
     * @param array $category Associative array with the category data.
     *
     * @return int Returns the category ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $category): int
    {
        if ( ! $this->db->update('categories', $category, ['id' => $category['id']]))
        {
            throw new RuntimeException('Could not update service categories.');
        }

        return $category['id'];
    }

    /**
     * Remove an existing category from the database.
     *
     * @param int $category_id Category ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $category_id)
    {
        if ( ! $this->db->delete('categories', ['id' => $category_id]))
        {
            throw new RuntimeException('Could not delete service categories.');
        }
    }

    /**
     * Get a specific category from the database.
     *
     * @param int $category_id The ID of the record to be returned.
     *
     * @return array Returns an array with the category data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $category_id): array
    {
        if ( ! $this->db->get_where('categories', ['id' => $category_id])->num_rows())
        {
            throw new InvalidArgumentException('The provided category ID was not found in the database: ' . $category_id);
        }

        $category = $this->db->get_where('categories', ['id' => $category_id])->row_array();

        $this->cast($category);

        return $category;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $category_id Category ID.
     * @param string $field Name of the value to be returned.
     *
     * @return string Returns the selected category value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $category_id, string $field): string
    {
        if (empty($field))
        {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($category_id))
        {
            throw new InvalidArgumentException('The category ID argument cannot be empty.');
        }

        // Check whether the service exists.
        $query = $this->db->get_where('categories', ['id' => $category_id]);

        if ( ! $query->num_rows())
        {
            throw new InvalidArgumentException('The provided category ID was not found in the database: ' . $category_id);
        }

        // Check if the required field is part of the category data.
        $category = $query->row_array();

        $this->cast($category);

        if ( ! array_key_exists($field, $category))
        {
            throw new InvalidArgumentException('The requested field was not found in the category data: ' . $field);
        }

        return $category[$field];
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

        $categories = $this->db->get('categories', $limit, $offset)->result_array();

        foreach ($categories as $category)
        {
            $this->cast($category);
        }

        return $categories;
    }

    /**
     * Get the query builder interface, configured for use with the service categories table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('categories');
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
        $categories = $this
            ->db
            ->select()
            ->from('categories')
            ->like('name', $keyword)
            ->or_like('description', $keyword)
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();

        foreach ($categories as &$category)
        {
            $this->cast($category);
        }

        return $categories;
    }

    /**
     * Load related resources to a category.
     *
     * @param array $category Associative array with the category data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$category, array $resources)
    {
        // Service categories do not currently have any related resources. 
    }

    /**
     * Convert the database category record to the equivalent API resource.
     *
     * @param array $category Category data.
     */
    public function api_encode(array &$category)
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $category) ? (int)$category['id'] : NULL,
            'name' => $category['name'],
            'description' => array_key_exists('description', $category) ? $category['description'] : NULL
        ];

        $category = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database category record.
     *
     * @param array $category API resource.
     * @param array|null $base Base category data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$category, array $base = NULL)
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('id', $category))
        {
            $decoded_resource['id'] = $category['id'];
        }

        if (array_key_exists('name', $category))
        {
            $decoded_resource['name'] = $category['name'];
        }

        if (array_key_exists('description', $category))
        {
            $decoded_resource['description'] = $category['description'];
        }

        $category = $decoded_resource;
    }
}
