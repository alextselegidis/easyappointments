/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

window.BackendSettingsCurrentUser = window.BackendSettingsCurrentUser || {};

/**
 * Backend Settings
 *
 * Contains the functionality of the backend settings page. Can either work for system or user settings,
 * but the actions allowed to the user are restricted to his role (only admin has full privileges).
 *
 * @module BackendSettingsCurrentUser
 */
(function (exports) {
    'use strict';

    // Constants
    exports.SETTINGS_USER = 'SETTINGS_USER';

    /**
     * Use this WorkingPlan class instance to perform actions on the page's working plan tables.
     *
     * @type {WorkingPlan}
     */
    exports.wp = {};

    /**
     * Tab settings object.
     *
     * @type {Object}
     */
    var settings = {};

    /**
     * Initialize Page
     *
     * @param {bool} defaultEventHandlers Optional (true), determines whether to bind the default event handlers.
     */
    exports.initialize = function (defaultEventHandlers) {
        defaultEventHandlers = defaultEventHandlers || true;

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
        $('#timezone').val(GlobalVariables.settings.user.timezone);
        $('#username').val(GlobalVariables.settings.user.settings.username);
        $('#password, #retype-password').val('');
        $('#calendar-view').val(GlobalVariables.settings.user.settings.calendar_view);
        $('#user-notifications').prop('checked', Boolean(Number(GlobalVariables.settings.user.settings.notifications)));

        // Set default settings helper.
        settings = new SystemSettingsCurrentUserHelper();

        if (defaultEventHandlers) {
            bindEventHandlers();
        }

        Backend.placeFooterToBottom();
    };

    /**
     * Bind the backend/settings default event handlers.
     *
     * This method depends on the backend/settings html, so do not use this method on a different page.
     */
    function bindEventHandlers() {
        /**
         * Event: Save Settings Button "Click"
         *
         * Store the setting changes into the database.
         */
        $('.save-settings').on('click', function () {
            var data = settings.get();
            settings.save(data);
        });
    }
})(window.BackendSettingsCurrentUser);
