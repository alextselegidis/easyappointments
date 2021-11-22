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
     * @class SystemSettingsLegalContentsHelper
     */
    var SystemSettingsLegalContentsHelper = function () {};

    /**
     * Save the system settings.
     *
     * This method is run after changes are detected on the tab input fields.
     *
     * @param {Array} settings Contains the system settings data.
     */
    SystemSettingsLegalContentsHelper.prototype.save = function (settings) {
        if (!this.validate()) {
            return; // Validation failed, do not proceed.
        }

        var url = GlobalVariables.baseUrl + '/index.php/settings/legal_contents/save';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            settings: JSON.stringify(settings),
            type: BackendSettingsLegalContents.SETTINGS_SYSTEM
        };

        $.post(url, data).done(function () {
            Backend.displayNotification(EALang.settings_saved);
        });
    };

    /**
     * Prepare the system settings array.
     *
     * This method uses the DOM elements of the backend/settings page, so it can't be used in another page.
     *
     * @return {Array} Returns the system settings array.
     */
    SystemSettingsLegalContentsHelper.prototype.get = function () {
        var settings = [];

        // Legal Contents Tab

        settings.push({
            name: 'display_cookie_notice',
            value: $('#display-cookie-notice').prop('checked') ? '1' : '0'
        });

        settings.push({
            name: 'cookie_notice_content',
            value: $('#cookie-notice-content').trumbowyg('html')
        });

        settings.push({
            name: 'display_terms_and_conditions',
            value: $('#display-terms-and-conditions').prop('checked') ? '1' : '0'
        });

        settings.push({
            name: 'terms_and_conditions_content',
            value: $('#terms-and-conditions-content').trumbowyg('html')
        });

        settings.push({
            name: 'display_privacy_policy',
            value: $('#display-privacy-policy').prop('checked') ? '1' : '0'
        });

        settings.push({
            name: 'privacy_policy_content',
            value: $('#privacy-policy-content').trumbowyg('html')
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
    SystemSettingsLegalContentsHelper.prototype.validate = function () {
        $('#legal-contents .has-error').removeClass('has-error');

        try {
            // Validate required fields.
            var missingRequired = false;
            $('#general .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).closest('.form-group').addClass('has-error');
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

    window.SystemSettingsLegalContentsHelper = SystemSettingsLegalContentsHelper;
})();
