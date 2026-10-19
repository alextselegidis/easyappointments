/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.6.0
 * ---------------------------------------------------------------------------- */

/**
 * CalDAV Settings HTTP client.
 *
 * This module implements the CalDAV settings related HTTP requests.
 */
App.Http.CaldavSettings = (function () {
    /**
     * Save CalDAV settings.
     *
     * @param {Array} caldavSettings
     *
     * @return {Object}
     */
    function save(caldavSettings) {
        const url = App.Utils.Url.siteUrl('caldav_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            caldav_settings: caldavSettings,
        };

        return $.post(url, data);
    }

    return {
        save,
    };
})();
