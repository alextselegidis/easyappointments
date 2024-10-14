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
 * UI utility.
 *
 * This module provides commonly used UI functionality.
 */
window.App.Utils.UI = (function () {
    /**
     * Get the Flatpickr compatible date format.
     *
     * @return {String}
     */
    function getDateFormat() {
        switch (vars('date_format')) {
            case 'DMY':
                return 'd/m/Y';
            case 'MDY':
                return 'm/d/Y';
            case 'YMD':
                return 'Y/m/d';
            default:
                throw new Error('Invalid date format value.');
        }
    }

    /**
     * Get the Flatpickr compatible date format.
     *
     * @return {String}
     */
    function getTimeFormat() {
        switch (vars('time_format')) {
            case 'regular':
                return 'h:i K';
            case 'military':
                return 'H:i';
            default:
                throw new Error('Invalid date format value.');
        }
    }

    /**
     * Get the localization object.
     *
     * @return Object
     */
    function getFlatpickrLocale() {
        const firstWeekDay = vars('first_weekday');

        const firstWeekDayNumber = App.Utils.Date.getWeekdayId(firstWeekDay);

        return {
            weekdays: {
                shorthand: [
                    lang('sunday_short'),
                    lang('monday_short'),
                    lang('tuesday_short'),
                    lang('wednesday_short'),
                    lang('thursday_short'),
                    lang('friday_short'),
                    lang('saturday_short'),
                ],
                longhand: [
                    lang('sunday'),
                    lang('monday'),
                    lang('tuesday'),
                    lang('wednesday'),
                    lang('thursday'),
                    lang('friday'),
                    lang('saturday'),
                ],
            },
            months: {
                shorthand: [
                    lang('january_short'),
                    lang('february_short'),
                    lang('march_short'),
                    lang('april_short'),
                    lang('may_short'),
                    lang('june_short'),
                    lang('july_short'),
                    lang('august_short'),
                    lang('september_short'),
                    lang('october_short'),
                    lang('november_short'),
                    lang('december_short'),
                ],
                longhand: [
                    lang('january'),
                    lang('february'),
                    lang('march'),
                    lang('april'),
                    lang('may'),
                    lang('june'),
                    lang('july'),
                    lang('august'),
                    lang('september'),
                    lang('october'),
                    lang('november'),
                    lang('december'),
                ],
            },
            daysInMonth: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
            firstDayOfWeek: firstWeekDayNumber,
            ordinal: function (nth) {
                const s = nth % 100;

                if (s > 3 && s < 21) return 'th';
                switch (s % 10) {
                    case 1:
                        return 'st';
                    case 2:
                        return 'nd';
                    case 3:
                        return 'rd';
                    default:
                        return 'th';
                }
            },
            rangeSeparator: ` ${lang('to')} `,
            weekAbbreviation: lang('week_short'),
            scrollTitle: lang('scroll_to_increment'),
            toggleTitle: lang('click_to_toggle'),
            amPM: [lang('am'), lang('pm')],
            yearAriaLabel: lang('year'),
            monthAriaLabel: lang('month'),
            hourAriaLabel: lang('hour'),
            minuteAriaLabel: lang('minute'),
            time_24hr: false,
        };
    }

    /**
     * Initialize the date time picker component.
     *
     * This method is based on jQuery UI.
     *
     * @param {jQuery} $target
     * @param {Object} [params]
     */
    function initializeDateTimePicker($target, params = {}) {
        $target.flatpickr({
            enableTime: true,
            allowInput: true,
            static: true,
            dateFormat: `${getDateFormat()} ${getTimeFormat()}`,
            time_24hr: vars('time_format') === 'military',
            locale: getFlatpickrLocale(),
            ...params,
        });
    }

    /**
     * Initialize the date picker component.
     *
     * This method is based on jQuery UI.
     *
     * @param {jQuery} $target
     * @param {Object} [params]
     */
    function initializeDatePicker($target, params = {}) {
        $target.flatpickr({
            allowInput: true,
            dateFormat: getDateFormat(),
            locale: getFlatpickrLocale(),
            static: true,
            ...params,
        });
    }

    /**
     * Initialize the time picker component.
     *
     * This method is based on jQuery UI.
     *
     * @param {jQuery} $target
     * @param {Object} [params]
     */
    function initializeTimePicker($target, params = {}) {
        $target.flatpickr({
            noCalendar: true,
            enableTime: true,
            allowInput: true,
            dateFormat: getTimeFormat(),
            time_24hr: vars('time_format') === 'military',
            locale: getFlatpickrLocale(),
            static: true,
            ...params,
        });
    }

    /**
     * Initialize the select dropdown component.
     *
     * This method is based on Select2.
     *
     * @param {jQuery} $target
     * @param {Object} [params]
     */
    function initializeDropdown($target, params = {}) {
        $target.select2(params);
    }

    /**
     * Initialize the text editor component.
     *
     * This method is based on Trumbowyg.
     *
     * @param {jQuery} $target
     * @param {Object} [params]
     */
    function initializeTextEditor($target, params = {}) {
        $target.trumbowyg(params);
    }

    /**
     * Get Date, Date-Time or Time picker value.
     *
     * @param {jQuery} $target
     *
     * @return {Date}
     */
    function getDateTimePickerValue($target) {
        if (!$target?.length) {
            throw new Error('Empty $target argument provided.');
        }

        return $target[0]._flatpickr.selectedDates[0];
    }

    /**
     * Set Date, Date-Time or Time picker value.
     *
     * @param {jQuery} $target
     * @param {Date} value
     */
    function setDateTimePickerValue($target, value) {
        if (!$target?.length) {
            throw new Error('Empty $target argument provided.');
        }

        return $target[0]._flatpickr.setDate(value);
    }

    return {
        initializeDateTimePicker,
        initializeDatePicker,
        initializeTimePicker,
        initializeDropdown,
        initializeTextEditor,
        getDateTimePickerValue,
        setDateTimePickerValue,
    };
})();
