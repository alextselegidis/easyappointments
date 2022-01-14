/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

/**
 * Calendar HTTP client.
 *
 * This module implements the calendar related HTTP requests.
 *
 * Old Name: BackendCalendarApi
 */
App.Http.Calendar = (function () {
    /**
     * Save Appointment
     *
     * This method stores the changes of an already registered appointment into the database, via an ajax call.
     *
     * @param {Object} appointment Contain the new appointment data. The ID of the appointment must be already included.
     * The rest values must follow the database structure.
     * @param {Object} [customer] Optional, contains the customer data.
     * @param {Function} [successCallback] Optional, if defined, this function is going to be executed on post success.
     * @param {Function} [errorCallback] Optional, if defined, this function is going to be executed on post failure.
     */
    function saveAppointment(appointment, customer, successCallback, errorCallback) {
        const url = App.Utils.Url.siteUrl('calendar/ajax_save_appointment');

        const data = {
            csrf_token: App.Vars.csrf_token,
            appointment_data: appointment
        };

        if (customer) {
            data.customer_data = customer;
        }

        return $.post(url, data)
            .done((response) => {
                if (successCallback) {
                    successCallback(response);
                }
            })
            .fail(() => {
                if (errorCallback) {
                    errorCallback();
                }
            });
    }

    /**
     * Save unavailable period to database.
     *
     * @param {Object} unavailable Contains the unavailable period data.
     * @param {Function} successCallback The ajax success callback function.
     * @param {Function} errorCallback The ajax failure callback function.
     */
    function saveUnavailable(unavailable, successCallback, errorCallback) {
        const url = App.Utils.Url.siteUrl('calendar/ajax_save_unavailable');

        const data = {
            csrf_token: App.Vars.csrf_token,
            unavailable: unavailable
        };

        return $.post(url, data)
            .done((response) => {
                if (successCallback) {
                    successCallback(response);
                }
            })
            .fail(() => {
                if (errorCallback) {
                    errorCallback();
                }
            });
    }

    /**
     * Save working plan exception of work to database.
     *
     * @param {Date} date Contains the working plan exceptions data.
     * @param {Object} workingPlanException Contains the working plan exceptions data.
     * @param {Number} providerId Contains the working plan exceptions data.
     * @param {Function} successCallback The ajax success callback function.
     * @param {Function} errorCallback The ajax failure callback function.
     */
    function saveWorkingPlanException(date, workingPlanException, providerId, successCallback, errorCallback) {
        const url = App.Utils.Url.siteUrl('calendar/ajax_save_working_plan_exception');

        const data = {
            csrf_token: App.Vars.csrf_token,
            date: date,
            working_plan_exception: workingPlanException,
            provider_id: providerId
        };

        return $.post(url, data)
            .done((response) => {
                if (successCallback) {
                    successCallback(response);
                }
            })
            .fail(() => {
                if (errorCallback) {
                    errorCallback();
                }
            });
    }

    function deleteWorkingPlanException(date, providerId, successCallback, errorCallback) {
        const url = App.Utils.Url.siteUrl('calendar/ajax_delete_working_plan_exception');

        const data = {
            csrf_token: App.Vars.csrf_token,
            date: date,
            provider_id: providerId
        };

        return $.post(url, data)
            .done((response) => {
                if (successCallback) {
                    successCallback(response);
                }
            })
            .fail(() => {
                if (errorCallback) {
                    errorCallback();
                }
            });
    }

    return {
        saveAppointment,
        saveUnavailable,
        saveWorkingPlanException,
        deleteWorkingPlanException
    };
})();
