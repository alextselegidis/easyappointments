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

window.App.Http.Appointments = (function () {
    /**
     * Create an appointment.
     *
     * @param {Object} appointment
     *
     * @return {jQuery.Deferred}
     */
    function create(appointment) {
        const url = App.Utils.Url.siteUrl('appointments/create');

        const data = {
            csrf_token: App.Config.csrf_token,
            appointment: appointment
        };

        return $.post(url, data);
    }

    /**
     * Update an appointment.
     *
     * @param {Object} appointment
     *
     * @return {jQuery.Deferred}
     */
    function update(appointment) {
        const url = App.Utils.Url.siteUrl('appointments/update');

        const data = {
            csrf_token: App.Config.csrf_token,
            appointment: appointment
        };

        return $.post(url, data);
    }

    /**
     * Delete an appointment.
     *
     * @param {Number} appointmentId
     *
     * @return {jQuery.Deferred}
     */
    function destroy(appointmentId) {
        const url = App.Utils.Url.siteUrl('appointments/destroy');

        const data = {
            csrf_token: App.Config.csrf_token,
            appointment_id: appointmentId
        };

        return $.post(url, data);
    }

    /**
     * Search appointments by keyword.
     *
     * @param {String} keyword
     * @param {Number} limit
     * @param {Number} offset
     * @param {String} orderBy
     *
     * @return {jQuery.Deferred}
     */
    function search(keyword, limit, offset, orderBy) {
        const url = App.Utils.Url.siteUrl('appointments/search');

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
     * Find an appointment.
     *
     * @param {Number} appointmentId
     *
     * @return {jQuery.Deferred}
     */
    function find(appointmentId) {
        const url = App.Utils.Url.siteUrl('appointments/find');

        const data = {
            csrf_token: App.Config.csrf_token,
            appointment_id: appointmentId
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
