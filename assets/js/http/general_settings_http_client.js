/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

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
            csrf_token: App.Vars.csrf_token,
            general_settings: generalSettings
        };

        return $.post(url, data);
    }

    return {
        save
    };
})();
