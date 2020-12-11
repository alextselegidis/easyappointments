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
 * Providers_Model Class
 *
 * Contains the database operations for the service provider users of Easy!Appointments.
 *
 * @package Models
 */
class Providers_model extends EA_Model {
    /**
     * Providers_Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('data_validation');
        $this->load->helper('general');
    }

    /**
     * Add (insert - update) a service provider record.
     *
     * If the record already exists (id value provided) then it is going to be updated, otherwise inserted into the
     * database.
     *
     * @param array $provider Contains the service provider data.
     *
     * @return int Returns the record id.
     *
     * @throws Exception When the record data validation fails.
     */
    public function add($provider)
    {
        $this->validate($provider);

        if ($this->exists($provider) && ! isset($provider['id']))
        {
            $provider['id'] = $this->find_record_id($provider);
        }

        if ( ! isset($provider['id']))
        {
            $provider['id'] = $this->insert($provider);
        }
        else
        {
            $provider['id'] = $this->update($provider);
        }

        return (int)$provider['id'];
    }

    /**
     * Validate provider data before the insert or  update operation is executed.
     *
     * @param array $provider Contains the provider data.
     *
     * @return bool Returns the validation result.
     *
     * @throws Exception If provider validation fails.
     */
    public function validate($provider)
    {
        // If a provider id is present, check whether the record exist in the database.
        if (isset($provider['id']))
        {
            $num_rows = $this->db->get_where('users',
                ['id' => $provider['id']])->num_rows();
            if ($num_rows == 0)
            {
                throw new Exception('Provided record id does not exist in the database.');
            }
        }

        // Validate required fields.
        if ( ! isset(
            $provider['last_name'],
            $provider['email'],
            $provider['phone_number']
        ))
        {
            throw new Exception('Not all required fields are provided: ' . print_r($provider, TRUE));
        }

        // Validate provider email address.
        if ( ! filter_var($provider['email'], FILTER_VALIDATE_EMAIL))
        {
            throw new Exception('Invalid email address provided: ' . $provider['email']);
        }

        // Validate provider services.
        if ( ! isset($provider['services']) || ! is_array($provider['services']))
        {
            throw new Exception('Invalid provider services given: ' . print_r($provider, TRUE));
        }
        else
        {
            // Check if services are valid int values.
            foreach ($provider['services'] as $service_id)
            {
                if ( ! is_numeric($service_id))
                {
                    throw new Exception('A provider service with invalid id was found: '
                        . print_r($provider, TRUE));
                }
            }
        }

        // Validate provider settings.
        if ( ! isset($provider['settings']) || count($provider['settings']) === 0
            || ! is_array($provider['settings']))
        {
            throw new Exception('Invalid provider settings given: ' . print_r($provider, TRUE));
        }

        // Check if username exists.
        if (isset($provider['settings']['username']))
        {
            $user_id = (isset($provider['id'])) ? $provider['id'] : '';
            if ( ! $this->validate_username($provider['settings']['username'], $user_id))
            {
                throw new Exception ('Username already exists. Please select a different '
                    . 'username for this record.');
            }
        }

        // Validate provider password
        if (isset($provider['settings']['password']))
        {
            if (strlen($provider['settings']['password']) < MIN_PASSWORD_LENGTH)
            {
                throw new Exception('The user password must be at least '
                    . MIN_PASSWORD_LENGTH . ' characters long.');
            }
        }

        if ( ! isset($provider['id']) && ! isset($provider['settings']['password']))
        {
            throw new Exception('The user password cannot be empty for new users.');
        }

        // Validate calendar view mode.
        if (isset($provider['settings']['calendar_view']) && ($provider['settings']['calendar_view'] !== CALENDAR_VIEW_DEFAULT
                && $provider['settings']['calendar_view'] !== CALENDAR_VIEW_TABLE))
        {
            throw new Exception('The calendar view setting must be either "' . CALENDAR_VIEW_DEFAULT
                . '" or "' . CALENDAR_VIEW_TABLE . '", given: ' . $provider['settings']['calendar_view']);
        }

        // When inserting a record the email address must be unique.
        $provider_id = (isset($provider['id'])) ? $provider['id'] : '';

        $num_rows = $this->db
            ->select('*')
            ->from('users')
            ->join('roles', 'roles.id = users.id_roles', 'inner')
            ->where('roles.slug', DB_SLUG_PROVIDER)
            ->where('users.email', $provider['email'])
            ->where('users.id !=', $provider_id)
            ->get()
            ->num_rows();

        if ($num_rows > 0)
        {
            throw new Exception('Given email address belongs to another provider record. '
                . 'Please use a different email.');
        }

        return TRUE;
    }

    /**
     * Validate Records Username
     *
     * @param string $username The provider records username.
     * @param int $user_id The user record id.
     *
     * @return bool Returns the validation result.
     */
    public function validate_username($username, $user_id)
    {
        if ( ! empty($user_id))
        {
            $this->db->where('id_users !=', $user_id);
        }

        $this->db->where('username', $username);

        return $this->db->get('user_settings')->num_rows() === 0;
    }

    /**
     * Check whether a particular provider record already exists in the database.
     *
     * @param array $provider Contains the provider data. The 'email' value is required in order to check for a provider.
     *
     * @return bool Returns whether the provider record exists or not.
     *
     * @throws Exception When the 'email' value is not provided.
     */
    public function exists($provider)
    {
        if ( ! isset($provider['email']))
        {
            throw new Exception('Provider email is not provided:' . print_r($provider, TRUE));
        }

        // This method shouldn't depend on another method of this class.
        $num_rows = $this->db
            ->select('*')
            ->from('users')
            ->join('roles', 'roles.id = users.id_roles', 'inner')
            ->where('users.email', $provider['email'])
            ->where('roles.slug', DB_SLUG_PROVIDER)
            ->get()->num_rows();

        return $num_rows > 0;
    }

    /**
     * Find the database record id of a provider.
     *
     * @param array $provider Contains the provider data. The 'email' value is required in order to find the record id.
     *
     * @return int Returns the record id.
     *
     * @throws Exception When the provider's email value is not provided.
     */
    public function find_record_id($provider)
    {
        if ( ! isset($provider['email']))
        {
            throw new Exception('Provider email was not provided:' . print_r($provider, TRUE));
        }

        $result = $this->db
            ->select('users.id')
            ->from('users')
            ->join('roles', 'roles.id = users.id_roles', 'inner')
            ->where('users.email', $provider['email'])
            ->where('roles.slug', DB_SLUG_PROVIDER)
            ->get();

        if ($result->num_rows() == 0)
        {
            throw new Exception('Could not find provider record id.');
        }

        return (int)$result->row()->id;
    }

    /**
     * Insert a new provider record into the database.
     *
     * @param array $provider Contains the provider data (must be already validated).
     *
     * @return int Returns the new record id.
     *
     * @throws Exception When the insert operation fails.
     */
    protected function insert($provider)
    {

        // Get provider role id.
        $provider['id_roles'] = $this->get_providers_role_id();

        // Store provider settings and services (must not be present on the $provider array).
        $services = $provider['services'];
        unset($provider['services']);
        $settings = $provider['settings'];
        unset($provider['settings']);

        // Insert provider record and save settings.
        if ( ! $this->db->insert('users', $provider))
        {
            throw new Exception('Could not insert provider into the database');
        }

        $settings['salt'] = generate_salt();
        $settings['password'] = hash_password($settings['salt'], $settings['password']);

        $provider['id'] = $this->db->insert_id();
        $this->save_settings($settings, $provider['id']);
        $this->save_services($services, $provider['id']);

        // Return the new record id.
        return (int)$provider['id'];
    }

    /**
     * Get the providers role id from the database.
     *
     * @return int Returns the role id for the provider records.
     */
    public function get_providers_role_id()
    {
        return $this->db->get_where('roles', ['slug' => DB_SLUG_PROVIDER])->row()->id;
    }

    /**
     * Save the provider settings (used from insert or update operation).
     *
     * @param array $settings Contains the setting values.
     * @param int $provider_id Record id of the provider.
     *
     * @throws Exception If $provider_id argument is invalid.
     * @throws Exception If $settings argument is invalid.
     */
    protected function save_settings($settings, $provider_id)
    {
        if ( ! is_numeric($provider_id))
        {
            throw new Exception('Invalid $provider_id argument given:' . $provider_id);
        }

        if (count($settings) == 0 || ! is_array($settings))
        {
            throw new Exception('Invalid $settings argument given:' . print_r($settings, TRUE));
        }

        // Check if the setting record exists in db.
        if ($this->db->get_where('user_settings', ['id_users' => $provider_id])->num_rows() === 0)
        {
            $this->db->insert('user_settings', ['id_users' => $provider_id]);
        }

        foreach ($settings as $name => $value)
        {
            // Sort in descending order the working plan exceptions in a reverse order (makes it easier to edit them
            // later on).
            if ($name === 'working_plan_exceptions')
            {
                $value = json_decode($value, TRUE);

                if ( ! $value)
                {
                    $value = [];
                }

                krsort($value);

                $value = json_encode($value);
            }

            $this->set_setting($name, $value, $provider_id);
        }
    }

    /**
     * Set a provider's setting value in the database.
     *
     * The provider and settings record must already exist.
     *
     * @param string $setting_name The setting's name.
     * @param string $value The setting's value.
     * @param int $provider_id The selected provider id.
     *
     * @return bool
     */
    public function set_setting($setting_name, $value, $provider_id)
    {
        $this->db->where(['id_users' => $provider_id]);
        return $this->db->update('user_settings', [$setting_name => $value]);
    }

    /**
     * Save the provider services in the database (use on both insert and update operation).
     *
     * @param array $services Contains the service ids that the selected provider can provide.
     * @param int $provider_id The selected provider record id.
     *
     * @throws Exception When the $services argument type is not array.
     * @throws Exception When the $provider_id argument type is not int.
     */
    protected function save_services($services, $provider_id)
    {
        // Validate method arguments.
        if ( ! is_array($services))
        {
            throw new Exception('Invalid argument type $services: ' . $services);
        }

        if ( ! is_numeric($provider_id))
        {
            throw new Exception('Invalid argument type $provider_id: ' . $provider_id);
        }

        // Save provider services in the database (delete old records and add new).
        $this->db->delete('services_providers', ['id_users' => $provider_id]);

        foreach ($services as $service_id)
        {
            $service_provider = [
                'id_users' => $provider_id,
                'id_services' => $service_id
            ];
            $this->db->insert('services_providers', $service_provider);
        }
    }

    /**
     * Update an existing provider record in the database.
     *
     * @param array $provider Contains the provider data.
     *
     * @return int Returns the record id.
     *
     * @throws Exception When the update operation fails.
     */
    protected function update($provider)
    {
        // Store service and settings (must not be present on the $provider array).
        $services = $provider['services'];
        unset($provider['services']);
        $settings = $provider['settings'];
        unset($provider['settings']);

        if (isset($settings['password']))
        {
            $salt = $this->db->get_where('user_settings', ['id_users' => $provider['id']])->row()->salt;
            $settings['password'] = hash_password($salt, $settings['password']);
        }

        // Update provider record.
        $this->db->where('id', $provider['id']);
        if ( ! $this->db->update('users', $provider))
        {
            throw new Exception('Could not update provider record.');
        }

        $this->save_services($services, $provider['id']);
        $this->save_settings($settings, $provider['id']);

        // Return record id.
        return (int)$provider['id'];
    }

    /**
     * Delete an existing provider record from the database.
     *
     * @param $provider_id
     *
     * @return bool Returns the delete operation result.
     *
     * @throws Exception When the provider id value is not int.
     */
    public function delete($provider_id)
    {
        if ( ! is_numeric($provider_id))
        {
            throw new Exception('Invalid argument type $provider_id: ' . $provider_id);
        }

        $num_rows = $this->db->get_where('users', ['id' => $provider_id])->num_rows();
        if ($num_rows === 0)
        {
            return FALSE; // Record does not exist in database.
        }

        return $this->db->delete('users', ['id' => $provider_id]);
    }

    /**
     * Get a specific field value from the database.
     *
     * @param string $field_name The field name of the value to be returned.
     * @param int $provider_id Record id of the value to be returned.
     *
     * @return string Returns the selected record value from the database.
     *
     * @throws Exception When the $field_name argument is not a valid string.
     * @throws Exception When the $provider_id is not a valid int.
     * @throws Exception When the provider record does not exist in the database.
     * @throws Exception When the selected field value is not present on database.
     */
    public function get_value($field_name, $provider_id)
    {
        if ( ! is_numeric($provider_id))
        {
            throw new Exception('Invalid argument provided as $provider_id: ' . $provider_id);
        }

        if ( ! is_string($field_name))
        {
            throw new Exception('$field_name argument is not a string: ' . $field_name);
        }

        // Check whether the provider record exists in database.
        $result = $this->db->get_where('users', ['id' => $provider_id]);

        if ($result->num_rows() == 0)
        {
            throw new Exception('The record with the $provider_id argument does not exist in the database: '
                . $provider_id);
        }

        $row_data = $result->row_array();

        if ( ! isset($row_data[$field_name]))
        {
            throw new Exception('The given $field_name argument does not exist in the database: ' . $field_name);
        }

        return $row_data[$field_name];
    }

    /**
     * Get all, or specific records from provider's table.
     *
     * Example:
     *
     * $this->Model->get_batch('id = ' . $recordId);
     *
     * @param mixed|null $where (OPTIONAL) The WHERE clause of the query to be executed.
     * @param int|null $limit
     * @param int|null $offset
     * @param mixed|null $order_by
     *
     * @return array Returns the rows from the database.
     */
    public function get_batch($where = NULL, $limit = NULL, $offset = NULL, $order_by = NULL)
    {
        // CI db class may confuse two where clauses made in the same time, so get the role id first and then apply the
        // get_batch() where clause.
        $role_id = $this->get_providers_role_id();

        if ($where !== NULL)
        {
            $this->db->where($where);
        }

        if ($order_by !== NULL)
        {
            $this->db->order_by($order_by);
        }

        $batch = $this->db->get_where('users', ['id_roles' => $role_id], $limit, $offset)->result_array();

        // Include each provider services and settings.
        foreach ($batch as &$provider)
        {
            // Services
            $services = $this->db->get_where('services_providers',
                ['id_users' => $provider['id']])->result_array();
            $provider['services'] = [];
            foreach ($services as $service)
            {
                $provider['services'][] = $service['id_services'];
            }

            // Settings
            $provider['settings'] = $this->db->get_where('user_settings', ['id_users' => $provider['id']])->row_array();
            unset($provider['settings']['id_users']);
        }

        // Return provider records in an array.
        return $batch;
    }

    /**
     * Get the available system providers.
     *
     * This method returns the available providers and the services that can provide.
     *
     * @return array Returns an array with the providers data.
     */
    public function get_available_providers()
    {
        // Get provider records from database.
        $this->db
            ->select('users.*')
            ->from('users')
            ->join('roles', 'roles.id = users.id_roles', 'inner')
            ->where('roles.slug', DB_SLUG_PROVIDER)
            ->order_by('first_name ASC, last_name ASC, email ASC');

        $providers = $this->db->get()->result_array();

        // Include each provider services and settings.
        foreach ($providers as &$provider)
        {
            // Services
            $services = $this->db->get_where('services_providers', ['id_users' => $provider['id']])->result_array();

            $provider['services'] = [];
            foreach ($services as $service)
            {
                $provider['services'][] = $service['id_services'];
            }

            // Settings
            $provider['settings'] = $this->db->get_where('user_settings', ['id_users' => $provider['id']])->row_array();
            unset(
                $provider['settings']['username'],
                $provider['settings']['password'],
                $provider['settings']['salt']
            );
        }

        // Return provider records.
        return $providers;
    }

    /**
     * Save the provider working plan exception.
     *
     * @param string $date The working plan exception date (in YYYY-MM-DD format).
     * @param array $working_plan_exception Contains the working plan exception information ("start", "end" and "breaks"
     * properties).
     * @param int $provider_id The selected provider record id.
     *
     * @return bool Return if the new working plan exceptions is correctly saved to DB.
     *
     * @throws Exception If start time is after the end time.
     * @throws Exception If $provider_id argument is invalid.
     */
    public function save_working_plan_exception($date, $working_plan_exception, $provider_id)
    {
        // Validate the working plan exception data.
        $start = date('H:i', strtotime($working_plan_exception['start']));
        $end = date('H:i', strtotime($working_plan_exception['end']));

        if ($start > $end)
        {
            throw new Exception('Working plan exception "start" must be prior to "end".');
        }

        // Make sure the provider record exists.
        $conditions = [
            'id' => $provider_id,
            'id_roles' => $this->db->get_where('roles', ['slug' => DB_SLUG_PROVIDER])->row()->id
        ];

        if ($this->db->get_where('users', $conditions)->num_rows() === 0)
        {
            throw new Exception('Provider record was not found in database: ' . $provider_id);
        }

        // Add record to database.
        $working_plan_exceptions = json_decode($this->get_setting('working_plan_exceptions', $provider_id), TRUE);

        if ( ! isset($working_plan_exception['breaks']))
        {
            $working_plan_exception['breaks'] = [];
        }

        $working_plan_exceptions[$date] = $working_plan_exception;

        return $this->set_setting(
            'working_plan_exceptions',
            json_encode($working_plan_exceptions),
            $provider_id
        );
    }

    /**
     * Get a providers setting from the database.
     *
     * @param string $setting_name The setting name that is going to be returned.
     * @param int $provider_id The selected provider id.
     *
     * @return string Returns the value of the selected user setting.
     */
    public function get_setting($setting_name, $provider_id)
    {
        $provider_settings = $this->db->get_where('user_settings', ['id_users' => $provider_id])->row_array();

        return $provider_settings[$setting_name];
    }

    /**
     * Delete a provider working plan exception.
     *
     * @param string $date The working plan exception date (in YYYY-MM-DD format).
     * @param int $provider_id The selected provider record id.
     *
     * @return bool Return if the new working plan exceptions is correctly deleted from DB.
     *
     * @throws Exception If $provider_id argument is invalid.
     */
    public function delete_working_plan_exception($date, $provider_id)
    {
        $provider = $this->get_row($provider_id);

        $working_plan_exceptions = json_decode($provider['settings']['working_plan_exceptions'], TRUE);

        if ( ! isset($working_plan_exceptions[$date]))
        {
            return TRUE; // The selected date does not exist in provider's settings.
        }

        unset($working_plan_exceptions[$date]);

        return $this->set_setting(
            'working_plan_exceptions',
            json_encode(empty($working_plan_exceptions) ? new stdClass() : $working_plan_exceptions),
            $provider_id
        );
    }

    /**
     * Get a specific provider record from the database.
     *
     * @param int $provider_id The id of the record to be returned.
     *
     * @return array Returns an associative array with the selected record's data. Each key has the same name as the
     * database field names.
     *
     * @throws Exception When the selected record does not exist in database.
     */
    public function get_row($provider_id)
    {
        if ( ! is_numeric($provider_id))
        {
            throw new Exception('$provider_id argument is not a valid numeric value: ' . $provider_id);
        }

        // Check if selected record exists on database.
        if ($this->db->get_where('users', ['id' => $provider_id])->num_rows() == 0)
        {
            throw new Exception('Selected record does not exist in the database.');
        }

        // Get provider data.
        $provider = $this->db->get_where('users', ['id' => $provider_id])->row_array();

        // Include provider services.
        $services = $this->db->get_where('services_providers',
            ['id_users' => $provider_id])->result_array();
        $provider['services'] = [];
        foreach ($services as $service)
        {
            $provider['services'][] = $service['id_services'];
        }

        // Include provider settings.
        $provider['settings'] = $this->db->get_where('user_settings', ['id_users' => $provider_id])->row_array();
        unset($provider['settings']['id_users']);

        // Return provider data array.
        return $provider;
    }
}
