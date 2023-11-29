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
    function can(string $action, string $resource, int $user_id = null): bool
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
    function cannot(string $action, string $resource, int $user_id = null): bool
    {
        return !can($action, $resource, $user_id);
    }
}
