<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
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

        if (cannot('view', PRIV_SERVICES))
        {
            abort(403, 'Forbidden');
        }

        $user_id = session('user_id');

        $role_slug = session('role_slug');

        html_vars([
            'page_title' => lang('categories'),
            'active_menu' => PRIV_SERVICES,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
            'timezones' => $this->timezones->to_array(),
            'privileges' => $this->roles_model->get_permissions_by_slug($role_slug),
        ]);

        $this->load->view('pages/categories', html_vars());
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

            $order_by = 'name ASC';

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
            $category = json_decode(request('category'), TRUE);

            if (cannot('add', PRIV_SERVICES))
            {
                abort(403, 'Forbidden');
            }

            $category_id = $this->categories_model->save($category);

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
            $category = json_decode(request('category'), TRUE);

            if (cannot('edit', PRIV_SERVICES))
            {
                abort(403, 'Forbidden');
            }

            $category_id = $this->categories_model->save($category);

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

            $this->categories_model->delete($category_id);

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
