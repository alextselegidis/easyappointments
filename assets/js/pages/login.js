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
 * Login page.
 *
 * This module implements the functionality of the login page.
 */
App.Pages.Login = (function () {
    const $loginForm = $('#login-form');
    const $username = $('#username');
    const $password = $('#password');
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
     * Login Button "Click"
     *
     * Make an ajax call to the server and check whether the user's credentials are right.
     *
     * If yes then redirect him to his desired page, otherwise display a message.
     */
    function onLoginFormSubmit(event) {
        event.preventDefault();

        const username = $username.val();
        const password = $password.val();

        if (!username || !password) {
            return;
        }

        if ($captchaText.length > 0) {
            $captchaText.removeClass('is-invalid');
            if ($captchaText.val() === '') {
                $captchaText.addClass('is-invalid');
                return;
            }
        }

        const captcha = $captchaText.length > 0 ? $captchaText.val() : null;

        const $alert = $('.alert');

        $alert.addClass('d-none');

        App.Http.Login.validate(username, password, captcha).done((response) => {
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
                window.location.href = vars('dest_url');
            } else {
                $alert.text(lang('login_failed'));
                $alert.removeClass('d-none alert-danger alert-success').addClass('alert-danger');
                refreshCaptcha();
            }
        });
    }

    $loginForm.on('submit', onLoginFormSubmit);

    $captchaTitle.on('click', 'button', refreshCaptcha);

    return {};
})();
