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
 * Working plan exceptions modal component.
 *
 * This module implements the working plan exceptions modal functionality.
 */
App.Components.WorkingPlanExceptionsModal = (function () {
    const $modal = $('#working-plan-exceptions-modal');
    const $startDate = $('#working-plan-exceptions-start-date');
    const $endDate = $('#working-plan-exceptions-end-date');
    const $startTime = $('#working-plan-exceptions-start-time');
    const $endTime = $('#working-plan-exceptions-end-time');
    const $breaks = $('#working-plan-exceptions-breaks');
    const $save = $('#working-plan-exceptions-save');
    const $addBreak = $('.working-plan-exceptions-add-break');
    const $isNonWorkingDay = $('#working-plan-exceptions-is-non-working-day');

    const moment = window.moment;

    let deferred = null;
    let enableSubmit = false;
    let enableCancel = false;

    /**
     * Reset the modal fields back to the original empty state.
     */
    function resetModal() {
        $addBreak.prop('disabled', false);
        $startDate.val('');
        $endDate.val('');
        $startTime.val('');
        $endTime.val('');
        $breaks.find('tbody').html(renderNoBreaksRow());
        $isNonWorkingDay.prop('checked', false);
        toggleFieldsByNonWorkingDay(false);
    }

    /**
     * Render a single table row as a placeholder to empty breaks table.
     */
    function renderNoBreaksRow() {
        return $(`
            <tr class="no-breaks-row">
                <td colspan="3" class="text-center">
                    ${lang('no_breaks')}
                </td>
            </tr>
        `);
    }

    /**
     * Toggle the state of the fields depending on the non-working day checkbox value.
     *
     * @param {Boolean} isNonWorkingDay
     */
    function toggleFieldsByNonWorkingDay(isNonWorkingDay) {
        $startTime.prop('disabled', isNonWorkingDay).toggleClass('text-decoration-line-through', isNonWorkingDay);
        $endTime.prop('disabled', isNonWorkingDay).toggleClass('text-decoration-line-through', isNonWorkingDay);
        $addBreak.prop('disabled', isNonWorkingDay);
        $breaks.find('button').prop('disabled', isNonWorkingDay);
        $breaks.toggleClass('text-decoration-line-through', isNonWorkingDay);
    }

    /**
     * Validate the modal form fields and return false if the validation fails.
     *
     * @returns {Boolean}
     */
    function validate() {
        $modal.find('.is-invalid').removeClass('is-invalid');

        const startDate = App.Utils.UI.getDateTimePickerValue($startDate);

        if (!startDate) {
            $startDate.addClass('is-invalid');
        }

        const endDate = App.Utils.UI.getDateTimePickerValue($endDate);

        if (!endDate) {
            $endDate.addClass('is-invalid');
        }

        // Validate that start date is before or equal to end date
        if (startDate && endDate && moment(startDate).isAfter(moment(endDate))) {
            $endDate.addClass('is-invalid');
        }

        if (!$isNonWorkingDay.prop('checked')) {
            const startTime = App.Utils.UI.getDateTimePickerValue($startTime);

            if (!startTime) {
                $startTime.addClass('is-invalid');
            }

            const endTime = App.Utils.UI.getDateTimePickerValue($endTime);

            if (!endTime) {
                $endTime.addClass('is-invalid');
            }
        }

        return !$modal.find('.is-invalid').length;
    }

    /**
     * Event: On Modal "Hidden"
     *
     * This event is used to automatically reset the modal back to the original state.
     */
    function onModalHidden() {
        resetModal();
    }

    /**
     * Serialize the entered break entries.
     *
     * @returns {Array}
     */
    function getBreaks() {
        const breaks = [];

        $breaks
            .find('tbody tr')
            .not('.no-breaks-row')
            .each((index, tr) => {
                const $tr = $(tr);

                if ($tr.find('input:text').length) {
                    return true;
                }

                const start = $tr.find('.working-plan-exceptions-break-start').text();
                const end = $tr.find('.working-plan-exceptions-break-end').text();

                breaks.push({
                    start: moment(start, vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm').format('HH:mm'),
                    end: moment(end, vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm').format('HH:mm'),
                });
            });

        // Sort breaks increasingly by hour within day
        breaks.sort((break1, break2) => {
            // We can do a direct string comparison since we have time based on 24 hours clock.
            return break1.start.localeCompare(break2.start);
        });

        return breaks;
    }

    /**
     * Event: On Save "Click"
     *
     * Serialize the entire working plan exception and resolved the promise so that external code can save it.
     */
    function onSaveClick() {
        if (!deferred) {
            return;
        }

        if (!validate()) {
            return;
        }

        const startDate = moment(App.Utils.UI.getDateTimePickerValue($startDate)).format('YYYY-MM-DD');
        const endDate = moment(App.Utils.UI.getDateTimePickerValue($endDate)).format('YYYY-MM-DD');

        const isNonWorkingDay = $isNonWorkingDay.prop('checked');

        const workingPlanException = {
            startDate: startDate,
            endDate: endDate,
            startTime: isNonWorkingDay ? null : moment(App.Utils.UI.getDateTimePickerValue($startTime)).format('HH:mm'),
            endTime: isNonWorkingDay ? null : moment(App.Utils.UI.getDateTimePickerValue($endTime)).format('HH:mm'),
            breaks: isNonWorkingDay ? [] : getBreaks(),
        };

        deferred.resolve(workingPlanException);

        $modal.modal('hide');

        resetModal();
    }

    /**
     * Enable the inline-editable table cell functionality for the provided target element..
     *
     * @param {jQuery} $target
     */
    function editableTimeCell($target) {
        $target.editable(
            (value) => {
                // Do not return the value because the user needs to press the "Save" button.
                return value;
            },
            {
                event: 'edit',
                height: '30px',
                submit: $('<button/>', {
                    'type': 'button',
                    'class': 'd-none submit-editable',
                    'text': lang('save'),
                }).get(0).outerHTML,
                cancel: $('<button/>', {
                    'type': 'button',
                    'class': 'd-none cancel-editable',
                    'text': lang('cancel'),
                }).get(0).outerHTML,
                onblur: 'ignore',
                onreset: () => {
                    if (!enableCancel) {
                        return false; // disable ESC button
                    }
                },
                onsubmit: () => {
                    if (!enableSubmit) {
                        return false; // disable Enter button
                    }
                },
            },
        );
    }

    function resetTimeSelection() {
        App.Utils.UI.setDateTimePickerValue($startTime, moment('08:00', 'HH:mm').toDate());
        App.Utils.UI.setDateTimePickerValue($endTime, moment('20:00', 'HH:mm').toDate());
    }

    /**
     * Open the modal and start adding a new working plan exception.
     *
     * @returns {*|jQuery.Deferred}
     */
    function add() {
        deferred = $.Deferred();

        App.Utils.UI.setDateTimePickerValue($startDate, new Date());
        App.Utils.UI.setDateTimePickerValue($endDate, new Date());

        resetTimeSelection();

        $isNonWorkingDay.prop('checked', false);

        $breaks.find('tbody').html(renderNoBreaksRow());

        $modal.modal('show');

        return deferred.promise();
    }

    /**
     * Modify the provided working plan exception.
     *
     * @param {Object} workingPlanException Contains startDate, endDate, startTime, endTime, breaks
     *
     * @return {*|jQuery.Deferred}
     */
    function edit(workingPlanException) {
        deferred = $.Deferred();

        const isNonWorkingDay = !workingPlanException.startTime;

        App.Utils.UI.setDateTimePickerValue($startDate, moment(workingPlanException.startDate, 'YYYY-MM-DD').toDate());
        App.Utils.UI.setDateTimePickerValue($endDate, moment(workingPlanException.endDate, 'YYYY-MM-DD').toDate());

        if (isNonWorkingDay === false) {
            App.Utils.UI.setDateTimePickerValue($startTime, moment(workingPlanException.startTime, 'HH:mm').toDate());
            App.Utils.UI.setDateTimePickerValue($endTime, moment(workingPlanException.endTime, 'HH:mm').toDate());

            if (!workingPlanException.breaks || !workingPlanException.breaks.length) {
                $breaks.find('tbody').html(renderNoBreaksRow());
            } else {
                $breaks.find('tbody').empty();
                workingPlanException.breaks.forEach((workingPlanExceptionBreak) => {
                    renderBreakRow(workingPlanExceptionBreak).appendTo($breaks.find('tbody'));
                });
            }

            editableTimeCell(
                $breaks.find('tbody .working-plan-exceptions-break-start, tbody .working-plan-exceptions-break-end'),
            );
        } else {
            App.Utils.UI.setDateTimePickerValue($startTime, moment('08:00', 'HH:mm').toDate());
            App.Utils.UI.setDateTimePickerValue($endTime, moment('20:00', 'HH:mm').toDate());
            $breaks.find('tbody').html(renderNoBreaksRow());
        }

        $isNonWorkingDay.prop('checked', isNonWorkingDay);

        toggleFieldsByNonWorkingDay(isNonWorkingDay);

        $modal.modal('show');

        return deferred.promise();
    }

    /**
     * Render a break table row based on the provided break period object.
     *
     * @param {Object} breakPeriod
     *
     * @return {jQuery}
     */
    function renderBreakRow(breakPeriod) {
        const timeFormat = vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm';

        return $('<tr/>', {
            'html': [
                $('<td/>', {
                    'class': 'working-plan-exceptions-break-start editable',
                    'text': moment(breakPeriod.start, 'HH:mm').format(timeFormat),
                }),
                $('<td/>', {
                    'class': 'working-plan-exceptions-break-end editable',
                    'text': moment(breakPeriod.end, 'HH:mm').format(timeFormat),
                }),
                $('<td/>', {
                    'html': [
                        $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-outline-secondary btn-sm me-2 working-plan-exceptions-edit-break',
                            'title': lang('edit'),
                            'html': [
                                $('<span/>', {
                                    'class': 'fas fa-edit',
                                }),
                            ],
                        }),
                        $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-outline-secondary btn-sm working-plan-exceptions-delete-break',
                            'title': lang('delete'),
                            'html': [
                                $('<span/>', {
                                    'class': 'fas fa-trash-alt',
                                }),
                            ],
                        }),
                        $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-outline-secondary btn-sm me-2 working-plan-exceptions-save-break d-none',
                            'title': lang('save'),
                            'html': [
                                $('<span/>', {
                                    'class': 'fas fa-check-circle',
                                }),
                            ],
                        }),
                        $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-outline-secondary btn-sm working-plan-exceptions-cancel-break d-none',
                            'title': lang('cancel'),
                            'html': [
                                $('<span/>', {
                                    'class': 'fas fa-ban',
                                }),
                            ],
                        }),
                    ],
                }),
            ],
        });
    }

    /**
     * Event: Add Break "Click"
     */
    function onAddBreakClick() {
        $breaks.find('.no-breaks-row').remove();

        const $newBreak = renderBreakRow({
            start: '12:00',
            end: '14:00',
        }).appendTo('#working-plan-exceptions-breaks tbody');

        // Bind editable and event handlers.
        editableTimeCell($newBreak.find('.working-plan-exceptions-break-start, .working-plan-exceptions-break-end'));
        $newBreak.find('.working-plan-exceptions-edit-break').trigger('click');
        $addBreak.prop('disabled', true);
    }

    /**
     * Event: Edit Break "Click"
     */
    function onEditBreakClick() {
        // Reset previous editable table cells.
        const $previousEdits = $(this).closest('table').find('.editable');

        $previousEdits.each((index, editable) => {
            if (editable.reset) {
                editable.reset();
            }
        });

        // Make all cells in current row editable.
        let $tr = $(this).closest('tr');
        $tr.children().trigger('edit');
        App.Utils.UI.initializeTimePicker(
            $tr.find('.working-plan-exceptions-break-start input, .working-plan-exceptions-break-end input'),
        );
        $(this).closest('tr').find('.working-plan-exceptions-break-start').focus();

        // Show save - cancel buttons.
        $tr = $(this).closest('tr');
        $tr.find('.working-plan-exceptions-edit-break, .working-plan-exceptions-delete-break').addClass('d-none');
        $tr.find('.working-plan-exceptions-save-break, .working-plan-exceptions-cancel-break').removeClass('d-none');
        $tr.find('select,input:text').addClass('form-control form-control-sm');

        $addBreak.prop('disabled', true);
    }

    /**
     * Event: Delete Break "Click"
     */
    function onDeleteBreakClick() {
        $(this).closest('tr').remove();
    }

    /**
     * Event: Save Break "Click"
     */
    function onSaveBreakClick() {
        // Break's start time must always be prior to break's end.
        const $tr = $(this).closest('tr');
        const start = moment(
            $tr.find('.working-plan-exceptions-break-start input').val(),
            vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm',
        );
        const end = moment(
            $tr.find('.working-plan-exceptions-break-end input').val(),
            vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm',
        );

        if (start > end) {
            $tr.find('.working-plan-exceptions-break-end input').val(
                start.add(1, 'hour').format(vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm'),
            );
        }

        enableSubmit = true;
        $tr.find('.editable .submit-editable').trigger('click');
        enableSubmit = false;

        $tr.find('.working-plan-exceptions-save-break, .working-plan-exceptions-cancel-break').addClass('d-none');
        $tr.closest('table')
            .find('.working-plan-exceptions-edit-break, .working-plan-exceptions-delete-break')
            .removeClass('d-none');
        $addBreak.prop('disabled', false);
    }

    /**
     * Event: Cancel Break "Click"
     */
    function onCancelBreakClick() {
        const $tr = $(this).closest('tr');
        enableCancel = true;
        $tr.find('.cancel-editable').trigger('click');
        enableCancel = false;

        $breaks
            .find('.working-plan-exceptions-edit-break, .working-plan-exceptions-delete-break')
            .removeClass('d-none');
        $tr.find('.working-plan-exceptions-save-break, .working-plan-exceptions-cancel-break').addClass('d-none');
        $addBreak.prop('disabled', false);
    }

    /**
     * Event: Is Non-Working Day "Change"
     */
    function onIsNonWorkingDayChange() {
        const isNonWorkingDay = $isNonWorkingDay.prop('checked');
        resetTimeSelection();
        toggleFieldsByNonWorkingDay(isNonWorkingDay);
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        App.Utils.UI.initializeDatePicker($startDate);
        App.Utils.UI.initializeDatePicker($endDate);
        App.Utils.UI.initializeTimePicker($startTime);
        App.Utils.UI.initializeTimePicker($endTime);

        $modal
            .on('hidden.bs.modal', onModalHidden)
            .on('click', '.working-plan-exceptions-add-break', onAddBreakClick)
            .on('click', '.working-plan-exceptions-edit-break', onEditBreakClick)
            .on('click', '.working-plan-exceptions-delete-break', onDeleteBreakClick)
            .on('click', '.working-plan-exceptions-save-break', onSaveBreakClick)
            .on('click', '.working-plan-exceptions-cancel-break', onCancelBreakClick);

        $save.on('click', onSaveClick);

        $isNonWorkingDay.on('change', onIsNonWorkingDayChange);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        add,
        edit,
    };
})();
