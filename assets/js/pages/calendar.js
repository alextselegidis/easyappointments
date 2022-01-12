/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Calendar Page
 *
 * This module contains functions that are used by the backend calendar page.
 */
App.Pages.Calendar = (function () {
    /**
     * Bind common event handlers.
     */
    function bindEventHandlers() {
        const $calendarPage = $('#calendar-page');

        $calendarPage.on('click', '#toggle-fullscreen', (event) => {
            const $toggleFullscreen = $(event.target);
            const element = document.documentElement;
            const isFullScreen = document.fullScreenElement || document.mozFullScreen || document.webkitIsFullScreen;

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

                $toggleFullscreen.removeClass('btn-success').addClass('btn-light');
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
                $toggleFullscreen.removeClass('btn-light').addClass('btn-success');
            }
        });

        $('#insert-working-plan-exception').on('click', () => {
            const providerId = $('#select-filter-item').val();

            const provider = App.Vars.available_providers.find((availableProvider) => {
                return Number(availableProvider.id) === Number(providerId);
            });

            if (!provider) {
                throw new Error('Provider could not be found: ' + providerId);
            }

            App.Components.WorkingPlanExceptionsModal.add().done((date, workingPlanException) => {
                const successCallback = () => {
                    Backend.displayNotification(App.Lang.working_plan_exception_saved);

                    const workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};

                    workingPlanExceptions[date] = workingPlanException;

                    for (let index in App.Vars.available_providers) {
                        const availableProvider = App.Vars.available_providers[index];

                        if (Number(availableProvider.id) === Number(providerId)) {
                            App.Vars.available_providers[index].settings.working_plan_exceptions =
                                JSON.stringify(workingPlanExceptions);
                            break;
                        }
                    }

                    $('#select-filter-item').trigger('change'); // Update the calendar.
                };

                App.Http.Calendar.saveWorkingPlanException(
                    date,
                    workingPlanException,
                    providerId,
                    successCallback,
                    null
                );
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
    function initialize(view) {
        App.Utils.CalendarGoogleSync.initialize();

        App.Components.ManageAppointmentsModal.initialize();

        App.Components.ManageUnavailabilitiesModal.initialize();

        // Load and initialize the calendar view.
        if (view === 'table') {
            App.Utils.CalendarTableView.initialize();
        } else {
            App.Utils.CalendarDefaultView.initialize();
        }

        bindEventHandlers();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        initialize
    };
})();
