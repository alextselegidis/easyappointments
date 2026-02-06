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
 * Permissions library.
 *
 * Handles permission related functionality.
 *
 * @package Libraries
 */
class Permissions
{
    /**
     * @var EA_Controller|CI_Controller
     */
    protected EA_Controller|CI_Controller $CI;

    /**
     * Permissions constructor.
     */
    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('appointments_model');
        $this->CI->load->model('roles_model');
        $this->CI->load->model('secretaries_model');
        $this->CI->load->model('users_model');

        $this->CI->load->library('timezones');
    }

    /**
     * Check if a user is allowed to manage the provided customer.
     *
     * The "limit_customer_access" setting changes the access permissions to customer entries. In order for a provider
     * or a secretary to be able to make changes to a customer, they will first need to at least have a single
     * appointment with them.
     *
     * @param int $user_id
     * @param int $customer_id
     *
     * @return bool
     */
    public function has_customer_access(int $user_id, int $customer_id): bool
    {
        $role_id = $this->CI->users_model->value($user_id, 'id_roles');

        $role_slug = $this->CI->roles_model->value($role_id, 'slug');

        $limit_customer_access = setting('limit_customer_access');

        if ($role_slug === DB_SLUG_ADMIN || !$limit_customer_access) {
            return true;
        }

        if ($role_slug === DB_SLUG_PROVIDER) {
            return $this->CI->appointments_model
                ->query()
                ->where(['id_users_provider' => $user_id, 'id_users_customer' => $customer_id])
                ->get()
                ->num_rows() > 0;
        }

        if ($role_slug === DB_SLUG_SECRETARY) {
            $secretary = $this->CI->secretaries_model->find($user_id);

            foreach ($secretary['providers'] as $secretary_provider_id) {
                $has_appointments_with_customer =
                    $this->CI->appointments_model
                        ->query()
                        ->where(['id_users_provider' => $secretary_provider_id, 'id_users_customer' => $customer_id])
                        ->get()
                        ->num_rows() > 0;

                if ($has_appointments_with_customer) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }
}
