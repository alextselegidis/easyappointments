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
 * Blocked-periods HTTP client.
 *
 * This module implements the blocked-periods related HTTP requests.
 */
App.Http.BlockedPeriods = (function () {
    /**
     * Save (create or update) a blocked-period.
     *
     * @param {Object} blockedPeriod
     *
     * @return {Object}
     */
    function save(blockedPeriod) {
        return blockedPeriod.id ? update(blockedPeriod) : store(blockedPeriod);
    }

    /**
     * Create a blocked-period.
     *
     * @param {Object} blockedPeriod
     *
     * @return {Object}
     */
    function store(blockedPeriod) {
        const url = App.Utils.Url.siteUrl('blocked_periods/store');

        const data = {
            csrf_token: vars('csrf_token'),
            blocked_period: blockedPeriod,
        };

        return $.post(url, data);
    }

    /**
     * Update a blocked-period.
     *
     * @param {Object} blockedPeriod
     *
     * @return {Object}
     */
    function update(blockedPeriod) {
        const url = App.Utils.Url.siteUrl('blocked_periods/update');

        const data = {
            csrf_token: vars('csrf_token'),
            blocked_period: blockedPeriod,
        };

        return $.post(url, data);
    }

    /**
     * Delete a blocked-period.
     *
     * @param {Number} blockedPeriodId
     *
     * @return {Object}
     */
    function destroy(blockedPeriodId) {
        const url = App.Utils.Url.siteUrl('blocked_periods/destroy');

        const data = {
            csrf_token: vars('csrf_token'),
            blocked_period_id: blockedPeriodId,
        };

        return $.post(url, data);
    }

    /**
     * Search blocked-periods by keyword.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('blocked_periods/search');

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
     * Find a blocked-period.
     *
     * @param {Number} blockedPeriodId
     *
     * @return {Object}
     */
    function find(blockedPeriodId) {
        const url = App.Utils.Url.siteUrl('blocked_periods/find');

        const data = {
            csrf_token: vars('csrf_token'),
            blocked_period_id: blockedPeriodId,
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
