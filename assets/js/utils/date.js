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

window.App.Utils.Date = (function () {
    /**
     * Format a YYYY-MM-DD HH:mm:ss date string.
     *
     * @param {String} dateValue The date string to be formatted.
     * @param {String} [dateFormatType] The date format type value ("DMY", "MDY" or "YMD").
     * @param {String} [timeFormatType] The time format type value ("regular", "military").
     * @param {Boolean} [withHours] Whether to add hours to the returned string.

     * @return {String} Returns the formatted string.
     */
    function format(dateValue, dateFormatType = 'YMD', timeFormatType = 'regular', withHours = false) {
        const dateMoment = moment(dateValue);

        if (!dateMoment.isValid()) {
            throw new Error(`Invalid date value provided: ${dateValue}`);
        }

        let dateFormat;

        switch (dateFormatType) {
            case 'DMY':
                dateFormat = 'DD/MM/YYYY';
                break;

            case 'MDY':
                dateFormat = 'MM/DD/YYYY';
                break;

            case 'YMD':
                dateFormat = 'YYYY/MM/DD';
                break;

            default:
                throw new Error(`Invalid date format type provided: ${dateFormatType}`);
        }

        let timeFormat;

        switch (timeFormatType) {
            case 'regular':
                timeFormat = 'h:mm a';
                break;
            case 'military':
                timeFormat = 'HH:mm';
                break;
            default:
                throw new Error(`Invalid time format type provided: ${timeFormatType}`);
        }

        const format = withHours ? `${dateFormat} ${timeFormat}` : dateFormat;

        return dateMoment.format(format);
    }

    return {
        format
    };
})();
