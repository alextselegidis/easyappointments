/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

App.Http.Admins = (function () {
    /**
     * Create an admin.
     *
     * @param {Object} admin
     *
     * @return {Object}
     */
    function create(admin) {
        const url = App.Utils.Url.siteUrl('admins/create');

        const data = {
            csrf_token: App.Vars.csrf_token,
            admin: admin
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
            csrf_token: App.Vars.csrf_token,
            admin: admin
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
            csrf_token: App.Vars.csrf_token,
            admin_id: adminId
        };

        return $.post(url, data);
    }

    /**
     * Search admins by keyword.
     *
     * @param {String} keyword
     * @param {Number} limit
     * @param {Number} offset
     * @param {String} orderBy
     *
     * @return {Object}
     */
    function search(keyword, limit, offset, orderBy) {
        const url = App.Utils.Url.siteUrl('admins/search');

        const data = {
            csrf_token: App.Vars.csrf_token,
            keyword,
            limit,
            offset,
            order_by: orderBy
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
            csrf_token: App.Vars.csrf_token,
            admin_id: adminId
        };

        return $.post(url, data);
    }

    return {
        create,
        update,
        destroy,
        search,
        find
    };
})();
