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
    const $date = $('#working-plan-exceptions-date');
    const $start = $('#working-plan-exceptions-start');
    const $end = $('#working-plan-exceptions-end');
    const $breaks = $('#working-plan-exceptions-breaks');
    const $save = $('#working-plan-exceptions-save');
    const $addBreak = $('.working-plan-exceptions-add-break');
    let deferred = null;
    let enableSubmit = false;
    let enableCancel = false;

    /**
     * Reset the modal fields back to the original empty state.
     */
    function resetModal() {
        $addBreak.prop('disabled', false);
        $date.val('');
        $start.val('');
        $end.val('');
        $breaks.find('tbody').empty();
    }

    /**
     * Validate the modal form fields and return false if the validation fails.
     *
     * @returns {Boolean}
     */
    function validate() {
        $modal.find('.is-invalid').removeClass('is-invalid');

        const date = $date[0]._flatpickr.selectedDates[0];

        if (!date) {
            $date.addClass('is-invalid');
        }

        const start = $start[0]._flatpickr.selectedDates[0];

        if (!start) {
            $start.addClass('is-invalid');
        }

        const end = $end[0]._flatpickr.selectedDates[0];

        if (!end) {
            $end.addClass('is-invalid');
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

        $breaks.find('tbody tr').each((index, tr) => {
            const $tr = $(tr);

            if ($tr.find('input:text').length) {
                return true;
            }

            const start = $tr.find('.working-plan-exceptions-break-start').text();
            const end = $tr.find('.working-plan-exceptions-break-end').text();

            breaks.push({
                start: moment(start, vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm').format('HH:mm'),
                end: moment(end, vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm').format('HH:mm')
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

        const date = moment($date[0]._flatpickr.selectedDates[0]).format('YYYY-MM-DD');

        const workingPlanException = {
            start: moment($start[0]._flatpickr.selectedDates[0]).format('HH:mm'),
            end: moment($end[0]._flatpickr.selectedDates[0]).format('HH:mm'),
            breaks: getBreaks()
        };

        deferred.resolve(date, workingPlanException);

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
                    'text': lang('save')
                }).get(0).outerHTML,
                cancel: $('<button/>', {
                    'type': 'button',
                    'class': 'd-none cancel-editable',
                    'text': lang('cancel')
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
                }
            }
        );
    }

    /**
     * Open the modal and start adding a new working plan exception.
     *
     * @returns {*|jQuery.Deferred}
     */
    function add() {
        deferred = $.Deferred();

        $date[0]._flatpickr.setDate(new Date());
        $start[0]._flatpickr.setDate(moment('08:00', 'HH:mm').toDate());
        $end[0]._flatpickr.setDate(moment('20:00', 'HH:mm').toDate());

        $modal.modal('show');

        return deferred.promise();
    }

    /**
     * Modify the provided working plan exception for the selected date.
     *
     * @param {String} date
     * @param {Object} workingPlanException
     *
     * @return {*|jQuery.Deferred}
     */
    function edit(date, workingPlanException) {
        deferred = $.Deferred();

        $date[0]._flatpickr.setDate(moment(date, 'YYYY-MM-DD').toDate());
        $start[0]._flatpickr.setDate(moment(workingPlanException.start, 'HH:mm').toDate());
        $end[0]._flatpickr.setDate(moment(workingPlanException.end, 'HH:mm').toDate());

        workingPlanException.breaks.forEach((workingPlanExceptionBreak) => {
            renderBreakRow(workingPlanExceptionBreak).appendTo($breaks.find('tbody'));
        });

        editableTimeCell(
            $breaks.find('tbody .working-plan-exceptions-break-start, tbody .working-plan-exceptions-break-end')
        );

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
                    'text': moment(breakPeriod.start, 'HH:mm').format(timeFormat)
                }),
                $('<td/>', {
                    'class': 'working-plan-exceptions-break-end editable',
                    'text': moment(breakPeriod.end, 'HH:mm').format(timeFormat)
                }),
                $('<td/>', {
                    'html': [
                        $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-outline-secondary btn-sm me-2 working-plan-exceptions-edit-break',
                            'title': lang('edit'),
                            'html': [
                                $('<span/>', {
                                    'class': 'fas fa-edit'
                                })
                            ]
                        }),
                        $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-outline-secondary btn-sm working-plan-exceptions-delete-break',
                            'title': lang('delete'),
                            'html': [
                                $('<span/>', {
                                    'class': 'fas fa-trash-alt'
                                })
                            ]
                        }),
                        $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-outline-secondary btn-sm me-2 working-plan-exceptions-save-break d-none',
                            'title': lang('save'),
                            'html': [
                                $('<span/>', {
                                    'class': 'fas fa-check-circle'
                                })
                            ]
                        }),
                        $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-outline-secondary btn-sm working-plan-exceptions-cancel-break d-none',
                            'title': lang('cancel'),
                            'html': [
                                $('<span/>', {
                                    'class': 'fas fa-ban'
                                })
                            ]
                        })
                    ]
                })
            ]
        });
    }

    /**
     * Event: Add Break "Click"
     */
    function onAddBreakClick() {
        const $newBreak = renderBreakRow({
            start: '12:00',
            end: '14:00'
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
        App.Utils.UI.initializeTimepicker(
            $tr.find('.working-plan-exceptions-break-start input, .working-plan-exceptions-break-end input')
        );
        $(this).closest('tr').find('.working-plan-exceptions-break-start').focus();

        // Show save - cancel buttons.
        $tr = $(this).closest('tr');
        $tr.find('.working-plan-exceptions-edit-break, .working-plan-exceptions-delete-break').addClass('d-none');
        $tr.find('.working-plan-exceptions-save-break, .working-plan-exceptions-cancel-break').removeClass('d-none');
        $tr.find('select,input:text').addClass('form-control input-sm');

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
            vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm'
        );
        const end = moment(
            $tr.find('.working-plan-exceptions-break-end input').val(),
            vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm'
        );

        if (start > end) {
            $tr.find('.working-plan-exceptions-break-end input').val(
                start.add(1, 'hour').format(vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm')
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
     * Initialize the module.
     */
    function initialize() {
        App.Utils.UI.initializeDatepicker($date);
        App.Utils.UI.initializeTimepicker($start);
        App.Utils.UI.initializeTimepicker($end);

        $modal
            .on('hidden.bs.modal', onModalHidden)
            .on('click', '.working-plan-exceptions-add-break', onAddBreakClick)
            .on('click', '.working-plan-exceptions-edit-break', onEditBreakClick)
            .on('click', '.working-plan-exceptions-delete-break', onDeleteBreakClick)
            .on('click', '.working-plan-exceptions-save-break', onSaveBreakClick)
            .on('click', '.working-plan-exceptions-cancel-break', onCancelBreakClick);

        $save.on('click', onSaveClick);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        add,
        edit
    };
})();
