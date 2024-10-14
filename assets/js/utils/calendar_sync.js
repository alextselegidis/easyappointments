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
 * Calendar sync utility.
 *
 * This module implements the functionality of calendar sync.
 *
 * Old Name: BackendCalendarSync
 */
App.Utils.CalendarSync = (function () {
    const $selectFilterItem = $('#select-filter-item');
    const $enableSync = $('#enable-sync');
    const $disableSync = $('#disable-sync');
    const $triggerSync = $('#trigger-sync');
    const $syncButtonGroup = $('#sync-button-group');
    const $reloadAppointments = $('#reload-appointments');

    const FILTER_TYPE_PROVIDER = 'provider';

    function hasSync(type) {
        const $selectedOption = $selectFilterItem.find('option:selected');

        return Boolean(Number($selectedOption.attr(`${type}-sync`)));
    }

    function updateSyncButtons() {
        const $selectedOption = $selectFilterItem.find('option:selected');
        const type = $selectedOption.attr('type');
        const isProvider = type === FILTER_TYPE_PROVIDER;
        const hasGoogleSync = Boolean(Number($selectedOption.attr('google-sync')));
        const hasCaldavSync = Boolean(Number($selectedOption.attr('caldav-sync')));
        const hasSync = hasGoogleSync || hasCaldavSync;

        $enableSync.prop('hidden', !isProvider || hasSync);
        $syncButtonGroup.prop('hidden', !isProvider || !hasSync);
    }

    function enableGoogleSync() {
        // Enable synchronization for selected provider.

        const authUrl = App.Utils.Url.siteUrl('google/oauth/' + $('#select-filter-item').val());

        const redirectUrl = App.Utils.Url.siteUrl('google/oauth_callback');

        const windowHandle = window.open(authUrl, 'Easy!Appointments', 'width=800, height=600');

        const authInterval = window.setInterval(() => {
            // When the browser redirects to the Google user consent page the "window.document" variable
            // becomes "undefined" and when it comes back to the redirect URL it changes back. So check
            // whether the variable is undefined to avoid javascript errors.
            try {
                if (windowHandle.document) {
                    if (windowHandle.document.URL.indexOf(redirectUrl) !== -1) {
                        // The user has granted access to his data.
                        windowHandle.close();

                        window.clearInterval(authInterval);

                        const $selectedOption = $selectFilterItem.find('option:selected');

                        $selectedOption.attr('google-sync', '1');

                        updateSyncButtons();

                        selectGoogleCalendar();
                    }
                }
            } catch (Error) {
                // Accessing the document object before the window is loaded throws an error, but it will only
                // happen during the initialization of the window. Attaching "load" event handling is not
                // possible due to CORS restrictions.
            }
        }, 100);
    }

    function disableGoogleSync() {
        App.Utils.Message.show(lang('disable_sync'), lang('disable_sync_prompt'), [
            {
                text: lang('cancel'),
                click: (event, messageModal) => {
                    messageModal.hide();
                },
            },
            {
                text: lang('confirm'),
                click: (event, messageModal) => {
                    // Disable synchronization for selected provider.
                    const providerId = $selectFilterItem.val();

                    const provider = vars('available_providers').find(
                        (availableProvider) => Number(availableProvider.id) === Number(providerId),
                    );

                    if (!provider) {
                        throw new Error('Provider not found: ' + providerId);
                    }

                    provider.settings.google_sync = '0';
                    provider.settings.google_token = null;

                    App.Http.Google.disableProviderSync(provider.id);

                    const $selectedOption = $selectFilterItem.find('option:selected');

                    $selectedOption.attr('google-sync', '0');

                    updateSyncButtons();

                    messageModal.hide();
                },
            },
        ]);
    }

    function selectGoogleCalendar() {
        const providerId = $selectFilterItem.val();

        App.Http.Google.getGoogleCalendars(providerId).done((googleCalendars) => {
            const $selectGoogleCalendar = $(`
                <select class="form-control">
                    <!-- JS -->
                </select>
            `);

            googleCalendars.forEach((googleCalendar) => {
                $selectGoogleCalendar.append(new Option(googleCalendar.summary, googleCalendar.id));
            });

            const $messageModal = App.Utils.Message.show(
                lang('select_sync_calendar'),
                lang('select_sync_calendar_prompt'),
                [
                    {
                        text: lang('select'),
                        click: (event, messageModal) => {
                            const googleCalendarId = $selectGoogleCalendar.val();

                            App.Http.Google.selectGoogleCalendar(providerId, googleCalendarId).done(() => {
                                App.Layouts.Backend.displayNotification(lang('sync_calendar_selected'));
                            });

                            messageModal.hide();
                        },
                    },
                ],
            );

            $selectGoogleCalendar.appendTo($messageModal.find('.modal-body'));
        });
    }

    function triggerGoogleSync() {
        const providerId = $selectFilterItem.val();

        App.Http.Google.syncWithGoogle(providerId)
            .done(() => {
                App.Layouts.Backend.displayNotification(lang('calendar_sync_completed'));
                $reloadAppointments.trigger('click');
            })
            .fail(() => {
                App.Layouts.Backend.displayNotification(lang('calendar_sync_failed'));
            });
    }

    function enableCaldavSync(defaultCaldavUrl = '', defaultCaldavUsername = '', defaultCaldavPassword = '') {
        const $container = $(`
            <div>
                <div class="mb-3">
                    <label for="caldav-url" class="form-label">
                        ${lang('calendar_url')}
                    </label>
                    <input type="text" class="form-control" id="caldav-url" value="${defaultCaldavUrl}"/>
                </div> 
                <div class="mb-3">
                    <label for="caldav-username" class="form-label">
                        ${lang('username')}
                    </label>
                    <input type="text" class="form-control" id="caldav-username" value="${defaultCaldavUsername}"/>
                </div> 
                <div class="mb-3">
                    <label for="caldav-password" class="form-label">
                        ${lang('password')}
                    </label>
                    <input type="password" class="form-control" id="caldav-password" value="${defaultCaldavPassword}"/>
                </div>    
                
                <div class="alert alert-danger" hidden>
                    <!-- JS -->
                </div>
            </div>
        `);

        const $messageModal = App.Utils.Message.show(lang('caldav_server'), lang('caldav_connection_info_prompt'), [
            {
                text: lang('cancel'),
                click: (event, messageModal) => {
                    messageModal.hide();
                },
            },
            {
                text: lang('connect'),
                click: (event, messageModal) => {
                    const providerId = $selectFilterItem.val();

                    $messageModal.find('.is-invalid').removeClass('is-invalid');

                    const $alert = $messageModal.find('.alert');
                    $alert.text('').prop('hidden', true);

                    const $caldavUrl = $container.find('#caldav-url');
                    const caldavUrl = $caldavUrl.val();

                    if (!caldavUrl) {
                        $caldavUrl.addClass('is-invalid');
                        return;
                    }

                    const $caldavUsername = $container.find('#caldav-username');
                    const caldavUsername = $caldavUsername.val();

                    if (!caldavUsername) {
                        $caldavUsername.addClass('is-invalid');
                        return;
                    }

                    const $caldavPassword = $container.find('#caldav-password');
                    const caldavPassword = $caldavPassword.val();

                    if (!caldavPassword) {
                        $caldavPassword.addClass('is-invalid');
                        return;
                    }

                    App.Http.Caldav.connectToServer(providerId, caldavUrl, caldavUsername, caldavPassword).done(
                        (response) => {
                            if (!response.success) {
                                $caldavUrl.addClass('is-invalid');
                                $caldavUsername.addClass('is-invalid');
                                $caldavPassword.addClass('is-invalid');

                                $alert.text(lang('login_failed') + ' ' + response.message).prop('hidden', false);

                                return;
                            }

                            const $selectedOption = $selectFilterItem.find('option:selected');

                            $selectedOption.attr('caldav-sync', '1');

                            updateSyncButtons();

                            App.Layouts.Backend.displayNotification(lang('sync_calendar_selected'));

                            messageModal.hide();
                        },
                    );
                },
            },
        ]);

        $messageModal.find('.modal-body').append($container);
    }

    function disableCaldavSync() {
        App.Utils.Message.show(lang('disable_sync'), lang('disable_sync_prompt'), [
            {
                text: lang('cancel'),
                click: (event, messageModal) => {
                    messageModal.hide();
                },
            },
            {
                text: lang('confirm'),
                click: (event, messageModal) => {
                    // Disable synchronization for selected provider.
                    const providerId = $selectFilterItem.val();

                    const provider = vars('available_providers').find(
                        (availableProvider) => Number(availableProvider.id) === Number(providerId),
                    );

                    if (!provider) {
                        throw new Error('Provider not found: ' + providerId);
                    }

                    provider.settings.caldav_sync = '0';
                    provider.settings.caldav_url = null;
                    provider.settings.caldav_username = null;
                    provider.settings.caldav_password = null;

                    App.Http.Caldav.disableProviderSync(provider.id);

                    const $selectedOption = $selectFilterItem.find('option:selected');

                    $selectedOption.attr('caldav-sync', '0');

                    updateSyncButtons();

                    messageModal.hide();
                },
            },
        ]);
    }

    function triggerCaldavSync() {
        const providerId = $selectFilterItem.val();

        App.Http.Caldav.syncWithCaldav(providerId)
            .done(() => {
                App.Layouts.Backend.displayNotification(lang('calendar_sync_completed'));
                $reloadAppointments.trigger('click');
            })
            .fail(() => {
                App.Layouts.Backend.displayNotification(lang('calendar_sync_failed'));
            });
    }

    function onSelectFilterItemChange() {
        updateSyncButtons();
    }

    function onEnableSyncClick() {
        const isGoogleSyncFeatureEnabled = vars('google_sync_feature');

        if (!isGoogleSyncFeatureEnabled) {
            enableCaldavSync();
            return;
        }

        App.Utils.Message.show(lang('enable_sync'), lang('sync_method_prompt'), [
            {
                text: 'CalDAV Calendar',
                className: 'btn btn-outline-primary me-auto',
                click: (event, messageModal) => {
                    messageModal.hide();
                    enableCaldavSync();
                },
            },
            {
                text: 'Google Calendar',
                click: (event, messageModal) => {
                    messageModal.hide();
                    enableGoogleSync();
                },
            },
        ]);
    }

    function onDisableSyncClick() {
        const hasGoogleSync = hasSync('google');

        if (hasGoogleSync) {
            disableGoogleSync();
            return;
        }

        const hasCalSync = hasSync('caldav');

        if (hasCalSync) {
            disableCaldavSync();
        }
    }

    function onTriggerSyncClick() {
        const hasGoogleSync = hasSync('google');

        if (hasGoogleSync) {
            triggerGoogleSync();
            return;
        }

        const hasCalSync = hasSync('caldav');

        if (hasCalSync) {
            triggerCaldavSync();
        }
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $selectFilterItem.on('change', onSelectFilterItemChange);
        $enableSync.on('click', onEnableSyncClick);
        $disableSync.on('click', onDisableSyncClick);
        $triggerSync.on('click', onTriggerSyncClick);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        initialize,
    };
})();
