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
 * Calendar Table View Utility
 *
 * This module implements the table calendar view functionality, displaying
 * multiple provider columns side by side for easy comparison and scheduling.
 */
App.Utils.CalendarTableView = (function () {
    // Constants

    const EVENT_COLORS = {
        unavailability: '#879DB4',
        blockedPeriod: '#d65069',
        notWorking: '#BEBEBE',
        workingPlanException: '#879DB4',
        default: '#7cbae8',
    };

    // DOM Elements Cache

    const $calendar = $('#calendar');
    const $calendarToolbar = $('#calendar-toolbar');
    const $calendarFilter = $('#calendar-filter');
    const $notification = $('#notification');
    const $reloadAppointments = $('#reload-appointments');
    const $selectFilterItem = $('#select-filter-item');
    const $selectService = $('#select-service');
    const $selectProvider = $('#select-provider');
    const $appointmentsModal = $('#appointments-modal');
    const $unavailabilitiesModal = $('#unavailabilities-modal');
    const $header = $('#header');
    const $footer = $('#footer');

    // Module State

    const moment = window.moment;

    let $filterProvider;
    let $filterService;
    let $selectDate;
    let $popoverTarget = null;
    let lastFocusedEventData = null;

    // Helper Functions

    /**
     * Get the calendar height based on window size.
     *
     * @returns {number} Calendar height in pixels (minimum 800px).
     */
    function getCalendarHeight() {
        const offset = $footer.outerHeight() + $header.outerHeight() + 60;
        const height = window.innerHeight - offset;

        return Math.max(height, 800);
    }

    /**
     * Close the current popover if open.
     */
    function closePopover() {
        if ($popoverTarget) {
            $popoverTarget.popover('dispose');
            $popoverTarget = null;
        }
    }

    /**
     * Check if the event is an unavailability.
     *
     * @param {Object} eventData - Event data object.
     * @returns {boolean}
     */
    function isUnavailability(eventData) {
        return Boolean(Number(eventData?.is_unavailability));
    }

    /**
     * Check if the event is a working plan exception.
     *
     * @param {Object} eventData - Event data object.
     * @returns {boolean}
     */
    function isWorkingPlanException(eventData) {
        return eventData?.workingPlanException !== undefined;
    }

    /**
     * Check if the event is a blocked period.
     *
     * @param {Object} eventData - Event data object.
     * @returns {boolean}
     */
    function isBlockedPeriod(eventData) {
        return eventData?.hasOwnProperty('name') && !eventData?.hasOwnProperty('is_unavailability');
    }

    /**
     * Get available providers based on user role.
     *
     * @returns {Array} Filtered providers array.
     */
    function getAvailableProviders() {
        const roleSlug = vars('role_slug');
        const userId = vars('user_id');

        return vars('available_providers').filter((provider) => {
            if (roleSlug === App.Layouts.Backend.DB_SLUG_ADMIN) {
                return true;
            }

            if (roleSlug === App.Layouts.Backend.DB_SLUG_SECRETARY) {
                return vars('secretary_providers').indexOf(Number(provider.id)) !== -1;
            }

            if (roleSlug === App.Layouts.Backend.DB_SLUG_PROVIDER) {
                return Number(provider.id) === Number(userId);
            }

            return false;
        });
    }

    /**
     * Get date/time format settings for FullCalendar.
     *
     * @returns {Object} Format configuration object.
     */
    function getFormatSettings() {
        let columnFormat, timeFormat, slotTimeFormat;
        switch (vars('date_format')) {
            case 'DMY':
                columnFormat = 'ddd D/M';
                break;
            case 'MDY':
            case 'YMD':
                columnFormat = 'ddd M/D';
                break;
            default:
                throw new Error('Invalid date format setting: ' + vars('date_format'));
        }

        switch (vars('time_format')) {
            case 'military':
                timeFormat = 'HH:mm';
                slotTimeFormat = 'HH:mm';
                break;
            case 'regular':
                timeFormat = 'h:mm a';
                slotTimeFormat = 'h a';
                break;
            default:
                throw new Error('Invalid time format setting: ' + vars('time_format'));
        }

        return {columnFormat, timeFormat, slotTimeFormat};
    }

    // Modal Population Helpers

    /**
     * Populate the appointments modal with appointment data.
     *
     * @param {Object} appointment - Appointment data object.
     */
    function populateAppointmentModal(appointment) {
        const customer = appointment.customer;

        App.Components.AppointmentsModal.resetModal();
        $appointmentsModal.find('.modal-header h3').text(lang('edit_appointment_title'));
        $appointmentsModal.find('#appointment-id').val(appointment.id);
        $appointmentsModal.find('#select-service').val(appointment.id_services).trigger('change');
        $appointmentsModal.find('#select-provider').val(appointment.id_users_provider);
        App.Utils.UI.setDateTimePickerValue(
            $appointmentsModal.find('#start-datetime'),
            moment(appointment.start_datetime).toDate(),
        );
        App.Utils.UI.setDateTimePickerValue(
            $appointmentsModal.find('#end-datetime'),
            moment(appointment.end_datetime).toDate(),
        );

        // Customer fields

        $appointmentsModal.find('#customer-id').val(appointment.id_users_customer);
        $appointmentsModal.find('#first-name').val(customer.first_name);
        $appointmentsModal.find('#last-name').val(customer.last_name);
        $appointmentsModal.find('#email').val(customer.email);
        $appointmentsModal.find('#phone-number').val(customer.phone_number);
        $appointmentsModal.find('#address').val(customer.address);
        $appointmentsModal.find('#city').val(customer.city);
        $appointmentsModal.find('#zip-code').val(customer.zip_code);
        $appointmentsModal.find('#language').val(customer.language);
        $appointmentsModal.find('#timezone').val(customer.timezone);
        $appointmentsModal.find('#customer-notes').val(customer.notes);
        $appointmentsModal.find('#custom-field-1').val(customer.custom_field_1);
        $appointmentsModal.find('#custom-field-2').val(customer.custom_field_2);
        $appointmentsModal.find('#custom-field-3').val(customer.custom_field_3);
        $appointmentsModal.find('#custom-field-4').val(customer.custom_field_4);
        $appointmentsModal.find('#custom-field-5').val(customer.custom_field_5);

        // Appointment fields

        $appointmentsModal.find('#appointment-location').val(appointment.location);
        $appointmentsModal.find('#appointment-meeting-link').val(appointment.meeting_link);
        $appointmentsModal.find('#appointment-status').val(appointment.status);
        $appointmentsModal.find('#appointment-notes').val(appointment.notes);
        App.Components.ColorSelection.setColor($appointmentsModal.find('#appointment-color'), appointment.color);
        $appointmentsModal.modal('show');
    }

    /**
     * Populate the unavailabilities modal with data.
     *
     * @param {Object} unavailability - Unavailability data object.
     */
    function populateUnavailabilityModal(unavailability) {
        App.Components.UnavailabilitiesModal.resetModal();
        $unavailabilitiesModal.find('.modal-header h3').text(lang('edit_unavailability_title'));
        App.Utils.UI.setDateTimePickerValue(
            $unavailabilitiesModal.find('#unavailability-start'),
            moment(unavailability.start_datetime).toDate(),
        );
        App.Utils.UI.setDateTimePickerValue(
            $unavailabilitiesModal.find('#unavailability-end'),
            moment(unavailability.end_datetime).toDate(),
        );
        $unavailabilitiesModal.find('#unavailability-id').val(unavailability.id);
        $unavailabilitiesModal.find('#unavailability-provider').val(unavailability.id_users_provider);
        $unavailabilitiesModal.find('#unavailability-notes').val(unavailability.notes);
        $unavailabilitiesModal.modal('show');
    }

    // Working Plan Exception Handlers

    /**
     * Update provider's working plan exceptions in memory.
     *
     * @param {number} providerId - Provider ID.
     * @param {Object} workingPlanExceptions - Updated exceptions object.
     */
    function updateProviderWorkingPlanExceptions(providerId, workingPlanExceptions) {
        for (const provider of vars('available_providers')) {
            if (Number(provider.id) === Number(providerId)) {
                provider.settings.working_plan_exceptions = JSON.stringify(workingPlanExceptions);
                break;
            }
        }
    }

    /**
     * Handle editing a working plan exception.
     *
     * @param {Object} data - Working plan exception data.
     */
    function handleEditWorkingPlanException(data) {
        const originalDate = data.date;
        const workingPlanException = data.workingPlanException;
        const provider = data.provider;

        App.Components.WorkingPlanExceptionsModal.edit(originalDate, workingPlanException).done(
            (date, updatedException) => {
                const successCallback = () => {
                    App.Layouts.Backend.displayNotification(lang('working_plan_exception_saved'));

                    const exceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};

                    exceptions[date] = updatedException;
                    if (date !== originalDate) {
                        delete exceptions[originalDate];
                    }

                    updateProviderWorkingPlanExceptions(provider.id, exceptions);
                    $reloadAppointments.trigger('click');
                };
                App.Http.Calendar.saveWorkingPlanException(
                    date,
                    updatedException,
                    provider.id,
                    successCallback,
                    null,
                    originalDate,
                );
            },
        );
    }

    /**
     * Handle deleting a working plan exception.
     *
     * @param {Object} eventData - Event data containing provider info.
     */
    function handleDeleteWorkingPlanException(eventData) {
        const date = moment(lastFocusedEventData.start).format('YYYY-MM-DD');
        const providerId = eventData.id;

        const successCallback = () => {
            App.Layouts.Backend.displayNotification(lang('working_plan_exception_deleted'));

            const exceptions = JSON.parse(eventData.settings.working_plan_exceptions) || {};

            delete exceptions[date];

            updateProviderWorkingPlanExceptions(providerId, exceptions);
            $reloadAppointments.trigger('click');
        };
        App.Http.Calendar.deleteWorkingPlanException(date, providerId, successCallback);
    }

    /**
     * Show appointment deletion confirmation dialog.
     *
     * @param {number} appointmentId - Appointment ID to delete.
     */
    function handleDeleteAppointment(appointmentId) {
        const buttons = [
            {
                text: lang('cancel'),
                click: (event, messageModal) => messageModal.hide(),
            },
            {
                text: lang('delete'),
                click: (event, messageModal) => {
                    const reason = $('#cancellation-reason').val();

                    App.Http.Calendar.deleteAppointment(appointmentId, reason).done(() => {
                        messageModal.hide();
                        $reloadAppointments.trigger('click');
                    });
                },
            },
        ];

        App.Utils.Message.show(lang('delete_appointment_title'), lang('write_appointment_removal_reason'), buttons);

        $('<textarea/>', {
            class: 'form-control w-100',
            id: 'cancellation-reason',
            rows: '3',
        }).appendTo('#message-modal .modal-body');
    }

    // Event Handlers - Popover Actions

    /**
     * Handle edit popover button click.
     */
    function onEditPopoverClick() {
        closePopover();

        const data = lastFocusedEventData.extendedProps.data;

        if (isWorkingPlanException(data)) {
            handleEditWorkingPlanException(data);
        } else if (!isUnavailability(data)) {
            populateAppointmentModal(data);
        } else {
            const unavailability = {
                ...data,
                start_datetime: moment(lastFocusedEventData.start).format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: moment(lastFocusedEventData.end).format('YYYY-MM-DD HH:mm:ss'),
            };

            populateUnavailabilityModal(unavailability);
        }
    }

    /**
     * Handle delete popover button click.
     */
    function onDeletePopoverClick() {
        closePopover();
        const data = lastFocusedEventData.extendedProps.data;

        if (data.hasOwnProperty('id_roles')) {
            // Working plan exception

            handleDeleteWorkingPlanException(data);
        } else if (!isUnavailability(data)) {
            handleDeleteAppointment(data.id);
        } else {
            App.Http.Calendar.deleteUnavailability(data.id).done(() => {
                $reloadAppointments.trigger('click');
            });
        }
    }

    // Calendar Event Callbacks

    /**
     * Handle calendar event click - show popover.
     *
     * @param {Object} info - FullCalendar event info.
     */
    function onEventClick(info) {
        closePopover();
        const $target = $(info.el);

        let $html;
        let displayEdit;
        let displayDelete;

        if ($target.hasClass('fc-blocked-period')) {
            // Blocked periods are read-only

            $html = App.Utils.CalendarEventPopover.buildBlockedPeriodPopover(info);
        } else if ($target.hasClass('fc-working-plan-exception')) {
            displayEdit = $target.hasClass('fc-custom') && vars('privileges').appointments.edit ? '' : 'd-none';
            displayDelete = $target.hasClass('fc-custom') && vars('privileges').appointments.delete ? 'me-2' : 'd-none';
            $html = App.Utils.CalendarEventPopover.buildWorkingPlanExceptionPopover(info, displayEdit, displayDelete);
        } else if ($target.hasClass('fc-unavailability')) {
            const isCustom = $target.hasClass('fc-custom');

            displayEdit = isCustom && vars('privileges').appointments.edit ? '' : 'd-none';
            displayDelete = isCustom && vars('privileges').appointments.delete ? 'me-2' : 'd-none';
            $html = App.Utils.CalendarEventPopover.buildUnavailabilityPopover(info, displayEdit, displayDelete);
        } else {
            displayEdit = vars('privileges').appointments.edit ? '' : 'd-none';
            displayDelete = vars('privileges').appointments.delete ? 'me-2' : 'd-none';
            $html = App.Utils.CalendarEventPopover.buildAppointmentPopover(info, displayEdit, displayDelete);
        }

        $target.popover({
            placement: 'top',
            title: App.Utils.String.escapeHtml(info.event.title),
            content: $html,
            html: true,
            container: '#calendar',
            trigger: 'manual',
        });
        lastFocusedEventData = info.event;
        $target.popover('show');
        $popoverTarget = $target;

        // Fix popover position if too high

        const $popover = $calendar.find('.popover');

        if ($popover.length && $popover.position().top < 200) {
            $popover.css('top', '200px');
        }
    }

    /**
     * Handle calendar event resize.
     *
     * @param {Object} info - FullCalendar event info.
     */
    function onEventResize(info) {
        if (!vars('privileges').appointments.edit) {
            info.revert();
            App.Layouts.Backend.displayNotification(lang('no_privileges_edit_appointments'));
            return;
        }

        if ($notification.is(':visible')) {
            $notification.hide('bind');
        }

        const eventData = info.event.extendedProps.data;

        if (!isUnavailability(eventData)) {
            handleAppointmentResize(info, eventData);
        } else {
            handleUnavailabilityResize(info, eventData);
        }
    }

    /**
     * Handle appointment resize operation.
     *
     * @param {Object} info - FullCalendar resize info.
     * @param {Object} eventData - Event data.
     */
    function handleAppointmentResize(info, eventData) {
        eventData.end_datetime = moment(eventData.end_datetime)
            .add({days: info.endDelta.days, milliseconds: info.endDelta.milliseconds})
            .format('YYYY-MM-DD HH:mm:ss');

        const appointment = prepareAppointmentForSave(eventData);

        const successCallback = (notifyCustomer) => {
            const undoFunction = () => {
                appointment.end_datetime = eventData.end_datetime = moment(appointment.end_datetime)
                    .add({days: -info.endDelta.days, milliseconds: -info.endDelta.milliseconds})
                    .format('YYYY-MM-DD HH:mm:ss');

                App.Http.Calendar.saveAppointment(appointment, null, null, null, notifyCustomer).done(() =>
                    $notification.hide('blind'),
                );
                info.revert();
            };

            App.Layouts.Backend.displayNotification(lang('appointment_updated'), [
                {label: lang('undo'), function: undoFunction},
            ]);

            $footer.css('position', 'static');
            info.event.setProp('data', eventData);
        };
        showNotifyCustomerDialog(appointment, successCallback, () => info.revert());
    }

    /**
     * Handle unavailability resize operation.
     *
     * @param {Object} info - FullCalendar resize info.
     * @param {Object} eventData - Event data.
     */
    function handleUnavailabilityResize(info, eventData) {
        const unavailability = {
            id: eventData.id,
            start_datetime: moment(info.event.start).format('YYYY-MM-DD HH:mm:ss'),
            end_datetime: moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'),
            id_users_provider: eventData.id_users_provider,
        };

        eventData.end_datetime = unavailability.end_datetime;

        const successCallback = () => {
            const undoFunction = () => {
                unavailability.end_datetime = eventData.end_datetime = moment(unavailability.end_datetime)
                    .add({days: -info.endDelta.days, milliseconds: -info.endDelta.milliseconds})
                    .format('YYYY-MM-DD HH:mm:ss');

                App.Http.Calendar.saveUnavailability(unavailability).done(() => $notification.hide('blind'));
                info.revert();
            };

            App.Layouts.Backend.displayNotification(lang('unavailability_updated'), [
                {label: lang('undo'), function: undoFunction},
            ]);

            $footer.css('position', 'static');

            info.event.setProp('data', eventData);
        };
        App.Http.Calendar.saveUnavailability(unavailability, successCallback, null);
    }

    /**
     * Handle calendar event drop (drag and drop).
     *
     * @param {Object} info - FullCalendar event info.
     */
    function onEventDrop(info) {
        if (!vars('privileges').appointments.edit) {
            info.revert();
            App.Layouts.Backend.displayNotification(lang('no_privileges_edit_appointments'));
            return;
        }

        if ($notification.is(':visible')) {
            $notification.hide('bind');
        }

        const eventData = info.event.extendedProps.data;

        if (!isUnavailability(eventData)) {
            handleAppointmentDrop(info, eventData);
        } else {
            handleUnavailabilityDrop(info, eventData);
        }
    }

    /**
     * Handle appointment drop operation.
     *
     * @param {Object} info - FullCalendar drop info.
     * @param {Object} eventData - Event data.
     */
    function handleAppointmentDrop(info, eventData) {
        const appointment = prepareAppointmentForSave(eventData);

        appointment.start_datetime = moment(appointment.start_datetime)
            .add({days: info.delta.days, milliseconds: info.delta.milliseconds})
            .format('YYYY-MM-DD HH:mm:ss');
        appointment.end_datetime = moment(appointment.end_datetime)
            .add({days: info.delta.days, milliseconds: info.delta.milliseconds})
            .format('YYYY-MM-DD HH:mm:ss');
        appointment.is_unavailability = 0;
        eventData.start_datetime = appointment.start_datetime;
        eventData.end_datetime = appointment.end_datetime;
        const successCallback = (notifyCustomer) => {
            const undoFunction = () => {
                const delta = {days: -info.delta.days, milliseconds: -info.delta.milliseconds};

                appointment.start_datetime = moment(appointment.start_datetime)
                    .add(delta)
                    .format('YYYY-MM-DD HH:mm:ss');
                appointment.end_datetime = moment(appointment.end_datetime).add(delta).format('YYYY-MM-DD HH:mm:ss');
                eventData.start_datetime = appointment.start_datetime;
                eventData.end_datetime = appointment.end_datetime;
                App.Http.Calendar.saveAppointment(appointment, null, null, null, notifyCustomer).done(() =>
                    $notification.hide('blind'),
                );
                info.revert();
            };
            App.Layouts.Backend.displayNotification(lang('appointment_updated'), [
                {label: lang('undo'), function: undoFunction},
            ]);
            $footer.css('position', 'static');
        };
        showNotifyCustomerDialog(appointment, successCallback, () => info.revert());
    }

    /**
     * Handle unavailability drop operation.
     *
     * @param {Object} info - FullCalendar drop info.
     * @param {Object} eventData - Event data.
     */
    function handleUnavailabilityDrop(info, eventData) {
        const unavailability = {
            id: eventData.id,
            start_datetime: moment(info.event.start).format('YYYY-MM-DD HH:mm:ss'),
            end_datetime: moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'),
            id_users_provider: eventData.id_users_provider,
        };

        const successCallback = () => {
            const undoFunction = () => {
                const delta = {days: -info.delta.days, milliseconds: -info.delta.milliseconds};

                unavailability.start_datetime = moment(unavailability.start_datetime)
                    .add(delta)
                    .format('YYYY-MM-DD HH:mm:ss');
                unavailability.end_datetime = moment(unavailability.end_datetime)
                    .add(delta)
                    .format('YYYY-MM-DD HH:mm:ss');
                unavailability.is_unavailability = 1;
                eventData.start_datetime = unavailability.start_datetime;
                eventData.end_datetime = unavailability.end_datetime;
                App.Http.Calendar.saveUnavailability(unavailability).done(() => $notification.hide('blind'));
                info.revert();
            };
            App.Layouts.Backend.displayNotification(lang('unavailability_updated'), [
                {label: lang('undo'), function: undoFunction},
            ]);
            $footer.css('position', 'static');
        };
        App.Http.Calendar.saveUnavailability(unavailability, successCallback);
    }

    /**
     * Prepare appointment object for saving (remove related data).
     *
     * @param {Object} eventData - Event data object.
     * @returns {Object} Clean appointment object.
     */
    function prepareAppointmentForSave(eventData) {
        const appointment = {...eventData};

        appointment.is_unavailability = Number(appointment.is_unavailability) || 0;
        delete appointment.customer;
        delete appointment.provider;
        delete appointment.service;

        return appointment;
    }

    /**
     * Show dialog asking whether to notify customer.
     *
     * @param {Object} appointment - Appointment data.
     * @param {Function} successCallback - Callback on success.
     * @param {Function} revertCallback - Callback to revert changes.
     */
    function showNotifyCustomerDialog(appointment, successCallback, revertCallback) {
        App.Utils.Message.show(lang('appointment_update'), lang('notify_customer_on_update_question'), [
            {
                text: lang('no'),
                click: (event, messageModal) => {
                    messageModal.hide();
                    App.Http.Calendar.saveAppointmentWithConflictHandling(
                        appointment,
                        null,
                        () => successCallback(false),
                        null,
                        false,
                        revertCallback,
                    );
                },
            },
            {
                text: lang('yes'),
                click: (event, messageModal) => {
                    messageModal.hide();
                    App.Http.Calendar.saveAppointmentWithConflictHandling(
                        appointment,
                        null,
                        () => successCallback(true),
                        null,
                        true,
                        revertCallback,
                    );
                },
            },
        ]);
    }

    /**
     * Handle calendar selection (drag to create).
     *
     * @param {Object} info - FullCalendar selection info.
     * @param {Object} fullCalendar - FullCalendar instance.
     */
    function onSelect(info, fullCalendar) {
        if (info.allDay) {
            return;
        }

        const $providerColumn = $(info.jsEvent.target).parents('.provider-column');
        const providerId = $providerColumn.data('provider').id;

        const buttons = [
            {
                text: lang('unavailability'),
                click: (event, messageModal) => {
                    $('#insert-unavailability').trigger('click');

                    if (providerId) {
                        $('#unavailability-provider').val(providerId);
                    } else {
                        $('#unavailability-provider option:first').prop('selected', true);
                    }

                    $('#unavailability-provider').trigger('change');
                    App.Utils.UI.setDateTimePickerValue($('#unavailability-start'), info.start);
                    App.Utils.UI.setDateTimePickerValue($('#unavailability-end'), info.end);
                    messageModal.hide();
                },
            },
            {
                text: lang('appointment'),
                click: (event, messageModal) => {
                    $('#insert-appointment').trigger('click');
                    const provider = vars('available_providers').find(
                        (provider) => Number(provider.id) === Number(providerId),
                    );

                    const service = vars('available_services').find(
                        (service) => provider.services.indexOf(service.id) !== -1,
                    );

                    if (service) {
                        $selectService.val(service.id);
                    }

                    if (!$selectService.val()) {
                        $selectService.find('option:first').prop('selected', true);
                    }

                    $selectService.trigger('change');
                    if (provider) {
                        $selectProvider.val(provider.id);
                    }

                    if (!$selectProvider.val()) {
                        $('#select-provider option:first').prop('selected', true);
                    }

                    $selectProvider.trigger('change');

                    // Preselect time

                    App.Utils.UI.setDateTimePickerValue($('#start-datetime'), info.start);
                    App.Utils.UI.setDateTimePickerValue(
                        $('#end-datetime'),
                        App.Pages.Calendar.getSelectionEndDate(info),
                    );
                    messageModal.hide();
                },
            },
        ];
        App.Utils.Message.show(lang('add_new_event'), lang('what_kind_of_event'), buttons);
        $('#message-modal .modal-footer')
            .addClass('justify-content-between')
            .find('.btn')
            .css('width', 'calc(50% - 10px)');
        fullCalendar.unselect();

        return false;
    }

    // Calendar Event Source Creation

    /**
     * Create appointment calendar events.
     *
     * @param {jQuery} $providerColumn - Provider column element.
     * @param {Array} appointments - Appointment data array.
     */
    function createAppointments($providerColumn, appointments) {
        if (!appointments.length) {
            return;
        }

        const providerId = $providerColumn.data('provider').id;

        const filterServiceIds = $filterService.val().map((id) => Number(id));

        const calendarEvents = appointments
            .filter((appointment) => {
                if (Number(appointment.id_users_provider) !== Number(providerId)) {
                    return false;
                }

                return !filterServiceIds.length || filterServiceIds.includes(appointment.id_services);
            })
            .map((appointment) => {
                const customerName = [appointment.customer.first_name, appointment.customer.last_name]
                    .filter(Boolean)
                    .join(' ');

                const title = customerName ? appointment.service.name + ' - ' + customerName : appointment.service.name;

                return {
                    id: appointment.id,
                    title: title,
                    start: moment(appointment.start_datetime).toDate(),
                    end: moment(appointment.end_datetime).toDate(),
                    allDay: false,
                    color: appointment.color,
                    display: 'block',
                    data: appointment,
                };
            });
        $providerColumn.find('.calendar-wrapper').data('fullCalendar').addEventSource(calendarEvents);
    }

    /**
     * Create unavailability calendar events.
     *
     * @param {jQuery} $providerColumn - Provider column element.
     * @param {Array} unavailabilities - Unavailability data array.
     */
    function createUnavailabilities($providerColumn, unavailabilities) {
        if (!unavailabilities.length) {
            return;
        }

        const providerId = $providerColumn.data('provider').id;

        const calendarEvents = unavailabilities
            .filter((u) => Number(u.id_users_provider) === Number(providerId))
            .map((unavailability) => ({
                title: lang('unavailability'),
                start: moment(unavailability.start_datetime).toDate(),
                end: moment(unavailability.end_datetime).toDate(),
                allDay: false,
                color: EVENT_COLORS.unavailability,
                display: 'block',
                editable: true,
                className: 'fc-unavailability fc-custom',
                data: unavailability,
            }));

        $providerColumn.find('.calendar-wrapper').data('fullCalendar').addEventSource(calendarEvents);
    }

    /**
     * Create blocked period calendar events.
     *
     * @param {jQuery} $providerColumn - Provider column element.
     * @param {Array} blockedPeriods - Blocked period data array.
     */
    function createBlockedPeriods($providerColumn, blockedPeriods) {
        if (!blockedPeriods?.length) {
            return;
        }

        const calendarEvents = blockedPeriods.map((blockedPeriod) => ({
            title: blockedPeriod.name,
            start: moment(blockedPeriod.start_datetime).toDate(),
            end: moment(blockedPeriod.end_datetime).toDate(),
            allDay: true,
            backgroundColor: EVENT_COLORS.blockedPeriod,
            borderColor: EVENT_COLORS.blockedPeriod,
            textColor: '#ffffff',
            display: 'block',
            editable: false,
            className: 'fc-blocked-period fc-unavailability',
            data: blockedPeriod,
        }));

        $providerColumn.find('.calendar-wrapper').data('fullCalendar').addEventSource(calendarEvents);
    }

    /**
     * Create non-working hours background events.
     *
     * @param {jQuery} $calendar - Calendar wrapper element.
     * @param {Object} provider - Provider data.
     */
    function createNonWorkingHours($calendar, provider) {
        const workingPlan = JSON.parse(provider.settings.working_plan);

        const workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};

        const view = $calendar.data('fullCalendar').view;

        const start = moment(view.currentStart).clone();

        const end = moment(view.currentEnd).clone();

        const selDayName = start.format('dddd').toLowerCase();

        const selDayDate = start.format('YYYY-MM-DD');

        const calendarEventSource = [];

        // Apply working plan exception if exists

        if (workingPlanExceptions[selDayDate]) {
            workingPlan[selDayName] = workingPlanExceptions[selDayDate];
            calendarEventSource.push({
                title: lang('working_plan_exception'),
                start: moment(selDayDate + ' ' + workingPlan[selDayName].start, 'YYYY-MM-DD HH:mm', true).toDate(),
                end: moment(selDayDate + ' ' + workingPlan[selDayName].end, 'YYYY-MM-DD HH:mm', true)
                    .add(1, 'day')
                    .toDate(),
                allDay: true,
                color: EVENT_COLORS.workingPlanException,
                display: 'block',
                editable: false,
                className: 'fc-working-plan-exception fc-custom',
                data: {
                    date: selDayDate,
                    workingPlanException: workingPlanExceptions[selDayDate],
                    provider: provider,
                },
            });
        }

        // Non-working day

        if (workingPlan[selDayName] === null) {
            calendarEventSource.push({
                title: lang('not_working'),
                start: start.toDate(),
                end: end.toDate(),
                allDay: false,
                color: EVENT_COLORS.notWorking,
                editable: false,
                display: 'background',
                className: 'fc-unavailability',
            });
            $calendar.data('fullCalendar').addEventSource(calendarEventSource);
            return;
        }

        // Before work starts

        const workDateStart = moment(start.format('YYYY-MM-DD') + ' ' + workingPlan[selDayName].start);

        if (start < workDateStart) {
            calendarEventSource.push({
                title: lang('not_working'),
                start: start.toDate(),
                end: workDateStart.toDate(),
                allDay: false,
                color: EVENT_COLORS.notWorking,
                editable: false,
                display: 'background',
                className: 'fc-unavailability',
            });
        }

        // After work ends

        const workDateEnd = moment(start.format('YYYY-MM-DD') + ' ' + workingPlan[selDayName].end);

        if (end > workDateEnd) {
            calendarEventSource.push({
                title: lang('not_working'),
                start: workDateEnd.toDate(),
                end: end.toDate(),
                allDay: false,
                color: EVENT_COLORS.notWorking,
                editable: false,
                display: 'background',
                className: 'fc-unavailability',
            });
        }

        // Breaks

        workingPlan[selDayName].breaks.forEach((breakPeriod) => {
            const breakStart = moment(start.format('YYYY-MM-DD') + ' ' + breakPeriod.start);

            const breakEnd = moment(start.format('YYYY-MM-DD') + ' ' + breakPeriod.end);

            calendarEventSource.push({
                title: lang('break'),
                start: breakStart.toDate(),
                end: breakEnd.toDate(),
                allDay: false,
                color: EVENT_COLORS.notWorking,
                editable: false,
                display: 'background',
                className: 'fc-unavailability fc-break',
            });
        });
        $calendar.data('fullCalendar').addEventSource(calendarEventSource);
    }

    /**
     * Create break events in the table view (legacy support).
     *
     * @param {jQuery} $providerColumn - Provider column element.
     * @param {Array} breaks - Break periods array.
     */
    function createBreaks($providerColumn, breaks) {
        if (!breaks.length) {
            return;
        }

        const currentDate = new Date($providerColumn.parents('.date-column').data('date'));

        const $tbody = $providerColumn.find('table tbody');

        breaks.forEach((entry) => {
            const startHour = entry.start.split(':');

            const eventDate = new Date(
                currentDate.getFullYear(),
                currentDate.getMonth(),
                currentDate.getDate(),
                startHour[0],
                startHour[1],
            );

            const endHour = entry.end.split(':');

            const endDate = new Date(
                currentDate.getFullYear(),
                currentDate.getMonth(),
                currentDate.getDate(),
                endHour[0],
                endHour[1],
            );

            const eventDuration = Math.round((endDate - eventDate) / 60000);

            const $event = $('<div/>', {class: 'event unavailability break'});

            $event.html(
                lang('break') +
                    ' <span class="hour">' +
                    moment(eventDate).format('HH:mm') +
                    '</span> (' +
                    eventDuration +
                    "')",
            );
            $event.data(entry);
            $tbody.find('tr').each((index, tr) => {
                const $td = $(tr).find('td:first');

                const cellDate = moment(currentDate)
                    .set({
                        hour: parseInt($td.text().split(':')[0]),
                        minute: parseInt($td.text().split(':')[1]),
                    })
                    .toDate();

                if (eventDate < cellDate) {
                    if (moment(eventDate).format('HH:mm') === $(tr).prev().find('td').eq(0).text()) {
                        $event.find('.hour').remove();
                    }

                    $(tr)
                        .prev()
                        .find('td:gt(0)')
                        .each((index, td) => {
                            $event.clone().appendTo($(td));
                        });
                    return false;
                }
            });
        });
    }

    // View Construction

    /**
     * Create table view header container.
     */
    function createHeader() {
        $calendarFilter
            .find('select')
            .empty()
            .append(new Option('1 ' + lang('day'), '1'))
            .append(new Option('3 ' + lang('days'), '3'));
        const $calendarHeader = $('<div/>', {class: 'calendar-header'}).appendTo('#calendar');

        // Previous button

        $('<button/>', {
            class: 'btn btn-xs btn-outline-secondary previous me-2',
            html: [$('<span/>', {class: 'fas fa-chevron-left'})],
        }).appendTo($calendarHeader);

        // Date picker

        $selectDate = $('<input/>', {
            type: 'text',
            class: 'form-control d-inline-block select-date me-2',
            value: App.Utils.Date.format(new Date(), vars('date_format'), vars('time_format'), false),
        }).appendTo($calendarHeader);

        // Next button

        $('<button/>', {
            class: 'btn btn-xs btn-outline-secondary next',
            html: [$('<span/>', {class: 'fas fa-chevron-right'})],
        }).appendTo($calendarHeader);
        App.Utils.UI.initializeDatePicker($calendarHeader.find('.select-date'), {
            onChange(selectedDates) {
                const startDate = selectedDates[0];

                const endDate = moment(startDate)
                    .add(parseInt($selectFilterItem.val()) - 1, 'days')
                    .toDate();

                createView(startDate, endDate);
            },
        });
        $calendarHeader.find('.flatpickr-wrapper').addClass('w-auto');
        const providers = getAvailableProviders();

        // Provider filter

        $('<label/>', {text: lang('provider')}).appendTo($calendarHeader);
        $filterProvider = $('<select/>', {
            id: 'filter-provider',
            multiple: 'multiple',
            on: {
                change: () => {
                    const firstColumnDate = $('.calendar-view .date-column:first').data('date');

                    const startDateMoment = moment(firstColumnDate);

                    const endDateMoment = moment(firstColumnDate).add(parseInt($selectFilterItem.val()) - 1, 'day');

                    createView(startDateMoment.toDate(), endDateMoment.toDate());
                },
            },
        }).appendTo($calendarHeader);

        if (vars('role_slug') !== App.Layouts.Backend.DB_SLUG_PROVIDER) {
            providers.forEach((provider) => {
                $filterProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
            });
        } else {
            providers.forEach((provider) => {
                if (Number(provider.id) === Number(vars('user_id'))) {
                    $filterProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
                }
            });
        }

        App.Utils.UI.initializeDropdown($filterProvider);

        // Service filter

        const services = vars('available_services').filter((service) => {
            const provider = providers.find((p) => p.services.indexOf(service.id) !== -1);

            return vars('role_slug') === App.Layouts.Backend.DB_SLUG_ADMIN || provider;
        });
        $('<label/>', {text: lang('service')}).appendTo($calendarHeader);
        $filterService = $('<select/>', {
            id: 'filter-service',
            multiple: 'multiple',
            on: {
                change: () => {
                    const firstColumnDate = $('.calendar-view .date-column:first').data('date');

                    const startDateMoment = moment(firstColumnDate);

                    const endDateMoment = moment(firstColumnDate).add({days: parseInt($selectFilterItem.val()) - 1});

                    createView(startDateMoment.toDate(), endDateMoment.toDate());
                },
            },
        }).appendTo($calendarHeader);
        services.forEach((service) => {
            $filterService.append(new Option(service.name, service.id));
        });
        App.Utils.UI.initializeDropdown($filterService);
    }

    /**
     * Create table schedule view.
     *
     * @param {Date} startDate - Start date to be displayed.
     * @param {Date} endDate - End date to be displayed.
     */
    function createView(startDate, endDate) {
        // Disable date navigation

        $('#calendar .calendar-header .btn').addClass('disabled').prop('disabled', true);

        // Remember provider calendar view mode

        const providerView = {};

        $('.provider-column').each((index, providerColumn) => {
            const $providerColumn = $(providerColumn);

            const providerId = $providerColumn.data('provider').id;

            providerView[providerId] = $providerColumn.find('.calendar-wrapper').data('fullCalendar').view.type;
        });
        $('#calendar .calendar-view').remove();
        const $calendarView = $('<div/>', {class: 'calendar-view'}).appendTo('#calendar');

        $calendarView.data({
            startDate: moment(startDate).format('YYYY-MM-DD'),
            endDate: moment(endDate).format('YYYY-MM-DD'),
        });
        const $wrapper = $('<div/>').appendTo($calendarView);

        App.Http.Calendar.getCalendarAppointmentsForTableView(startDate, endDate).done((response) => {
            let currentDate = startDate;
            while (currentDate <= endDate) {
                createDateColumn($wrapper, currentDate, response);
                currentDate = moment(currentDate).add({days: 1}).toDate();
            }

            setCalendarViewSize();

            // Activate calendar navigation

            $('#calendar .calendar-header .btn').removeClass('disabled').prop('disabled', false);

            // Apply provider calendar view mode

            $('.provider-column').each((index, providerColumn) => {
                const $providerColumn = $(providerColumn);

                const providerId = $providerColumn.data('provider').id;

                $providerColumn
                    .find('.calendar-wrapper')
                    .data('fullCalendar')
                    .changeView(providerView[providerId] || 'timeGridDay');
            });
        });
    }

    /**
     * Create date column container.
     *
     * @param {jQuery} $wrapper - Wrapper element.
     * @param {Date} date - Selected date for the column.
     * @param {Object} events - Events to be displayed.
     */
    function createDateColumn($wrapper, date, events) {
        const $dateColumn = $('<div/>', {class: 'date-column'}).appendTo($wrapper);

        $dateColumn.data('date', date.getTime());
        $('<h5/>', {
            class: 'date-column-title',
            text: App.Utils.Date.format(date, vars('date_format'), vars('time_format')),
        }).appendTo($dateColumn);
        const filterProviderIds = $filterProvider.val().map((id) => Number(id));

        const filterServiceIds = $filterService.val().map((id) => Number(id));

        let providers = vars('available_providers').filter((provider) => {
            const servedServiceIds = provider.services.filter((serviceId) => {
                return filterServiceIds.some((filterId) => Number(serviceId) === Number(filterId));
            });
            return (
                (!filterProviderIds.length && !filterServiceIds.length) ||
                (filterProviderIds.length && !filterServiceIds.length && filterProviderIds.includes(provider.id)) ||
                (!filterProviderIds.length && filterServiceIds.length && servedServiceIds.length) ||
                (filterProviderIds.length &&
                    filterServiceIds.length &&
                    servedServiceIds.length &&
                    filterProviderIds.includes(provider.id))
            );
        });
        // Role-based filtering

        if (vars('role_slug') === 'provider') {
            providers = vars('available_providers').filter((p) => Number(p.id) === Number(vars('user_id')));
        }

        if (vars('role_slug') === 'secretary') {
            providers = vars('available_providers').filter((p) => vars('secretary_providers').indexOf(p.id) > -1);
        }

        providers.forEach((provider) => {
            createProviderColumn($dateColumn, date, provider, events);
        });
    }

    /**
     * Create provider column container.
     *
     * @param {jQuery} $dateColumn - Date column element.
     * @param {Date} date - Selected date.
     * @param {Object} provider - Provider data.
     * @param {Object} events - Events data.
     */
    function createProviderColumn($dateColumn, date, provider, events) {
        if (provider.services.length === 0) {
            return;
        }

        const $providerColumn = $('<div/>', {class: 'provider-column'}).appendTo($dateColumn);

        $providerColumn.data('provider', provider);
        createCalendar($providerColumn, date, provider);
        createNonWorkingHours($providerColumn.find('.calendar-wrapper'), provider);
        createAppointments($providerColumn, events.appointments);
        createUnavailabilities($providerColumn, events.unavailabilities);
        createBlockedPeriods($providerColumn, events.blocked_periods);
    }

    /**
     * Create FullCalendar instance for a provider column.
     *
     * @param {jQuery} $providerColumn - Provider column element.
     * @param {Date} goToDate - Date to navigate to.
     * @param {Object} provider - Provider data.
     */
    function createCalendar($providerColumn, goToDate, provider) {
        const $wrapper = $('<div/>', {class: 'calendar-wrapper'}).appendTo($providerColumn);

        const {columnFormat, timeFormat, slotTimeFormat} = getFormatSettings();
        const firstWeekdayNumber = App.Utils.Date.getWeekdayId(vars('first_weekday'));

        const fullCalendar = new FullCalendar.Calendar($wrapper[0], {
            locale: vars('language_code'),
            initialView: 'timeGridDay',
            nowIndicator: true,
            height: getCalendarHeight(),
            editable: true,
            firstDay: firstWeekdayNumber,
            slotDuration: '00:15:00',
            snapDuration: '00:15:00',
            scrollTime: '07:00:00',
            slotLabelInterval: '01:00',
            eventTimeFormat: timeFormat,
            eventTextColor: '#333',
            eventColor: EVENT_COLORS.default,
            slotLabelFormat: slotTimeFormat,
            allDayContent: lang('all_day'),
            dayHeaderFormat: columnFormat,
            selectable: true,
            selectHelper: true,
            themeSystem: 'bootstrap5',
            selectLongPressDelay: 100,
            headerToolbar: {
                left: 'listDay,timeGridDay',
                center: '',
                right: '',
            },
            buttonText: {
                today: lang('today'),
                day: lang('day'),
                week: lang('week'),
                month: lang('month'),
                timeGridDay: lang('calendar'),
                listDay: lang('list'),
            },
            eventClick: onEventClick,
            eventResize: onEventResize,
            eventDrop: onEventDrop,
            select: (info) => onSelect(info, fullCalendar),
        });

        fullCalendar.render();
        $wrapper.data('fullCalendar', fullCalendar);
        fullCalendar.gotoDate(goToDate);
        $('<h6/>', {text: provider.first_name + ' ' + provider.last_name}).prependTo($providerColumn);
    }

    /**
     * Set calendar view size to fit the page.
     */
    function setCalendarViewSize() {
        let height =
            window.innerHeight -
            $header.outerHeight() -
            $footer.outerHeight() -
            $calendarToolbar.outerHeight() -
            $('.calendar-header').outerHeight() -
            50;

        if (height < 500) {
            height = 500;
        }

        const $dateColumn = $('.date-column');

        const $calendarViewDiv = $('.calendar-view > div');

        $calendarViewDiv.css('min-width', '1000%');
        let width = 0;
        $dateColumn.each((index, dateColumn) => {
            width += $(dateColumn).outerWidth();
        });
        $calendarViewDiv.css('min-width', width + 200);
        const dateColumnHeight = $dateColumn.outerHeight();

        $('.calendar-view .not-working').outerHeight((dateColumnHeight > height ? dateColumnHeight : height) - 70);
    }

    // Event Listeners

    /**
     * Add all page event listeners.
     */
    function addEventListeners() {
        // Previous day button

        $calendar.on('click', '.calendar-header .btn.previous', () => {
            const dayInterval = $selectFilterItem.val();

            const currentDate = App.Utils.UI.getDateTimePickerValue($selectDate);

            const startDate = moment(currentDate).subtract(1, 'days');

            const endDate = startDate.clone().add(dayInterval - 1, 'days');

            App.Utils.UI.setDateTimePickerValue($selectDate, startDate.toDate());
            createView(startDate.toDate(), endDate.toDate());
        });

        // Next day button

        $calendar.on('click', '.calendar-header .btn.next', () => {
            const dayInterval = $selectFilterItem.val();

            const currentDate = App.Utils.UI.getDateTimePickerValue($selectDate);

            const startDate = moment(currentDate).add(1, 'days');

            const endDate = startDate.clone().add(dayInterval - 1, 'days');

            App.Utils.UI.setDateTimePickerValue($selectDate, startDate.toDate());
            createView(startDate.toDate(), endDate.toDate());
        });

        // Day interval change

        $calendarToolbar.on('change', '#select-filter-item', () => {
            const dayInterval = $selectFilterItem.val();

            const currentDate = App.Utils.UI.getDateTimePickerValue($selectDate);

            const startDate = moment(currentDate);

            const endDate = startDate.clone().add(dayInterval - 1, 'days');

            createView(startDate.toDate(), endDate.toDate());
        });

        // Reload appointments button

        $calendarToolbar.on('click', '#reload-appointments', () => {
            const dayInterval = $selectFilterItem.val();

            const currentDate = App.Utils.UI.getDateTimePickerValue($selectDate);

            const startDate = moment(currentDate).toDate();

            const endDate = moment(currentDate)
                .add(dayInterval - 1, 'days')
                .toDate();

            App.Http.Calendar.getCalendarAppointmentsForTableView(startDate, endDate).done((response) => {
                let currentDateIter = startDate;
                while (currentDateIter <= endDate) {
                    $('.calendar-view .date-column').each((index, dateColumn) => {
                        const $dateColumn = $(dateColumn);

                        const date = new Date($dateColumn.data('date'));

                        if (moment(currentDateIter).format('YYYY-MM-DD') !== moment(date).format('YYYY-MM-DD')) {
                            return true;
                        }

                        $dateColumn
                            .find('.date-column-title')
                            .text(App.Utils.Date.format(date, vars('date_format'), vars('time_format')));
                        $dateColumn.find('.provider-column').each((index, providerColumn) => {
                            const $providerColumn = $(providerColumn);

                            const provider = $providerColumn.data('provider');

                            $providerColumn
                                .find('.calendar-wrapper')
                                .data('fullCalendar')
                                .getEventSources()
                                .forEach((eventSource) => eventSource.remove());
                            createNonWorkingHours($providerColumn.find('.calendar-wrapper'), provider);
                            createAppointments($providerColumn, response.appointments);
                            createUnavailabilities($providerColumn, response.unavailabilities);
                            createBlockedPeriods($providerColumn, response.blocked_periods);

                            // Add provider breaks

                            const workingPlan = JSON.parse(provider.settings.working_plan);

                            const day = moment(date).format('dddd').toLowerCase();

                            if (workingPlan[day]) {
                                createBreaks($providerColumn, workingPlan[day].breaks);
                            }
                        });
                    });
                    currentDateIter = moment(currentDateIter).add({days: 1}).toDate();
                }
            });
        });

        // Window resize

        $(window).on('resize', () => {
            setCalendarViewSize();
        });

        // Popover close button

        $calendar.on('click', '.close-popover', closePopover);

        // Popover edit button

        $calendar.on('click', '.edit-popover', onEditPopoverClick);

        // Popover delete button

        $calendar.on('click', '.delete-popover', onDeletePopoverClick);
    }

    // Initialization

    /**
     * Initialize the calendar page.
     */
    function initialize() {
        createHeader();
        const startDate = moment().toDate();

        const endDate = moment()
            .add(Number($selectFilterItem.val()) - 1, 'days')
            .toDate();

        createView(startDate, endDate);
        $('#insert-working-plan-exception').hide();
        addEventListeners();
        // Hide Google Calendar Sync buttons (not supported in table view)
        $('#enable-sync, #google-sync').hide();

        // Load modified appointment if provided

        if (vars('edit_appointment')) {
            const appointment = vars('edit_appointment');

            const startDateTimeObject = moment(appointment.start_datetime)
                .set({hour: 0, minutes: 0, seconds: 0})
                .toDate();

            const endDateTimeObject = moment(appointment.end_datetime)
                .set({hour: 23, minutes: 59, seconds: 59})
                .toDate();

            App.Utils.UI.setDateTimePickerValue($selectDate, startDateTimeObject);
            createView(startDateTimeObject, endDateTimeObject);
            populateAppointmentModal(appointment);
        }
    }

    // Public API

    return {
        initialize,
    };
})();
