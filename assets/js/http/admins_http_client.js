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
 * Admins HTTP client.
 *
 * This module implements the admins related HTTP requests.
 */
App.Http.Admins = (function () {
    /**
     * Save (create or update) a admin.
     *
     * @param {Object} admin
     *
     * @return {Object}
     */
    function save(admin) {
        return admin.id ? update(admin) : store(admin);
    }

    /**
     * Create an admin.
     *
     * @param {Object} admin
     *
     * @return {Object}
     */
    function store(admin) {
        const url = App.Utils.Url.siteUrl('admins/store');

        const data = {
            csrf_token: vars('csrf_token'),
            admin: admin,
        };

        return $.post(url, data);
    }

    /**
     * Update an admin.
     *
     * @param {Object} admin
     *
     * @return {Object}
     */
    function update(admin) {
        const url = App.Utils.Url.siteUrl('admins/update');

        const data = {
            csrf_token: vars('csrf_token'),
            admin: admin,
        };

        return $.post(url, data);
    }

    /**
     * Delete an admin.
     *
     * @param {Number} adminId
     *
     * @return {Object}
     */
    function destroy(adminId) {
        const url = App.Utils.Url.siteUrl('admins/destroy');

        const data = {
            csrf_token: vars('csrf_token'),
            admin_id: adminId,
        };

        return $.post(url, data);
    }

    /**
     * Search admins by keyword.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('admins/search');

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
     * Find an admin.
     *
     * @param {Number} adminId
     *
     * @return {Object}
     */
    function find(adminId) {
        const url = App.Utils.Url.siteUrl('admins/find');

        const data = {
            csrf_token: vars('csrf_token'),
            admin_id: adminId,
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
