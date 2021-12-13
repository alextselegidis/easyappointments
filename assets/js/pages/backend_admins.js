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

window.BackendAdmins = window.BackendAdmins || {};

/**
 * Backend Admins
 *
 * This module handles the js functionality of the admins backend page. It uses three other
 * classes (defined below) in order to handle the admin, admin and secretary record types.
 *
 * @module BackendAdmins
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
     * @type {AdminsHelper}
     */
    var helper = {};

    /**
     * Initialize the backend admins page.
     *
     * @param {Boolean} defaultEventHandlers (OPTIONAL) Whether to bind the default event handlers.
     */
    exports.initialize = function (defaultEventHandlers) {
        defaultEventHandlers = defaultEventHandlers || true;

        // Instantiate default helper object (admin).
        helper = new AdminsHelper();
        helper.resetForm();
        helper.filter('');
        helper.bindEventHandlers();

        // Bind event handlers.
        if (defaultEventHandlers) {
            bindEventHandlers();
        }
    };

    /**
     * Binds the default backend admins event handlers. Do not use this method on a different
     * page because it needs the backend admins page DOM.
     */
    function bindEventHandlers() {
        /**
         * Event: Admin Username "Blur"
         *
         * When the admin leaves the username input field we will need to check if the username
         * is not taken by another record in the system.
         */
        $('#admin-username').focusout(function () {
            var $input = $(this);

            if ($input.prop('readonly') === true || $input.val() === '') {
                return;
            }

            var adminId = $input.parents().eq(2).find('.record-id').val();

            if (!adminId) {
                return;
            }

            var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_validate_username';

            var data = {
                csrfToken: GlobalVariables.csrfToken,
                username: $input.val(),
                user_id: adminId
            };

            $.post(url, data).done(function (response) {
                if (response.is_valid === 'false') {
                    $input.addClass('is-invalid');
                    $input.attr('already-exists', 'true');
                    $input.parents().eq(3).find('.form-message').text(App.Lang.username_already_exists);
                    $input.parents().eq(3).find('.form-message').show();
                } else {
                    $input.removeClass('is-invalid');
                    $input.attr('already-exists', 'false');
                    if ($input.parents().eq(3).find('.form-message').text() === App.Lang.username_already_exists) {
                        $input.parents().eq(3).find('.form-message').hide();
                    }
                }
            });
        });
    }
})(window.BackendAdmins);
