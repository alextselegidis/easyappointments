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
 * Users model.
 *
 * Handles all the database operations of the user resource.
 *
 * @package Models
 */
class Users_model extends EA_Model
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
        'ldapDn' => 'ldap_dn',
        'notes' => 'notes',
        'roleId' => 'id_roles',
    ];

    /**
     * Save (insert or update) a user.
     *
     * @param array $user Associative array with the user data.
     *
     * @return int Returns the user ID.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function save(array $user): int
    {
        $this->validate($user);

        if (empty($user['id'])) {
            return $this->insert($user);
        } else {
            return $this->update($user);
        }
    }

    /**
     * Validate the user data.
     *
     * @param array $user Associative array with the user data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $user): void
    {
        // If a user ID is provided then check whether the record really exists in the database.
        if (!empty($user['id'])) {
            $count = $this->db->get_where('users', ['id' => $user['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided user ID does not exist in the database: ' . $user['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($user['first_name']) ||
            empty($user['last_name']) ||
            empty($user['email']) ||
            empty($user['phone_number'])
        ) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($user, true));
        }
    }

    /**
     * Insert a new user into the database.
     *
     * @param array $user Associative array with the user data.
     *
     * @return int Returns the user ID.
     *
     * @throws RuntimeException|Exception
     */
    protected function insert(array $user): int
    {
        $user['create_datetime'] = date('Y-m-d H:i:s');
        $user['update_datetime'] = date('Y-m-d H:i:s');

        $settings = $user['settings'];
        unset($user['settings']);

        if (!$this->db->insert('users', $user)) {
            throw new RuntimeException('Could not insert user.');
        }

        $user['id'] = $this->db->insert_id();
        $settings['salt'] = generate_salt();
        $settings['password'] = hash_password($settings['salt'], $settings['password']);

        $this->set_settings($user['id'], $settings);

        return $user['id'];
    }

    /**
     * Save the user settings.
     *
     * @param int $user_id User ID.
     * @param array $settings Associative array with the settings data.
     *
     * @throws InvalidArgumentException
     */
    protected function set_settings(int $user_id, array $settings): void
    {
        if (empty($settings)) {
            throw new InvalidArgumentException('The settings argument cannot be empty.');
        }

        // Make sure the settings record exists in the database.
        $count = $this->db->get_where('user_settings', ['id_users' => $user_id])->num_rows();

        if (!$count) {
            $this->db->insert('user_settings', ['id_users' => $user_id]);
        }

        foreach ($settings as $name => $value) {
            $this->set_setting($user_id, $name, $value);
        }
    }

    /**
     * Get the user settings.
     *
     * @param int $user_id User ID.
     *
     * @throws InvalidArgumentException
     */
    public function get_settings(int $user_id): array
    {
        $settings = $this->db->get_where('user_settings', ['id_users' => $user_id])->row_array();

        unset($settings['id_users'], $settings['password'], $settings['salt']);

        return $settings;
    }

    /**
     * Set the value of a user setting.
     *
     * @param int $user_id User ID.
     * @param string $name Setting name.
     * @param string $value Setting value.
     */
    public function set_setting(int $user_id, string $name, string $value): void
    {
        if (!$this->db->update('user_settings', [$name => $value], ['id_users' => $user_id])) {
            throw new RuntimeException('Could not set the new user setting value: ' . $name);
        }
    }

    /**
     * Update an existing user.
     *
     * @param array $user Associative array with the user data.
     *
     * @return int Returns the user ID.
     *
     * @throws RuntimeException|Exception
     */
    protected function update(array $user): int
    {
        $user['update_datetime'] = date('Y-m-d H:i:s');

        $settings = $user['settings'];
        unset($user['settings']);

        if (isset($settings['password'])) {
            $existing_settings = $this->db->get_where('user_settings', ['id_users' => $user['id']])->row_array();

            if (empty($existing_settings)) {
                throw new RuntimeException('No settings record found for user with ID: ' . $user['id']);
            }

            $settings['password'] = hash_password($existing_settings['salt'], $settings['password']);
        }

        if (!$this->db->update('users', $user, ['id' => $user['id']])) {
            throw new RuntimeException('Could not update user.');
        }

        $this->set_settings($user['id'], $settings);

        return $user['id'];
    }

    /**
     * Remove an existing user from the database.
     *
     * @param int $user_id User ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $user_id): void
    {
        $this->db->delete('users', ['id' => $user_id]);
    }

    /**
     * Get a specific user from the database.
     *
     * @param int $user_id The ID of the record to be returned.
     *
     * @return array Returns an array with the user data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $user_id): array
    {
        $user = $this->db->get_where('users', ['id' => $user_id])->row_array();

        if (!$user) {
            throw new InvalidArgumentException('The provided user ID was not found in the database: ' . $user_id);
        }

        $this->cast($user);

        $user['settings'] = $this->get_settings($user['id']);

        return $user;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $user_id User ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected user value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $user_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($user_id)) {
            throw new InvalidArgumentException('The user ID argument cannot be empty.');
        }

        // Check whether the user exists.
        $query = $this->db->get_where('users', ['id' => $user_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException('The provided user ID was not found in the database: ' . $user_id);
        }

        // Check if the required field is part of the user data.
        $user = $query->row_array();

        $this->cast($user);

        if (!array_key_exists($field, $user)) {
            throw new InvalidArgumentException('The requested field was not found in the user data: ' . $field);
        }

        return $user[$field];
    }

    /**
     * Get the value of a user setting.
     *
     * @param int $user_id User ID.
     * @param string $name Setting name.
     *
     * @return string Returns the value of the requested user setting.
     */
    public function get_setting(int $user_id, string $name): string
    {
        $settings = $this->db->get_where('user_settings', ['id_users' => $user_id])->row_array();

        if (empty($settings[$name])) {
            throw new RuntimeException('The requested setting value was not found: ' . $user_id);
        }

        return $settings[$name];
    }

    /**
     * Get the query builder interface, configured for use with the users table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        return $this->db->from('users');
    }

    /**
     * Search users by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of settings.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null): array
    {
        $users = $this->db
            ->select()
            ->from('users')
            ->group_start()
            ->like('first_name', $keyword)
            ->or_like('last_name', $keyword)
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

        foreach ($users as &$user) {
            $this->cast($user);
            $user['settings'] = $this->get_settings($user['id']);
        }

        return $users;
    }

    /**
     * Get all users that match the provided criteria.
     *
     * @param array|string|null $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of users.
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

        $users = $this->db->get('users', $limit, $offset)->result_array();

        foreach ($users as &$user) {
            $this->cast($user);
            $user['settings'] = $this->get_settings($user['id']);
        }

        return $users;
    }

    /**
     * Load related resources to a user.
     *
     * @param array $user Associative array with the user data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$user, array $resources)
    {
        // Users do not currently have any related resources.
    }

    /**
     * Validate the username.
     *
     * @param string $username Username.
     * @param int|null $user_id Exclude user ID.
     *
     * @return bool Returns the validation result.
     */
    public function validate_username(string $username, int $user_id = null): bool
    {
        if (!empty($user_id)) {
            $this->db->where('id_users !=', $user_id);
        }

        return $this->db->get_where('user_settings', ['username' => $username])->num_rows() === 0;
    }
}
