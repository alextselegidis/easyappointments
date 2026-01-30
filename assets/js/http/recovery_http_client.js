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
     *
     * @return {Object}
     */
    function perform(username, email) {
        const url = App.Utils.Url.siteUrl('recovery/perform');

        const data = {
            csrf_token: vars('csrf_token'),
            username,
            email,
        };

        return $.post(url, data);
    }

    return {
        perform,
    };
})();
