/**
 * This class contains the Admins helper class declaration, along with the "Admins" tab 
 * event handlers. By deviding the backend/users tab functionality into separate files
 * it is easier to maintain the code.
 * 
 * @class AdminsHelper
 */
var AdminsHelper = function() {
    this.filterResults = {}; // Store the results for later use.
};

/**
 * Bind the event handlers for the backend/users "Admins" tab.
 */
AdminsHelper.prototype.bindEventHandlers = function() {
    /**
     * Event: Filter Admins Button "Click"
     * 
     * Filter the admin records with the given key string.
     */
    $('.filter-admins').click(function() {
        var key = $('#admins .filter-key').val();
        $('.selected-row').removeClass('selected-row');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter(key);
    });

    /**
     * Event: Filter Admin Row "Click"
     * 
     * Display the selected admin data to the user.
     */
    $(document).on('click', '.admin-row', function() {
        if ($('#admins .filter-admins').prop('disabled')) {
            $('#admins .filter-results').css('color', '#AAA');
            return; // exit because we are currently on edit mode
        }

        var admin = { 'id': $(this).attr('data-id') };
        $.each(BackendUsers.helper.filterResults, function(index, item) {
            if (item.id === admin.id) {
                admin = item;
                return;
            }
        });
        BackendUsers.helper.display(admin);
        $('.selected-row').removeClass('selected-row');
        $(this).addClass('selected-row');
        $('#edit-admin, #delete-admin').prop('disabled', false);
    });

    /**
     * Event: Add New Admin Button "Click"
     */
    $('#add-admin').click(function() {
        BackendUsers.helper.resetForm();
        $('#admins .add-edit-delete-group').hide();
        $('#admins .save-cancel-group').show();
        $('#admins .details').find('input, textarea').prop('readonly', false);
        $('#admins .filter-admins').prop('disabled', true);
        $('#admins .filter-results').css('color', '#AAA');
        $('#admin-password, #admin-password-confirm').addClass('required');
        $('#admin-notifications').prop('disabled', false);
    });

    /**
     * Event: Edit Admin Button "Click"
     */
    $('#edit-admin').click(function() {
        $('#admins .add-edit-delete-group').hide();
        $('#admins .save-cancel-group').show();
        $('.filter-admins').prop('disabled', true);
        $('#admins .filter-results').css('color', '#AAA');
        $('#admins .details').find('input, textarea').prop('readonly', false);
        $('#admin-password, #admin-password-confirm').removeClass('required');
        $('#admin-notifications').prop('disabled', false);
    });

    /**
     * Event: Delete Admin Button "Click"
     */
    $('#delete-admin').click(function() {
        var adminId = $('#admin-id').val();

        var messageBtns = {
            'Delete': function() {
                BackendUsers.helper.delete(adminId);
                $('#message_box').dialog('close');
            },
            'Cancel': function() {
                $('#message_box').dialog('close');
            }
        };

        GeneralFunctions.displayMessageBox('Delete Admin', 'Are you sure that you want '
                + 'to delete this record? This action cannot be undone.', messageBtns);
    });

    /**
     * Event: Save Admin Button "Click"
     */
    $('#save-admin').click(function() {
        var admin = {
            'first_name': $('#admin-first-name').val(),
            'last_name': $('#admin-last-name').val(),
            'email': $('#admin-email').val(),
            'mobile_number': $('#admin-mobile-number').val(),
            'phone_number': $('#admin-phone-number').val(),
            'address': $('#admin-address').val(),
            'city': $('#admin-city').val(),
            'state': $('#admin-state').val(),
            'zip_code': $('#admin-zip-code').val(),
            'notes': $('#admin-notes').val(),
            'settings': {
                'username': $('#admin-username').val(),                    
                'notifications': $('#admin-notifications').hasClass('active')
            }
        };

        // Include password if changed.
        if ($('#admin-password').val() !== '') {
            admin.settings.password = $('#admin-password').val();
        }

        // Include id if changed.
        if ($('#admin-id').val() !== '') {
            admin.id = $('#admin-id').val();
        }

        if (!BackendUsers.helper.validate(admin)) return;

        BackendUsers.helper.save(admin);
    });

    /**
     * Event: Cancel Admin Button "Click"
     * 
     * Cancel add or edit of an admin record.
     */
    $('#cancel-admin').click(function() {
        BackendUsers.helper.resetForm();
        if ($('admins .selected-row').length == 0) return; 
        var id = $('#admins .selected-row').attr('data-id');
        BackendUsers.helper.selectRecord(id);
    });
};

/**
 * Save admin record to database.
 * 
 * @param {object} admin Contains the admin record data. If an 'id' value is provided
 * then the update operation is going to be executed.
 */
AdminsHelper.prototype.save = function(admin) {
    ////////////////////////////////////////////
    //console.log('Admin data to save:', admin);
    ////////////////////////////////////////////
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_admin';
    var postData = { 'admin': JSON.stringify(admin) };
    
    $.post(postUrl, postData, function(response) {
        ////////////////////////////////////////////////
        //console.log('Save Admin Response:', response);
        ////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Admin saved successfully!');
        BackendUsers.helper.resetForm(true);
        // When adding a new record the "admin.id" will be undefined. In this situation
        // no record will be selected because we do not yet know the id of the new record,
        // but no error will occur either.
        BackendUsers.helper.filter($('#admins .filter-key').val(), admin.id); 
    }, 'json');
};

/**
 * Delete an admin record from database.
 * 
 * @param {int} id Record id to be deleted. 
 */
AdminsHelper.prototype.delete = function(id) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_delete_admin';
    var postData = { 'admin_id': id };
    
    $.post(postUrl, postData, function(response) {
        //////////////////////////////////////////////////
        //console.log('Delete admin response:', response);
        //////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification('Admin deleted successfully!');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter($('#admins .filter-key').val());
    });
};

/**
 * Validates an admin record.
 * 
 * @param {object} admin Contains the admin data to be validated.
 * @returns {bool} Returns the validation result.
 */
AdminsHelper.prototype.validate = function(admin) {
    $('#admins .required').css('border', '');
    $('#admin-password, #admin-password-confirm').css('border', '');
    
    try {
        // Validate required fields.
        var missingRequired = false;
        $('#admins .required').each(function() {
            if ($(this).val() == '' || $(this).val() == undefined) {
                $(this).css('border', '2px solid red');
                missingRequired = true;
            }
        });
        if (missingRequired) {
            throw 'Fields with * are  required.';
        }
        
        // Validate passwords.
        if ($('#admin-password').val() != $('#admin-password-confirm').val()) {
            $('#admin-password, #admin-password-confirm').css('border', '2px solid red');
            throw 'Passwords mismatch!';
        }
        
        if ($('#admin-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#admin-password').val() != '') {
            $('#admin-password, #admin-password-confirm').css('border', '2px solid red');
            throw 'Password must be at least ' + BackendUsers.MIN_PASSWORD_LENGTH 
                    + ' characters long.';
        }
        
        // Validate user email.
        if (!GeneralFunctions.validateEmail($('#admin-email').val())) {
            $('#admin-email').css('border', '2px solid red');
            throw 'Invalid email address!';
        }
        
        return true;
    } catch(exc) {
        $('#admins .form-message').text(exc);
        $('#admins .form-message').show();
        return false;
    }
};

/**
 * Resets the admin tab form back to its initial state. 
 * 
 * @param {bool} keepRecordData (OPTIONAL = false) If false then the current record data 
 * will remain on the form.
 */
AdminsHelper.prototype.resetForm = function(keepRecordData) {
    if (keepRecordData == undefined) keepRecordData = false;
    
    $('#admins .add-edit-delete-group').show();
    $('#admins .save-cancel-group').hide();
    $('#admins .details').find('input, textarea').prop('readonly', true);
    $('.filter-admins').prop('disabled', false);
    $('#admins .filter-results').css('color', '');
    $('#admins .form-message').hide();    
    $('#admin-notifications').prop('disabled', true);
    $('#admins .required').css('border', '');
    $('#admin-password, #admin-password-confirm').css('border', '');
    
    if (!keepRecordData) {
        $('#admins .details').find('input, textarea').val('');
        $('#admin-notifications').removeClass('active');
        $('#edit-admin, #delete-admin').prop('disabled', true);
    }
};

/**
 * Display a admin record into the admin form.
 * 
 * @param {object} admin Contains the admin record data.
 */
AdminsHelper.prototype.display = function(admin) {
    $('#admin-id').val(admin.id);
    $('#admin-first-name').val(admin.first_name);
    $('#admin-last-name').val(admin.last_name);
    $('#admin-email').val(admin.email);
    $('#admin-mobile-number').val(admin.mobile_number);
    $('#admin-phone-number').val(admin.phone_number);
    $('#admin-address').val(admin.address);
    $('#admin-city').val(admin.city);
    $('#admin-state').val(admin.state);
    $('#admin-zip-code').val(admin.zip_code);
    $('#admin-notes').val(admin.notes);
    
    $('#admin-username').val(admin.settings.username);
    if (admin.settings.notifications == true) {
        $('#admin-notifications').addClass('active');
    } else {
        $('#admin-notifications').removeClass('active');
    }
};

/**
 * Filters admin records depending a string key.
 * 
 * @param {string} key This is used to filter the admin records of the database.
 */
AdminsHelper.prototype.filter = function(key, selectRecordId) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_admins';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        ///////////////////////////////////////////////////
        //console.log('Filter admins response:', response);
        ///////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        BackendUsers.helper.filterResults = response;
        
        $('#admins .filter-results').html('');
        $.each(response, function(index, admin) {
            var html = AdminsHelper.prototype.getFilterHtml(admin);
            $('#admins .filter-results').append(html);
        });
        
        if (selectRecordId != undefined) {
            $('.admin-row').each(function() {
                if ($(this).attr('data-id') == selectRecordId) {
                    $(this).addClass('selected-row');
                    return false;
                }
            });
        }
    }, 'json');
};

/**
 * Get an admin row html code that is going to be displayed on the filter results list.
 * 
 * @param {object} admin Contains the admin record data.
 * @returns {string} The html code that represents the record on the filter results list.
 */
AdminsHelper.prototype.getFilterHtml = function(admin) {
    var html =
            '<div class="admin-row" data-id="' + admin.id + '">' + 
                '<strong>' + admin.first_name + ' ' + admin.last_name + '</strong><br>' +
                admin.email + ', ' + admin.mobile_number + ', ' + admin.phone_number + '<br>' + 
            '</div>';

    return html;
};

/**
 * Select a specific record from the current filter results. If the admin does not exist in 
 * the list then no record will be selected. 
 * 
 * @param {numeric} id The record id to be selected.
 * @param {bool} display (OPTIONAL = false) If true then the method will display the record
 * on the form.
 */
AdminsHelper.prototype.selectRecord = function(id, display) {
    if (display == undefined) display = false;
    
    $('#admins .selected-row').removeClass('selected-row');
    
    $('.admin-row').each(function() {
        if ($(this).attr('data-id') == id) {
            $(this).addClass('selected-row');
            return;
        }
    });
    
    if (display) { 
        var admin;
        $.each(BackendUsers.helper.filterResults, function(index, item) {
            if (item.id === id) {
                admin = item;
                BackendUsers.helper.display(admin);
                $('#edit-admin, #delete-admin').prop('disabled', false);
                return;
            }
        });
    }
};

