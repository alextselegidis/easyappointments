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
 * Backend Calendar Working plan periods Modal
 *
 * This module implements the working plan periods modal functionality.
 *
 * @module BackendCalendarWorkingPlanPeriodsModal
 */
window.BackendCalendarWorkingPlanPeriodsModal = window.BackendCalendarWorkingPlanPeriodsModal || {};

(function (exports) {

    'use strict';

    function bindEventHandlers() {
        /**
         * Event: Manage Modal Save Button "Click"
         *
         * Stores the working plan period changes or inserts a new record.
         */
        $('#manage-working-plan-periods #save-working-plan-period').on('click', function () {
            $('#manage-working-plan-periods .modal-message').addClass('hidden');

            $('#manage-working-plan-periods').find('.has-error').removeClass('has-error');

            var startdate = $('#working-plan-period-startdate').datetimepicker('getDate');

            if (!startdate) {
                $('#working-plan-period-startdate').closest('.form-group').addClass('has-error');
                return;
            }

            var enddate = $('#working-plan-period-enddate').datetimepicker('getDate');

            if (!enddate) {
                $('#working-plan-period-enddate').closest('.form-group').addClass('has-error');
                return;
            }

            if (startdate > enddate) {
                // Start time is after end time - display message to user.
                $('#manage-working-plan-periods .modal-message')
                    .text(EALang.start_date_before_end_error)
                    .addClass('alert-danger')
                    .removeClass('hidden');

                $('#working-plan-period-startdate').addClass('has-error');
                $('#working-plan-period-enddate').addClass('has-error');
                return;
            }

            var workingPlanPeriod = {
                startdate: startdate.toString('YYY-MM-DD'),
                enddate: enddate.toString('YYY-MM-DD'),
                breaks: []
            };

            var successCallback = function () {
                // Display success message to the user.
                Backend.displayNotification(EALang.working_plan_period_saved);

                // Close the modal modal and update the local provider.
                $('#manage-working-plan-periods .modal-message').addClass('hidden');
                $('#manage-working-plan-periods').modal('hide');

                var providerId = $('#working-plan-period-provider').val();
                var provider = GlobalVariables.availableProviders.find(function (availableProvider) {
                    return Number(availableProvider.id) === Number(providerId);
                });

                if (!provider) {
                    throw new Error('Provider could not be found: ' + providerId);
                }

                var selectedDate = date.toString('yyyy-MM-dd');
                var workingPlanPeriods = JSON.parse(provider.settings.working_plan_periods);

                workingPlanPeriods[selectedDate] = {
                    start: start.toString('HH:mm'),
                    end: end.toString('HH:mm'),
                    breaks: [],
                };

                provider.settings.working_plan_periods = JSON.stringify(workingPlanPeriods);

                $('#select-filter-item').trigger('change'); // Update the calendar.
            };

            BackendCalendarApi.saveWorkingPlanPeriod(date, workingPlanPeriod, provider.id, successCallback, null);
        });

        /**
         * Event: Insert Custom Working Time Period Button "Click"
         *
         * When the user clicks this button a popup modal appears and the use can set a time period where he cannot
         * accept any appointments.
         */
        $('#insert-working-plan-period').on('click', function () {
            BackendCalendarWorkingPlanPeriodsModal.resetWorkingPlanPeriodModal();

            if ($('.calendar-view').length === 0) {
                $('#manage-working-plan-periods').find('#working-plan-period-provider')
                    .val($('#select-filter-item').val())
                    .closest('.form-group')
                    .hide();
            }

            $('#working-plan-period-startdate').val(GeneralFunctions.formatDate(new Date(), GlobalVariables.dateFormat, false));
            $('#working-plan-period-enddate').val(GeneralFunctions.formatDate(new Date(), GlobalVariables.dateFormat, false));
            $('#manage-working-plan-periods').find('.modal-header h3').text(EALang.new_working_plan_period_title);
            $('#manage-working-plan-periods').modal('show');
        });
    }

    /**
     * Reset working plan period modal form.
     *
     * Reset the "#manage-working-plan-periods" modal. Use this method to bring the modal to the initial state
     * before it becomes visible to the user.
     */
    exports.resetWorkingPlanPeriodModal = function () {
        $('#manage-working-plan-periods').find('#working-plan-period-id').val('');

        // Set the default datetime values.
        var startdate = new Date();
        var enddate = new Date();
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

        $('#working-plan-period-startdate').datepicker({
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
        $('#working-plan-period-startdate').val(GeneralFunctions.formatDate(startdate, GlobalVariables.dateFormat, false));

        $('#working-plan-period-enddate').datepicker({
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
        $('#working-plan-period-enddate').val(GeneralFunctions.formatDate(enddate, GlobalVariables.dateFormat, false));


    };

    exports.initialize = function () {
        GlobalVariables.availableProviders.forEach(function (availableProvider) {
            $('#working-plan-period-provider').append(new Option(availableProvider.first_name + ' ' + availableProvider.last_name, availableProvider.id));
        });

        bindEventHandlers();
    };

})(window.BackendCalendarWorkingPlanPeriodsModal);
