/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

window.BackendSettingsLegalContents = window.BackendSettingsLegalContents || {};

/**
 * Backend Settings
 *
 * Contains the functionality of the backend settings page. Can either work for system or user settings,
 * but the actions allowed to the user are restricted to his role (only admin has full privileges).
 *
 * @module BackendSettingsLegalContents
 */
(function (exports) {
    'use strict';

    // Constants
    exports.SETTINGS_SYSTEM = 'SETTINGS_SYSTEM';

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

        $('#cookie-notice-content, #terms-and-conditions-content, #privacy-policy-content').trumbowyg();

        // Apply setting values from database.
        var workingPlan = {};

        GlobalVariables.settings.system.forEach(function (setting) {
            $('input[data-field="' + setting.name + '"]').val(setting.value);
            $('select[data-field="' + setting.name + '"]').val(setting.value);

            if (setting.name === 'display_cookie_notice') {
                $('#display-cookie-notice').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'cookie_notice_content') {
                $('#cookie-notice-content').trumbowyg('html', setting.value);
            }

            if (setting.name === 'display_terms_and_conditions') {
                $('#display-terms-and-conditions').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'terms_and_conditions_content') {
                $('#terms-and-conditions-content').trumbowyg('html', setting.value);
            }

            if (setting.name === 'display_privacy_policy') {
                $('#display-privacy-policy').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'privacy_policy_content') {
                $('#privacy-policy-content').trumbowyg('html', setting.value);
            }
        });

        // Set default settings helper.
        settings = new SystemSettingsLegalContentsHelper();

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
})(window.BackendSettingsLegalContents);
