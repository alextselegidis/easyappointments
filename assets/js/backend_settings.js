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

window.BackendSettings = window.BackendSettings || {};

/**
 * Backend Settings
 *
 * Contains the functionality of the backend settings page. Can either work for system or user settings,
 * but the actions allowed to the user are restricted to his role (only admin has full privileges).
 *
 * @module BackendSettings
 */
(function (exports) {

    'use strict';

    // Constants
    exports.SETTINGS_SYSTEM = 'SETTINGS_SYSTEM';
    exports.SETTINGS_USER = 'SETTINGS_USER';

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

        $('#cookie-notice-content, #terms-and-conditions-content, #privacy-policy-content').trumbowyg();

        // Apply setting values from database.
        var workingPlan = {};

        GlobalVariables.settings.system.forEach(function (setting) {
            $('input[data-field="' + setting.name + '"]').val(setting.value);
            $('select[data-field="' + setting.name + '"]').val(setting.value);

            if (setting.name === 'company_working_plan') {
                workingPlan = $.parseJSON(setting.value);
            }

            if (setting.name === 'customer_notifications') {
                $('#customer-notifications').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'require_captcha') {
                $('#require-captcha').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'require_phone_number') {
                $('#require-phone-number').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'display_any_provider') {
                $('#display-any-provider').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'display_cookie_notice') {
                $('#display-cookie-notice').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'cookie_notice_content') {
                $('#cookie-notice-content').trumbowyg('html', setting.value);
            }

            if (setting.name === 'display_terms_and_conditions') {
                $('#display-terms-and-conditions').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'terms_and_conditions_content') {
                $('#terms-and-conditions-content').trumbowyg('html', setting.value);
            }

            if (setting.name === 'display_privacy_policy') {
                $('#display-privacy-policy').prop('checked', Boolean(Number(setting.value)));
            }

            if (setting.name === 'privacy_policy_content') {
                $('#privacy-policy-content').trumbowyg('html', setting.value);
            }
        });

        exports.wp = new WorkingPlan();
        exports.wp.setup(workingPlan);
        exports.wp.timepickers(false);

        // Load user settings into form
        $('#user-id').val(GlobalVariables.settings.user.id);
        $('#first-name').val(GlobalVariables.settings.user.first_name);
        $('#last-name').val(GlobalVariables.settings.user.last_name);
        $('#email').val(GlobalVariables.settings.user.email);
        $('#mobile-number').val(GlobalVariables.settings.user.mobile_number);
        $('#phone-number').val(GlobalVariables.settings.user.phone_number);
        $('#address').val(GlobalVariables.settings.user.address);
        $('#city').val(GlobalVariables.settings.user.city);
        $('#state').val(GlobalVariables.settings.user.state);
        $('#zip-code').val(GlobalVariables.settings.user.zip_code);
        $('#notes').val(GlobalVariables.settings.user.notes);
        $('#timezone').val(GlobalVariables.settings.user.timezone);
        $('#username').val(GlobalVariables.settings.user.settings.username);
        $('#password, #retype-password').val('');
        $('#calendar-view').val(GlobalVariables.settings.user.settings.calendar_view);
        $('#user-notifications').prop('checked', Boolean(Number(GlobalVariables.settings.user.settings.notifications)));

        // Set default settings helper.
        settings = new SystemSettings();

        if (defaultEventHandlers) {
            bindEventHandlers();
            var $link = $('#settings-page .nav li').not('.d-none').first().find('a');
            $link.tab('show');
        }

        // Apply Privileges
        if (GlobalVariables.user.privileges.system_settings.edit === false) {
            $('#general, #business-logic').find('select, input, textarea').prop('readonly', true);
            $('#general, #business-logic').find('button').prop('disabled', true);
        }

        if (GlobalVariables.user.privileges.user_settings.edit === false) {
            $('#user').find('select, input, textarea').prop('readonly', true);
            $('#user').find('button').prop('disabled', true);
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
         * Event: Tab "Click"
         *
         * Change the visible tab contents.
         */
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            var href = $(this).attr('href');

            if (href === '#general') {
                settings = new SystemSettings();
            } else if (href === '#business-logic') {
                settings = new SystemSettings();
            } else if (href === '#legal-contents') {
                settings = new SystemSettings();
            } else if (href === '#current-user') {
                settings = new UserSettings();
            }

            Backend.placeFooterToBottom();
        });

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
         * Event: Username "Focusout"
         *
         * When the user leaves the username input field we will need to check if the username
         * is not taken by another record in the system. Usernames must be unique.
         */
        $('#username').focusout(function () {
            var $input = $(this);

            if ($input.prop('readonly') === true || $input.val() === '') {
                return;
            }

            var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_validate_username';

            var data = {
                csrfToken: GlobalVariables.csrfToken,
                username: $input.val(),
                user_id: $input.parents().eq(2).find('#user-id').val()
            };

            $.post(url, data)
                .done(function (response) {
                    if (response === 'false') {
                        $input.closest('.form-group').addClass('has-error');
                        Backend.displayNotification(EALang.username_already_exists);
                        $input.attr('already-exists', 'true');
                    } else {
                        $input.closest('.form-group').removeClass('has-error');
                        $input.attr('already-exists', 'false');
                    }
                });
        });

        /**
         * Event: Apply Global Working Plan
         */
        $('#apply-global-working-plan').on('click', function () {
            var buttons = [
                {
                    text: EALang.cancel,
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
                                Backend.displayNotification(EALang.working_plans_got_updated);
                            })
                            .always(function () {
                                $('#message-box').dialog('close');
                            });
                    }
                }
            ];

            GeneralFunctions.displayMessageBox(EALang.working_plan, EALang.overwrite_existing_working_plans, buttons);
        });
    }

})(window.BackendSettings);
