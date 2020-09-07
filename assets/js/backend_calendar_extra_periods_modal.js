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
 * Backend Calendar Extra Periods Modal
 *
 * This module implements the extra periods modal functionality.
 *
 * @module BackendCalendarExtraPeriodsModal
 */
window.BackendCalendarExtraPeriodsModal = window.BackendCalendarExtraPeriodsModal || {};

(function (exports) {

    'use strict';

    function bindEventHandlers() {
        /**
         * Event: Manage extra Dialog Save Button "Click"
         *
         * Stores the extra period changes or inserts a new record.
         */
        $('#manage-extra #save-extra').on('click', function () {
            var $dialog = $('#manage-extra');
            $dialog.find('.has-error').removeClass('has-error');
            var start = $dialog.find('#extra-start').datetimepicker('getDate');
            var end = Date.parse($dialog.find('#extra-end').datetimepicker('getDate'));

            if (start.toString('HH:mm') > end.toString('HH:mm')) {
                // Start time is after end time - display message to user.
                $dialog.find('.modal-message')
                    .text(EALang.start_date_before_end_error)
                    .addClass('alert-danger')
                    .removeClass('hidden');

                $dialog.find('#extra-start, #extra-end').closest('.form-group').addClass('has-error');
                return;
            }

            // extra period records go to the appointments table.
            var extra = {
                start_datetime: start.toString('yyyy-MM-dd HH:mm'),
                end_datetime: start.toString('yyyy-MM-dd') + ' ' + end.toString('HH:mm'),
                id_users_provider: $('#extra-provider').val() // curr provider
            };

            //if ($dialog.find('#extra-id').val() !== '') {
            //    // Set the id value, only if we are editing an appointment.
            //    extra.id = $dialog.find('#extra-id').val();
            //}

            var successCallback = function (response) {
                // Display success message to the user.
                Backend.displayNotification(EALang.extra_period_saved);

                // Close the modal dialog and refresh the calendar appointments.
                $dialog.find('.alert').addClass('hidden');
                $dialog.modal('hide');

                var providerId = $('#extra-provider').val();
                var provider = GlobalVariables.availableProviders.filter(function (p) {
                    return p.id === providerId;
                })[0];
                var providerIdx = GlobalVariables.availableProviders.indexOf(provider);

                var extraWorkingPlan = jQuery.parseJSON(provider.settings.extra_working_plan);
                extraWorkingPlan[start.toString('yyyy-MM-dd')] = {
                    start: start.toString('HH:mm'),
                    end: end.toString('HH:mm'),
                    breaks: []
                };
                provider.settings.extra_working_plan = JSON.stringify(extraWorkingPlan);
                GlobalVariables.availableProviders[providerIdx] = provider;

                $('#select-filter-item').trigger('change');
            };

            var errorCallback = function (jqXHR, textStatus, errorThrown) {
                $dialog.find('.modal-message').text(EALang.service_communication_error);
                $dialog.find('.modal-message').addClass('alert-danger').removeClass('hidden');
            };

            BackendCalendarApi.saveExtraPeriod(extra, successCallback, errorCallback);
        });

        /**
         * Event: Manage extra Dialog Cancel Button "Click"
         *
         * Closes the dialog without saveing any changes to the database.
         */
        $('#manage-extra #cancel-extra').on('click', function () {
            $('#manage-extra').modal('hide');
        });

        /**
         * Event : Insert extra Time Period Button "Click"
         *
         * When the user clicks this button a popup dialog appears and the use can set a time period where
         * he cannot accept any appointments.
         */
        $('#insert-extra-period').on('click', function () {
            BackendCalendarExtraPeriodsModal.resetExtraDialog();
            var $dialog = $('#manage-extra');

            // Set the default datetime values.
            var start = new Date();
            start.addDays(1);
            start.set({'hour': 8});
            start.set({'minute': 30});

            if ($('.calendar-view').length === 0) {
                $dialog.find('#extra-provider')
                    .val($('#select-filter-item').val())
                    .closest('.form-group')
                    .hide();
            }

            $dialog.find('#extra-start').val(GeneralFunctions.formatDate(start, GlobalVariables.dateFormat, true));
            $dialog.find('#extra-end').val(GlobalVariables.timeFormat === 'regular' ? '8:00 PM' : '19:00');
            $dialog.find('.modal-header h3').text(EALang.new_extra_period_title);
            $dialog.modal('show');
        });
    }

    /**
     * Reset extra dialog form.
     *
     * Reset the "#manage-extra" dialog. Use this method to bring the dialog to the initial state
     * before it becomes visible to the user.
     */
    exports.resetExtraDialog = function () {
        var $dialog = $('#manage-extra');

        $dialog.find('#extra-id').val('');

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


        $dialog.find('#extra-start').datetimepicker({
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
        $dialog.find('#extra-start').val(GeneralFunctions.formatDate(start, GlobalVariables.dateFormat, true));
        $dialog.find('#extra-start').draggable();

        $dialog.find('#extra-end').timepicker({
            timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : GlobalVariables.timeFormat,
            currentText: EALang.now,
            closeText: EALang.close,
            timeOnlyTitle: EALang.select_time,
            timeText: EALang.time,
            hourText: EALang.hour,
            minuteText: EALang.minutes
        });
        $dialog.find('#extra-end').val(end);

        // Clear the extra notes field.
        $dialog.find('#extra-notes').val('');
    };

    exports.initialize = function () {
        var extraProvider = $('#extra-provider');

        for (var index in GlobalVariables.availableProviders) {
            var provider = GlobalVariables.availableProviders[index];

            extraProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
        }

        bindEventHandlers();
    };

})(window.BackendCalendarExtraPeriodsModal);
