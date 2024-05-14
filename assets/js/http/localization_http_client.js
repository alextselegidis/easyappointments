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
 * Localization HTTP client.
 *
 * This module implements the account related HTTP requests.
 */
App.Http.Localization = (function () {
    /**
     * Change language.
     *
     * @param {String} language
     */
    function changeLanguage(language) {
        const url = App.Utils.Url.siteUrl('localization/change_language');

        const data = {
            csrf_token: vars('csrf_token'),
            language,
        };

        return $.post(url, data);
    }

    return {
        changeLanguage,
    };
})();
