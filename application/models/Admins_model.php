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
 * Admins model.
 *
 * Handles all the database operations of the admin resource.
 *
 * @package Models
 */
class Admins_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'id_roles' => 'integer',
    ];

    /**
     * @var array
     */
    protected array $api_resource = [
        'id' => 'id',
        'firstName' => 'first_name',
        'lastName' => 'last_name',
        'email' => 'email',
        'mobile' => 'mobile_number',
        'phone' => 'phone_number',
        'address' => 'address',
        'city' => 'city',
        'state' => 'state',
        'zip' => 'zip_code',
        'timezone' => 'timezone',
        'language' => 'language',
        'notes' => 'notes',
        'ldapDn' => 'ldap_dn',
        'roleId' => 'id_roles',
    ];

    /**
     * Save (insert or update) an admin.
     *
     * @param array $admin Associative array with the admin data.
     *
     * @return int Returns the admin ID.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function save(array $admin): int
    {
        $this->validate($admin);

        if (empty($admin['id'])) {
            return $this->insert($admin);
        } else {
            return $this->update($admin);
        }
    }

    /**
     * Validate the admin data.
     *
     * @param array $admin Associative array with the admin data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $admin): void
    {
        // If an admin ID is provided then check whether the record really exists in the database.
        if (!empty($admin['id'])) {
            $count = $this->db->get_where('users', ['id' => $admin['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided admin ID does not exist in the database: ' . $admin['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($admin['first_name']) ||
            empty($admin['last_name']) ||
            empty($admin['email']) ||
            empty($admin['phone_number'])
        ) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($admin, true));
        }

        // Validate the email address.
        if (!filter_var($admin['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address provided: ' . $admin['email']);
        }

        // Make sure the username is unique.
        if (!empty($admin['settings']['username'])) {
            $admin_id = $admin['id'] ?? null;

            if (!$this->validate_username($admin['settings']['username'], $admin_id)) {
                throw new InvalidArgumentException(
                    'The provided username is already in use, please use a different one.',
                );
            }
        }

        // Validate the password.
        if (!empty($admin['settings']['password'])) {
            if (strlen($admin['settings']['password']) < MIN_PASSWORD_LENGTH) {
                throw new InvalidArgumentException(
                    'The admin password must be at least ' . MIN_PASSWORD_LENGTH . ' characters long.',
                );
            }
        }

        // New users must always have a password value set.
        if (empty($admin['id']) && empty($admin['settings']['password'])) {
            throw new InvalidArgumentException('The admin password cannot be empty when inserting a new record.');
        }

        // Validate calendar view type value.
        if (
            !empty($admin['settings']['calendar_view']) &&
            !in_array($admin['settings']['calendar_view'], [CALENDAR_VIEW_DEFAULT, CALENDAR_VIEW_TABLE])
        ) {
            throw new InvalidArgumentException(
                'The provided calendar view is invalid: ' . $admin['settings']['calendar_view'],
            );
        }

        // Make sure the email address is unique.
        $admin_id = $admin['id'] ?? null;

        $count = $this->db
            ->select()
            ->from('users')
            ->join('roles', 'roles.id = users.id_roles', 'inner')
            ->where('roles.slug', DB_SLUG_ADMIN)
            ->where('users.email', $admin['email'])
            ->where('users.id !=', $admin_id)
            ->get()
            ->num_rows();

        if ($count > 0) {
            throw new InvalidArgumentException(
                'The provided email address is already in use, please use a different one.',
            );
        }
    }

    /**
     * Validate the admin username.
     *
     * @param string $username Admin username.
     * @param int|null $admin_id Admin ID.
     *
     * @return bool Returns the validation result.
     */
    public function validate_username(string $username, int $admin_id = null): bool
    {
        if (!empty($admin_id)) {
            $this->db->where('id_users !=', $admin_id);
        }

        return $this->db
            ->from('users')
            ->join('user_settings', 'user_settings.id_users = users.id', 'inner')
            ->where(['username' => $username])
            ->get()
            ->num_rows() === 0;
    }

    /**
     * Get all admins that match the provided criteria.
     *
     * @param array|string|null $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of admins.
     */
    public function get(
        array|string $where = null,
        int $limit = null,
        int $offset = null,
        string $order_by = null,
    ): array {
        $role_id = $this->get_admin_role_id();

        if ($where !== null) {
            $this->db->where($where);
        }

        if ($order_by !== null) {
            $this->db->order_by($order_by);
        }

        $admins = $this->db->get_where('users', ['id_roles' => $role_id], $limit, $offset)->result_array();

        foreach ($admins as &$admin) {
            $this->cast($admin);
            $admin['settings'] = $this->get_settings($admin['id']);
        }

        return $admins;
    }

    /**
     * Get the admin role ID.
     *
     * @return int Returns the role ID.
     */
    public function get_admin_role_id(): int
    {
        $role = $this->db->get_where('roles', ['slug' => DB_SLUG_ADMIN])->row_array();

        if (empty($role)) {
            throw new RuntimeException('The admin role was not found in the database.');
        }

        return $role['id'];
    }

    /**
     * Insert a new admin into the database.
     *
     * @param array $admin Associative array with the admin data.
     *
     * @return int Returns the admin ID.
     *
     * @throws RuntimeException|Exception
     */
    protected function insert(array $admin): int
    {
        $admin['id_roles'] = $this->get_admin_role_id();

        $settings = $admin['settings'];

        unset($admin['settings']);

        $admin['create_datetime'] = date('Y-m-d H:i:s');
        $admin['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->insert('users', $admin)) {
            throw new RuntimeException('Could not insert admin.');
        }

        $admin['id'] = $this->db->insert_id();
        $settings['salt'] = generate_salt();
        $settings['password'] = hash_password($settings['salt'], $settings['password']);

        $this->set_settings($admin['id'], $settings);

        return $admin['id'];
    }

    /**
     * Save the admin settings.
     *
     * @param int $admin_id Admin ID.
     * @param array $settings Associative array with the settings data.
     *
     * @throws InvalidArgumentException
     */
    public function set_settings(int $admin_id, array $settings): void
    {
        if (empty($settings)) {
            throw new InvalidArgumentException('The settings argument cannot be empty.');
        }

        // Make sure the settings record exists in the database.
        $count = $this->db->get_where('user_settings', ['id_users' => $admin_id])->num_rows();

        if (!$count) {
            $this->db->insert('user_settings', ['id_users' => $admin_id]);
        }

        foreach ($settings as $name => $value) {
            $this->set_setting($admin_id, $name, $value);
        }
    }

    /**
     * Get the admin settings.
     *
     * @param int $admin_id Admin ID.
     *
     * @throws InvalidArgumentException
     */
    public function get_settings(int $admin_id): array
    {
        $settings = $this->db->get_where('user_settings', ['id_users' => $admin_id])->row_array();

        unset($settings['id_users'], $settings['password'], $settings['salt']);

        return $settings;
    }

    /**
     * Set the value of an admin setting.
     *
     * @param int $admin_id Admin ID.
     * @param string $name Setting name.
     * @param mixed|null $value Setting value.
     */
    public function set_setting(int $admin_id, string $name, mixed $value = null): void
    {
        if (!$this->db->update('user_settings', [$name => $value], ['id_users' => $admin_id])) {
            throw new RuntimeException('Could not set the new admin setting value: ' . $name);
        }
    }

    /**
     * Update an existing admin.
     *
     * @param array $admin Associative array with the admin data.
     *
     * @return int Returns the admin ID.
     *
     * @throws RuntimeException
     * @throws Exception
     */
    protected function update(array $admin): int
    {
        $settings = $admin['settings'];

        $settings['id_users'] = $admin['id'];

        unset($admin['settings']);

        if (!empty($settings['password'])) {
            $existing_settings = $this->db->get_where('user_settings', ['id_users' => $admin['id']])->row_array();

            if (empty($existing_settings)) {
                throw new RuntimeException('No settings record found for admin with ID: ' . $admin['id']);
            }

            if (empty($existing_settings['salt'])) {
                $existing_settings['salt'] = $settings['salt'] = generate_salt();
            }

            $settings['password'] = hash_password($existing_settings['salt'], $settings['password']);
        }

        $admin['update_datetime'] = date('Y-m-d H:i:s');

        if (!$this->db->update('users', $admin, ['id' => $admin['id']])) {
            throw new RuntimeException('Could not update admin.');
        }

        $this->set_settings($admin['id'], $settings);

        return $admin['id'];
    }

    /**
     * Remove an existing admin from the database.
     *
     * @param int $admin_id Admin ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $admin_id): void
    {
        $role_id = $this->get_admin_role_id();

        $count = $this->db->get_where('users', ['id_roles' => $role_id])->num_rows();

        if ($count <= 1) {
            throw new RuntimeException('Record could not be deleted as the app requires at least one admin user.');
        }

        $this->db->delete('users', ['id' => $admin_id]);
    }

    /**
     * Get a specific admin from the database.
     *
     * @param int $admin_id The ID of the record to be returned.
     *
     * @return array Returns an array with the admin data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $admin_id): array
    {
        $admin = $this->db->get_where('users', ['id' => $admin_id])->row_array();

        if (!$admin) {
            throw new InvalidArgumentException('The provided admin ID was not found in the database: ' . $admin_id);
        }

        $this->cast($admin);
        $admin['settings'] = $this->get_settings($admin['id']);

        return $admin;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $admin_id Admin ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected admin value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $admin_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($admin_id)) {
            throw new InvalidArgumentException('The admin ID argument cannot be empty.');
        }

        // Check whether the admin exists.
        $query = $this->db->get_where('users', ['id' => $admin_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException('The provided admin ID was not found in the database: ' . $admin_id);
        }

        // Check if the required field is part of the admin data.
        $admin = $query->row_array();

        $this->cast($admin);

        if (!array_key_exists($field, $admin)) {
            throw new InvalidArgumentException('The requested field was not found in the admin data: ' . $field);
        }

        return $admin[$field];
    }

    /**
     * Get the value of an admin setting.
     *
     * @param int $admin_id Admin ID.
     * @param string $name Setting name.
     *
     * @return string Returns the value of the requested user setting.
     */
    public function get_setting(int $admin_id, string $name): string
    {
        $settings = $this->db->get_where('user_settings', ['id_users' => $admin_id])->row_array();

        if (!array_key_exists($name, $settings)) {
            throw new RuntimeException('The requested setting value was not found: ' . $admin_id);
        }

        return $settings[$name];
    }

    /**
     * Get the query builder interface, configured for use with the users (admin-filtered) table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        $role_id = $this->get_admin_role_id();

        return $this->db->from('users')->where('id_roles', $role_id);
    }

    /**
     * Search admins by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of admins.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null): array
    {
        $role_id = $this->get_admin_role_id();

        $admins = $this->db
            ->select()
            ->from('users')
            ->where('id_roles', $role_id)
            ->group_start()
            ->like('first_name', $keyword)
            ->or_like('last_name', $keyword)
            ->or_like('CONCAT_WS(" ", first_name, last_name)', $keyword)
            ->or_like('email', $keyword)
            ->or_like('phone_number', $keyword)
            ->or_like('mobile_number', $keyword)
            ->or_like('address', $keyword)
            ->or_like('city', $keyword)
            ->or_like('state', $keyword)
            ->or_like('zip_code', $keyword)
            ->or_like('notes', $keyword)
            ->group_end()
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();

        foreach ($admins as &$admin) {
            $this->cast($admin);
            $admin['settings'] = $this->get_settings($admin['id']);
        }

        return $admins;
    }

    /**
     * Load related resources to an admin.
     *
     * @param array $admin Associative array with the admin data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$admin, array $resources)
    {
        // Admins do not currently have any related resources (settings are already part of the admins).
    }

    /**
     * Convert the database admin record to the equivalent API resource.
     *
     * @param array $admin Admin data.
     */
    public function api_encode(array &$admin): void
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $admin) ? (int) $admin['id'] : null,
            'firstName' => $admin['first_name'],
            'lastName' => $admin['last_name'],
            'email' => $admin['email'],
            'mobile' => $admin['mobile_number'],
            'phone' => $admin['phone_number'],
            'address' => $admin['address'],
            'city' => $admin['city'],
            'state' => $admin['state'],
            'zip' => $admin['zip_code'],
            'notes' => $admin['notes'],
            'timezone' => $admin['timezone'],
            'language' => $admin['language'],
            'ldapDn' => $admin['ldap_dn'],
            'settings' => [
                'username' => $admin['settings']['username'],
                'notifications' => filter_var($admin['settings']['notifications'], FILTER_VALIDATE_BOOLEAN),
                'calendarView' => $admin['settings']['calendar_view'],
            ],
        ];

        $admin = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database admin record.
     *
     * @param array $admin API resource.
     * @param array|null $base Base admin data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$admin, array $base = null): void
    {
        $decoded_resource = $base ?? [];

        if (array_key_exists('id', $admin)) {
            $decoded_resource['id'] = $admin['id'];
        }

        if (array_key_exists('firstName', $admin)) {
            $decoded_resource['first_name'] = $admin['firstName'];
        }

        if (array_key_exists('lastName', $admin)) {
            $decoded_resource['last_name'] = $admin['lastName'];
        }

        if (array_key_exists('email', $admin)) {
            $decoded_resource['email'] = $admin['email'];
        }

        if (array_key_exists('mobile', $admin)) {
            $decoded_resource['mobile_number'] = $admin['mobile'];
        }

        if (array_key_exists('phone', $admin)) {
            $decoded_resource['phone_number'] = $admin['phone'];
        }

        if (array_key_exists('address', $admin)) {
            $decoded_resource['address'] = $admin['address'];
        }

        if (array_key_exists('city', $admin)) {
            $decoded_resource['city'] = $admin['city'];
        }

        if (array_key_exists('state', $admin)) {
            $decoded_resource['state'] = $admin['state'];
        }

        if (array_key_exists('zip', $admin)) {
            $decoded_resource['zip_code'] = $admin['zip'];
        }

        if (array_key_exists('notes', $admin)) {
            $decoded_resource['notes'] = $admin['notes'];
        }

        if (array_key_exists('timezone', $admin)) {
            $decoded_resource['timezone'] = $admin['timezone'];
        }

        if (array_key_exists('language', $admin)) {
            $decoded_resource['language'] = $admin['language'];
        }

        if (array_key_exists('ldapDn', $admin)) {
            $decoded_resource['ldap_dn'] = $admin['ldapDn'];
        }

        if (array_key_exists('settings', $admin)) {
            if (empty($decoded_resource['settings'])) {
                $decoded_resource['settings'] = [];
            }

            if (array_key_exists('username', $admin['settings'])) {
                $decoded_resource['settings']['username'] = $admin['settings']['username'];
            }

            if (array_key_exists('password', $admin['settings'])) {
                $decoded_resource['settings']['password'] = $admin['settings']['password'];
            }

            if (array_key_exists('notifications', $admin['settings'])) {
                $decoded_resource['settings']['notifications'] = filter_var(
                    $admin['settings']['notifications'],
                    FILTER_VALIDATE_BOOLEAN,
                );
            }

            if (array_key_exists('calendarView', $admin['settings'])) {
                $decoded_resource['settings']['calendar_view'] = $admin['settings']['calendarView'];
            }
        }

        $admin = $decoded_resource;
    }
}
