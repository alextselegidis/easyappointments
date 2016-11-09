<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed.');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Secretaries Model
 *
 * Handles the db actions that have to do with secretaries.
 *
 * Data Structure
 *      'first_name'
 *      'last_name'
 *      'email'
 *      'mobile_number'
 *      'phone_number'
 *      'address'
 *      'city'
 *      'state'
 *      'zip_code'
 *      'notes'
 *      'id_roles'
 *      'providers' >> array with provider ids that the secretary handles
 *      'settings' >> array with the secretary settings
 *
 * @package Models
 */
class Secretaries_Model extends CI_Model {
    /**
     * Add (insert or update) a secretary user record into database.
     *
     * @param array $secretary Contains the secretary user data.
     * @return int Returns the record id.
     * @throws Exception When the secretary data are invalid (see validate() method).
     */
    public function add($secretary) {
        $this->validate($secretary);

        if ($this->exists($secretary) && !isset($secretary['id'])) {
            $secretary['id'] = $this->find_record_id($secretary);
        }

        if (!isset($secretary['id'])) {
            $secretary['id'] = $this->_insert($secretary);
        } else {
            $secretary['id'] = $this->_update($secretary);
        }

        return intval($secretary['id']);
    }

    /**
     * Check whether a particular secretary record exists in the database.
     *
     * @param array $secretary Contains the secretary data. The 'email' value is required to
     * be present at the moment.
     * @return bool Returns whether the record exists or not.
     * @throws Exception When the 'email' value is not present on the $secretary argument.
     */
    public function exists($secretary) {
        if (!isset($secretary['email'])) {
            throw new Exception('Secretary email is not provided: ' . print_r($secretary, TRUE));
        }

        // This method shouldn't depend on another method of this class.
        $num_rows = $this->db
                ->select('*')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_users.email', $secretary['email'])
                ->where('ea_roles.slug', DB_SLUG_SECRETARY)
                ->get()->num_rows();

        return ($num_rows > 0) ? TRUE : FALSE;
    }

     /**
     * Insert a new secretary record into the database.
     *
     * @param array $secretary Contains the secretary data.
     * @return int Returns the new record id.
     * @throws Exception When the insert operation fails.
     */
    protected function _insert($secretary) {
        $this->load->helper('general');

        $providers = $secretary['providers'];
        unset($secretary['providers']);
        $settings = $secretary['settings'];
        unset($secretary['settings']);

        $secretary['id_roles'] = $this->get_secretary_role_id();

        if (!$this->db->insert('ea_users', $secretary)) {
            throw new Exception('Could not insert secretary into the database.');
        }

        $secretary['id'] = intval($this->db->insert_id());
        $settings['salt'] = generate_salt();
        $settings['password'] = hash_password($settings['salt'], $settings['password']);

        $this->save_providers($providers, $secretary['id']);
        $this->save_settings($settings, $secretary['id']);

        return $secretary['id'];
    }

    /**
     * Update an existing secretary record in the database.
     *
     * @param array $secretary Contains the secretary record data.
     * @return int Returns the record id.
     * @throws Exception When the update operation fails.
     */
    protected function _update($secretary) {
        $this->load->helper('general');

        $providers = $secretary['providers'];
        unset($secretary['providers']);
        $settings = $secretary['settings'];
        unset($secretary['settings']);

        if (isset($settings['password'])) {
            $salt = $this->db->get_where('ea_user_settings', array('id_users' => $secretary['id']))->row()->salt;
            $settings['password'] = hash_password($salt, $settings['password']);
        }

        $this->db->where('id', $secretary['id']);
        if (!$this->db->update('ea_users', $secretary)){
            throw new Exception('Could not update secretary record.');
        }

        $this->save_providers($providers, $secretary['id']);
        $this->save_settings($settings, $secretary['id']);

        return intval($secretary['id']);
    }

    /**
     * Find the database record id of a secretary.
     *
     * @param array $secretary Contains the secretary data. The 'email' value is required
     * in order to find the record id.
     * @return int Returns the record id
     * @throws Exception When the 'email' value is not present on the $secretary array.
     */
    public function find_record_id($secretary) {
        if (!isset($secretary['email'])) {
            throw new Exception('Secretary email was not provided: ' . print_r($secretary, TRUE));
        }

        $result = $this->db
                ->select('ea_users.id')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_users.email', $secretary['email'])
                ->where('ea_roles.slug', DB_SLUG_SECRETARY)
                ->get();

        if ($result->num_rows() == 0) {
            throw new Exception('Could not find secretary record id.');
        }

        return intval($result->row()->id);
    }

    /**
     * Validate secretary user data before add() operation is executed.
     *
     * @param array $secretary Contains the secretary user data.
     * @return bool Returns the validation result.
     */
    public function validate($secretary) {
        $this->load->helper('data_validation');

        // If a record id is provided then check whether the record exists in the database.
        if (isset($secretary['id'])) {
            $num_rows = $this->db->get_where('ea_users', array('id' => $secretary['id']))
                    ->num_rows();
            if ($num_rows == 0) {
                throw new Exception('Given secretary id does not exist in database: ' . $secretary['id']);
            }
        }

        // Validate 'providers' value data type (must be array)
        if (isset($secretary['providers']) && !is_array($secretary['providers'])) {
            throw new Exception('Secretary providers value is not an array.');
        }

        // Validate required fields integrity.
        if (!isset($secretary['last_name'])
                || !isset($secretary['email'])
                || !isset($secretary['phone_number'])) {
            throw new Exception('Not all required fields are provided: ' . print_r($secretary, TRUE));
        }

        // Validate secretary email address.
        if (!filter_var($secretary['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address provided: ' . $secretary['email']);
        }

        // Check if username exists.
        if (isset($secretary['settings']['username'])) {
            $user_id = (isset($secretary['id'])) ? $secretary['id'] : '';
            if (!$this->validate_username($secretary['settings']['username'], $user_id)) {
                throw new Exception ('Username already exists. Please select a different '
                        . 'username for this record.');
            }
        }

        // Validate secretary password.
        if (isset($secretary['settings']['password'])) {
            if (strlen($secretary['settings']['password']) < MIN_PASSWORD_LENGTH) {
                throw new Exception('The user password must be at least '
                        . MIN_PASSWORD_LENGTH . ' characters long.');
            }
        }

        // Validate calendar view mode. 
        if (isset($secretary['settings']['calendar_view']) && ($secretary['settings']['calendar_view'] !== CALENDAR_VIEW_DEFAULT 
                && $secretary['settings']['calendar_view'] !== CALENDAR_VIEW_TABLE)) {
             throw new Exception('The calendar view setting must be either "' . CALENDAR_VIEW_DEFAULT 
                    . '" or "' . CALENDAR_VIEW_TABLE . '", given: ' .  $secretary['settings']['calendar_view']);
        }

        // When inserting a record the email address must be unique.
        $secretary_id = (isset($secretary['id'])) ? $secretary['id'] : '';

        $num_rows = $this->db
                ->select('*')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_roles.slug', DB_SLUG_SECRETARY)
                ->where('ea_users.email', $secretary['email'])
                ->where('ea_users.id <>', $secretary_id)
                ->get()
                ->num_rows();

        if ($num_rows > 0) {
            throw new Exception('Given email address belongs to another secretary record. '
                    . 'Please use a different email.');
        }

        return TRUE;
    }

    /**
     * Delete an existing secretary record from the database.
     *
     * @param numeric $secretary_id The secretary record id to be deleted.
     * @return bool Returns the delete operation result.
     * @throws Exception When the $secretary_id is not a valid numeric value.
     */
    public function delete($secretary_id) {
        if (!is_numeric($secretary_id)) {
            throw new Exception('Invalid argument type $secretary_id: ' . $secretary_id);
        }

        $num_rows = $this->db->get_where('ea_users', array('id' => $secretary_id))->num_rows();
        if ($num_rows == 0) {
            return FALSE; // Record does not exist in database.
        }

        return $this->db->delete('ea_users', array('id' => $secretary_id));
    }

    /**
     * Get a specific secretary record from the database.
     *
     * @param numeric $secretary_id The id of the record to be returned.
     * @return array Returns an array with the secretary user data.
     * @throws Exception When the $secretary_id is not a valid numeric value.
     * @throws Exception When given record id does not exist in the database.
     */
    public function get_row($secretary_id) {
        if (!is_numeric($secretary_id)) {
            throw new Exception('$secretary_id argument is not a valid numeric value: ' . $secretary_id);
        }

        // Check if record exists
        if ($this->db->get_where('ea_users', array('id' => $secretary_id))->num_rows() == 0) {
            throw new Exception('The given secretary id does not match a record in the database.');
        }

        $secretary = $this->db->get_where('ea_users', array('id' => $secretary_id))->row_array();

        $secretary_providers = $this->db->get_where('ea_secretaries_providers',
                array('id_users_secretary' => $secretary['id']))->result_array();
        $secretary['providers'] = array();
        foreach($secretary_providers as $secretary_provider) {
            $secretary['providers'][] = $secretary_provider['id_users_provider'];
        }

        $secretary['settings'] = $this->db->get_where('ea_user_settings',
                array('id_users' => $secretary['id']))->row_array();
        unset($secretary['settings']['id_users'], $secretary['settings']['salt']);

        return $secretary;
    }

    /**
     * Get a specific field value from the database.
     *
     * @param string $field_name The field name of the value to be returned.
     * @param numeric $secretary_id Record id of the value to be returned.
     * @return string Returns the selected record value from the database.
     * @throws Exception When the $field_name argument is not a valid string.
     * @throws Exception When the $secretary_id is not a valid numeric.
     * @throws Exception When the secretary record does not exist in the database.
     * @throws Exception When the selected field value is not present on database.
     */
    public function get_value($field_name, $secretary_id) {
        if (!is_string($field_name)) {
            throw new Exception('$field_name argument is not a string: ' . $field_name);
        }

        if (!is_numeric($secretary_id)) {
            throw new Exception('$secretary_id argument is not a valid numeric value: ' . $secretary_id);
        }

        // Check whether the secretary record exists.
        $result = $this->db->get_where('ea_users', array('id' => $secretary_id));
        if ($result->num_rows() == 0) {
            throw new Exception('The record with the given id does not exist in the '
                    . 'database: ' . $secretary_id);
        }

        // Check if the required field name exist in database.
        $provider = $result->row_array();
        if (!isset($provider[$field_name])) {
            throw new Exception('The given $field_name argument does not exist in the '
                    . 'database: ' . $field_name);
        }

        return $provider[$field_name];
    }

    /**
     * Get all, or specific secretary records from database.
     *
     * @param string|array $where_clause (OPTIONAL) The WHERE clause of the query to be executed.
     * Use this to get specific secretary records.
     * @return array Returns an array with secretary records.
     */
    public function get_batch($where_clause = '') {
        $role_id = $this->get_secretary_role_id();

        if ($where_clause != '') {
            $this->db->where($where_clause);
        }

        $this->db->where('id_roles', $role_id);
        $batch = $this->db->get('ea_users')->result_array();

        // Include every secretary providers.
        foreach ($batch as &$secretary) {
            $secretary_providers = $this->db->get_where('ea_secretaries_providers',
                    array('id_users_secretary' => $secretary['id']))->result_array();

            $secretary['providers'] = array();
            foreach($secretary_providers as $secretary_provider) {
                $secretary['providers'][] = $secretary_provider['id_users_provider'];
            }

            $secretary['settings'] = $this->db->get_where('ea_user_settings',
                    array('id_users' => $secretary['id']))->row_array();
            unset($secretary['settings']['id_users']);
        }

        return $batch;
    }

    /**
     * Get the secretary users role id.
     *
     * @return int Returns the role record id.
     */
    public function get_secretary_role_id() {
        return intval($this->db->get_where('ea_roles', array('slug' => DB_SLUG_SECRETARY))->row()->id);
    }

    /**
     * Save a secretary handling users.
     * @param array $providers Contains the provider ids that are handled by the secretary.
     * @param numeric $secretary_id The selected secretary record.
     */
    protected function save_providers($providers, $secretary_id) {
        if (!is_array($providers)) {
            throw new Exception('Invalid argument given $providers: ' . print_r($providers, TRUE));
        }

        // Delete old connections
        $this->db->delete('ea_secretaries_providers', array('id_users_secretary' => $secretary_id));

        if (count($providers) > 0) {
            foreach ($providers as $provider_id) {
                $this->db->insert('ea_secretaries_providers', array(
                    'id_users_secretary' => $secretary_id,
                    'id_users_provider' => $provider_id
                ));
            }
        }
    }

    /**
     * Save the secretary settings (used from insert or update operation).
     *
     * @param array $settings Contains the setting values.
     * @param numeric $secretary_id Record id of the secretary.
     */
    protected function save_settings($settings, $secretary_id) {
        if (!is_numeric($secretary_id)) {
            throw new Exception('Invalid $provider_id argument given:' . $secretary_id);
        }

        if (count($settings) == 0 || !is_array($settings)) {
            throw new Exception('Invalid $settings argument given:' . print_r($settings, TRUE));
        }

        // Check if the setting record exists in db.
        $num_rows = $this->db->get_where('ea_user_settings',
                array('id_users' => $secretary_id))->num_rows();
        if ($num_rows == 0) {
            $this->db->insert('ea_user_settings', array('id_users' => $secretary_id));
        }

        foreach($settings as $name => $value) {
            $this->set_setting($name, $value, $secretary_id);
        }
    }

    /**
     * Get a providers setting from the database.
     *
     * @param string $setting_name The setting name that is going to be returned.
     * @param int $secretary_id The selected provider id.
     * @return string Returns the value of the selected user setting.
     */
    public function get_setting($setting_name, $secretary_id) {
        $provider_settings = $this->db->get_where('ea_user_settings',
                array('id_users' => $secretary_id))->row_array();
        return $provider_settings[$setting_name];
    }

    /**
     * Set a provider's setting value in the database.
     *
     * The provider and settings record must already exist.
     *
     * @param string $setting_name The setting's name.
     * @param string $value The setting's value.
     * @param numeric $secretary_id The selected provider id.
     */
    public function set_setting($setting_name, $value, $secretary_id) {
        $this->db->where(array('id_users' => $secretary_id));
        return $this->db->update('ea_user_settings', array($setting_name => $value));
    }

    /**
     * Validate Records Username
     *
     * @param string $username The provider records username.
     * @param numeric $user_id The user record id.
     * @return bool Returns the validation result.
     */
    public function validate_username($username, $user_id) {
        $num_rows = $this->db->get_where('ea_user_settings',
                array('username' => $username, 'id_users <> ' => $user_id))->num_rows();
        return ($num_rows > 0) ? FALSE : TRUE;
    }
}

/* End of file secretaries_model.php */
/* Location: ./application/models/secretaries_model.php */
