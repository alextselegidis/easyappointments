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
 * Business Settings
 *
 * Contains the functionality of the business settings page.
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
                throw new Error(App.Lang.fields_are_required);
            }

            return false;
        } catch (error) {
            Backend.displayNotification(error.message);
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
            Backend.displayNotification(App.Lang.settings_are_invalid);

            return;
        }

        const businessSettings = serialize();

        App.Http.BusinessSettings.save(businessSettings).done(() => {
            Backend.displayNotification(App.Lang.settings_saved);
        });
    }

    /**
     * Save the global working plan information.
     */
    function onApplyGlobalWorkingPlan() {
        var buttons = [
            {
                text: App.Lang.cancel,
                click: function () {
                    $('#message-box').dialog('close');
                }
            },
            {
                text: 'OK',
                click: function () {
                    const workingPlan = workingPlanManager.get();

                    App.Http.BusinessSettings.applyGlobalWorkingPlan(workingPlan)
                        .done(() => {
                            Backend.displayNotification(App.Lang.working_plans_got_updated);
                        })
                        .always(function () {
                            $('#message-box').dialog('close');
                        });
                }
            }
        ];

        GeneralFunctions.displayMessageBox(App.Lang.working_plan, App.Lang.overwrite_existing_working_plans, buttons);
    }

    function init() {
        const businessSettings = App.Vars.business_settings;

        deserialize(businessSettings);

        let workingPlan = {};

        App.Vars.business_settings.forEach((generalSetting) => {
            if (generalSetting.name === 'company_working_plan') {
                workingPlan = JSON.parse(generalSetting.value);
            }
        });

        workingPlanManager = new WorkingPlan();
        workingPlanManager.setup(workingPlan);
        workingPlanManager.timepickers(false);
        workingPlanManager.bindEventHandlers();

        $saveSettings.on('click', onSaveSettingsClick);

        $applyGlobalWorkingPlan.on('click', onApplyGlobalWorkingPlan);

        Backend.placeFooterToBottom();
    }

    document.addEventListener('DOMContentLoaded', init);

    return {};
})();
