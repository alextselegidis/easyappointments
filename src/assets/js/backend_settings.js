/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
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
(function(exports) {

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
     * @param {bool} bindEventHandlers Optional (true), determines whether to bind the default event handlers.
     */
    exports.initialize = function(bindEventHandlers) {
        bindEventHandlers = bindEventHandlers || true;

        // Apply setting values from database.
        $.each(GlobalVariables.settings.system, function(index, setting) {
            $('input[data-field="' + setting.name + '"]').val(setting.value);
            $('select[data-field="' + setting.name + '"]').val(setting.value);
        });

        var workingPlan = {};
        $.each(GlobalVariables.settings.system, function(index, setting) {
            if (setting.name == 'company_working_plan') {
                workingPlan = $.parseJSON(setting.value);
            }

            if (setting.name == 'customer_notifications' && setting.value == '1') {
                $('#customer-notifications').addClass('active');
            }

            if (setting.name == 'require_captcha' && setting.value == '1') {
                $('#require-captcha').addClass('active');
            }
        });

        exports.wp = new WorkingPlan();
        exports.wp.setup(workingPlan);
        exports.wp.timepickers(false);

        // Book Advance Timeout Spinner
        $('#book-advance-timeout').spinner({
            'min': 0
        });

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

        $('#username').val(GlobalVariables.settings.user.settings.username);
        $('#password, #retype-password').val('');
        $('#calendar-view').val(GlobalVariables.settings.user.settings.calendar_view);

        if (GlobalVariables.settings.user.settings.notifications == true) {
            $('#user-notifications').addClass('active');
        } else {
            $('#user-notifications').removeClass('active');
        }

        // Set default settings helper.
        settings = new SystemSettings();

        if (bindEventHandlers) {
            _bindEventHandlers();
            $('#settings-page .nav li').first().addClass('active');
            $('#settings-page .nav li').first().find('a').trigger('click');
        }

        // Apply Privileges
        if (GlobalVariables.user.privileges.system_settings.edit == false) {
            $('#general, #business-logic').find('select, input, textarea').prop('readonly', true);
            $('#general, #business-logic').find('button').prop('disabled', true);
        }

        if (GlobalVariables.user.privileges.user_settings.edit == false) {
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
    function _bindEventHandlers() {
        exports.wp.bindEventHandlers();

        /**
         * Event: Tab "Click"
         *
         * Change the visible tab contents.
         */
        $('.tab').click(function() {
            // Bootstrap has a bug with toggle buttons. Their toggle state is lost when the
            // button is hidden or shown. Show before anything else we need to store the toggle
            // and apply it whenever the user tab is clicked..
            var areNotificationsActive = $('#user-notifications').hasClass('active');

            $(this).parent().find('.active').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();

            if ($(this).hasClass('general-tab')) {
                $('#general').show();
                settings = new SystemSettings();
            } else if ($(this).hasClass('business-logic-tab')) {
                $('#business-logic').show();
                settings = new SystemSettings();

            } else if ($(this).hasClass('user-tab')) {
                $('#user').show();
                settings = new UserSettings();

                // Apply toggle state to user notifications button.
                if (areNotificationsActive) {
                    $('#user-notifications').addClass('active');
                } else {
                    $('#user-notifications').removeClass('active');
                }
            } else if ($(this).hasClass('about-tab')) {
                $('#about').show();
            }

            Backend.placeFooterToBottom();
        });

        /**
         * Event: Save Settings Button "Click"
         *
         * Store the setting changes into the database.
         */
        $('.save-settings').click(function() {
            var data = settings.get();
            settings.save(data);
        });

        /**
         * Event: Username "Focusout"
         *
         * When the user leaves the username input field we will need to check if the username
         * is not taken by another record in the system. Usernames must be unique.
         */
        $('#username').focusout(function() {
            var $input = $(this);

            if ($input.prop('readonly') == true || $input.val() == '') {
                return;
            }

            var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_validate_username';
            var postData = {
                csrfToken: GlobalVariables.csrfToken,
                username: $input.val(),
                user_id: $input.parents().eq(2).find('#user-id').val()
            };

            $.post(postUrl, postData, function(response) {
                if (!GeneralFunctions.handleAjaxExceptions(response)) {
                    return;
                }

                if (response == false) {
                    $input.css('border', '2px solid red');
                    Backend.displayNotification(EALang['username_already_exists']);
                    $input.attr('already-exists', 'true');
                } else {
                    $input.css('border', '');
                    $input.attr('already-exists', 'false');
                }
            }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
        });
    }

})(window.BackendSettings);
