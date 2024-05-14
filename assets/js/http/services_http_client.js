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
 * Services HTTP client.
 *
 * This module implements the services related HTTP requests.
 */
App.Http.Services = (function () {
    /**
     * Save (create or update) a service.
     *
     * @param {Object} service
     *
     * @return {Object}
     */
    function save(service) {
        return service.id ? update(service) : store(service);
    }

    /**
     * Create an service.
     *
     * @param {Object} service
     *
     * @return {Object}
     */
    function store(service) {
        const url = App.Utils.Url.siteUrl('services/store');

        const data = {
            csrf_token: vars('csrf_token'),
            service: service,
        };

        return $.post(url, data);
    }

    /**
     * Update an service.
     *
     * @param {Object} service
     *
     * @return {Object}
     */
    function update(service) {
        const url = App.Utils.Url.siteUrl('services/update');

        const data = {
            csrf_token: vars('csrf_token'),
            service: service,
        };

        return $.post(url, data);
    }

    /**
     * Delete an service.
     *
     * @param {Number} serviceId
     *
     * @return {Object}
     */
    function destroy(serviceId) {
        const url = App.Utils.Url.siteUrl('services/destroy');

        const data = {
            csrf_token: vars('csrf_token'),
            service_id: serviceId,
        };

        return $.post(url, data);
    }

    /**
     * Search services by keyword.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('services/search');

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
     * Find an service.
     *
     * @param {Number} serviceId
     *
     * @return {Object}
     */
    function find(serviceId) {
        const url = App.Utils.Url.siteUrl('services/find');

        const data = {
            csrf_token: vars('csrf_token'),
            service_id: serviceId,
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
