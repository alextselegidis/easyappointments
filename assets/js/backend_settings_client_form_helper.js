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

(function () {
    'use strict';

    /**
     * "System Settings" Tab Helper Class
     *
     * @class SystemSettingsClientFormHelper
     */
    var SystemSettingsClientFormHelper = function () {};

    /**
     * Save the system settings.
     *
     * This method is run after changes are detected on the tab input fields.
     *
     * @param {Array} settings Contains the system settings data.
     */
    SystemSettingsClientFormHelper.prototype.save = function (settings) {
        if (!this.validate()) {
            return; // Validation failed, do not proceed.
        }

        var url = GlobalVariables.baseUrl + '/index.php/settings/client_form/save';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            settings: JSON.stringify(settings),
            type: BackendSettingsClientForm.SETTINGS_SYSTEM
        };

        $.post(url, data).done(function () {
            Backend.displayNotification(EALang.settings_saved);
        });
    };

    /**
     * Get the state of a visible/hidden toggle button
     *
     * This method uses the DOM elements of the backend/settings page, so it can't be used in another page.
     *
     * @argument the element jquery of a button object that is a visible/hidden toggle.
     *
     * @return '0' when the button shows 'invisible' and '1' when the button shows 'visible'. Will always return '0' on an error.
     */
    function getToggleButtonState($element) {
        var visiblePartArray = $element.find('.hide-toggle-visible');
        var invisiblePartArray = $element.find('.hide-toggle-hidden');
        if (!(visiblePartArray.length === 0 || invisiblePartArray.length === 0)) {
            if (visiblePartArray.prop('hidden')) {
                //our button is currently invisible
                return '0'; //invisible
            } else {
                //our button is currently visible
                return '1'; //visible
            }
        } else {
            return '0'; //invisible
        }
    }

    /**
     * Prepare the system settings array.
     *
     * This method uses the DOM elements of the backend/settings page, so it can't be used in another page.
     *
     * @return {Array} Returns the system settings array.
     */
    SystemSettingsClientFormHelper.prototype.get = function () {
        var settings = [];

        settings.push({
            name: 'customer_notifications',
            value: $('#customer-notifications').prop('checked') ? '1' : '0'
        });

        settings.push({
            name: 'require_captcha',
            value: $('#require-captcha').prop('checked') ? '1' : '0'
        });

        settings.push({
            name: 'require_phone_number',
            value: $('#require-phone-number').prop('checked') ? '1' : '0'
        });

        settings.push({
            name: 'display_any_provider',
            value: $('#display-any-provider').prop('checked') ? '1' : '0'
        });

        settings.push({
            name: 'show_phone_number',
            value: getToggleButtonState($('#show-phone-number'))
        });

        settings.push({
            name: 'show_address',
            value: getToggleButtonState($('#show-address'))
        });

        settings.push({
            name: 'show_city',
            value: getToggleButtonState($('#show-city'))
        });

        settings.push({
            name: 'show_zip_code',
            value: getToggleButtonState($('#show-zip-code'))
        });

        settings.push({
            name: 'show_notes',
            value: getToggleButtonState($('#show-notes'))
        });

        return settings;
    };

    /**
     * Validate the settings data.
     *
     * If the validation fails then display a message to the user.
     *
     * @return {Boolean} Returns the validation result.
     */
    SystemSettingsClientFormHelper.prototype.validate = function () {
        $('#client-form .is-invalid').removeClass('is-invalid');

        try {
            // Validate required fields.
            var missingRequired = false;
            $('#client-form .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(EALang.fields_are_required);
            }

            return true;
        } catch (error) {
            Backend.displayNotification(error.message);
            return false;
        }
    };

    window.SystemSettingsClientFormHelper = SystemSettingsClientFormHelper;
})();
