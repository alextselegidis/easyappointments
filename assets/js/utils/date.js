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
 * Date utility.
 *
 * This module implements the functionality of dates.
 */
window.App.Utils.Date = (function () {
    /**
     * Format a YYYY-MM-DD HH:mm:ss date string.
     *
     * @param {String|Date} dateValue The date string to be formatted.
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

    /**
     * Get the Id of a Weekday using the US week format and day names (Sunday=0) as used in the JS code of the
     * application, case-insensitive, short and long names supported.
     *
     * @param {String} weekDayName The weekday name among Sunday, Monday, Tuesday, Wednesday, Thursday, Friday,
     * Saturday.

     * @return {Number} Returns the ID of the weekday.
     */
    function getWeekdayId(weekDayName) {
        let result;

        switch (weekDayName.toLowerCase()) {
            case 'sunday':
            case 'sun':
                result = 0;
                break;

            case 'monday':
            case 'mon':
                result = 1;
                break;

            case 'tuesday':
            case 'tue':
                result = 2;
                break;

            case 'wednesday':
            case 'wed':
                result = 3;
                break;

            case 'thursday':
            case 'thu':
                result = 4;
                break;

            case 'friday':
            case 'fri':
                result = 5;
                break;

            case 'saturday':
            case 'sat':
                result = 6;
                break;

            default:
                throw new Error(`Invalid weekday name provided: ${weekDayName}`);
        }

        return result;
    }

    /**
     * Sort a dictionary where keys are weekdays
     *
     * @param {Object} weekDictionary A dictionary with weekdays as keys.
     * @param {Number} startDayId Id of the first day to start sorting (From 0 for sunday to 6 for saturday).

     * @return {Object} Returns a sorted dictionary
     */
    function sortWeekDictionary(weekDictionary, startDayId) {
        const sortedWeekDictionary = {};

        for (let i = startDayId; i < startDayId + 7; i++) {
            const weekdayName = getWeekdayName(i % 7);
            sortedWeekDictionary[weekdayName] = weekDictionary[weekdayName];
        }

        return sortedWeekDictionary;
    }

    /**
     * Get the name in lowercase of a Weekday using its Id.
     *
     * @param {Number} weekDayId The Id (From 0 for sunday to 6 for saturday).

     * @return {String} Returns the name of the weekday.
     */
    function getWeekdayName(weekDayId) {
        let result;

        switch (weekDayId) {
            case 0:
                result = 'sunday';
                break;

            case 1:
                result = 'monday';
                break;

            case 2:
                result = 'tuesday';
                break;

            case 3:
                result = 'wednesday';
                break;

            case 4:
                result = 'thursday';
                break;

            case 5:
                result = 'friday';
                break;

            case 6:
                result = 'saturday';
                break;

            default:
                throw new Error(`Invalid weekday Id provided: ${weekDayId}`);
        }

        return result;
    }

    return {
        format,
        getWeekdayId,
        sortWeekDictionary,
        getWeekdayName,
    };
})();
