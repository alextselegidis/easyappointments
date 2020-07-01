/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
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

(function (exports) {

    'use strict';

    /**
     * Bind common event handlers.
     */
    function bindEventHandlers() {
        var $calendarPage = $('#calendar-page');

        $calendarPage.on('click', '#toggle-fullscreen', function () {
            var $toggleFullscreen = $(this);
            var element = document.documentElement;
            var isFullScreen = document.fullScreenElement || document.mozFullScreen || document.webkitIsFullScreen;

            if (isFullScreen) {
                // Exit fullscreen mode.
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }

                $toggleFullscreen
                    .removeClass('btn-success')
                    .addClass('btn-default');
            } else {
                // Switch to fullscreen mode.
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                }
                $toggleFullscreen
                    .removeClass('btn-default')
                    .addClass('btn-success');
            }
        });
    }

    /**
     * Initialize Module
     *
     * This function makes the necessary initialization for the default backend calendar page. If this module
     * is used in another page then this function might not be needed.
     *
     * @param {String} view Optional (default), the calendar view to be loaded.
     */
    exports.initialize = function (view) {
        // Load and initialize the calendar view.
        if (view === 'table') {
            BackendCalendarTableView.initialize();
        } else {
            BackendCalendarDefaultView.initialize();
        }

        BackendCalendarGoogleSync.initialize();
        BackendCalendarAppointmentsModal.initialize();
        BackendCalendarUnavailabilitiesModal.initialize();
        BackendCalendarExtraPeriodsModal.initialize();

        bindEventHandlers();
    };

})(window.BackendCalendar);
