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
 * Caldav HTTP client.
 *
 * This module implements the Caldav Calendar related HTTP requests.
 */
App.Http.Caldav = (function () {
    /**
     * Select the Caldav Calendar for the synchronization with a provider.
     *
     * @param {Number} providerId
     * @param {String} caldavCalendarId
     *
     * @return {*|jQuery}
     */
    function selectCaldavCalendar(providerId, caldavCalendarId) {
        const url = App.Utils.Url.siteUrl('caldav/select_caldav_calendar');

        const data = {
            csrf_token: vars('csrf_token'),
            provider_id: providerId,
            calendar_id: caldavCalendarId,
        };

        return $.post(url, data);
    }

    /**
     * Disable the Caldav Calendar syncing of a provider.
     *
     * @param {Number} providerId
     *
     * @return {*|jQuery}
     */
    function disableProviderSync(providerId) {
        const url = App.Utils.Url.siteUrl('caldav/disable_provider_sync');

        const data = {
            csrf_token: vars('csrf_token'),
            provider_id: providerId,
        };

        return $.post(url, data);
    }

    /**
     * Get the available Caldav Calendars of the connected provider's account.
     *
     * @param {Number} providerId
     *
     * @return {*|jQuery}
     */
    function getCaldavCalendars(providerId) {
        const url = App.Utils.Url.siteUrl('caldav/get_caldav_calendars');

        const data = {
            csrf_token: vars('csrf_token'),
            provider_id: providerId,
        };

        return $.post(url, data);
    }

    /**
     * Trigger the sync process between Easy!Appointments and Caldav Calendar.
     *
     * @param {Number} providerId
     *
     * @return {*|jQuery}
     */
    function syncWithCaldav(providerId) {
        const url = App.Utils.Url.siteUrl('caldav/sync/' + providerId);

        return $.get(url);
    }

    function connectToServer(providerId, caldavUrl, caldavUsername, caldavPassword) {
        const url = App.Utils.Url.siteUrl('caldav/connect_to_server');

        const data = {
            csrf_token: vars('csrf_token'),
            provider_id: providerId,
            caldav_url: caldavUrl,
            caldav_username: caldavUsername,
            caldav_password: caldavPassword,
        };

        return $.post(url, data);
    }

    return {
        getCaldavCalendars,
        selectCaldavCalendar,
        disableProviderSync,
        syncWithCaldav,
        connectToServer,
    };
})();
