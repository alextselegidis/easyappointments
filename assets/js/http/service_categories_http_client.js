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
 * Service-categories HTTP client.
 *
 * This module implements the service-categories related HTTP requests.
 */
App.Http.ServiceCategories = (function () {
    /**
     * Save (create or update) a service-category.
     *
     * @param {Object} serviceCategory
     *
     * @return {Object}
     */
    function save(serviceCategory) {
        return serviceCategory.id ? update(serviceCategory) : store(serviceCategory);
    }

    /**
     * Create a service-category.
     *
     * @param {Object} serviceCategory
     *
     * @return {Object}
     */
    function store(serviceCategory) {
        const url = App.Utils.Url.siteUrl('service_categories/store');

        const data = {
            csrf_token: vars('csrf_token'),
            service_category: serviceCategory,
        };

        return $.post(url, data);
    }

    /**
     * Update a service-category.
     *
     * @param {Object} serviceCategory
     *
     * @return {Object}
     */
    function update(serviceCategory) {
        const url = App.Utils.Url.siteUrl('service_categories/update');

        const data = {
            csrf_token: vars('csrf_token'),
            service_category: serviceCategory,
        };

        return $.post(url, data);
    }

    /**
     * Delete a service-category.
     *
     * @param {Number} serviceCategoryId
     *
     * @return {Object}
     */
    function destroy(serviceCategoryId) {
        const url = App.Utils.Url.siteUrl('service_categories/destroy');

        const data = {
            csrf_token: vars('csrf_token'),
            service_category_id: serviceCategoryId,
        };

        return $.post(url, data);
    }

    /**
     * Search service-categories by keyword.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('service_categories/search');

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
     * Find a service-category.
     *
     * @param {Number} serviceCategoryId
     *
     * @return {Object}
     */
    function find(serviceCategoryId) {
        const url = App.Utils.Url.siteUrl('service_categories/find');

        const data = {
            csrf_token: vars('csrf_token'),
            service_category_id: serviceCategoryId,
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
