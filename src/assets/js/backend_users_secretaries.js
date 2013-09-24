/**
 * This class contains the Secretaries helper class declaration, along with the "Secretaries"  
 * tab event handlers. By deviding the backend/users tab functionality into separate files
 * it is easier to maintain the code.
 * 
 * @class SecretariesHelper
 */
var SecretariesHelper = function() {
    this.filterResults = {}; // Store the results for later use.
};

/**
 * Bind the event handlers for the backend/users "Secretaries" tab.
 */
SecretariesHelper.prototype.bindEventHandlers = function() {
    /**
     * Event: Filter Secretaries Button "Click"
     * 
     * Filter the secretary records with the given key string.
     */
    $('.filter-secretaries').click(function() {
        var key = $('#secretaries .filter-key').val();
        $('.selected-row').removeClass('selected-row');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter(key);
    });

    /**
     * Event: Filter Secretary Row "Click"
     * 
     * Display the selected secretary data to the user.
     */
    $(document).on('click', '.secretary-row', function() {
        if ($('#secretaries .filter-secretaries').prop('disabled')) {
            $('#secretaries .filter-results').css('color', '#AAA');
            return; // exit because we are currently on edit mode
        }

        var secretary = { 'id': $(this).attr('data-id') };
        $.each(BackendUsers.helper.filterResults, function(index, item) {
            if (item.id === secretary.id) {
                secretary = item;
                return;
            }
        });
        BackendUsers.helper.display(secretary);
        $('.selected-row').removeClass('selected-row');
        $(this).addClass('selected-row');
        $('#edit-secretary, #delete-secretary').prop('disabled', false);
    });

    /**
     * Event: Add New Secretary Button "Click"
     */
    $('#add-secretary').click(function() {
        BackendUsers.helper.resetForm();
        $('#secretaries .add-edit-delete-group').hide();
        $('#secretaries .save-cancel-group').show();
        $('#secretaries .details').find('input, textarea').prop('readonly', false);
        $('#secretaries .filter-secretaries').prop('disabled', true);
        $('#secretaries .filter-results').css('color', '#AAA');
        $('#secretary-password, #secretary-password-confirm').addClass('required');
        $('#secretary-notifications').prop('disabled', false);
        $('#secretary-providers input[type="checkbox"]').prop('disabled', false);
    });

    /**
     * Event: Edit Secretary Button "Click"
     */
    $('#edit-secretary').click(function() {
        $('#secretaries .add-edit-delete-group').hide();
        $('#secretaries .save-cancel-group').show();
        $('.filter-secretaries').prop('disabled', true);
        $('#secretaries .filter-results').css('color', '#AAA');
        $('#secretaries .details').find('input, textarea').prop('readonly', false);
        $('#secretary-password, #secretary-password-confirm').removeClass('required');
        $('#secretary-notifications').prop('disabled', false);
        $('#secretary-providers input[type="checkbox"]').prop('disabled', false);
    });

    /**
     * Event: Delete Secretary Button "Click"
     */
    $('#delete-secretary').click(function() {
        var providerId = $('#secretary-id').val();

        var messageBtns = {
            'Delete': function() {
                BackendUsers.helper.delete(providerId);
                $('#message_box').dialog('close');
            },
            'Cancel': function() {
                $('#message_box').dialog('close');
            }
        };

        GeneralFunctions.displayMessageBox('Delete Secretary', 'Are you sure that you want '
                + 'to delete this record? This action cannot be undone.', messageBtns);
    });

    /**
     * Event: Save Secretary Button "Click"
     */
    $('#save-secretary').click(function() {
        var secretary = {
            'first_name': $('#secretary-first-name').val(),
            'last_name': $('#secretary-last-name').val(),
            'email': $('#secretary-email').val(),
            'mobile_number': $('#secretary-mobile-number').val(),
            'phone_number': $('#secretary-phone-number').val(),
            'address': $('#secretary-address').val(),
            'city': $('#secretary-city').val(),
            'state': $('#secretary-state').val(),
            'zip_code': $('#secretary-zip-code').val(),
            'notes': $('#secretary-notes').val(),
            'settings': {
                'username': $('#secretary-username').val(),                    
                'notifications': $('#secretary-notifications').hasClass('active')
            }
        };

        // Include secretary services.
        secretary.providers = [];
        $('#secretary-providers input[type="checkbox"]').each(function() {
            if ($(this).prop('checked')) {
                secretary.providers.push($(this).attr('data-id'));
            }
        });

        // Include password if changed.
        if ($('#secretary-password').val() !== '') {
            secretary.settings.password = $('#secretary-password').val();
        }

        // Include id if changed.
        if ($('#secretary-id').val() !== '') {
            secretary.id = $('#secretary-id').val();
        }

        if (!BackendUsers.helper.validate(secretary)) return;

        BackendUsers.helper.save(secretary);
    });

    /**
     * Event: Cancel Secretary Button "Click"
     * 
     * Cancel add or edit of an secretary record.
     */
    $('#cancel-secretary').click(function() {
        BackendUsers.helper.resetForm();
    });
};

/**
 * Save secretary record to database.
 * 
 * @param {object} secretary Contains the admin record data. If an 'id' value is provided
 * then the update operation is going to be executed.
 */
SecretariesHelper.prototype.save = function(secretary) {
    ////////////////////////////////////////////////////
    //console.log('Secretary data to save:', secretary);
    ////////////////////////////////////////////////////
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_secretary';
    var postData = { 'secretary': JSON.stringify(secretary) };
    
    $.post(postUrl, postData, function(response) {
        ////////////////////////////////////////////////////
        //console.log('Save Secretary Response:', response);
        ////////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Secretary saved successfully!');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter($('#secretaries .filter-key').val());
    }, 'json');
};

/**
 * Delete a secretary record from database.
 * 
 * @param {int} id Record id to be deleted. 
 */
SecretariesHelper.prototype.delete = function(id) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_delete_secretary';
    var postData = { 'secretary_id': id };
    
    $.post(postUrl, postData, function(response) {
        //////////////////////////////////////////////////////
        //console.log('Delete secretary response:', response);
        //////////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Secretary deleted successfully!');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter($('#secretaries .filter-key').val());
    });
};

/**
 * Validates a secretary record.
 * 
 * @param {object} secretary Contains the admin data to be validated.
 * @returns {bool} Returns the validation result.
 */
SecretariesHelper.prototype.validate = function(secretary) {
    $('#secretaries .required').css('border', '');
    $('#secretary-password, #secretary-password-confirm').css('border', '');
    
    try {
        // Validate required fields.
        var missingRequired = false;
        $('#secretaries .required').each(function() {
            if ($(this).val() == '' || $(this).val() == undefined) {
                $(this).css('border', '2px solid red');
                missingRequired = true;
            }
        });
        if (missingRequired) {
            throw 'Fields with * are  required.';
        }
        
        // Validate passwords.
        if ($('#secretary-password').val() != $('#secretary-password-confirm').val()) {
            $('#secretary-password, #secretary-password-confirm').css('border', '2px solid red');
            throw 'Passwords mismatch!';
        }
        
        if ($('#secretary-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#secretary-password').val() != '') {
            $('#secretary-password, #secretary-password-confirm').css('border', '2px solid red');
            throw 'Password must be at least ' + BackendUsers.MIN_PASSWORD_LENGTH 
                    + ' characters long.';
        }
        
        // Validate user email.
        if (!GeneralFunctions.validateEmail($('#secretary-email').val())) {
            $('#secretary-email').css('border', '2px solid red');
            throw 'Invalid email address!';
        }
        
        return true;
    } catch(exc) {
        $('#secretaries .form-message').text(exc);
        $('#secretaries .form-message').show();
        return false;
    }
};

/**
 * Resets the admin tab form back to its initial state. 
 */
SecretariesHelper.prototype.resetForm = function() {
    $('#secretaries .details').find('input, textarea').val('');
    $('#secretaries .add-edit-delete-group').show();
    $('#secretaries .save-cancel-group').hide();
    $('#edit-secretary, #delete-secretary').prop('disabled', true);
    $('#secretaries .details').find('input, textarea').prop('readonly', true);
    $('.filter-secretaries').prop('disabled', false);
    $('#secretaries .filter-results').css('color', '');
    $('#secretaries .form-message').hide();    
    $('#secretary-notifications').removeClass('active');
    $('#secretary-notifications').prop('disabled', true);
    $('#secretary-providers input[type="checkbox"]').prop('checked', false);
    $('#secretary-providers input[type="checkbox"]').prop('disabled', true);
    $('#secretaries .required').css('border', '');
    $('#secretary-password, #secretary-password-confirm').css('border', '');
};

/**
 * Display a secretary record into the admin form.
 * 
 * @param {object} secretary Contains the secretary record data.
 */
SecretariesHelper.prototype.display = function(secretary) {
    $('#secretary-id').val(secretary.id);
    $('#secretary-first-name').val(secretary.first_name);
    $('#secretary-last-name').val(secretary.last_name);
    $('#secretary-email').val(secretary.email);
    $('#secretary-mobile-number').val(secretary.mobile_number);
    $('#secretary-phone-number').val(secretary.phone_number);
    $('#secretary-address').val(secretary.address);
    $('#secretary-city').val(secretary.city);
    $('#secretary-state').val(secretary.state);
    $('#secretary-zip-code').val(secretary.zip_code);
    $('#secretary-notes').val(secretary.notes);
    
    $('#secretary-username').val(secretary.settings.username);
    if (secretary.settings.notifications == true) {
        $('#secretary-notifications').addClass('active');
    } else {
        $('#secretary-notifications').removeClass('active');
    }
    
    $('#secretary-providers input[type="checkbox"]').prop('checked', false);
    $.each(secretary.providers, function(index, providerId) {
        $('#secretary-providers input[type="checkbox"]').each(function() {
            if ($(this).attr('data-id') == providerId) {
                $(this).prop('checked', true);
            }
        });
    });
};

/**
 * Filters secretary records depending a string key.
 * 
 * @param {string} key This is used to filter the secretary records of the database.
 * @param {numeric} selectRecordId (OPTIONAL) If provided then the given id will be 
 * selected in the filter results (only selected, not displayed).
 */
SecretariesHelper.prototype.filter = function(key, selectRecordId) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_secretaries';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        ////////////////////////////////////////////////////////
        //console.log('Filter secretaries response:', response);
        ////////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        BackendUsers.helper.filterResults = response;
        
        $('#secretaries .filter-results').html('');
        $.each(response, function(index, secretary) {
            var html = SecretariesHelper.prototype.getFilterHtml(secretary);
            $('#secretaries .filter-results').append(html);
        });
        
        if (selectRecordId != undefined) {
            $('.secretary-row').each(function() {
                if ($(this).attr('data-id') == selectRecordId) {
                    $(this).addClass('selected-row');
                    return false;
                }
            });
        }
    }, 'json');
};

/**
 * Get an secretary row html code that is going to be displayed on the filter results list.
 * 
 * @param {object} secretary Contains the secretary record data.
 * @returns {string} The html code that represents the record on the filter results list.
 */
SecretariesHelper.prototype.getFilterHtml = function(secretary) {
    var html =
            '<div class="secretary-row" data-id="' + secretary.id + '">' + 
                '<strong>' + secretary.first_name + ' ' + secretary.last_name + '</strong><br>' +
                secretary.email + ', ' + secretary.mobile_number + ', ' + secretary.phone_number + '<br>' + 
            '</div>';

    return html;
};