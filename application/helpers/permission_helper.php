<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

if ( ! function_exists('can'))
{
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
    function can(string $action, string $resource, int $user_id = NULL): bool
    {
        /** @var EA_Controller $CI */
        $CI = &get_instance();

        $CI->load->model('roles_model');
        $CI->load->model('user_model');

        if (empty($user_id))
        {
            $role_slug = session('role_slug');
        }
        else
        {
            $user = $CI->user_model->get_user($user_id);
            $role_slug = $CI->roles_model->value('slug', $user['id_roles']);
        }

        if (empty($role_slug))
        {
            return FALSE;
        }

        $permissions = $CI->roles_model->get_privileges($role_slug);

        return $permissions[$resource][$action];
    }
}

if ( ! function_exists('cannot'))
{
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
    function cannot(string $action, string $resource, int $user_id = NULL): bool
    {
        return ! can($action, $resource, $user_id);
    }
}
