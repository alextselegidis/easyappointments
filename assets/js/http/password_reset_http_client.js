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
 * Password Reset HTTP client.
 *
 * This module implements the password reset related HTTP requests.
 */
App.Http.PasswordReset = (function () {
    /**
     * Complete the password reset.
     *
     * @param {String} token
     * @param {String} password
     * @param {String} passwordConfirm
     *
     * @return {Object}
     */
    function complete(token, password, passwordConfirm) {
        const url = App.Utils.Url.siteUrl('recovery/complete');

        const data = {
            csrf_token: vars('csrf_token'),
            token,
            password,
            password_confirm: passwordConfirm,
        };

        return $.post(url, data);
    }

    return {
        complete,
    };
})();
