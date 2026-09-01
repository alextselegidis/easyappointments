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
 * CalDAV settings page.
 *
 * This module implements the functionality of the CalDAV settings page.
 */
App.Pages.CaldavSettings = (function () {
    const $saveSettings = $('#save-settings');

    /**
     * Deserialize the CalDAV settings.
     *
     * @param {Array} caldavSettings
     */
    function deserialize(caldavSettings) {
        caldavSettings.forEach((caldavSetting) => {
            $('[data-field="' + caldavSetting.name + '"]').val(caldavSetting.value);
        });
    }

    /**
     * Serialize the CalDAV settings.
     *
     * @return {Array}
     */
    function serialize() {
        const caldavSettings = [];

        $('#caldav-settings [data-field]').each((index, field) => {
            const $field = $(field);

            caldavSettings.push({
                name: $field.data('field'),
                value: $field.val(),
            });
        });

        return caldavSettings;
    }

    /**
     * Save the CalDAV settings.
     */
    function onSaveSettingsClick() {
        App.Http.CaldavSettings.save(serialize()).done(() => {
            App.Layouts.Backend.displayNotification(lang('settings_saved'));
        });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $saveSettings.on('click', onSaveSettingsClick);

        deserialize(vars('caldav_settings'));
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
