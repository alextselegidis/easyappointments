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

(function () {
    const $loginForm = $('#login-form');

    /**
     * Login Button "Click"
     *
     * Make an ajax call to the server and check whether the user's credentials are right.
     * If yes then redirect him to his desired page, otherwise display a message.
     */
    function onLoginFormSubmit(event) {
        event.preventDefault();

        const url = App.Vars.baseUrl + '/index.php/login/validate';

        const data = {
            csrf_token: App.Vars.csrf_token,
            username: $('#username').val(),
            password: $('#password').val()
        };

        const $alert = $('.alert');

        $alert.addClass('d-none');

        $.post(url, data).done((response) => {
            if (response.success) {
                window.location.href = App.Vars.dest_url;
            } else {
                $alert.text(App.Lang.login_failed);
                $alert.removeClass('d-none alert-danger alert-success').addClass('alert-danger');
            }
        });
    }

    $loginForm.on('submit', onLoginFormSubmit);
})();
