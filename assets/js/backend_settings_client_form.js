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

window.BackendSettingsClientForm = window.BackendSettingsClientForm || {};

/**
 * Backend Settings
 *
 * Contains the functionality of the backend settings page. Can either work for system or user settings,
 * but the actions allowed to the user are restricted to his role (only admin has full privileges).
 *
 * @module BackendSettingsClientForm
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

            if (setting.name === 'customer_notifications') {
                $('#customer-notifications').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'require_captcha') {
                $('#require-captcha').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'require_phone_number') {
                $('#require-phone-number').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'display_any_provider') {
                $('#display-any-provider').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'display_cookie_notice') {
                $('#display-cookie-notice').prop('checked', Boolean(Number(setting.value)));
            }

        });

        // Set default settings helper.
        settings = new SystemSettingsClientFormHelper();

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
         * Event: Visible/Hidden button "Click"
         *
         * Change the state of the Visible/Hidden button
         */
        $('.hide-toggle').on('click', function () {
            var $input = $(this);
            $input.find('span').toggleClass('hidden');
        });

        /**
         * set a Visible/Hidden toggle button to a certain state
         *
         * @argument $element for which jquery element to set the state for
         * @argument isVisible a boolean which is true if the button should display 'visible' and false when the button should display 'hidden'
         *
         */
        function setShowToggleValue($element, isVisible) {
            if (getShowToggleValue($element) !== isVisible) {
                $element.find('span').toggleClass('hidden');
            }
        }

        /**
         * get the Visible/Hidden toggle button
         *
         * @argument $element for which jquery element to set the state for
         *
         * @return the state of the button. True for visible, false for hidden.
         */
        function getShowToggleValue($element) {
            var visiblePartArray = $element.find('.hide-toggle-visible');
            return !visiblePartArray.hasClass('hidden');
        }

        /**
         * Event: require phone number switch "Click"
         *
         * make sure that our phone number is visible when it is required.
         */
        $('#show-phone-number').on('click', function () {
            if (!getShowToggleValue($(this))) {
                //if button is set to hidden
                $('#require-phone-number').prop('checked', false);
            }
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
})(window.BackendSettingsClientForm);
