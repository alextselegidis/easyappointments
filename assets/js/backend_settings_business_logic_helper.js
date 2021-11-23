/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

(function () {
    'use strict';

    /**
     * "System Settings" Tab Helper Class
     *
     * @class SystemSettingsBusinessLogicHelper
     */
    var SystemSettingsBusinessLogicHelper = function () {};

    /**
     * Save the system settings.
     *
     * This method is run after changes are detected on the tab input fields.
     *
     * @param {Array} settings Contains the system settings data.
     */
    SystemSettingsBusinessLogicHelper.prototype.save = function (settings) {
        if (!this.validate()) {
            return; // Validation failed, do not proceed.
        }

        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_settings';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            settings: JSON.stringify(settings),
            type: BackendSettingsBusinessLogic.SETTINGS_SYSTEM
        };

        $.post(url, data).done(function () {
            Backend.displayNotification(EALang.settings_saved);

            // Update the logo title on the header.
            $('#header-logo span').text($('#company-name').val());

            // Update book_advance_timeout preview
            var totalMinutes = $('#book-advance-timeout').val();
            var hours = Math.floor(totalMinutes / 60);
            var minutes = totalMinutes % 60;
            $('#book-advance-timeout-helper').text(
                EALang.book_advance_timeout_hint.replace(
                    '{$limit}',
                    ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2)
                )
            );

            // We need to refresh the working plan.
            var workingPlan = BackendSettingsBusinessLogic.wp.get();
            BackendSettingsBusinessLogic.wp.setup(workingPlan);
            BackendSettingsBusinessLogic.wp.timepickers(false);
        });
    };

    /**
     * Prepare the system settings array.
     *
     * This method uses the DOM elements of the backend/settings page, so it can't be used in another page.
     *
     * @return {Array} Returns the system settings array.
     */
    SystemSettingsBusinessLogicHelper.prototype.get = function () {
        var settings = [];

        // Business Logic Tab

        settings.push({
            name: 'company_working_plan',
            value: JSON.stringify(BackendSettingsBusinessLogic.wp.get())
        });

        settings.push({
            name: 'book_advance_timeout',
            value: $('#book-advance-timeout').val()
        });

        return settings;
    };

    /**
     * Validate the settings data.
     *
     * If the validation fails then display a message to the user.
     *
     * @return {Boolean} Returns the validation result.
     */
    SystemSettingsBusinessLogicHelper.prototype.validate = function () {
        $('#business-logic .is-invalid').removeClass('is-invalid');

        try {
            // Validate required fields.
            var missingRequired = false;
            $('#business-logic .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(EALang.fields_are_required);
            }

            return true;
        } catch (error) {
            Backend.displayNotification(error.message);
            return false;
        }
    };

    window.SystemSettingsBusinessLogicHelper = SystemSettingsBusinessLogicHelper;
})();
