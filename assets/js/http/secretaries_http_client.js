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

App.Http.Secretaries = (function () {
    /**
     * Create an secretary.
     *
     * @param {Object} secretary
     *
     * @return {Object}
     */
    function create(secretary) {
        const url = App.Utils.Url.siteUrl('secretaries/create');

        const data = {
            csrf_token: App.Vars.csrf_token,
            secretary: secretary
        };

        return $.post(url, data);
    }

    /**
     * Update an secretary.
     *
     * @param {Object} secretary
     *
     * @return {Object}
     */
    function update(secretary) {
        const url = App.Utils.Url.siteUrl('secretaries/update');

        const data = {
            csrf_token: App.Vars.csrf_token,
            secretary: secretary
        };

        return $.post(url, data);
    }

    /**
     * Delete an secretary.
     *
     * @param {Number} secretaryId
     *
     * @return {Object}
     */
    function destroy(secretaryId) {
        const url = App.Utils.Url.siteUrl('secretaries/destroy');

        const data = {
            csrf_token: App.Vars.csrf_token,
            secretary_id: secretaryId
        };

        return $.post(url, data);
    }

    /**
     * Search secretaries by keyword.
     *
     * @param {String} keyword
     * @param {Number} limit
     * @param {Number} offset
     * @param {String} orderBy
     *
     * @return {Object}
     */
    function search(keyword, limit, offset, orderBy) {
        const url = App.Utils.Url.siteUrl('secretaries/search');

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
     * Find an secretary.
     *
     * @param {Number} secretaryId
     *
     * @return {Object}
     */
    function find(secretaryId) {
        const url = App.Utils.Url.siteUrl('secretaries/find');

        const data = {
            csrf_token: App.Vars.csrf_token,
            secretary_id: secretaryId
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
