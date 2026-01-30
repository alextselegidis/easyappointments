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
     * @param {Boolean} [notifyCustomer] Optional, whether to send notification to customer (defaults to true).
     * @param {Boolean} [forceSave] Optional, whether to force save even if there's a conflict (defaults to false).
     *
     * @return {*|jQuery}
     */
    function saveAppointment(
        appointment,
        customer,
        successCallback,
        errorCallback,
        notifyCustomer = true,
        forceSave = false,
    ) {
        const url = App.Utils.Url.siteUrl('calendar/save_appointment');

        const data = {
            csrf_token: vars('csrf_token'),
            appointment_data: appointment,
            notify_customer: notifyCustomer ? 1 : 0,
            force_save: forceSave ? 1 : 0,
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
     * Remove an appointment.
     *
     * @param {Number} appointmentId
     * @param {String} cancellationReason
     *
     * @return {*|jQuery}
     */
    function deleteAppointment(appointmentId, cancellationReason) {
        const url = App.Utils.Url.siteUrl('calendar/delete_appointment');

        const data = {
            csrf_token: vars('csrf_token'),
            appointment_id: appointmentId,
            cancellation_reason: cancellationReason,
        };

        return $.post(url, data);
    }

    /**
     * Save unavailability period to database.
     *
     * @param {Object} unavailability Contains the unavailability period data.
     * @param {Function} [successCallback] The ajax success callback function.
     * @param {Function} [errorCallback] The ajax failure callback function.
     *
     * @return {*|jQuery}
     */
    function saveUnavailability(unavailability, successCallback, errorCallback) {
        const url = App.Utils.Url.siteUrl('calendar/save_unavailability');

        const data = {
            csrf_token: vars('csrf_token'),
            unavailability: unavailability,
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
     * Remove an unavailability.
     *
     * @param {Number} unavailabilityId
     *
     * @return {*|jQuery}
     */
    function deleteUnavailability(unavailabilityId) {
        const url = App.Utils.Url.siteUrl('calendar/delete_unavailability');

        const data = {
            csrf_token: vars('csrf_token'),
            unavailability_id: unavailabilityId,
        };

        return $.post(url, data);
    }

    /**
     * Save working plan exception of work to database.
     *
     * @param {Date} date Contains the working plan exceptions data.
     * @param {Object} workingPlanException Contains the working plan exceptions data.
     * @param {Number} providerId Contains the working plan exceptions data.
     * @param {Function} successCallback The ajax success callback function.
     * @param {Function} errorCallback The ajax failure callback function.
     * @param {Date} [originalDate] On edit, provide the original date.
     *
     * @return {*|jQuery}
     */
    function saveWorkingPlanException(
        date,
        workingPlanException,
        providerId,
        successCallback,
        errorCallback,
        originalDate,
    ) {
        const url = App.Utils.Url.siteUrl('calendar/save_working_plan_exception');

        const data = {
            csrf_token: vars('csrf_token'),
            date: date,
            working_plan_exception: workingPlanException,
            provider_id: providerId,
            original_date: originalDate,
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
     * Delete working plan exception
     *
     * @param {String} date
     * @param {Number} providerId
     * @param {Function} [successCallback]
     * @param {Function} [errorCallback]
     *
     * @return {*|jQuery}
     */
    function deleteWorkingPlanException(date, providerId, successCallback, errorCallback) {
        const url = App.Utils.Url.siteUrl('calendar/delete_working_plan_exception');

        const data = {
            csrf_token: vars('csrf_token'),
            date: date,
            provider_id: providerId,
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
     * Get the appointments for the displayed calendar period.
     *
     * @param {Number} recordId Record ID (provider or service).
     * @param {String} filterType The filter type, could be either "provider" or "service".
     * @param {String} startDate Visible start date of the calendar.
     * @param {String} endDate Visible end date of the calendar.
     *
     * @returns {jQuery.jqXHR}
     */
    function getCalendarAppointments(recordId, startDate, endDate, filterType) {
        const url = App.Utils.Url.siteUrl('calendar/get_calendar_appointments');

        const data = {
            csrf_token: vars('csrf_token'),
            record_id: recordId,
            start_date: moment(startDate).format('YYYY-MM-DD'),
            end_date: moment(endDate).format('YYYY-MM-DD'),
            filter_type: filterType,
        };

        return $.post(url, data);
    }

    /**
     * Get the calendar appointments for the table view (different data structure).
     *
     * @param {Date} startDate
     * @param {Date} endDate
     *
     * @return {*|jQuery}
     */
    function getCalendarAppointmentsForTableView(startDate, endDate) {
        const url = App.Utils.Url.siteUrl('calendar/get_calendar_appointments_for_table_view');

        const data = {
            csrf_token: vars('csrf_token'),
            start_date: moment(startDate).format('YYYY-MM-DD'),
            end_date: moment(endDate).format('YYYY-MM-DD'),
        };

        return $.post(url, data);
    }

    /**
     * Save appointment with conflict handling.
     *
     * This method saves an appointment and handles conflict responses by showing a confirmation dialog
     * that allows the user to force save or cancel the operation.
     *
     * @param {Object} appointment The appointment data to save.
     * @param {Object} [customer] Optional customer data.
     * @param {Function} successCallback Callback function to execute on successful save.
     * @param {Function} [errorCallback] Optional callback function to execute on error.
     * @param {Boolean} notifyCustomer Whether to notify the customer.
     * @param {Function} [revertCallback] Optional callback function to execute when user cancels on conflict.
     */
    function saveAppointmentWithConflictHandling(
        appointment,
        customer,
        successCallback,
        errorCallback,
        notifyCustomer,
        revertCallback,
    ) {
        const attemptSave = (forceSave = false) => {
            saveAppointment(appointment, customer, null, errorCallback, notifyCustomer, forceSave).done((response) => {
                if (response.conflict) {
                    // Show conflict confirmation dialog
                    App.Utils.Message.show(
                        lang('appointment_update'),
                        response.message + ' ' + lang('would_you_like_to_proceed'),
                        [
                            {
                                text: lang('cancel'),
                                click: (event, messageModal) => {
                                    messageModal.hide();
                                    if (revertCallback) {
                                        revertCallback();
                                    }
                                },
                            },
                            {
                                text: lang('proceed'),
                                click: (event, messageModal) => {
                                    messageModal.hide();
                                    attemptSave(true);
                                },
                            },
                        ],
                    );
                } else if (response.success) {
                    if (successCallback) {
                        successCallback(response);
                    }
                }
            });
        };

        attemptSave();
    }

    return {
        saveAppointment,
        saveAppointmentWithConflictHandling,
        deleteAppointment,
        saveUnavailability,
        deleteUnavailability,
        saveWorkingPlanException,
        deleteWorkingPlanException,
        getCalendarAppointments,
        getCalendarAppointmentsForTableView,
    };
})();
