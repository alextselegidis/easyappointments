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

window.Backend = window.Backend || {};

/**
 * Backend
 *
 * This module contains functions that are used in the backend section of the application.
 *
 * @module Backend
 */
(function (exports) {

    'use strict';

    /**
     * Main javascript code for the backend of Easy!Appointments.
     */
    $(function () {
        $(window)
            .on('resize', function () {
                Backend.placeFooterToBottom();
            })
            .trigger('resize');

        $(document).ajaxStart(function () {
            $('#loading').show();
        });

        $(document).ajaxStop(function () {
            $('#loading').hide();
        });

        tippy('[data-tippy-content]');

        GeneralFunctions.enableLanguageSelection($('#select-language'));
    });

    /**
     * Backend Constants
     */
    exports.DB_SLUG_ADMIN = 'admin';
    exports.DB_SLUG_PROVIDER = 'provider';
    exports.DB_SLUG_SECRETARY = 'secretary';
    exports.DB_SLUG_CUSTOMER = 'customer';

    exports.PRIV_VIEW = 1;
    exports.PRIV_ADD = 2;
    exports.PRIV_EDIT = 4;
    exports.PRIV_DELETE = 8;

    exports.PRIV_APPOINTMENTS = 'appointments';
    exports.PRIV_CUSTOMERS = 'customers';
    exports.PRIV_SERVICES = 'services';
    exports.PRIV_USERS = 'users';
    exports.PRIV_SYSTEM_SETTINGS = 'system_settings';
    exports.PRIV_USER_SETTINGS = 'user_settings';

    /**
     * Place the backend footer always on the bottom of the page.
     */
    exports.placeFooterToBottom = function () {
        var $footer = $('#footer');

        if (window.innerHeight > $('body').height()) {
            $footer.css({
                'position': 'absolute',
                'width': '100%',
                'bottom': '0px'
            });
        } else {
            $footer.css({
                'position': 'static'
            });
        }
    };

    /**
     * Display backend notifications to user.
     *
     * Using this method you can display notifications to the use with custom messages. If the 'actions' array is
     * provided then an action link will be displayed too.
     *
     * @param {String} message Notification message
     * @param {Array} [actions] An array with custom actions that will be available to the user. Every array item is an
     * object that contains the 'label' and 'function' key values.
     */
    exports.displayNotification = function (message, actions) {
        message = message || '- No message provided for this notification -';

        var $notification = $('#notification');

        if (!actions) {
            actions = [];

            setTimeout(function () {
                $notification.fadeOut();
            }, 5000);
        }

        $notification.empty();

        var $instance = $('<div/>', {
            'class': 'notification alert',
            'html': [
                $('<button/>', {
                    'type': 'button',
                    'class': 'close',
                    'data-dismiss': 'alert',
                    'html': [
                        $('<span/>', {
                            'html': '&times;'
                        })
                    ]
                }),
                $('<strong/>', {
                    'html': message
                })
            ]
        })
            .appendTo($notification);

        actions.forEach(function (action) {
            $('<button/>', {
                'class': 'btn btn-outline-secondary btn-xs',
                'text': action.label,
                'on': {
                    'click': action.function
                }
            })
                .appendTo($instance);
        });

        $notification.show('fade');
    }

})(window.Backend);
