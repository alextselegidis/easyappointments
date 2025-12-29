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
 * Custom Fields HTTP client.
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
        const url = App.Utils.Url.siteUrl('custom_fields/save');

        const data = {
            csrf_token: vars('csrf_token'),
            custom_field: customField
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
            custom_field_id: customFieldId
        };

        return $.post(url, data);
    }

    /**
     * Search custom fields by keyword.
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
            order_by: orderBy
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
            custom_field_id: customFieldId
        };

        return $.post(url, data);
    }

    /**
     * Update sort order of custom fields.
     *
     * @param {Array} fields
     *
     * @return {Object}
     */
    function updateSortOrder(fields) {
        const url = App.Utils.Url.siteUrl('custom_fields/update_sort_order');

        const data = {
            csrf_token: vars('csrf_token'),
            fields: fields
        };

        return $.post(url, data);
    }

    /**
     * Save (create or update) a custom field option.
     *
     * @param {Object} option
     *
     * @return {Object}
     */
    function saveOption(option) {
        const url = App.Utils.Url.siteUrl('custom_fields/save_option');

        const data = {
            csrf_token: vars('csrf_token'),
            option: option
        };

        return $.post(url, data);
    }

    /**
     * Delete a custom field option.
     *
     * @param {Number} optionId
     *
     * @return {Object}
     */
    function destroyOption(optionId) {
        const url = App.Utils.Url.siteUrl('custom_fields/delete_option');

        const data = {
            csrf_token: vars('csrf_token'),
            option_id: optionId
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
            custom_field_id: customFieldId
        };

        return $.post(url, data);
    }

    /**
     * Update sort order of options.
     *
     * @param {Array} options
     *
     * @return {Object}
     */
    function updateOptionsSortOrder(options) {
        const url = App.Utils.Url.siteUrl('custom_fields/update_options_sort_order');

        const data = {
            csrf_token: vars('csrf_token'),
            options: options
        };

        return $.post(url, data);
    }

    return {
        save,
        destroy,
        search,
        find,
        updateSortOrder,
        saveOption,
        destroyOption,
        getOptions,
        updateOptionsSortOrder
    };
})();
