/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

(function () {
    const $form = $('form');

    /**
     * Event: Login Button "Click"
     *
     * Make an HTTP request to the server and check whether the user's credentials are right. If yes then redirect the
     * user to the destination page, otherwise display an error message.
     */
    function onFormSubmit(event) {
        event.preventDefault();

        const url = GlobalVariables.baseUrl + '/index.php/recovery/perform';

        const data = {
            csrfToken: GlobalVariables.csrfToken,
            username: $('#username').val(),
            email: $('#email').val()
        };

        const $alert = $('.alert');

        $alert.addClass('d-none');

        $('#get-new-password').prop('disabled', true);

        $.post(url, data).done((response) => {
            $alert.removeClass('d-none alert-danger alert-success');

            $('#get-new-password').prop('disabled', false);

            if (response.success) {
                $alert.addClass('alert-success');
                $alert.text(App.Lang.new_password_sent_with_email);
            } else {
                $alert.addClass('alert-danger');
                $alert.text(
                    'The operation failed! Please enter a valid username ' +
                        'and email address in order to get a new password.'
                );
            }
        });
    }

    $form.on('submit', onFormSubmit);
})();
