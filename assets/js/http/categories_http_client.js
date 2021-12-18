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

window.App.Http.Categories = (function () {
    /**
     * Create an category.
     *
     * @param {Object} category
     *
     * @return {jQuery.Deferred}
     */
    function create(category) {
        const url = App.Utils.Url.siteUrl('categories/create');

        const data = {
            csrf_token: App.Config.csrf_token,
            category: category
        };

        return $.post(url, data);
    }

    /**
     * Update an category.
     *
     * @param {Object} category
     *
     * @return {jQuery.Deferred}
     */
    function update(category) {
        const url = App.Utils.Url.siteUrl('categories/update');

        const data = {
            csrf_token: App.Config.csrf_token,
            category: category
        };

        return $.post(url, data);
    }

    /**
     * Delete an category.
     *
     * @param {Number} categoryId
     *
     * @return {jQuery.Deferred}
     */
    function destroy(categoryId) {
        const url = App.Utils.Url.siteUrl('categories/destroy');

        const data = {
            csrf_token: App.Config.csrf_token,
            category_id: categoryId
        };

        return $.post(url, data);
    }

    /**
     * Search categories by keyword.
     *
     * @param {String} keyword
     * @param {Number} limit
     * @param {Number} offset
     * @param {String} orderBy
     *
     * @return {jQuery.Deferred}
     */
    function search(keyword, limit, offset, orderBy) {
        const url = App.Utils.Url.siteUrl('categories/search');

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
     * Find an category.
     *
     * @param {Number} categoryId
     *
     * @return {jQuery.Deferred}
     */
    function find(categoryId) {
        const url = App.Utils.Url.siteUrl('categories/find');

        const data = {
            csrf_token: App.Config.csrf_token,
            category_id: categoryId
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
