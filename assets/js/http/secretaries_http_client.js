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
 * Secretaries HTTP client.
 *
 * This module implements the secretaries related HTTP requests.
 */
App.Http.Secretaries = (function () {
    /**
     * Save (create or update) a secretary.
     *
     * @param {Object} secretary
     *
     * @return {Object}
     */
    function save(secretary) {
        return secretary.id ? update(secretary) : store(secretary);
    }

    /**
     * Create a secretary.
     *
     * @param {Object} secretary
     *
     * @return {Object}
     */
    function store(secretary) {
        const url = App.Utils.Url.siteUrl('secretaries/store');

        const data = {
            csrf_token: vars('csrf_token'),
            secretary: secretary,
        };

        return $.post(url, data);
    }

    /**
     * Update a secretary.
     *
     * @param {Object} secretary
     *
     * @return {Object}
     */
    function update(secretary) {
        const url = App.Utils.Url.siteUrl('secretaries/update');

        const data = {
            csrf_token: vars('csrf_token'),
            secretary: secretary,
        };

        return $.post(url, data);
    }

    /**
     * Delete a secretary.
     *
     * @param {Number} secretaryId
     *
     * @return {Object}
     */
    function destroy(secretaryId) {
        const url = App.Utils.Url.siteUrl('secretaries/destroy');

        const data = {
            csrf_token: vars('csrf_token'),
            secretary_id: secretaryId,
        };

        return $.post(url, data);
    }

    /**
     * Search secretaries by keyword.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('secretaries/search');

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
     * Find a secretary.
     *
     * @param {Number} secretaryId
     *
     * @return {Object}
     */
    function find(secretaryId) {
        const url = App.Utils.Url.siteUrl('secretaries/find');

        const data = {
            csrf_token: vars('csrf_token'),
            secretary_id: secretaryId,
        };

        return $.post(url, data);
    }

    return {
        save,
        store,
        update,
        destroy,
        search,
        find,
    };
})();
