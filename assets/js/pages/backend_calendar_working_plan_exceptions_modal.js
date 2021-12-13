/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

/**
 * Backend Calendar Working plan exceptions Modal
 *
 * This module implements the working plan exceptions modal functionality.
 *
 * @module BackendCalendarWorkingPlanExceptionsModal
 */
window.BackendCalendarWorkingPlanExceptionsModal = window.BackendCalendarWorkingPlanExceptionsModal || {};

(function (exports) {
    'use strict';

    function bindEventHandlers() {
        /**
         * Event: Manage Modal Save Button "Click"
         *
         * Stores the working plan exception changes or inserts a new record.
         */
        $('#manage-working-plan-exceptions #save-working-plan-exception').on('click', function () {
            $('#manage-working-plan-exceptions .modal-message').prop('hidden', true);

            $('#manage-working-plan-exceptions').find('.is-invalid').removeClass('is-invalid');

            var date = $('#working-plan-exception-date').datetimepicker('getDate');

            if (!date) {
                $('#working-plan-exception-date').addClass('is-invalid');
                return;
            }

            var start = $('#working-plan-exception-start').datetimepicker('getDate');

            if (!start) {
                $('#working-plan-exception-start').addClass('is-invalid');
                return;
            }

            var end = moment($('#working-plan-exception-end').datetimepicker('getDate')).toDate();

            if (!end) {
                $('#working-plan-exception-end').addClass('is-invalid');
                return;
            }

            if (start > end) {
                // Start time is after end time - display message to user.
                $('#manage-working-plan-exceptions .modal-message')
                    .text(App.Lang.start_date_before_end_error)
                    .addClass('alert-danger')
                    .prop('hidden', false);

                $('#working-plan-exception-start').addClass('is-invalid');
                $('#working-plan-exception-end').addClass('is-invalid');
                return;
            }

            var workingPlanException = {
                start: moment(start).format('HH:mm'),
                end: moment(end).format('HH:mm'),
                breaks: []
            };

            var successCallback = function () {
                // Display success message to the user.
                Backend.displayNotification(App.Lang.working_plan_exception_saved);

                // Close the modal modal and update the local provider.
                $('#manage-working-plan-exceptions .modal-message').prop('hidden', true);
                $('#manage-working-plan-exceptions').modal('hide');

                var providerId = $('#working-plan-exception-provider').val();
                var provider = GlobalVariables.availableProviders.find(function (availableProvider) {
                    return Number(availableProvider.id) === Number(providerId);
                });

                if (!provider) {
                    throw new Error('Provider could not be found: ' + providerId);
                }

                var selectedDate = moment(date).format('yyyy-MM-dd');
                var workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions);

                workingPlanExceptions[selectedDate] = {
                    start: moment(start).format('HH:mm'),
                    end: moment(end).format('HH:mm'),
                    breaks: []
                };

                provider.settings.working_plan_exceptions = JSON.stringify(workingPlanExceptions);

                $('#select-filter-item').trigger('change'); // Update the calendar.
            };

            BackendCalendarApi.saveWorkingPlanException(date, workingPlanException, provider.id, successCallback, null);
        });

        /**
         * Event: Insert Custom Working Time Period Button "Click"
         *
         * When the user clicks this button a popup modal appears and the use can set a time period where he cannot
         * accept any appointments.
         */
        $('#insert-working-plan-exception').on('click', function () {
            BackendCalendarWorkingPlanExceptionsModal.resetWorkingPlanExceptionModal();

            if ($('.calendar-view').length === 0) {
                $('#manage-working-plan-exceptions')
                    .find('#working-plan-exception-provider')
                    .val($('#select-filter-item').val())
                    .closest('.form-group')
                    .hide();
            }

            $('#working-plan-exception-date').val(
                GeneralFunctions.formatDate(new Date(), GlobalVariables.dateFormat, false)
            );
            $('#working-plan-exception-start').val(GlobalVariables.timeFormat === 'regular' ? '8:00 AM' : '08:00');
            $('#working-plan-exception-end').val(GlobalVariables.timeFormat === 'regular' ? '8:00 PM' : '20:00');
            $('#manage-working-plan-exceptions')
                .find('.modal-header h3')
                .text(App.Lang.new_working_plan_exception_title);
            $('#manage-working-plan-exceptions').modal('show');
        });
    }

    /**
     * Reset working plan exception modal form.
     *
     * Reset the "#manage-working-plan-exceptions" modal. Use this method to bring the modal to the initial state
     * before it becomes visible to the user.
     */
    exports.resetWorkingPlanExceptionModal = function () {
        $('#manage-working-plan-exceptions').find('#working-plan-exception-id').val('');

        // Set the default datetime values.
        var date = new Date();
        var start = GlobalVariables.timeFormat === 'regular' ? '8:00 AM' : '08:00';
        var end = GlobalVariables.timeFormat === 'regular' ? '8:00 PM' : '20:00';

        var dateFormat;

        switch (GlobalVariables.dateFormat) {
            case 'DMY':
                dateFormat = 'dd/mm/yy';
                break;
            case 'MDY':
                dateFormat = 'mm/dd/yy';
                break;
            case 'YMD':
                dateFormat = 'yy/mm/dd';
                break;
        }

        $('#working-plan-exception-date').datepicker({
            dateFormat: dateFormat,

            // Translation
            dayNames: [
                App.Lang.sunday,
                App.Lang.monday,
                App.Lang.tuesday,
                App.Lang.wednesday,
                App.Lang.thursday,
                App.Lang.friday,
                App.Lang.saturday
            ],
            dayNamesShort: [
                App.Lang.sunday.substr(0, 3),
                App.Lang.monday.substr(0, 3),
                App.Lang.tuesday.substr(0, 3),
                App.Lang.wednesday.substr(0, 3),
                App.Lang.thursday.substr(0, 3),
                App.Lang.friday.substr(0, 3),
                App.Lang.saturday.substr(0, 3)
            ],
            dayNamesMin: [
                App.Lang.sunday.substr(0, 2),
                App.Lang.monday.substr(0, 2),
                App.Lang.tuesday.substr(0, 2),
                App.Lang.wednesday.substr(0, 2),
                App.Lang.thursday.substr(0, 2),
                App.Lang.friday.substr(0, 2),
                App.Lang.saturday.substr(0, 2)
            ],
            monthNames: [
                App.Lang.january,
                App.Lang.february,
                App.Lang.march,
                App.Lang.april,
                App.Lang.may,
                App.Lang.june,
                App.Lang.july,
                App.Lang.august,
                App.Lang.september,
                App.Lang.october,
                App.Lang.november,
                App.Lang.december
            ],
            prevText: App.Lang.previous,
            nextText: App.Lang.next,
            currentText: App.Lang.now,
            closeText: App.Lang.close,
            timeOnlyTitle: App.Lang.select_time,
            timeText: App.Lang.time,
            hourText: App.Lang.hour,
            minuteText: App.Lang.minutes,
            firstDay: 0
        });
        $('#working-plan-exception-date').val(GeneralFunctions.formatDate(date, GlobalVariables.dateFormat, false));

        $('#working-plan-exception-start').timepicker({
            timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : GlobalVariables.timeFormat,

            // Translation
            prevText: App.Lang.previous,
            nextText: App.Lang.next,
            currentText: App.Lang.now,
            closeText: App.Lang.close,
            timeOnlyTitle: App.Lang.select_time,
            timeText: App.Lang.time,
            hourText: App.Lang.hour,
            minuteText: App.Lang.minutes,
            firstDay: 0
        });
        $('#working-plan-exception-start').val(start);

        $('#working-plan-exception-end').timepicker({
            timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : GlobalVariables.timeFormat,
            // Translation
            prevText: App.Lang.previous,
            nextText: App.Lang.next,
            currentText: App.Lang.now,
            closeText: App.Lang.close,
            timeOnlyTitle: App.Lang.select_time,
            timeText: App.Lang.time,
            hourText: App.Lang.hour,
            minuteText: App.Lang.minutes,
            firstDay: 0
        });
        $('#working-plan-exception-end').val(end);
    };

    exports.initialize = function () {
        GlobalVariables.availableProviders.forEach(function (availableProvider) {
            $('#working-plan-exception-provider').append(
                new Option(availableProvider.first_name + ' ' + availableProvider.last_name, availableProvider.id)
            );
        });

        bindEventHandlers();
    };
})(window.BackendCalendarWorkingPlanExceptionsModal);
