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
     * Event: Filter Providers Form "Submit"
     * 
     * Filter the provider records with the given key string.
     */
    $('#filter-providers form').submit(function(event) {
        var key = $('#filter-providers .key').val();
        $('.selected-row').removeClass('selected-row');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter(key);
        return false;
    });

    /**
     * Event: Clear Filter Button "Click"
     */
    $('#filter-providers .clear').click(function() {
        BackendUsers.helper.filter('');
        $('#filter-providers .key').val('');
    });

    /**
     * Event: Filter Provider Row "Click"
     * 
     * Display the selected provider data to the user.
     */
    $(document).on('click', '.provider-row', function() {
        if ($('#filter-providers .filter').prop('disabled')) {
            $('#filter-providers .results').css('color', '#AAA');
            return; // Exit because we are currently on edit mode.
        }
        
        var providerId = $(this).attr('data-id');
        var provider = {};
        $.each(BackendUsers.helper.filterResults, function(index, item) {
            if (item.id === providerId) {
                provider = item;
                return false;
            }
        });
        
        BackendUsers.helper.display(provider);
        $('#filter-providers .selected-row').removeClass('selected-row');
        $(this).addClass('selected-row');
        $('#edit-provider, #delete-provider').prop('disabled', false);
    });

    /**
     * Event: Add New Provider Button "Click"
     */
    $('#add-provider').click(function() {
        BackendUsers.helper.resetForm();
        $('#filter-providers button').prop('disabled', true);
        $('#filter-providers .results').css('color', '#AAA');
        $('#providers .add-edit-delete-group').hide();
        $('#providers .save-cancel-group').show();
        $('#providers .details').find('input, textarea').prop('readonly', false);
        $('#provider-password, #provider-password-confirm').addClass('required');
        $('#provider-notifications').prop('disabled', false);
        $('#providers').find('.add-break, .edit-break, .delete-break, #reset-working-plan').prop('disabled', false);
        $('#provider-services input[type="checkbox"]').prop('disabled', false);
        $('#providers input[type="checkbox"]').prop('disabled', false);

        // Apply default working plan
        BackendUsers.wp.setup(GlobalVariables.workingPlan);
        BackendUsers.wp.timepickers(false);
    });

    /**
     * Event: Edit Provider Button "Click"
     */
    $('#edit-provider').click(function() {
        $('#providers .add-edit-delete-group').hide();
        $('#providers .save-cancel-group').show();
        $('#filter-providers button').prop('disabled', true);
        $('#filter-providers .results').css('color', '#AAA');
        $('#providers .details').find('input, textarea').prop('readonly', false);
        $('#provider-password, #provider-password-confirm').removeClass('required');
        $('#provider-notifications').prop('disabled', false);
        $('#provider-services input[type="checkbox"]').prop('disabled', false);
        $('#providers').find('.add-break, .edit-break, .delete-break, #reset-working-plan').prop('disabled', false);
        $('#providers input[type="checkbox"]').prop('disabled', false);
        BackendUsers.wp.timepickers(false);
    });

    /**
     * Event: Delete Provider Button "Click"
     */
    $('#delete-provider').click(function() {
        var providerId = $('#provider-id').val();

        var messageBtns = {};
        messageBtns[EALang['delete']] = function() {
            BackendUsers.helper.delete(providerId);
            $('#message_box').dialog('close');
        };
        messageBtns[EALang['cancel']] = function() {
            $('#message_box').dialog('close');
        };

        GeneralFunctions.displayMessageBox(EALang['delete_provider'], 
                EALang['delete_record_prompt'], messageBtns);
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
                'working_plan': JSON.stringify(BackendUsers.wp.get()),
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
        var id = $('#filter-providers .selected-row').attr('data-id');
        BackendUsers.helper.resetForm();
        if (id != '') {
            BackendUsers.helper.select(id, true);
        }
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
    
    /**
     * Event: Reset Working Plan Button "Click".
     */
    $('#reset-working-plan').click(function() {
        $('.breaks').empty();
        $('.work-start, .work-end').val('');
        BackendUsers.wp.setup(GlobalVariables.workingPlan);
        BackendUsers.wp.timepickers(false);
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
        Backend.displayNotification(EALang['provider_saved']);
        BackendUsers.helper.resetForm();
        $('#filter-providers .key').val('');
        BackendUsers.helper.filter('', response.id, true); 
    }, 'json');
};

/**
 * Delete a provider record from database.
 * 
 * @param {numeric} id Record id to be deleted. 
 */
ProvidersHelper.prototype.delete = function(id) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_delete_provider';
    var postData = { 'provider_id': id };
    
    $.post(postUrl, postData, function(response) {
        /////////////////////////////////////////////////////
        //console.log('Delete provider response:', response);
        /////////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification(EALang['provider_deleted']);
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter($('#filter-providers .key').val());
    }, 'json');
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
            throw EALang['fields_are_required'];
        }
        
        // Validate passwords.
        if ($('#provider-password').val() != $('#provider-password-confirm').val()) {
            $('#provider-password, #provider-password-confirm').css('border', '2px solid red');
            throw EALang['passwords_mismatch'];
        }
        
        if ($('#provider-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#provider-password').val() != '') {
            $('#provider-password, #provider-password-confirm').css('border', '2px solid red');
            throw EALang['password_length_notice'].replace('$number', BackendUsers.MIN_PASSWORD_LENGTH);
        }
        
        // Validate user email.
        if (!GeneralFunctions.validateEmail($('#provider-email').val())) {
            $('#provider-email').css('border', '2px solid red');
            throw EALang['invalid_email'];
        }
        
        // Check if username exists
        if ($('#provider-username').attr('already-exists') ==  'true') {
            $('#provider-username').css('border', '2px solid red');
            throw EALang['username_already_exists'];
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
 */
ProvidersHelper.prototype.resetForm = function() {
    $('#filter-providers .selected-row').removeClass('selected-row');
    $('#filter-providers button').prop('disabled', false);
    $('#filter-providers .results').css('color', '');
    
    $('#providers .add-edit-delete-group').show();
    $('#providers .save-cancel-group').hide();
    $('#providers .details').find('input, textarea').prop('readonly', true);
    $('#providers .form-message').hide();    
    $('#provider-notifications').removeClass('active');
    $('#provider-notifications').prop('disabled', true);
    $('#provider-services input[type="checkbox"]').prop('disabled', true);
    $('#providers .required').css('border', '');
    $('#provider-password, #provider-password-confirm').css('border', '');
    $('#providers .add-break, #reset-working-plan').prop('disabled', true);
    BackendUsers.wp.timepickers(true);
    $('#providers .working-plan input[type="text"]').timepicker('destroy');
    $('#providers .working-plan input[type="checkbox"]').prop('disabled', true);
    $('.breaks').find('.edit-break, .delete-break').prop('disabled', true);

    $('#edit-provider, #delete-provider').prop('disabled', true);
    $('#providers .details').find('input, textarea').val('');
    $('#providers input[type="checkbox"]').prop('checked', false);
    $('#provider-services input[type="checkbox"]').prop('checked', false);
    $('#providers .breaks tbody').empty();

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
    $('#providers .breaks tbody').empty();
    var workingPlan = $.parseJSON(provider.settings.working_plan);
    BackendUsers.wp.setup(workingPlan);
    $('.breaks').find('.edit-break, .delete-break').prop('disabled', true);
};

/**
 * Filters provider records depending a string key.
 * 
 * @param {string} key This is used to filter the provider records of the database.
 * @param {numeric} selectId (OPTIONAL = undefined) If set, when the function is complete
 * a result row can be set as selected. 
 * @param {bool} display (OPTIONAL = false) If true then the selected record will be also 
 * displayed.
 */
ProvidersHelper.prototype.filter = function(key, selectId, display) {
    if (display == undefined) display = false;
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_providers';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        //////////////////////////////////////////////////////
        //console.log('Filter providers response:', response);
        //////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        BackendUsers.helper.filterResults = response;
        
        
        $('#filter-providers .results').data('jsp').destroy;
        $('#filter-providers .results').html('');
        $.each(response, function(index, provider) {
            var html = ProvidersHelper.prototype.getFilterHtml(provider);
            $('#filter-providers .results').append(html);
        });
        $('#filter-providers .results').jScrollPane({ mouseWheelSpeed: 70 });
        
        if (response.length == 0) {
            $('#filter-providers .results').html('<em>' + EALang['no_records_found'] + '</em>')
        }
        
        if (selectId != undefined) {
            BackendUsers.helper.select(selectId, display);
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
    var name = provider.first_name + ' ' + provider.last_name;
    var info = provider.email;
    info = (provider.mobile_number != '' && provider.mobile_number != null)
            ? info + ', ' + provider.mobile_number : info;
    info = (provider.phone_number != '' && provider.phone_number != null)
            ? info + ', ' + provider.phone_number : info;        
    
    var html =
            '<div class="provider-row" data-id="' + provider.id + '">' + 
                '<strong>' + name + '</strong><br>' +
                info + '<br>' + 
            '</div><hr>';

    return html;
};

/**
 * Initialize the editable functionality to the break day table cells.
 * 
 * @param {object} $selector The cells to be initialized.
 */
ProvidersHelper.prototype.editableBreakDay = function($selector) {
    var weekDays = {};
    weekDays[EALang['monday']] = 'Monday';
    weekDays[EALang['tuesday']] = 'Tuesday';
    weekDays[EALang['wednesday']] = 'Wednesday';
    weekDays[EALang['thursday']] = 'Thursday';
    weekDays[EALang['friday']] = 'Friday';
    weekDays[EALang['saturday']] = 'Saturday';
    weekDays[EALang['sunday']] = 'Sunday';


    $selector.editable(function(value, settings) {
        return value;
    }, {
        'type': 'select',
        // 'data': '{ "Monday": "Monday", "Tuesday": "Tuesday", "Wednesday": "Wednesday", '
        //         + '"Thursday": "Thursday", "Friday": "Friday", "Saturday": "Saturday", '
        //         + '"Sunday": "Sunday", "selected": "Monday"}',
        'data': weekDays,
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
};

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
};

/**
 * Select and display a providers filter result on the form.
 * 
 * @param {numeric} id Record id to be selected.
 * @param {bool} display (OPTIONAL = false) If true the record will be displayed on the form.
 */
ProvidersHelper.prototype.select = function(id, display) {
    if (display == undefined) display = false;
    
    // Select record in filter results.
    $('#filter-providers .provider-row').each(function() {
        if ($(this).attr('data-id') == id) {
            $(this).addClass('selected-row');
            return false;
        }
    });
    
    // Display record in form (if display = true).
    if (display) {
        $.each(BackendUsers.helper.filterResults, function(index, provider) {
            if (provider.id == id) {
                BackendUsers.helper.display(provider);
                $('#edit-provider, #delete-provider').prop('disabled', false);
                return false;
            }
        });
    }
};