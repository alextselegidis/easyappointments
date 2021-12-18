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

/**
 * General Settings
 *
 * Contains the functionality of the general settings page.
 */
App.Pages.GeneralSettings = (function () {
    const $saveSettings = $('#save-settings');

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#general-settings .is-invalid').removeClass('is-invalid');

            // Validate required fields.

            let missingRequiredFields = false;

            $('#general-settings .required').each(function (index, requiredField) {
                const $requiredField = $(requiredField);

                if (!$requiredField.val()) {
                    $requiredField.addClass('is-invalid');
                    missingRequiredFields = true;
                }
            });

            if (missingRequiredFields) {
                throw new Error(App.Lang.fields_are_required);
            }

            return false;
        } catch (error) {
            Backend.displayNotification(error.message);
            return true;
        }
    }

    function serialize(generalSettings) {
        generalSettings.forEach(function (generalSetting) {
            $('[data-field="' + generalSetting.name + '"]').val(generalSetting.value);
        });
    }

    function deserialize() {
        const generalSettings = [];

        $('[data-field]').each(function (index, field) {
            const $field = $(field);

            generalSettings.push({
                name: $field.data('field'),
                value: $field.val()
            });
        });

        return generalSettings;
    }

    /**
     * Save the account information.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            Backend.displayNotification(App.Lang.settings_are_invalid);

            return;
        }

        const generalSettings = deserialize();

        App.Http.GeneralSettings.save(generalSettings).done(function () {
            Backend.displayNotification(App.Lang.settings_saved);
        });
    }

    function init() {
        const generalSettings = App.Vars.general_settings;

        serialize(generalSettings);

        $saveSettings.on('click', onSaveSettingsClick);

        Backend.placeFooterToBottom();
    }

    document.addEventListener('DOMContentLoaded', init);

    return {};
})();
