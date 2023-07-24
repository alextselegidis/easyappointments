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
 * Categories controller.
 *
 * Handles the categories related operations.
 *
 * @package Controllers
 */
class Categories extends EA_Controller {
    /**
     * Categories constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('categories_model');
        $this->load->model('roles_model');

        $this->load->library('accounts');
        $this->load->library('timezones');
        $this->load->library('webhooks_client');
    }

    /**
     * Render the backend categories page.
     *
     * On this page admin users will be able to manage categories, which are eventually selected by customers during the
     * booking process.
     */
    public function index()
    {
        session(['dest_url' => site_url('categories')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_SERVICES))
        {
            if ($user_id)
            {
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
            'page_title' => lang('categories'),
            'active_menu' => PRIV_SERVICES,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/categories');
    }

    /**
     * Filter categories by the provided keyword.
     */
    public function search()
    {
        try
        {
            if (cannot('view', PRIV_SERVICES))
            {
                abort(403, 'Forbidden');
            }

            $keyword = request('keyword', '');

            $order_by = 'update_datetime DESC';

            $limit = request('limit', 1000);

            $offset = 0;

            $categories = $this->categories_model->search($keyword, $limit, $offset, $order_by);

            json_response($categories);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Create a category.
     */
    public function create()
    {
        try
        {
            if (cannot('add', PRIV_SERVICES))
            {
                abort(403, 'Forbidden');
            }

            $category = request('category');

            $this->categories_model->only($category, [
                'name',
                'description'
            ]);

            $category_id = $this->categories_model->save($category);

            $category = $this->categories_model->find($category_id);

            $this->webhooks_client->trigger(WEBHOOK_CATEGORY_SAVE, $category);

            json_response([
                'success' => TRUE,
                'id' => $category_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Update a category.
     */
    public function update()
    {
        try
        {
            if (cannot('edit', PRIV_SERVICES))
            {
                abort(403, 'Forbidden');
            }

            $category = request('category');

            $this->categories_model->only($category, [
                'id',
                'name',
                'description'
            ]);

            $category_id = $this->categories_model->save($category);

            $category = $this->categories_model->find($category_id);

            $this->webhooks_client->trigger(WEBHOOK_CATEGORY_SAVE, $category);

            json_response([
                'success' => TRUE,
                'id' => $category_id
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Remove a category.
     */
    public function destroy()
    {
        try
        {
            if (cannot('delete', PRIV_SERVICES))
            {
                abort(403, 'Forbidden');
            }

            $category_id = request('category_id');

            $category = $this->categories_model->find($category_id);

            $this->categories_model->delete($category_id);

            $this->webhooks_client->trigger(WEBHOOK_CATEGORY_DELETE, $category);

            json_response([
                'success' => TRUE,
            ]);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }

    /**
     * Find a category.
     */
    public function find()
    {
        try
        {
            if (cannot('view', PRIV_SERVICES))
            {
                abort(403, 'Forbidden');
            }

            $category_id = request('category_id');

            $category = $this->categories_model->find($category_id);

            json_response($category);
        }
        catch (Throwable $e)
        {
            json_exception($e);
        }
    }
}
