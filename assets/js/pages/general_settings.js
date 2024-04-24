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
 * General settings page.
 *
 * This module implements the functionality of the general settings page.
 */
App.Pages.GeneralSettings = (function () {
    const $saveSettings = $('#save-settings');
    const $companyLogo = $('#company-logo');
    const $companyLogoPreview = $('#company-logo-preview');
    const $removeCompanyLogo = $('#remove-company-logo');
    const $companyColor = $('#company-color');
    const $resetCompanyColor = $('#reset-company-color');
    let companyLogoBase64, theme, color = '';

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

            $('#general-settings .required').each((index, requiredField) => {
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

    function deserialize(generalSettings) {
        generalSettings.forEach((generalSetting) => {
            if (generalSetting.name === 'company_logo' && generalSetting.value) {
                companyLogoBase64 = generalSetting.value;
                $companyLogoPreview.attr('src', generalSetting.value);
                $companyLogoPreview.prop('hidden', false);
                $removeCompanyLogo.prop('hidden', false);
                return;
            }

            if (generalSetting.name === 'company_color') {
                color = generalSetting.value;
                if (generalSetting.value !== '#ffffff') {
                    $resetCompanyColor.prop('hidden', false);
                }
            }

            if (generalSetting.name === 'theme') {
                theme = generalSetting.value;
            }

            const $field = $('[data-field="' + generalSetting.name + '"]');

            $field.is(':checkbox')
                ? $field.prop('checked', Boolean(Number(generalSetting.value)))
                : $field.val(generalSetting.value);
        });
    }

    function serialize() {
        const generalSettings = [];

        $('[data-field]').each((index, field) => {
            const $field = $(field);

            if($field.data('field') === 'company_color')
                color = $field.val();
            if($field.data('field') === 'theme')
                theme = $field.val();

            generalSettings.push({
                name: $field.data('field'),
                value: $field.is(':checkbox') ? Number($field.prop('checked')) : $field.val(),
            });
        });

        generalSettings.push({
            name: 'company_logo',
            value: companyLogoBase64,
        });

        return generalSettings;
    }

    /**
     * Save the general settings informations, add notification reload button when color or theme changed.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            App.Layouts.Backend.displayNotification(lang('settings_are_invalid'));

            return;
        }

        const oTheme = theme;
        const oColor = color;

        const generalSettings = serialize();

        App.Http.GeneralSettings.save(generalSettings).done(() => {

            if(theme != oTheme || color != oColor) {

                const refreshFunction = () => {
                    document.location.reload();
                };

                App.Layouts.Backend.displayNotification(lang('settings_saved'), [
                    {
                        'label': '⟳ ' + lang('reload'),
                        'function': refreshFunction,
                    },
                ]);

            } else {
                App.Layouts.Backend.displayNotification(lang('settings_saved'));
            }

        });
    }

    /**
     * Convert the selected image to a base64 encoded string.
     */
    function onCompanyLogoChange() {
        const file = $companyLogo[0].files[0];

        if (!file) {
            $removeCompanyLogo.trigger('click');
            return;
        }

        App.Utils.File.toBase64(file).then((base64) => {
            companyLogoBase64 = base64;
            $companyLogoPreview.attr('src', base64);
            $companyLogoPreview.prop('hidden', false);
            $removeCompanyLogo.prop('hidden', false);
        });
    }

    /**
     * Remove the company logo data.
     */
    function onRemoveCompanyLogoClick() {
        companyLogoBase64 = '';
        $companyLogo.val('');
        $companyLogoPreview.attr('src', '#');
        $companyLogoPreview.prop('hidden', true);
        $removeCompanyLogo.prop('hidden', true);
    }

    /**
     * Toggle the reset company color button.
     */
    function onCompanyColorChange() {
        $resetCompanyColor.prop('hidden', $companyColor.val() === '#ffffff');
    }

    /**
     * Set the company color value to "#ffffff" which is the default one.
     */
    function onResetCompanyColorClick() {
        $companyColor.val('#ffffff');
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $saveSettings.on('click', onSaveSettingsClick);

        $companyLogo.on('change', onCompanyLogoChange);

        $removeCompanyLogo.on('click', onRemoveCompanyLogoClick);

        $companyColor.on('change', onCompanyColorChange);

        $resetCompanyColor.on('click', onResetCompanyColorClick);

        const generalSettings = vars('general_settings');

        deserialize(generalSettings);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
