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
 * Custom fields HTTP client.
 *
 * This module implements the custom fields related HTTP requests.
 */
App.Http.CustomFields = (function () {
    /**
     * Save (create or update) a custom field.
     *
     * @param {Object} customField
     *
     * @return {Object}
     */
    function save(customField) {
        return customField.id ? update(customField) : create(customField);
    }

    /**
     * Create a custom field.
     *
     * @param {Object} customField
     *
     * @return {Object}
     */
    function create(customField) {
        const url = App.Utils.Url.siteUrl('custom_fields/save');

        const data = {
            csrf_token: vars('csrf_token'),
            custom_field: customField,
        };

        return $.post(url, data);
    }

    /**
     * Update a custom field.
     *
     * @param {Object} customField
     *
     * @return {Object}
     */
    function update(customField) {
        const url = App.Utils.Url.siteUrl('custom_fields/save');

        const data = {
            csrf_token: vars('csrf_token'),
            custom_field: customField,
        };

        return $.post(url, data);
    }

    /**
     * Delete a custom field.
     *
     * @param {Number} customFieldId
     *
     * @return {Object}
     */
    function destroy(customFieldId) {
        const url = App.Utils.Url.siteUrl('custom_fields/delete');

        const data = {
            csrf_token: vars('csrf_token'),
            custom_field_id: customFieldId,
        };

        return $.post(url, data);
    }

    /**
     * Search custom fields.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('custom_fields/search');

        const data = {
            csrf_token: vars('csrf_token'),
            keyword,
            limit,
            offset,
            order_by: orderBy,
        };

        return $.post(url, data);
    }

    /**
     * Find a custom field.
     *
     * @param {Number} customFieldId
     *
     * @return {Object}
     */
    function find(customFieldId) {
        const url = App.Utils.Url.siteUrl('custom_fields/find');

        const data = {
            csrf_token: vars('csrf_token'),
            custom_field_id: customFieldId,
        };

        return $.post(url, data);
    }

    /**
     * Get options for a custom field.
     *
     * @param {Number} customFieldId
     *
     * @return {Object}
     */
    function getOptions(customFieldId) {
        const url = App.Utils.Url.siteUrl('custom_fields/get_options');

        const data = {
            csrf_token: vars('csrf_token'),
            custom_field_id: customFieldId,
        };

        return $.post(url, data);
    }

    /**
     * Save an option.
     *
     * @param {Object} option
     *
     * @return {Object}
     */
    function saveOption(option) {
        const url = App.Utils.Url.siteUrl('custom_fields/save_option');

        const data = {
            csrf_token: vars('csrf_token'),
            option: option,
        };

        return $.post(url, data);
    }

    /**
     * Save multiple options in batch (single request).
     *
     * @param {Number} customFieldId
     * @param {Array} options
     *
     * @return {Object}
     */
    function saveOptionsBatch(customFieldId, options) {
        const url = App.Utils.Url.siteUrl('custom_fields/save_options_batch');

        const data = {
            csrf_token: vars('csrf_token'),
            custom_field_id: customFieldId,
            options: options,
        };

        return $.post(url, data);
    }

    /**
     * Delete an option.
     *
     * @param {Number} optionId
     *
     * @return {Object}
     */
    function deleteOption(optionId) {
        const url = App.Utils.Url.siteUrl('custom_fields/delete_option');

        const data = {
            csrf_token: vars('csrf_token'),
            option_id: optionId,
        };

        return $.post(url, data);
    }

    return {
        save,
        create,
        update,
        delete: destroy,
        search,
        find,
        getOptions,
        saveOption,
        saveOptionsBatch,
        deleteOption,
    };
})();
