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
            $('#general input[data-field="' + setting.name + '"]').val(setting.value);
        });
        
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
            BackendSettings.settings.save();
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
    var settings = []
    var tmpSetting = {};
    
    // General Settings Tab
    $('#general input').each(function() {
        tmpSetting[$(this).attr('data-field')] = $(this).val();
        settings.push(tmpSetting);
    });
    
    // Business Logic Tab
    // @task Get business logic settings.
    
    return settings;
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
    
}

/**
 * Store the user settings into the database.
 * 
 * @param {array} settings Contains the user settings.
 */
UserSettings.prototype.save = function(settings) {
    
}
