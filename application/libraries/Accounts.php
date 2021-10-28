<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */


/**
 * Accounts library
 *
 * Handles account related functionality.
 * 
 * @package Libraries
 */
class Accounts {
    /**
     * @var EA_Controller
     */
    protected $CI;

    /**
     * Accounts constructor.
     */
    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('users_model');
        $this->CI->load->model('roles_model');

        $this->CI->load->library('timezones');
    }

    /**
     * Authenticate the provided credentials.
     *
     * @param string $username Username.
     * @param string $password Password (non-hashed).
     *
     * @return array|null Returns an associative array with the PHP session data or NULL on failure.
     */
    public function check_login(string $username, string $password): ?array
    {
        $salt = $this->get_salt_by_username($username);

        $password = hash_password($salt, $password);

        $user_settings = $this->CI->db->get_where('user_settings', [
            'username' => $username,
            'password' => $password
        ])->row_array();

        if (empty($user_settings))
        {
            return NULL;
        }

        $user = $this->CI->users_model->find($user_settings['id_users']);

        $role = $this->CI->roles_model->find($user['id_roles']);

        $default_timezone = $this->CI->timezones->get_default_timezone();

        return [
            'user_id' => $user['id'],
            'user_email' => $user['email'],
            'username' => $username,
            'timezone' => ! empty($user['timezone']) ? $user['timezone'] : $default_timezone,
            'role_slug' => $role['slug'],
        ];
    }

    /**
     * Get the user's salt value.
     *
     * @param string $username Username.
     *
     * @return string Returns the salt value.
     */
    public function get_salt_by_username(string $username): string
    {
        $user_settings = $this->CI->db->get_where('user_settings', ['username' => $username])->row_array();

        return $user_settings['salt'] ?? '';
    }

    /**
     * Get the user full name.
     *
     * @param int $user_id User ID.
     *
     * @return string Returns the user full name.
     */
    public function get_user_display_name(int $user_id): string
    {
        $user = $this->CI->users_model->find($user_id);

        return $user['first_name'] . ' ' . $user['last_name'];
    }

    /**
     * Regenerate the password of the user that matches the provided username and email.
     *
     * @param string $username Username.
     * @param string $email Email.
     *
     * @return string|bool Returns the new password on success or FALSE on failure.
     *
     * @throws RuntimeException
     */
    public function regenerate_password(string $username, string $email): string
    {
        $query = $this
            ->CI
            ->db
            ->select('users.id')
            ->from('users')
            ->join('user_settings', 'user_settings.id_users = users.id', 'inner')
            ->where('users.email', $email)
            ->where('user_settings.username', $username)
            ->get();

        if ( ! $query->num_rows())
        {
            throw new RuntimeException('The username was not found in the database: ' . $username);
        }

        $user = $query->row_array();

        // Generate a new password for the user. 
        $new_password = random_string('alnum', 12);

        $salt = $this->get_salt_by_username($username);

        $hash_password = hash_password($salt, $new_password);

        $this->CI->users_model->set_setting($user['id'], 'password', $hash_password);

        return $new_password;
    }
}
