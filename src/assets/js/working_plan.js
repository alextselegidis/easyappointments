/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
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
        $.each(workingPlan, function (index, workingDay) {
            if (workingDay != null) {
                $('#' + index).prop('checked', true);
                $('#' + index + '-start').val(Date.parse(workingDay.start).toString(GlobalVariables.timeFormat  === 'regular' ? 'h:mm tt' : 'HH:mm').toUpperCase());
                $('#' + index + '-end').val(Date.parse(workingDay.end).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm').toUpperCase());

                // Add the day's breaks on the breaks table.
                $.each(workingDay.breaks, function (i, brk) {
                    var day = this.convertValueToDay(index);

                    var tr =
                        '<tr>' +
                        '<td class="break-day editable">' + GeneralFunctions.ucaseFirstLetter(day) + '</td>' +
                        '<td class="break-start editable">' + Date.parse(brk.start).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm').toUpperCase() + '</td>' +
                        '<td class="break-end editable">' + Date.parse(brk.end).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm').toUpperCase() + '</td>' +
                        '<td>' +
                        '<button type="button" class="btn btn-default btn-sm edit-break" title="' + EALang.edit + '">' +
                        '<span class="glyphicon glyphicon-pencil"></span>' +
                        '</button>' +
                        '<button type="button" class="btn btn-default btn-sm delete-break" title="' + EALang.delete + '">' +
                        '<span class="glyphicon glyphicon-remove"></span>' +
                        '</button>' +
                        '<button type="button" class="btn btn-default btn-sm save-break hidden" title="' + EALang.save + '">' +
                        '<span class="glyphicon glyphicon-ok"></span>' +
                        '</button>' +
                        '<button type="button" class="btn btn-default btn-sm cancel-break hidden" title="' + EALang.cancel + '">' +
                        '<span class="glyphicon glyphicon-ban-circle"></span>' +
                        '</button>' +
                        '</td>' +
                        '</tr>';
                    $('.breaks tbody').append(tr);
                }.bind(this));
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
            submit: '<button type="button" class="hidden submit-editable">Submit</button>',
            cancel: '<button type="button" class="hidden cancel-editable">Cancel</button>',
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
            submit: '<button type="button" class="hidden submit-editable">Submit</button>',
            cancel: '<button type="button" class="hidden cancel-editable">Cancel</button>',
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
        $('.working-plan input:checkbox').click(function () {
            var id = $(this).attr('id');

            if ($(this).prop('checked') == true) {
                $('#' + id + '-start').prop('disabled', false).val(GlobalVariables.timeFormat === 'regular' ? '9:00 AM' : '09:00');
                $('#' + id + '-end').prop('disabled', false).val(GlobalVariables.timeFormat === 'regular' ? '6:00 PM' : '18:00');
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
            var tr =
                '<tr>' +
                '<td class="break-day editable">' + EALang.sunday + '</td>' +
                '<td class="break-start editable">' + (GlobalVariables.timeFormat === 'regular' ? '9:00 AM' : '09:00') + '</td>' +
                '<td class="break-end editable">' + (GlobalVariables.timeFormat === 'regular' ? '10:00 AM' : '10:00') + '</td>' +
                '<td>' +
                '<button type="button" class="btn btn-default btn-sm edit-break" title="' + EALang.edit + '">' +
                '<span class="glyphicon glyphicon-pencil"></span>' +
                '</button>' +
                '<button type="button" class="btn btn-default btn-sm delete-break" title="' + EALang.delete + '">' +
                '<span class="glyphicon glyphicon-remove"></span>' +
                '</button>' +
                '<button type="button" class="btn btn-default btn-sm save-break hidden" title="' + EALang.save + '">' +
                '<span class="glyphicon glyphicon-ok"></span>' +
                '</button>' +
                '<button type="button" class="btn btn-default btn-sm cancel-break hidden" title="' + EALang.cancel + '">' +
                '<span class="glyphicon glyphicon-ban-circle"></span>' +
                '</button>' +
                '</td>' +
                '</tr>';
            $('.breaks').prepend(tr);

            // Bind editable and event handlers.
            tr = $('.breaks tr')[1];
            this.editableBreakDay($(tr).find('.break-day'));
            this.editableBreakTime($(tr).find('.break-start, .break-end'));
            $(tr).find('.edit-break').trigger('click');
            $('.add-break').prop('disabled', true);
        }.bind(this));

        /**
         * Event: Edit Break Button "Click"
         *
         * Enables the row editing for the "Breaks" table rows.
         */
        $(document).on('click', '.edit-break', function () {
            // Reset previous editable tds
            var $previousEdt = $(this).closest('table').find('.editable').get();
            $.each($previousEdt, function (index, editable) {
                if (editable.reset !== undefined) {
                    editable.reset();
                }
            });

            // Make all cells in current row editable.
            $(this).parent().parent().children().trigger('edit');
            $(this).parent().parent().find('.break-start input, .break-end input').timepicker({
                timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm TT' : 'HH:mm',
                currentText: EALang.now,
                closeText: EALang.close,
                timeOnlyTitle: EALang.select_time,
                timeText: EALang.time,
                hourText: EALang.hour,
                minuteText: EALang.minutes
            });
            $(this).parent().parent().find('.break-day select').focus();

            // Show save - cancel buttons.
            $(this).closest('table').find('.edit-break, .delete-break').addClass('hidden');
            $(this).parent().find('.save-break, .cancel-break').removeClass('hidden');
            $(this).closest('tr').find('select,input:text').addClass('form-control input-sm')

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
        $(document).on('click', '.cancel-break', function (e) {
            var element = e.target;
            var $modifiedRow = $(element).closest('tr');
            this.enableCancel = true;
            $modifiedRow.find('.cancel-editable').trigger('click');
            this.enableCancel = false;

            $(element).closest('table').find('.edit-break, .delete-break').removeClass('hidden');
            $modifiedRow.find('.save-break, .cancel-break').addClass('hidden');
            $('.add-break').prop('disabled', false);
        }.bind(this));

        /**
         * Event: Save Break Button "Click"
         *
         * Save the editable values and restore the table to its initial state.
         *
         * @param {jQuery.Event} e
         */
        $(document).on('click', '.save-break', function (e) {
            // Break's start time must always be prior to break's end.
            var element = e.target,
                $modifiedRow = $(element).closest('tr'),
                start = Date.parse($modifiedRow.find('.break-start input').val()),
                end = Date.parse($modifiedRow.find('.break-end input').val());

            if (start > end) {
                $modifiedRow.find('.break-end input').val(start.addHours(1).toString(GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm'));
            }

            this.enableSubmit = true;
            $modifiedRow.find('.editable .submit-editable').trigger('click');
            this.enableSubmit = false;

            $modifiedRow.find('.save-break, .cancel-break').addClass('hidden');
            $(element).closest('table').find('.edit-break, .delete-break').removeClass('hidden');
            $('.add-break').prop('disabled', false);
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
            if ($(checkbox).prop('checked') == true) {
                workingPlan[id] = {
                    start: Date.parse($('#' + id + '-start').val()).toString('HH:mm'),
                    end: Date.parse($('#' + id + '-end').val()).toString('HH:mm'),
                    breaks: []
                };

                $('.breaks tr').each(function (index, tr) {
                    var day = this.convertDayToValue($(tr).find('.break-day').text());

                    if (day == id) {
                        var start = $(tr).find('.break-start').text();
                        var end = $(tr).find('.break-end').text();

                        workingPlan[id].breaks.push({
                            start: Date.parse(start).toString('HH:mm'),
                            end: Date.parse(end).toString('HH:mm')
                        });
                    }

                    workingPlan[id].breaks.sort(function (break1, break2) {
                        // We can do a direct string comparison since we have time based on 24 hours clock.
                        return break1.start - break2.start;
                    });
                }.bind(this));
            } else {
                workingPlan[id] = null;
            }
        }.bind(this));

        return workingPlan;
    };

    /**
     * Enables or disables the timepicker functionality from the working plan input text fields.
     *
     * @param {Boolean} disabled (OPTIONAL = false) If true then the timepickers will be disabled.
     */
    WorkingPlan.prototype.timepickers = function (disabled) {
        disabled = disabled || false;

        if (disabled == false) {
            // Set timepickers where needed.
            $('.working-plan input:text').timepicker({
                timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm TT' : 'HH:mm',
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
