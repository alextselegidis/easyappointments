/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

window.App.Http.Customers = (function () {
    /**
     * Create an customer.
     *
     * @param {Object} customer
     *
     * @return {jQuery.Deferred}
     */
    function create(customer) {
        const url = App.Utils.Url.siteUrl('customers/create');

        const data = {
            csrf_token: App.Config.csrf_token,
            customer: customer
        };

        return $.post(url, data);
    }

    /**
     * Update an customer.
     *
     * @param {Object} customer
     *
     * @return {jQuery.Deferred}
     */
    function update(customer) {
        const url = App.Utils.Url.siteUrl('customers/update');

        const data = {
            csrf_token: App.Config.csrf_token,
            customer: customer
        };

        return $.post(url, data);
    }

    /**
     * Delete an customer.
     *
     * @param {Number} customerId
     *
     * @return {jQuery.Deferred}
     */
    function destroy(customerId) {
        const url = App.Utils.Url.siteUrl('customers/destroy');

        const data = {
            csrf_token: App.Config.csrf_token,
            customer_id: customerId
        };

        return $.post(url, data);
    }

    /**
     * Search customers by keyword.
     *
     * @param {String} keyword
     * @param {Number} limit
     * @param {Number} offset
     * @param {String} orderBy
     *
     * @return {jQuery.Deferred}
     */
    function search(keyword, limit, offset, orderBy) {
        const url = App.Utils.Url.siteUrl('customers/search');

        const data = {
            csrf_token: App.Config.csrf_token,
            keyword,
            limit,
            offset,
            order_by: orderBy
        };

        return $.post(url, data);
    }

    /**
     * Find an customer.
     *
     * @param {Number} customerId
     *
     * @return {jQuery.Deferred}
     */
    function find(customerId) {
        const url = App.Utils.Url.siteUrl('customers/find');

        const data = {
            csrf_token: App.Config.csrf_token,
            customer_id: customerId
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
