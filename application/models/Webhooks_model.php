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
 * Webhooks model.
 *
 * Handles all the database operations of the webhook resource.
 *
 * @package Models
 */
class Webhooks_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'is_ssl_verified' => 'boolean',
    ];

    /**
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'name' => 'name',
        'url' => 'url',
        'action' => 'action',
        'secretToken' => 'secret_token',
        'isActive' => 'is_active',
        'isSslVerified' => 'is_ssl_verified',
        'notes' => 'notes',
    ];

    /**
     * Save (insert or update) a webhook.
     *
     * @param array $webhook Associative array with the webhook data.
     *
     * @return int Returns the webhook ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $webhook): int
    {
        $this->validate($webhook);

        if (empty($webhook['id'])) {
            return $this->insert($webhook);
        } else {
            return $this->update($webhook);
        }
    }

    /**
     * Validate the webhook data.
     *
     * @param array $webhook Associative array with the webhook data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $webhook): void
    {
        if (empty($webhook['name']) || empty($webhook['url'])) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($webhook, true));
        }
    }

    /**
     * Insert a new webhook into the database.
     *
     * @param array $webhook Associative array with the webhook data.
     *
     * @return int Returns the webhook ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $webhook): int
    {
        $webhook['create_datetime'] = date('Y-m-d H:i:s');
        $webhook['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('webhooks', $webhook)) {
            throw new RuntimeException('Could not insert webhook.');
        }

        return $this->db->insert_id();
    }

    /**
     * Update an existing webhook.
     *
     * @param array $webhook Associative array with the webhook data.
     *
     * @return int Returns the webhook ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $webhook): int
    {
        $webhook['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('webhooks', $webhook, ['id' => $webhook['id']])) {
            throw new RuntimeException('Could not update webhook.');
        }

        return $webhook['id'];
    }

    /**
     * Remove an existing webhook from the database.
     *
     * @param int $webhook_id Webhook ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $webhook_id): void
    {
        $this->db->delete('webhooks', ['id' => $webhook_id]);
    }

    /**
     * Get a specific webhook from the database.
     *
     * @param int $webhook_id The ID of the record to be returned.
     *
     * @return array Returns an array with the webhook data.
     */
    public function find(int $webhook_id): array
    {
        $webhook = $this->db->get_where('webhooks', ['id' => $webhook_id])->row_array();

        if (!$webhook) {
            throw new InvalidArgumentException('The provided webhook ID was not found in the database: ' . $webhook_id);
        }

        $this->cast($webhook);

        return $webhook;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $webhook_id Webhook ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected webhook value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $webhook_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($webhook_id)) {
            throw new InvalidArgumentException('The webhook ID argument cannot be empty.');
        }

        // Check whether the webhook exists.
        $query = $this->db->get_where('webhooks', ['id' => $webhook_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException('The provided webhook ID was not found in the database: ' . $webhook_id);
        }

        // Check if the required field is part of the webhook data.
        $webhook = $query->row_array();

        $this->cast($webhook);

        if (!array_key_exists($field, $webhook)) {
            throw new InvalidArgumentException('The requested field was not found in the webhook data: ' . $field);
        }

        return $webhook[$field];
    }

    /**
     * Get the query builder interface, configured for use with the webhooks table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('webhooks');
    }

    /**
     * Search webhooks by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of webhooks.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null): array
    {
        $webhooks = $this->db
            ->select()
            ->from('webhooks')
            ->group_start()
            ->like('name', $keyword)
            ->or_like('url', $keyword)
            ->or_like('actions', $keyword)
            ->group_end()
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();

        foreach ($webhooks as &$webhook) {
            $this->cast($webhook);
        }

        return $webhooks;
    }

    /**
     * Get all webhooks that match the provided criteria.
     *
     * @param array|string|null $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of webhooks.
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

        $webhooks = $this->db->get('webhooks', $limit, $offset)->result_array();

        foreach ($webhooks as &$webhook) {
            $this->cast($webhook);
        }

        return $webhooks;
    }

    /**
     * Load related resources to a webhook.
     *
     * @param array $webhook Associative array with the webhook data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$webhook, array $resources)
    {
        // Webhooks do not currently have any related resources.
    }

    /**
     * Convert the database webhook record to the equivalent API resource.
     *
     * @param array $webhook Webhook data.
     */
    public function api_encode(array &$webhook): void
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $webhook) ? (int) $webhook['id'] : null,
            'name' => $webhook['name'],
            'url' => $webhook['url'],
            'actions' => $webhook['actions'],
            'secret_token' => $webhook['secret_token'],
            'is_ssl_verified' => $webhook['is_ssl_verified'],
            'notes' => $webhook['notes'],
        ];

        $webhook = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database webhook record.
     *
     * @param array $webhook API resource.
     * @param array|null $base Base webhook data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$webhook, array $base = null): void
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('id', $webhook)) {
            $decoded_resource['id'] = $webhook['id'];
        }

        if (array_key_exists('name', $webhook)) {
            $decoded_resource['name'] = $webhook['name'];
        }

        if (array_key_exists('url', $webhook)) {
            $decoded_resource['url'] = $webhook['url'];
        }

        if (array_key_exists('actions', $webhook)) {
            $decoded_resource['actions'] = $webhook['actions'];
        }

        if (array_key_exists('secretToken', $webhook)) {
            $decoded_resource['secret_token'] = $webhook['secretToken'];
        }

        if (array_key_exists('isSslVerified', $webhook)) {
            $decoded_resource['is_ssl_verified'] = $webhook['isSslVerified'];
        }

        if (array_key_exists('notes', $webhook)) {
            $decoded_resource['notes'] = $webhook['notes'];
        }

        $webhook = $decoded_resource;
    }
}
