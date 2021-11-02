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
 * Admins model
 *
 * Handles all the database operations of the admin resource.
 *
 * @package Models
 */
class Admins_model extends EA_Model {
    /**
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'id_roles' => 'integer',
    ];

    /**
     * Save (insert or update) an admin.
     *
     * @param array $admin Associative array with the admin data.
     *
     * @return int Returns the admin ID.
     *
     * @throws InvalidArgumentException
     */
    public function save(array $admin): int
    {
        $this->validate($admin);

        if (empty($admin['id']))
        {
            return $this->insert($admin);
        }
        else
        {
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
    public function validate(array $admin)
    {
        // If an admin ID is provided then check whether the record really exists in the database.
        if ( ! empty($admin['id']))
        {
            $count = $this->db->get_where('users', ['id' => $admin['id']])->num_rows();

            if ( ! $count)
            {
                throw new InvalidArgumentException('The provided admin ID does not exist in the database: ' . $admin['id']);
            }
        }

        // Make sure all required fields are provided. 
        if (
            empty($admin['first_name'])
            || empty($admin['last_name'])
            || empty($admin['email'])
            || empty($admin['phone_number'])
        )
        {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($admin, TRUE));
        }

        // Validate the email address.
        if ( ! filter_var($admin['email'], FILTER_VALIDATE_EMAIL))
        {
            throw new InvalidArgumentException('Invalid email address provided: ' . $admin['email']);
        }

        // Make sure the username is unique. 
        if ( ! empty($admin['settings']['username']))
        {
            $admin_id = $admin['id'] ?? NULL;

            if ( ! $this->validate_username($admin['settings']['username'], $admin_id))
            {
                throw new InvalidArgumentException('The provided username is already in use, please use a different one.');
            }
        }

        // Validate the password. 
        if ( ! empty($admin['settings']['password']))
        {
            if (strlen($admin['settings']['password']) < MIN_PASSWORD_LENGTH)
            {
                throw new InvalidArgumentException('The admin password must be at least ' . MIN_PASSWORD_LENGTH . ' characters long.');
            }
        }

        // New users must always have a password value set. 
        if (empty($admin['id']) && empty($admin['settings']['password']))
        {
            throw new InvalidArgumentException('The admin password cannot be empty when inserting a new record.');
        }

        // Validate calendar view type value.
        if (
            ! empty($admin['settings']['calendar_view'])
            && ! in_array($admin['settings']['calendar_view'], [CALENDAR_VIEW_DEFAULT, CALENDAR_VIEW_TABLE])
        )
        {
            throw new InvalidArgumentException('The provided calendar view is invalid: ' . $admin['settings']['calendar_view']);
        }

        // Make sure the email address is unique.
        $admin_id = $admin['id'] ?? NULL;

        $count = $this
            ->db
            ->select()
            ->from('users')
            ->join('roles', 'roles.id = users.id_roles', 'inner')
            ->where('roles.slug', DB_SLUG_ADMIN)
            ->where('users.email', $admin['email'])
            ->where('users.id !=', $admin_id)
            ->get()
            ->num_rows();

        if ($count > 0)
        {
            throw new InvalidArgumentException('The provided email address is already in use, please use a different one.');
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
    public function validate_username(string $username, int $admin_id = NULL): bool
    {
        if ( ! empty($admin_id))
        {
            $this->db->where('id_users !=', $admin_id);
        }

        return $this->db->get_where('user_settings', ['username' => $username])->num_rows() === 0;
    }

    /**
     * Insert a new admin into the database.
     *
     * @param array $admin Associative array with the admin data.
     *
     * @return int Returns the admin ID.
     *
     * @throws RuntimeException
     */
    protected function insert(array $admin): int
    {
        $admin['id_roles'] = $this->get_admin_role_id();

        $settings = $admin['settings'];
        unset($admin['settings']);

        if ( ! $this->db->insert('users', $admin))
        {
            throw new RuntimeException('Could not insert admin.');
        }

        $admin['id'] = $this->db->insert_id();
        $settings['salt'] = generate_salt();
        $settings['password'] = hash_password($settings['salt'], $settings['password']);

        $this->save_settings($admin['id'], $settings);

        return $admin['id'];
    }

    /**
     * Update an existing admin.
     *
     * @param array $admin Associative array with the admin data.
     *
     * @return int Returns the admin ID.
     *
     * @throws RuntimeException
     */
    protected function update(array $admin): int
    {
        $settings = $admin['settings'];
        $settings['id_users'] = $admin['id'];
        unset($admin['settings']);

        if ( ! empty($settings['password']))
        {
            $existing_settings = $this->db->get_where('user_settings', ['id_users' => $admin['id']])->row_array();

            if (empty($existing_settings))
            {
                throw new RuntimeException('No settings record found for admin with ID: ' . $admin['id']);
            }

            $settings['password'] = hash_password($existing_settings['salt'], $settings['password']);
        }

        if ( ! $this->db->update('users', $admin, ['id' => $admin['id']]))
        {
            throw new RuntimeException('Could not update admin.');
        }

        $this->save_settings($admin['id'], $settings);

        return $admin['id'];
    }

    /**
     * Remove an existing admin from the database.
     *
     * @param int $admin_id Admin ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $admin_id)
    {
        $role_id = $this->get_admin_role_id();

        $count = $this->db->get_where('users', ['id_roles' => $role_id])->num_rows();

        if ($count === 1)
        {
            throw new RuntimeException('Record could not be deleted as the app requires at least one admin user.');
        }

        if ( ! $this->db->delete('users', ['id' => $admin_id]))
        {
            throw new RuntimeException('Could not delete admin.');
        }
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
        if ( ! $this->db->get_where('users', ['id' => $admin_id])->num_rows())
        {
            throw new InvalidArgumentException('The provided admin ID was not found in the database: ' . $admin_id);
        }

        $admin = $this->db->get_where('users', ['id' => $admin_id])->row_array();

        $this->cast($admin);

        $admin['settings'] = $this->db->get_where('user_settings', ['id_users' => $admin_id])->row_array();

        unset(
            $admin['settings']['id_users'],
            $admin['settings']['password'],
            $admin['settings']['salt']
        );

        return $admin;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $admin_id Admin ID.
     * @param string $field Name of the value to be returned.
     *
     * @return string Returns the selected admin value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $admin_id, string $field): string
    {
        if (empty($field))
        {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($admin_id))
        {
            throw new InvalidArgumentException('The admin ID argument cannot be empty.');
        }

        // Check whether the admin exists.
        $query = $this->db->get_where('users', ['id' => $admin_id]);

        if ( ! $query->num_rows())
        {
            throw new InvalidArgumentException('The provided admin ID was not found in the database: ' . $admin_id);
        }

        // Check if the required field is part of the admin data.
        $admin = $query->row_array();

        $this->cast($admin);

        if ( ! array_key_exists($field, $admin))
        {
            throw new InvalidArgumentException('The requested field was not found in the admin data: ' . $field);
        }

        return $admin[$field];
    }

    /**
     * Get all admins that match the provided criteria.
     *
     * @param array|string $where Where conditions.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of admins.
     */
    public function get($where = NULL, int $limit = NULL, int $offset = NULL, string $order_by = NULL): array
    {
        $role_id = $this->get_admin_role_id();

        if ($where !== NULL)
        {
            $this->db->where($where);
        }

        if ($order_by !== NULL)
        {
            $this->db->order_by($order_by);
        }

        $admins = $this->db->get_where('users', ['id_roles' => $role_id], $limit, $offset)->result_array();

        foreach ($admins as &$admin)
        {
            $this->cast($admin);

            $admin['settings'] = $this->db->get_where('user_settings', ['id_users' => $admin['id']])->row_array();

            unset(
                $admin['settings']['id_users'],
                $admin['settings']['password'],
                $admin['settings']['salt']
            );
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

        if (empty($role))
        {
            throw new RuntimeException('The admin role was not found in the database.');
        }

        return $role['id'];
    }

    /**
     * Save the admin settings.
     *
     * @param int $admin_id Admin ID.
     * @param array $settings Associative array with the settings data.
     *
     * @throws InvalidArgumentException
     */
    protected function save_settings(int $admin_id, array $settings)
    {
        if (empty($settings))
        {
            throw new InvalidArgumentException('The settings argument cannot be empty.');
        }

        // Make sure the settings record exists in the database. 
        $count = $this->db->get_where('user_settings', ['id_users' => $admin_id])->num_rows();

        if ( ! $count)
        {
            $this->db->insert('user_settings', ['id_users' => $admin_id]);
        }

        foreach ($settings as $name => $value)
        {
            $this->set_setting($admin_id, $name, $value);
        }
    }

    /**
     * Set the value of an admin setting.
     *
     * @param int $admin_id Admin ID.
     * @param string $name Setting name.
     * @param string $value Setting value.
     */
    public function set_setting(int $admin_id, string $name, string $value)
    {
        if ( ! $this->db->update('user_settings', [$name => $value], ['id_users' => $admin_id]))
        {
            throw new RuntimeException('Could not set the new admin setting value: ' . $name);
        }
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

        if (empty($settings))
        {
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
    public function search(string $keyword, int $limit = NULL, int $offset = NULL, string $order_by = NULL): array
    {
        $role_id = $this->get_admin_role_id();

        $admins = $this
            ->db
            ->select()
            ->from('users')
            ->where('id_roles', $role_id)
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
            ->limit($limit)
            ->offset($offset)
            ->order_by($order_by)
            ->get()
            ->result_array();

        foreach ($admins as &$admin)
        {
            $this->cast($admin);

            $admin['settings'] = $this->db->get_where('user_settings', ['id_users' => $admin['id']])->row_array();

            unset(
                $admin['settings']['id_users'],
                $admin['settings']['password'],
                $admin['settings']['salt']
            );
        }

        return $admins;
    }

    /**
     * Attach related resources to an admin.
     *
     * @param array $admin Associative array with the admin data.
     * @param array $resources Resource names to be attached.
     *
     * @throws InvalidArgumentException
     */
    public function attach(array &$admin, array $resources)
    {
        // Admins do not currently have any related resources (settings are already part of the admins). 
    }

    /**
     * Convert the database admin record to the equivalent API resource.
     *
     * @param array $admin Admin data.
     */
    public function api_encode(array &$admin)
    {
        $encoded_response = [
            'id' => array_key_exists('id', $admin) ? (int)$admin['id'] : NULL,
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
            'settings' => [
                'username' => $admin['settings']['username'],
                'notifications' => filter_var($admin['settings']['notifications'], FILTER_VALIDATE_BOOLEAN),
                'calendarView' => $admin['settings']['calendar_view']
            ]
        ];

        $admin = $encoded_response;
    }

    /**
     * Convert the API resource to the equivalent database admin record.
     *
     * @param array &$admin API resource.
     * @param array|null $base Base admin data to be overwritten with the provided values (useful for updates).
     */
    public function decode(array &$admin, array $base = NULL)
    {
        $decoded_response = $base ?? [];

        if (array_key_exists('id', $admin))
        {
            $decoded_response['id'] = $admin['id'];
        }

        if (array_key_exists('firstName', $admin))
        {
            $decoded_response['first_name'] = $admin['firstName'];
        }

        if (array_key_exists('lastName', $admin))
        {
            $decoded_response['last_name'] = $admin['lastName'];
        }

        if (array_key_exists('email', $admin))
        {
            $decoded_response['email'] = $admin['email'];
        }

        if (array_key_exists('mobile', $admin))
        {
            $decoded_response['mobile_number'] = $admin['mobile'];
        }

        if (array_key_exists('phone', $admin))
        {
            $decoded_response['phone_number'] = $admin['phone'];
        }

        if (array_key_exists('address', $admin))
        {
            $decoded_response['address'] = $admin['address'];
        }

        if (array_key_exists('city', $admin))
        {
            $decoded_response['city'] = $admin['city'];
        }

        if (array_key_exists('state', $admin))
        {
            $decoded_response['state'] = $admin['state'];
        }

        if (array_key_exists('zip', $admin))
        {
            $decoded_response['zip_code'] = $admin['zip'];
        }

        if (array_key_exists('notes', $admin))
        {
            $decoded_response['notes'] = $admin['notes'];
        }

        if (array_key_exists('timezone', $admin))
        {
            $decoded_response['timezone'] = $admin['timezone'];
        }

        if (array_key_exists('settings', $admin))
        {
            if (empty($decoded_response['settings']))
            {
                $decoded_response['settings'] = [];
            }

            if (array_key_exists('username', $admin['settings']))
            {
                $decoded_response['settings']['username'] = $admin['settings']['username'];
            }

            if (array_key_exists('password', $admin['settings']))
            {
                $decoded_response['settings']['password'] = $admin['settings']['password'];
            }

            if (array_key_exists('notifications', $admin['settings']))
            {
                $decoded_response['settings']['notifications'] = filter_var($admin['settings']['notifications'],
                    FILTER_VALIDATE_BOOLEAN);
            }

            if (array_key_exists('calendarView', $admin['settings']))
            {
                $decoded_response['settings']['calendar_view'] = $admin['settings']['calendarView'];
            }
        }

        $admin = $decoded_response;
    }
}
