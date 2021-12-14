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
    const MIN_PASSWORD_LENGTH = 7;
    const $install = $('#install');
    const $alert = $('.alert');
    const $loading = $('#loading');
    const $firstName = $('#first-name');
    const $lastName = $('#last-name');
    const $email = $('#email');
    const $phoneNumber = $('#phone-number');
    const $username = $('#username');
    const $password = $('#password');
    const $retypePassword = $('#retype-password');
    const $companyName = $('#company-name');
    const $companyEmail = $('#company-email');
    const $companyLink = $('#company-link');

    $(document).ajaxStart(function () {
        $loading.removeClass('d-none');
    });

    $(document).ajaxStop(function () {
        $loading.addClass('d-none');
    });

    /**
     * Event: Install Easy!Appointments Button "Click"
     */
    $install.on('click', function () {
        if (!validate()) {
            return;
        }

        const url = GlobalVariables.baseUrl + '/index.php/installation/perform';

        const data = {
            csrf_token: GlobalVariables.csrfToken,
            admin: getAdminData(),
            company: getCompanyData()
        };

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json'
        }).done(() => {
            $alert
                .text('Easy!Appointments has been successfully installed!')
                .addClass('alert-success')
                .prop('hidden', false);

            setTimeout(function () {
                window.location.href = GlobalVariables.baseUrl + '/index.php/calendar';
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
            const $fields = $('input');

            $alert.removeClass('alert-danger').prop('hidden', true);

            $fields.removeClass('is-invalid');

            // Check for empty fields.
            let missingRequired = false;

            $fields.each((index, field) => {
                if (!$(field).val()) {
                    $(field).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error('All the page fields are required.');
            }

            // Validate Passwords
            if ($password.val() !== $retypePassword.val()) {
                $password.addClass('is-invalid');
                $retypePassword.addClass('is-invalid');
                throw new Error('Passwords do not match!');
            }

            if ($password.val().length < MIN_PASSWORD_LENGTH) {
                $password.addClass('is-invalid');
                $retypePassword.addClass('is-invalid');
                throw new Error(`The password must be at least ${MIN_PASSWORD_LENGTH} characters long.`);
            }

            // Validate Email
            if (!GeneralFunctions.validateEmail($email.val())) {
                $email.addClass('is-invalid');
                throw new Error('The email address is invalid!');
            }

            if (!GeneralFunctions.validateEmail($companyEmail.val())) {
                $companyEmail.addClass('is-invalid');
                throw new Error('The email address is invalid!');
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
            phone_number: $phoneNumber.val(),
            username: $username.val(),
            password: $password.val()
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
            company_link: $companyLink.val()
        };
    }

    // Validate the base URL setting (must not contain any trailing slash).
    if (GlobalVariables.baseUrl.slice(-1) === '/') {
        GeneralFunctions.displayMessageBox(
            'Invalid Configuration Detected',
            'Please remove any trailing slashes from your "BASE_URL" setting of the root "config.php" file and try again.'
        );
        $install.prop('disabled', true).fadeTo('0.4');
    }
});
