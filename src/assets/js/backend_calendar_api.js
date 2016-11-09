/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

/**
 * Backend Calendar API
 *
 * This module implements the AJAX requests for the calendar page.
 *
 * @module BackendCalendarApi
 */
window.BackendCalendarApi = window.BackendCalendarApi || {};

(function(exports) {

    'use strict'; 

    /**
     * Save Appointment
     *
     * This method stores the changes of an already registered appointment into the database, via an ajax call.
     *
     * @param {Object} appointment Contain the new appointment data. The ID of the appointment MUST be
     * already included. The rest values must follow the database structure.
     * @param {Object} customer Optional, contains the customer data.
     * @param {Function} successCallback Optional, if defined, this function is going to be executed on post success.
     * @param {Function} errorCallback Optional, if defined, this function is going to be executed on post failure.
     */
    exports.saveAppointment = function(appointment, customer, successCallback, errorCallback) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_appointment';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            appointment_data: JSON.stringify(appointment)
        };

        if (customer !== undefined) {
            postData['customer_data'] = JSON.stringify(customer);
        }

        $.ajax({
            url: postUrl,
            type: 'POST',
            data: postData,
            dataType: 'json'
        })
            .done(function(response) {
                if (successCallback !== undefined) {
                    successCallback(response);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                if (errorCallback !== undefined) {
                    errorCallback();
                }
            });
    };

    /**
     * Save unavailable period to database.
     *
     * @param {Object} unavailable Contains the unavailable period data.
     * @param {Function} successCallback The ajax success callback function.
     * @param {Function} errorCallback The ajax failure callback function.
     */
    exports.saveUnavailable = function(unavailable, successCallback, errorCallback) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_unavailable';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            unavailable: JSON.stringify(unavailable)
        };

        $.ajax({
            type: 'POST',
            url: postUrl,
            data: postData,
            success: successCallback,
            error: errorCallback
        });
    };

})(window.BackendCalendarApi); 
