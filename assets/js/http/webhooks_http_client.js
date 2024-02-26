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
 * Webhooks HTTP client.
 *
 * This module implements the webhooks related HTTP requests.
 */
App.Http.Webhooks = (function () {
    /**
     * Save (create or update) a webhook.
     *
     * @param {Object} webhook
     *
     * @return {Object}
     */
    function save(webhook) {
        return webhook.id ? update(webhook) : store(webhook);
    }

    /**
     * Create an webhook.
     *
     * @param {Object} webhook
     *
     * @return {Object}
     */
    function store(webhook) {
        const url = App.Utils.Url.siteUrl('webhooks/store');

        const data = {
            csrf_token: vars('csrf_token'),
            webhook: webhook,
        };

        return $.post(url, data);
    }

    /**
     * Update an webhook.
     *
     * @param {Object} webhook
     *
     * @return {Object}
     */
    function update(webhook) {
        const url = App.Utils.Url.siteUrl('webhooks/update');

        const data = {
            csrf_token: vars('csrf_token'),
            webhook: webhook,
        };

        return $.post(url, data);
    }

    /**
     * Delete an webhook.
     *
     * @param {Number} webhookId
     *
     * @return {Object}
     */
    function destroy(webhookId) {
        const url = App.Utils.Url.siteUrl('webhooks/destroy');

        const data = {
            csrf_token: vars('csrf_token'),
            webhook_id: webhookId,
        };

        return $.post(url, data);
    }

    /**
     * Search webhooks by keyword.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('webhooks/search');

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
     * Find an webhook.
     *
     * @param {Number} webhookId
     *
     * @return {Object}
     */
    function find(webhookId) {
        const url = App.Utils.Url.siteUrl('webhooks/find');

        const data = {
            csrf_token: vars('csrf_token'),
            webhook_id: webhookId,
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
