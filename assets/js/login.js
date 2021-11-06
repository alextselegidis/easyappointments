/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

$(function () {
    'use strict';

    var $loginForm = $('#login-form');

    /**
     * Login Button "Click"
     *
     * Make an ajax call to the server and check whether the user's credentials are right.
     * If yes then redirect him to his desired page, otherwise display a message.
     */
    function onLoginFormSubmit(event) {
        event.preventDefault();

        var url = GlobalVariables.baseUrl + '/index.php/user/ajax_check_login';

        var data = {
            'csrfToken': GlobalVariables.csrfToken,
            'username': $('#username').val(),
            'password': $('#password').val()
        };

        var $alert = $('.alert');

        $alert.addClass('d-none');

        $.post(url, data)
            .done(function (response) {
                if (response.success) {
                    window.location.href = GlobalVariables.destUrl;
                } else {
                    $alert.text(EALang['login_failed']);
                    $alert
                        .removeClass('d-none alert-danger alert-success')
                        .addClass('alert-danger');
                }
            });
    }

    $loginForm.on('submit', onLoginFormSubmit);
});
