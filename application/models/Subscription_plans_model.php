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
 * Subscription plans model.
 *
 * Handles all the database operations of the subscription plan resource.
 *
 * @package Models
 */
class Subscription_plans_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'appointments_per_month' => 'integer',
        'price' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'name' => 'name',
        'description' => 'description',
        'appointmentsPerMonth' => 'appointments_per_month',
        'price' => 'price',
        'isActive' => 'is_active',
        'notes' => 'notes',
    ];

    /**
     * Save (insert or update) a subscription plan.
     *
     * @param array $subscription_plan Associative array with the subscription plan data.
     *
     * @return int Returns the subscription plan ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $subscription_plan): int
    {
        $this->validate($subscription_plan);

        if (empty($subscription_plan['id'])) {
            return $this->insert($subscription_plan);
        } else {
            return $this->update($subscription_plan);
        }
    }

    /**
     * Validate the subscription plan data.
     *
     * @param array $subscription_plan Associative array with the subscription plan data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $subscription_plan): void
    {
        // If a subscription plan ID is provided then check whether the record really exists in the database.
        if (!empty($subscription_plan['id'])) {
            $count = $this->db->get_where('subscription_plans', ['id' => $subscription_plan['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided subscription plan ID does not exist in the database: ' . $subscription_plan['id']
                );
            }
        }

        // Make sure all required fields are provided.
        if (empty($subscription_plan['name'])) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($subscription_plan, true));
        }

        // Validate appointments_per_month
        if (
            isset($subscription_plan['appointments_per_month']) &&
            (!is_numeric($subscription_plan['appointments_per_month']) || $subscription_plan['appointments_per_month'] < 0)
        ) {
            throw new InvalidArgumentException('The appointments_per_month field must be a non-negative number.');
        }

        // Validate price
        if (isset($subscription_plan['price']) && (!is_numeric($subscription_plan['price']) || $subscription_plan['price'] < 0)) {
            throw new InvalidArgumentException('The price field must be a non-negative number.');
        }
    }

    /**
     * Insert a new subscription plan into the database.
     *
     * @param array $subscription_plan Associative array with the subscription plan data.
     *
     * @return int Returns the subscription plan ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $subscription_plan): int
    {
        $subscription_plan['create_datetime'] = date('Y-m-d H:i:s');
        $subscription_plan['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('subscription_plans', $subscription_plan)) {
            throw new RuntimeException('Could not insert subscription plan.');
        }

        return (int) $this->db->insert_id();
    }

    /**
     * Update an existing subscription plan.
     *
     * @param array $subscription_plan Associative array with the subscription plan data.
     *
     * @return int Returns the subscription plan ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $subscription_plan): int
    {
        $subscription_plan['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('subscription_plans', $subscription_plan, ['id' => $subscription_plan['id']])) {
            throw new RuntimeException('Could not update subscription plan.');
        }

        return (int) $subscription_plan['id'];
    }

    /**
     * Remove an existing subscription plan from the database.
     *
     * @param int $subscription_plan_id Subscription plan ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $subscription_plan_id): void
    {
        $subscription_plan = $this->find($subscription_plan_id);

        if (!$this->db->delete('subscription_plans', ['id' => $subscription_plan_id])) {
            throw new RuntimeException('Could not delete subscription plan.');
        }
    }

    /**
     * Get a specific subscription plan from the database.
     *
     * @param int $subscription_plan_id The ID of the record to be returned.
     *
     * @return array Returns an array with the subscription plan data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $subscription_plan_id): array
    {
        $subscription_plan = $this->db
            ->get_where('subscription_plans', ['id' => $subscription_plan_id])
            ->row_array();

        if (!$subscription_plan) {
            throw new InvalidArgumentException('The provided subscription plan ID was not found in the database: ' . $subscription_plan_id);
        }

        $this->cast($subscription_plan);

        return $subscription_plan;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $subscription_plan_id Subscription plan ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected subscription plan value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $subscription_plan_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($subscription_plan_id)) {
            throw new InvalidArgumentException('The subscription plan ID argument cannot be empty.');
        }

        // Check whether the subscription plan exists.
        $query = $this->db->get_where('subscription_plans', ['id' => $subscription_plan_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException('The provided subscription plan ID was not found in the database: ' . $subscription_plan_id);
        }

        // Check if the required field is part of the subscription plan data.
        $subscription_plan = $query->row_array();

        $this->cast($subscription_plan);

        if (!array_key_exists($field, $subscription_plan)) {
            throw new InvalidArgumentException('The requested field was not found in the subscription plan data: ' . $field);
        }

        return $subscription_plan[$field];
    }

    /**
     * Get all subscription plans that match the provided criteria.
     *
     * @param array|string|null $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of subscription plans.
     */
    public function get(array|string|null $where = null, int $limit = null, int $offset = null, string $order_by = null): array
    {
        if ($where !== null) {
            $this->db->where($where);
        }

        if ($order_by !== null) {
            $this->db->order_by($order_by);
        }

        $subscription_plans = $this->db->get('subscription_plans', $limit, $offset)->result_array();

        foreach ($subscription_plans as &$subscription_plan) {
            $this->cast($subscription_plan);
        }

        return $subscription_plans;
    }

    /**
     * Get all active subscription plans.
     *
     * @return array Returns an array of active subscription plans.
     */
    public function get_active(): array
    {
        return $this->get(['is_active' => true, 'delete_datetime' => null], null, null, 'name ASC');
    }

    /**
     * Search subscription plans by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of subscription plans.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null): array
    {
        $subscription_plans = $this->db
            ->select()
            ->from('subscription_plans')
            ->group_start()
            ->like('name', $keyword)
            ->or_like('description', $keyword)
            ->or_like('notes', $keyword)
            ->group_end()
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by ?? 'name ASC')
            ->get()
            ->result_array();

        foreach ($subscription_plans as &$subscription_plan) {
            $this->cast($subscription_plan);
        }

        return $subscription_plans;
    }

    /**
     * Load related resources to a subscription plan.
     *
     * @param array $subscription_plan Associative array with the subscription plan data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$subscription_plan, array $resources): void
    {
        // Subscription plans don't have related resources yet, but this method
        // is kept for future extensibility (e.g., loading user subscriptions)
        if (empty($subscription_plan) || empty($resources)) {
            return;
        }
    }

    /**
     * Convert the database subscription plan record to the equivalent API resource.
     *
     * @param array $subscription_plan Subscription plan data.
     */
    public function api_encode(array &$subscription_plan): void
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $subscription_plan) ? (int) $subscription_plan['id'] : null,
            'name' => $subscription_plan['name'] ?? null,
            'description' => $subscription_plan['description'] ?? null,
            'appointmentsPerMonth' => array_key_exists('appointments_per_month', $subscription_plan)
                ? (int) $subscription_plan['appointments_per_month']
                : null,
            'price' => array_key_exists('price', $subscription_plan) ? (float) $subscription_plan['price'] : null,
            'isActive' => array_key_exists('is_active', $subscription_plan)
                ? (bool) $subscription_plan['is_active']
                : null,
            'notes' => $subscription_plan['notes'] ?? null,
        ];

        $subscription_plan = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database subscription plan record.
     *
     * @param array $subscription_plan API resource.
     * @param array|null $base Base subscription plan data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$subscription_plan, array $base = null): void
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('id', $subscription_plan)) {
            $decoded_resource['id'] = $subscription_plan['id'];
        }

        if (array_key_exists('name', $subscription_plan)) {
            $decoded_resource['name'] = $subscription_plan['name'];
        }

        if (array_key_exists('description', $subscription_plan)) {
            $decoded_resource['description'] = $subscription_plan['description'];
        }

        if (array_key_exists('appointmentsPerMonth', $subscription_plan)) {
            $decoded_resource['appointments_per_month'] = $subscription_plan['appointmentsPerMonth'];
        }

        if (array_key_exists('price', $subscription_plan)) {
            $decoded_resource['price'] = $subscription_plan['price'];
        }

        if (array_key_exists('isActive', $subscription_plan)) {
            $decoded_resource['is_active'] = $subscription_plan['isActive'];
        }

        if (array_key_exists('notes', $subscription_plan)) {
            $decoded_resource['notes'] = $subscription_plan['notes'];
        }

        $subscription_plan = $decoded_resource;
    }
}
