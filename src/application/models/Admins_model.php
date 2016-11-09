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
 * Admins Model Class
 * 
 * Handles the database actions for admin users management.
 * 
 * Data Structure: 
 *      'fist_name'
 *      'last_name' (required)
 *      'email' (required)
 *      'mobile_number'
 *      'phone_number' (required)
 *      'address'
 *      'city'
 *      'state'
 *      'zip_code'
 *      'notes'
 *      'id_roles'
 *      'settings' >>> array that contains user settings (username, password etc)
 *
 * @package Models
 */
class Admins_Model extends CI_Model {
    /**
     * Add (insert or update) an admin user record into database.
     * 
     * @param array $admin Contains the admin user data.
     * @return int Returns the record id.
     * @throws Exception When the admin data are invalid (see validate() method).
     */
    public function add($admin) {
        $this->validate($admin);
        
        if ($this->exists($admin) && !isset($admin['id'])) {
            $admin['id'] = $this->find_record_id($admin);
        }
        
        if (!isset($admin['id'])) {
            $admin['id'] = $this->insert($admin);
        } else {
            $admin['id'] = $this->update($admin);
        }
        
        return intval($admin['id']);
    }
    
    /**
     * Check whether a particular admin record exists in the database.
     * 
     * @param array $admin Contains the admin data. The 'email' value is required to be present
     * at the moment.
     * @return bool Returns whether the record exists or not.
     * @throws Exception When the 'email' value is not present on the $admin argument.
     */
    public function exists($admin) {
        if (!isset($admin['email'])) {
            throw new Exception('Admin email is not provided: ' . print_r($admin, TRUE));
        }
        
        // This method shouldn't depend on another method of this class.
        $num_rows = $this->db
                ->select('*')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_users.email', $admin['email'])
                ->where('ea_roles.slug', DB_SLUG_ADMIN)
                ->get()->num_rows();
        
        return ($num_rows > 0) ? TRUE : FALSE;
    }
    
    /**
     * Insert a new admin record into the database.
     * 
     * @param array $admin Contains the admin data.
     * @return int Returns the new record id.
     * @throws Exception When the insert operation fails.
     */
    public function insert($admin) {
        $this->load->helper('general');
        
        $admin['id_roles'] = $this->get_admin_role_id();
        $settings = $admin['settings'];
        unset($admin['settings']);
        
        $this->db->trans_begin();        
        
        if (!$this->db->insert('ea_users', $admin)) {
            throw new Exception('Could not insert admin into the database.');
        }
        
        $admin['id'] = intval($this->db->insert_id());
        $settings['id_users'] = $admin['id'];
        $settings['salt'] = generate_salt();
        $settings['password'] = hash_password($settings['salt'], $settings['password']);
        
        // Insert admin settings. 
        if (!$this->db->insert('ea_user_settings', $settings)) {
            $this->db->trans_rollback();
            throw new Exception('Could not insert admin settings into the database.');
        }
        
        $this->db->trans_complete();
        
        return $admin['id'];
    }   
    
    /**
     * Update an existing admin record in the database.
     * 
     * @param array $admin Contains the admin record data.
     * @return int Returns the record id.
     * @throws Exception When the update operation fails.
     */
    public function update($admin) {
        $this->load->helper('general');
        
        $settings = $admin['settings'];
        unset($admin['settings']);
        $settings['id_users'] = $admin['id'];
        
        if (isset($settings['password'])) {
            $salt = $this->db->get_where('ea_user_settings', array('id_users' => $admin['id']))->row()->salt;
            $settings['password'] = hash_password($salt, $settings['password']);
        }
        
        $this->db->where('id', $admin['id']);
        if (!$this->db->update('ea_users', $admin)) {
            throw new Exception('Could not update admin record.');
        }
        
        $this->db->where('id_users', $settings['id_users']);
        if (!$this->db->update('ea_user_settings', $settings)) {
            throw new Exception('Could not update admin settings.');
        }
        
        return intval($admin['id']);
    }
    
    /**
     * Find the database record id of an admin user.
     * 
     * @param array $admin Contains the admin data. The 'email' value is required in order to 
     * find the record id.
     * @return int Returns the record id
     * @throws Exception When the 'email' value is not present on the $admin array.
     */
    public function find_record_id($admin) {
        if (!isset($admin['email'])) {
            throw new Exception('Admin email was not provided: ' . print_r($admin, TRUE));
        }
        
        $result = $this->db
                ->select('ea_users.id')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_users.email', $admin['email'])
                ->where('ea_roles.slug', DB_SLUG_ADMIN)
                ->get();
        
        if ($result->num_rows() == 0) {
            throw new Exception('Could not find admin record id.');
        }
        
        return intval($result->row()->id);
    }
    
    /**
     * Validate admin user data before add() operation is executed.
     * 
     * @param array $admin Contains the admin user data.
     * @return bool Returns the validation result.
     * 
     * @throws Exception When data are invalid.
     */
    public function validate($admin) {
        $this->load->helper('data_validation');

        // If a record id is provided then check whether the record exists in the database.
        if (isset($admin['id'])) {
            $num_rows = $this->db->get_where('ea_users', array('id' => $admin['id']))
                    ->num_rows();
            if ($num_rows == 0) {
                throw new Exception('Given admin id does not exist in database: ' . $admin['id']);
            }
        }

        // Validate required fields integrity.
        if (!isset($admin['last_name'])
                || !isset($admin['email'])
                || !isset($admin['phone_number'])) { 
            throw new Exception('Not all required fields are provided: ' . print_r($admin, TRUE));
        }

        // Validate admin email address.
        if (!filter_var($admin['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address provided: ' . $admin['email']);
        }
        
        // Check if username exists.
        if (isset($admin['settings']['username'])) {
            $user_id = (isset($admin['id'])) ? $admin['id'] : '';
            if (!$this->validate_username($admin['settings']['username'], $user_id)) {
                throw new Exception ('Username already exists. Please select a different ' 
                        . 'username for this record.');
            }
        }

        // Validate admin password
        if (isset($admin['settings']['password'])) {
            if (strlen($admin['settings']['password']) < MIN_PASSWORD_LENGTH) {
                throw new Exception('The user password must be at least ' 
                        . MIN_PASSWORD_LENGTH . ' characters long.');
            }
        }

        // Validate calendar view mode. 
        if (isset($admin['settings']['calendar_view']) && ($admin['settings']['calendar_view'] !== CALENDAR_VIEW_DEFAULT 
                && $admin['settings']['calendar_view'] !== CALENDAR_VIEW_TABLE)) {
             throw new Exception('The calendar view setting must be either "' . CALENDAR_VIEW_DEFAULT 
                    . '" or "' . CALENDAR_VIEW_TABLE . '", given: ' .  $admin['settings']['calendar_view']);
        }
        
        // When inserting a record the email address must be unique.
        $admin_id = (isset($admin['id'])) ? $admin['id'] : '';
        
        $num_rows = $this->db
                ->select('*')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->where('ea_roles.slug', DB_SLUG_ADMIN)
                ->where('ea_users.email', $admin['email'])
                ->where('ea_users.id <>', $admin_id)
                ->get()
                ->num_rows();
        
        if ($num_rows > 0) {
            throw new Exception('Given email address belongs to another admin record. ' 
                    . 'Please use a different email.');
        }
            
        return TRUE; // Operation completed successfully.
    }
    
    /**
     * Delete an existing admin record from the database.
     * 
     * @param numeric $admin_id The admin record id to be deleted.
     * @return bool Returns the delete operation result.
     * @throws Exception When the $admin_id is not a valid numeric value.
     * @throws Exception When the record to be deleted is the only one admin user left on 
     * the system.
     */
    public function delete($admin_id) {
        if (!is_numeric($admin_id)) {
            throw new Exception('Invalid argument type $admin_id: ' . $admin_id);
        }
        
        // There must be always at least one admin user. If this is the only admin
        // the system, it cannot be deleted.
        $admin_count = $this->db->get_where('ea_users', 
                array('id_roles' => $this->get_admin_role_id()))->num_rows();
        if ($admin_count == 1) {
            throw new Exception('Record could not be deleted. The system requires at least '
                    . 'one admin user.');
        }
        
        $num_rows = $this->db->get_where('ea_users', array('id' => $admin_id))->num_rows();
        if ($num_rows == 0) {
            return FALSE; // Record does not exist in database.
        }
        
        return $this->db->delete('ea_users', array('id' => $admin_id));
    }
    
    /**
     * Get a specific admin record from the database.
     * 
     * @param numeric $admin_id The id of the record to be returned.
     * @return array Returns an array with the admin user data.
     * @throws Exception When the $admin_id is not a valid numeric value.
     */
    public function get_row($admin_id) {
        if (!is_numeric($admin_id)) {
            throw new Exception('$admin_id argument is not a valid numeric value: ' . $admin_id);
        }

        // Check if record exists
        if ($this->db->get_where('ea_users', array('id' => $admin_id))->num_rows() == 0) {
            throw new Exception('The given admin id does not match a record in the database.');
        }
        
        $admin = $this->db->get_where('ea_users', array('id' => $admin_id))->row_array();
        
        $admin['settings'] = $this->db->get_where('ea_user_settings', 
                array('id_users' => $admin_id))->row_array();
        unset($admin['settings']['id_users']);
        
        
        return $admin;
    }
    
    /**
     * Get a specific field value from the database.
     * 
     * @param string $field_name The field name of the value to be returned.
     * @param numeric $admin_id Record id of the value to be returned.
     * @return string Returns the selected record value from the database.
     * @throws Exception When the $field_name argument is not a valid string.
     * @throws Exception When the $admin_id is not a valid numeric.
     * @throws Exception When the admin record does not exist in the database.
     * @throws Exception When the selected field value is not present on database.
     */
    public function get_value($field_name, $admin_id) {
        if (!is_string($field_name)) {
            throw new Exception('$field_name argument is not a string: ' . $field_name);
        }
        
        if (!is_numeric($admin_id)) {
            throw new Exception('$admin_id argument is not a valid numeric value: ' . $admin_id);
        }
        
        // Check whether the admin record exists. 
        $result = $this->db->get_where('ea_users', array('id' => $admin_id));
        if ($result->num_rows() == 0) {
            throw new Exception('The record with the given id does not exist in the '
                    . 'database: ' . $admin_id);
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
     * Get all, or specific admin records from database.
     * 
     * @param string|array $where_clause (OPTIONAL) The WHERE clause of the query to be executed. 
     * Use this to get specific admin records.
     * @return array Returns an array with admin records.
     */
    public function get_batch($where_clause = '') {
        $role_id = $this->get_admin_role_id();
        
        if ($where_clause != '') {
            $this->db->where($where_clause);
        }
        
        $batch = $this->db->get_where('ea_users', array('id_roles' => $role_id))->result_array();
        
        // Get every admin settings.
        foreach ($batch as &$admin) {
            $admin['settings'] = $this->db->get_where('ea_user_settings', 
                    array('id_users' => $admin['id']))->row_array();
            unset($admin['settings']['id_users']);
        }
        
        return $batch;
    }
    
    /**
     * Get the admin users role id. 
     * 
     * @return int Returns the role record id. 
     */
    public function get_admin_role_id() {
        return intval($this->db->get_where('ea_roles', array('slug' => DB_SLUG_ADMIN))->row()->id);
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

/* End of file admins_model.php */
/* Location: ./application/models/admins_model.php */
