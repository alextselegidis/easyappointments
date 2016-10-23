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
 * Backend Calendar Unavailabilities Modal
 *
 * This module implements the unavailabilities modal functionality.
 *
 * @module BackendCalendarUnavailabilitiesModal
 */
window.BackendCalendarUnavailabilitiesModal = window.BackendCalendarUnavailabilitiesModal || {};

(function(exports) {

    'use strict';

    function _bindEventHandlers() {
        /**
         * Event: Manage Unavailable Dialog Save Button "Click"
         *
         * Stores the unavailable period changes or inserts a new record.
         */
        $('#manage-unavailable #save-unavailable').click(function() {
            var $dialog = $('#manage-unavailable');
            var start = $dialog.find('#unavailable-start').datetimepicker('getDate');
            var end = Date.parse($dialog.find('#unavailable-end').datetimepicker('getDate'));

            if (start > end) {
                // Start time is after end time - display message to user.
                $dialog.find('.modal-message')
                    .text(EALang['start_date_before_end_error'])
                    .addClass('alert-danger')
                    .removeClass('hidden');
                return;
            }

            // Unavailable period records go to the appointments table.
            var unavailable = {
                start_datetime: start.toString('yyyy-MM-dd HH:mm'),
                end_datetime: end.toString('yyyy-MM-dd HH:mm'),
                notes: $dialog.find('#unavailable-notes').val(),
                id_users_provider: $('#unavailable-provider').val() // curr provider
            };

            if ($dialog.find('#unavailable-id').val() !== '') {
                // Set the id value, only if we are editing an appointment.
                unavailable.id = $dialog.find('#unavailable-id').val();
            }

            var successCallback = function(response) {
                if (response.exceptions) {
                    response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                    GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE,
                            GeneralFunctions.EXCEPTIONS_MESSAGE);
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));

                    $dialog.find('.modal-message')
                        .text(EALang['unexpected_issues_occurred'])
                        .addClass('alert-danger')
                        .removeClass('hidden');

                    return;
                }

                if (response.warnings) {
                    response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                    GeneralFunctions.displayMessageBox(GeneralFunctions.WARNINGS_TITLE,
                            GeneralFunctions.WARNINGS_MESSAGE);
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
                }

                // Display success message to the user.
                $dialog.find('.modal-message')
                    .text(EALang['unavailable_saved'])
                    .addClass('alert-success')
                    .removeClass('alert-danger hidden');

                // Close the modal dialog and refresh the calendar appointments after one second.
                setTimeout(function() {
                    $dialog.find('.alert').addClass('hidden');
                    $dialog.modal('hide');
                    $('#select-filter-item').trigger('change');
                }, 2000);
            };

            var errorCallback = function(jqXHR, textStatus, errorThrown) {
                GeneralFunctions.displayMessageBox('Communication Error', 'Unfortunately ' +
                        'the operation could not complete due to server communication errors.');

                $dialog.find('.modal-message').txt(EALang['service_communication_error']);
                $dialog.find('.modal-message').addClass('alert-danger').removeClass('hidden');
            };

            BackendCalendarApi.saveUnavailable(unavailable, successCallback, errorCallback);
        });

        /**
         * Event: Manage Unavailable Dialog Cancel Button "Click"
         *
         * Closes the dialog without saveing any changes to the database.
         */
        $('#manage-unavailable #cancel-unavailable').click(function() {
            $('#manage-unavailable').modal('hide');
        });

        /**
         * Event : Insert Unavailable Time Period Button "Click"
         *
         * When the user clicks this button a popup dialog appears and the use can set a time period where
         * he cannot accept any appointments.
         */
        $('#insert-unavailable').click(function() {
            BackendCalendarUnavailabilitiesModal.resetUnavailableDialog();
            var $dialog = $('#manage-unavailable');

            // Set the default datetime values.
            var start = new Date();
            var currentMin = parseInt(start.toString('mm'));

            if (currentMin > 0 && currentMin < 15) {
                start.set({ 'minute': 15 });
            } else if (currentMin > 15 && currentMin < 30) {
                start.set({ 'minute': 30 });
            } else if (currentMin > 30 && currentMin < 45) {
                start.set({ 'minute': 45 });
            } else {
                start.addHours(1).set({ 'minute': 0 });
            }

            if ($('.calendar-view').length === 0) {
                $dialog.find('#unavailable-provider').val($('#select-filter-item').val())
                    .parents('.form-group')
                    .hide();
            }

            $dialog.find('#unavailable-start').val(GeneralFunctions.formatDate(start, GlobalVariables.dateFormat, true));
            $dialog.find('#unavailable-end').val(GeneralFunctions.formatDate(start.addHours(1), GlobalVariables.dateFormat, true));
            $dialog.find('.modal-header h3').text(EALang['new_unavailable_title']);
            $dialog.modal('show');
        });
    }

    /**
     * Reset unavailable dialog form.
     *
     * Reset the "#manage-unavailable" dialog. Use this method to bring the dialog to the initial state
     * before it becomes visible to the user.
     */
    exports.resetUnavailableDialog = function() {
        var $dialog = $('#manage-unavailable');

        $dialog.find('#unavailable-id').val('');

        // Set default time values
        var start = GeneralFunctions.formatDate(new Date(), GlobalVariables.dateFormat, true);
        var end = GeneralFunctions.formatDate(new Date().addHours(1), GlobalVariables.dateFormat, true);
        var dateFormat;

        switch(GlobalVariables.dateFormat) {
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


        $dialog.find('#unavailable-start').datetimepicker({
            dateFormat: dateFormat,

            // Translation
            dayNames: [EALang['sunday'], EALang['monday'], EALang['tuesday'], EALang['wednesday'],
                    EALang['thursday'], EALang['friday'], EALang['saturday']],
            dayNamesShort: [EALang['sunday'].substr(0,3), EALang['monday'].substr(0,3),
                    EALang['tuesday'].substr(0,3), EALang['wednesday'].substr(0,3),
                    EALang['thursday'].substr(0,3), EALang['friday'].substr(0,3),
                    EALang['saturday'].substr(0,3)],
            dayNamesMin: [EALang['sunday'].substr(0,2), EALang['monday'].substr(0,2),
                    EALang['tuesday'].substr(0,2), EALang['wednesday'].substr(0,2),
                    EALang['thursday'].substr(0,2), EALang['friday'].substr(0,2),
                    EALang['saturday'].substr(0,2)],
            monthNames: [EALang['january'], EALang['february'], EALang['march'], EALang['april'],
                    EALang['may'], EALang['june'], EALang['july'], EALang['august'], EALang['september'],
                    EALang['october'], EALang['november'], EALang['december']],
            prevText: EALang['previous'],
            nextText: EALang['next'],
            currentText: EALang['now'],
            closeText: EALang['close'],
            timeOnlyTitle: EALang['select_time'],
            timeText: EALang['time'],
            hourText: EALang['hour'],
            minuteText: EALang['minutes'],
            firstDay: 1
        });
        $dialog.find('#unavailable-start').val(start);

        $dialog.find('#unavailable-end').datetimepicker({
            dateFormat: dateFormat,

            // Translation
            dayNames: [EALang['sunday'], EALang['monday'], EALang['tuesday'], EALang['wednesday'],
                    EALang['thursday'], EALang['friday'], EALang['saturday']],
            dayNamesShort: [EALang['sunday'].substr(0,3), EALang['monday'].substr(0,3),
                    EALang['tuesday'].substr(0,3), EALang['wednesday'].substr(0,3),
                    EALang['thursday'].substr(0,3), EALang['friday'].substr(0,3),
                    EALang['saturday'].substr(0,3)],
            dayNamesMin: [EALang['sunday'].substr(0,2), EALang['monday'].substr(0,2),
                    EALang['tuesday'].substr(0,2), EALang['wednesday'].substr(0,2),
                    EALang['thursday'].substr(0,2), EALang['friday'].substr(0,2),
                    EALang['saturday'].substr(0,2)],
            monthNames: [EALang['january'], EALang['february'], EALang['march'], EALang['april'],
                    EALang['may'], EALang['june'], EALang['july'], EALang['august'], EALang['september'],
                    EALang['october'], EALang['november'], EALang['december']],
            prevText: EALang['previous'],
            nextText: EALang['next'],
            currentText: EALang['now'],
            closeText: EALang['close'],
            timeOnlyTitle: EALang['select_time'],
            timeText: EALang['time'],
            hourText: EALang['hour'],
            minuteText: EALang['minutes'],
            firstDay: 1
        });
        $dialog.find('#unavailable-end').val(end);

        // Clear the unavailable notes field.
        $dialog.find('#unavailable-notes').val('');
    }

    exports.initialize = function() {
        var $unavailabilityProvider = $('#unavailable-provider'); 

        for (var index in GlobalVariables.availableProviders) {
            var provider = GlobalVariables.availableProviders[index]; 

            $unavailabilityProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
        }

        _bindEventHandlers();
    };

})(window.BackendCalendarUnavailabilitiesModal); 
