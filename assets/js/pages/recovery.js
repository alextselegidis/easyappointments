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

    /**
     * Event: Login Button "Click"
     *
     * Make an HTTP request to the server and check whether the user's credentials are right. If yes then redirect the
     * user to the destination page, otherwise display an error message.
     */
    function onFormSubmit(event) {
        event.preventDefault();

        const $alert = $('.alert');

        $alert.addClass('d-none');

        $getNewPassword.prop('disabled', true);

        const username = $username.val();
        const email = $email.val();

        App.Http.Recovery.perform(username, email).done((response) => {
            $alert.removeClass('d-none alert-danger alert-success');

            $getNewPassword.prop('disabled', false);

            if (response.success) {
                $alert.addClass('alert-success');
                $alert.text(lang('new_password_sent_with_email'));
            } else {
                $alert.addClass('alert-danger');
                $alert.text(
                    'The operation failed! Please enter a valid username ' +
                        'and email address in order to get a new password.',
                );
            }
        });
    }

    $form.on('submit', onFormSubmit);

    return {};
})();
