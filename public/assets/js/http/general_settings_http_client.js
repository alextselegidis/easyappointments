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
 * General Settings HTTP client.
 *
 * This module implements the general settings related HTTP requests.
 */
App.Http.GeneralSettings = (function () {
    /**
     * Save general settings.
     *
     * @param {Object} generalSettings
     *
     * @return {Object}
     */
    function save(generalSettings) {
        const url = App.Utils.Url.siteUrl('general_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            general_settings: generalSettings,
        };

        return $.post(url, data);
    }

    return {
        save,
    };
})();
