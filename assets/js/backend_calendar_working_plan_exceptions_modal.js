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
            $('#manage-working-plan-exceptions .modal-message').addClass('hidden');

            $('#manage-working-plan-exceptions').find('.has-error').removeClass('has-error');

            var date = $('#working-plan-exception-date').datetimepicker('getDate');

            if (!date) {
                $('#working-plan-exception-date').closest('.form-group').addClass('has-error');
                return;
            }

            var start = $('#working-plan-exception-start').datetimepicker('getDate');

            if (!start) {
                $('#working-plan-exception-start').closest('.form-group').addClass('has-error');
                return;
            }

            var end = Date.parse($('#working-plan-exception-end').datetimepicker('getDate'));

            if (!end) {
                $('#working-plan-exception-end').closest('.form-group').addClass('has-error');
                return;
            }

            if (start > end) {
                // Start time is after end time - display message to user.
                $('#manage-working-plan-exceptions .modal-message')
                    .text(EALang.start_date_before_end_error)
                    .addClass('alert-danger')
                    .removeClass('hidden');

                $('#working-plan-exception-start').addClass('has-error');
                $('#working-plan-exception-end').addClass('has-error');
                return;
            }

            var workingPlanException = {
                start: start.toString('HH:mm'),
                end: end.toString('HH:mm'),
                breaks: []
            };

            var successCallback = function () {
                // Display success message to the user.
                Backend.displayNotification(EALang.working_plan_exception_saved);

                // Close the modal modal and update the local provider.
                $('#manage-working-plan-exceptions .modal-message').addClass('hidden');
                $('#manage-working-plan-exceptions').modal('hide');

                var providerId = $('#working-plan-exception-provider').val();
                var provider = GlobalVariables.availableProviders.find(function (availableProvider) {
                    return Number(availableProvider.id) === Number(providerId);
                });

                if (!provider) {
                    throw new Error('Provider could not be found: ' + providerId);
                }

                var selectedDate = date.toString('yyyy-MM-dd');
                var workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions);

                workingPlanExceptions[selectedDate] = {
                    start: start.toString('HH:mm'),
                    end: end.toString('HH:mm'),
                    breaks: [],
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
                $('#manage-working-plan-exceptions').find('#working-plan-exception-provider')
                    .val($('#select-filter-item').val())
                    .closest('.form-group')
                    .hide();
            }

            $('#working-plan-exception-date').val(GeneralFunctions.formatDate(new Date(), GlobalVariables.dateFormat, false));
            $('#working-plan-exception-start').val(GlobalVariables.timeFormat === 'regular' ? '8:00 AM' : '08:00');
            $('#working-plan-exception-end').val(GlobalVariables.timeFormat === 'regular' ? '8:00 PM' : '20:00');
            $('#manage-working-plan-exceptions').find('.modal-header h3').text(EALang.new_working_plan_exception_title);
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
        var start = GlobalVariables.timeFormat === 'regular' ? '8:00 AM' : '08:00'
        var end = GlobalVariables.timeFormat === 'regular' ? '8:00 PM' : '20:00'

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
            dayNames: [EALang.sunday, EALang.monday, EALang.tuesday, EALang.wednesday,
                EALang.thursday, EALang.friday, EALang.saturday],
            dayNamesShort: [EALang.sunday.substr(0, 3), EALang.monday.substr(0, 3),
                EALang.tuesday.substr(0, 3), EALang.wednesday.substr(0, 3),
                EALang.thursday.substr(0, 3), EALang.friday.substr(0, 3),
                EALang.saturday.substr(0, 3)],
            dayNamesMin: [EALang.sunday.substr(0, 2), EALang.monday.substr(0, 2),
                EALang.tuesday.substr(0, 2), EALang.wednesday.substr(0, 2),
                EALang.thursday.substr(0, 2), EALang.friday.substr(0, 2),
                EALang.saturday.substr(0, 2)],
            monthNames: [EALang.january, EALang.february, EALang.march, EALang.april,
                EALang.may, EALang.june, EALang.july, EALang.august, EALang.september,
                EALang.october, EALang.november, EALang.december],
            prevText: EALang.previous,
            nextText: EALang.next,
            currentText: EALang.now,
            closeText: EALang.close,
            timeOnlyTitle: EALang.select_time,
            timeText: EALang.time,
            hourText: EALang.hour,
            minuteText: EALang.minutes,
            firstDay: 0
        });
        $('#working-plan-exception-date').val(GeneralFunctions.formatDate(date, GlobalVariables.dateFormat, false));

        $('#working-plan-exception-start').timepicker({
            timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : GlobalVariables.timeFormat,

            // Translation
            prevText: EALang.previous,
            nextText: EALang.next,
            currentText: EALang.now,
            closeText: EALang.close,
            timeOnlyTitle: EALang.select_time,
            timeText: EALang.time,
            hourText: EALang.hour,
            minuteText: EALang.minutes,
            firstDay: 0
        });
        $('#working-plan-exception-start').val(start);

        $('#working-plan-exception-end').timepicker({
            timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : GlobalVariables.timeFormat,
            // Translation
            prevText: EALang.previous,
            nextText: EALang.next,
            currentText: EALang.now,
            closeText: EALang.close,
            timeOnlyTitle: EALang.select_time,
            timeText: EALang.time,
            hourText: EALang.hour,
            minuteText: EALang.minutes,
            firstDay: 0
        });
        $('#working-plan-exception-end').val(end);
    };

    exports.initialize = function () {
        GlobalVariables.availableProviders.forEach(function (availableProvider) {
            $('#working-plan-exception-provider').append(new Option(availableProvider.first_name + ' ' + availableProvider.last_name, availableProvider.id));
        });

        bindEventHandlers();
    };

})(window.BackendCalendarWorkingPlanExceptionsModal);
