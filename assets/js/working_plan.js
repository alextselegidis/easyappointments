/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

(function () {

    'use strict';

    /**
     * Class WorkingPlan
     *
     * Contains the working plan functionality. The working plan DOM elements must be same
     * in every page this class is used.
     *
     * @class WorkingPlan
     */
    var WorkingPlan = function () {
        /**
         * This flag is used when trying to cancel row editing. It is
         * true only whenever the user presses the cancel button.
         *
         * @type {Boolean}
         */
        this.enableCancel = false;

        /**
         * This flag determines whether the jeditables are allowed to submit. It is
         * true only whenever the user presses the save button.
         *
         * @type {Boolean}
         */
        this.enableSubmit = false;
    };

    /**
     * Setup the dom elements of a given working plan.
     *
     * @param {Object} workingPlan Contains the working hours and breaks for each day of the week.
     */
    WorkingPlan.prototype.setup = function (workingPlan) {
        var weekDayId = GeneralFunctions.getWeekDayId(GlobalVariables.firstWeekday);
        var workingPlanSorted = GeneralFunctions.sortWeekDictionary(workingPlan, weekDayId);

        $('.working-plan tbody').empty();
        $('.breaks tbody').empty();

        // Build working plan day list starting with the first weekday as set in the General settings
        var timeFormat = GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm';

        $.each(workingPlanSorted, function (index, workingDay) {
            var day = this.convertValueToDay(index);

            var dayDisplayName = GeneralFunctions.upperCaseFirstLetter(day)

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
                                        'id': index
                                    }),
                                    $('<label/>', {
                                        'class': 'form-check-label',
                                        'text': dayDisplayName,
                                        'for': index
                                    }),

                                ]
                            })
                        ]
                    }),
                    $('<td/>', {
                        'html': [
                            $('<input/>', {
                                'id': index + '-start',
                                'class': 'work-start form-control form-control-sm'
                            })
                        ]
                    }),
                    $('<td/>', {
                        'html': [
                            $('<input/>', {
                                'id': index + '-end',
                                'class': 'work-start form-control form-control-sm'
                            })
                        ]
                    })
                ]
            })
                .appendTo('.working-plan tbody');

            if (workingDay) {
                $('#' + index).prop('checked', true);
                $('#' + index + '-start').val(Date.parse(workingDay.start).toString(timeFormat).toLowerCase());
                $('#' + index + '-end').val(Date.parse(workingDay.end).toString(timeFormat).toLowerCase());

                // Sort day's breaks according to the starting hour
                workingDay.breaks.sort(function (break1, break2) {
                    // We can do a direct string comparison since we have time based on 24 hours clock.
                    return (break1.start).localeCompare(break2.start);
                });

                workingDay.breaks.forEach(function (workingDayBreak) {
                    $('<tr/>', {
                        'html': [
                            $('<td/>', {
                                'class': 'break-day editable',
                                'text': dayDisplayName
                            }),
                            $('<td/>', {
                                'class': 'break-start editable',
                                'text': Date.parse(workingDayBreak.start).toString(timeFormat).toLowerCase()
                            }),
                            $('<td/>', {
                                'class': 'break-end editable',
                                'text': Date.parse(workingDayBreak.end).toString(timeFormat).toLowerCase()
                            }),
                            $('<td/>', {
                                'html': [
                                    $('<button/>', {
                                        'type': 'button',
                                        'class': 'btn btn-outline-secondary btn-sm edit-break',
                                        'title': EALang.edit,
                                        'html': [
                                            $('<span/>', {
                                                'class': 'fas fa-edit'
                                            })
                                        ]
                                    }),
                                    $('<button/>', {
                                        'type': 'button',
                                        'class': 'btn btn-outline-secondary btn-sm delete-break',
                                        'title': EALang.delete,
                                        'html': [
                                            $('<span/>', {
                                                'class': 'fas fa-trash-alt'
                                            })
                                        ]
                                    }),
                                    $('<button/>', {
                                        'type': 'button',
                                        'class': 'btn btn-outline-secondary btn-sm save-break d-none',
                                        'title': EALang.save,
                                        'html': [
                                            $('<span/>', {
                                                'class': 'fas fa-check-circle'
                                            })
                                        ]
                                    }),
                                    $('<button/>', {
                                        'type': 'button',
                                        'class': 'btn btn-outline-secondary btn-sm cancel-break d-none',
                                        'title': EALang.cancel,
                                        'html': [
                                            $('<span/>', {
                                                'class': 'fas fa-ban'
                                            })
                                        ]
                                    })
                                ]
                            })
                        ]
                    })
                        .appendTo('.breaks tbody');
                });
            } else {
                $('#' + index).prop('checked', false);
                $('#' + index + '-start').prop('disabled', true);
                $('#' + index + '-end').prop('disabled', true);
            }
        }.bind(this));

        // Make break cells editable.
        this.editableDayCell($('.breaks .break-day'));
        this.editableTimeCell($('.breaks').find('.break-start, .break-end'));
    };

    /**
     * Setup the dom elements of a given working plan exception.
     *
     * @param {Object} workingPlanExceptions Contains the working plan exception.
     */
    WorkingPlan.prototype.setupWorkingPlanExceptions = function (workingPlanExceptions) {
        for (var date in workingPlanExceptions) {
            var workingPlanException = workingPlanExceptions[date];

            this
                .renderWorkingPlanExceptionRow(date, workingPlanException)
                .appendTo('.working-plan-exceptions tbody');
        }
    };

    /**
     * Enable editable break day.
     *
     * This method makes editable the break day cells.
     *
     * @param {Object} $selector The jquery selector ready for use.
     */
    WorkingPlan.prototype.editableDayCell = function ($selector) {
        var weekDays = {};
        weekDays[EALang.sunday] = EALang.sunday; //'Sunday';
        weekDays[EALang.monday] = EALang.monday; //'Monday';
        weekDays[EALang.tuesday] = EALang.tuesday; //'Tuesday';
        weekDays[EALang.wednesday] = EALang.wednesday; //'Wednesday';
        weekDays[EALang.thursday] = EALang.thursday; //'Thursday';
        weekDays[EALang.friday] = EALang.friday; //'Friday';
        weekDays[EALang.saturday] = EALang.saturday; //'Saturday';

        $selector.editable(function (value, settings) {
            return value;
        }, {
            type: 'select',
            data: weekDays,
            event: 'edit',
            height: '30px',
            submit: '<button type="button" class="d-none submit-editable">Submit</button>',
            cancel: '<button type="button" class="d-none cancel-editable">Cancel</button>',
            onblur: 'ignore',
            onreset: function (settings, td) {
                if (!this.enableCancel) {
                    return false; // disable ESC button
                }
            }.bind(this),
            onsubmit: function (settings, td) {
                if (!this.enableSubmit) {
                    return false; // disable Enter button
                }
            }.bind(this)
        });
    };

    /**
     * Enable editable break time.
     *
     * This method makes editable the break time cells.
     *
     * @param {Object} $selector The jquery selector ready for use.
     */
    WorkingPlan.prototype.editableTimeCell = function ($selector) {
        $selector.editable(function (value, settings) {
            // Do not return the value because the user needs to press the "Save" button.
            return value;
        }, {
            event: 'edit',
            height: '30px',
            submit: $('<button/>', {
                'type': 'button',
                'class': 'd-none submit-editable',
                'text': EALang.save
            })
                .get(0)
                .outerHTML,
            cancel: $('<button/>', {
                'type': 'button',
                'class': 'd-none cancel-editable',
                'text': EALang.cancel
            })
                .get(0)
                .outerHTML,
            onblur: 'ignore',
            onreset: function (settings, td) {
                if (!this.enableCancel) {
                    return false; // disable ESC button
                }
            }.bind(this),
            onsubmit: function (settings, td) {
                if (!this.enableSubmit) {
                    return false; // disable Enter button
                }
            }.bind(this)
        });
    };

    /**
     * Enable editable break time.
     *
     * This method makes editable the break time cells.
     *
     * @param {String} date In "Y-m-d" format.
     * @param {Object} workingPlanException Contains exception information.
     */
    WorkingPlan.prototype.renderWorkingPlanExceptionRow = function (date, workingPlanException) {
        var timeFormat = GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm';

        return $('<tr/>', {
            'data': {
                'date': date,
                'workingPlanException': workingPlanException
            },
            'html': [
                $('<td/>', {
                    'class': 'working-plan-exception-date',
                    'text': GeneralFunctions.formatDate(date, GlobalVariables.dateFormat, false)
                }),
                $('<td/>', {
                    'class': 'working-plan-exception--start',
                    'text': Date.parse(workingPlanException.start).toString(timeFormat).toLowerCase()
                }),
                $('<td/>', {
                    'class': 'working-plan-exception--end',
                    'text': Date.parse(workingPlanException.end).toString(timeFormat).toLowerCase()
                }),
                $('<td/>', {
                    'html': [
                        $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-outline-secondary btn-sm edit-working-plan-exception',
                            'title': EALang.edit,
                            'html': [
                                $('<span/>', {
                                    'class': 'fas fa-edit'
                                })
                            ]
                        }),
                        $('<button/>', {
                            'type': 'button',
                            'class': 'btn btn-outline-secondary btn-sm delete-working-plan-exception',
                            'title': EALang.delete,
                            'html': [
                                $('<span/>', {
                                    'class': 'fas fa-trash-alt'
                                })
                            ]
                        }),
                    ]
                })
            ]
        });
    };

    /**
     * Binds the event handlers for the working plan dom elements.
     */
    WorkingPlan.prototype.bindEventHandlers = function () {
        /**
         * Event: Day Checkbox "Click"
         *
         * Enable or disable the time selection for each day.
         */
        $('.working-plan tbody').on('click', 'input:checkbox', function () {
            var id = $(this).attr('id');

            if ($(this).prop('checked') === true) {
                $('#' + id + '-start').prop('disabled', false).val('9:00 AM');
                $('#' + id + '-end').prop('disabled', false).val('6:00 PM');
            } else {
                $('#' + id + '-start').prop('disabled', true).val('');
                $('#' + id + '-end').prop('disabled', true).val('');
            }
        });

        /**
         * Event: Add Break Button "Click"
         *
         * A new row is added on the table and the user can enter the new break
         * data. After that he can either press the save or cancel button.
         */
        $('.add-break').on('click', function () {
            var timeFormat = GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm';

            var $newBreak = $('<tr/>', {
                'html': [
                    $('<td/>', {
                        'class': 'break-day editable',
                        'text': EALang.sunday
                    }),
                    $('<td/>', {
                        'class': 'break-start editable',
                        'text': Date.parse('12:00:00').toString(timeFormat).toLowerCase()
                    }),
                    $('<td/>', {
                        'class': 'break-end editable',
                        'text': Date.parse('14:00:00').toString(timeFormat).toLowerCase()
                    }),
                    $('<td/>', {
                        'html': [
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-outline-secondary btn-sm edit-break',
                                'title': EALang.edit,
                                'html': [
                                    $('<span/>', {
                                        'class': 'fas fa-edit'
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-outline-secondary btn-sm delete-break',
                                'title': EALang.delete,
                                'html': [
                                    $('<span/>', {
                                        'class': 'fas fa-trash-alt'
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-outline-secondary btn-sm save-break d-none',
                                'title': EALang.save,
                                'html': [
                                    $('<span/>', {
                                        'class': 'fas fa-check-circle'
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-outline-secondary btn-sm cancel-break d-none',
                                'title': EALang.cancel,
                                'html': [
                                    $('<span/>', {
                                        'class': 'fas fa-ban'
                                    })
                                ]
                            })
                        ]
                    })
                ]
            })
                .appendTo('.breaks tbody');

            // Bind editable and event handlers.
            this.editableDayCell($newBreak.find('.break-day'));
            this.editableTimeCell($newBreak.find('.break-start, .break-end'));
            $newBreak.find('.edit-break').trigger('click');
        }.bind(this));

        /**
         * Event: Edit Break Button "Click"
         *
         * Enables the row editing for the "Breaks" table rows.
         */
        $(document).on('click', '.edit-break', function () {
            // Reset previous editable table cells.
            var $previousEdits = $(this).closest('table').find('.editable');

            $previousEdits.each(function (index, editable) {
                if (editable.reset) {
                    editable.reset();
                }
            });

            // Make all cells in current row editable.
            $(this).parent().parent().children().trigger('edit');
            $(this).parent().parent().find('.break-start input, .break-end input').timepicker({
                timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm',
                currentText: EALang.now,
                closeText: EALang.close,
                timeOnlyTitle: EALang.select_time,
                timeText: EALang.time,
                hourText: EALang.hour,
                minuteText: EALang.minutes
            });
            $(this).parent().parent().find('.break-day select').focus();

            // Show save - cancel buttons.
            var $tr = $(this).closest('tr');
            $tr.find('.edit-break, .delete-break').addClass('d-none');
            $tr.find('.save-break, .cancel-break').removeClass('d-none');
            $tr.find('select,input:text').addClass('form-control form-control-sm')
        });

        /**
         * Event: Delete Break Button "Click"
         *
         * Removes the current line from the "Breaks" table.
         */
        $(document).on('click', '.delete-break', function () {
            $(this).parent().parent().remove();
        });

        /**
         * Event: Cancel Break Button "Click"
         *
         * Bring the ".breaks" table back to its initial state.
         *
         * @param {jQuery.Event} event
         */
        $(document).on('click', '.cancel-break', function (event) {
            var element = event.target;
            var $modifiedRow = $(element).closest('tr');
            this.enableCancel = true;
            $modifiedRow.find('.cancel-editable').trigger('click');
            this.enableCancel = false;

            $modifiedRow.find('.edit-break, .delete-break').removeClass('d-none');
            $modifiedRow.find('.save-break, .cancel-break').addClass('d-none');
        }.bind(this));

        /**
         * Event: Save Break Button "Click"
         *
         * Save the editable values and restore the table to its initial state.
         *
         * @param {jQuery.Event} e
         */
        $(document).on('click', '.save-break', function (event) {
            // Break's start time must always be prior to break's end.
            var element = event.target;
            var $modifiedRow = $(element).closest('tr');
            var start = Date.parse($modifiedRow.find('.break-start input').val());
            var end = Date.parse($modifiedRow.find('.break-end input').val());

            if (start > end) {
                $modifiedRow.find('.break-end input').val(start.addHours(1).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm').toLowerCase());
            }

            this.enableSubmit = true;
            $modifiedRow.find('.editable .submit-editable').trigger('click');
            this.enableSubmit = false;

            $modifiedRow.find('.save-break, .cancel-break').addClass('d-none');
            $modifiedRow.find('.edit-break, .delete-break').removeClass('d-none');
        }.bind(this));

        /**
         * Event: Add Working Plan Exception Button "Click"
         *
         * A new row is added on the table and the user can enter the new working plan exception.
         */
        $(document).on('click', '.add-working-plan-exception', function () {
            WorkingPlanExceptionsModal
                .add()
                .done(function (date, workingPlanException) {
                    var $tr = null;

                    $('.working-plan-exceptions tbody tr').each(function (index, tr) {
                        if (date === $(tr).data('date')) {
                            $tr = $(tr);
                            return false;
                        }
                    });

                    var $newTr = this.renderWorkingPlanExceptionRow(date, workingPlanException);

                    if ($tr) {
                        $tr.replaceWith($newTr);
                    } else {
                        $newTr.appendTo('.working-plan-exceptions tbody');
                    }
                }.bind(this));
        }.bind(this));

        /**
         * Event: Edit working plan exception Button "Click"
         *
         * Enables the row editing for the "working plan exception" table rows.
         *
         * @param {jQuery.Event} event
         */
        $(document).on('click', '.edit-working-plan-exception', function (event) {
            var $tr = $(event.target).closest('tr');
            var date = $tr.data('date');
            var workingPlanException = $tr.data('workingPlanException');

            WorkingPlanExceptionsModal
                .edit(date, workingPlanException)
                .done(function (date, workingPlanException) {
                    $tr.replaceWith(
                        this.renderWorkingPlanExceptionRow(date, workingPlanException)
                    );
                }.bind(this));
        }.bind(this));

        /**
         * Event: Delete working plan exception Button "Click"
         *
         * Removes the current line from the "working plan exceptions" table.
         */
        $(document).on('click', '.delete-working-plan-exception', function () {
            $(this).closest('tr').remove();
        });
    };

    /**
     * Get the working plan settings.
     *
     * @return {Object} Returns the working plan settings object.
     */
    WorkingPlan.prototype.get = function () {
        var workingPlan = {};

        $('.working-plan input:checkbox').each(function (index, checkbox) {
            var id = $(checkbox).attr('id');
            if ($(checkbox).prop('checked') === true) {
                workingPlan[id] = {
                    start: Date.parse($('#' + id + '-start').val()).toString('HH:mm'),
                    end: Date.parse($('#' + id + '-end').val()).toString('HH:mm'),
                    breaks: []
                };

                $('.breaks tr').each(function (index, tr) {
                    var day = this.convertDayToValue($(tr).find('.break-day').text());

                    if (day === id) {
                        var start = $(tr).find('.break-start').text();
                        var end = $(tr).find('.break-end').text();

                        workingPlan[id].breaks.push({
                            start: Date.parse(start).toString('HH:mm'),
                            end: Date.parse(end).toString('HH:mm')
                        });
                    }
                }.bind(this));

                // Sort breaks increasingly by hour within day
                workingPlan[id].breaks.sort(function (break1, break2) {
                    // We can do a direct string comparison since we have time based on 24 hours clock.
                    return (break1.start).localeCompare(break2.start);
                });
            } else {
                workingPlan[id] = null;
            }
        }.bind(this));

        return workingPlan;
    };

    /**
     * Get the working plan exceptions settings.
     *
     * @return {Object} Returns the working plan exceptions settings object.
     */
    WorkingPlan.prototype.getWorkingPlanExceptions = function () {
        var workingPlanExceptions = {};

        $('.working-plan-exceptions tbody tr').each(function (index, tr) {
            var $tr = $(tr);
            var date = $tr.data('date');
            workingPlanExceptions[date] = $tr.data('workingPlanException');
        });

        return workingPlanExceptions;
    };

    /**
     * Enables or disables the timepicker functionality from the working plan input text fields.
     *
     * @param {Boolean} [disabled] If true then the timepickers will be disabled.
     */
    WorkingPlan.prototype.timepickers = function (disabled) {
        disabled = disabled || false;

        if (disabled === false) {
            // Set timepickers where needed.
            $('.working-plan input:text').timepicker({
                timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm',
                currentText: EALang.now,
                closeText: EALang.close,
                timeOnlyTitle: EALang.select_time,
                timeText: EALang.time,
                hourText: EALang.hour,
                minuteText: EALang.minutes,

                onSelect: function (datetime, inst) {
                    // Start time must be earlier than end time.
                    var start = Date.parse($(this).parent().parent().find('.work-start').val()),
                        end = Date.parse($(this).parent().parent().find('.work-end').val());

                    if (start > end) {
                        $(this).parent().parent().find('.work-end').val(start.addHours(1).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm').toLowerCase());
                    }
                }
            });
        } else {
            $('.working-plan input').timepicker('destroy');
        }
    };

    /**
     * Reset the current plan back to the company's default working plan.
     */
    WorkingPlan.prototype.reset = function () {

    };

    /**
     * This is necessary for translated days.
     *
     * @param {String} value Day value could be like "monday", "tuesday" etc.
     */
    WorkingPlan.prototype.convertValueToDay = function (value) {
        switch (value) {
            case 'sunday':
                return EALang.sunday;
            case 'monday':
                return EALang.monday;
            case 'tuesday':
                return EALang.tuesday;
            case 'wednesday':
                return EALang.wednesday;
            case 'thursday':
                return EALang.thursday;
            case 'friday':
                return EALang.friday;
            case 'saturday':
                return EALang.saturday;
        }
    };

    /**
     * This is necessary for translated days.
     *
     * @param {String} day Day value could be like "Monday", "Tuesday" etc.
     */
    WorkingPlan.prototype.convertDayToValue = function (day) {
        switch (day) {
            case EALang.sunday:
                return 'sunday';
            case EALang.monday:
                return 'monday';
            case EALang.tuesday:
                return 'tuesday';
            case EALang.wednesday:
                return 'wednesday';
            case EALang.thursday:
                return 'thursday';
            case EALang.friday:
                return 'friday';
            case EALang.saturday:
                return 'saturday';
        }
    };

    window.WorkingPlan = WorkingPlan;

})();
