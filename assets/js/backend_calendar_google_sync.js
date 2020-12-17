/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

/**
 * Backend Calendar Google Sync
 *
 * This module implements the Google Calendar sync operations.
 *
 * @module BackendCalendarGoogleSync
 */
window.BackendCalendarGoogleSync = window.BackendCalendarGoogleSync || {};

(function (exports) {

    'use strict';

    /**
     * Bind event handlers.
     */
    function bindEventHandlers() {
        /**
         * Event: Enable - Disable Synchronization Button "Click"
         *
         * When the user clicks on the "Enable Sync" button, a popup should appear
         * that is going to follow the web server authorization flow of OAuth.
         */
        $('#enable-sync').on('click', function () {
            if ($('#enable-sync').hasClass('enabled') === false) {
                // Enable synchronization for selected provider.
                var authUrl = GlobalVariables.baseUrl + '/index.php/google/oauth/'
                    + $('#select-filter-item').val();

                var redirectUrl = GlobalVariables.baseUrl + '/index.php/google/oauth_callback';

                var windowHandle = window.open(authUrl, 'Authorize Easy!Appointments',
                    'width=800, height=600');

                var authInterval = window.setInterval(function () {
                    // When the browser redirects to the google user consent page the "window.document" variable
                    // becomes "undefined" and when it comes back to the redirect URL it changes back. So check
                    // whether the variable is undefined to avoid javascript errors.
                    try {
                        if (windowHandle.document) {
                            if (windowHandle.document.URL.indexOf(redirectUrl) !== -1) {
                                // The user has granted access to his data.
                                windowHandle.close();
                                window.clearInterval(authInterval);
                                $('#enable-sync').addClass('btn-secondary enabled').removeClass('btn-light');
                                $('#enable-sync span').text(EALang.disable_sync);
                                $('#google-sync').prop('disabled', false);
                                $('#select-filter-item option:selected').attr('google-sync', 'true');

                                // Display the calendar selection dialog. First we will get a list of the available
                                // user's calendars and then we will display a selection modal so the user can select
                                // the sync calendar.
                                var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_get_google_calendars';

                                var data = {
                                    csrfToken: GlobalVariables.csrfToken,
                                    provider_id: $('#select-filter-item').val()
                                };

                                $.post(url, data)
                                    .done(function (response) {
                                        $('#google-calendar').empty();

                                        response.forEach(function (calendar) {
                                            $('#google-calendar').append(new Option(calendar.summary, calendar.id));
                                        });

                                        $('#select-google-calendar').modal('show');
                                    });
                            }
                        }
                    } catch (Error) {
                        // Accessing the document object before the window is loaded throws an error, but it will only
                        // happen during the initialization of the window. Attaching "load" event handling is not
                        // possible due to CORS restrictions.
                    }
                }, 100);

            } else {
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
                            // Disable synchronization for selected provider.
                            var providerId = $('#select-filter-item').val();

                            var provider = GlobalVariables.availableProviders.find(function (availableProvider) {
                                return Number(availableProvider.id) === Number(providerId);
                            });

                            if (!provider) {
                                throw new Error('Provider not found: ' + providerId);
                            }

                            provider.settings.google_sync = '0';
                            provider.settings.google_token = null;

                            disableProviderSync(provider.id);

                            $('#enable-sync').removeClass('btn-secondary enabled').addClass('btn-light');
                            $('#enable-sync span').text(EALang.enable_sync);
                            $('#google-sync').prop('disabled', true);
                            $('#select-filter-item option:selected').attr('google-sync', 'false');

                            $('#message-box').dialog('close');
                        }
                    }
                ];

                GeneralFunctions.displayMessageBox(EALang.disable_sync, EALang.disable_sync_prompt, buttons);
            }
        });

        /**
         * Event: Select Google Calendar "Click"
         */
        $('#select-calendar').on('click', function () {
            var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_select_google_calendar';

            var data = {
                csrfToken: GlobalVariables.csrfToken,
                provider_id: $('#select-filter-item').val(),
                calendar_id: $('#google-calendar').val()
            };
            $.post(url, data)
                .done(function () {
                    Backend.displayNotification(EALang.google_calendar_selected);
                    $('#select-google-calendar').modal('hide');
                });
        });

        /**
         * Event: Google Sync Button "Click"
         *
         * Trigger the synchronization algorithm.
         */
        $('#google-sync').on('click', function () {
            var url = GlobalVariables.baseUrl + '/index.php/google/sync/' + $('#select-filter-item').val();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json'
            })
                .done(function (response) {
                    Backend.displayNotification(EALang.google_sync_completed);
                    $('#reload-appointments').trigger('click');
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    Backend.displayNotification(EALang.google_sync_failed);
                });
        });
    }

    /**
     * Disable Provider Sync
     *
     * This method disables the google synchronization for a specific provider.
     *
     * @param {Number} providerId The selected provider record ID.
     */
    function disableProviderSync(providerId) {
        // Make an ajax call to the server in order to disable the setting from the database.
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_disable_provider_sync';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            provider_id: providerId
        };

        $.post(url, data);
    }


    exports.initialize = function () {
        bindEventHandlers();
    };

})(window.BackendCalendarGoogleSync);
