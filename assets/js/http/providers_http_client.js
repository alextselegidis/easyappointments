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

App.Http.Providers = (function () {
    /**
     * Create an provider.
     *
     * @param {Object} provider
     *
     * @return {Object}
     */
    function create(provider) {
        const url = App.Utils.Url.siteUrl('providers/create');

        const data = {
            csrf_token: App.Vars.csrf_token,
            provider: provider
        };

        return $.post(url, data);
    }

    /**
     * Update an provider.
     *
     * @param {Object} provider
     *
     * @return {Object}
     */
    function update(provider) {
        const url = App.Utils.Url.siteUrl('providers/update');

        const data = {
            csrf_token: App.Vars.csrf_token,
            provider: provider
        };

        return $.post(url, data);
    }

    /**
     * Delete an provider.
     *
     * @param {Number} providerId
     *
     * @return {Object}
     */
    function destroy(providerId) {
        const url = App.Utils.Url.siteUrl('providers/destroy');

        const data = {
            csrf_token: App.Vars.csrf_token,
            provider_id: providerId
        };

        return $.post(url, data);
    }

    /**
     * Search providers by keyword.
     *
     * @param {String} keyword
     * @param {Number} limit
     * @param {Number} offset
     * @param {String} orderBy
     *
     * @return {Object}
     */
    function search(keyword, limit, offset, orderBy) {
        const url = App.Utils.Url.siteUrl('providers/search');

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
     * Find an provider.
     *
     * @param {Number} providerId
     *
     * @return {Object}
     */
    function find(providerId) {
        const url = App.Utils.Url.siteUrl('providers/find');

        const data = {
            csrf_token: App.Vars.csrf_token,
            provider_id: providerId
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
