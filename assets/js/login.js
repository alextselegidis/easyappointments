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
                if (response === GlobalVariables.AJAX_SUCCESS) {
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
