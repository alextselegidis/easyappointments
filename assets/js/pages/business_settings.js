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
 * Business settings page.
 *
 * This module implements the functionality of the business settings page.
 */
App.Pages.BusinessSettings = (function () {
    const $saveSettings = $('#save-settings');
    const $applyGlobalWorkingPlan = $('#apply-global-working-plan');
    let workingPlanManager = null;

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#business-settings .is-invalid').removeClass('is-invalid');

            // Validate required fields.

            let missingRequiredFields = false;

            $('#business-settings .required').each((index, requiredField) => {
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

    function deserialize(businessSettings) {
        businessSettings.forEach((businessSetting) => {
            $('[data-field="' + businessSetting.name + '"]').val(businessSetting.value);
        });
    }

    function serialize() {
        const businessSettings = [];

        $('[data-field]').each((index, field) => {
            const $field = $(field);

            businessSettings.push({
                name: $field.data('field'),
                value: $field.val()
            });
        });

        const workingPlan = workingPlanManager.get();

        businessSettings.push({
            name: 'company_working_plan',
            value: JSON.stringify(workingPlan)
        });

        return businessSettings;
    }

    /**
     * Save the account information.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            App.Layouts.Backend.displayNotification(lang('settings_are_invalid'));

            return;
        }

        const businessSettings = serialize();

        App.Http.BusinessSettings.save(businessSettings).done(() => {
            App.Layouts.Backend.displayNotification(lang('settings_saved'));
        });
    }

    /**
     * Save the global working plan information.
     */
    function onApplyGlobalWorkingPlan() {
        const buttons = [
            {
                text: lang('cancel'),
                click: () => {
                    $('#message-box').dialog('close');
                }
            },
            {
                text: 'OK',
                click: () => {
                    const workingPlan = workingPlanManager.get();

                    App.Http.BusinessSettings.applyGlobalWorkingPlan(workingPlan)
                        .done(() => {
                            App.Layouts.Backend.displayNotification(lang('working_plans_got_updated'));
                        })
                        .always(() => {
                            $('#message-box').dialog('close');
                        });
                }
            }
        ];

        App.Utils.Message.show(lang('working_plan'), lang('overwrite_existing_working_plans'), buttons);
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        const businessSettings = vars('business_settings');

        deserialize(businessSettings);

        let companyWorkingPlan = {};

        vars('business_settings').forEach((businessSetting) => {
            if (businessSetting.name === 'company_working_plan') {
                companyWorkingPlan = JSON.parse(businessSetting.value);
            }
        });

        workingPlanManager = new App.Utils.WorkingPlan();
        workingPlanManager.setup(companyWorkingPlan);
        workingPlanManager.timepickers(false);
        workingPlanManager.addEventListeners();

        $saveSettings.on('click', onSaveSettingsClick);

        $applyGlobalWorkingPlan.on('click', onApplyGlobalWorkingPlan);

        App.Layouts.Backend.placeFooterToBottom();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
