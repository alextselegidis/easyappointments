/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

$(function () {
    'use strict';

    var MIN_PASSWORD_LENGTH = 7;

    var $alert = $('.alert');

    $(document).ajaxStart(function () {
        $('#loading').removeClass('hidden');
    });

    $(document).ajaxStop(function () {
        $('#loading').addClass('hidden');
    });

    /**
     * Event: Install Easy!Appointments Button "Click"
     */
    $('#install').click(function () {
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
                if (!GeneralFunctions.handleAjaxExceptions(response)) {
                    return;
                }

                $alert
                    .text('Easy!Appointments has been successfully installed!')
                    .addClass('alert-success')
                    .show();

                setTimeout(function () {
                    window.location.href = GlobalVariables.baseUrl + '/index.php/backend';
                }, 1000);
            })
            .fail(GeneralFunctions.ajaxFailureHandler);
    });

    /**
     * Validates the user input.
     *
     *   Use this before executing the installation procedure.
     *
     * @returns {Boolean} Returns the validation result.
     */
    function validate() {
        try {
            $alert.hide();
            $('input').closest('.form-group').removeClass('has-error');

            // Check for empty fields.
            var missingRequired = false;
            $('input').each(function () {
                if ($(this).val() == '') {
                    $(this).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw 'All the page fields are required.';
            }

            // Validate Passwords
            if ($('#password').val() != $('#retype-password').val()) {
                $('#password').closest('.form-group').addClass('has-error');
                $('#retype-password').closest('.form-group').addClass('has-error');
                throw 'Passwords do not match!';
            }

            if ($('#password').val().length < MIN_PASSWORD_LENGTH) {
                $('#password').closest('.form-group').addClass('has-error');
                $('#retype-password').closest('.form-group').addClass('has-error');
                throw 'The password must be at least ' + MIN_PASSWORD_LENGTH + ' characters long.';
            }

            // Validate Email
            if (!GeneralFunctions.validateEmail($('#email').val())) {
                $('#email').closest('.form-group').addClass('has-error');
                throw 'The email address is invalid!';
            }

            if (!GeneralFunctions.validateEmail($('#company-email').val())) {
                $('#company-email').closest('.form-group').addClass('has-error');
                throw 'The email address is invalid!';
            }

            return true;
        } catch (error) {
            $alert
                .text(error)
                .show();

            return false;
        }
    }

    /**
     * Get the admin data as an object.
     *
     * @return {Object}
     */
    function getAdminData() {
        var admin = {
            first_name: $('#first-name').val(),
            last_name: $('#last-name').val(),
            email: $('#email').val(),
            phone_number: $('#phone-number').val(),
            username: $('#username').val(),
            password: $('#password').val()
        };

        return admin;
    }

    /**
     * Get the company data as an object.
     *
     * @return {Object}
     */
    function getCompanyData() {
        var company = {
            company_name: $('#company-name').val(),
            company_email: $('#company-email').val(),
            company_link: $('#company-link').val()
        };

        return company;
    }

    // Validate the base URL setting (must not contain any trailing slash).
    if (GlobalVariables.baseUrl.slice(-1) === '/') {
        GeneralFunctions.displayMessageBox('Misconfiguration Detected', 'Please remove any trailing slashes from your '
            + 'BASE_URL setting of the root config.php file and try again.');
        $('#install')
            .prop('disabled', true)
            .fadeTo('0.4');
    }
});
