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
 * Legal settings page.
 *
 * This module implements the functionality of the legal settings page.
 */
App.Pages.LegalSettings = (function () {
    const $saveSettings = $('#save-settings');
    const $displayCookieNotice = $('#display-cookie-notice');
    const $cookieNoticeContent = $('#cookie-notice-content');
    const $displayTermsAndConditions = $('#display-terms-and-conditions');
    const $termsAndConditionsContent = $('#terms-and-conditions-content');
    const $displayPrivacyPolicy = $('#display-privacy-policy');
    const $privacyPolicyContent = $('#privacy-policy-content');

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#legal-settings .is-invalid').removeClass('is-invalid');

            // Validate required fields.

            let missingRequiredFields = false;

            $('#legal-settings .required').each((index, requiredField) => {
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

    function deserialize(legalSettings) {
        legalSettings.forEach((legalSetting) => {
            if (legalSetting.name === 'display_cookie_notice') {
                $displayCookieNotice.prop('checked', Boolean(Number(legalSetting.value)));
            }

            if (legalSetting.name === 'cookie_notice_content') {
                $cookieNoticeContent.trumbowyg('html', legalSetting.value);
            }

            if (legalSetting.name === 'display_terms_and_conditions') {
                $displayTermsAndConditions.prop('checked', Boolean(Number(legalSetting.value)));
            }

            if (legalSetting.name === 'terms_and_conditions_content') {
                $termsAndConditionsContent.trumbowyg('html', legalSetting.value);
            }

            if (legalSetting.name === 'display_privacy_policy') {
                $displayPrivacyPolicy.prop('checked', Boolean(Number(legalSetting.value)));
            }

            if (legalSetting.name === 'privacy_policy_content') {
                $privacyPolicyContent.trumbowyg('html', legalSetting.value);
            }
        });
    }

    function serialize() {
        const legalSettings = [];

        legalSettings.push({
            name: 'display_cookie_notice',
            value: $displayCookieNotice.prop('checked') ? '1' : '0',
        });

        legalSettings.push({
            name: 'cookie_notice_content',
            value: $cookieNoticeContent.trumbowyg('html'),
        });

        legalSettings.push({
            name: 'display_terms_and_conditions',
            value: $displayTermsAndConditions.prop('checked') ? '1' : '0',
        });

        legalSettings.push({
            name: 'terms_and_conditions_content',
            value: $termsAndConditionsContent.trumbowyg('html'),
        });

        legalSettings.push({
            name: 'display_privacy_policy',
            value: $displayPrivacyPolicy.prop('checked') ? '1' : '0',
        });

        legalSettings.push({
            name: 'privacy_policy_content',
            value: $privacyPolicyContent.trumbowyg('html'),
        });

        return legalSettings;
    }

    /**
     * Save the account information.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            App.Layouts.Backend.displayNotification(lang('settings_are_invalid'));

            return;
        }

        const legalSettings = serialize();

        App.Http.LegalSettings.save(legalSettings).done(() => {
            App.Layouts.Backend.displayNotification(lang('settings_saved'));
        });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        App.Utils.UI.initializeTextEditor($cookieNoticeContent);
        App.Utils.UI.initializeTextEditor($termsAndConditionsContent);
        App.Utils.UI.initializeTextEditor($privacyPolicyContent);

        const legalSettings = vars('legal_settings');

        deserialize(legalSettings);

        $saveSettings.on('click', onSaveSettingsClick);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
