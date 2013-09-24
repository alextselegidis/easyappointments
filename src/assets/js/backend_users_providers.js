/**
 * This class contains the Providers helper class declaration, along with the "Providers" tab 
 * event handlers. By deviding the backend/users tab functionality into separate files
 * it is easier to maintain the code.
 * 
 * @class ProvidersHelper
 */
var ProvidersHelper = function() {
    this.filterResults = {}; // Store the results for later use.
};

/**
 * Bind the event handlers for the backend/users "Providers" tab.
 */
ProvidersHelper.prototype.bindEventHandlers = function() {
    /**
     * Event: Filter Providers Button "Click"
     * 
     * Filter the provider records with the given key string.
     */
    $('.filter-providers').click(function() {
        var key = $('#providers .filter-key').val();
        $('.selected-row').removeClass('selected-row');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter(key);
    });

    /**
     * Event: Filter Provider Row "Click"
     * 
     * Display the selected provider data to the user.
     */
    $(document).on('click', '.provider-row', function() {
        if ($('#providers .filter-providers').prop('disabled')) {
            $('#providers .filter-results').css('color', '#AAA');
            return; // exit because we are currently on edit mode
        }

        var provider = { 'id': $(this).attr('data-id') };
        $.each(BackendUsers.helper.filterResults, function(index, item) {
            if (item.id === provider.id) {
                provider = item;
                return;
            }
        });
        BackendUsers.helper.display(provider);
        $('.selected-row').removeClass('selected-row');
        $(this).addClass('selected-row');
        $('#edit-provider, #delete-provider').prop('disabled', false);
    });

    /**
     * Event: Add New Provider Button "Click"
     */
    $('#add-provider').click(function() {
        BackendUsers.helper.resetForm();
        $('#providers .add-edit-delete-group').hide();
        $('#providers .save-cancel-group').show();
        $('#providers .details').find('input, textarea').prop('readonly', false);
        $('#providers .filter-providers').prop('disabled', true);
        $('#providers .filter-results').css('color', '#AAA');
        $('#provider-password, #provider-password-confirm').addClass('required');
        $('#provider-notifications').prop('disabled', false);
        $('#provider-services input[type="checkbox"]').prop('disabled', false);

        $('#providers .add-break').prop('disabled', false);
        $('.edit-break, .delete-break').prop('disabled', false);
        $('#providers input[type="checkbox"]').prop('disabled', false);

        // Apply default working plan
        $.each(GlobalVariables.workingPlan, function(index, workingDay) {
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
        BackendUsers.helper.editableBreakDay($('#breaks .break-day'));
        BackendUsers.helper.editableBreakTime($('#breaks').find('.break-start, .break-end'));

        // Set timepickers where needed.
        $('.working-plan input').timepicker({
            'timeFormat': 'HH:mm',
            'onSelect': function(datetime, inst) {
                // Start time must be earlier than end time. 
                var start = Date.parse($(this).parent().parent().find('.work-start').val());
                var end = Date.parse($(this).parent().parent().find('.work-end').val());

                if (start > end) {
                    $(this).parent().parent().find('.work-end').val(start.addHours(1).toString('HH:mm'));
                }
            }
        });
    });

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
        BackendUsers.helper.editableBreakDay($(tr).find('.break-day'));
        BackendUsers.helper.editableBreakTime($(tr).find('.break-start, .break-end'));
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
        BackendUsers.enableCancel = true;
        $(this).parent().parent().find('.cancel-editable').trigger('click');
        BackendUsers.enableCancel = false;

        $(this).closest('table').find('.edit-break, .delete-break').removeClass('hidden');
        $(this).parent().find('.save-break, .cancel-break').addClass('hidden');
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

        BackendUsers.enableSubmit = true;
        $(this).parent().parent().find('.editable .submit-editable').trigger('click');
        BackendUsers.enableSubmit = false;

        $(this).closest('table').find('.edit-break, .delete-break').removeClass('hidden');
        $(this).parent().find('.save-break, .cancel-break').addClass('hidden');
    });

    /**
     * Event: Edit Provider Button "Click"
     */
    $('#edit-provider').click(function() {
        $('#providers .add-edit-delete-group').hide();
        $('#providers .save-cancel-group').show();
        $('.filter-providers').prop('disabled', true);
        $('#providers .filter-results').css('color', '#AAA');
        $('#providers .details').find('input, textarea').prop('readonly', false);
        $('#provider-password, #provider-password-confirm').removeClass('required');
        $('#provider-notifications').prop('disabled', false);
        $('#provider-services input[type="checkbox"]').prop('disabled', false);

        $('#providers .add-break').prop('disabled', false);
        $('.edit-break, .delete-break').prop('disabled', false);
        $('#providers input[type="checkbox"]').prop('disabled', false);
    });

    /**
     * Event: Delete Provider Button "Click"
     */
    $('#delete-provider').click(function() {
        var providerId = $('#provider-id').val();

        var messageBtns = {
            'Delete': function() {
                BackendUsers.helper.delete(providerId);
                $('#message_box').dialog('close');
            },
            'Cancel': function() {
                $('#message_box').dialog('close');
            }
        };

        GeneralFunctions.displayMessageBox('Delete Provider', 'Are you sure that you want '
                + 'to delete this record? This action cannot be undone.', messageBtns);
    });

    /**
     * Event: Save Provider Button "Click"
     */
    $('#save-provider').click(function() {
        var provider = {
            'first_name': $('#provider-first-name').val(),
            'last_name': $('#provider-last-name').val(),
            'email': $('#provider-email').val(),
            'mobile_number': $('#provider-mobile-number').val(),
            'phone_number': $('#provider-phone-number').val(),
            'address': $('#provider-address').val(),
            'city': $('#provider-city').val(),
            'state': $('#provider-state').val(),
            'zip_code': $('#provider-zip-code').val(),
            'notes': $('#provider-notes').val(),
            'settings': {
                'username': $('#provider-username').val(),  
                'working_plan': BackendUsers.helper.getWorkingPlan(),
                'notifications': $('#provider-notifications').hasClass('active')
            }
        };

        // Include provider services.
        provider.services = [];
        $('#provider-services input[type="checkbox"]').each(function() {
            if ($(this).prop('checked')) {
                provider.services.push($(this).attr('data-id'));
            }
        });

        // Include password if changed.
        if ($('#provider-password').val() !== '') {
            provider.settings.password = $('#provider-password').val();
        }

        // Include id if changed.
        if ($('#provider-id').val() !== '') {
            provider.id = $('#provider-id').val();
        }

        if (!BackendUsers.helper.validate(provider)) return;

        BackendUsers.helper.save(provider);
    });

    /**
     * Event: Cancel Provider Button "Click"
     * 
     * Cancel add or edit of an provider record.
     */
    $('#cancel-provider').click(function() {
        BackendUsers.helper.resetForm();

        var provider = { 'id': $('#providers .selected-row').attr('data-id') };

        $.each(BackendUsers.helper.filterResults, function(index, item) {
            if (item.id === provider.id) {
                provider = item;
                return;
            }
        });

        BackendUsers.helper.display(provider);

        $('#edit-provider, #delete-provider').prop('disabled', false);
    });

    /**
     * Event: Display Provider Details "Click"
     */
    $('#providers .display-details').click(function() {
        $('#providers .switch-view .current').removeClass('current');
        $(this).addClass('current');
        $('.working-plan-view').hide('fade', function() {
            $('.details-view').show('fade');
        });

    });

    /**
     * Event: Display Provider Working Plan "Click"
     */
    $('#providers .display-working-plan').click(function() {
        $('#providers .switch-view .current').removeClass('current');
        $(this).addClass('current');
        $('.details-view').hide('fade', function() {
            $('.working-plan-view').show('fade');
        });
    });
};

/**
 * Save provider record to database.
 * 
 * @param {object} provider Contains the admin record data. If an 'id' value is provided
 * then the update operation is going to be executed.
 */
ProvidersHelper.prototype.save = function(provider) {
    //////////////////////////////////////////////////
    //console.log('Provider data to save:', provider);
    //////////////////////////////////////////////////
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_provider';
    var postData = { 'provider': JSON.stringify(provider) };
    
    $.post(postUrl, postData, function(response) {
        ///////////////////////////////////////////////////
        //console.log('Save Provider Response:', response);
        ///////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Provider saved successfully!');
        BackendUsers.helper.resetForm(true);
        // If "id" is not defined then no record will be selected (applies when adding 
        // a new provider record).
        BackendUsers.helper.filter($('#providers .filter-key').val(), provider.id); 
    }, 'json');
};

/**
 * Delete a provider record from database.
 * 
 * @param {int} id Record id to be deleted. 
 */
ProvidersHelper.prototype.delete = function(id) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_delete_provider';
    var postData = { 'provider_id': id };
    
    $.post(postUrl, postData, function(response) {
        /////////////////////////////////////////////////////
        //console.log('Delete provider response:', response);
        /////////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Provider deleted successfully!');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter($('#providers .filter-key').val());
    });
};

/**
 * Validates a provider record.
 * 
 * @param {object} provider Contains the admin data to be validated.
 * @returns {bool} Returns the validation result.
 */
ProvidersHelper.prototype.validate = function(provider) {
    $('#providers .required').css('border', '');
    $('#provider-password, #provider-password-confirm').css('border', '');
    
    try {
        // Validate required fields.
        var missingRequired = false;
        $('#providers .required').each(function() {
            if ($(this).val() == '' || $(this).val() == undefined) {
                $(this).css('border', '2px solid red');
                missingRequired = true;
            }
        });
        if (missingRequired) {
            throw 'Fields with * are  required.';
        }
        
        // Validate passwords.
        if ($('#provider-password').val() != $('#provider-password-confirm').val()) {
            $('#provider-password, #provider-password-confirm').css('border', '2px solid red');
            throw 'Passwords mismatch!';
        }
        
        if ($('#provider-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#provider-password').val() != '') {
            $('#provider-password, #provider-password-confirm').css('border', '2px solid red');
            throw 'Password must be at least ' + BackendUsers.MIN_PASSWORD_LENGTH 
                    + ' characters long.';
        }
        
        // Validate user email.
        if (!GeneralFunctions.validateEmail($('#provider-email').val())) {
            $('#provider-email').css('border', '2px solid red');
            throw 'Invalid email address!';
        }
        
        return true;
    } catch(exc) {
        $('#providers .form-message').text(exc);
        $('#providers .form-message').show();
        return false;
    }
};

/**
 * Resets the admin tab form back to its initial state. 
 * 
 * @param {bool} keepRecordData (OPTIONAL = false) If true then the current record data will
 * remain on the form.
 */
ProvidersHelper.prototype.resetForm = function(keepRecordData) {
    if (keepRecordData == undefined) keepRecordData = false;
    
    $('#providers .add-edit-delete-group').show();
    $('#providers .save-cancel-group').hide();
    $('#providers .details').find('input, textarea').prop('readonly', true);
    $('.filter-providers').prop('disabled', false);
    $('#providers .filter-results').css('color', '');
    $('#providers .form-message').hide();    
    $('#provider-notifications').removeClass('active');
    $('#provider-notifications').prop('disabled', true);
    $('#provider-services input[type="checkbox"]').prop('disabled', true);
    $('#providers .required').css('border', '');
    $('#provider-password, #provider-password-confirm').css('border', '');
    $('#providers .add-break').prop('disabled', true);
    $('#providers input[type="checkbox"]').prop('disabled', true);
    $('#providers .working-plan input[type="text"]').timepicker('destroy');
    $('#breaks').find('.edit-break, .delete-break').prop('disabled', true);
    
    if (!keepRecordData) {
        $('#edit-provider, #delete-provider').prop('disabled', true);
        $('#providers .details').find('input, textarea').val('');
        $('#providers input[type="checkbox"]').prop('checked', false);
        $('#provider-services input[type="checkbox"]').prop('checked', false);
        $('#providers #breaks tbody').empty();
    }
};

/**
 * Display a provider record into the admin form.
 * 
 * @param {object} provider Contains the provider record data.
 */
ProvidersHelper.prototype.display = function(provider) {
    $('#provider-id').val(provider.id);
    $('#provider-first-name').val(provider.first_name);
    $('#provider-last-name').val(provider.last_name);
    $('#provider-email').val(provider.email);
    $('#provider-mobile-number').val(provider.mobile_number);
    $('#provider-phone-number').val(provider.phone_number);
    $('#provider-address').val(provider.address);
    $('#provider-city').val(provider.city);
    $('#provider-state').val(provider.state);
    $('#provider-zip-code').val(provider.zip_code);
    $('#provider-notes').val(provider.notes);
    
    $('#provider-username').val(provider.settings.username);
    if (provider.settings.notifications == true) {
        $('#provider-notifications').addClass('active');
    } else {
        $('#provider-notifications').removeClass('active');
    }
    
    $('#provider-services input[type="checkbox"]').prop('checked', false);
    $.each(provider.services, function(index, serviceId) {
        $('#provider-services input[type="checkbox"]').each(function() {
            if ($(this).attr('data-id') == serviceId) {
                $(this).prop('checked', true);
            }
        });
    });
    
    // Display working plan
    $('#providers #breaks tbody').empty();
    var workingPlan = $.parseJSON(provider.settings.working_plan);
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
    
    $('.edit-break, .delete-break').prop('disabled', true);

    // Make break cells editable.
    BackendUsers.helper.editableBreakDay($('#breaks .break-day'));
    BackendUsers.helper.editableBreakTime($('#breaks').find('.break-start, .break-end'));

    // Set timepickers where needed.
    $('.working-plan input').timepicker({
        'timeFormat': 'HH:mm',
        'onSelect': function(datetime, inst) {
            // Start time must be earlier than end time. 
            var start = Date.parse($(this).parent().parent().find('.work-start').val());
            var end = Date.parse($(this).parent().parent().find('.work-end').val());

            if (start > end) {
                $(this).parent().parent().find('.work-end').val(start.addHours(1).toString('HH:mm'));
            }
        }
    });
};

/**
 * Filters provider records depending a string key.
 * 
 * @param {string} key This is used to filter the provider records of the database.
 * @param {numeric} selectRecordId (OPTIONAL) If set, when the function is complete
 * a result row can be set as selected. 
 */
ProvidersHelper.prototype.filter = function(key, selectRecordId) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_providers';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        //////////////////////////////////////////////////////
        //console.log('Filter providers response:', response);
        //////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        BackendUsers.helper.filterResults = response;
        
        $('#providers .filter-results').html('');
        $.each(response, function(index, provider) {
            var html = ProvidersHelper.prototype.getFilterHtml(provider);
            $('#providers .filter-results').append(html);
        });
        
        if (selectRecordId != undefined) {
            $('.provider-row').each(function() {
                if ($(this).attr('data-id') == selectRecordId) {
                    $(this).addClass('selected-row');
                    return false;
                }
            });
        }
    }, 'json');
};

/**
 * Get an provider row html code that is going to be displayed on the filter results list.
 * 
 * @param {object} provider Contains the provider record data.
 * @returns {string} The html code that represents the record on the filter results list.
 */
ProvidersHelper.prototype.getFilterHtml = function(provider) {
    var html =
            '<div class="provider-row" data-id="' + provider.id + '">' + 
                '<strong>' + provider.first_name + ' ' + provider.last_name + '</strong><br>' +
                provider.email + ', ' + provider.mobile_number + ', ' + provider.phone_number + '<br>' + 
            '</div>';

    return html;
};

/**
 * Get the current working plan.
 * 
 * @return {string} Returns the working plan (already stringified).
 */
ProvidersHelper.prototype.getWorkingPlan = function() {
    var workingPlan = {};
    $('.working-plan input[type="checkbox"').each(function() {
        var id = $(this).attr('id');
        if ($(this).prop('checked') == true) {
            workingPlan[id] = {}
            workingPlan[id].start = $('#' + id + '-start').val();
            workingPlan[id].end = $('#' + id + '-end').val();
            workingPlan[id].breaks = [];
            
            $('#breaks tr').each(function(index, tr) {
                var day = $(tr).find('.break-day').text().toLowerCase();
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
    
    return JSON.stringify(workingPlan);
};


/**
 * Initialize the editable functionality to the break day table cells.
 * 
 * @param {object} $selector The cells to be initialized.
 */
ProvidersHelper.prototype.editableBreakDay = function($selector) {
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
            if (!BackendUsers.enableCancel) return false; // disable ESC button
        },
        'onsubmit': function(settings, td) {
            if (!BackendUsers.enableSubmit) return false; // disable Enter button
        }
    });
},

/**
 * Initialize the editable functionality to the break time table cells.
 * 
 * @param {object} $selector The cells to be initialized.
 */        
ProvidersHelper.prototype.editableBreakTime = function($selector) {
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
            if (!BackendUsers.enableCancel) return false; // disable ESC button
        },
        'onsubmit': function(settings, td) {
            if (!BackendUsers.enableSubmit) return false; // disable Enter button
        }
    });
}