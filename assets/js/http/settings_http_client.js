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
 * Settings HTTP client.
 *
 * This module implements the settings related HTTP requests.
 */
App.Http.Settings = (function () {
    /**
     * Save (create or update) a setting.
     *
     * @param {Object} setting
     *
     * @return {Object}
     */
    function save(setting) {
        return setting.id ? update(setting) : create(setting);
    }

    /**
     * Create an setting.
     *
     * @param {Object} setting
     *
     * @return {Object}
     */
    function create(setting) {
        const url = App.Utils.Url.siteUrl('settings/create');

        const data = {
            csrf_token: vars('csrf_token'),
            setting: setting,
        };

        return $.post(url, data);
    }

    /**
     * Update an setting.
     *
     * @param {Object} setting
     *
     * @return {Object}
     */
    function update(setting) {
        const url = App.Utils.Url.siteUrl('settings/update');

        const data = {
            csrf_token: vars('csrf_token'),
            setting: setting,
        };

        return $.post(url, data);
    }

    /**
     * Delete an setting.
     *
     * @param {Number} settingId
     *
     * @return {Object}
     */
    function destroy(settingId) {
        const url = App.Utils.Url.siteUrl('settings/destroy');

        const data = {
            csrf_token: vars('csrf_token'),
            setting_id: settingId,
        };

        return $.post(url, data);
    }

    /**
     * Search settings by keyword.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('settings/search');

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
     * Find an setting.
     *
     * @param {Number} settingId
     *
     * @return {Object}
     */
    function find(settingId) {
        const url = App.Utils.Url.siteUrl('settings/find');

        const data = {
            csrf_token: vars('csrf_token'),
            setting_id: settingId,
        };

        return $.post(url, data);
    }

    return {
        save,
        create,
        update,
        destroy,
        search,
        find,
    };
})();
