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
 * Working plan utility.
 *
 * This module implements the functionality of working plans.
 */
App.Utils.WorkingPlan = (function () {
    const moment = window.moment;

    /**
     * Class WorkingPlan
     *
     * Contains the working plan functionality. The working plan DOM elements must be same
     * in every page this class is used.
     *
     * @class WorkingPlan
     */
    class WorkingPlan {
        /**
         * This flag is used when trying to cancel row editing. It is
         * true only whenever the user presses the cancel button.
         *
         * @type {Boolean}
         */
        enableCancel = false;

        /**
         * This flag determines whether the jeditables are allowed to submit. It is
         * true only whenever the user presses the save button.
         *
         * @type {Boolean}
         */
        enableSubmit = false;

        /**
         * Setup the dom elements of a given working plan.
         *
         * @param {Object} workingPlan Contains the working hours and breaks for each day of the week.
         */
        setup(workingPlan) {
            const weekDayId = App.Utils.Date.getWeekdayId(vars('first_weekday'));
            const workingPlanSorted = App.Utils.Date.sortWeekDictionary(workingPlan, weekDayId);

            $('.working-plan tbody').empty();
            $('.breaks tbody').empty();

            // Build working plan day list starting with the first weekday as set in the General settings
            const timeFormat = vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm';

            $.each(
                workingPlanSorted,
                function (index, workingDay) {
                    const day = this.convertValueToDay(index);

                    const dayDisplayName = App.Utils.String.upperCaseFirstLetter(day);

                    $('<tr/>', {
                        'html': [
                            $('<td/>', {
                                'html': [
                                    $('<div/>', {
                                        'class': 'checkbox form-check',
                                        'html': [
                                            $('<input/>', {
                                                'class': 'form-check-input',
                                                'type': 'checkbox',
                                                'id': index,
                                            }),
                                            $('<label/>', {
                                                'class': 'form-check-label',
                                                'text': dayDisplayName,
                                                'for': index,
                                            }),
                                        ],
                                    }),
                                ],
                            }),
                            $('<td/>', {
                                'html': [
                                    $('<input/>', {
                                        'id': index + '-start',
                                        'class': 'work-start form-control form-control-sm',
                                    }),
                                ],
                            }),
                            $('<td/>', {
                                'html': [
                                    $('<input/>', {
                                        'id': index + '-end',
                                        'class': 'work-end form-control form-control-sm',
                                    }),
                                ],
                            }),
                        ],
                    }).appendTo('.working-plan tbody');

                    if (workingDay) {
                        $('#' + index).prop('checked', true);
                        $('#' + index + '-start').val(
                            moment(workingDay.start, 'HH:mm').format(timeFormat).toLowerCase(),
                        );
                        $('#' + index + '-end').val(moment(workingDay.end, 'HH:mm').format(timeFormat).toLowerCase());

                        // Sort day's breaks according to the starting hour
                        workingDay.breaks.sort(function (break1, break2) {
                            // We can do a direct string comparison since we have time based on 24 hours clock.
                            return break1.start.localeCompare(break2.start);
                        });

                        workingDay.breaks.forEach(function (workingDayBreak) {
                            $('<tr/>', {
                                'html': [
                                    $('<td/>', {
                                        'class': 'break-day editable',
                                        'text': dayDisplayName,
                                    }),
                                    $('<td/>', {
                                        'class': 'break-start editable',
                                        'text': moment(workingDayBreak.start, 'HH:mm').format(timeFormat).toLowerCase(),
                                    }),
                                    $('<td/>', {
                                        'class': 'break-end editable',
                                        'text': moment(workingDayBreak.end, 'HH:mm').format(timeFormat).toLowerCase(),
                                    }),
                                    $('<td/>', {
                                        'html': [
                                            $('<button/>', {
                                                'type': 'button',
                                                'class': 'btn btn-outline-secondary btn-sm edit-break',
                                                'title': lang('edit'),
                                                'html': [
                                                    $('<span/>', {
                                                        'class': 'fas fa-edit',
                                                    }),
                                                ],
                                            }),
                                            $('<button/>', {
                                                'type': 'button',
                                                'class': 'btn btn-outline-secondary btn-sm delete-break',
                                                'title': lang('delete'),
                                                'html': [
                                                    $('<span/>', {
                                                        'class': 'fas fa-trash-alt',
                                                    }),
                                                ],
                                            }),
                                            $('<button/>', {
                                                'type': 'button',
                                                'class': 'btn btn-outline-secondary btn-sm save-break d-none',
                                                'title': lang('save'),
                                                'html': [
                                                    $('<span/>', {
                                                        'class': 'fas fa-check-circle',
                                                    }),
                                                ],
                                            }),
                                            $('<button/>', {
                                                'type': 'button',
                                                'class': 'btn btn-outline-secondary btn-sm cancel-break d-none',
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
                            }).appendTo('.breaks tbody');
                        });
                    } else {
                        $('#' + index).prop('checked', false);
                        $('#' + index + '-start').prop('disabled', true);
                        $('#' + index + '-end').prop('disabled', true);
                    }
                }.bind(this),
            );

            // Make break cells editable.
            this.editableDayCell($('.breaks .break-day'));
            this.editableTimeCell($('.breaks').find('.break-start, .break-end'));
        }

        /**
         * Setup the dom elements of a given working plan exception.
         *
         * @param {Object} workingPlanExceptions Contains the working plan exception.
         */
        setupWorkingPlanExceptions(workingPlanExceptions) {
            for (const date in workingPlanExceptions) {
                const workingPlanException = workingPlanExceptions[date];

                this.renderWorkingPlanExceptionRow(date, workingPlanException).appendTo(
                    '.working-plan-exceptions tbody',
                );
            }
        }

        /**
         * Enable editable break day.
         *
         * This method makes editable the break day cells.
         *
         * @param {Object} $selector The jquery selector ready for use.
         */
        editableDayCell($selector) {
            const weekDays = {};
            weekDays[lang('sunday')] = lang('sunday'); //'Sunday';
            weekDays[lang('monday')] = lang('monday'); //'Monday';
            weekDays[lang('tuesday')] = lang('tuesday'); //'Tuesday';
            weekDays[lang('wednesday')] = lang('wednesday'); //'Wednesday';
            weekDays[lang('thursday')] = lang('thursday'); //'Thursday';
            weekDays[lang('friday')] = lang('friday'); //'Friday';
            weekDays[lang('saturday')] = lang('saturday'); //'Saturday';

            $selector.editable(
                function (value) {
                    return value;
                },
                {
                    type: 'select',
                    data: weekDays,
                    event: 'edit',
                    width: '100px',
                    height: '30px',
                    submit: '<button type="button" class="d-none submit-editable">Submit</button>',
                    cancel: '<button type="button" class="d-none cancel-editable">Cancel</button>',
                    onblur: 'ignore',
                    onreset: function () {
                        if (!this.enableCancel) {
                            return false; // disable ESC button
                        }
                    }.bind(this),
                    onsubmit: function () {
                        if (!this.enableSubmit) {
                            return false; // disable Enter button
                        }
                    }.bind(this),
                },
            );
        }

        /**
         * Enable editable break time.
         *
         * This method makes editable the break time cells.
         *
         * @param {Object} $selector The jquery selector ready for use.
         */
        editableTimeCell($selector) {
            $selector.editable(
                function (value) {
                    // Do not return the value because the user needs to press the "Save" button.
                    return value;
                },
                {
                    event: 'edit',
                    width: '100px',
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
                    onreset: function () {
                        if (!this.enableCancel) {
                            return false; // disable ESC button
                        }
                    }.bind(this),
                    onsubmit: function () {
                        if (!this.enableSubmit) {
                            return false; // disable Enter button
                        }
                    }.bind(this),
                },
            );
        }

        /**
         * Render a working plan exception row.
         *
         * This method makes editable the break time cells.
         *
         * @param {String} date In "Y-m-d" format.
         * @param {Object} workingPlanException Contains exception information.
         */
        renderWorkingPlanExceptionRow(date, workingPlanException) {
            const timeFormat = vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm';

            const start = workingPlanException?.start;
            const end = workingPlanException?.end;

            return $('<tr/>', {
                'data': {
                    'date': date,
                    'workingPlanException': workingPlanException,
                },
                'html': [
                    $('<td/>', {
                        'class': 'working-plan-exception-date',
                        'text': App.Utils.Date.format(date, vars('date_format'), vars('time_format'), false),
                    }),
                    $('<td/>', {
                        'class': 'working-plan-exception--start',
                        'text': start ? moment(start, 'HH:mm').format(timeFormat).toLowerCase() : '-',
                    }),
                    $('<td/>', {
                        'class': 'working-plan-exception--end',
                        'text': end ? moment(end, 'HH:mm').format(timeFormat).toLowerCase() : '-',
                    }),
                    $('<td/>', {
                        'html': [
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-outline-secondary btn-sm edit-working-plan-exception',
                                'title': lang('edit'),
                                'html': [
                                    $('<span/>', {
                                        'class': 'fas fa-edit',
                                    }),
                                ],
                            }),
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-outline-secondary btn-sm delete-working-plan-exception',
                                'title': lang('delete'),
                                'html': [
                                    $('<span/>', {
                                        'class': 'fas fa-trash-alt',
                                    }),
                                ],
                            }),
                        ],
                    }),
                ],
            });
        }

        /**
         * Add the utility event listeners.
         */
        addEventListeners() {
            /**
             * Event: Day Checkbox "Click"
             *
             * Enable or disable the time selection for each day.
             *
             * @param {jQuery.Event} event
             */
            $('.working-plan tbody').on('click', 'input:checkbox', (event) => {
                const id = $(event.currentTarget).attr('id');

                if ($(event.currentTarget).prop('checked') === true) {
                    $('#' + id + '-start')
                        .prop('disabled', false)
                        .val('9:00 AM');
                    $('#' + id + '-end')
                        .prop('disabled', false)
                        .val('6:00 PM');
                } else {
                    $('#' + id + '-start')
                        .prop('disabled', true)
                        .val('');
                    $('#' + id + '-end')
                        .prop('disabled', true)
                        .val('');
                }
            });

            /**
             * Event: Add Break Button "Click"
             *
             * A new row is added on the table and the user can enter the new break
             * data. After that he can either press the save or cancel button.
             */
            $('.add-break').on('click', () => {
                const timeFormat = vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm';

                const $newBreak = $('<tr/>', {
                    'html': [
                        $('<td/>', {
                            'class': 'break-day editable',
                            'text': lang('sunday'),
                        }),
                        $('<td/>', {
                            'class': 'break-start editable',
                            'text': moment('12:00', 'HH:mm').format(timeFormat).toLowerCase(),
                        }),
                        $('<td/>', {
                            'class': 'break-end editable',
                            'text': moment('14:00', 'HH:mm').format(timeFormat).toLowerCase(),
                        }),
                        $('<td/>', {
                            'html': [
                                $('<button/>', {
                                    'type': 'button',
                                    'class': 'btn btn-outline-secondary btn-sm edit-break',
                                    'title': lang('edit'),
                                    'html': [
                                        $('<span/>', {
                                            'class': 'fas fa-edit',
                                        }),
                                    ],
                                }),
                                $('<button/>', {
                                    'type': 'button',
                                    'class': 'btn btn-outline-secondary btn-sm delete-break',
                                    'title': lang('delete'),
                                    'html': [
                                        $('<span/>', {
                                            'class': 'fas fa-trash-alt',
                                        }),
                                    ],
                                }),
                                $('<button/>', {
                                    'type': 'button',
                                    'class': 'btn btn-outline-secondary btn-sm save-break d-none',
                                    'title': lang('save'),
                                    'html': [
                                        $('<span/>', {
                                            'class': 'fas fa-check-circle',
                                        }),
                                    ],
                                }),
                                $('<button/>', {
                                    'type': 'button',
                                    'class': 'btn btn-outline-secondary btn-sm cancel-break d-none',
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
                }).appendTo('.breaks tbody');

                // Bind editable and event handlers.
                this.editableDayCell($newBreak.find('.break-day'));
                this.editableTimeCell($newBreak.find('.break-start, .break-end'));
                $newBreak.find('.edit-break').trigger('click');
            });

            /**
             * Event: Edit Break Button "Click"
             *
             * Enables the row editing for the "Breaks" table rows.
             *
             * @param {jQuery.Event} event
             */
            $(document).on('click', '.edit-break', (event) => {
                // Reset previous editable table cells.
                const $previousEdits = $(event.currentTarget).closest('table').find('.editable');

                $previousEdits.each(function (index, editable) {
                    if (editable.reset) {
                        editable.reset();
                    }
                });

                // Make all cells in current row editable.
                const $tr = $(event.currentTarget).closest('tr');

                $tr.children().trigger('edit');

                App.Utils.UI.initializeTimePicker($tr.find('.break-start input, .break-end input'));

                $tr.find('.break-day select').focus();

                // Show save - cancel buttons.
                $tr.find('.edit-break, .delete-break').addClass('d-none');
                $tr.find('.save-break, .cancel-break').removeClass('d-none');
                $tr.find('select,input:text').addClass('form-control form-control-sm');
            });

            /**
             * Event: Delete Break Button "Click"
             *
             * Removes the current line from the "Breaks" table.
             *
             * @param {jQuery.Event} event
             */
            $(document).on('click', '.delete-break', (event) => {
                $(event.currentTarget).closest('tr').remove();
            });

            /**
             * Event: Cancel Break Button "Click"
             *
             * Bring the ".breaks" table back to its initial state.
             *
             * @param {jQuery.Event} event
             */
            $(document).on('click', '.cancel-break', (event) => {
                const element = event.target;
                const $modifiedRow = $(element).closest('tr');
                this.enableCancel = true;
                $modifiedRow.find('.cancel-editable').trigger('click');
                this.enableCancel = false;

                $modifiedRow.find('.edit-break, .delete-break').removeClass('d-none');
                $modifiedRow.find('.save-break, .cancel-break').addClass('d-none');
            });

            /**
             * Event: Save Break Button "Click"
             *
             * Save the editable values and restore the table to its initial state.
             *
             * @param {jQuery.Event} event
             */
            $(document).on('click', '.save-break', (event) => {
                // Break's start time must always be prior to break's end.
                const element = event.target;

                const $modifiedRow = $(element).closest('tr');

                const startMoment = moment($modifiedRow.find('.break-start input').val(), 'HH:mm');

                const endMoment = moment($modifiedRow.find('.break-end input').val(), 'HH:mm');

                if (startMoment.isAfter(endMoment)) {
                    $modifiedRow.find('.break-end input').val(
                        startMoment
                            .add(1, 'hour')
                            .format(vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm')
                            .toLowerCase(),
                    );
                }

                this.enableSubmit = true;
                $modifiedRow.find('.editable .submit-editable').trigger('click');
                this.enableSubmit = false;

                $modifiedRow.find('.save-break, .cancel-break').addClass('d-none');
                $modifiedRow.find('.edit-break, .delete-break').removeClass('d-none');
            });

            /**
             * Event: Add Working Plan Exception Button "Click"
             *
             * A new row is added on the table and the user can enter the new working plan exception.
             */
            $(document).on('click', '.add-working-plan-exception', () => {
                App.Components.WorkingPlanExceptionsModal.add().done((date, workingPlanException) => {
                    let $tr = null;

                    $('.working-plan-exceptions tbody tr').each((index, tr) => {
                        if (date === $(tr).data('date')) {
                            $tr = $(tr);
                            return false;
                        }
                    });

                    let $newTr = this.renderWorkingPlanExceptionRow(date, workingPlanException);

                    if ($tr) {
                        $tr.replaceWith($newTr);
                    } else {
                        $newTr.appendTo('.working-plan-exceptions tbody');
                    }
                });
            });

            /**
             * Event: Edit working plan exception Button "Click"
             *
             * Enables the row editing for the "working plan exception" table rows.
             *
             * @param {jQuery.Event} event
             */
            $(document).on('click', '.edit-working-plan-exception', (event) => {
                const $tr = $(event.target).closest('tr');
                const date = $tr.data('date');
                const workingPlanException = $tr.data('workingPlanException');

                App.Components.WorkingPlanExceptionsModal.edit(date, workingPlanException).done(
                    (date, workingPlanException) => {
                        $tr.replaceWith(this.renderWorkingPlanExceptionRow(date, workingPlanException));
                    },
                );
            });

            /**
             * Event: Delete working plan exception Button "Click"
             *
             * Removes the current line from the "working plan exceptions" table.
             *
             * @param {jQuery.Event} event
             */
            $(document).on('click', '.delete-working-plan-exception', (event) => {
                $(event.currentTarget).closest('tr').remove();
            });
        }

        /**
         * Get the working plan settings.
         *
         * @return {Object} Returns the working plan settings object.
         */
        get() {
            const workingPlan = {};

            const timeFormat = vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm';

            $('.working-plan input:checkbox').each((index, checkbox) => {
                const id = $(checkbox).attr('id');
                if ($(checkbox).prop('checked') === true) {
                    workingPlan[id] = {
                        start: moment($('#' + id + '-start').val(), timeFormat).format('HH:mm'),
                        end: moment($('#' + id + '-end').val(), timeFormat).format('HH:mm'),
                        breaks: [],
                    };

                    $('.breaks tr').each((index, tr) => {
                        const day = this.convertDayToValue($(tr).find('.break-day').text());

                        if (day === id) {
                            const start = $(tr).find('.break-start').text();
                            const end = $(tr).find('.break-end').text();

                            workingPlan[id].breaks.push({
                                start: moment(start, vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm').format(
                                    'HH:mm',
                                ),
                                end: moment(end, vars('time_format') === 'regular' ? 'h:mm a' : 'HH:mm').format(
                                    'HH:mm',
                                ),
                            });
                        }
                    });

                    // Sort breaks increasingly by hour within day
                    workingPlan[id].breaks.sort((break1, break2) => {
                        // We can do a direct string comparison since we have time based on 24 hours clock.
                        return break1.start.localeCompare(break2.start);
                    });
                } else {
                    workingPlan[id] = null;
                }
            });

            return workingPlan;
        }

        /**
         * Get the working plan exceptions settings.
         *
         * @return {Object} Returns the working plan exceptions settings object.
         */
        getWorkingPlanExceptions() {
            const workingPlanExceptions = {};

            $('.working-plan-exceptions tbody tr').each((index, tr) => {
                const $tr = $(tr);
                const date = $tr.data('date');
                workingPlanExceptions[date] = $tr.data('workingPlanException');
            });

            return workingPlanExceptions;
        }

        /**
         * Enables or disables the timepicker functionality from the working plan input text fields.
         *
         * @param {Boolean} [disabled] If true then the timepickers will be disabled.
         */
        timepickers(disabled) {
            disabled = disabled || false;

            if (disabled === false) {
                App.Utils.UI.initializeTimePicker($('.working-plan input:text'), {
                    onChange: (selectedDates, dateStr, instance) => {
                        const startMoment = moment(selectedDates[0]);

                        const $workEnd = $(instance.input).closest('tr').find('.work-end');

                        const endMoment = moment(App.Utils.UI.getDateTimePickerValue($workEnd));

                        if (startMoment > endMoment) {
                            App.Utils.UI.setDateTimePickerValue($workEnd, startMoment.add(1, 'hour').toDate());
                        }
                    },
                });
            }
        }

        /**
         * Reset the current plan back to the company's default working plan.
         */
        reset() {}

        /**
         * This is necessary for translated days.
         *
         * @param {String} value Day value could be like "monday", "tuesday" etc.
         */
        convertValueToDay(value) {
            switch (value) {
                case 'sunday':
                    return lang('sunday');
                case 'monday':
                    return lang('monday');
                case 'tuesday':
                    return lang('tuesday');
                case 'wednesday':
                    return lang('wednesday');
                case 'thursday':
                    return lang('thursday');
                case 'friday':
                    return lang('friday');
                case 'saturday':
                    return lang('saturday');
            }
        }

        /**
         * This is necessary for translated days.
         *
         * @param {String} day Day value could be like "Monday", "Tuesday" etc.
         */
        convertDayToValue(day) {
            switch (day) {
                case lang('sunday'):
                    return 'sunday';
                case lang('monday'):
                    return 'monday';
                case lang('tuesday'):
                    return 'tuesday';
                case lang('wednesday'):
                    return 'wednesday';
                case lang('thursday'):
                    return 'thursday';
                case lang('friday'):
                    return 'friday';
                case lang('saturday'):
                    return 'saturday';
            }
        }
    }

    return WorkingPlan;
})();
