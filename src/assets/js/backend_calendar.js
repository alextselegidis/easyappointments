/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Backend Calendar
 *
 * This module contains functions that are used by the backend calendar page.
 *
 * @module BackendCalendar
 */
window.BackendCalendar = window.BackendCalendar || {};

(function(exports) {

    'use strict';

    /**
     * Initialize Module
     *
     * This function makes the necessary initialization for the default backend calendar page. If this module
     * is used in another page then this function might not be needed.
     *
     * @param {String} view Optional (default), the calendar view to be loaded.
     */
    exports.initialize = function(view) { 
        // Load and initialize the calendar view. 
        if (view === 'table') {
            BackendCalendarTableView.initialize(); 
        } else {
            BackendCalendarDefaultView.initialize();
        }

        BackendCalendarGoogleSync.initialize();
        BackendCalendarAppointmentsModal.initialize();
        BackendCalendarUnavailabilitiesModal.initialize();
    };

})(window.BackendCalendar);
