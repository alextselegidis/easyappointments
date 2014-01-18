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
     * Use this WorkingPlan class instance to perform actions on the page's working plan
     * tables.
     */
    wp: {},
    
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
        
        // Apply setting values from database.
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
        
        BackendSettings.wp = new WorkingPlan();
        BackendSettings.wp.setup(workingPlan);
        BackendSettings.wp.timepickers(false);
        
        // Book Advance Timeout Spinner
        $('#book-advance-timeout').spinner({
            'min': 0
        });
        
        // Load user settings into form
        $('#user-id').val(GlobalVariables.settings.user.id);
        $('#first-name').val(GlobalVariables.settings.user.first_name);
        $('#last-name').val(GlobalVariables.settings.user.last_name);
        $('#email').val(GlobalVariables.settings.user.email);
        $('#mobile-number').val(GlobalVariables.settings.user.mobile_number);
        $('#phone-number').val(GlobalVariables.settings.user.phone_number);
        $('#address').val(GlobalVariables.settings.user.address);
        $('#city').val(GlobalVariables.settings.user.city);
        $('#state').val(GlobalVariables.settings.user.state);
        $('#zip-code').val(GlobalVariables.settings.user.zip_code);
        $('#notes').val(GlobalVariables.settings.user.notes);
        
        $('#username').val(GlobalVariables.settings.user.settings.username);
        $('#password, #retype-password').val('');
        
        if (GlobalVariables.settings.user.settings.notifications == true) { 
            $('#user-notifications').addClass('active');
        } else {
            $('#user-notifications').removeClass('active');
        }
        
        // Set default settings helper.
        BackendSettings.settings = new SystemSettings();
        
        if (bindEventHandlers) {
            BackendSettings.bindEventHandlers();
            $('#settings-page .nav li').first().addClass('active');
            $('#settings-page .nav li').first().find('a').trigger('click');
        }
        
        // Apply Privileges
        if (GlobalVariables.user.privileges.system_settings.edit == false) {
            $('#general, #business-logic').find('select, input, textarea').prop('readonly', true);
            $('#general, #business-logic').find('button').prop('disabled', true);
        }
        
        if (GlobalVariables.user.privileges.user_settings.edit == false) {
            $('#user').find('select, input, textarea').prop('readonly', true);
            $('#user').find('button').prop('disabled', true);
        }
        
        Backend.placeFooterToBottom();
    },
            
    /**
     * Bind the backend/settings default event handlers. This method depends on the 
     * backend/settings html, so do not use this method on a different page.
     */
    bindEventHandlers: function() {
        BackendSettings.wp.bindEventHandlers();
        
        /**
         * Event: Tab "Click"
         * 
         * Change the visible tab contents.
         */
        $('.tab').click(function() {
            // Bootstrap has a bug with toggle buttons. Their toggle state is lost when the
            // button is hidden or shown. Show before anything else we need to store the toggle 
            // and apply it whenever the user tab is clicked..
            var areNotificationsActive = $('#user-notifications').hasClass('active');
            
            $(this).parent().find('.active').removeClass('active');
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
               
                // Apply toggle state to user notifications button.
                if (areNotificationsActive) {
                    $('#user-notifications').addClass('active');
                } else {
                    $('#user-notifications').removeClass('active');   
                }
            } else if ($(this).hasClass('about-tab')) {
                $('#about').show();
            }
            
            Backend.placeFooterToBottom();
        });
        
        /**
         * Event: Save Settings Button "Click"
         * 
         * Store the setting changes into the database.
         */
        $('.save-settings').click(function() {
            var settings = BackendSettings.settings.get();
            BackendSettings.settings.save(settings);
            //////////////////////////////////////////////
            //console.log('Settings To Save: ', settings);
            //////////////////////////////////////////////
        });
        
        /**
         * Event: Username "Focusout" 
         * 
         * When the user leaves the username input field we will need to check if the username 
         * is not taken by another record in the system. Usernames must be unique.
         */
        $('#username').focusout(function() {
            var $input = $(this);
            
            if ($input.prop('readonly') == true || $input.val() == '') return;
            
            var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_validate_username';
            var postData = { 
                'username': $input.val(), 
                'user_id': $input.parents().eq(2).find('#user-id').val()
            };
            
            $.post(postUrl, postData, function(response) {
                ///////////////////////////////////////////////////////
                //console.log('Validate Username Response:', response);
                ///////////////////////////////////////////////////////
                if (!GeneralFunctions.handleAjaxExceptions(response)) return;
                if (response == false) {
                    $input.css('border', '2px solid red');
                    Backend.displayNotification(EALang['username_already_exists']);
                    $input.attr('already-exists', 'true');
                } else {
                    $input.css('border', '');
                    $input.attr('already-exists', 'false');
                }
            }, 'json');
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
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
       
        Backend.displayNotification(EALang['settings_saved']);
        
        // Update the logo title on the header.
        $('#header-logo span').text($('#company-name').val());
        
        // We need to refresh the working plan.
        var workingPlan = BackendSettings.wp.get();
        $('.breaks').empty();
        BackendSettings.wp.setup(workingPlan);
        BackendSettings.wp.timepickers(false);
        
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
    settings.push({
        'name': 'company_working_plan',
        'value': JSON.stringify(BackendSettings.wp.get())
    });
    
    settings.push({
        'name': 'book_advance_timeout',
        'value': $('#book-advance-timeout').val()
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
    $('#general .required').css('border', '');
    
    try {
        // Validate required fields.
        var missingRequired = false;
        $('#general .required').each(function() {
            if ($(this).val() == '' || $(this).val() == undefined) {
                $(this).css('border', '2px solid red');
                missingRequired = true;
            }
        });
        if (missingRequired) {
            throw EALang['fields_are_required'];
        }
        
        // Validate company email address.
        if (!GeneralFunctions.validateEmail($('#company-email').val())) {
            $('#company-email').css('border', '2px solid red');
            throw EALang['invalid_email'];
        }
        
        return true;
    } catch(exc) {
        Backend.displayNotification(exc);
        return false;
    }
};

/**
 * "User Settings" Tab Helper
 * @class UserSettings
 */
var UserSettings = function() {};

/**
 * Get the settings data for the user settings.
 * 
 * @returns {object} Returns the user settings array.
 */
UserSettings.prototype.get = function() {
    var user = {
        'id': $('#user-id').val(),
        'first_name': $('#first-name').val(),
        'last_name': $('#last-name').val(),
        'email': $('#email').val(),
        'mobile_number': $('#mobile-number').val(),
        'phone_number': $('#phone-number').val(),
        'address': $('#address').val(),
        'city': $('#city').val(),
        'state': $('#state').val(),
        'zip_code': $('#zip-code').val(),
        'notes': $('#notes').val(),
        'settings': {
            'username': $('#username').val(),
            'notifications': $('#user-notifications').hasClass('active')
        }
    };
    
    if ($('#password').val() != '') {
        user.settings.password = $('#password').val();
    }
    
    return user;
};

/**
 * Store the user settings into the database.
 * 
 * @param {array} settings Contains the user settings.
 */
UserSettings.prototype.save = function(settings) {
    if (!BackendSettings.settings.validate(settings)) {
        Backend.displayNotification(EALang['user_settings_are_invalid']);
        return; // Validation failed, do not procceed.
    }
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_settings';
    var postData = {
        'type': BackendSettings.SETTINGS_USER,
        'settings': JSON.stringify(settings)
    };
    
    $.post(postUrl, postData, function(response) {
        //////////////////////////////////////////////////////////
        console.log('Save User Settings Response: ', response);
        //////////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        Backend.displayNotification(EALang['settings_saved']);
        
        // Update footer greetings.
        $('#footer-user-display-name').text('Hello, ' + $('#first-name').val() + ' ' + $('#last-name').val() + '!');
        
    }, 'json');
};

/**
 * Validate the settings data. If the validation fails then display a 
 * message to the user.
 * 
 * @returns {bool} Returns the validation result.
 */
UserSettings.prototype.validate = function() {
    $('#user .required').css('border', '');
    $('#user').find('#password, #retype-password').css('border', '');

    try {
        // Validate required fields.
        var missingRequired = false;
        $('#user .required').each(function() {
            if ($(this).val() == '' || $(this).val() == undefined) {
                $(this).css('border', '2px solid red');
                missingRequired = true;
            }
        });
        if (missingRequired) {
            throw EALang['fields_are_required'];
        }
        
        // Validate passwords (if provided).
        if ($('#password').val() != $('#retype-password').val()) {
            $('#password, #retype-password').css('border', '2px solid red');
            throw EALang['passwords_mismatch'];
        }
        
        // Validate user email.
        if (!GeneralFunctions.validateEmail($('#email').val())) {
            $('#email').css('border', '2px solid red');
            throw EALang['invalid_email'];
        }
        
        if ($('#username').attr('already-exists') === 'true') {
            $('#username').css('border', '2px solid red');
            throw EALang['username_already_exists'];
        }
        
        return true;
    } catch(exc) {
        Backend.displayNotification(exc);
        return false;
    }
};
