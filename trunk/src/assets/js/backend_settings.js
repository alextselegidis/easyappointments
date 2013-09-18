/**
 * Contains the functionality of the backend settings page. Can either work for 
 * system or user settings, but the actions allowed to the user are restricted to
 * his role (only admin has full privileges).
 * 
 * @namespace BackendSettings
 */
var BackendSettings = {
    SETTINGS_SYSTEM: 'SETTINGS_SYSTEM',
    SETTINGS_USER: 'SETTINGS_USER',
    
    /**
     * This flag is used when trying to cancel row editing. It is
     * true only whenever the user presses the cancel button.
     * 
     * @type {bool}
     */
    enableCancel: false,
            
    /**
     * This flag determines whether the jeditables are allowed to submit. It is  
     * true only whenever the user presses the save button.
     * 
     * @type {bool}
     */
    enableSubmit: false,
    
    /**
     * Tab settings object.
     * 
     * @type {object}
     */
    settings: {},
    
    /**
     * Initialize Page
     * 
     * @param {bool} bindEventHandlers (OPTIONAL)Determines whether to bind the default event
     * handlers (default = true).
     * @returns {undefined}
     */
    initialize: function(bindEventHandlers) {
        if (bindEventHandlers == undefined) bindEventHandlers = true;
        
        // Apply values from database.
        $.each(GlobalVariables.settings.system, function(index, setting) {
            $('input[data-field="' + setting.name + '"]').val(setting.value);
        });
        
        var workingPlan = {};
        $.each(GlobalVariables.settings.system, function(index, setting) {
            if (setting.name == 'company_working_plan') {
                workingPlan = $.parseJSON(setting.value); 
                return false;
            }
        });
        
        $.each(workingPlan, function(index, workingDay) {
            if (workingDay != null) {
                $('#' + index).prop('checked', true);
                $('#' + index + '-start').val(workingDay.start);
                $('#' + index + '-end').val(workingDay.end);
                
                // Add the day's breaks on the breaks table.
                $.each(workingDay.breaks, function(i, brk) {
                    var tr = 
                            '<tr>' + 
                                '<td class="break-day editable">' + GeneralFunctions.ucaseFirstLetter(index) + '</td>' +
                                '<td class="break-start editable">' + brk.start + '</td>' +
                                '<td class="break-end editable">' + brk.end + '</td>' +
                                '<td>' + 
                                    '<button type="button" class="btn edit-break" title="Edit Break">' +
                                        '<i class="icon-pencil"></i>' +
                                    '</button>' +
                                    '<button type="button" class="btn delete-break" title="Delete Break">' +
                                        '<i class="icon-remove"></i>' +
                                    '</button>' +
                                    '<button type="button" class="btn save-break hidden" title="Save Break">' +
                                        '<i class="icon-ok"></i>' +
                                    '</button>' +
                                    '<button type="button" class="btn cancel-break hidden" title="Cancel Break">' +
                                        '<i class="icon-ban-circle"></i>' +
                                    '</button>' +
                                '</td>' +
                            '</tr>';
                    $('#breaks').append(tr);
                });
            } else {
                $('#' + index).prop('checked', false);
                $('#' + index + '-start').prop('disabled', true);
                $('#' + index + '-end').prop('disabled', true);
            }
        });
        
        // Make break cells editable.
        BackendSettings.breakDayEditable($('#breaks .break-day'));
        BackendSettings.breakTimeEditable($('#breaks').find('.break-start, .break-end'));
        
        // Set timepickers where needed.
        $('.working-hours input').timepicker({
            timeFormat: 'HH:mm'
        });
        
        // Book Advance Timeout Spinner
        $('#book-advance-timeout').spinner({
            min: 0
        });
        
        // Set default settings helper.
        BackendSettings.settings = new SystemSettings();
        
        if (bindEventHandlers) {
            BackendSettings.bindEventHandlers();
        }
    },
            
    bindEventHandlers: function() {
        /**
         * Event: Tab "Click"
         * 
         * Change the visible tab contents.
         */
        $('.tab').click(function() {
            $('.active').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();
            
            if ($(this).hasClass('general-tab')) { 
                $('#general').show();
                BackendSettings.settings = new SystemSettings();
            } else if ($(this).hasClass('business-logic-tab')) { 
                $('#business-logic').show();
                BackendSettings.settings = new SystemSettings();
            } else if ($(this).hasClass('user-tab')) {
                $('#user').show();
                BackendSettings.settings = new UserSettings();
            }
        });
        
        /**
         * Event: Save Settings Button "Click"
         * 
         * Store the setting changes into the database.
         */
        $('.save-settings').click(function() {
            var settings = BackendSettings.settings.get();
            BackendSettings.settings.save(settings);
        });
        
        /**
         * Event: Day Checkbox "Click"
         * 
         * Enable or disable the time selection for each day.
         */
        $('.working-hours input[type="checkbox"]').click(function() {
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
                        '<td class="break-day editable">Monday</td>' +
                        '<td class="break-start editable">09:00</td>' +
                        '<td class="break-end editable">10:00</td>' +
                        '<td>' + 
                            '<button type="button" class="btn edit-break" title="Edit Break">' +
                                '<i class="icon-pencil"></i>' +
                            '</button>' +
                            '<button type="button" class="btn delete-break" title="Delete Break">' +
                                '<i class="icon-remove"></i>' +
                            '</button>' +
                            '<button type="button" class="btn save-break hidden" title="Save Break">' +
                                '<i class="icon-ok"></i>' +
                            '</button>' +
                            '<button type="button" class="btn cancel-break hidden" title="Cancel Break">' +
                                '<i class="icon-ban-circle"></i>' +
                            '</button>' +
                        '</td>' +
                    '</tr>';
            $('#breaks').prepend(tr);
            
            // Bind editable and event handlers.
            tr = $('#breaks tr').get()[1];
            BackendSettings.breakDayEditable($(tr).find('.break-day'));
            BackendSettings.breakTimeEditable($(tr).find('.break-start, .break-end'));
            $(tr).find('.edit-break').trigger('click');
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
               edt.reset();
            });
            
            // Make all cells in current row editable.
            $(this).parent().parent().children().trigger('edit');
            $(this).parent().parent().find('.break-start input, .break-end input').timepicker();
            $(this).parent().parent().find('.break-day select').focus();
            
            // Show save - cancel buttons.
            $(this).closest('table').find('.edit-break, .delete-break').addClass('hidden');
            $(this).parent().find('.save-break, .cancel-break').removeClass('hidden');
            
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
         * Bring the "#breaks" table back to its initial state.
         */
        $(document).on('click', '.cancel-break', function() {
            BackendSettings.enableCancel = true;
            $(this).parent().parent().find('.cancel-editable').trigger('click');
            BackendSettings.enableCancel = false;
            
            $(this).closest('table').find('.edit-break, .delete-break').removeClass('hidden');
            $(this).parent().find('.save-break, .cancel-break').addClass('hidden');
        });
        
        /**
         * Event: Save Break Button "Click"
         * 
         * Save the editable values and restore the table to its initial state.
         */
        $(document).on('click', '.save-break', function() {
            BackendSettings.enableSubmit = true;
            $(this).parent().parent().find('.editable .submit-editable').trigger('click');
            BackendSettings.enableSubmit = false;
            
            $(this).closest('table').find('.edit-break, .delete-break').removeClass('hidden');
            $(this).parent().find('.save-break, .cancel-break').addClass('hidden');
        });
    },
    
    /**
     * Initialize the editable functionality to the break day table cells.
     * 
     * @param {object} $selector The cells to be initialized.
     */
    breakDayEditable: function($selector) {
        $selector.editable(function(value, settings) {
            return value;
        }, {
            'type': 'select',
            'data': '{ "Monday": "Monday", "Tuesday": "Tuesday", "Wednesday": "Wednesday", '
                    + '"Thursday": "Thursday", "Friday": "Friday", "Saturday": "Saturday", '
                    + '"Sunday": "Sunday", "selected": "Monday"}',
            'event': 'edit',
            'height': '30px',
            'submit': '<button type="button" class="hidden submit-editable">Submit</button>',
            'cancel': '<button type="button" class="hidden cancel-editable">Cancel</button>',
            'onblur': 'ignore',
            'onreset': function(settings, td) {
                if (!BackendSettings.enableCancel) return false; // disable ESC button
            },
            'onsubmit': function(settings, td) {
                if (!BackendSettings.enableSubmit) return false; // disable Enter button
            }
        });
    },
    
    /**
     * Initialize the editable functionality to the break time table cells.
     * 
     * @param {object} $selector The cells to be initialized.
     */        
    breakTimeEditable: function($selector) {
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
                if (!BackendSettings.enableCancel) return false; // disable ESC button
            },
            'onsubmit': function(settings, td) {
                if (!BackendSettings.enableSubmit) return false; // disable Enter button
            }
        });
    }
};

/**
 * "System Settings" Tab Helper
 * @class SystemSettings
 */
var SystemSettings = function() {};

/**
 * Save the system settings. This method is run after changes are detected on the 
 * tab input fields.
 * 
 * @param {array} settings Contains the system settings data.
 */
SystemSettings.prototype.save = function(settings) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_settings';
    var postData = {
        'settings': JSON.stringify(settings),
        'type': BackendSettings.SETTINGS_SYSTEM
    };
    
    $.post(postUrl, postData, function(response) {
        ///////////////////////////////////////////////////////////
        console.log('Save General Settings Response:', response);
        ///////////////////////////////////////////////////////////
        
        if (!Backend.handleAjaxExceptions(response)) return;
       
        Backend.displayNotification('Settings saved successfully!');
    }, 'json');
};

/**
 * Prepare the system settings array. This method uses the DOM elements of the 
 * backend/settings page, so it can't be used in another page.
 * 
 * @returns {array} Returns the system settings array.
 */
SystemSettings.prototype.get = function() {
    var settings = [];
    
    // General Settings Tab
    $('#general input').each(function() {
        settings.push({
            'name': $(this).attr('data-field'),
            'value': $(this).val()
        });
    });
    
    // Business Logic Tab
    var workingPlan = {};
    $('.working-hours input[type="checkbox"').each(function() {
        var id = $(this).attr('id');
        if ($(this).prop('checked') == true) {
            workingPlan[id] = {}
            workingPlan[id].start = $('#' + id + '-start').val();
            workingPlan[id].end = $('#' + id + '-end').val();
            workingPlan[id].breaks = {};
            
        } else {
            workingPlan[id] = null;
        }
    });
    
    settings.push({
        'name': 'company_working_plan',
        'value': JSON.stringify(workingPlan)
    });
    
    return settings;
};

/**
 * Validate the settings data. If the validation fails then display a 
 * message to the user.
 * 
 * @returns {bool} Returns the validation result.
 */
SystemSettings.prototype.validate = function() {
    
};

/**
 * "User Settings" Tab Helper
 * @class UserSettings
 */
var UserSettings = function() {};

/**
 * Get the settings data for the user settings.
 * 
 * @returns {array} Returns the user settings array.
 */
UserSettings.prototype.get = function() {
    
};

/**
 * Store the user settings into the database.
 * 
 * @param {array} settings Contains the user settings.
 */
UserSettings.prototype.save = function(settings) {
    
};

/**
 * Validate the settings data. If the validation fails then display a 
 * message to the user.
 * 
 * @returns {bool} Returns the validation result.
 */
UserSettings.prototype.validate = function() {
    
};
