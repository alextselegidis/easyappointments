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
 * Jitsi settings page.
 *
 * This module implements the functionality of the Jitsi settings page.
 */
App.Pages.JitsiSettings = (function () {
    const $saveSettings = $('#save-settings');

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#jitsi-settings .is-invalid').removeClass('is-invalid');

            // No required fields for Jitsi settings, just the checkbox

            return false;
        } catch (error) {
            App.Layouts.Backend.displayNotification(error.message);
            return true;
        }
    }

    /**
     * Deserialize the Jitsi settings.
     *
     * @param {Array} jitsiSettings
     */
    function deserialize(jitsiSettings) {
        jitsiSettings.forEach((jitsiSetting) => {
            const $field = $('[data-field="' + jitsiSetting.name + '"]');

            $field.is(':checkbox')
                ? $field.prop('checked', Boolean(Number(jitsiSetting.value)))
                : $field.val(jitsiSetting.value);
        });
    }

    /**
     * Serialize the Jitsi settings.
     *
     * @return {Array}
     */
    function serialize() {
        const jitsiSettings = [];

        $('[data-field]').each((index, field) => {
            const $field = $(field);

            jitsiSettings.push({
                name: $field.data('field'),
                value: $field.is(':checkbox') ? Number($field.prop('checked')) : $field.val(),
            });
        });

        return jitsiSettings;
    }

    /**
     * Save the Jitsi settings.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            App.Layouts.Backend.displayNotification(lang('settings_are_invalid'));
            return;
        }

        const jitsiSettings = serialize();

        App.Http.JitsiSettings.save(jitsiSettings).done(() => {
            App.Layouts.Backend.displayNotification(lang('settings_saved'));
        });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $saveSettings.on('click', onSaveSettingsClick);

        const jitsiSettings = vars('jitsi_settings');

        deserialize(jitsiSettings);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
