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
     * Event: Filter Admins Form "Sumbit"
     * 
     * Filter the admin records with the given key string.
     */
    $('#filter-admins form').submit(function(event) {
        var key = $('#filter-admins .key').val();
        $('#filter-admins .selected-row').removeClass('selected-row');
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter(key);
        return false;
    });
    
    /**
     * Event: Clear Filter Results Button "Click"
     */
    $('#filter-admins .clear').click(function() {
        BackendUsers.helper.filter('');
        $('#filter-admins .key').val('');
    });

    /**
     * Event: Filter Admin Row "Click"
     * 
     * Display the selected admin data to the user.
     */
    $(document).on('click', '.admin-row', function() {
        if ($('#filter-admins .filter').prop('disabled')) {
            $('#filter-admins .results').css('color', '#AAA');
            return; // exit because we are currently on edit mode
        }

        var adminId = $(this).attr('data-id');
        var admin = {};
        $.each(BackendUsers.helper.filterResults, function(index, item) {
            if (item.id === adminId) {
                admin = item;
                return false;
            }
        });
        
        BackendUsers.helper.display(admin);
        $('#filter-admins .selected-row').removeClass('selected-row');
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
        $('#admin-password, #admin-password-confirm').addClass('required');
        $('#admin-notifications').prop('disabled', false);
        $('#filter-admins button').prop('disabled', true);
        $('#filter-admins .results').css('color', '#AAA');
    });

    /**
     * Event: Edit Admin Button "Click"
     */
    $('#edit-admin').click(function() {
        $('#admins .add-edit-delete-group').hide();
        $('#admins .save-cancel-group').show();
        $('#admins .details').find('input, textarea').prop('readonly', false);
        $('#admin-password, #admin-password-confirm').removeClass('required');
        $('#admin-notifications').prop('disabled', false);
        
        $('#filter-admins .filter').prop('disabled', true);
        $('#filter-admins .results').css('color', '#AAA');
    });

    /**
     * Event: Delete Admin Button "Click"
     */
    $('#delete-admin').click(function() {
        var adminId = $('#admin-id').val();

        var messageBtns = {};
        messageBtns[EALang['delete']] = function() {
            BackendUsers.helper.delete(adminId);
            $('#message_box').dialog('close');
        };
        messageBtns[EALang['cancel']] = function() {
            $('#message_box').dialog('close');
        };

        GeneralFunctions.displayMessageBox(EALang['delete_admin'], 
                EALang['delete_record_prompt'], messageBtns);
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
        var id = $('#admin-id').val();
        BackendUsers.helper.resetForm();
        if (id != '') {
            BackendUsers.helper.select(id, true);
        }
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
        Backend.displayNotification(EALang['admin_saved']);
        BackendUsers.helper.resetForm();
        $('#filter-admins .key').val('');
        BackendUsers.helper.filter('', response.id, true); 
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
        Backend.displayNotification(EALang['admin_deleted']);
        BackendUsers.helper.resetForm();
        BackendUsers.helper.filter($('#filter-admins .key').val());
    }, 'json');
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
            throw EALang['passwords_mismatch'];
        }
        
        if ($('#admin-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#admin-password').val() != '') {
            $('#admin-password, #admin-password-confirm').css('border', '2px solid red');
            
            throw EALang['password_length_notice'].replace('$number', BackendUsers.MIN_PASSWORD_LENGTH);
        }
        
        // Validate user email.
        if (!GeneralFunctions.validateEmail($('#admin-email').val())) {
            $('#admin-email').css('border', '2px solid red');
            throw EALang['invalid_email'];
        }
        
        // Check if username exists
        if ($('#admin-username').attr('already-exists') ==  'true') {
            $('#admin-username').css('border', '2px solid red');
            throw EALang['username_already_exists'];
        } 
        
        return true;
    } catch(exc) {
        $('#admins .form-message').text(exc);
        $('#admins .form-message').show();
        return false;
    }
};

/**
 * Resets the admin form back to its initial state. 
 */
AdminsHelper.prototype.resetForm = function() {
    $('#admins .add-edit-delete-group').show();
    $('#admins .save-cancel-group').hide();
    $('#admins .details').find('input, textarea').prop('readonly', true);
    $('#admins .form-message').hide();    
    $('#admin-notifications').prop('disabled', true);
    $('#admins .required').css('border', '');
    $('#admin-password, #admin-password-confirm').css('border', '');
    $('#admins .details').find('input, textarea').val('');
    $('#admin-notifications').removeClass('active');
    $('#edit-admin, #delete-admin').prop('disabled', true);
    
    $('#filter-admins .selected-row').removeClass('selected-row');
    $('#filter-admins button').prop('disabled', false);
    $('#filter-admins .results').css('color', '');
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
 * Filters admin records depending a key string.
 * 
 * @param {string} key This string is used to filter the admin records of the database.
 * @param {numeric} selectId (OPTIONAL = undefined) This record id will be selected when 
 * the filter operation is finished.
 * @param {bool} display (OPTIONAL = false) If true the selected record data are going
 * to be displayed on the details column (requires a selected record though).
 */
AdminsHelper.prototype.filter = function(key, selectId, display) {
    if (display == undefined) display = false;
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_admins';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        ///////////////////////////////////////////////////
        //console.log('Filter admins response:', response);
        ///////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        BackendUsers.helper.filterResults = response;
        
        $('#filter-admins .results').data('jsp').destroy();
        $('#filter-admins .results').html('');
        $.each(response, function(index, admin) {
            var html = AdminsHelper.prototype.getFilterHtml(admin);
            $('#filter-admins .results').append(html);
        });
        $('#filter-admins .results').jScrollPane({ mouseWheelSpeed: 70 });
        
        if (response.length == 0) {
            $('#filter-admins .results').html('<em>' + EALang['no_records_found'] + '</em>')
        }
        
        if (selectId != undefined) {
            BackendUsers.helper.select(selectId, display);
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
    var name = admin.first_name + ' ' + admin.last_name;
    var info = admin.email;
    info = (admin.mobile_number != '' && admin.mobile_number != null) 
            ? info + ', ' + admin.mobile_number : info;
    info = (admin.phone_number != '' && admin.phone_number != null) 
            ? info + ', ' + admin.phone_number : info;
    
    var html =
            '<div class="admin-row" data-id="' + admin.id + '">' + 
                '<strong>' + name + '</strong><br>' +
                info + '<br>' + 
            '</div><hr>';

    return html;
};

/**
 * Select a specific record from the current filter results. If the admin id does not exist 
 * in the list then no record will be selected. 
 * 
 * @param {numeric} id The record id to be selected from the filter results.
 * @param {bool} display (OPTIONAL = false) If true then the method will display the record
 * on the form.
 */
AdminsHelper.prototype.select = function(id, display) {
    if (display == undefined) display = false;
    
    $('#filter-admins .selected-row').removeClass('selected-row');
    
    $('.admin-row').each(function() {
        if ($(this).attr('data-id') == id) {
            $(this).addClass('selected-row');
            return false;
        }
    });
    
    if (display) { 
        $.each(BackendUsers.helper.filterResults, function(index, admin) {
            if (admin.id == id) {
                BackendUsers.helper.display(admin);
                $('#edit-admin, #delete-admin').prop('disabled', false);
                return false;
            }
        });
    }
};

