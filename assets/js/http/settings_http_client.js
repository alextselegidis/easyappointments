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

window.App.Http.Settings = (function () {
    /**
     * Create an setting.
     *
     * @param {Object} setting
     *
     * @return {jQuery.Deferred}
     */
    function create(setting) {
        const url = App.Utils.Url.siteUrl('settings/create');

        const data = {
            csrf_token: App.Vars.csrf_token,
            setting: setting
        };

        return $.post(url, data);
    }

    /**
     * Update an setting.
     *
     * @param {Object} setting
     *
     * @return {jQuery.Deferred}
     */
    function update(setting) {
        const url = App.Utils.Url.siteUrl('settings/update');

        const data = {
            csrf_token: App.Vars.csrf_token,
            setting: setting
        };

        return $.post(url, data);
    }

    /**
     * Delete an setting.
     *
     * @param {Number} settingId
     *
     * @return {jQuery.Deferred}
     */
    function destroy(settingId) {
        const url = App.Utils.Url.siteUrl('settings/destroy');

        const data = {
            csrf_token: App.Vars.csrf_token,
            setting_id: settingId
        };

        return $.post(url, data);
    }

    /**
     * Search settings by keyword.
     *
     * @param {String} keyword
     * @param {Number} limit
     * @param {Number} offset
     * @param {String} orderBy
     *
     * @return {jQuery.Deferred}
     */
    function search(keyword, limit, offset, orderBy) {
        const url = App.Utils.Url.siteUrl('settings/search');

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
     * Find an setting.
     *
     * @param {Number} settingId
     *
     * @return {jQuery.Deferred}
     */
    function find(settingId) {
        const url = App.Utils.Url.siteUrl('settings/find');

        const data = {
            csrf_token: App.Vars.csrf_token,
            setting_id: settingId
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
