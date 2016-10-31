/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

$(document).ready(function() {
    'use strict';

    var MIN_PASSWORD_LENGTH = 7;
    var AJAX_SUCCESS = 'SUCCESS';
    var AJAX_FAILURE = 'FAILURE';

    $(document).ajaxStart(function() {
        $('#loading').removeClass('hidden');
    });

    $(document).ajaxStop(function() {
        $('#loading').addClass('hidden');
    });

    /**
     * Event: Install Easy!Appointments Button "Click"
     */
    $('#install').click(function() {
        if (!validate()) {
            return;
        }

        var postUrl = GlobalVariables.baseUrl + '/index.php/installation/ajax_install';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            admin: JSON.stringify(getAdminData()),
            company: JSON.stringify(getCompanyData())
        };

        $.ajax({
            url: postUrl,
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (!GeneralFunctions.handleAjaxExceptions(response)) {
                    return;
                }

                $('.alert').text('Easy!Appointments has been successfully installed!');
                $('.alert').addClass('alert-success');
                $('.alert').show();
                setTimeout(function() {
                    window.location.href = GlobalVariables.baseUrl + '/index.php/backend';
                }, 1000);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Treat the error the same way as php exceptions.
                var exc = {
                    exceptions: [
                        JSON.stringify({
                            message: 'The installation could not be completed due to an ' +
                                'unexpected issue. Please check the browser\'s console for ' +
                                'more information.'
                        })
                    ]
                };
                GeneralFunctions.handleAjaxExceptions(exc);
                console.log(exc.exceptions[0].message, jqXHR, textStatus, errorThrown);
            }
        });
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
            $('.alert').hide();
            $('input').css('border', '');

            // Check for empty fields.
            var missingRequired = false;
            $('input').each(function() {
                if ($(this).val() == '') {
                    $(this).css('border', '2px solid red');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw 'All the page fields are required.';
            }

            // Validate Passwords
            if ($('#password').val() != $('#retype-password').val()) {
                $('#password').css('border', '2px solid red');
                $('#retype-password').css('border', '2px solid red');
                throw 'Passwords do not match!';
            }

            if ($('#password').val().length < MIN_PASSWORD_LENGTH) {
                $('#password').css('border', '2px solid red');
                $('#retype-password').css('border', '2px solid red');
                throw 'The password must be at least ' + MIN_PASSWORD_LENGTH + ' characters long.';
            }

            // Validate Email
            if (!GeneralFunctions.validateEmail($('#email').val())) {
                $('#email').css('border', '2px solid red');
                throw 'The email address is invalid!';
            }

            if (!GeneralFunctions.validateEmail($('#company-email').val())) {
                $('#company-email').css('border', '2px solid red');
                throw 'The email address is invalid!';
            }

            return true;
        } catch (exc) {
            $('.alert').text(exc);
            $('.alert').show();
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
});
