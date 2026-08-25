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

if (!function_exists('can')) {
    /**
     * Check if the currently logged-in user can perform an action
     *
     * Example:
     *
     * if (can('edit', 'appointments') === FALSE) abort(403);
     *
     * @param string $action
     * @param string $resource
     * @param int|null $user_id
     *
     * @return bool
     */
    function can(string $action, string $resource, ?int $user_id = null): bool
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $CI->load->model('roles_model');
        $CI->load->model('users_model');

        if (empty($user_id)) {
            $role_slug = session('role_slug');
        } else {
            $user = $CI->users_model->find($user_id);

            $role_slug = $CI->roles_model->value($user['id_roles'], 'slug');
        }

        if (empty($role_slug)) {
            return false;
        }

        $permissions = $CI->roles_model->get_permissions_by_slug($role_slug);

        return $permissions[$resource][$action] ?? false;
    }
}

if (!function_exists('cannot')) {
    /**
     * Check if the currently logged-in user can perform an action
     *
     * Example:
     *
     * if (cannot('edit', 'appointments')) abort(403);
     *
     * @param string $action
     * @param string $resource
     * @param int|null $user_id
     *
     * @return bool
     */
    function cannot(string $action, string $resource, ?int $user_id = null): bool
    {
        return !can($action, $resource, $user_id);
    }
}

if (!function_exists('can_manage_provider')) {
    /**
     * Check whether the currently logged-in user is allowed to manage the records of the provided provider.
     *
     * Providers may only manage their own records and secretaries only the records of their assigned providers. Every
     * other role is not bound to a provider scope.
     *
     * @param int $provider_id Provider ID.
     *
     * @return bool
     */
    function can_manage_provider(int $provider_id): bool
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $user_id = (int) session('user_id');

        $role_slug = session('role_slug');

        if ($role_slug === DB_SLUG_PROVIDER) {
            return $user_id === $provider_id;
        }

        if ($role_slug === DB_SLUG_SECRETARY) {
            $CI->load->model('secretaries_model');

            return $CI->secretaries_model->is_provider_supported($user_id, $provider_id);
        }

        return true;
    }
}

if (!function_exists('authorize_event_write')) {
    /**
     * Authorize both sides of the provider boundary before an appointment or unavailability write.
     *
     * Checking only one side is not enough: authorizing just the stored record allows a rebind to a foreign provider,
     * while authorizing just the requested provider allows an existing foreign record to be overwritten.
     *
     * Example:
     *
     * authorize_event_write($appointment['id'] ?? null, $appointment['id_users_provider'] ?? null);
     *
     * @param mixed $record_id ID of the record that is about to be updated, empty for new records.
     * @param mixed $provider_id Provider ID that the record will be assigned to.
     */
    function authorize_event_write(mixed $record_id, mixed $provider_id): void
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        if (!empty($record_id)) {
            $record = $CI->db->get_where('appointments', ['id' => (int) $record_id])->row_array();

            // Do not disclose whether the record exists to users that are out of scope anyway.
            if (!$record || !can_manage_provider((int) $record['id_users_provider'])) {
                abort(403, 'Forbidden');
            }
        }

        if (!empty($provider_id) && !can_manage_provider((int) $provider_id)) {
            abort(403, 'Forbidden');
        }
    }
}
