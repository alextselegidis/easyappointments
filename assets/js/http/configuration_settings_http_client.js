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
 * General Settings HTTP client.
 *
 * This module implements the configuration settings related HTTP requests.
 */
App.Http.ConfigurationSettings = (function () {
    /**
     * Save configuration settings.
     *
     * @param {Object} configSettings
     *
     * @return {Object}
     */
    function save(configSettings) {
        // Make sure the 'index' members are gone
        if (configSettings.constructor === Array) {
            configSettings.forEach(setting => {
                delete(setting['index']);
            });
        }

        const url = App.Utils.Url.siteUrl('configuration_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            config_settings: configSettings,
        };

        return $.post(url, data);
    }

    function remove(deletedSettings) {
        // Make sure the 'index' members are gone
        if (deletedSettings.constructor === Array) {
            deletedSettings.forEach(setting => {
                delete(setting['index']);
            });
        }

        const url = App.Utils.Url.siteUrl('configuration_settings/delete');

        const data = {
            csrf_token: vars('csrf_token'),
            config_settings: deletedSettings,
        };

        return $.post(url, data);
    }

    return {
        save,
        remove,
    };
})();
