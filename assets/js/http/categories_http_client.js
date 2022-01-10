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

App.Http.Categories = (function () {
    /**
     * Save (create or update) a category.
     *
     * @param {Object} category
     *
     * @return {Object}
     */
    function save(category) {
        return category.id ? update(category) : create(category);
    }

    /**
     * Create a category.
     *
     * @param {Object} category
     *
     * @return {Object}
     */
    function create(category) {
        const url = App.Utils.Url.siteUrl('categories/create');

        const data = {
            csrf_token: App.Vars.csrf_token,
            category: category
        };

        return $.post(url, data);
    }

    /**
     * Update a category.
     *
     * @param {Object} category
     *
     * @return {Object}
     */
    function update(category) {
        const url = App.Utils.Url.siteUrl('categories/update');

        const data = {
            csrf_token: App.Vars.csrf_token,
            category: category
        };

        return $.post(url, data);
    }

    /**
     * Delete a category.
     *
     * @param {Number} categoryId
     *
     * @return {Object}
     */
    function destroy(categoryId) {
        const url = App.Utils.Url.siteUrl('categories/destroy');

        const data = {
            csrf_token: App.Vars.csrf_token,
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
     * @return {Object}
     */
    function search(keyword, limit, offset, orderBy) {
        const url = App.Utils.Url.siteUrl('categories/search');

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
     * Find a category.
     *
     * @param {Number} categoryId
     *
     * @return {Object}
     */
    function find(categoryId) {
        const url = App.Utils.Url.siteUrl('categories/find');

        const data = {
            csrf_token: App.Vars.csrf_token,
            category_id: categoryId
        };

        return $.post(url, data);
    }

    return {
        save,
        create,
        update,
        destroy,
        search,
        find
    };
})();
