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
 * Service-categories controller.
 *
 * Handles the service-categories related operations.
 *
 * @package Controllers
 */
class Service_categories extends EA_Controller
{
    public array $allowed_service_category_fields = ['id', 'name', 'description'];

    /**
     * Service-categories constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('service_categories_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
        $this->load->library('webhooks_client');
    }

    /**
     * Render the backend service-categories page.
     *
     * On this page admin users will be able to manage service-categories, which are eventually selected by customers during the
     * booking process.
     */
    public function index(): void
    {
        session(['dest_url' => site_url('service_categories')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_SERVICES)) {
            if ($user_id) {
                abort(403, 'Forbidden');
            }

            redirect('login');

            return;
        }

        $role_slug = session('role_slug');

        script_vars([
            'user_id' => $user_id,
            'role_slug' => $role_slug,
        ]);

        html_vars([
            'page_title' => lang('service_categories'),
            'active_menu' => PRIV_SERVICES,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/service_categories');
    }

    /**
     * Filter service-categories by the provided keyword.
     */
    public function search(): void
    {
        try {
            if (cannot('view', PRIV_SERVICES)) {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = request('order_by', 'update_datetime DESC');

            $limit = request('limit', 1000);

            $offset = (int) request('offset', '0');

            $service_categories = $this->service_categories_model->search($keyword, $limit, $offset, $order_by);

            json_response($service_categories);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Store a new service-category.
     */
    public function store(): void
    {
        try {
            if (cannot('add', PRIV_SERVICES)) {
                abort(403, 'Forbidden');
            }

            $service_category = request('service_category');

            $this->service_categories_model->only($service_category, $this->allowed_service_category_fields);

            $service_category_id = $this->service_categories_model->save($service_category);

            $service_category = $this->service_categories_model->find($service_category_id);

            $this->webhooks_client->trigger(WEBHOOK_SERVICE_CATEGORY_SAVE, $service_category);

            json_response([
                'success' => true,
                'id' => $service_category_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Find a service-category.
     */
    public function find(): void
    {
        try {
            if (cannot('view', PRIV_SERVICES)) {
                abort(403, 'Forbidden');
            }

            $service_category_id = request('service_category_id');

            $service_category = $this->service_categories_model->find($service_category_id);

            json_response($service_category);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update a service-category.
     */
    public function update(): void
    {
        try {
            if (cannot('edit', PRIV_SERVICES)) {
                abort(403, 'Forbidden');
            }

            $service_category = request('service_category');

            $this->service_categories_model->only($service_category, $this->allowed_service_category_fields);

            $service_category_id = $this->service_categories_model->save($service_category);

            $service_category = $this->service_categories_model->find($service_category_id);

            $this->webhooks_client->trigger(WEBHOOK_SERVICE_CATEGORY_SAVE, $service_category);

            json_response([
                'success' => true,
                'id' => $service_category_id,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Remove a service-category.
     */
    public function destroy(): void
    {
        try {
            if (cannot('delete', PRIV_SERVICES)) {
                abort(403, 'Forbidden');
            }

            $service_category_id = request('service_category_id');

            $service_category = $this->service_categories_model->find($service_category_id);

            $this->service_categories_model->delete($service_category_id);

            $this->webhooks_client->trigger(WEBHOOK_SERVICE_CATEGORY_DELETE, $service_category);

            json_response([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
