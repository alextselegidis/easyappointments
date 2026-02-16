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
 * Recovery page.
 *
 * This module implements the functionality of the recovery page.
 */
App.Pages.Recovery = (function () {
    const $form = $('form');
    const $username = $('#username');
    const $email = $('#email');
    const $getNewPassword = $('#get-new-password');
    const $captchaText = $('.captcha-text');
    const $captchaTitle = $('.captcha-title');
    const $captchaHint = $('#captcha-hint');

    /**
     * Refresh the captcha image.
     */
    function refreshCaptcha() {
        $('.captcha-image').attr('src', App.Utils.Url.siteUrl('captcha?' + Date.now()));
    }

    /**
     * Event: Form "Submit"
     *
     * Make an HTTP request to the server to request a password reset link.
     */
    function onFormSubmit(event) {
        event.preventDefault();

        const $alert = $('.alert');

        $alert.addClass('d-none');

        if ($captchaText.length > 0) {
            $captchaText.removeClass('is-invalid');
            if ($captchaText.val() === '') {
                $captchaText.addClass('is-invalid');
                return;
            }
        }

        $getNewPassword.prop('disabled', true);

        const username = $username.val();
        const email = $email.val();
        const captcha = $captchaText.length > 0 ? $captchaText.val() : null;

        App.Http.Recovery.perform(username, email, captcha)
            .done((response) => {
                $alert.removeClass('d-none alert-danger alert-success');

                if (response.captcha_verification === false) {
                    $captchaHint.text(lang('captcha_is_wrong')).fadeTo(400, 1);

                    setTimeout(() => {
                        $captchaHint.fadeTo(400, 0);
                    }, 3000);

                    refreshCaptcha();

                    $captchaText.addClass('is-invalid');

                    return;
                }

                if (response.success) {
                    $alert.addClass('alert-success');
                    $alert.text(lang('reset_link_sent_with_email'));
                } else {
                    $alert.addClass('alert-danger');
                    $alert.text(
                        'The operation failed! Please enter a valid username ' +
                            'and email address in order to receive a password reset link.',
                    );
                    refreshCaptcha();
                }
            })
            .always(() => {
                $getNewPassword.prop('disabled', false);
            });
    }

    $form.on('submit', onFormSubmit);

    $captchaTitle.on('click', 'button', refreshCaptcha);

    return {};
})();
