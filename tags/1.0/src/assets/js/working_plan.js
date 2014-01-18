/**
 * Contains the working plan functionality. The working plan DOM elements must be same 
 * in every page this class is used.
 * 
 * @class WorkingPlan
 */
var WorkingPlan = function() {
    /**
     * This flag is used when trying to cancel row editing. It is
     * true only whenever the user presses the cancel button.
     * 
     * @type {bool}
     */
    this.enableCancel = false;
            
    /**
     * This flag determines whether the jeditables are allowed to submit. It is  
     * true only whenever the user presses the save button.
     * 
     * @type {bool}
     */
    this.enableSubmit = false;
};

/**
 * Setup the dom elements of a given working plan. 
 * 
 * @param {object} workingPlan Contains the working hours and breaks for each day of the week.
 */
WorkingPlan.prototype.setup = function(workingPlan) {
    $.each(workingPlan, function(index, workingDay) {
        if (workingDay != null) {
            $('#' + index).prop('checked', true);
            $('#' + index + '-start').val(workingDay.start);
            $('#' + index + '-end').val(workingDay.end);

            // Add the day's breaks on the breaks table.
            $.each(workingDay.breaks, function(i, brk) {
                var day = WorkingPlan.prototype.convertValueToDay(index);
                
                var tr = 
                        '<tr>' + 
                            '<td class="break-day editable">' + GeneralFunctions.ucaseFirstLetter(day) + '</td>' +
                            '<td class="break-start editable">' + brk.start + '</td>' +
                            '<td class="break-end editable">' + brk.end + '</td>' +
                            '<td>' + 
                                '<button type="button" class="btn edit-break" title="' + EALang['edit'] + '">' +
                                    '<i class="icon-pencil"></i>' +
                                '</button>' +
                                '<button type="button" class="btn delete-break" title="' + EALang['delete'] + '">' +
                                    '<i class="icon-remove"></i>' +
                                '</button>' +
                                '<button type="button" class="btn save-break hidden" title="' + EALang['save'] + '">' +
                                    '<i class="icon-ok"></i>' +
                                '</button>' +
                                '<button type="button" class="btn cancel-break hidden" title="' + EALang['cancel'] + '">' +
                                    '<i class="icon-ban-circle"></i>' +
                                '</button>' +
                            '</td>' +
                        '</tr>';
                $('.breaks').append(tr);
            });
        } else {
            $('#' + index).prop('checked', false);
            $('#' + index + '-start').prop('disabled', true);
            $('#' + index + '-end').prop('disabled', true);
        }
    });
    
    // Make break cells editable.
    WorkingPlan.prototype.editableBreakDay($('.breaks .break-day'));
    WorkingPlan.prototype.editableBreakTime($('.breaks').find('.break-start, .break-end'));
};

/**
 * This method makes editable the break day cells. 
 * 
 * @param {object} $selector The jquery selector ready for use.
 */
WorkingPlan.prototype.editableBreakDay = function($selector) {
    var weekDays = {};
    weekDays[EALang['monday']] = EALang['monday']; //'Monday';
    weekDays[EALang['tuesday']] = EALang['tuesday']; //'Tuesday';
    weekDays[EALang['wednesday']] = EALang['wednesday']; //'Wednesday';
    weekDays[EALang['thursday']] = EALang['thursday']; //'Thursday';
    weekDays[EALang['friday']] = EALang['friday']; //'Friday';
    weekDays[EALang['saturday']] = EALang['saturday']; //'Saturday';
    weekDays[EALang['sunday']] = EALang['sunday']; //'Sunday';

    $selector.editable(function(value, settings) {
        return value;
    }, {
        'type': 'select',
        'data': weekDays,
        // 'data': '{ "Monday": "Monday", "Tuesday": "Tuesday", "Wednesday": "Wednesday", '
        //         + '"Thursday": "Thursday", "Friday": "Friday", "Saturday": "Saturday", '
        //         + '"Sunday": "Sunday", "selected": "Monday"}',
        'event': 'edit',
        'height': '30px',
        'submit': '<button type="button" class="hidden submit-editable">Submit</button>',
        'cancel': '<button type="button" class="hidden cancel-editable">Cancel</button>',
        'onblur': 'ignore',
        'onreset': function(settings, td) {
            if (!WorkingPlan.prototype.enableCancel) return false; // disable ESC button
        },
        'onsubmit': function(settings, td) {
            if (!WorkingPlan.prototype.enableSubmit) return false; // disable Enter button
        }
    });
};

/**
 * This method makes editable the break time cells. 
 * 
 * @param {object} $selector The jquery selector ready for use.
 */
WorkingPlan.prototype.editableBreakTime = function($selector) {
    $selector.editable(function(value, settings) {
        // Do not return the value because the user needs to press the "Save" button.
        return value;
    }, {
        'event': 'edit',
        'height': '25px',
        'submit': '<button type="button" class="hidden submit-editable">Submit</button>',
        'cancel': '<button type="button" class="hidden cancel-editable">Cancel</button>',
        'onblur': 'ignore',
        'onreset': function(settings, td) {
            if (!WorkingPlan.prototype.enableCancel) return false; // disable ESC button
        },
        'onsubmit': function(settings, td) {
            if (!WorkingPlan.prototype.enableSubmit) return false; // disable Enter button
        }
    });
};

/**
 * Binds the event handlers for the working plan dom elements.
 */
WorkingPlan.prototype.bindEventHandlers = function() {
    /**
     * Event: Day Checkbox "Click"
     * 
     * Enable or disable the time selection for each day.
     */
    $('.working-plan input[type="checkbox"]').click(function() {
        var id = $(this).attr('id');

        if ($(this).prop('checked') == true) {
            $('#' + id + '-start').prop('disabled', false).val('09:00');
            $('#' + id + '-end').prop('disabled', false).val('18:00');
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
    $('.add-break').click(function() {
        var tr = 
                '<tr>' + 
                    '<td class="break-day editable">' + EALang['monday'] + '</td>' +
                    '<td class="break-start editable">09:00</td>' +
                    '<td class="break-end editable">10:00</td>' +
                    '<td>' + 
                        '<button type="button" class="btn edit-break" title="' + EALang['edit'] + '">' +
                            '<i class="icon-pencil"></i>' +
                        '</button>' +
                        '<button type="button" class="btn delete-break" title="' + EALang['delete'] + '">' +
                            '<i class="icon-remove"></i>' +
                        '</button>' +
                        '<button type="button" class="btn save-break hidden" title="' + EALang['save'] + '">' +
                            '<i class="icon-ok"></i>' +
                        '</button>' +
                        '<button type="button" class="btn cancel-break hidden" title="' + EALang['cancel'] + '">' +
                            '<i class="icon-ban-circle"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>';
        $('.breaks').prepend(tr);

        // Bind editable and event handlers.
        tr = $('.breaks tr').get()[1];
        WorkingPlan.prototype.editableBreakDay($(tr).find('.break-day'));
        WorkingPlan.prototype.editableBreakTime($(tr).find('.break-start, .break-end'));
        $(tr).find('.edit-break').trigger('click');
        $('.add-break').prop('disabled', true);
    });

    /**
     * Event: Edit Break Button "Click"
     * 
     * Enables the row editing for the "Breaks" table rows.
     */
    $(document).on('click', '.edit-break', function() {
        // Reset previous editable tds
        var $previousEdt = $(this).closest('table').find('.editable').get();
        $.each($previousEdt, function(index, edt) {
            if (edt.reset !== undefined) edt.reset();
        });

        // Make all cells in current row editable.
        $(this).parent().parent().children().trigger('edit');
        $(this).parent().parent().find('.break-start input, .break-end input').timepicker({
            currentText: EALang['now'],
            closeText: EALang['close'],
            timeOnlyTitle: EALang['select_time'],
            timeText: EALang['time'],
            hourText: EALang['hour'],
            minuteText: EALang['minutes']
        });
        $(this).parent().parent().find('.break-day select').focus();

        // Show save - cancel buttons.
        $(this).closest('table').find('.edit-break, .delete-break').addClass('hidden');
        $(this).parent().find('.save-break, .cancel-break').removeClass('hidden');
        
        $('.add-break').prop('disabled', true);
    });

    /**
     * Event: Delete Break Button "Click"
     * 
     * Removes the current line from the "Breaks" table.
     */
    $(document).on('click', '.delete-break', function() {
       $(this).parent().parent().remove();
    });

    /**
     * Event: Cancel Break Button "Click"
     * 
     * Bring the ".breaks" table back to its initial state.
     */
    $(document).on('click', '.cancel-break', function() {
        WorkingPlan.prototype.enableCancel = true;
        $(this).parent().parent().find('.cancel-editable').trigger('click');
        WorkingPlan.prototype.enableCancel = false;

        $(this).closest('table').find('.edit-break, .delete-break').removeClass('hidden');
        $(this).parent().find('.save-break, .cancel-break').addClass('hidden');
        $('.add-break').prop('disabled', false);
    });

    /**
     * Event: Save Break Button "Click"
     * 
     * Save the editable values and restore the table to its initial state.
     */
    $(document).on('click', '.save-break', function() {
        // Break's start time must always be prior to break's end. 
        var start = Date.parse($(this).parent().parent().find('.break-start input').val());
        var end = Date.parse($(this).parent().parent().find('.break-end input').val());
        if (start > end) {
            $(this).parent().parent().find('.break-end  input').val(start.addHours(1).toString('HH:mm'));
        }

        WorkingPlan.prototype.enableSubmit = true;
        $(this).parent().parent().find('.editable .submit-editable').trigger('click');
        WorkingPlan.prototype.enableSubmit = false;

        $(this).parent().find('.save-break, .cancel-break').addClass('hidden');
        $(this).closest('table').find('.edit-break, .delete-break').removeClass('hidden');
        $('.add-break').prop('disabled', false);
    });
};

/**
 * Get the working plan settings.
 * 
 * @returns {object} Returns the working plan settings object.
 */
WorkingPlan.prototype.get = function() {
    var workingPlan = {};
    $('.working-plan input[type="checkbox"]').each(function() {
        var id = $(this).attr('id');
        if ($(this).prop('checked') == true) {
            workingPlan[id] = {};
            workingPlan[id].start = $('#' + id + '-start').val();
            workingPlan[id].end = $('#' + id + '-end').val();
            workingPlan[id].breaks = [];
            
            $('.breaks tr').each(function(index, tr) {
                var day = WorkingPlan.prototype.convertDayToValue(
                        $(tr).find('.break-day').text());
                if (day == id) {
                    var start = $(tr).find('.break-start').text();
                    var end = $(tr).find('.break-end').text();
                    
                    workingPlan[id].breaks.push({
                        'start': start,
                        'end': end
                    });
                }
            });
            
        } else {
            workingPlan[id] = null;
        }
    });
    
    return workingPlan;
};

/**
 * Enables or disabled the timepicker functionality from the working plan input 
 * text fields.
 * 
 * @param {bool} disabled (OPTIONAL = false) If true then the timepickers will be
 * disabled.
 */
WorkingPlan.prototype.timepickers = function(disabled) {
    if (disabled == undefined) disabled == false;
    
    if (disabled == false) {
        // Set timepickers where needed.
        $('.working-plan input[type="text"]').timepicker({
            'timeFormat': 'HH:mm',
            
            currentText: EALang['now'],
            closeText: EALang['close'],
            timeOnlyTitle: EALang['select_time'],
            timeText: EALang['time'],
            hourText: EALang['hour'],
            minuteText: EALang['minutes'],
            
            'onSelect': function(datetime, inst) {
                // Start time must be earlier than end time. 
                var start = Date.parse($(this).parent().parent().find('.work-start').val());
                var end = Date.parse($(this).parent().parent().find('.work-end').val());

                if (start > end) {
                    $(this).parent().parent().find('.work-end').val(start.addHours(1).toString('HH:mm'));
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
WorkingPlan.prototype.reset = function() {
    
};

/**
 * This is necessary for translated days.
 * 
 * @param {string} value Day value could be like "monday", "tuesday" etc.
 */
WorkingPlan.prototype.convertValueToDay = function(value) {
    switch (value) {
        case 'monday': 
            return EALang['monday'];
            break;
        case 'tuesday': 
            return EALang['tuesday'];
            break;
        case 'wednesday': 
            return EALang['wednesday'];
            break;
        case 'thursday': 
            return EALang['thursday'];
            break;
        case 'friday': 
            return EALang['friday'];
            break;
        case 'saturday': 
            return EALang['saturday'];
            break;
        case 'sunday': 
            return EALang['sunday'];
            break;
    }
};

/**
 * This is necessary for translated days.
 * 
 * @param {string} value Day value could be like "Monday", "Tuesday" etc.
 */
WorkingPlan.prototype.convertDayToValue = function(day) {
    switch (day) {
        case EALang['monday']: 
            return 'monday';
            break;
        case EALang['tuesday']: 
            return 'tuesday';
            break;
        case EALang['wednesday']: 
            return 'wednesday';
            break;
        case EALang['thursday']: 
            return 'thursday';
            break;
        case EALang['friday']: 
            return 'friday';
            break;
        case EALang['saturday']: 
            return 'saturday';
            break;
        case EALang['sunday']: 
            return 'sunday';
            break;
    }
};