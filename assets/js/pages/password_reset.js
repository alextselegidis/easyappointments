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
 * Password Reset page.
 *
 * This module implements the functionality of the password reset page.
 */
App.Pages.PasswordReset = (function () {
    const $form = $('#password-reset-form');
    const $token = $('#token');
    const $password = $('#password');
    const $passwordConfirm = $('#password-confirm');
    const $resetPassword = $('#reset-password');
    const $alert = $('.alert');

    /**
     * Event: Form "Submit"
     *
     * Validate the password fields and submit the reset request.
     */
    function onFormSubmit(event) {
        event.preventDefault();

        $alert.addClass('d-none');

        const token = $token.val();
        const password = $password.val();
        const passwordConfirm = $passwordConfirm.val();

        // Validate password
        if (!password) {
            $alert.removeClass('d-none alert-success').addClass('alert-danger');
            $alert.text(lang('no_password_provided'));

            return;
        }

        if (password !== passwordConfirm) {
            $alert.removeClass('d-none alert-success').addClass('alert-danger');
            $alert.text(lang('passwords_mismatch'));

            return;
        }

        $resetPassword.prop('disabled', true);

        App.Http.PasswordReset.complete(token, password, passwordConfirm)
            .done((response) => {
                $alert.removeClass('d-none alert-danger');

                if (response.success) {
                    $alert.addClass('alert-success');
                    $alert.text(lang('password_reset_success'));
                    // Hide the form after successful reset
                    $form.hide();
                    // Redirect to login page after a short delay
                    setTimeout(() => {
                        window.location.href = App.Utils.Url.siteUrl('login');
                    }, 2000);
                } else {
                    $alert.addClass('alert-danger');
                    $alert.text(lang('password_reset_failed'));
                }
            })
            .fail((jqXHR) => {
                $alert.removeClass('d-none alert-success').addClass('alert-danger');

                const response = jqXHR.responseJSON;

                if (response && response.message) {
                    $alert.text(response.message);
                } else {
                    $alert.text(lang('password_reset_failed'));
                }
            })
            .always(() => {
                $resetPassword.prop('disabled', false);
            });
    }

    // Only attach event listener if the form exists (token is valid)
    if ($form.length) {
        $form.on('submit', onFormSubmit);
    }

    return {};
})();
