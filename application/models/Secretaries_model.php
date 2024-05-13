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
 * Secretaries model.
 *
 * Handles all the database operations of the secretary resource.
 *
 * @package Models
 */
class Secretaries_model extends EA_Model
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
     * Save (insert or update) a secretary.
     *
     * @param array $secretary Associative array with the secretary data.
     *
     * @return int Returns the secretary ID.
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function save(array $secretary): int
    {
        $this->validate($secretary);

        if (empty($secretary['id'])) {
            return $this->insert($secretary);
        } else {
            return $this->update($secretary);
        }
    }

    /**
     * Validate the secretary data.
     *
     * @param array $secretary Associative array with the secretary data.
     *
     * @throws InvalidArgumentException
     */
    public function validate(array $secretary): void
    {
        // If a secretary ID is provided then check whether the record really exists in the database.
        if (!empty($secretary['id'])) {
            $count = $this->db->get_where('users', ['id' => $secretary['id']])->num_rows();

            if (!$count) {
                throw new InvalidArgumentException(
                    'The provided secretary ID does not exist in the database: ' . $secretary['id'],
                );
            }
        }

        // Make sure all required fields are provided.
        if (
            empty($secretary['first_name']) ||
            empty($secretary['last_name']) ||
            empty($secretary['email']) ||
            empty($secretary['phone_number'])
        ) {
            throw new InvalidArgumentException('Not all required fields are provided: ' . print_r($secretary, true));
        }

        // Validate the email address.
        if (!filter_var($secretary['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address provided: ' . $secretary['email']);
        }

        // Validate secretary providers.
        if (!empty($secretary['providers'])) {
            // Make sure the provided provider entries are numeric values.
            foreach ($secretary['providers'] as $provider_id) {
                if (!is_numeric($provider_id)) {
                    throw new InvalidArgumentException(
                        'The provided secretary providers are invalid: ' . print_r($secretary, true),
                    );
                }
            }
        }

        // Make sure the username is unique.
        if (!empty($secretary['settings']['username'])) {
            $secretary_id = $secretary['id'] ?? null;

            if (!$this->validate_username($secretary['settings']['username'], $secretary_id)) {
                throw new InvalidArgumentException(
                    'The provided username is already in use, please use a different one.',
                );
            }
        }

        // Validate the password.
        if (!empty($secretary['settings']['password'])) {
            if (strlen($secretary['settings']['password']) < MIN_PASSWORD_LENGTH) {
                throw new InvalidArgumentException(
                    'The secretary password must be at least ' . MIN_PASSWORD_LENGTH . ' characters long.',
                );
            }
        }

        // New users must always have a password value set.
        if (empty($secretary['id']) && empty($secretary['settings']['password'])) {
            throw new InvalidArgumentException('The provider password cannot be empty when inserting a new record.');
        }

        // Validate calendar view type value.
        if (
            !empty($secretary['settings']['calendar_view']) &&
            !in_array($secretary['settings']['calendar_view'], [CALENDAR_VIEW_DEFAULT, CALENDAR_VIEW_TABLE])
        ) {
            throw new InvalidArgumentException(
                'The provided calendar view is invalid: ' . $secretary['settings']['calendar_view'],
            );
        }

        // Make sure the email address is unique.
        $secretary_id = $secretary['id'] ?? null;

        $count = $this->db
            ->select()
            ->from('users')
            ->join('roles', 'roles.id = users.id_roles', 'inner')
            ->where('roles.slug', DB_SLUG_SECRETARY)
            ->where('users.email', $secretary['email'])
            ->where('users.id !=', $secretary_id)
            ->get()
            ->num_rows();

        if ($count > 0) {
            throw new InvalidArgumentException(
                'The provided email address is already in use, please use a different one.',
            );
        }
    }

    /**
     * Validate the secretary username.
     *
     * @param string $username Secretary username.
     * @param int|null $secretary_id Secretary ID.
     *
     * @return bool Returns the validation result.
     */
    public function validate_username(string $username, int $secretary_id = null): bool
    {
        if (!empty($secretary_id)) {
            $this->db->where('id_users !=', $secretary_id);
        }

        return $this->db
            ->from('users')
            ->join('user_settings', 'user_settings.id_users = users.id', 'inner')
            ->where(['username' => $username])
            ->get()
            ->num_rows() === 0;
    }

    /**
     * Get all secretaries that match the provided criteria.
     *
     * @param array|string|null $where Where conditions
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of secretaries.
     */
    public function get(
        array|string $where = null,
        int $limit = null,
        int $offset = null,
        string $order_by = null,
    ): array {
        $role_id = $this->get_secretary_role_id();

        if ($where !== null) {
            $this->db->where($where);
        }

        if ($order_by !== null) {
            $this->db->order_by($order_by);
        }

        $secretaries = $this->db->get_where('users', ['id_roles' => $role_id], $limit, $offset)->result_array();

        foreach ($secretaries as &$secretary) {
            $this->cast($secretary);
            $secretary['settings'] = $this->get_settings($secretary['id']);
            $secretary['providers'] = $this->get_provider_ids($secretary['id']);
        }

        return $secretaries;
    }

    /**
     * Get the secretary role ID.
     *
     * @return int Returns the role ID.
     */
    public function get_secretary_role_id(): int
    {
        $role = $this->db->get_where('roles', ['slug' => DB_SLUG_SECRETARY])->row_array();

        if (empty($role)) {
            throw new RuntimeException('The secretary role was not found in the database.');
        }

        return $role['id'];
    }

    /**
     * Insert a new secretary into the database.
     *
     * @param array $secretary Associative array with the secretary data.
     *
     * @return int Returns the secretary ID.
     *
     * @throws RuntimeException|Exception
     */
    protected function insert(array $secretary): int
    {
        $secretary['create_datetime'] = date('Y-m-d H:i:s');
        $secretary['update_datetime'] = date('Y-m-d H:i:s');

        $secretary['id_roles'] = $this->get_secretary_role_id();

        $provider_ids = $secretary['providers'] ?? [];

        $settings = $secretary['settings'];

        unset($secretary['providers'], $secretary['settings']);

        if (!$this->db->insert('users', $secretary)) {
            throw new RuntimeException('Could not insert secretary.');
        }

        $secretary['id'] = $this->db->insert_id();
        $settings['salt'] = generate_salt();
        $settings['password'] = hash_password($settings['salt'], $settings['password']);

        $this->set_settings($secretary['id'], $settings);
        $this->set_provider_ids($secretary['id'], $provider_ids);

        return $secretary['id'];
    }

    /**
     * Set the secretary settings.
     *
     * @param int $secretary_id Secretary ID.
     * @param array $settings Associative array with the settings data.
     *
     * @throws InvalidArgumentException
     */
    public function set_settings(int $secretary_id, array $settings): void
    {
        if (empty($settings)) {
            throw new InvalidArgumentException('The settings argument cannot be empty.');
        }

        // Make sure the settings record exists in the database.
        $count = $this->db->get_where('user_settings', ['id_users' => $secretary_id])->num_rows();

        if (!$count) {
            $this->db->insert('user_settings', ['id_users' => $secretary_id]);
        }

        foreach ($settings as $name => $value) {
            $this->set_setting($secretary_id, $name, $value);
        }
    }

    /**
     * Get the secretary settings.
     *
     * @param int $secretary_id Secretary ID.
     *
     * @throws InvalidArgumentException
     */
    public function get_settings(int $secretary_id): array
    {
        $settings = $this->db->get_where('user_settings', ['id_users' => $secretary_id])->row_array();

        unset($settings['id_users'], $settings['password'], $settings['salt']);

        return $settings;
    }

    /**
     * Set the value of a secretary setting.
     *
     * @param int $secretary_id Secretary ID.
     * @param string $name Setting name.
     * @param mixed|null $value Setting value.
     */
    public function set_setting(int $secretary_id, string $name, mixed $value = null): void
    {
        if (!$this->db->update('user_settings', [$name => $value], ['id_users' => $secretary_id])) {
            throw new RuntimeException('Could not set the new secretary setting value: ' . $name);
        }
    }

    /**
     * Update an existing secretary.
     *
     * @param array $secretary Associative array with the secretary data.
     *
     * @return int Returns the secretary ID.
     *
     * @throws RuntimeException|Exception
     */
    protected function update(array $secretary): int
    {
        $secretary['update_datetime'] = date('Y-m-d H:i:s');

        $provider_ids = $secretary['providers'] ?? [];

        $settings = $secretary['settings'];

        unset($secretary['providers'], $secretary['settings']);

        if (isset($settings['password'])) {
            $existing_settings = $this->db->get_where('user_settings', ['id_users' => $secretary['id']])->row_array();

            if (empty($existing_settings)) {
                throw new RuntimeException('No settings record found for secretary with ID: ' . $secretary['id']);
            }

            if (empty($existing_settings['salt'])) {
                $existing_settings['salt'] = $settings['salt'] = generate_salt();
            }

            $settings['password'] = hash_password($existing_settings['salt'], $settings['password']);
        }

        if (!$this->db->update('users', $secretary, ['id' => $secretary['id']])) {
            throw new RuntimeException('Could not update secretary.');
        }

        $this->set_settings($secretary['id'], $settings);
        $this->set_provider_ids($secretary['id'], $provider_ids);

        return (int) $secretary['id'];
    }

    /**
     * Set the secretary provider IDs.
     *
     * @param int $secretary_id Secretary ID.
     * @param array $provider_ids Provider IDs.
     */
    public function set_provider_ids(int $secretary_id, array $provider_ids): void
    {
        // Re-insert the secretary-provider connections.
        $this->db->delete('secretaries_providers', ['id_users_secretary' => $secretary_id]);

        foreach ($provider_ids as $provider_id) {
            $secretary_provider_connection = [
                'id_users_secretary' => $secretary_id,
                'id_users_provider' => $provider_id,
            ];

            $this->db->insert('secretaries_providers', $secretary_provider_connection);
        }
    }

    /**
     * Get the secretary provider IDs.
     *
     * @param int $secretary_id Secretary ID.
     */
    public function get_provider_ids(int $secretary_id): array
    {
        $secretary_provider_connections = $this->db
            ->get_where('secretaries_providers', ['id_users_secretary' => $secretary_id])
            ->result_array();

        $provider_ids = [];

        foreach ($secretary_provider_connections as $secretary_provider_connection) {
            $provider_ids[] = (int) $secretary_provider_connection['id_users_provider'];
        }

        return $provider_ids;
    }

    /**
     * Remove an existing secretary from the database.
     *
     * @param int $secretary_id Provider ID.
     *
     * @throws RuntimeException
     */
    public function delete(int $secretary_id): void
    {
        $this->db->delete('users', ['id' => $secretary_id]);
    }

    /**
     * Get a specific field value from the database.
     *
     * @param int $secretary_id Secretary ID.
     * @param string $field Name of the value to be returned.
     *
     * @return mixed Returns the selected secretary value from the database.
     *
     * @throws InvalidArgumentException
     */
    public function value(int $secretary_id, string $field): mixed
    {
        if (empty($field)) {
            throw new InvalidArgumentException('The field argument is cannot be empty.');
        }

        if (empty($secretary_id)) {
            throw new InvalidArgumentException('The secretary ID argument cannot be empty.');
        }

        // Check whether the secretary exists.
        $query = $this->db->get_where('users', ['id' => $secretary_id]);

        if (!$query->num_rows()) {
            throw new InvalidArgumentException(
                'The provided secretary ID was not found in the database: ' . $secretary_id,
            );
        }

        // Check if the required field is part of the secretary data.
        $secretary = $query->row_array();

        if (!array_key_exists($field, $secretary)) {
            throw new InvalidArgumentException('The requested field was not found in the secretary data: ' . $field);
        }

        return $secretary[$field];
    }

    /**
     * Get the value of a secretary setting.
     *
     * @param int $secretary_id Secretary ID.
     * @param string $name Setting name.
     *
     * @return string Returns the value of the requested user setting.
     */
    public function get_setting(int $secretary_id, string $name): string
    {
        $settings = $this->db->get_where('user_settings', ['id_users' => $secretary_id])->row_array();

        if (!array_key_exists($name, $settings)) {
            throw new RuntimeException('The requested setting value was not found: ' . $secretary_id);
        }

        return $settings[$name];
    }

    /**
     * Get the query builder interface, configured for use with the users (secretary-filtered) table.
     *
     * @return CI_DB_query_builder
     */
    public function query(): CI_DB_query_builder
    {
        $role_id = $this->get_secretary_role_id();

        return $this->db->from('users')->where('id_roles', $role_id);
    }

    /**
     * Search secretaries by the provided keyword.
     *
     * @param string $keyword Search keyword.
     * @param int|null $limit Record limit.
     * @param int|null $offset Record offset.
     * @param string|null $order_by Order by.
     *
     * @return array Returns an array of secretaries.
     */
    public function search(string $keyword, int $limit = null, int $offset = null, string $order_by = null): array
    {
        $role_id = $this->get_secretary_role_id();

        $secretaries = $this->db
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

        foreach ($secretaries as &$secretary) {
            $this->cast($secretary);
            $secretary['settings'] = $this->get_settings($secretary['id']);
            $secretary['providers'] = $this->get_provider_ids($secretary['id']);
        }

        return $secretaries;
    }

    /**
     * Load related resources to a secretary.
     *
     * @param array $secretary Associative array with the secretary data.
     * @param array $resources Resource names to be attached ("providers" supported).
     *
     * @throws InvalidArgumentException
     */
    public function load(array &$secretary, array $resources): void
    {
        if (empty($secretary) || empty($resources)) {
            return;
        }

        foreach ($resources as $resource) {
            $secretary['providers'] = match ($resource) {
                'providers' => $this->db
                    ->select('users.*')
                    ->from('users')
                    ->join('secretaries_providers', 'secretaries_providers.id_users_provider = users.id', 'inner')
                    ->where('id_users_secretary', $secretary['id'])
                    ->get()
                    ->result_array(),
                default => throw new InvalidArgumentException(
                    'The requested secretary relation is not supported: ' . $resource,
                ),
            };
        }
    }

    /**
     * Convert the database secretary record to the equivalent API resource.
     *
     * @param array $secretary Secretary data.
     */
    public function api_encode(array &$secretary): void
    {
        $encoded_resource = [
            'id' => array_key_exists('id', $secretary) ? (int) $secretary['id'] : null,
            'firstName' => $secretary['first_name'],
            'lastName' => $secretary['last_name'],
            'email' => $secretary['email'],
            'mobile' => $secretary['mobile_number'],
            'phone' => $secretary['phone_number'],
            'address' => $secretary['address'],
            'city' => $secretary['city'],
            'state' => $secretary['state'],
            'zip' => $secretary['zip_code'],
            'notes' => $secretary['notes'],
            'providers' => $secretary['providers'],
            'timezone' => $secretary['timezone'],
            'language' => $secretary['language'],
            'ldapDn' => $secretary['ldap_dn'],
            'settings' => [
                'username' => $secretary['settings']['username'],
                'notifications' => filter_var($secretary['settings']['notifications'], FILTER_VALIDATE_BOOLEAN),
                'calendarView' => $secretary['settings']['calendar_view'],
            ],
        ];

        $secretary = $encoded_resource;
    }

    /**
     * Convert the API resource to the equivalent database secretary record.
     *
     * @param array $secretary API resource.
     * @param array|null $base Base secretary data to be overwritten with the provided values (useful for updates).
     */
    public function api_decode(array &$secretary, array $base = null): void
    {
        $decoded_resource = $base ?: [];

        if (array_key_exists('id', $secretary)) {
            $decoded_resource['id'] = $secretary['id'];
        }

        if (array_key_exists('firstName', $secretary)) {
            $decoded_resource['first_name'] = $secretary['firstName'];
        }

        if (array_key_exists('lastName', $secretary)) {
            $decoded_resource['last_name'] = $secretary['lastName'];
        }

        if (array_key_exists('email', $secretary)) {
            $decoded_resource['email'] = $secretary['email'];
        }

        if (array_key_exists('mobile', $secretary)) {
            $decoded_resource['mobile_number'] = $secretary['mobile'];
        }

        if (array_key_exists('phone', $secretary)) {
            $decoded_resource['phone_number'] = $secretary['phone'];
        }

        if (array_key_exists('address', $secretary)) {
            $decoded_resource['address'] = $secretary['address'];
        }

        if (array_key_exists('city', $secretary)) {
            $decoded_resource['city'] = $secretary['city'];
        }

        if (array_key_exists('state', $secretary)) {
            $decoded_resource['state'] = $secretary['state'];
        }

        if (array_key_exists('zip', $secretary)) {
            $decoded_resource['zip_code'] = $secretary['zip'];
        }

        if (array_key_exists('notes', $secretary)) {
            $decoded_resource['notes'] = $secretary['notes'];
        }

        if (array_key_exists('timezone', $secretary)) {
            $decoded_resource['timezone'] = $secretary['timezone'];
        }

        if (array_key_exists('language', $secretary)) {
            $decoded_resource['language'] = $secretary['language'];
        }

        if (array_key_exists('ldapDn', $secretary)) {
            $decoded_resource['ldap_dn'] = $secretary['ldapDn'];
        }

        if (array_key_exists('providers', $secretary)) {
            $decoded_resource['providers'] = $secretary['providers'];
        }

        if (array_key_exists('settings', $secretary)) {
            if (empty($decoded_resource['settings'])) {
                $decoded_resource['settings'] = [];
            }

            if (array_key_exists('username', $secretary['settings'])) {
                $decoded_resource['settings']['username'] = $secretary['settings']['username'];
            }

            if (array_key_exists('password', $secretary['settings'])) {
                $decoded_resource['settings']['password'] = $secretary['settings']['password'];
            }

            if (array_key_exists('notifications', $secretary['settings'])) {
                $decoded_resource['settings']['notifications'] = filter_var(
                    $secretary['settings']['notifications'],
                    FILTER_VALIDATE_BOOLEAN,
                );
            }

            if (array_key_exists('calendarView', $secretary['settings'])) {
                $decoded_resource['settings']['calendar_view'] = $secretary['settings']['calendarView'];
            }
        }

        $secretary = $decoded_resource;
    }

    /**
     * Quickly check if a provider is assigned to a provider.
     *
     * @param int $secretary_id
     * @param int $provider_id
     *
     * @return bool
     */
    public function is_provider_supported(int $secretary_id, int $provider_id): bool
    {
        $secretary = $this->find($secretary_id);

        return in_array($provider_id, $secretary['providers']);
    }

    /**
     * Get a specific secretary from the database.
     *
     * @param int $secretary_id The ID of the record to be returned.
     *
     * @return array Returns an array with the secretary data.
     *
     * @throws InvalidArgumentException
     */
    public function find(int $secretary_id): array
    {
        $secretary = $this->db->get_where('users', ['id' => $secretary_id])->row_array();

        if (!$secretary) {
            throw new InvalidArgumentException(
                'The provided secretary ID was not found in the database: ' . $secretary_id,
            );
        }

        $this->cast($secretary);
        $secretary['settings'] = $this->get_settings($secretary['id']);
        $secretary['providers'] = $this->get_provider_ids($secretary['id']);

        return $secretary;
    }
}
