/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

window.BackendUsers = window.BackendUsers || {};

/**
 * Backend Users
 *
 * This module handles the js functionality of the users backend page. It uses three other
 * classes (defined below) in order to handle the admin, provider and secretary record types.
 *
 * @module BackendUsers
 */
(function (exports) {

    'use strict';

    /**
     * Minimum Password Length
     *
     * @type {Number}
     */
    exports.MIN_PASSWORD_LENGTH = 7;

    /**
     * Contains the current tab record methods for the page.
     *
     * @type {AdminsHelper|ProvidersHelper|SecretariesHelper}
     */
    var helper = {};

    /**
     * Use this class instance for performing actions on the working plan.
     *
     * @type {WorkingPlan}
     */
    exports.wp = {};

    /**
     * Initialize the backend users page.
     *
     * @param {Boolean} defaultEventHandlers (OPTIONAL) Whether to bind the default event handlers.
     */
    exports.initialize = function (defaultEventHandlers) {
        defaultEventHandlers = defaultEventHandlers || true;

        exports.wp = new WorkingPlan();
        exports.wp.bindEventHandlers();

        // Instantiate default helper object (admin).
        helper = new ProvidersHelper();
        helper.resetForm();
        helper.filter('');
        helper.bindEventHandlers();

        // Fill the services and providers list boxes.
        var html = '<div>';
        $.each(GlobalVariables.services, function (index, service) {
            html +=
                '<div class="checkbox">' +
                '<label class="checkbox">' +
                '<input type="checkbox" data-id="' + service.id + '" />' +
                service.name +
                '</label>' +
                '</div>';

        });
        html += '</div>';
        $('#provider-services').html(html);

        html = '<div>';
        $.each(GlobalVariables.providers, function (index, provider) {
            html +=
                '<div class="checkbox">' +
                '<label class="checkbox">' +
                '<input type="checkbox" data-id="' + provider.id + '" />' +
                provider.first_name + ' ' + provider.last_name +
                '</label>' +
                '</div>';

        });
        html += '</div>';
        $('#secretary-providers').html(html);

        $('#reset-working-plan').qtip({
            position: {
                my: 'top center',
                at: 'bottom center'
            },
            style: {
                classes: 'qtip-green qtip-shadow custom-qtip'
            }
        });

        // Bind event handlers.
        if (defaultEventHandlers) {
            _bindEventHandlers();
        }
    };

    /**
     * Binds the default backend users event handlers. Do not use this method on a different
     * page because it needs the backend users page DOM.
     */
    function _bindEventHandlers() {
        /**
         * Event: Page Tab Button "Click"
         *
         * Changes the displayed tab.
         */
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            if ($(this).attr('href') === '#admins') {
                helper = new AdminsHelper();
            } else if ($(this).attr('href') === '#providers') {
                helper = new ProvidersHelper();
            } else if ($(this).attr('href') === '#secretaries') {
                helper = new SecretariesHelper();

                // Update the list with the all the available providers.
                var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_providers';
                var data = {
                    csrfToken: GlobalVariables.csrfToken,
                    key: ''
                };
                $.post(url, data, function (response) {
                    if (!GeneralFunctions.handleAjaxExceptions(response)) {
                        return;
                    }

                    GlobalVariables.providers = response;

                    var html = '<div>';
                    $.each(GlobalVariables.providers, function (index, provider) {
                        html +=
                            '<div class="checkbox">' +
                            '<label class="checkbox">' +
                            '<input type="checkbox" data-id="' + provider.id + '" />' +
                            provider.first_name + ' ' + provider.last_name +
                            '</label>' +
                            '</div>';
                    });
                    html += '</div>';
                    $('#secretary-providers').html(html);
                    $('#secretary-providers input:checkbox').prop('disabled', true);
                }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
            }

            helper.resetForm();
            helper.filter('');
            helper.bindEventHandlers();
            $('.filter-key').val('');
        });

        /**
         * Event: Admin, Provider, Secretary Username "Focusout"
         *
         * When the user leaves the username input field we will need to check if the username
         * is not taken by another record in the system. Usernames must be unique.
         */
        $('#admin-username, #provider-username, #secretary-username').focusout(function () {
            var $input = $(this);

            if ($input.prop('readonly') == true || $input.val() == '') {
                return;
            }

            var userId = $input.parents().eq(2).find('.record-id').val();

            if (userId == undefined) {
                return;
            }

            var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_validate_username';
            var postData = {
                csrfToken: GlobalVariables.csrfToken,
                username: $input.val(),
                user_id: userId
            };

            $.post(postUrl, postData, function (response) {
                if (!GeneralFunctions.handleAjaxExceptions(response)) {
                    return;
                }

                if (response == false) {
                    $input.closest('.form-group').addClass('has-error');
                    $input.attr('already-exists', 'true');
                    $input.parents().eq(3).find('.form-message').text(EALang.username_already_exists);
                    $input.parents().eq(3).find('.form-message').show();
                } else {
                    $input.closest('.form-group').removeClass('has-error');
                    $input.attr('already-exists', 'false');
                    if ($input.parents().eq(3).find('.form-message').text() == EALang.username_already_exists) {
                        $input.parents().eq(3).find('.form-message').hide();
                    }
                }
            }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
        });
    }

})(window.BackendUsers);
