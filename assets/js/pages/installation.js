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
 * Installation page.
 *
 * This module implements the functionality of the installation page.
 */
App.Pages.Installation = (function () {
    const MIN_PASSWORD_LENGTH = 8;
    const USERNAME_PATTERN = /^[a-zA-Z0-9_@.\-]{3,50}$/;
    const URL_PATTERN = /^https?:\/\/[^\s/$.?#].[^\s]*$/i;
    const $install = $('#install');
    const $alert = $('.alert');
    const $loading = $('#loading');
    const $firstName = $('#first-name');
    const $lastName = $('#last-name');
    const $email = $('#email');
    const $username = $('#username');
    const $password = $('#password');
    const $passwordConfirm = $('#password-confirm');
    const $language = $('#language');
    const $companyName = $('#company-name');
    const $companyEmail = $('#company-email');
    const $companyLink = $('#company-link');

    $(document).ajaxStart(() => {
        $loading.removeClass('d-none');
    });

    $(document).ajaxStop(() => {
        $loading.addClass('d-none');
    });

    /**
     * Event: Install Easy!Appointments Button "Click"
     */
    $install.on('click', () => {
        if (!validate()) {
            return;
        }

        const url = App.Utils.Url.siteUrl('installation/perform');

        const data = {
            csrf_token: vars('csrf_token'),
            admin: getAdminData(),
            company: getCompanyData(),
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
        }).done(() => {
            $alert
                .text('Easy!Appointments has been successfully installed!')
                .addClass('alert-success')
                .prop('hidden', false);

            setTimeout(() => {
                window.location.href = App.Utils.Url.siteUrl('calendar');
            }, 1000);
        });
    });

    /**
     * Validates the user input.
     *
     * Use this before executing the installation procedure.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        try {
            const $fields = $('input, select');

            $alert.removeClass('alert-danger').prop('hidden', true);

            $fields.removeClass('is-invalid');

            // Check for empty fields.
            let missingRequired = false;

            $fields.filter('.required').each((index, field) => {
                const $field = $(field);

                if (!$field.val() || ($field.is('select') && !$field.val().length)) {
                    $field.addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(lang('fields_are_required'));
            }

            // Check max length (defensive — also enforced by the maxlength attribute).
            let exceedsMaxLength = false;

            $fields.filter('input[maxlength]').each((index, field) => {
                const $field = $(field);
                const maxLength = parseInt($field.attr('maxlength'), 10);

                if (maxLength > 0 && $field.val().length > maxLength) {
                    $field.addClass('is-invalid');
                    exceedsMaxLength = true;
                }
            });

            if (exceedsMaxLength) {
                throw new Error('One or more fields exceed the maximum allowed length.');
            }

            // Validate name fields (do not allow HTML tags, mirror server-side strip_tags + trim).
            [$firstName, $lastName, $companyName].forEach(($field) => {
                if ($field.val().trim() === '') {
                    $field.addClass('is-invalid');
                    throw new Error(lang('fields_are_required'));
                }

                if (/<[^>]*>/.test($field.val())) {
                    $field.addClass('is-invalid');
                    throw new Error('HTML tags are not allowed in name fields.');
                }
            });

            // Validate username (must match server-side regex: 3-50 alphanumeric, _, @, ., -).
            if (!USERNAME_PATTERN.test($username.val())) {
                $username.addClass('is-invalid');
                throw new Error(
                    'Invalid username. Use 3-50 characters: letters, numbers, underscores, dashes, @ or dots.',
                );
            }

            // Validate Passwords
            if ($password.val() !== $passwordConfirm.val()) {
                $password.addClass('is-invalid');
                $passwordConfirm.addClass('is-invalid');
                throw new Error(lang('passwords_mismatch'));
            }

            if ($password.val().length < MIN_PASSWORD_LENGTH) {
                $password.addClass('is-invalid');
                $passwordConfirm.addClass('is-invalid');
                throw new Error(lang('password_length_notice').replace('$number', MIN_PASSWORD_LENGTH));
            }

            // Validate Email
            if (!App.Utils.Validation.email($email.val())) {
                $email.addClass('is-invalid');
                throw new Error(lang('invalid_email'));
            }

            if (!App.Utils.Validation.email($companyEmail.val())) {
                $companyEmail.addClass('is-invalid');
                throw new Error(lang('invalid_email'));
            }

            // Validate Company Link (must be a valid http(s) URL, mirroring FILTER_VALIDATE_URL on the server).
            if (!URL_PATTERN.test($companyLink.val().trim())) {
                $companyLink.addClass('is-invalid');
                throw new Error('Invalid company link. Please provide a valid URL (e.g. https://example.org).');
            }

            return true;
        } catch (error) {
            $alert.addClass('alert-danger').text(error.message).prop('hidden', false);

            return false;
        }
    }

    /**
     * Get the admin data as an object.
     *
     * @return {Object}
     */
    function getAdminData() {
        return {
            first_name: $firstName.val(),
            last_name: $lastName.val(),
            email: $email.val(),
            username: $username.val(),
            password: $password.val(),
            language: $language.val(),
        };
    }

    /**
     * Get the company data as an object.
     *
     * @return {Object}
     */
    function getCompanyData() {
        return {
            company_name: $companyName.val(),
            company_email: $companyEmail.val(),
            company_link: $companyLink.val(),
        };
    }

    // Validate the base URL setting (must not contain any trailing slash).
    if (vars('base_url').slice(-1) === '/') {
        App.Utils.Message.show(
            'Invalid Configuration Detected',
            'Please remove any trailing slashes from your "BASE_URL" setting of the root "config.php" file and try again.',
        );
        $install.prop('disabled', true).fadeTo('0.4');
    }

    return {};
})();
