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

window.App.Http.Services = (function () {
    /**
     * Create an service.
     *
     * @param {Object} service
     *
     * @return {jQuery.Deferred}
     */
    function create(service) {
        const url = App.Utils.Url.siteUrl('services/create');

        const data = {
            csrf_token: App.Vars.csrf_token,
            service: service
        };

        return $.post(url, data);
    }

    /**
     * Update an service.
     *
     * @param {Object} service
     *
     * @return {jQuery.Deferred}
     */
    function update(service) {
        const url = App.Utils.Url.siteUrl('services/update');

        const data = {
            csrf_token: App.Vars.csrf_token,
            service: service
        };

        return $.post(url, data);
    }

    /**
     * Delete an service.
     *
     * @param {Number} serviceId
     *
     * @return {jQuery.Deferred}
     */
    function destroy(serviceId) {
        const url = App.Utils.Url.siteUrl('services/destroy');

        const data = {
            csrf_token: App.Vars.csrf_token,
            service_id: serviceId
        };

        return $.post(url, data);
    }

    /**
     * Search services by keyword.
     *
     * @param {String} keyword
     * @param {Number} limit
     * @param {Number} offset
     * @param {String} orderBy
     *
     * @return {jQuery.Deferred}
     */
    function search(keyword, limit, offset, orderBy) {
        const url = App.Utils.Url.siteUrl('services/search');

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
     * Find an service.
     *
     * @param {Number} serviceId
     *
     * @return {jQuery.Deferred}
     */
    function find(serviceId) {
        const url = App.Utils.Url.siteUrl('services/find');

        const data = {
            csrf_token: App.Vars.csrf_token,
            service_id: serviceId
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
