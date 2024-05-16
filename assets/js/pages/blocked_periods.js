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
 * Blocked-periods page.
 *
 * This module implements the functionality of the blocked-periods page.
 */
App.Pages.BlockedPeriods = (function () {
    const $blockedPeriods = $('#blocked-periods');
    const $filterBlockedPeriods = $('#filter-blocked-periods');
    const $id = $('#id');
    const $name = $('#name');
    const $startDateTime = $('#start-date-time');
    const $endDateTime = $('#end-date-time');
    const $notes = $('#notes');
    const moment = window.moment;

    let filterResults = {};
    let filterLimit = 20;
    let backupStartDateTimeObject = undefined;

    /**
     * Add the page event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Filter Blocked Periods Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $blockedPeriods.on('submit', '#filter-blocked-periods form', (event) => {
            event.preventDefault();
            const key = $('#filter-blocked-periods .key').val();
            $('.selected').removeClass('selected');
            App.Pages.BlockedPeriods.resetForm();
            App.Pages.BlockedPeriods.filter(key);
        });

        /**
         * Event: Filter Blocked-Periods Row "Click"
         *
         * Displays the selected row data on the right side of the page.
         *
         * @param {jQuery.Event} event
         */
        $blockedPeriods.on('click', '.blocked-period-row', (event) => {
            if ($('#filter-blocked-periods .filter').prop('disabled')) {
                $('#filter-blocked-periods .results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            const blockedPeriodId = $(event.currentTarget).attr('data-id');

            const blockedPeriod = filterResults.find(
                (filterResult) => Number(filterResult.id) === Number(blockedPeriodId),
            );

            App.Pages.BlockedPeriods.display(blockedPeriod);
            $('#filter-blocked-periods .selected').removeClass('selected');
            $(event.currentTarget).addClass('selected');
            $('#edit-blocked-period, #delete-blocked-period').prop('disabled', false);
        });

        /**
         * Event: Add Blocked-Period Button "Click"
         */
        $blockedPeriods.on('click', '#add-blocked-period', () => {
            App.Pages.BlockedPeriods.resetForm();
            $blockedPeriods.find('.add-edit-delete-group').hide();
            $blockedPeriods.find('.save-cancel-group').show();
            $blockedPeriods.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $blockedPeriods.find('.record-details .form-label span').prop('hidden', false);
            $filterBlockedPeriods.find('button').prop('disabled', true);
            $filterBlockedPeriods.find('.results').css('color', '#AAA');

            App.Utils.UI.setDateTimePickerValue($startDateTime, moment('00:00', 'HH:mm').toDate());
            App.Utils.UI.setDateTimePickerValue($endDateTime, moment('00:00', 'HH:mm').add(1, 'day').toDate());
        });

        /**
         * Event: Edit Blocked-Period Button "Click"
         */
        $blockedPeriods.on('click', '#edit-blocked-period', () => {
            $blockedPeriods.find('.add-edit-delete-group').hide();
            $blockedPeriods.find('.save-cancel-group').show();
            $blockedPeriods.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $blockedPeriods.find('.record-details .form-label span').prop('hidden', false);
            $filterBlockedPeriods.find('button').prop('disabled', true);
            $filterBlockedPeriods.find('.results').css('color', '#AAA');
        });

        /**
         * Event: Delete Blocked-Period Button "Click"
         */
        $blockedPeriods.on('click', '#delete-blocked-period', () => {
            const blockedPeriodId = $id.val();

            const buttons = [
                {
                    text: lang('cancel'),
                    click: (event, messageModal) => {
                        messageModal.hide();
                    },
                },
                {
                    text: lang('delete'),
                    click: (event, messageModal) => {
                        App.Pages.BlockedPeriods.remove(blockedPeriodId);
                        messageModal.hide();
                    },
                },
            ];

            App.Utils.Message.show(lang('delete_blocked_period'), lang('delete_record_prompt'), buttons);
        });

        /**
         * Event: Blocked period Save Button "Click"
         */
        $blockedPeriods.on('click', '#save-blocked-period', () => {
            const startDateTimeObject = App.Utils.UI.getDateTimePickerValue($startDateTime);
            const startDateTimeMoment = moment(startDateTimeObject);
            const endDateTimeObject = App.Utils.UI.getDateTimePickerValue($endDateTime);
            const endDateTimeMoment = moment(endDateTimeObject);

            const blockedPeriod = {
                name: $name.val(),
                start_datetime: startDateTimeMoment.format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: endDateTimeMoment.format('YYYY-MM-DD HH:mm:ss'),
                notes: $notes.val(),
            };

            if ($id.val() !== '') {
                blockedPeriod.id = $id.val();
            }

            if (!App.Pages.BlockedPeriods.validate()) {
                return;
            }

            App.Pages.BlockedPeriods.save(blockedPeriod);
        });

        /**
         * Event: Cancel Blocked-Period Button "Click"
         */
        $blockedPeriods.on('click', '#cancel-blocked-period', () => {
            const id = $id.val();
            App.Pages.BlockedPeriods.resetForm();
            if (id !== '') {
                App.Pages.BlockedPeriods.select(id, true);
            }
        });

        $blockedPeriods.on('focus', '#start-date-time', () => {
            backupStartDateTimeObject = App.Utils.UI.getDateTimePickerValue($startDateTime);
        });

        /**
         * Event: Start Date Time Input "Change"
         */
        $blockedPeriods.on('change', '#start-date-time', (event) => {
            const endDateTimeObject = App.Utils.UI.getDateTimePickerValue($endDateTime);

            if (!backupStartDateTimeObject || !endDateTimeObject) {
                return;
            }

            const endDateTimeMoment = moment(endDateTimeObject);
            const backupStartDateTimeMoment = moment(backupStartDateTimeObject);
            const diff = endDateTimeMoment.diff(backupStartDateTimeMoment);
            const newEndDateTimeMoment = endDateTimeMoment.clone().add(diff, 'milliseconds');
            App.Utils.UI.setDateTimePickerValue($endDateTime, newEndDateTimeMoment.toDate());
        });
    }

    /**
     * Filter blocked periods records.
     *
     * @param {String} keyword This key string is used to filter the blocked-period records.
     * @param {Number} selectId Optional, if set then after the filter operation the record with the given ID will be
     * selected (but not displayed).
     * @param {Boolean} show Optional (false), if true then the selected record will be displayed on the form.
     */
    function filter(keyword, selectId = null, show = false) {
        App.Http.BlockedPeriods.search(keyword, filterLimit).then((response) => {
            filterResults = response;

            $('#filter-blocked-periods .results').empty();

            response.forEach((blockedPeriod) => {
                $('#filter-blocked-periods .results')
                    .append(App.Pages.BlockedPeriods.getFilterHtml(blockedPeriod))
                    .append($('<hr/>'));
            });

            if (response.length === 0) {
                $('#filter-blocked-periods .results').append(
                    $('<em/>', {
                        'text': lang('no_records_found'),
                    }),
                );
            } else if (response.length === filterLimit) {
                $('<button/>', {
                    'type': 'button',
                    'class': 'btn btn-outline-secondary w-100 load-more text-center',
                    'text': lang('load_more'),
                    'click': () => {
                        filterLimit += 20;
                        App.Pages.BlockedPeriods.filter(keyword, selectId, show);
                    },
                }).appendTo('#filter-blocked-periods .results');
            }

            if (selectId) {
                App.Pages.BlockedPeriods.select(selectId, show);
            }
        });
    }

    /**
     * Save a blocked-period record to the database (via AJAX post).
     *
     * @param {Object} blockedPeriod Contains the blocked-period data.
     */
    function save(blockedPeriod) {
        App.Http.BlockedPeriods.save(blockedPeriod).then((response) => {
            App.Layouts.Backend.displayNotification(lang('blocked_period_saved'));
            App.Pages.BlockedPeriods.resetForm();
            $filterBlockedPeriods.find('.key').val('');
            App.Pages.BlockedPeriods.filter('', response.id, true);
        });
    }

    /**
     * Delete blocked-period record.
     *
     * @param {Number} id Record ID to be deleted.
     */
    function remove(id) {
        App.Http.BlockedPeriods.destroy(id).then(() => {
            App.Layouts.Backend.displayNotification(lang('blocked_period_deleted'));
            App.Pages.BlockedPeriods.resetForm();
            App.Pages.BlockedPeriods.filter($('#filter-blocked-periods .key').val());
        });
    }

    /**
     * Display a blocked-period record on the form.
     *
     * @param {Object} blockedPeriod Contains the blocked-period data.
     */
    function display(blockedPeriod) {
        $id.val(blockedPeriod.id);
        $name.val(blockedPeriod.name);
        App.Utils.UI.setDateTimePickerValue($startDateTime, new Date(blockedPeriod.start_datetime));
        App.Utils.UI.setDateTimePickerValue($endDateTime, new Date(blockedPeriod.end_datetime));
        $notes.val(blockedPeriod.notes);
    }

    /**
     * Validate blocked-period data before save (insert or update).
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $blockedPeriods.find('.is-invalid').removeClass('is-invalid');
        $blockedPeriods.find('.form-message').removeClass('alert-danger').hide();

        try {
            let missingRequired = false;

            $blockedPeriods.find('.required').each((index, fieldEl) => {
                if (!$(fieldEl).val()) {
                    $(fieldEl).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(lang('fields_are_required'));
            }

            const startDateTimeObject = App.Utils.UI.getDateTimePickerValue($startDateTime);
            const endDateTimeObject = App.Utils.UI.getDateTimePickerValue($endDateTime);

            if (startDateTimeObject >= endDateTimeObject) {
                $startDateTime.addClass('is-invalid');
                $endDateTime.addClass('is-invalid');
                throw new Error(lang('start_date_before_end_error'));
            }

            return true;
        } catch (error) {
            $blockedPeriods.find('.form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Bring the blocked-period form back to its initial state.
     */
    function resetForm() {
        $filterBlockedPeriods.find('.selected').removeClass('selected');
        $filterBlockedPeriods.find('button').prop('disabled', false);
        $filterBlockedPeriods.find('.results').css('color', '');

        $blockedPeriods.find('.add-edit-delete-group').show();
        $blockedPeriods.find('.save-cancel-group').hide();
        $blockedPeriods.find('.record-details').find('input, select, textarea').val('').prop('disabled', true);
        $blockedPeriods.find('.record-details .form-label span').prop('hidden', true);
        $('#edit-blocked-period, #delete-blocked-period').prop('disabled', true);

        $blockedPeriods.find('.record-details .is-invalid').removeClass('is-invalid');
        $blockedPeriods.find('.record-details .form-message').hide();

        backupStartDateTimeObject = undefined;
    }

    /**
     * Get the filter results row HTML code.
     *
     * @param {Object} blockedPeriod Contains the blocked-period data.
     *
     * @return {String} Returns the record HTML code.
     */
    function getFilterHtml(blockedPeriod) {
        return $('<div/>', {
            'class': 'blocked-period-row entry',
            'data-id': blockedPeriod.id,
            'html': [
                $('<strong/>', {
                    'text': blockedPeriod.name,
                }),
                $('<br/>'),
            ],
        });
    }

    /**
     * Select a specific record from the current filter results.
     *
     * If the blocked-period ID does not exist in the list then no record will be selected.
     *
     * @param {Number} id The record ID to be selected from the filter results.
     * @param {Boolean} show Optional (false), if true then the method will display the record on the form.
     */
    function select(id, show = false) {
        $filterBlockedPeriods.find('.selected').removeClass('selected');

        $filterBlockedPeriods.find('.blocked-period-row[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const blockedPeriod = filterResults.find((blockedPeriod) => Number(blockedPeriod.id) === Number(id));

            App.Pages.BlockedPeriods.display(blockedPeriod);

            $('#edit-blocked-period, #delete-blocked-period').prop('disabled', false);
        }
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        App.Pages.BlockedPeriods.resetForm();
        App.Pages.BlockedPeriods.filter('');
        App.Pages.BlockedPeriods.addEventListeners();
        App.Utils.UI.initializeDateTimePicker($startDateTime);
        App.Utils.UI.initializeDateTimePicker($endDateTime);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        filter,
        save,
        remove,
        validate,
        getFilterHtml,
        resetForm,
        display,
        select,
        addEventListeners,
    };
})();
