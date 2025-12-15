/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Account page.
 *
 * This module implements the functionality of the account page.
 */
App.Pages.Account = (function () {
    const $userId = $('#user-id');
    const $firstName = $('#first-name');
    const $lastName = $('#last-name');
    const $email = $('#email');
    const $mobileNumber = $('#mobile-number');
    const $phoneNumber = $('#phone-number');
    const $address = $('#address');
    const $city = $('#city');
    const $state = $('#state');
    const $zipCode = $('#zip-code');
    const $notes = $('#notes');
    const $language = $('#language');
    const $timezone = $('#timezone');
    const $username = $('#username');
    const $password = $('#password');
    const $retypePassword = $('#retype-password');
    const $calendarView = $('#calendar-view');
    const notifications = $('#notifications');
    const $saveSettings = $('#save-settings');
    const $footerUserDisplayName = $('#footer-user-display-name');
    const $generateToken = $('#generate-token');
    const $hideToken = $('#hide-token');
    const $apiToken = $('#api-token');

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#account .is-invalid').removeClass('is-invalid');

            // Validate required fields.

            let missingRequiredFields = false;

            $('#account .required').each((index, requiredField) => {
                const $requiredField = $(requiredField);

                if (!$requiredField.val()) {
                    $requiredField.addClass('is-invalid');
                    missingRequiredFields = true;
                }
            });

            if (missingRequiredFields) {
                throw new Error(lang('fields_are_required'));
            }

            // Validate passwords (if values provided).

            if ($password.val() && $password.val() !== $retypePassword.val()) {
                $password.addClass('is-invalid');
                $retypePassword.addClass('is-invalid');
                throw new Error(lang('passwords_mismatch'));
            }

            // Validate user email.

            const emailValue = $email.val();

            if (!App.Utils.Validation.email(emailValue)) {
                $email.addClass('is-invalid');
                throw new Error(lang('invalid_email'));
            }

            if ($username.hasClass('is-invalid')) {
                throw new Error(lang('username_already_exists'));
            }

            return false;
        } catch (error) {
            App.Layouts.Backend.displayNotification(error.message);
            return true;
        }
    }

    /**
     * Apply the account values to the form.
     *
     * @param {Object} account
     */
    function deserialize(account) {
        $userId.val(account.id);
        $firstName.val(account.first_name);
        $lastName.val(account.last_name);
        $email.val(account.email);
        $mobileNumber.val(account.mobile_number);
        $phoneNumber.val(account.phone_number);
        $address.val(account.address);
        $city.val(account.city);
        $state.val(account.state);
        $zipCode.val(account.zip_code);
        $notes.val(account.notes);
        $language.val(account.language);
        $timezone.val(account.timezone);
        $username.val(account.settings.username);
        $password.val('');
        $retypePassword.val('');
        $calendarView.val(account.settings.calendar_view);
        notifications.prop('checked', Boolean(Number(account.settings.notifications)));
        $apiToken.val(account.settings.bearertoken);
    }

    /**
     * Get the account information from the form.
     *
     * @return {Object}
     */
    function serialize() {
        return {
            id: $userId.val(),
            first_name: $firstName.val(),
            last_name: $lastName.val(),
            email: $email.val(),
            mobile_number: $mobileNumber.val(),
            phone_number: $phoneNumber.val(),
            address: $address.val(),
            city: $city.val(),
            state: $state.val(),
            zip_code: $zipCode.val(),
            notes: $notes.val(),
            language: $language.val(),
            timezone: $timezone.val(),
            settings: {
                username: $username.val(),
                password: $password.val() || undefined,
                calendar_view: $calendarView.val(),
                notifications: Number(notifications.prop('checked')),
                bearertoken: $apiToken.val(),
            },
        };
    }

    /**
     * Save the account information.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            App.Layouts.Backend.displayNotification(lang('user_settings_are_invalid'));

            return;
        }

        const account = serialize();

        App.Http.Account.save(account).done(() => {
            App.Layouts.Backend.displayNotification(lang('settings_saved'));

            $footerUserDisplayName.text('Hello, ' + $firstName.val() + ' ' + $lastName.val() + '!');
        });
    }

    /**
     * Make sure the username is unique.
     */
    function onUsernameChange() {
        const username = $username.val();

        App.Http.Account.validateUsername(vars('user_id'), username).done((response) => {
            const isValid = response.is_valid;
            $username.toggleClass('is-invalid', !isValid);
            if (!isValid) {
                App.Layouts.Backend.displayNotification(lang('username_already_exists'));
            }
        });
    }

    /**
     * Initialize the page.
     */
    function initialize() {
        /*
        const $generateToken = $('#generate-token');
    const $hideToken = $('#hide-token');
    const $apiToken = $('#api-token');
        */
        
        $generateToken.on('click', () => {
            App.Http.Account.generateToken().done((response) => {
                $apiToken.val(response);
            });
        });

        $('#hide-token').on('click', () => {
            $apiToken.attr('type', $apiToken.attr('type') === 'text'?'password':'text');
        });

        $('#clear-token').on('click', () => {
            $apiToken.val(null);
        });

        $('#copy-token').on('click', () => {
            const token = $apiToken.val();
            navigator.clipboard.writeText(token).then(() => {
                App.Layouts.Backend.displayNotification(lang('token_copied'));
            });
        });

        const account = vars('account');

        deserialize(account);

        $saveSettings.on('click', onSaveSettingsClick);

        $username.on('change', onUsernameChange);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
