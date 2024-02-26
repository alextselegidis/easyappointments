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
 * Customers HTTP client.
 *
 * This module implements the customers related HTTP requests.
 */
App.Http.Customers = (function () {
    /**
     * Save (create or update) a customer.
     *
     * @param {Object} customer
     *
     * @return {Object}
     */
    function save(customer) {
        return customer.id ? update(customer) : store(customer);
    }

    /**
     * Create a customer.
     *
     * @param {Object} customer
     *
     * @return {Object}
     */
    function store(customer) {
        const url = App.Utils.Url.siteUrl('customers/store');

        const data = {
            csrf_token: vars('csrf_token'),
            customer: customer,
        };

        return $.post(url, data);
    }

    /**
     * Update a customer.
     *
     * @param {Object} customer
     *
     * @return {Object}
     */
    function update(customer) {
        const url = App.Utils.Url.siteUrl('customers/update');

        const data = {
            csrf_token: vars('csrf_token'),
            customer: customer,
        };

        return $.post(url, data);
    }

    /**
     * Delete a customer.
     *
     * @param {Number} customerId
     *
     * @return {Object}
     */
    function destroy(customerId) {
        const url = App.Utils.Url.siteUrl('customers/destroy');

        const data = {
            csrf_token: vars('csrf_token'),
            customer_id: customerId,
        };

        return $.post(url, data);
    }

    /**
     * Search customers by keyword.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('customers/search');

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
     * Find a customer.
     *
     * @param {Number} customerId
     *
     * @return {Object}
     */
    function find(customerId) {
        const url = App.Utils.Url.siteUrl('customers/find');

        const data = {
            csrf_token: vars('csrf_token'),
            customer_id: customerId,
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
