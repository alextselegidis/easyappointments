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
 * User Model
 *
 * Contains current user's methods.
 *
 * @package Models
 */
class User_Model extends CI_Model {
    /**
     * Returns the user from the database for the "settings" page.
     *
     * @param numeric $user_id User record id.
     *
     * @return array Returns an array with user data.
     *
     * @todo Refactor this method as it does not do as it states. 
     */
    public function get_settings($user_id) {
        $user = $this->db->get_where('ea_users', array('id' => $user_id))->row_array();
        $user['settings'] = $this->db->get_where('ea_user_settings', array('id_users' => $user_id))->row_array();
        unset($user['settings']['id_users']);
        return $user;
    }

    /**
     * This method saves the user record into the database (used in backend settings page).
     *
     * @param array $user Contains the current users data.
     *
     * @return bool Returns the operation result.
     *
     * @todo Refactor this method as it does not do as it states. 
     */
    public function save_settings($user) {
        $user_settings = $user['settings'];
        $user_settings['id_users'] = $user['id'];
        unset($user['settings']);

        // Prepare user password (hash).
        if (isset($user_settings['password'])) {
            $this->load->helper('general');
            $salt = $this->db->get_where('ea_user_settings', array('id_users' => $user['id']))->row()->salt;
            $user_settings['password'] = hash_password($salt, $user_settings['password']);
        }

        if (!$this->db->update('ea_users', $user, array('id' => $user['id']))) {
            return FALSE;
        }

        if (!$this->db->update('ea_user_settings', $user_settings, array('id_users' => $user['id']))) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Retrieve user's salt from database.
     *
     * @param string $username This will be used to find the user record.
     *
     * @return string Returns the salt db value.
     */
    public function get_salt($username) {
        $user =  $this->db->get_where('ea_user_settings', array('username' => $username))->row_array();
        return ($user) ? $user['salt'] : '';
    }

    /**
     * Performs the check of the given user credentials.
     *
     * @param string $username Given user's name.
     * @param type $password Given user's password (not hashed yet).
     *
     * @return array|null Returns the session data of the logged in user or null on failure.
     */
    public function check_login($username, $password) {
        $this->load->helper('general');
        $salt = $this->user_model->get_salt($username);
        $password = hash_password($salt, $password);

        $user_data = $this->db
                ->select('ea_users.id AS user_id, ea_users.email AS user_email, '
                        . 'ea_roles.slug AS role_slug, ea_user_settings.username')
                ->from('ea_users')
                ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
                ->join('ea_user_settings', 'ea_user_settings.id_users = ea_users.id')
                ->where('ea_user_settings.username', $username)
                ->where('ea_user_settings.password', $password)
                ->get()->row_array();

        return ($user_data) ? $user_data : NULL;
    }

    /**
     * Get the given user's display name (first + last name).
     *
     * @param numeric $user_id The given user record id.
     *
     * @return string Returns the user display name.
     */
    public function get_user_display_name($user_id) {
        if (!is_numeric($user_id))
            throw new Exception ('Invalid argument given ($user_id = "' . $user_id . '").');
        $user = $this->db->get_where('ea_users', array('id' => $user_id))->row_array();
        return $user['first_name'] . ' ' . $user['last_name'];
    }

    /**
     * If the given arguments correspond to an existing user record, generate a new
     * password and send it with an email.
     *
     * @param string $username
     * @param string $email
     *
     * @return string|bool Returns the new password on success or FALSE on failure.
     */
    public function regenerate_password($username, $email) {
        $this->load->helper('general');

        $result = $this->db
                ->select('ea_users.id')
                ->from('ea_users')
                ->join('ea_user_settings', 'ea_user_settings.id_users = ea_users.id', 'inner')
                ->where('ea_users.email', $email)
                ->where('ea_user_settings.username', $username)
                ->get();

        if ($result->num_rows() == 0) return FALSE;

        $user_id = $result->row()->id;

        // Create a new password and send it with an email to the given email address.
        $new_password = generate_random_string();
        $salt = $this->db->get_where('ea_user_settings', array('id_users' => $user_id))->row()->salt;
        $hash_password = hash_password($salt, $new_password);
        $this->db->update('ea_user_settings', array('password' => $hash_password), array('id_users' => $user_id));

        return $new_password;
    }
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */
