/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
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
    const $unavailabilityId = $('#unavailability-id');
    const $unavailabilityStart = $('#unavailability-start');
    const $unavailabilityEnd = $('#unavailability-end');
    const $unavailabilityProvider = $('#unavailability-provider');
    const $unavailabilityNotes = $('#unavailability-notes');
    const $saveUnavailability = $('#save-unavailability');
    const $insertUnavailability = $('#insert-unavailability');
    const $selectFilterItem = $('#select-filter-item');

    /**
     * Add the component event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Manage Unavailable Dialog Save Button "Click"
         *
         * Stores the unavailable period changes or inserts a new record.
         */
        $saveUnavailability.on('click', () => {
            $unavailabilitiesModal.find('.modal-message').addClass('d-none');
            $unavailabilitiesModal.find('.is-invalid').removeClass('is-invalid');

            const startMoment = moment($unavailabilityStart.datetimepicker('getDate'));

            if (!startMoment.isValid()) {
                $unavailabilityStart.addClass('is-invalid');
                return;
            }

            const endMoment = moment($unavailabilityEnd.datetimepicker('getDate'));

            if (!endMoment.isValid()) {
                $unavailabilityEnd.addClass('is-invalid');

                return;
            }

            if (startMoment.isAfter(endMoment)) {
                // Start time is after end time - display message to user.
                $unavailabilitiesModal
                    .find('.modal-message')
                    .text(App.Lang.start_date_before_end_error)
                    .addClass('alert-danger')
                    .removeClass('d-none');

                $unavailabilityStart.addClass('is-invalid');

                $unavailabilityEnd.addClass('is-invalid');

                return;
            }

            // Unavailable period records go to the appointments table.
            const unavailability = {
                start_datetime: startMoment.format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: endMoment.format('YYYY-MM-DD HH:mm:ss'),
                notes: $unavailabilitiesModal.find('#unavailability-notes').val(),
                id_users_provider: $('#unavailability-provider').val()
            };

            if ($unavailabilityId.val() !== '') {
                // Set the id value, only if we are editing an appointment.
                unavailability.id = $unavailabilityId.val();
            }

            const successCallback = () => {
                // Display success message to the user.
                App.Layouts.Backend.displayNotification(App.Lang.unavailable_saved);

                // Close the modal dialog and refresh the calendar appointments.
                $unavailabilitiesModal.find('.alert').addClass('d-none');

                $unavailabilitiesModal.modal('hide');

                $selectFilterItem.trigger('change');
            };

            App.Http.Calendar.saveUnavailable(unavailability, successCallback, null);
        });

        /**
         * Event : Insert Unavailable Time Period Button "Click"
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
                $unavailabilityProvider.val($selectFilterItem.val()).closest('.form-group').hide();
            }

            $unavailabilityStart.val(
                App.Utils.Date.format(startMoment.toDate(), App.Vars.date_format, App.Vars.time_format, true)
            );
            $unavailabilityEnd.val(
                App.Utils.Date.format(
                    startMoment.add(1, 'hour').toDate(),
                    App.Vars.date_format,
                    App.Vars.time_format,
                    true
                )
            );
            $dialog.find('.modal-header h3').text(App.Lang.new_unavailable_title);
            $dialog.modal('show');
        });
    }

    /**
     * Reset unavailable dialog form.
     *
     * Reset the "#unavailabilities-modal" dialog. Use this method to bring the dialog to the initial state
     * before it becomes visible to the user.
     */
    function resetModal() {
        $unavailabilityId.val('');

        // Set default time values
        const start = App.Utils.Date.format(moment().toDate(), App.Vars.date_format, App.Vars.time_format, true);

        const end = App.Utils.Date.format(
            moment().add(1, 'hour').toDate(),
            App.Vars.date_format,
            App.Vars.time_format,
            true
        );

        let dateFormat;

        switch (App.Vars.date_format) {
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

        const firstWeekday = App.Vars.first_weekday;

        const firstWeekdayId = App.Utils.Date.getWeekdayId(firstWeekday);

        $unavailabilityStart.datetimepicker({
            dateFormat: dateFormat,
            timeFormat: App.Vars.time_format === 'regular' ? 'h:mm tt' : 'HH:mm',

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
            firstDay: firstWeekdayId
        });
        $unavailabilityStart.val(start);

        $unavailabilityEnd.datetimepicker({
            dateFormat: dateFormat,
            timeFormat: App.Vars.time_format === 'regular' ? 'h:mm tt' : 'HH:mm',

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
            firstDay: firstWeekdayId
        });
        $unavailabilityEnd.val(end);

        // Clear the unavailable notes field.
        $unavailabilityNotes.val('');
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        for (const index in App.Vars.available_providers) {
            const provider = App.Vars.available_providers[index];

            $unavailabilityProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
        }

        addEventListeners();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        resetModal,
        initialize
    };
})();
