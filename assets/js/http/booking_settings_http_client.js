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

App.Http.BookingSettings = (function () {
    /**
     * Save booking settings.
     *
     * @param {Object} bookingSettings
     *
     * @return {Object}
     */
    function save(bookingSettings) {
        const url = App.Utils.Url.siteUrl('booking_settings/save');

        const data = {
            csrf_token: App.Vars.csrf_token,
            booking_settings: bookingSettings
        };

        return $.post(url, data);
    }

    return {
        save
    };
})();
