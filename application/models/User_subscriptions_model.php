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
 * User subscriptions model.
 *
 * Handles all the database operations of the user subscription resource.
 *
 * @package Models
 */
class User_subscriptions_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'id_users_customer' => 'integer',
        'id_subscription_plans' => 'integer',
        'appointments_quota' => 'integer',
        'appointments_used' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'customerId' => 'id_users_customer',
        'subscriptionPlanId' => 'id_subscription_plans',
        'startDate' => 'start_date',
        'endDate' => 'end_date',
        'appointmentsQuota' => 'appointments_quota',
        'appointmentsUsed' => 'appointments_used',
        'lastQuotaResetDate' => 'last_quota_reset_date',
        'isActive' => 'is_active',
        'notes' => 'notes',
    ];

    /**
     * Save (insert or update) a user subscription.
     *
     * @param array $user_subscription Associative array with the user subscription data.
     *
     * @return int Returns the user subscription ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $user_subscription): int
    {
        $this->validate($user_subscription);

        if (empty($user_subscription['id'])) {
            return $this->insert($user_subscription);
        } else {
            return $this->update($user_subscription);
        }
    }

    /**
     * Validate the user subscription data.
     *
     * @param array $user_subscription Associative array with the user subscription data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $user_subscription): void
    {
        // If a user subscription ID is provided then check whether the record really exists in the database.
        if (!empty($user_subscription['id'])) {
            $count = $this->db->get_where('user_subscriptions', ['id' => $user_subscription['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided user subscription ID does not exist in the database: ' . $user_subscription['id']
                );
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($user_subscription['id_users_customer']) ||
            empty($user_subscription['id_subscription_plans']) ||
            empty($user_subscription['start_date'])
        ) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($user_subscription, true));
        }

        // Validate that the customer exists and has the customer role.
        $customer = $this->db->get_where('users', ['id' => $user_subscription['id_users_customer']])->row_array();

        if (!$customer) {
            throw new InvalidArgumentException(
                'The provided customer ID does not exist in the database: ' . $user_subscription['id_users_customer']
            );
        }

        $role = $this->db->get_where('roles', ['id' => $customer['id_roles']])->row_array();

        if ($role['slug'] !== DB_SLUG_CUSTOMER) {
            throw new InvalidArgumentException(
                'The provided user is not a customer: ' . $user_subscription['id_users_customer']
            );
        }

        // Validate that the subscription plan exists.
        $subscription_plan = $this->db
            ->get_where('subscription_plans', ['id' => $user_subscription['id_subscription_plans']])
            ->row_array();

        if (!$subscription_plan) {
            throw new InvalidArgumentException(
                'The provided subscription plan ID does not exist in the database: ' .
                    $user_subscription['id_subscription_plans']
            );
        }

        // Validate date format
        if (isset($user_subscription['start_date']) && !validate_date($user_subscription['start_date'], 'Y-m-d')) {
            throw new InvalidArgumentException('The start_date field must be in Y-m-d format.');
        }

        if (
            isset($user_subscription['end_date']) &&
            $user_subscription['end_date'] &&
            !validate_date($user_subscription['end_date'], 'Y-m-d')
        ) {
            throw new InvalidArgumentException('The end_date field must be in Y-m-d format.');
        }

        // Validate quota values
        if (
            isset($user_subscription['appointments_quota']) &&
            (!is_numeric($user_subscription['appointments_quota']) || $user_subscription['appointments_quota'] < 0)
        ) {
            throw new InvalidArgumentException('The appointments_quota field must be a non-negative number.');
        }

        if (
            isset($user_subscription['appointments_used']) &&
            (!is_numeric($user_subscription['appointments_used']) || $user_subscription['appointments_used'] < 0)
        ) {
            throw new InvalidArgumentException('The appointments_used field must be a non-negative number.');
        }
    }

    /**
     * Insert a new user subscription into the database.
     *
     * @param array $user_subscription Associative array with the user subscription data.
     *
     * @return int Returns the user subscription ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $user_subscription): int
    {
        // Get the subscription plan to set initial quota
        if (empty($user_subscription['appointments_quota'])) {
            $subscription_plan = $this->db
                ->get_where('subscription_plans', ['id' => $user_subscription['id_subscription_plans']])
                ->row_array();

            $user_subscription['appointments_quota'] = $subscription_plan['appointments_per_month'];
        }

        // Set default values
        if (!isset($user_subscription['appointments_used'])) {
            $user_subscription['appointments_used'] = 0;
        }

        if (!isset($user_subscription['is_active'])) {
            $user_subscription['is_active'] = 1;
        }

        $user_subscription['create_datetime'] = date('Y-m-d H:i:s');
        $user_subscription['update_datetime'] = date('Y-m-d H:i:s');
        $user_subscription['last_quota_reset_date'] = date('Y-m-d');

        if (!$this->db->insert('user_subscriptions', $user_subscription)) {
            throw new RuntimeException('Could not insert user subscription.');
        }

        return (int) $this->db->insert_id();
    }

    /**
     * Update an existing user subscription.
     *
     * @param array $user_subscription Associative array with the user subscription data.
     *
     * @return int Returns the user subscription ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $user_subscription): int
    {
        $user_subscription['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('user_subscriptions', $user_subscription, ['id' => $user_subscription['id']])) {
            throw new RuntimeException('Could not update user subscription.');
        }

        return (int) $user_subscription['id'];
    }

    /**
     * Remove an existing user subscription from the database.
     *
     * @param int $user_subscription_id User subscription ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $user_subscription_id): void
    {
        $user_subscription = $this->find($user_subscription_id);

        if (!$this->db->delete('user_subscriptions', ['id' => $user_subscription_id])) {
            throw new RuntimeException('Could not delete user subscription.');
        }
    }

    /**
     * Get a specific user subscription from the database.
     *
     * @param int $user_subscription_id The ID of the record to be returned.
     *
     * @return array Returns an array with the user subscription data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $user_subscription_id): array
    {
        $user_subscription = $this->db
            ->get_where('user_subscriptions', ['id' => $user_subscription_id])
            ->row_array();

        if (!$user_subscription) {
            throw new InvalidArgumentException(
                'The provided user subscription ID was not found in the database: ' . $user_subscription_id
            );
        }

        $this->cast($user_subscription);

        return $user_subscription;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $user_subscription_id User subscription ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected user subscription value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $user_subscription_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($user_subscription_id)) {
            throw new InvalidArgumentException('The user subscription ID argument cannot be empty.');
        }

        // Check whether the user subscription exists.
        $query = $this->db->get_where('user_subscriptions', ['id' => $user_subscription_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException(
                'The provided user subscription ID was not found in the database: ' . $user_subscription_id
            );
        }

        // Check if the required field is part of the user subscription data.
        $user_subscription = $query->row_array();

        $this->cast($user_subscription);

        if (!array_key_exists($field, $user_subscription)) {
            throw new InvalidArgumentException('The requested field was not found in the user subscription data: ' . $field);
        }

        return $user_subscription[$field];
    }

    /**
     * Get all user subscriptions that match the provided criteria.
     *
     * @param array|string|null $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of user subscriptions.
     */
    public function get(array|string|null $where = null, int $limit = null, int $offset = null, string $order_by = null): array
    {
        if ($where !== null) {
            $this->db->where($where);
        }

        if ($order_by !== null) {
            $this->db->order_by($order_by);
        }

        $user_subscriptions = $this->db->get('user_subscriptions', $limit, $offset)->result_array();

        foreach ($user_subscriptions as &$user_subscription) {
            $this->cast($user_subscription);
        }

        return $user_subscriptions;
    }

    /**
     * Get active subscription for a customer.
     *
     * @param int $customer_id Customer ID.
     *
     * @return array|null Returns the active subscription or null if not found.
     */
    public function get_active_subscription(int $customer_id): ?array
    {
        $user_subscription = $this->db
            ->where('id_users_customer', $customer_id)
            ->where('is_active', 1)
            ->where('delete_datetime IS NULL')
            ->where('(end_date IS NULL OR end_date >= CURDATE())')
            ->get('user_subscriptions')
            ->row_array();

        if (!$user_subscription) {
            return null;
        }

        $this->cast($user_subscription);

        return $user_subscription;
    }

    /**
     * Check if customer has quota available.
     *
     * @param int $customer_id Customer ID.
     *
     * @return array Returns array with 'has_quota' boolean and 'subscription' data.
     */
    public function check_quota(int $customer_id): array
    {
        $subscription = $this->get_active_subscription($customer_id);

        if (!$subscription) {
            return [
                'has_quota' => false,
                'subscription' => null,
                'remaining' => 0,
                'message' => 'No active subscription found.',
            ];
        }

        $remaining = $subscription['appointments_quota'] - $subscription['appointments_used'];

        return [
            'has_quota' => $remaining > 0,
            'subscription' => $subscription,
            'remaining' => $remaining,
            'quota' => $subscription['appointments_quota'],
            'used' => $subscription['appointments_used'],
            'message' =>
                $remaining > 0 ? "You have {$remaining} appointments remaining." : 'No appointments remaining in your quota.',
        ];
    }

    /**
     * Increment appointments_used for a subscription.
     *
     * @param int $user_subscription_id User subscription ID.
     *
     * @throws RuntimeException
     */
    public function increment_usage(int $user_subscription_id): void
    {
        $this->db
            ->set('appointments_used', 'appointments_used + 1', false)
            ->set('update_datetime', date('Y-m-d H:i:s'))
            ->where('id', $user_subscription_id)
            ->update('user_subscriptions');

        if ($this->db->affected_rows() === 0) {
            throw new RuntimeException('Could not increment appointments_used.');
        }
    }

    /**
     * Decrement appointments_used for a subscription.
     *
     * @param int $user_subscription_id User subscription ID.
     *
     * @throws RuntimeException
     */
    public function decrement_usage(int $user_subscription_id): void
    {
        $this->db
            ->set('appointments_used', 'GREATEST(appointments_used - 1, 0)', false)
            ->set('update_datetime', date('Y-m-d H:i:s'))
            ->where('id', $user_subscription_id)
            ->update('user_subscriptions');

        if ($this->db->affected_rows() === 0) {
            throw new RuntimeException('Could not decrement appointments_used.');
        }
    }

    /**
     * Reset monthly quota for all active subscriptions.
     *
     * @return int Number of subscriptions reset.
     */
    public function reset_monthly_quotas(): int
    {
        // Reset quotas for all active subscriptions that haven't been reset this month
        $this->db
            ->set('appointments_used', 0)
            ->set('last_quota_reset_date', date('Y-m-d'))
            ->set('update_datetime', date('Y-m-d H:i:s'))
            ->where('is_active', 1)
            ->where('delete_datetime IS NULL')
            ->where('(end_date IS NULL OR end_date >= CURDATE())')
            ->where('(last_quota_reset_date IS NULL OR last_quota_reset_date < DATE_FORMAT(NOW(), "%Y-%m-01"))')
            ->update('user_subscriptions');

        return $this->db->affected_rows();
    }

    /**
     * Search user subscriptions by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of user subscriptions.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null): array
    {
        $user_subscriptions = $this->db
            ->select('user_subscriptions.*')
            ->from('user_subscriptions')
            ->join('users', 'users.id = user_subscriptions.id_users_customer')
            ->join('subscription_plans', 'subscription_plans.id = user_subscriptions.id_subscription_plans')
            ->group_start()
            ->like('users.first_name', $keyword)
            ->or_like('users.last_name', $keyword)
            ->or_like('users.email', $keyword)
            ->or_like('subscription_plans.name', $keyword)
            ->or_like('user_subscriptions.notes', $keyword)
            ->group_end()
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by ?? 'user_subscriptions.id DESC')
            ->get()
            ->result_array();

        foreach ($user_subscriptions as &$user_subscription) {
            $this->cast($user_subscription);
        }

        return $user_subscriptions;
    }

    /**
     * Load related resources to a user subscription.
     *
     * @param array $user_subscription Associative array with the user subscription data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$user_subscription, array $resources): void
    {
        if (empty($user_subscription) || empty($resources)) {
            return;
        }

        foreach ($resources as $resource) {
            $user_subscription[$resource] = match ($resource) {
                'customer' => $this->db
                    ->get_where('users', ['id' => $user_subscription['id_users_customer']])
                    ->row_array(),
                'subscription_plan' => $this->db
                    ->get_where('subscription_plans', ['id' => $user_subscription['id_subscription_plans']])
                    ->row_array(),
                default => throw new InvalidArgumentException('The requested resource is not supported: ' . $resource),
            };
        }
    }

    /**
     * Convert the database user subscription record to the equivalent API resource.
     *
     * @param array $user_subscription User subscription data.
     */
    public function api_encode(array &$user_subscription): void
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $user_subscription) ? (int) $user_subscription['id'] : null,
            'customerId' => array_key_exists('id_users_customer', $user_subscription)
                ? (int) $user_subscription['id_users_customer']
                : null,
            'subscriptionPlanId' => array_key_exists('id_subscription_plans', $user_subscription)
                ? (int) $user_subscription['id_subscription_plans']
                : null,
            'startDate' => $user_subscription['start_date'] ?? null,
            'endDate' => $user_subscription['end_date'] ?? null,
            'appointmentsQuota' => array_key_exists('appointments_quota', $user_subscription)
                ? (int) $user_subscription['appointments_quota']
                : null,
            'appointmentsUsed' => array_key_exists('appointments_used', $user_subscription)
                ? (int) $user_subscription['appointments_used']
                : null,
            'lastQuotaResetDate' => $user_subscription['last_quota_reset_date'] ?? null,
            'isActive' => array_key_exists('is_active', $user_subscription)
                ? (bool) $user_subscription['is_active']
                : null,
            'notes' => $user_subscription['notes'] ?? null,
        ];

        // Include related resources if loaded
        if (array_key_exists('customer', $user_subscription)) {
            $encoded_resource['customer'] = $user_subscription['customer'];
        }

        if (array_key_exists('subscription_plan', $user_subscription)) {
            $encoded_resource['subscriptionPlan'] = $user_subscription['subscription_plan'];
        }

        $user_subscription = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database user subscription record.
     *
     * @param array $user_subscription API resource.
     * @param array|null $base Base user subscription data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$user_subscription, array $base = null): void
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('id', $user_subscription)) {
            $decoded_resource['id'] = $user_subscription['id'];
        }

        if (array_key_exists('customerId', $user_subscription)) {
            $decoded_resource['id_users_customer'] = $user_subscription['customerId'];
        }

        if (array_key_exists('subscriptionPlanId', $user_subscription)) {
            $decoded_resource['id_subscription_plans'] = $user_subscription['subscriptionPlanId'];
        }

        if (array_key_exists('startDate', $user_subscription)) {
            $decoded_resource['start_date'] = $user_subscription['startDate'];
        }

        if (array_key_exists('endDate', $user_subscription)) {
            $decoded_resource['end_date'] = $user_subscription['endDate'];
        }

        if (array_key_exists('appointmentsQuota', $user_subscription)) {
            $decoded_resource['appointments_quota'] = $user_subscription['appointmentsQuota'];
        }

        if (array_key_exists('appointmentsUsed', $user_subscription)) {
            $decoded_resource['appointments_used'] = $user_subscription['appointmentsUsed'];
        }

        if (array_key_exists('lastQuotaResetDate', $user_subscription)) {
            $decoded_resource['last_quota_reset_date'] = $user_subscription['lastQuotaResetDate'];
        }

        if (array_key_exists('isActive', $user_subscription)) {
            $decoded_resource['is_active'] = $user_subscription['isActive'];
        }

        if (array_key_exists('notes', $user_subscription)) {
            $decoded_resource['notes'] = $user_subscription['notes'];
        }

        $user_subscription = $decoded_resource;
    }
}
