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

$(function () {
    'use strict';

    var MIN_PASSWORD_LENGTH = 7;

    var $install = $('#install');
    var $alert = $('.alert');

    $(document).ajaxStart(function () {
        $('#loading').removeClass('d-none');
    });

    $(document).ajaxStop(function () {
        $('#loading').addClass('d-none');
    });

    /**
     * Event: Install Easy!Appointments Button "Click"
     */
    $install.on('click', function () {
        if (!validate()) {
            return;
        }

        var url = GlobalVariables.baseUrl + '/index.php/installation/ajax_install';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            admin: getAdminData(),
            company: getCompanyData()
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json'
        })
            .done(function (response) {
                $alert
                    .text('Easy!Appointments has been successfully installed!')
                    .addClass('alert-success')
                    .removeClass('hidden');

                setTimeout(function () {
                    window.location.href = GlobalVariables.baseUrl + '/index.php/backend';
                }, 1000);
            });
    });

    /**
     * Validates the user input.
     *
     *   Use this before executing the installation procedure.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        try {
            $alert
                .removeClass('alert-danger')
                .addClass('hidden');
            $('input').closest('.form-group').removeClass('has-error');

            // Check for empty fields.
            var missingRequired = false;
            $('input').each(function (index, field) {
                if (!$(field).val()) {
                    $(field).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error('All the page fields are required.');
            }

            // Validate Passwords
            if ($('#password').val() !== $('#retype-password').val()) {
                $('#password').closest('.form-group').addClass('has-error');
                $('#retype-password').closest('.form-group').addClass('has-error');
                throw new Error('Passwords do not match!');
            }

            if ($('#password').val().length < MIN_PASSWORD_LENGTH) {
                $('#password').closest('.form-group').addClass('has-error');
                $('#retype-password').closest('.form-group').addClass('has-error');
                throw new Error('The password must be at least ' + MIN_PASSWORD_LENGTH + ' characters long.');
            }

            // Validate Email
            if (!GeneralFunctions.validateEmail($('#email').val())) {
                $('#email').closest('.form-group').addClass('has-error');
                throw new Error('The email address is invalid!');
            }

            if (!GeneralFunctions.validateEmail($('#company-email').val())) {
                $('#company-email').closest('.form-group').addClass('has-error');
                throw new Error('The email address is invalid!');
            }

            return true;
        } catch (error) {
            $alert
                .addClass('alert-danger')
                .text(error.message)
                .removeClass('hidden');

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
            first_name: $('#first-name').val(),
            last_name: $('#last-name').val(),
            email: $('#email').val(),
            phone_number: $('#phone-number').val(),
            username: $('#username').val(),
            password: $('#password').val()
        };
    }

    /**
     * Get the company data as an object.
     *
     * @return {Object}
     */
    function getCompanyData() {
        return {
            company_name: $('#company-name').val(),
            company_email: $('#company-email').val(),
            company_link: $('#company-link').val()
        };
    }

    // Validate the base URL setting (must not contain any trailing slash).
    if (GlobalVariables.baseUrl.slice(-1) === '/') {
        GeneralFunctions.displayMessageBox('Misconfiguration Detected', 'Please remove any trailing '
            + 'slashes from your BASE_URL setting of the root config.php file and try again.');
        $install
            .prop('disabled', true)
            .fadeTo('0.4');
    }
});
