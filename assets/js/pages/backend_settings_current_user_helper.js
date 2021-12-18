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

(function () {
    'use strict';

    /**
     * "User Settings" Tab Helper Class
     *
     * @class SystemSettingsCurrentUserHelper
     */
    var SystemSettingsCurrentUserHelper = function () {};

    /**
     * Get the settings data for the user settings.
     *
     * @return {Object} Returns the user settings array.
     */
    SystemSettingsCurrentUserHelper.prototype.get = function () {
        var user = {
            id: $('#user-id').val(),
            first_name: $('#first-name').val(),
            last_name: $('#last-name').val(),
            email: $('#email').val(),
            mobile_number: $('#mobile-number').val(),
            phone_number: $('#phone-number').val(),
            address: $('#address').val(),
            city: $('#city').val(),
            state: $('#state').val(),
            zip_code: $('#zip-code').val(),
            notes: $('#notes').val(),
            timezone: $('#timezone').val(),
            settings: {
                username: $('#username').val(),
                notifications: $('#user-notifications').prop('checked'),
                calendar_view: $('#calendar-view').val()
            }
        };

        if ($('#password').val()) {
            user.settings.password = $('#password').val();
        }

        return user;
    };

    /**
     * Store the user settings into the database.
     *
     * @param {Array} settings Contains the user settings.
     */
    SystemSettingsCurrentUserHelper.prototype.save = function (settings) {
        if (!this.validate(settings)) {
            Backend.displayNotification(App.Lang.user_settings_are_invalid);
            return; // Validation failed, do not proceed.
        }

        var url = GlobalVariables.baseUrl + '/index.php/account_settings/save';

        var data = {
            csrf_token: GlobalVariables.csrfToken,
            type: BackendSettingsCurrentUser.SETTINGS_USER,
            settings: JSON.stringify(settings)
        };

        $.post(url, data).done(function () {
            Backend.displayNotification(App.Lang.settings_saved);

            // Update footer greetings.
            $('#footer-user-display-name').text('Hello, ' + $('#first-name').val() + ' ' + $('#last-name').val() + '!');
        });
    };

    /**
     * Validate the settings data.
     *
     * If the validation fails then display a message to the user.
     *
     * @return {Boolean} Returns the validation result.
     */
    SystemSettingsCurrentUserHelper.prototype.validate = function () {
        $('#current-user .is-invalid').removeClass('is-invalid');

        try {
            // Validate required fields.
            var missingRequired = false;
            $('#current-user .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(App.Lang.fields_are_required);
            }

            // Validate passwords (if provided).
            if ($('#password').val() !== $('#retype-password').val()) {
                $('#password, #retype-password').addClass('is-invalid');
                throw new Error(App.Lang.passwords_mismatch);
            }

            // Validate user email.
            if (!GeneralFunctions.validateEmail($('#email').val())) {
                $('#email').addClass('is-invalid');
                throw new Error(App.Lang.invalid_email);
            }

            if ($('#username').attr('already-exists') === 'true') {
                $('#username').addClass('is-invalid');
                throw new Error(App.Lang.username_already_exists);
            }

            return true;
        } catch (error) {
            Backend.displayNotification(error.message);
            return false;
        }
    };

    window.SystemSettingsCurrentUserHelper = SystemSettingsCurrentUserHelper;
})();
