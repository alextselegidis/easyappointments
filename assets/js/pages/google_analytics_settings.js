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
 * Google Analytics settings page.
 *
 * This module implements the functionality of the Google Analytics settings page.
 */
App.Pages.GoogleAnalyticsSettings = (function () {
    const $saveSettings = $('#save-settings');

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#google-analytics-settings .is-invalid').removeClass('is-invalid');

            // Validate required fields.

            let missingRequiredFields = false;

            $('#google-analytics-settings .required').each((index, requiredField) => {
                const $requiredField = $(requiredField);

                if (!$requiredField.val()) {
                    $requiredField.addClass('is-invalid');
                    missingRequiredFields = true;
                }
            });

            if (missingRequiredFields) {
                throw new Error(lang('fields_are_required'));
            }

            return false;
        } catch (error) {
            App.Layouts.Backend.displayNotification(error.message);
            return true;
        }
    }

    function deserialize(googleAnalyticsSettings) {
        googleAnalyticsSettings.forEach((googleAnalyticsSetting) => {
            const $field = $('[data-field="' + googleAnalyticsSetting.name + '"]');

            $field.is(':checkbox')
                ? $field.prop('checked', Boolean(Number(googleAnalyticsSetting.value)))
                : $field.val(googleAnalyticsSetting.value);
        });
    }

    function serialize() {
        const googleAnalyticsSettings = [];

        $('[data-field]').each((index, field) => {
            const $field = $(field);

            googleAnalyticsSettings.push({
                name: $field.data('field'),
                value: $field.is(':checkbox') ? Number($field.prop('checked')) : $field.val(),
            });
        });

        return googleAnalyticsSettings;
    }

    /**
     * Save the account information.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            App.Layouts.Backend.displayNotification(lang('settings_are_invalid'));

            return;
        }

        const googleAnalyticsSettings = serialize();

        App.Http.GoogleAnalyticsSettings.save(googleAnalyticsSettings).done(() => {
            App.Layouts.Backend.displayNotification(lang('settings_saved'));
        });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $saveSettings.on('click', onSaveSettingsClick);

        const googleAnalyticsSettings = vars('google_analytics_settings');

        deserialize(googleAnalyticsSettings);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
