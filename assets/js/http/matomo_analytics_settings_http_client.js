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
 * Matomo Analytics Settings HTTP client.
 *
 * This module implements the Matomo Analytics settings related HTTP requests.
 */
App.Http.MatomoAnalyticsSettings = (function () {
    /**
     * Save Matomo Analytics settings.
     *
     * @param {Object} matomoAnalyticsSettings
     *
     * @return {Object}
     */
    function save(matomoAnalyticsSettings) {
        const url = App.Utils.Url.siteUrl('matomo_analytics_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            matomo_analytics_settings: matomoAnalyticsSettings,
        };

        return $.post(url, data);
    }

    return {
        save,
    };
})();
