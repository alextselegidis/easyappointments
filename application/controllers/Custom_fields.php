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
 * Custom fields controller.
 *
 * Handles the custom fields related operations.
 *
 * @package Controllers
 */
class Custom_fields extends EA_Controller
{
    /**
     * Custom_fields constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('custom_fields_model');
        $this->load->model('custom_field_options_model');
        $this->load->model('custom_field_values_model');

        $this->load->library('accounts');
    }

    /**
     * Render the backend custom fields page.
     */
    public function index(): void
    {
        session(['dest_url' => site_url('custom_fields')]);

        $user_id = session('user_id');

        if (cannot('view', PRIV_SYSTEM_SETTINGS)) {
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
            'custom_fields' => $this->custom_fields_model->get(),
        ]);

        html_vars([
            'page_title' => lang('custom_fields'),
            'active_menu' => PRIV_SYSTEM_SETTINGS,
            'user_display_name' => $this->accounts->get_user_display_name($user_id),
        ]);

        $this->load->view('pages/custom_fields');
    }

    /**
     * Save (insert or update) a custom field.
     */
    public function save(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $custom_field = request('custom_field');

            $custom_field_id = $this->custom_fields_model->save($custom_field);

            $custom_field = $this->custom_fields_model->find($custom_field_id, true);

            json_response($custom_field);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Delete a custom field.
     */
    public function delete(): void
    {
        try {
            if (cannot('delete', PRIV_SYSTEM_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $custom_field_id = request('custom_field_id');

            $this->custom_fields_model->delete($custom_field_id);

            response();
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Find a custom field.
     */
    public function find(): void
    {
        try {
            if (cannot('view', PRIV_SYSTEM_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $custom_field_id = request('custom_field_id');

            $custom_field = $this->custom_fields_model->find($custom_field_id, true);

            json_response($custom_field);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Search custom fields.
     */
    public function search(): void
    {
        try {
            if (cannot('view', PRIV_SYSTEM_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $keyword = request('keyword');

            $limit = request('limit', null);
            $limit = $limit !== null ? (int)$limit : null;

            $offset = request('offset', null);
            $offset = $offset !== null ? (int)$offset : null;

            $order_by = request('order_by');

            $custom_fields = empty($keyword)
                ? $this->custom_fields_model->get(null, $limit, $offset, $order_by)
                : $this->custom_fields_model->search($keyword, $limit, $offset, $order_by);

            json_response($custom_fields);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update sort order of custom fields.
     */
    public function update_sort_order(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $fields = request('fields');

            $this->custom_fields_model->update_sort_order($fields);

            response();
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Save (insert or update) a custom field option.
     */
    public function save_option(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $option = request('option');

            $option_id = $this->custom_field_options_model->save($option);

            $option = $this->custom_field_options_model->find($option_id);

            json_response($option);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Delete a custom field option.
     */
    public function delete_option(): void
    {
        try {
            if (cannot('delete', PRIV_SYSTEM_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $option_id = request('option_id');

            $this->custom_field_options_model->delete($option_id);

            response();
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Get options for a custom field.
     */
    public function get_options(): void
    {
        try {
            if (cannot('view', PRIV_SYSTEM_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $custom_field_id = request('custom_field_id');

            $options = $this->custom_field_options_model->get_by_field($custom_field_id);

            json_response($options);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    /**
     * Update sort order of options.
     */
    public function update_options_sort_order(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                throw new RuntimeException('You do not have the required permissions for this task.');
            }

            $options = request('options');

            $this->custom_field_options_model->update_sort_order($options);

            response();
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
