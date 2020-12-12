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
                    .addClass('btn-light');
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
                    .removeClass('btn-light')
                    .addClass('btn-success');
            }
        });

        $('#insert-working-plan-exception').on('click', function () {
            var providerId = $('#select-filter-item').val();

            var provider = GlobalVariables.availableProviders.find(function (availableProvider) {
                return Number(availableProvider.id) === Number(providerId);
            });

            if (!provider) {
                throw new Error('Provider could not be found: ' + providerId);
            }

            WorkingPlanExceptionsModal
                .add()
                .done(function (date, workingPlanException) {
                    var successCallback = function () {
                        Backend.displayNotification(EALang.working_plan_exception_saved);

                        var workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};

                        workingPlanExceptions[date] = workingPlanException;

                        for (var index in GlobalVariables.availableProviders) {
                            var availableProvider = GlobalVariables.availableProviders[index];

                            if (Number(availableProvider.id) === Number(providerId)) {
                                GlobalVariables.availableProviders[index].settings.working_plan_exceptions = JSON.stringify(workingPlanExceptions);
                                break;
                            }
                        }

                        $('#select-filter-item').trigger('change'); // Update the calendar.
                    };

                    BackendCalendarApi.saveWorkingPlanException(date, workingPlanException, providerId, successCallback, null);
                });
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
        BackendCalendarGoogleSync.initialize();
        BackendCalendarAppointmentsModal.initialize();
        BackendCalendarUnavailabilityEventsModal.initialize();

        // Load and initialize the calendar view.
        if (view === 'table') {
            BackendCalendarTableView.initialize();
        } else {
            BackendCalendarDefaultView.initialize();
        }

        bindEventHandlers();
    };

})(window.BackendCalendar);
