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
 * Recovery HTTP client.
 *
 * This module implements the account recovery related HTTP requests.
 */
App.Http.Recovery = (function () {
    /**
     * Perform an account recovery.
     *
     * @param {String} username
     * @param {String} email
     * @param {String} captcha
     *
     * @return {Object}
     */
    function perform(username, email, captcha) {
        const url = App.Utils.Url.siteUrl('recovery/perform');

        const data = {
            csrf_token: vars('csrf_token'),
            username,
            email,
        };

        if (captcha) {
            data.captcha = captcha;
        }

        return $.post(url, data);
    }

    return {
        perform,
    };
})();
