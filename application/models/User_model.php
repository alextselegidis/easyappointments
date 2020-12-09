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
 * User Model
 *
 * Contains current user's methods.
 *
 * @package Models
 */
class User_model extends EA_Model {
    /**
     * User_Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('timezones');
        $this->load->helper('general');
        $this->load->helper('string');
    }

    /**
     * Returns the user from the database for the "settings" page.
     *
     * @param int $user_id User record id.
     *
     * @return array Returns an array with user data.
     */
    public function get_user($user_id)
    {
        $user = $this->db->get_where('users', ['id' => $user_id])->row_array();
        $user['settings'] = $this->db->get_where('user_settings', ['id_users' => $user_id])->row_array();
        unset($user['settings']['id_users']);
        return $user;
    }

    /**
     * This method saves the user record into the database (used in backend settings page).
     *
     * @param array $user Contains the current users data.
     *
     * @return bool Returns the operation result.
     */
    public function save_user($user)
    {
        $user_settings = $user['settings'];
        $user_settings['id_users'] = $user['id'];
        unset($user['settings']);

        // Prepare user password (hash).
        if (isset($user_settings['password']))
        {
            $salt = $this->db->get_where('user_settings', ['id_users' => $user['id']])->row()->salt;
            $user_settings['password'] = hash_password($salt, $user_settings['password']);
        }

        if ( ! $this->db->update('users', $user, ['id' => $user['id']]))
        {
            return FALSE;
        }

        if ( ! $this->db->update('user_settings', $user_settings, ['id_users' => $user['id']]))
        {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Performs the check of the given user credentials.
     *
     * @param string $username Given user's name.
     * @param string $password Given user's password (not hashed yet).
     *
     * @return array|null Returns the session data of the logged in user or null on failure.
     */
    public function check_login($username, $password)
    {
        $salt = $this->get_salt($username);
        $password = hash_password($salt, $password);

        $user_settings = $this->db->get_where('user_settings', [
            'username' => $username,
            'password' => $password
        ])->row_array();

        if (empty($user_settings))
        {
            return NULL;
        }

        $user = $this->db->get_where('users', ['id' => $user_settings['id_users']])->row_array();

        if (empty($user))
        {
            return NULL;
        }

        $role = $this->db->get_where('roles', ['id' => $user['id_roles']])->row_array();

        if (empty($role))
        {
            return NULL;
        }

        $default_timezone = $this->timezones->get_default_timezone();

        return [
            'user_id' => $user['id'],
            'user_email' => $user['email'],
            'username' => $username,
            'timezone' => isset($user['timezone']) ? $user['timezone'] : $default_timezone,
            'role_slug' => $role['slug'],
        ];
    }

    /**
     * Retrieve user's salt from database.
     *
     * @param string $username This will be used to find the user record.
     *
     * @return string Returns the salt db value.
     */
    public function get_salt($username)
    {
        $user = $this->db->get_where('user_settings', ['username' => $username])->row_array();
        return ($user) ? $user['salt'] : '';
    }

    /**
     * Get the given user's display name (first + last name).
     *
     * @param int $user_id The given user record id.
     *
     * @return string Returns the user display name.
     *
     * @throws Exception If $user_id argument is invalid.
     */
    public function get_user_display_name($user_id)
    {
        if ( ! is_numeric($user_id))
        {
            throw new Exception ('Invalid argument given: ' . $user_id);
        }

        $user = $this->db->get_where('users', ['id' => $user_id])->row_array();

        return $user['first_name'] . ' ' . $user['last_name'];
    }

    /**
     * If the given arguments correspond to an existing user record, generate a new
     * password and send it with an email.
     *
     * @param string $username User's username.
     * @param string $email User's email.
     *
     * @return string|bool Returns the new password on success or FALSE on failure.
     */
    public function regenerate_password($username, $email)
    {
        $result = $this->db
            ->select('users.id')
            ->from('users')
            ->join('user_settings', 'user_settings.id_users = users.id', 'inner')
            ->where('users.email', $email)
            ->where('user_settings.username', $username)
            ->get();

        if ($result->num_rows() == 0)
        {
            return FALSE;
        }

        $user_id = $result->row()->id;

        // Create a new password and send it with an email to the given email address.
        $new_password = random_string('alnum', 12);
        $salt = $this->db->get_where('user_settings', ['id_users' => $user_id])->row()->salt;
        $hash_password = hash_password($salt, $new_password);
        $this->db->update('user_settings', ['password' => $hash_password], ['id_users' => $user_id]);

        return $new_password;
    }

    /**
     * Get the timezone of a user.
     *
     * @param int $id Database ID of the user.
     *
     * @return string|null
     */
    public function get_user_timezone($id)
    {
        $row = $this->db->get_where('users', ['id' => $id])->row_array();

        return $row ? $row['timezone'] : NULL;
    }
}
