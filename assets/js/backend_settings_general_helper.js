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
     * @class SystemSettingsGeneralHelper
     */
    var SystemSettingsGeneralHelper = function () {};

    /**
     * Save the system settings.
     *
     * This method is run after changes are detected on the tab input fields.
     *
     * @param {Array} settings Contains the system settings data.
     */
    SystemSettingsGeneralHelper.prototype.save = function (settings) {
        if (!this.validate()) {
            return; // Validation failed, do not proceed.
        }

        var url = GlobalVariables.baseUrl + '/index.php/settings/general/save';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            settings: JSON.stringify(settings),
            type: BackendSettingsGeneral.SETTINGS_SYSTEM
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

            // Update variables also used in other setting tabs
            GlobalVariables.timeFormat = $('#time-format').val();
            GlobalVariables.firstWeekday = $('#first-weekday').val();
        });
    };

    /**
     * Prepare the system settings array.
     *
     * This method uses the DOM elements of the backend/settings page, so it can't be used in another page.
     *
     * @return {Array} Returns the system settings array.
     */
    SystemSettingsGeneralHelper.prototype.get = function () {
        var settings = [];

        // General Settings Tab

        $('#general')
            .find('input, select')
            .not('input:checkbox')
            .each(function (index, field) {
                settings.push({
                    name: $(field).attr('data-field'),
                    value: $(field).val()
                });
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
    SystemSettingsGeneralHelper.prototype.validate = function () {
        $('#general .has-error').removeClass('has-error');

        try {
            // Validate required fields.
            var missingRequired = false;
            $('#general .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(EALang.fields_are_required);
            }

            // Validate company email address.
            if (!GeneralFunctions.validateEmail($('#company-email').val())) {
                $('#company-email').closest('.form-group').addClass('has-error');
                throw new Error(EALang.invalid_email);
            }

            return true;
        } catch (error) {
            Backend.displayNotification(error.message);
            return false;
        }
    };

    window.SystemSettingsGeneralHelper = SystemSettingsGeneralHelper;
})();
