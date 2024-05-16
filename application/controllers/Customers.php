<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Customers controller.
 *
 * Handles the customers related operations.
 *
 * @package Controllers
 */
class Customers extends EA_Controller
{
    public array $allowed_customer_fields = [
        'id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'notes',
        'timezone',
        'language',
        'custom_field_1',
        'custom_field_2',
        'custom_field_3',
        'custom_field_4',
        'custom_field_5',
        'ldap_dn',
    ];

    /**
     * Customers constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('appointments_model');
        $this->load->model('customers_model');
        $this->load->model('secretaries_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('permissions');
        $this->load->library('timezones');
        $this->load->library('webhooks_client');
    }

    /**
     * Render the backend customers page.
     *
     * On this page admin users will be able to manage customers, which are eventually selected by customers during the
     * booking process.
     */
    public function index(): void
    {
        session(['dest_url' => site_url('customers')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_CUSTOMERS)) {
            if ($user_id) {
                abort(403, 'Forbidden');
            }

            redirect('login');

            return;
        }

        $role_slug = session('role_slug');

        $date_format = setting('date_format');
        $time_format = setting('time_format');
        $require_first_name = setting('require_first_name');
        $require_last_name = setting('require_last_name');
        $require_email = setting('require_email');
        $require_phone_number = setting('require_phone_number');
        $require_address = setting('require_address');
        $require_city = setting('require_city');
        $require_zip_code = setting('require_zip_code');

        $secretary_providers = [];

        if ($role_slug === DB_SLUG_SECRETARY) {
            $secretary = $this->secretaries_model->find($user_id);

            $secretary_providers = $secretary['providers'];
        }

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
            'date_format' => $date_format,
            'time_format' => $time_format,
            'timezones' => $this->timezones->to_array(),
            'secretary_providers' => $secretary_providers,
            'default_language' => setting('default_language'),
            'default_timezone' => setting('default_timezone'),
        ]);

        html_vars([
            'page_title' => lang('customers'),
            'active_menu' => PRIV_CUSTOMERS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'grouped_timezones' => $this->timezones->to_grouped_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
            'require_first_name' => $require_first_name,
            'require_last_name' => $require_last_name,
            'require_email' => $require_email,
            'require_phone_number' => $require_phone_number,
            'require_address' => $require_address,
            'require_city' => $require_city,
            'require_zip_code' => $require_zip_code,
            'available_languages' => config('available_languages'),
        ]);

        $this->load->view('pages/customers');
    }

    /**
     * Find a customer.
     */
    public function find(): void
    {
        try {
            if (cannot('view', PRIV_CUSTOMERS)) {
                abort(403, 'Forbidden');
            }

            $user_id = session('user_id');

            $customer_id = request('customer_id');

            if (!$this->permissions->has_customer_access($user_id, $customer_id)) {
                abort(403, 'Forbidden');
            }

            $customer = $this->customers_model->find($customer_id);

            json_response($customer);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Filter customers by the provided keyword.
     */
    public function search(): void
    {
        try {
            if (cannot('view', PRIV_CUSTOMERS)) {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = request('order_by', 'update_datetime DESC');

            $limit = request('limit', 1000);

            $offset = (int) request('offset', '0');

            $customers = $this->customers_model->search($keyword, $limit, $offset, $order_by);

            $user_id = session('user_id');

            foreach ($customers as $index => &$customer) {
                if (!$this->permissions->has_customer_access($user_id, $customer['id'])) {
                    unset($customers[$index]);

                    continue;
                }

                $appointments = $this->appointments_model->get(['id_users_customer' => $customer['id']]);

                foreach ($appointments as &$appointment) {
                    $this->appointments_model->load($appointment, ['service', 'provider']);
                }

                $customer['appointments'] = $appointments;
            }

            json_response(array_values($customers));
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new customer.
     */
    public function store(): void
    {
        try {
            if (cannot('add', PRIV_CUSTOMERS)) {
                abort(403, 'Forbidden');
            }

            if (session('role_slug') !== DB_SLUG_ADMIN && setting('limit_customer_visibility')) {
                abort(403);
            }

            $customer = request('customer');

            $this->customers_model->only($customer, $this->allowed_customer_fields);

            $customer_id = $this->customers_model->save($customer);

            $customer = $this->customers_model->find($customer_id);

            $this->webhooks_client->trigger(WEBHOOK_CUSTOMER_SAVE, $customer);

            json_response([
                'success' => true,
                'id' => $customer_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a customer.
     */
    public function update(): void
    {
        try {
            if (cannot('edit', PRIV_CUSTOMERS)) {
                abort(403, 'Forbidden');
            }

            $user_id = session('user_id');

            $customer = request('customer');

            if (!$this->permissions->has_customer_access($user_id, $customer['id'])) {
                abort(403, 'Forbidden');
            }

            $this->customers_model->only($customer, $this->allowed_customer_fields);

            $customer_id = $this->customers_model->save($customer);

            $customer = $this->customers_model->find($customer_id);

            $this->webhooks_client->trigger(WEBHOOK_CUSTOMER_SAVE, $customer);

            json_response([
                'success' => true,
                'id' => $customer_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Remove a customer.
     */
    public function destroy(): void
    {
        try {
            if (cannot('delete', PRIV_CUSTOMERS)) {
                abort(403, 'Forbidden');
            }

            $user_id = session('user_id');

            $customer_id = request('customer_id');

            if (!$this->permissions->has_customer_access($user_id, $customer_id)) {
                abort(403, 'Forbidden');
            }

            $customer = $this->customers_model->find($customer_id);

            $this->customers_model->delete($customer_id);

            $this->webhooks_client->trigger(WEBHOOK_CUSTOMER_DELETE, $customer);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
