/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.4.0
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

        const $alert = $('.alert');

        $alert.addClass('d-none');

        App.Http.Login.validate(username, password).done((response) => {
            if (response.success) {
                window.location.href = vars('dest_url');
            } else {
                $alert.text(App.Lang.login_failed);
                $alert.removeClass('d-none alert-danger alert-success').addClass('alert-danger');
            }
        });
    }

    $loginForm.on('submit', onLoginFormSubmit);

    return {};
})();
