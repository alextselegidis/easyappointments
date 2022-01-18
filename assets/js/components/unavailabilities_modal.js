/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Unavailabilities modal component.
 *
 * This module implements the unavailabilities modal functionality.
 *
 * Old Name: BackendCalendarUnavailabilityEventsModal
 */
App.Components.UnavailabilitiesModal = (function () {
    const $unavailabilitiesModal = $('#unavailabilities-modal');
    const $id = $('#unavailability-id');
    const $startDatetime = $('#unavailability-start');
    const $endDatetime = $('#unavailability-end');
    const $selectProvider = $('#unavailability-provider');
    const $notes = $('#unavailability-notes');
    const $saveUnavailability = $('#save-unavailability');
    const $insertUnavailability = $('#insert-unavailability');
    const $selectFilterItem = $('#select-filter-item');
    const $reloadAppointments = $('#reload-appointments');

    /**
     * Add the component event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Manage Unavailability Dialog Save Button "Click"
         *
         * Stores the unavailability period changes or inserts a new record.
         */
        $saveUnavailability.on('click', () => {
            $unavailabilitiesModal.find('.modal-message').addClass('d-none');
            $unavailabilitiesModal.find('.is-invalid').removeClass('is-invalid');

            const startMoment = moment($startDatetime.datetimepicker('getDate'));

            if (!startMoment.isValid()) {
                $startDatetime.addClass('is-invalid');
                return;
            }

            const endMoment = moment($endDatetime.datetimepicker('getDate'));

            if (!endMoment.isValid()) {
                $endDatetime.addClass('is-invalid');

                return;
            }

            if (startMoment.isAfter(endMoment)) {
                // Start time is after end time - display message to user.
                $unavailabilitiesModal
                    .find('.modal-message')
                    .text(lang('start_date_before_end_error'))
                    .addClass('alert-danger')
                    .removeClass('d-none');

                $startDatetime.addClass('is-invalid');

                $endDatetime.addClass('is-invalid');

                return;
            }

            // Unavailability period records go to the appointments table.
            const unavailability = {
                start_datetime: startMoment.format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: endMoment.format('YYYY-MM-DD HH:mm:ss'),
                notes: $unavailabilitiesModal.find('#unavailability-notes').val(),
                id_users_provider: $('#unavailability-provider').val()
            };

            if ($id.val() !== '') {
                // Set the id value, only if we are editing an appointment.
                unavailability.id = $id.val();
            }

            const successCallback = () => {
                // Display success message to the user.
                App.Layouts.Backend.displayNotification(lang('unavailability_saved'));

                // Close the modal dialog and refresh the calendar appointments.
                $unavailabilitiesModal.find('.alert').addClass('d-none');

                $unavailabilitiesModal.modal('hide');

                $reloadAppointments.trigger('change');
            };

            App.Http.Calendar.saveUnavailability(unavailability, successCallback, null);
        });

        /**
         * Event : Insert Unavailability Time Period Button "Click"
         *
         * When the user clicks this button a popup dialog appears and the use can set a time period where
         * he cannot accept any appointments.
         */
        $insertUnavailability.on('click', () => {
            resetModal();

            const $dialog = $('#unavailabilities-modal');

            // Set the default datetime values.
            const startMoment = moment();

            const currentMin = parseInt(startMoment.format('mm'));

            if (currentMin > 0 && currentMin < 15) {
                startMoment.set({minutes: 15});
            } else if (currentMin > 15 && currentMin < 30) {
                startMoment.set({minutes: 30});
            } else if (currentMin > 30 && currentMin < 45) {
                startMoment.set({minutes: 45});
            } else {
                startMoment.add(1, 'hour').set({minutes: 0});
            }

            if ($('.calendar-view').length === 0) {
                $selectProvider.val($selectFilterItem.val()).closest('.form-group').hide();
            }

            $startDatetime.val(
                App.Utils.Date.format(startMoment.toDate(), vars('date_format'), vars('time_format'), true)
            );
            $endDatetime.val(
                App.Utils.Date.format(
                    startMoment.add(1, 'hour').toDate(),
                    vars('date_format'),
                    vars('time_format'),
                    true
                )
            );
            $dialog.find('.modal-header h3').text(lang('new_unavailability_title'));
            $dialog.modal('show');
        });
    }

    /**
     * Reset unavailability dialog form.
     *
     * Reset the "#unavailabilities-modal" dialog. Use this method to bring the dialog to the initial state
     * before it becomes visible to the user.
     */
    function resetModal() {
        $id.val('');

        // Set default time values
        const start = App.Utils.Date.format(moment().toDate(), vars('date_format'), vars('time_format'), true);

        const end = App.Utils.Date.format(
            moment().add(1, 'hour').toDate(),
            vars('date_format'),
            vars('time_format'),
            true
        );

        let dateFormat;

        switch (vars('date_format')) {
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

        const firstWeekday = vars('first_weekday');

        const firstWeekdayId = App.Utils.Date.getWeekdayId(firstWeekday);

        $startDatetime.datetimepicker({
            dateFormat: dateFormat,
            timeFormat: vars('time_format') === 'regular' ? 'h:mm tt' : 'HH:mm',

            // Translation
            dayNames: [
                lang('sunday'),
                lang('monday'),
                lang('tuesday'),
                lang('wednesday'),
                lang('thursday'),
                lang('friday'),
                lang('saturday')
            ],
            dayNamesShort: [
                lang('sunday').substr(0, 3),
                lang('monday').substr(0, 3),
                lang('tuesday').substr(0, 3),
                lang('wednesday').substr(0, 3),
                lang('thursday').substr(0, 3),
                lang('friday').substr(0, 3),
                lang('saturday').substr(0, 3)
            ],
            dayNamesMin: [
                lang('sunday').substr(0, 2),
                lang('monday').substr(0, 2),
                lang('tuesday').substr(0, 2),
                lang('wednesday').substr(0, 2),
                lang('thursday').substr(0, 2),
                lang('friday').substr(0, 2),
                lang('saturday').substr(0, 2)
            ],
            monthNames: [
                lang('january'),
                lang('february'),
                lang('march'),
                lang('april'),
                lang('may'),
                lang('june'),
                lang('july'),
                lang('august'),
                lang('september'),
                lang('october'),
                lang('november'),
                lang('december')
            ],
            prevText: lang('previous'),
            nextText: lang('next'),
            currentText: lang('now'),
            closeText: lang('close'),
            timeOnlyTitle: lang('select_time'),
            timeText: lang('time'),
            hourText: lang('hour'),
            minuteText: lang('minutes'),
            firstDay: firstWeekdayId
        });
        $startDatetime.val(start);

        $endDatetime.datetimepicker({
            dateFormat: dateFormat,
            timeFormat: vars('time_format') === 'regular' ? 'h:mm tt' : 'HH:mm',

            // Translation
            dayNames: [
                lang('sunday'),
                lang('monday'),
                lang('tuesday'),
                lang('wednesday'),
                lang('thursday'),
                lang('friday'),
                lang('saturday')
            ],
            dayNamesShort: [
                lang('sunday').substr(0, 3),
                lang('monday').substr(0, 3),
                lang('tuesday').substr(0, 3),
                lang('wednesday').substr(0, 3),
                lang('thursday').substr(0, 3),
                lang('friday').substr(0, 3),
                lang('saturday').substr(0, 3)
            ],
            dayNamesMin: [
                lang('sunday').substr(0, 2),
                lang('monday').substr(0, 2),
                lang('tuesday').substr(0, 2),
                lang('wednesday').substr(0, 2),
                lang('thursday').substr(0, 2),
                lang('friday').substr(0, 2),
                lang('saturday').substr(0, 2)
            ],
            monthNames: [
                lang('january'),
                lang('february'),
                lang('march'),
                lang('april'),
                lang('may'),
                lang('june'),
                lang('july'),
                lang('august'),
                lang('september'),
                lang('october'),
                lang('november'),
                lang('december')
            ],
            prevText: lang('previous'),
            nextText: lang('next'),
            currentText: lang('now'),
            closeText: lang('close'),
            timeOnlyTitle: lang('select_time'),
            timeText: lang('time'),
            hourText: lang('hour'),
            minuteText: lang('minutes'),
            firstDay: firstWeekdayId
        });
        $endDatetime.val(end);

        // Clear the unavailability notes field.
        $notes.val('');
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        for (const index in vars('available_providers')) {
            const provider = vars('available_providers')[index];

            $selectProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
        }

        addEventListeners();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        resetModal,
        initialize
    };
})();
