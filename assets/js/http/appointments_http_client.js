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
 * Appointments HTTP client.
 *
 * This module implements the appointments related HTTP requests.
 */
App.Http.Appointments = (function () {
    /**
     * Save (create or update) an appointment.
     *
     * @param {Object} appointment
     *
     * @return {Object}
     */
    function save(appointment) {
        return appointment.id ? update(appointment) : store(appointment);
    }

    /**
     * Create an appointment.
     *
     * @param {Object} appointment
     *
     * @return {Object}
     */
    function store(appointment) {
        const url = App.Utils.Url.siteUrl('appointments/store');

        const data = {
            csrf_token: vars('csrf_token'),
            appointment: appointment,
        };

        return $.post(url, data);
    }

    /**
     * Update an appointment.
     *
     * @param {Object} appointment
     *
     * @return {Object}
     */
    function update(appointment) {
        const url = App.Utils.Url.siteUrl('appointments/update');

        const data = {
            csrf_token: vars('csrf_token'),
            appointment: appointment,
        };

        return $.post(url, data);
    }

    /**
     * Delete an appointment.
     *
     * @param {Number} appointmentId
     *
     * @return {Object}
     */
    function destroy(appointmentId) {
        const url = App.Utils.Url.siteUrl('appointments/destroy');

        const data = {
            csrf_token: vars('csrf_token'),
            appointment_id: appointmentId,
        };

        return $.post(url, data);
    }

    /**
     * Search appointments by keyword.
     *
     * @param {String} keyword
     * @param {Number} [limit]
     * @param {Number} [offset]
     * @param {String} [orderBy]
     *
     * @return {Object}
     */
    function search(keyword, limit = null, offset = null, orderBy = null) {
        const url = App.Utils.Url.siteUrl('appointments/search');

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
     * Find an appointment.
     *
     * @param {Number} appointmentId
     *
     * @return {Object}
     */
    function find(appointmentId) {
        const url = App.Utils.Url.siteUrl('appointments/find');

        const data = {
            csrf_token: vars('csrf_token'),
            appointment_id: appointmentId,
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
