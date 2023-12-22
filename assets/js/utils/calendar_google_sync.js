/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Calendar Google sync utility.
 *
 * This module implements the functionality of calendar google sync.
 *
 * Old Name: BackendCalendarGoogleSync
 */
App.Utils.CalendarGoogleSync = (function () {
    const $selectFilterItem = $('#select-filter-item');

    /**
     * Add the utility event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Enable - Disable Synchronization Button "Click"
         *
         * When the user clicks on the "Enable Sync" button, a popup should appear
         * that is going to follow the web server authorization flow of OAuth.
         */
        $('#enable-sync').on('click', () => {
            if ($('#enable-sync').hasClass('enabled') === false) {
                // Enable synchronization for selected provider.
                const authUrl = App.Utils.Url.siteUrl('google/oauth/' + $('#select-filter-item').val());

                const redirectUrl = App.Utils.Url.siteUrl('google/oauth_callback');

                const windowHandle = window.open(authUrl, 'Authorize Easy!Appointments', 'width=800, height=600');

                const authInterval = window.setInterval(() => {
                    // When the browser redirects to the google user consent page the "window.document" constiable
                    // becomes "undefined" and when it comes back to the redirect URL it changes back. So check
                    // whether the constiable is undefined to avoid javascript errors.
                    try {
                        if (windowHandle.document) {
                            if (windowHandle.document.URL.indexOf(redirectUrl) !== -1) {
                                // The user has granted access to his data.
                                windowHandle.close();
                                window.clearInterval(authInterval);
                                $('#enable-sync').addClass('btn-secondary enabled').removeClass('btn-light');
                                $('#enable-sync span').text(lang('disable_sync'));
                                $('#google-sync').prop('disabled', false);
                                $('#select-filter-item option:selected').attr('google-sync', 'true');

                                // Display the calendar selection dialog. First we will get a list of the available
                                // user's calendars and then we will display a selection modal so the user can select
                                // the sync calendar.
                                const providerId = $('#select-filter-item').val();

                                App.Http.Google.getGoogleCalendars(providerId).done((response) => {
                                    $('#google-calendar').empty();

                                    response.forEach((calendar) => {
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
                const buttons = [
                    {
                        text: lang('cancel'),
                        click: (event, messageModal) => {
                            messageModal.dispose();
                        },
                    },
                    {
                        text: 'OK',
                        click: (event, messageModal) => {
                            // Disable synchronization for selected provider.
                            const providerId = $('#select-filter-item').val();

                            const provider = vars('available_providers').find(
                                (availableProvider) => Number(availableProvider.id) === Number(providerId),
                            );

                            if (!provider) {
                                throw new Error('Provider not found: ' + providerId);
                            }

                            provider.settings.google_sync = '0';
                            provider.settings.google_token = null;

                            App.Http.Google.disableProviderSync(provider.id);

                            $('#enable-sync').removeClass('btn-secondary enabled').addClass('btn-light');
                            $('#enable-sync span').text(lang('enable_sync'));
                            $('#google-sync').prop('disabled', true);
                            $('#select-filter-item option:selected').attr('google-sync', 'false');

                            messageModal.dispose();
                        },
                    },
                ];

                App.Utils.Message.show(lang('disable_sync'), lang('disable_sync_prompt'), buttons);
            }
        });

        /**
         * Event: Select Google Calendar "Click"
         */
        $('#select-calendar').on('click', () => {
            const providerId = $('#select-filter-item').val();

            const calendarId = $('#google-calendar').val();

            App.Http.Google.selectGoogleCalendar(providerId, calendarId).done(() => {
                App.Layouts.Backend.displayNotification(lang('google_calendar_selected'));
                $('#select-google-calendar').modal('hide');
            });
        });

        /**
         * Event: Google Sync Button "Click"
         *
         * Trigger the synchronization algorithm.
         */
        $('#google-sync').on('click', () => {
            const providerId = $selectFilterItem.val();

            App.Http.Google.syncWithGoogle(providerId)
                .done(() => {
                    App.Layouts.Backend.displayNotification(lang('google_sync_completed'));
                    $('#reload-appointments').trigger('click');
                })
                .fail(() => {
                    App.Layouts.Backend.displayNotification(lang('google_sync_failed'));
                });
        });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        addEventListeners();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        initialize,
    };
})();
