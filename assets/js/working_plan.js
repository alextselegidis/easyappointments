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
                                'class': 'work-start form-control input-sm'
                            })
                        ]
                    }),
                    $('<td/>', {
                        'html': [
                            $('<input/>', {
                                'id': index + '-end',
                                'class': 'work-start form-control input-sm'
                            })
                        ]
                    })
                ]
            })
                .appendTo('.working-plan tbody');

            if (workingDay) {
                $('#' + index).prop('checked', true);
                $('#' + index + '-start').val(Date.parse(workingDay.start).toString(timeFormat).toUpperCase());
                $('#' + index + '-end').val(Date.parse(workingDay.end).toString(timeFormat).toUpperCase());

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
                                'text': Date.parse(workingDayBreak.start).toString(timeFormat).toUpperCase()
                            }),
                            $('<td/>', {
                                'class': 'break-end editable',
                                'text': Date.parse(workingDayBreak.end).toString(timeFormat).toUpperCase()
                            }),
                            $('<td/>', {
                                'html': [
                                    $('<button/>', {
                                        'type': 'button',
                                        'class': 'btn btn-light btn-sm edit-break',
                                        'title': EALang.edit,
                                        'html': [
                                            $('<span/>', {
                                                'class': 'far fa-edit'
                                            })
                                        ]
                                    }),
                                    $('<button/>', {
                                        'type': 'button',
                                        'class': 'btn btn-light btn-sm delete-break',
                                        'title': EALang.delete,
                                        'html': [
                                            $('<span/>', {
                                                'class': 'far fa-trash-alt'
                                            })
                                        ]
                                    }),
                                    $('<button/>', {
                                        'type': 'button',
                                        'class': 'btn btn-light btn-sm save-break d-none',
                                        'title': EALang.save,
                                        'html': [
                                            $('<span/>', {
                                                'class': 'far fa-check-circle'
                                            })
                                        ]
                                    }),
                                    $('<button/>', {
                                        'type': 'button',
                                        'class': 'btn btn-light btn-sm cancel-break d-none',
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
        this.editableBreakDay($('.breaks .break-day'));
        this.editableBreakTime($('.breaks').find('.break-start, .break-end'));
    };

    /**
     * Setup the dom elements of a given extra working plan day.
     *
     * @param {Object} extraWorkingPlan Contains the extra working day.
     */
    WorkingPlan.prototype.setupExtraPeriods = function (extraWorkingPlan) {
        $.each(extraWorkingPlan, function (index, extraWorkingDay) {
            if (extraWorkingDay) {
                $('#' + index).prop('checked', true);
                $('#' + index + '-start').val(Date.parse(extraWorkingDay.start).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm').toUpperCase());
                $('#' + index + '-end').val(Date.parse(extraWorkingDay.end).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm').toUpperCase());

                var day = GeneralFunctions.formatDate(Date.parse(index), GlobalVariables.dateFormat, false);

                var timeFormat = GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm';

                $('<tr/>', {
                    'html': [
                        $('<td/>', {
                            'class': 'extra-day editable',
                            'text': day
                        }),
                        $('<td/>', {
                            'class': 'extra-start editable',
                            'text': Date.parse(extraWorkingDay.start).toString(timeFormat).toUpperCase()
                        }),
                        $('<td/>', {
                            'class': 'extra-end editable',
                            'text': Date.parse(extraWorkingDay.end).toString(timeFormat).toUpperCase()
                        }),
                        $('<td/>', {
                            'html': [
                                $('<button/>', {
                                    'type': 'button',
                                    'class': 'btn btn-light btn-sm edit-extra',
                                    'title': EALang.edit,
                                    'html': [
                                        $('<span/>', {
                                            'class': 'far fa-edit'
                                        })
                                    ]
                                }),
                                $('<button/>', {
                                    'type': 'button',
                                    'class': 'btn btn-light btn-sm delete-extra',
                                    'title': EALang.delete,
                                    'html': [
                                        $('<span/>', {
                                            'class': 'far fa-trash-alt'
                                        })
                                    ]
                                }),
                                $('<button/>', {
                                    'type': 'button',
                                    'class': 'btn btn-light btn-sm save-extra d-none',
                                    'title': EALang.save,
                                    'html': [
                                        $('<span/>', {
                                            'class': 'far fa-check-circle'
                                        })
                                    ]
                                }),
                                $('<button/>', {
                                    'type': 'button',
                                    'class': 'btn btn-light btn-sm cancel-extra d-none',
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
                    .appendTo('.extra-periods tbody');
            } else {
                $('#' + index).prop('checked', false);
                $('#' + index + '-start').prop('disabled', true);
                $('#' + index + '-end').prop('disabled', true);
            }
        }.bind(this));

        // Make break cells editable.
        this.editableBreakTime($('.extra-periods .extra-day'));
        this.editableBreakTime($('.extra-periods').find('.extra-start, .extra-end'));
    };

    /**
     * Enable editable break day.
     *
     * This method makes editable the break day cells.
     *
     * @param {Object} $selector The jquery selector ready for use.
     */
    WorkingPlan.prototype.editableBreakDay = function ($selector) {
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
    WorkingPlan.prototype.editableBreakTime = function ($selector) {
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
                'class': 'd-none cancek-editable',
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
     * Binds the event handlers for the working plan dom elements.
     */
    WorkingPlan.prototype.bindEventHandlers = function () {
        /**
         * Event: Day Checkbox "Click"
         *
         * Enable or disable the time selection for each day.
         */
        $('.working-plan tbody').on("click", "input:checkbox", function () {
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
        $('.add-break').click(function () {
            var timeFormat = GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm';

            var $newBreak = $('<tr/>', {
                'html': [
                    $('<td/>', {
                        'class': 'break-day editable',
                        'text': EALang.sunday
                    }),
                    $('<td/>', {
                        'class': 'break-start editable',
                        'text': '9:00 AM'
                    }),
                    $('<td/>', {
                        'class': 'break-end editable',
                        'text': '10:00 AM'
                    }),
                    $('<td/>', {
                        'html': [
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-light btn-sm edit-break',
                                'title': EALang.edit,
                                'html': [
                                    $('<span/>', {
                                        'class': 'far fa-edit'
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-light btn-sm delete-break',
                                'title': EALang.delete,
                                'html': [
                                    $('<span/>', {
                                        'class': 'far fa-trash-alt'
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-light btn-sm save-break d-none',
                                'title': EALang.save,
                                'html': [
                                    $('<span/>', {
                                        'class': 'far fa-check-circle'
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-light btn-sm cancel-break d-none',
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
            this.editableBreakDay($newBreak.find('.break-day'));
            this.editableBreakTime($newBreak.find('.break-start, .break-end'));
            $newBreak.find('.edit-break').trigger('click');
            $('.add-break').prop('disabled', true);
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
            $tr.find('select,input:text').addClass('form-control input-sm')

            $('.add-break').prop('disabled', true);
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
         * @param {jQuery.Event} e
         */
        $(document).on('click', '.cancel-break', function (event) {
            var element = event.target;
            var $modifiedRow = $(element).closest('tr');
            this.enableCancel = true;
            $modifiedRow.find('.cancel-editable').trigger('click');
            this.enableCancel = false;

            $(element).closest('table').find('.edit-break, .delete-break').removeClass('d-none');
            $modifiedRow.find('.save-break, .cancel-break').addClass('d-none');
            $('.add-break').prop('disabled', false);
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
                $modifiedRow.find('.break-end input').val(start.addHours(1).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm'));
            }

            this.enableSubmit = true;
            $modifiedRow.find('.editable .submit-editable').trigger('click');
            this.enableSubmit = false;

            $modifiedRow.find('.save-break, .cancel-break').addClass('d-none');
            $(element).closest('table').find('.edit-break, .delete-break').removeClass('d-none');
            $('.add-break').prop('disabled', false);

            // Refresh working plan to have the new break sorted in the break list.
            var workingPlan = this.get();
            this.setup(workingPlan);
        }.bind(this));

        /**
         * Event: Add Extra Period Button "Click"
         *
         * A new row is added on the table and the user can enter the new extra day
         * data. After that he can either press the save or cancel button.
         */
        $('.add-extra-periods').click(function () {
            var today = GeneralFunctions.formatDate(new Date(), GlobalVariables.dateFormat, false);

            var $newExtraPeriod = $('<tr/>', {
                'html': [
                    $('<td/>', {
                        'class': 'extra-day editable',
                        'text': today
                    }),
                    $('<td/>', {
                        'class': 'extra-start editable',
                        'text': '9:00 AM'
                    }),
                    $('<td/>', {
                        'class': 'extra-end editable',
                        'text': '10:00 AM'
                    }),
                    $('<td/>', {
                        'html': [
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-light btn-sm edit-extra',
                                'title': EALang.edit,
                                'html': [
                                    $('<span/>', {
                                        'class': 'far fa-edit'
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-light btn-sm delete-extra',
                                'title': EALang.delete,
                                'html': [
                                    $('<span/>', {
                                        'class': 'far fa-trash-alt'
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-light btn-sm save-extra d-none',
                                'title': EALang.save,
                                'html': [
                                    $('<span/>', {
                                        'class': 'far fa-check-circle'
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'type': 'button',
                                'class': 'btn btn-light btn-sm cancel-extra d-none',
                                'title': EALang.cancel,
                                'html': [
                                    $('<span/>', {
                                        'class': 'fas fa-ban'
                                    })
                                ]
                            })
                        ]
                    }),
                ]
            })
                .appendTo('.extra-periods tbody');

            // Bind editable and event handlers.
            this.editableBreakTime($newExtraPeriod.find('.extra-day'));
            this.editableBreakTime($newExtraPeriod.find('.extra-start, .extra-end'));
            $newExtraPeriod.find('.edit-extra').trigger('click');
            $('.add-extra-periods').prop('disabled', true);
        }.bind(this));

        /**
         * Event: Edit Extra Period Button "Click"
         *
         * Enables the row editing for the "Extra Period" table rows.
         */
        $(document).on('click', '.edit-extra', function () {
            // Reset previous editable table cells.
            var $previousEdits = $(this).closest('table').find('.editable');

            $previousEdits.each(function (index, editable) {
                if (editable.reset) {
                    editable.reset();
                }
            });

            // Make all cells in current row editable.
            $(this).parent().parent().children().trigger('edit');
            $(this).parent().parent().find('.extra-start input, .extra-end input').timepicker({
                timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm',
                currentText: EALang.now,
                closeText: EALang.close,
                timeOnlyTitle: EALang.select_time,
                timeText: EALang.time,
                hourText: EALang.hour,
                minuteText: EALang.minutes
            });

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

            $(this).parent().parent().find('.extra-day input').datetimepicker({
                dateFormat: dateFormat,

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
                firstDay: 1,
                showTimepicker: false
            });

            // Show save - cancel buttons.
            var $tr = $(this).closest('tr');
            $tr.find('.edit-extra, .delete-extra').addClass('d-none');
            $tr.find('.save-extra, .cancel-extra').removeClass('d-none');
            $tr.find('select,input:text').addClass('form-control input-sm')

            $('.add-extra-periods').prop('disabled', true);
        });

        /**
         * Event: Delete Extra Period Button "Click"
         *
         * Removes the current line from the "Extra Periods" table.
         */
        $(document).on('click', '.delete-extra', function () {
            $(this).parent().parent().remove();
        });

        /**
         * Event: Cancel Extra Period Button "Click"
         *
         * Bring the ".extra-period" table back to its initial state.
         *
         * @param {jQuery.Event} e
         */
        $(document).on('click', '.cancel-extra', function (event) {
            var element = event.target;
            var $modifiedRow = $(element).closest('tr');
            this.enableCancel = true;
            $modifiedRow.find('.cancel-editable').trigger('click');
            this.enableCancel = false;

            $(element).closest('table').find('.edit-extra, .delete-extra').removeClass('d-none');
            $modifiedRow.find('.save-extra, .cancel-extra').addClass('d-none');
            $('.add-extra-periods').prop('disabled', false);
        }.bind(this));

        /**
         * Event: Save Extra Period Button "Click"
         *
         * Save the editable values and restore the table to its initial state.
         *
         * @param {jQuery.Event} e
         */
        $(document).on('click', '.save-extra', function (event) {
            // Break's start time must always be prior to break's end.
            var element = event.target;
            var $modifiedRow = $(element).closest('tr');
            var start = Date.parse($modifiedRow.find('.extra-start input').val());
            var end = Date.parse($modifiedRow.find('.extra-end input').val());

            if (start > end) {
                $modifiedRow.find('.extra-end input').val(start.addHours(1).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm').toUpperCase());
            }

            this.enableSubmit = true;
            $modifiedRow.find('.editable .submit-editable').trigger('click');
            this.enableSubmit = false;

            $modifiedRow.find('.save-extra, .cancel-extra').addClass('d-none');
            $(element).closest('table').find('.edit-extra, .delete-extra').removeClass('d-none');
            $('.add-extra-periods').prop('disabled', false);
        }.bind(this));
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
     * Get the extra working plan settings.
     *
     * @return {Object} Returns the extra working plan settings object.
     */
    WorkingPlan.prototype.getExtraWP = function () {
        var extraWorkingPlan = {};
        $('.extra-periods tbody tr').each(function (index, tr) {

            var day = GeneralFunctions.ISO8601DateString($(tr).find('.extra-day').text(), GlobalVariables.dateFormat);

            var start = $(tr).find('.extra-start').text();
            var end = $(tr).find('.extra-end').text();

            extraWorkingPlan[day] = {
                start: Date.parse(start).toString('HH:mm'),
                end: Date.parse(end).toString('HH:mm'),
                breaks: []
            };
        }.bind(this));

        return extraWorkingPlan;
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
                        $(this).parent().parent().find('.work-end').val(start.addHours(1).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm'));
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
     * @param {String} value Day value could be like "Monday", "Tuesday" etc.
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
