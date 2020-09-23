/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2017, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

/**
 * Backend Calendar Custom availability periods Modal
 *
 * This module implements the custom availability periods modal functionality.
 *
 * @module BackendCalendarCustomAvailabilityPeriodsModal
 */
window.BackendCalendarCustomAvailabilityPeriodsModal = window.BackendCalendarCustomAvailabilityPeriodsModal || {};

(function (exports) {

    'use strict';

    function bindEventHandlers() {
        /**
         * Event: Manage Dialog Save Button "Click"
         *
         * Stores the custom availability period changes or inserts a new record.
         */
        $('#manage-custom-availability-periods #save-custom-availability-period').on('click', function () {
            var $dialog = $('#manage-custom-availability-periods');
            $dialog.find('.has-error').removeClass('has-error');
            var start = $dialog.find('#custom-availability-period-start').datetimepicker('getDate');
            var end = Date.parse($dialog.find('#custom-availability-period-end').datetimepicker('getDate'));

            if (start.toString('HH:mm') > end.toString('HH:mm')) {
                // Start time is after end time - display message to user.
                $dialog.find('.modal-message')
                    .text(EALang.start_date_before_end_error)
                    .addClass('alert-danger')
                    .removeClass('hidden');

                $dialog.find('#custom-availability-period-start, #custom-availability-period-end').closest('.form-group').addClass('has-error');
                return;
            }

            // Custom availability period period records go to the appointments table.
            var customAvailabilityPeriod = {
                start_datetime: start.toString('yyyy-MM-dd HH:mm'),
                end_datetime: start.toString('yyyy-MM-dd') + ' ' + end.toString('HH:mm'),
                id_users_provider: $('#custom-availability-period-provider').val() // curr provider
            };

            //if ($dialog.find('#custom-availability-period-id').val() !== '') {
            //    // Set the id value, only if we are editing a custom availability period.
            //    customAvailabilityPeriod.id = $dialog.find('#custom-availability-period-id').val();
            //}

            var successCallback = function (response) {
                // Display success message to the user.
                Backend.displayNotification(EALang.custom_availability_period_saved);

                // Close the modal dialog and refresh the calendar appointments.
                $dialog.find('.alert').addClass('hidden');
                $dialog.modal('hide');

                var providerId = $('#custom-availability-period-provider').val();
                var provider = GlobalVariables.availableProviders.filter(function (p) {
                    return p.id === providerId;
                })[0];
                var providerIdx = GlobalVariables.availableProviders.indexOf(provider);

                var customAvailabilityPeriods = jQuery.parseJSON(provider.settings.custom_availability_periods);
                customAvailabilityPeriods[start.toString('yyyy-MM-dd')] = {
                    start: start.toString('HH:mm'),
                    end: end.toString('HH:mm'),
                    breaks: []
                };
                provider.settings.custom_availability_periods = JSON.stringify(customAvailabilityPeriods);
                GlobalVariables.availableProviders[providerIdx] = provider;

                $('#select-filter-item').trigger('change');
            };

            var errorCallback = function (jqXHR, textStatus, errorThrown) {
                $dialog.find('.modal-message').text(EALang.service_communication_error);
                $dialog.find('.modal-message').addClass('alert-danger').removeClass('hidden');
            };

            BackendCalendarApi.saveCustomavailabilityperiod(customAvailabilityPeriod, successCallback, errorCallback);
        });

        /**
         * Event: Manage Dialog Cancel Button "Click"
         *
         * Closes the dialog without saving any changes to the database.
         */
        $('#manage-custom-availability-periods #cancel-custom-availability-period').on('click', function () {
            $('#manage-custom-availability-periods').modal('hide');
        });

        /**
         * Event: Insert Custom Working Time Period Button "Click"
         *
         * When the user clicks this button a popup dialog appears and the use can set a time period where he cannot
         * accept any appointments.
         */
        $('#insert-custom-availability-period').on('click', function () {
            BackendCalendarCustomAvailabilityPeriodsModal.resetCustomavailabilityperiodDialog();
            var $dialog = $('#manage-custom-availability-periods');

            // Set the default datetime values.
            var start = new Date();
            start.addDays(1);
            start.set({'hour': 8});
            start.set({'minute': 30});

            if ($('.calendar-view').length === 0) {
                $dialog.find('#custom-availability-period-provider')
                    .val($('#select-filter-item').val())
                    .closest('.form-group')
                    .hide();
            }

            $dialog.find('#custom-availability-period-start').val(GeneralFunctions.formatDate(start, GlobalVariables.dateFormat, true));
            $dialog.find('#custom-availability-period-end').val(GlobalVariables.timeFormat === 'regular' ? '8:00 PM' : '19:00');
            $dialog.find('.modal-header h3').text(EALang.new_custom_availability_period_title);
            $dialog.modal('show');
        });
    }

    /**
     * Reset custom availability period dialog form.
     *
     * Reset the "#manage-custom-availability-periods" dialog. Use this method to bring the dialog to the initial state
     * before it becomes visible to the user.
     */
    exports.resetCustomavailabilityperiodDialog = function () {
        var $dialog = $('#manage-custom-availability-periods');

        $dialog.find('#custom-availability-period-id').val('');

        // Set the default datetime values.
        var start = new Date();
        start.addDays(1);
        start.set({'hour': 8});
        start.set({'minute': 30});
        var end = GlobalVariables.timeFormat === 'regular' ? '8:00 PM' : '19:00'

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


        $dialog.find('#custom-availability-period-start').datetimepicker({
            dateFormat: dateFormat,
            timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : GlobalVariables.timeFormat,

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
        $dialog.find('#custom-availability-period-start').val(GeneralFunctions.formatDate(start, GlobalVariables.dateFormat, true));
        $dialog.find('#custom-availability-period-start').draggable();

        $dialog.find('#custom-availability-period-end').timepicker({
            timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : GlobalVariables.timeFormat,
            currentText: EALang.now,
            closeText: EALang.close,
            timeOnlyTitle: EALang.select_time,
            timeText: EALang.time,
            hourText: EALang.hour,
            minuteText: EALang.minutes
        });
        $dialog.find('#custom-availability-period-end').val(end);

        // Clear the custom availability period notes field.
        $dialog.find('#custom-availability-period-notes').val('');
    };

    exports.initialize = function () {
        var customAvailabilityPeriodProvider = $('#custom-availability-period-provider');

        for (var index in GlobalVariables.availableProviders) {
            var provider = GlobalVariables.availableProviders[index];

            customAvailabilityPeriodProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
        }

        bindEventHandlers();
    };

})(window.BackendCalendarCustomAvailabilityPeriodsModal);
