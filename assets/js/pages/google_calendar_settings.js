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
 * Google Calendar Settings page.
 *
 * This module implements the functionality of the Google Calendar Settings page.
 */
App.Pages.GoogleCalendarSettings = (function () {
    const $saveSettings = $('#save-settings');

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#google-calendar-settings .is-invalid').removeClass('is-invalid');

            return false;
        } catch (error) {
            App.Layouts.Backend.displayNotification(error.message);
            return true;
        }
    }

    /**
     * Apply the setting values to the form.
     *
     * @param {Array} googleCalendarSettings
     */
    function deserialize(googleCalendarSettings) {
        googleCalendarSettings.forEach((googleCalendarSetting) => {
            const $field = $('[data-field="' + googleCalendarSetting.name + '"]');

            $field.is(':checkbox')
                ? $field.prop('checked', Boolean(Number(googleCalendarSetting.value)))
                : $field.val(googleCalendarSetting.value);
        });
    }

    /**
     * Serialize the form values into an array.
     *
     * @return {Array}
     */
    function serialize() {
        const googleCalendarSettings = [];

        $('[data-field]').each((index, field) => {
            const $field = $(field);

            googleCalendarSettings.push({
                name: $field.data('field'),
                value: $field.is(':checkbox') ? Number($field.prop('checked')) : $field.val(),
            });
        });

        return googleCalendarSettings;
    }

    /**
     * Save the settings.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            App.Layouts.Backend.displayNotification(lang('settings_are_invalid'));
            return;
        }

        const googleCalendarSettings = serialize();

        App.Http.GoogleCalendarSettings.save(googleCalendarSettings).done(() => {
            App.Layouts.Backend.displayNotification(lang('settings_saved'));
        });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $saveSettings.on('click', onSaveSettingsClick);

        const googleCalendarSettings = vars('google_calendar_settings');

        deserialize(googleCalendarSettings);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
