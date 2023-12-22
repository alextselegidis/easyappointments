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
 * Matomo Analytics settings page.
 *
 * This module implements the functionality of the Matomo Analytics settings page.
 */
App.Pages.MatomoAnalyticsSettings = (function () {
    const $saveSettings = $('#save-settings');

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#matomo-analytics-settings .is-invalid').removeClass('is-invalid');

            // Validate required fields.

            let missingRequiredFields = false;

            $('#matomo-analytics-settings .required').each((index, requiredField) => {
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

    function deserialize(matomoAnalyticsSettings) {
        matomoAnalyticsSettings.forEach((matomoAnalyticsSetting) => {
            const $field = $('[data-field="' + matomoAnalyticsSetting.name + '"]');

            $field.is(':checkbox')
                ? $field.prop('checked', Boolean(Number(matomoAnalyticsSetting.value)))
                : $field.val(matomoAnalyticsSetting.value);
        });
    }

    function serialize() {
        const matomoAnalyticsSettings = [];

        $('[data-field]').each((index, field) => {
            const $field = $(field);

            matomoAnalyticsSettings.push({
                name: $field.data('field'),
                value: $field.is(':checkbox') ? Number($field.prop('checked')) : $field.val(),
            });
        });

        return matomoAnalyticsSettings;
    }

    /**
     * Save the account information.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            App.Layouts.Backend.displayNotification(lang('settings_are_invalid'));

            return;
        }

        const matomoAnalyticsSettings = serialize();

        App.Http.MatomoAnalyticsSettings.save(matomoAnalyticsSettings).done(() => {
            App.Layouts.Backend.displayNotification(lang('settings_saved'));
        });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $saveSettings.on('click', onSaveSettingsClick);

        const matomoAnalyticsSettings = vars('matomo_analytics_settings');

        deserialize(matomoAnalyticsSettings);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
