<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.6.0
 * ---------------------------------------------------------------------------- */
/**
 * Configs model.
 *
 * Handles all the database operations of the config resource.
 *
 * @package Models
 */
class Configs_model extends EA_Model
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
        'name' => 'name',
        'value' => 'value',
        'description' => 'description',
    ];

    /**
     * Save (insert or update) a config.
     *
     * @param array $config Associative array with the config data.
     *
     * @return int Returns the config ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $config): int
    {
        $this->validate($config);

        if (empty($config['id'])) {
            return $this->insert($config);
        } else {
            return $this->update($config);
        }
    }

    /**
     * Validate the config data.
     *
     * @param array $config Associative array with the config data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $config): void
    {
        // If a config ID is provided then check whether the record really exists in the database.
        if (!empty($config['id'])) {
            $count = $this->db->get_where('configs', ['id' => $config['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided config ID does not exist in the database: ' . $config['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (empty($config['name'])) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($config, true));
        }
    }

    /**
     * Insert a new config into the database.
     *
     * @param array $config Associative array with the config data.
     *
     * @return int Returns the config ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $config): int
    {
        $config['create_datetime'] = date('Y-m-d H:i:s');
        $config['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('configs', $config)) {
            throw new RuntimeException('Could not insert config.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing config.
     *
     * @param array $config Associative array with the config data.
     *
     * @return int Returns the config ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $config): int
    {
        $config['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('configs', $config, ['id' => $config['id']])) {
            throw new RuntimeException('Could not update config.');
        }

        return $config['id'];
    }

    /**
     * Remove an existing config from the database.
     *
     * @param int $config_id config ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $config_id): void
    {
        $this->db->delete('configs', ['id' => $config_id]);
    }

    /**
     * Get a specific config from the database.
     *
     * @param int $config_id The ID of the record to be returned.
     *
     * @return array Returns an array with the config data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $config_id): array
    {
        $config = $this->db->get_where('configs', ['id' => $config_id])->row_array();

        if (!$config) {
            throw new InvalidArgumentException('The provided config ID was not found in the database: ' . $config_id);
        }

        $this->cast($config);

        return $config;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $config_id Config ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected config value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $config_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($config_id)) {
            throw new InvalidArgumentException('The config ID argument cannot be empty.');
        }

        // Check whether the config exists.
        $query = $this->db->get_where('configs', ['id' => $config_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException('The provided config ID was not found in the database: ' . $config_id);
        }

        // Check if the required field is part of the config data.
        $config = $query->row_array();

        $this->cast($config);

        if (!array_key_exists($field, $config)) {
            throw new InvalidArgumentException('The requested field was not found in the config data: ' . $field);
        }

        return $config[$field];
    }

    /**
     * Get the query builder interface, configured for use with the configs table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('configs');
    }

    /**
     * Search configs by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of configs.
     */
    public function search(string $keyword, ?int $limit = null, ?int $offset = null, ?string $order_by = null): array
    {
        $configs = $this->db
            ->select()
            ->from('configs')
            ->group_start()
            ->like('name', $keyword)
            ->or_like('value', $keyword)
            ->group_end()
            ->limit($limit)
            ->offset($offset)
            ->order_by($this->quote_order_by($order_by))
            ->get()
            ->result_array();

        foreach ($configs as &$config) {
            $this->cast($config);
        }

        return $configs;
    }

    /**
     * Get all configs that match the provided criteria.
     *
     * @param array|string|null $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of configs.
     */
    public function get(
        array|string|null $where = null,
        ?int $limit = null,
        ?int $offset = null,
        ?string $order_by = null,
    ): array {
        if ($where !== null) {
            $this->db->where($where);
        }

        if ($order_by !== null) {
            $this->db->order_by($this->quote_order_by($order_by));
        }

        $configs = $this->db->get('configs', $limit, $offset)->result_array();

        foreach ($configs as &$config) {
            $this->cast($config);
        }

        return $configs;
    }

    /**
     * Load related resources to a config.
     *
     * @param array $config Associative array with the config data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$config, array $resources)
    {
        // Users do not currently have any related resources.
    }

    /**
     * Convert the database config record to the equivalent API resource.
     *
     * @param array $config Config data.
     */
    public function api_encode(array &$config): void
    {
        $encoded_resource = [
            'name' => $config['name'],
            'value' => $config['value'],
        ];

        $config = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database config record.
     *
     * @param array $config API resource.
     * @param array|null $base Base config data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$config, ?array $base = null): void
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('name', $config)) {
            $decoded_resource['name'] = $config['name'];
        }

        if (array_key_exists('value', $config)) {
            $decoded_resource['value'] = $config['value'];
        }

        $config = $decoded_resource;
    }
}
