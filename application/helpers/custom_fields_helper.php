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
 * Save custom field values for a user from request data.
 *
 * @param int $user_id User ID.
 * @param array $request_data Request data containing custom field values.
 */
function save_custom_field_values(int $user_id, array $request_data): void
{
    $CI = &get_instance();

    if (!isset($CI->custom_fields_model)) {
        $CI->load->model('custom_fields_model');
    }

    if (!isset($CI->custom_field_values_model)) {
        $CI->load->model('custom_field_values_model');
    }

    // Get all active custom fields
    $custom_fields = $CI->custom_fields_model->get(['active' => 1]);

    $field_values = [];

    foreach ($custom_fields as $field) {
        // Check if value exists in request data (by field ID)
        $value_key = 'custom_field_' . $field['id'];

        if (isset($request_data[$value_key])) {
            $field_values[$field['id']] = $request_data[$value_key];
        }
    }

    // Save all field values at once
    if (!empty($field_values)) {
        $CI->custom_field_values_model->save_for_user($user_id, $field_values);
    }
}

/**
 * Load custom field values for a user.
 *
 * @param int $user_id User ID.
 *
 * @return array Array of custom field values.
 */
function load_custom_field_values(int $user_id): array
{
    $CI = &get_instance();

    if (!isset($CI->custom_field_values_model)) {
        $CI->load->model('custom_field_values_model');
    }

    return $CI->custom_field_values_model->get_by_user($user_id);
}

/**
 * Get custom fields formatted for email templates.
 *
 * @param int $user_id User ID.
 *
 * @return string HTML formatted custom fields for email.
 */
function get_custom_fields_for_email(int $user_id): string
{
    $custom_fields_data = load_custom_field_values($user_id);

    if (empty($custom_fields_data)) {
        return '';
    }

    $html = '<h2>' . lang('custom_fields') . '</h2>';

    foreach ($custom_fields_data as $field_name => $field_data) {
        $html .= '<p>';
        $html .= '<strong>' . e($field_data['label']) . ':</strong> ';
        $html .= e($field_data['value']);
        $html .= '</p>';
    }

    return $html;
}

/**
 * Validate required custom fields.
 *
 * @param array $request_data Request data containing custom field values.
 *
 * @return array Array of validation errors (empty if valid).
 */
function validate_custom_fields(array $request_data): array
{
    $CI = &get_instance();

    if (!isset($CI->custom_fields_model)) {
        $CI->load->model('custom_fields_model');
    }

    $errors = [];

    // Get all required custom fields
    $required_fields = $CI->custom_fields_model->get(['active' => 1, 'required' => 1]);

    foreach ($required_fields as $field) {
        $value_key = 'custom_field_' . $field['id'];

        if (empty($request_data[$value_key])) {
            $errors[] = sprintf(lang('field_required'), $field['label']);
        }
    }

    return $errors;
}
