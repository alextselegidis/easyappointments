<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Ldap_client library.
 *
 * Handles LDAP  related functionality.
 *
 * @package Libraries
 */
class Ldap_client
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * Ldap_client constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('roles_model');

        $this->CI->load->library('timezones');
        $this->CI->load->library('accounts');
    }

    /**
     * Try authenticating the user with LDAP
     *
     * @param string $username
     * @param string $password
     *
     * @return array|null
     *
     * @throws Exception
     */
    public function check_login(string $username, string $password): ?array
    {
        if (!extension_loaded('ldap')) {
            return null;
        }

        if (empty($username)) {
            throw new InvalidArgumentException('No username value provided.');
        }

        $ldap_is_active = setting('ldap_is_active');

        if (!$ldap_is_active) {
            return null;
        }

        // Match user by username

        $user = $this->CI->accounts->get_user_by_username($username);

        if (empty($user['ldap_dn'])) {
            return null; // User does not exist in Easy!Appointments
        }

        // Connect to LDAP server

        $ldap_host = setting('ldap_host');
        $ldap_port = (int) setting('ldap_port');

        $connection = @ldap_connect($ldap_host, $ldap_port);
        @ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        $user_bind = @ldap_bind($connection, $user['ldap_dn'], $password);

        if ($user_bind) {
            $role = $this->CI->roles_model->find($user['id_roles']);

            $default_timezone = $this->CI->timezones->get_default_timezone();

            return [
                'user_id' => $user['id'],
                'user_email' => $user['email'],
                'username' => $username,
                'timezone' => !empty($user['timezone']) ? $user['timezone'] : $default_timezone,
                'language' => !empty($user['language']) ? $user['language'] : Config::LANGUAGE,
                'role_slug' => $role['slug'],
            ];
        }

        return null;
    }

    /**
     * Search the LDAP server based on the provided keyword and configuration.
     *
     * @param string $keyword
     *
     * @return array
     *
     * @throws Exception
     */
    public function search(string $keyword): array
    {
        $this->check_environment();

        $host = setting('ldap_host');
        $port = (int) setting('ldap_port');
        $user_dn = setting('ldap_user_dn');
        $password = setting('ldap_password');
        $base_dn = setting('ldap_base_dn');
        $filter = setting('ldap_filter');

        $connection = @ldap_connect($host, $port);

        if (!$connection) {
            throw new Exception('Could not connect to LDAP server: ' . @ldap_error($connection));
        }

        @ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        @ldap_set_option($connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.

        $bind = @ldap_bind($connection, $user_dn, $password);

        if (!$bind) {
            throw new Exception('LDAP bind failed: ' . @ldap_error($connection));
        }

        $wildcard_keyword = !empty($keyword) ? '*' . $keyword . '*' : '*';

        $interpolated_filter = str_replace('{{KEYWORD}}', $wildcard_keyword, $filter);

        $result = @ldap_search($connection, $base_dn, $interpolated_filter);

        if (!$result) {
            throw new Exception('Search failed: ' . @ldap_error($connection));
        }

        $ldap_entries = @ldap_get_entries($connection, $result);

        // Flatten the LDAP entries so that they become easier to import

        $entries = [];

        foreach ($ldap_entries as $ldap_entry) {
            if (!is_array($ldap_entry)) {
                continue;
            }

            $entry = [
                'dn' => $ldap_entry['dn'],
            ];

            foreach ($ldap_entry as $key => $value) {
                if (!is_array($value) || !in_array($key, LDAP_WHITELISTED_ATTRIBUTES)) {
                    continue;
                }

                $entry[$key] = $value[0] ?? null;
            }

            $entries[] = $entry;
        }

        return $entries;
    }

    /**
     * Check if the ldap extension is installed
     *
     * @return void
     */
    private function check_environment(): void
    {
        if (!extension_loaded('ldap')) {
            throw new RuntimeException('The LDAP extension is not loaded.');
        }
    }
}
