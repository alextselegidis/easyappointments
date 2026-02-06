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
     * Event: Form "Submit"
     *
     * Make an HTTP request to the server to request a password reset link.
     */
    function onFormSubmit(event) {
        event.preventDefault();

        const $alert = $('.alert');

        $alert.addClass('d-none');

        $getNewPassword.prop('disabled', true);

        const username = $username.val();
        const email = $email.val();

        App.Http.Recovery.perform(username, email)
            .done((response) => {
                $alert.removeClass('d-none alert-danger alert-success');

                if (response.success) {
                    $alert.addClass('alert-success');
                    $alert.text(lang('reset_link_sent_with_email'));
                } else {
                    $alert.addClass('alert-danger');
                    $alert.text(
                        'The operation failed! Please enter a valid username ' +
                            'and email address in order to receive a password reset link.',
                    );
                }
            })
            .always(() => {
                $getNewPassword.prop('disabled', false);
            });
    }

    $form.on('submit', onFormSubmit);

    return {};
})();
