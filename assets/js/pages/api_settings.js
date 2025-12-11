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
 * API settings page.
 *
 * This module implements the functionality of the API settings page.
 */
App.Pages.ApiSettings = (function () {
    const $saveSettings = $('#save-settings');
    const $generateToken = $('#generate-token');
    const $hideToken = $('#hide-token');
    const $apiToken = $('#api-token');

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#api-settings .is-invalid').removeClass('is-invalid');

            // Validate required fields.

            let missingRequiredFields = false;

            $('#api-settings .required').each((index, requiredField) => {
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

    function deserialize(apiSettings) {
        apiSettings.forEach((apiSetting) => {
            const $field = $('[data-field="' + apiSetting.name + '"]');

            $field.is(':checkbox')
                ? $field.prop('checked', Boolean(Number(apiSetting.value)))
                : $field.val(apiSetting.value);
        });
    }

    function serialize() {
        const apiSettings = [];

        $('[data-field]').each((index, field) => {
            const $field = $(field);

            apiSettings.push({
                name: $field.data('field'),
                value: $field.is(':checkbox') ? Number($field.prop('checked')) : $field.val(),
            });
        });

        return apiSettings;
    }

    function generateToken() {
        App.Http.ApiSettings.generateToken().done((response) => {
            $apiToken.val(response);
        });
        
    }

    /**
     * Save the account information.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            App.Layouts.Backend.displayNotification(lang('settings_are_invalid'));

            return;
        }

        const apiSettings = serialize();

        App.Http.ApiSettings.save(apiSettings).done(() => {
            App.Layouts.Backend.displayNotification(lang('settings_saved'));
        });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $saveSettings.on('click', onSaveSettingsClick);

        $generateToken.on('click', generateToken);

        $('#hide-token').on('click', () => {
            $apiToken.attr('type', $apiToken.attr('type') === 'text'?'password':'text');
        });

        $('#clear-token').on('click', () => {
            $apiToken.val(null);
        });

        $('#copy-token').on('click', () => {
            const token = $apiToken.val();
            navigator.clipboard.writeText(token).then(() => {
                App.Layouts.Backend.displayNotification(lang('token_copied'));
            });
            
        });

        const apiSettings = vars('api_settings');

        deserialize(apiSettings);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
