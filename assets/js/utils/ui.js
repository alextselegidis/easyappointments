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
     * Get the jQuery UI compatible date format.
     *
     * @return {String}
     */
    function getDateFormat() {
        switch (vars('date_format')) {
            case 'DMY':
                return 'dd/mm/yy';
            case 'MDY':
                return 'mm/dd/yy';
            case 'YMD':
                return 'yy/mm/dd';
            default:
                throw new Error('Invalid date format value.');
        }
    }


    /**
     * Initialize the date time picker component.
     *
     * This method is based on jQuery UI.
     *
     * @param {jQuery} $target
     * @param {Object} [params]
     */
    function initializeDatetimepicker($target, params = {}) {
        const dateFormat = getDateFormat();

        const firstWeekDay = vars('first_weekday');

        const firstWeekDayNumber = App.Utils.Date.getWeekdayId(firstWeekDay);

        const options = {
            dateFormat: dateFormat,
            timeFormat: vars('time_format') === 'regular' ? 'h:mm tt' : 'HH:mm',

            // Translation
            dayNames: [
                lang('sunday'),
                lang('monday'),
                lang('tuesday'),
                lang('wednesday'),
                lang('thursday'),
                lang('friday'),
                lang('saturday')
            ],
            dayNamesShort: [
                lang('sunday').substring(0, 3),
                lang('monday').substring(0, 3),
                lang('tuesday').substring(0, 3),
                lang('wednesday').substring(0, 3),
                lang('thursday').substring(0, 3),
                lang('friday').substring(0, 3),
                lang('saturday').substring(0, 3)
            ],
            dayNamesMin: [
                lang('sunday').substring(0, 2),
                lang('monday').substring(0, 2),
                lang('tuesday').substring(0, 2),
                lang('wednesday').substring(0, 2),
                lang('thursday').substring(0, 2),
                lang('friday').substring(0, 2),
                lang('saturday').substring(0, 2)
            ],
            monthNames: [
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
                lang('december')
            ],
            prevText: lang('previous'),
            nextText: lang('next'),
            currentText: lang('now'),
            closeText: lang('close'),
            timeOnlyTitle: lang('select_time'),
            timeText: lang('time'),
            hourText: lang('hour'),
            minuteText: lang('minutes'),
            firstDay: firstWeekDayNumber,

            ...params
        }

        $target.datetimepicker(options);
    }

    /**
     * Initialize the date picker component.
     *
     * This method is based on jQuery UI.
     *
     * @param {jQuery} $target
     * @param {Object} [params]
     */
    function initializeDatepicker($target, params = {}) {
        const dateFormat = getDateFormat();

        $target.datepicker({
            dateFormat: dateFormat,
            firstDay: App.Utils.Date.getWeekdayId(vars('first_weekday')),
            minDate: 0,
            defaultDate: moment().toDate(),
            dayNames: [
                lang('sunday'),
                lang('monday'),
                lang('tuesday'),
                lang('wednesday'),
                lang('thursday'),
                lang('friday'),
                lang('saturday')
            ],
            dayNamesShort: [
                lang('sunday').substring(0, 3),
                lang('monday').substring(0, 3),
                lang('tuesday').substring(0, 3),
                lang('wednesday').substring(0, 3),
                lang('thursday').substring(0, 3),
                lang('friday').substring(0, 3),
                lang('saturday').substring(0, 3)
            ],
            dayNamesMin: [
                lang('sunday').substring(0, 2),
                lang('monday').substring(0, 2),
                lang('tuesday').substring(0, 2),
                lang('wednesday').substring(0, 2),
                lang('thursday').substring(0, 2),
                lang('friday').substring(0, 2),
                lang('saturday').substring(0, 2)
            ],
            monthNames: [
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
                lang('december')
            ],
            prevText: lang('previous'),
            nextText: lang('next'),
            currentText: lang('now'),
            closeText: lang('close'),

            ...params
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
    function initializeTimepicker($target, params = {}) {
        $target.timepicker({
            timeFormat: vars('time_format') === 'regular' ? 'h:mm tt' : 'HH:mm',
            currentText: lang('now'),
            closeText: lang('close'),
            timeOnlyTitle: lang('select_time'),
            timeText: lang('time'),
            hourText: lang('hour'),
            minuteText: lang('minutes'),

            ...params
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

    return {
        initializeDatetimepicker,
        initializeDatepicker,
        initializeTimepicker,
        initializeDropdown,
        initializeTextEditor,
    };
})();
