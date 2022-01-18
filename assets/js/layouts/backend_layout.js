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
 * Backend layout.
 *
 * This module implements the backend layout functionality.
 */
window.App.Layouts.Backend = (function () {
    const $selectLanguage = $('#select-language');
    const $notification = $('#notification');
    const $loading = $('#loading');
    const $footer = $('#footer');

    const DB_SLUG_ADMIN = 'admin';
    const DB_SLUG_PROVIDER = 'provider';
    const DB_SLUG_SECRETARY = 'secretary';
    const DB_SLUG_CUSTOMER = 'customer';

    const PRIV_VIEW = 1;
    const PRIV_ADD = 2;
    const PRIV_EDIT = 4;
    const PRIV_DELETE = 8;

    const PRIV_APPOINTMENTS = 'appointments';
    const PRIV_CUSTOMERS = 'customers';
    const PRIV_SERVICES = 'services';
    const PRIV_USERS = 'users';
    const PRIV_SYSTEM_SETTINGS = 'system_settings';
    const PRIV_USER_SETTINGS = 'user_settings';

    function placeFooterToBottom() {
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
    }

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
    function displayNotification(message = '- No message provided for this notification -', actions = null) {
        if (!actions) {
            actions = [];

            setTimeout(() => {
                $notification.fadeOut();
            }, 5000);
        }

        $notification.empty();

        const $instance = $('<div/>', {
            'class': 'notification alert alert-dismissible fade show',
            'html': [
                $('<strong/>', {
                    'html': message
                }),
                $('<button/>', {
                    'type': 'button',
                    'class': 'btn-close',
                    'data-bs-dismiss': 'alert'
                })
            ]
        }).appendTo($notification);

        actions.forEach((action) => {
            $('<button/>', {
                'class': 'btn btn-outline-secondary btn-xs',
                'text': action.label,
                'on': {
                    'click': action.function
                }
            }).prependTo($instance);
        });

        $notification.show('fade');
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $(window)
            .on('resize', () => {
                App.Layouts.Backend.placeFooterToBottom();
            })
            .trigger('resize');

        $(document).ajaxStart(() => {
            $loading.show();
        });

        $(document).ajaxStop(() => {
            $loading.hide();
        });

        tippy('[data-tippy-content]');

        App.Utils.Lang.enableLanguageSelection($selectLanguage);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        DB_SLUG_ADMIN,
        DB_SLUG_PROVIDER,
        DB_SLUG_SECRETARY,
        DB_SLUG_CUSTOMER,
        PRIV_VIEW,
        PRIV_ADD,
        PRIV_EDIT,
        PRIV_DELETE,
        PRIV_APPOINTMENTS,
        PRIV_CUSTOMERS,
        PRIV_SERVICES,
        PRIV_USERS,
        PRIV_SYSTEM_SETTINGS,
        PRIV_USER_SETTINGS,
        placeFooterToBottom,
        displayNotification
    };
})();
