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

window.BackendProviders = window.BackendProviders || {};

/**
 * Backend Providers
 *
 * This module handles the js functionality of the providers backend page.
 *
 * @module BackendProviders
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
     * @type {ProvidersHelper}
     */
    var helper = {};

    /**
     * Use this class instance for performing actions on the working plan.
     *
     * @type {WorkingPlan}
     */
    exports.wp = {};

    /**
     * Initialize the backend providers page.
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
        GlobalVariables.services.forEach(function (service) {
            $('<div/>', {
                'class': 'checkbox',
                'html': [
                    $('<div/>', {
                        'class': 'checkbox form-check',
                        'html': [
                            $('<input/>', {
                                'class': 'form-check-input',
                                'type': 'checkbox',
                                'data-id': service.id,
                                'prop': {
                                    'disabled': true
                                }
                            }),
                            $('<label/>', {
                                'class': 'form-check-label',
                                'text': service.name,
                                'for': service.id
                            })
                        ]
                    })
                ]
            }).appendTo('#provider-services');
        });

        // Bind event handlers.
        if (defaultEventHandlers) {
            bindEventHandlers();
        }
    };

    /**
     * Binds the default backend providers event handlers. Do not use this method on a different
     * page because it needs the backend providers page DOM.
     */
    function bindEventHandlers() {
        /**
         * Event: Provider Username "Blur"
         *
         * When the provider leaves the provider username input field we will need to check if the username
         * is not taken by another record in the system. 
         */
        $('#provider-username').focusout(function () {
            var $input = $(this);

            if ($input.prop('readonly') === true || $input.val() === '') {
                return;
            }

            var providerId = $input.parents().eq(2).find('.record-id').val();

            if (!providerId) {
                return;
            }

            var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_validate_username';

            var data = {
                csrfToken: GlobalVariables.csrfToken,
                username: $input.val(),
                user_id: providerId
            };

            $.post(url, data).done(function (response) {
                if (response.is_valid === 'false') {
                    $input.addClass('is-invalid');
                    $input.attr('already-exists', 'true');
                    $input.parents().eq(3).find('.form-message').text(EALang.username_already_exists);
                    $input.parents().eq(3).find('.form-message').show();
                } else {
                    $input.removeClass('is-invalid');
                    $input.attr('already-exists', 'false');
                    if ($input.parents().eq(3).find('.form-message').text() === EALang.username_already_exists) {
                        $input.parents().eq(3).find('.form-message').hide();
                    }
                }
            });
        });
    }
})(window.BackendProviders);
