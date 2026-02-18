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
    const $captchaText = $('.captcha-text');
    const $captchaTitle = $('.captcha-title');
    const $captchaHint = $('#captcha-hint');
    const $altchaPayload = $('#altcha-payload');
    const $altchaHint = $('#altcha-hint');

    /**
     * Refresh the captcha image.
     */
    function refreshCaptcha() {
        $('.captcha-image').attr('src', App.Utils.Url.siteUrl('captcha?' + Date.now()));
    }

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

        if ($captchaText.length > 0) {
            $captchaText.removeClass('is-invalid');
            if ($captchaText.val() === '') {
                $captchaText.addClass('is-invalid');
                return;
            }
        }
        
        if ($altchaPayload.length > 0 && $altchaPayload.val() === '') {
            $altchaHint.text(lang('altcha_verification_failed')).fadeTo(400, 1);
            
            setTimeout(() => {
                $altchaHint.fadeTo(400, 0);
            }, 3000);
            
            return;
        }

        const captcha = $captchaText.length > 0 ? $captchaText.val() : null;
        const altchaPayloadValue = $altchaPayload.length > 0 ? $altchaPayload.val() : null;

        $resetPassword.prop('disabled', true);

        App.Http.PasswordReset.complete(token, password, passwordConfirm, captcha, altchaPayloadValue)
            .done((response) => {
                $alert.removeClass('d-none alert-danger');

                if (response.captcha_verification === false) {
                    $captchaHint.text(lang('captcha_is_wrong')).fadeTo(400, 1);

                    setTimeout(() => {
                        $captchaHint.fadeTo(400, 0);
                    }, 3000);

                    refreshCaptcha();

                    $captchaText.addClass('is-invalid');

                    $alert.addClass('d-none');

                    return;
                }
                
                if (response.altcha_verification === false) {
                    $altchaHint.text(lang('altcha_verification_failed')).fadeTo(400, 1);
                    
                    setTimeout(() => {
                        $altchaHint.fadeTo(400, 0);
                    }, 3000);
                    
                    if (App.Utils.Altcha) {
                        App.Utils.Altcha.reset('altcha-widget');
                    }
                    
                    $alert.addClass('d-none');
                    
                    return;
                }

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
                    refreshCaptcha();
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

                refreshCaptcha();
            })
            .always(() => {
                $resetPassword.prop('disabled', false);
            });
    }
    
    /**
     * Initialize ALTCHA widget if present.
     */
    function initializeAltcha() {
        if ($('#altcha-widget').length && App.Utils.Altcha) {
            App.Utils.Altcha.initialize('altcha-widget');
        }
    }

    // Only attach event listener if the form exists (token is valid)
    if ($form.length) {
        $form.on('submit', onFormSubmit);
    }

    $captchaTitle.on('click', 'button', refreshCaptcha);
    
    // Initialize ALTCHA
    initializeAltcha();

    return {};
})();
