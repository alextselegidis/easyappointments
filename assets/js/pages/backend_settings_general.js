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

window.BackendSettingsGeneral = window.BackendSettingsGeneral || {};

/**
 * Backend Settings
 *
 * Contains the functionality of the backend settings page. Can either work for system or user settings,
 * but the actions allowed to the user are restricted to his role (only admin has full privileges).
 *
 * @module BackendSettingsGeneral
 */
(function (exports) {
    'use strict';

    // Constants
    exports.SETTINGS_SYSTEM = 'SETTINGS_SYSTEM';

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

        // Apply setting values from database.
        GlobalVariables.settings.system.forEach(function (setting) {
            $('input[data-field="' + setting.name + '"]').val(setting.value);
            $('select[data-field="' + setting.name + '"]').val(setting.value);
        });

        // Set default settings helper.
        settings = new SystemSettingsGeneralHelper();

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

        /**
         * Event: require phone number switch "Click"
         *
         * make sure that our phone number is visible when it is required.
         */
        $('#require-phone-number').on('click', function () {
            if ($(this).prop('checked')) {
                setShowToggleValue($('#show-phone-number'), true);
            }
        });
    }
})(window.BackendSettingsGeneral);
