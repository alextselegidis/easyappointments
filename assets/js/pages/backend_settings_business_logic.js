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

window.BackendSettingsBusinessLogic = window.BackendSettingsBusinessLogic || {};

/**
 * Backend Settings
 *
 * Contains the functionality of the backend settings page. Can either work for system or user settings,
 * but the actions allowed to the user are restricted to his role (only admin has full privileges).
 *
 * @module BackendSettingsBusinessLogic
 */
(function (exports) {
    'use strict';

    // Constants
    exports.SETTINGS_SYSTEM = 'SETTINGS_SYSTEM';

    /**
     * Use this WorkingPlan class instance to perform actions on the page's working plan tables.
     *
     * @type {WorkingPlan}
     */
    exports.wp = {};

    /**
     * Tab settings object.
     *
     * @type {Object}
     */
    var settings = {};

    /**
     * Initialize Page
     *
     * @param {bool} defaultEventHandlers Optional (true), determines whether to bind the default event handlers.
     */
    exports.initialize = function (defaultEventHandlers) {
        defaultEventHandlers = defaultEventHandlers || true;

        // Apply setting values from database.
        var workingPlan = {};

        GlobalVariables.settings.system.forEach(function (setting) {
            $('input[data-field="' + setting.name + '"]').val(setting.value);
            $('select[data-field="' + setting.name + '"]').val(setting.value);

            if (setting.name === 'company_working_plan') {
                workingPlan = $.parseJSON(setting.value);
            }
        });

        exports.wp = new WorkingPlan();
        exports.wp.setup(workingPlan);
        exports.wp.timepickers(false);

        // Set default settings helper.
        settings = new SystemSettingsBusinessLogicHelper();

        if (defaultEventHandlers) {
            bindEventHandlers();
            var $link = $('#settings-page .nav li').not('.d-none').first().find('a');
            $link.tab('show');
        }

        // Apply Privileges
        if (GlobalVariables.user.privileges.system_settings.edit === false) {
            $('#business-logic').find('select, input, textarea').prop('readonly', true);
            $('#business-logic').find('button').prop('disabled', true);
        }

        Backend.placeFooterToBottom();
    };

    /**
     * Bind the backend/settings default event handlers.
     *
     * This method depends on the backend/settings html, so do not use this method on a different page.
     */
    function bindEventHandlers() {
        exports.wp.bindEventHandlers();

        /**
         * Event: Save Settings Button "Click"
         *
         * Store the setting changes into the database.
         */
        $('.save-settings').on('click', function () {
            var data = settings.get();
            settings.save(data);
        });

        /**
         * Event: Apply Global Working Plan
         */
        $('#apply-global-working-plan').on('click', function () {
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
                        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_apply_global_working_plan';

                        var data = {
                            csrfToken: GlobalVariables.csrfToken,
                            working_plan: JSON.stringify(exports.wp.get())
                        };

                        $.post(url, data)
                            .done(function () {
                                Backend.displayNotification(App.Lang.working_plans_got_updated);
                            })
                            .always(function () {
                                $('#message-box').dialog('close');
                            });
                    }
                }
            ];

            GeneralFunctions.displayMessageBox(
                App.Lang.working_plan,
                App.Lang.overwrite_existing_working_plans,
                buttons
            );
        });
    }
})(window.BackendSettingsBusinessLogic);
