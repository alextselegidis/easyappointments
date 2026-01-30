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
 * Login HTTP client.
 *
 * This module implements the account login related HTTP requests.
 */
App.Http.Login = (function () {
    /**
     * Perform an account recovery.
     *
     * @param {String} username
     * @param {String} password
     *
     * @return {Object}
     */
    function validate(username, password) {
        const url = App.Utils.Url.siteUrl('login/validate');

        const data = {
            csrf_token: vars('csrf_token'),
            username,
            password,
        };

        return $.post(url, data);
    }

    return {
        validate,
    };
})();
