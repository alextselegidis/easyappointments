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
 * Calendar Default View Utility
 *
 * This module implements the default calendar view functionality, handling:
 * - Calendar initialization and rendering
 * - Appointment/unavailability CRUD operations via popovers
 * - Event drag, drop, and resize
 * - Working plan exceptions
 * - Calendar filtering by provider or service
 */
App.Utils.CalendarDefaultView = (function () {
    // =============================================================================
    // Constants
    // =============================================================================

    const FILTER_TYPE_ALL = 'all';
    const FILTER_TYPE_PROVIDER = 'provider';
    const FILTER_TYPE_SERVICE = 'service';

    const EVENT_COLORS = {
        unavailability: '#879DB4',
        blockedPeriod: '#d65069',
        notWorking: '#BEBEBE',
        default: '#7cbae8',
    };

    // =============================================================================
    // DOM Elements Cache
    // =============================================================================

    const $calendarPage = $('#calendar-page');
    const $reloadAppointments = $('#reload-appointments');
    const $calendar = $('#calendar');
    const $selectFilterItem = $('#select-filter-item');
    const $appointmentsModal = $('#appointments-modal');
    const $unavailabilitiesModal = $('#unavailabilities-modal');
    const $header = $('#header');
    const $footer = $('#footer');
    const $notification = $('#notification');
    const $calendarToolbar = $('#calendar-toolbar');

    // =============================================================================
    // Module State
    // =============================================================================

    const moment = window.moment;
    let $popoverTarget = null;
    let fullCalendar = null;
    let lastFocusedEventData = null;

    // =============================================================================
    // Helper Functions
    // =============================================================================

    /**
     * Get the calendar height based on window size.
     *
     * @returns {number} Calendar height in pixels (minimum 780px).
     */
    function getCalendarHeight() {
        const offset = $footer.outerHeight() + $header.outerHeight() + $calendarToolbar.outerHeight() + 60;
        const height = window.innerHeight - offset;
        return Math.max(height, 780);
    }

    /**
     * Format a datetime string for display.
     *
     * @param {Date|string} datetime - The datetime to format.
     * @returns {string} Formatted datetime string.
     */
    function formatDateTime(datetime) {
        return App.Utils.Date.format(
            moment(datetime).format('YYYY-MM-DD HH:mm:ss'),
            vars('date_format'),
            vars('time_format'),
            true,
        );
    }

    /**
     * Get truncated notes for popover display.
     *
     * @param {Object} event - Calendar event object.
     * @returns {string} Notes text (truncated to 100 chars) or '-'.
     */
    function getEventNotes(event) {
        const notes = event.extendedProps?.data?.notes;
        if (!notes) return '-';
        return notes.length > 100 ? notes.substring(0, 100) + '...' : notes;
    }

    /**
     * Get the selected filter type from the dropdown.
     *
     * @returns {string} Filter type constant.
     */
    function getSelectedFilterType() {
        return $selectFilterItem.find('option:selected').attr('type');
    }

    /**
     * Check if the selected filter is a provider.
     *
     * @returns {boolean}
     */
    function isProviderFilter() {
        return getSelectedFilterType() === FILTER_TYPE_PROVIDER;
    }

    /**
     * Find a provider by ID from available providers.
     *
     * @param {number} providerId - Provider ID to find.
     * @returns {Object|undefined} Provider object or undefined.
     */
    function findProvider(providerId) {
        return vars('available_providers').find((provider) => Number(provider.id) === Number(providerId));
    }

    /**
     * Find a service by ID from available services.
     *
     * @param {number} serviceId - Service ID to find.
     * @returns {Object|undefined} Service object or undefined.
     */
    function findService(serviceId) {
        return vars('available_services').find((service) => Number(service.id) === Number(serviceId));
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

    // =============================================================================
    // Popover UI Builders
    // =============================================================================

    /**
     * Create a popover button element.
     *
     * @param {string} className - CSS class names.
     * @param {string} iconClass - Font Awesome icon class.
     * @param {string} labelKey - Language key for button text.
     * @returns {jQuery} Button element.
     */
    function createPopoverButton(className, iconClass, labelKey) {
        return $('<button/>', {
            'class': className,
            'html': [$('<i/>', {'class': `${iconClass} me-2`}), $('<span/>', {'text': lang(labelKey)})],
        });
    }

    /**
     * Create the standard popover action buttons.
     *
     * @param {string} displayEdit - CSS class to show/hide edit button.
     * @param {string} displayDelete - CSS class to show/hide delete button.
     * @returns {jQuery} Button container element.
     */
    function createPopoverButtons(displayEdit, displayDelete) {
        return $('<div/>', {
            'class': 'd-flex justify-content-center',
            'html': [
                createPopoverButton('close-popover btn btn-outline-secondary me-2', 'fas fa-ban', 'close'),
                createPopoverButton(
                    `delete-popover btn btn-outline-secondary ${displayDelete}`,
                    'fas fa-trash-alt',
                    'delete',
                ),
                createPopoverButton(`edit-popover btn btn-primary ${displayEdit}`, 'fas fa-edit', 'edit'),
            ],
        });
    }

    /**
     * Create a labeled text row for popover content.
     *
     * @param {string} labelKey - Language key for label.
     * @param {string} text - Text content.
     * @returns {Array<jQuery>} Array of jQuery elements.
     */
    function createPopoverRow(labelKey, text) {
        return [
            $('<strong/>', {'class': 'd-inline-block me-2', 'text': lang(labelKey)}),
            $('<span/>', {'text': text}),
            $('<br/>'),
        ];
    }

    /**
     * Build popover content for unavailability events.
     *
     * @param {Object} info - FullCalendar event info.
     * @param {string} displayEdit - CSS class for edit visibility.
     * @param {string} displayDelete - CSS class for delete visibility.
     * @returns {jQuery} Popover content element.
     */
    function buildUnavailabilityPopover(info, displayEdit, displayDelete) {
        const data = info.event.extendedProps.data;
        const provider = data.provider;

        let startDateTime = info.event.start;
        let endDateTime = info.event.end || info.event.start;

        if (data.start_datetime) {
            startDateTime = new Date(data.start_datetime);
            endDateTime = new Date(data.end_datetime);
        }

        return $('<div/>', {
            'html': [
                ...createPopoverRow('provider', `${provider.first_name} ${provider.last_name}`),
                ...createPopoverRow('start', formatDateTime(startDateTime)),
                ...createPopoverRow('end', formatDateTime(endDateTime)),
                ...createPopoverRow('notes', getEventNotes(info.event)),
                App.Utils.CalendarEventPopover.renderCustomContent(info),
                $('<hr/>'),
                createPopoverButtons(displayEdit, displayDelete),
            ],
        });
    }

    /**
     * Build popover content for working plan exception events.
     *
     * @param {Object} info - FullCalendar event info.
     * @param {string} displayDelete - CSS class for delete visibility.
     * @returns {jQuery} Popover content element.
     */
    function buildWorkingPlanExceptionPopover(info, displayDelete) {
        const {date, workingPlanException, provider} = info.event.extendedProps.data;
        const startTime = workingPlanException?.start;
        const endTime = workingPlanException?.end;

        const formatTimeOrDash = (time) => {
            if (!time) return '-';
            return App.Utils.Date.format(`${date} ${time}`, vars('date_format'), vars('time_format'), true);
        };

        return $('<div/>', {
            'html': [
                ...createPopoverRow('provider', `${provider.first_name} ${provider.last_name}`),
                ...createPopoverRow('start', formatTimeOrDash(startTime)),
                ...createPopoverRow('end', formatTimeOrDash(endTime)),
                ...createPopoverRow('timezone', startTime ? vars('timezones')[provider.timezone] : '-'),
                App.Utils.CalendarEventPopover.renderCustomContent(info),
                $('<hr/>'),
                $('<div/>', {
                    'class': 'd-flex justify-content-between',
                    'html': [
                        createPopoverButton('close-popover btn btn-outline-secondary me-2', 'fas fa-ban', 'close'),
                        createPopoverButton(
                            `delete-popover btn btn-outline-secondary ${displayDelete}`,
                            'fas fa-trash-alt',
                            'delete',
                        ),
                        createPopoverButton('edit-popover btn btn-primary', 'fas fa-edit', 'edit'),
                    ],
                }),
            ],
        });
    }

    /**
     * Build popover content for appointment events.
     *
     * @param {Object} info - FullCalendar event info.
     * @param {string} displayEdit - CSS class for edit visibility.
     * @param {string} displayDelete - CSS class for delete visibility.
     * @returns {jQuery} Popover content element.
     */
    function buildAppointmentPopover(info, displayEdit, displayDelete) {
        const data = info.event.extendedProps.data;
        const customer = data.customer;
        const provider = data.provider;

        const customerName = [customer.first_name, customer.last_name].filter(Boolean).join(' ') || '-';

        const meetingLinkElements = data.meeting_link
            ? [
                  $('<strong/>', {'class': 'd-inline-block me-2', 'text': lang('meeting_link')}),
                  $('<a/>', {'href': data.meeting_link, 'target': '_blank', 'text': data.meeting_link}),
                  $('<br/>'),
              ]
            : [];

        return $('<div/>', {
            'html': [
                ...createPopoverRow('start', formatDateTime(info.event.start)),
                ...createPopoverRow('end', formatDateTime(info.event.end)),
                ...createPopoverRow('timezone', vars('timezones')[provider.timezone]),
                ...createPopoverRow('status', data.status || '-'),
                ...createPopoverRow('service', data.service.name),
                $('<strong/>', {'class': 'd-inline-block me-2', 'text': lang('provider')}),
                App.Utils.CalendarEventPopover.renderMapIcon(provider),
                $('<span/>', {'text': `${provider.first_name} ${provider.last_name}`}),
                $('<br/>'),
                $('<strong/>', {'class': 'd-inline-block me-2', 'text': lang('customer')}),
                App.Utils.CalendarEventPopover.renderMapIcon(customer),
                $('<span/>', {'class': 'd-inline-block', 'text': customerName}),
                $('<br/>'),
                $('<strong/>', {'class': 'd-inline-block me-2', 'text': lang('email')}),
                App.Utils.CalendarEventPopover.renderMailIcon(customer.email),
                $('<span/>', {'class': 'd-inline-block', 'text': customer.email || '-'}),
                $('<br/>'),
                $('<strong/>', {'class': 'd-inline-block me-2', 'text': lang('phone')}),
                App.Utils.CalendarEventPopover.renderPhoneIcon(customer.phone_number),
                $('<span/>', {'class': 'd-inline-block', 'text': customer.phone_number || '-'}),
                $('<br/>'),
                ...meetingLinkElements,
                ...createPopoverRow('notes', getEventNotes(info.event)),
                App.Utils.CalendarEventPopover.renderCustomContent(info),
                $('<hr/>'),
                createPopoverButtons(displayEdit, displayDelete),
            ],
        });
    }

    /**
     * Build popover content for blocked period events.
     *
     * @param {Object} info - FullCalendar event info.
     * @returns {jQuery} Popover content element.
     */
    function buildBlockedPeriodPopover(info) {
        const data = info.event.extendedProps.data;

        return $('<div/>', {
            html: [
                ...createPopoverRow('name', data.name || '-'),
                ...createPopoverRow('start', formatDateTime(info.event.start)),
                ...createPopoverRow('end', formatDateTime(info.event.end)),
                ...createPopoverRow('notes', data.notes || '-'),
                $('<hr/>'),
                $('<div/>', {
                    class: 'd-flex justify-content-center',
                    html: [createPopoverButton('close-popover btn btn-outline-secondary', 'fas fa-ban', 'close')],
                }),
            ],
        });
    }

    // =============================================================================
    // Appointment Modal Helpers
    // =============================================================================

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

    // =============================================================================
    // Working Plan Exception Handlers
    // =============================================================================

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
        const {date: originalDate, workingPlanException, provider} = data;

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
     */
    function handleDeleteWorkingPlanException() {
        const providerId = $selectFilterItem.val();
        const provider = findProvider(providerId);

        if (!provider) {
            throw new Error('Provider could not be found: ' + providerId);
        }

        const date = moment(lastFocusedEventData.start).format('YYYY-MM-DD');

        const successCallback = () => {
            App.Layouts.Backend.displayNotification(lang('working_plan_exception_deleted'));

            const exceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};
            delete exceptions[date];

            updateProviderWorkingPlanExceptions(providerId, exceptions);
            $reloadAppointments.trigger('click');
        };

        App.Http.Calendar.deleteWorkingPlanException(date, providerId, successCallback);
    }

    // =============================================================================
    // Event Handlers - Popover Actions
    // =============================================================================

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

        if (isWorkingPlanException(data)) {
            handleDeleteWorkingPlanException();
        } else if (!isUnavailability(data)) {
            handleDeleteAppointment(data.id);
        } else {
            App.Http.Calendar.deleteUnavailability(data.id).done(() => {
                $reloadAppointments.trigger('click');
            });
        }
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
            'class': 'form-control w-100',
            'id': 'cancellation-reason',
            'rows': '3',
        }).appendTo('#message-modal .modal-body');
    }

    // =============================================================================
    // Calendar Event Callbacks
    // =============================================================================

    /**
     * Handle calendar event click - show popover.
     *
     * @param {Object} info - FullCalendar event info.
     */
    function onEventClick(info) {
        const $target = $(info.el);
        closePopover();

        let $html;
        let displayEdit;
        let displayDelete;

        if ($target.hasClass('fc-blocked-period')) {
            // Blocked periods are read-only, only show close button
            $html = buildBlockedPeriodPopover(info);
        } else if ($target.hasClass('fc-working-plan-exception')) {
            displayDelete = $target.hasClass('fc-custom') && vars('privileges').appointments.delete ? 'me-2' : 'd-none';
            $html = buildWorkingPlanExceptionPopover(info, displayDelete);
        } else if ($target.hasClass('fc-unavailability')) {
            const isCustom = $target.hasClass('fc-custom');
            displayEdit = isCustom && vars('privileges').appointments.edit ? '' : 'd-none';
            displayDelete = isCustom && vars('privileges').appointments.delete ? 'me-2' : 'd-none';
            $html = buildUnavailabilityPopover(info, displayEdit, displayDelete);
        } else {
            displayEdit = vars('privileges').appointments.edit ? '' : 'd-none';
            displayDelete = vars('privileges').appointments.delete ? 'me-2' : 'd-none';
            $html = buildAppointmentPopover(info, displayEdit, displayDelete);
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
        const $popover = $calendarPage.find('.popover');
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
        appointment.is_unavailability = Number(appointment.is_unavailability);
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
     * Handle calendar date click.
     *
     * @param {Object} info - FullCalendar click info.
     */
    function onDateClick(info) {
        if (info.allDay) {
            fullCalendar.changeView('timeGridDay');
            fullCalendar.gotoDate(info.date);
        }
    }

    /**
     * Handle calendar selection (drag to create).
     *
     * @param {Object} info - FullCalendar selection info.
     */
    function onSelect(info) {
        if (info.allDay) return;

        const buttons = [
            {
                text: lang('unavailability'),
                click: (event, messageModal) => {
                    $('#insert-unavailability').trigger('click');

                    const $providerSelect = $('#unavailability-provider');
                    if (isProviderFilter()) {
                        $providerSelect.val($selectFilterItem.val());
                    } else {
                        $providerSelect.find('option:first').prop('selected', true);
                    }
                    $providerSelect.trigger('change');

                    App.Utils.UI.setDateTimePickerValue($('#unavailability-start'), info.start);
                    App.Utils.UI.setDateTimePickerValue($('#unavailability-end'), info.end);

                    messageModal.hide();
                },
            },
            {
                text: lang('appointment'),
                click: (event, messageModal) => {
                    $('#insert-appointment').trigger('click');
                    preselectServiceAndProvider();

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

    /**
     * Preselect service and provider based on current filter.
     */
    function preselectServiceAndProvider() {
        const $serviceSelect = $appointmentsModal.find('#select-service');
        const $providerSelect = $appointmentsModal.find('#select-provider');

        if (isProviderFilter()) {
            const provider = findProvider($selectFilterItem.val());
            if (provider) {
                const service = vars('available_services').find((s) => provider.services.indexOf(s.id) !== -1);
                if (service) {
                    $serviceSelect.val(service.id);
                }
            }

            if (!$serviceSelect.val()) {
                $serviceSelect.find('option:first').prop('selected', true);
            }
            $serviceSelect.trigger('change');

            if (provider) {
                $providerSelect.val(provider.id);
            }
            if (!$providerSelect.val()) {
                $providerSelect.find('option:first').prop('selected', true);
            }
            $providerSelect.trigger('change');
        } else {
            const service = findService($selectFilterItem.val());
            if (service) {
                $serviceSelect.val(service.id).trigger('change');
            }
        }
    }

    /**
     * Handle calendar window resize.
     */
    function onWindowResize() {
        fullCalendar.setOption('height', getCalendarHeight());
    }

    /**
     * Handle calendar dates change (view render).
     */
    function onDatesSet() {
        if (!$selectFilterItem.val()) return;

        refreshCalendarAppointments(
            $calendar,
            $selectFilterItem.val(),
            getSelectedFilterType(),
            fullCalendar.view.activeStart,
            fullCalendar.view.activeEnd,
        );

        $(window).trigger('resize');

        // Close all open popovers
        $('.close-popover').each(() => closePopover());

        // Initialize new popovers
        $('.fv-events').each((index, el) => $(el).popover());
    }

    // =============================================================================
    // Calendar Event Source Management
    // =============================================================================

    /**
     * Refresh calendar appointments from server.
     *
     * @param {jQuery} $calendar - Calendar element.
     * @param {string} recordId - Selected filter ID.
     * @param {string} filterType - Filter type constant.
     * @param {Date} startDate - View start date.
     * @param {Date} endDate - View end date.
     */
    function refreshCalendarAppointments($calendar, recordId, filterType, startDate, endDate) {
        $('#loading').css('visibility', 'hidden');

        const formattedStartDate = moment(startDate).format('YYYY-MM-DD');
        const formattedEndDate = moment(endDate).format('YYYY-MM-DD');

        App.Http.Calendar.getCalendarAppointments(recordId, formattedStartDate, formattedEndDate, filterType)
            .done((response) => {
                // Clear existing events
                fullCalendar.getEventSources().forEach((source) => source.remove());

                const events = [];

                // Add appointments
                events.push(...createAppointmentEvents(response.appointments));

                // Add unavailabilities
                events.push(...createUnavailabilityEvents(response.unavailabilities));

                // Add blocked periods
                events.push(...createBlockedPeriodEvents(response.blocked_periods));

                // Add working plan events (only for day/week views)
                if (fullCalendar.view.type !== 'dayGridMonth') {
                    events.push(...createWorkingPlanEvents(recordId));
                }

                fullCalendar.addEventSource(events);
            })
            .always(() => {
                $('#loading').css('visibility', '');
            });
    }

    /**
     * Create appointment calendar events.
     *
     * @param {Array} appointments - Appointment data array.
     * @returns {Array} Calendar event objects.
     */
    function createAppointmentEvents(appointments) {
        return appointments.map((appointment) => {
            const customerName = [appointment.customer.first_name, appointment.customer.last_name]
                .filter(Boolean)
                .join(' ');

            const title = customerName ? `${appointment.service.name} - ${customerName}` : appointment.service.name;

            return {
                id: appointment.id,
                title,
                start: moment(appointment.start_datetime).toDate(),
                end: moment(appointment.end_datetime).toDate(),
                allDay: false,
                color: appointment.color,
                data: appointment,
                display: 'block',
            };
        });
    }

    /**
     * Create unavailability calendar events.
     *
     * @param {Array} unavailabilities - Unavailability data array.
     * @returns {Array} Calendar event objects.
     */
    function createUnavailabilityEvents(unavailabilities) {
        return unavailabilities.map((unavailability) => {
            let notes = unavailability.notes ? ` - ${unavailability.notes}` : '';
            if (notes.length > 33) {
                notes = ` - ${unavailability.notes.substring(0, 30)}...`;
            }

            return {
                title: lang('unavailability') + notes,
                start: moment(unavailability.start_datetime).toDate(),
                end: moment(unavailability.end_datetime).toDate(),
                allDay: false,
                color: EVENT_COLORS.unavailability,
                editable: true,
                className: 'fc-unavailability fc-custom',
                data: unavailability,
                display: 'block',
            };
        });
    }

    /**
     * Create blocked period calendar events.
     *
     * @param {Array} blockedPeriods - Blocked period data array.
     * @returns {Array} Calendar event objects.
     */
    function createBlockedPeriodEvents(blockedPeriods) {
        if (!blockedPeriods) return [];

        return blockedPeriods.map((blockedPeriod) => ({
            title: blockedPeriod.name,
            start: moment(blockedPeriod.start_datetime).toDate(),
            end: moment(blockedPeriod.end_datetime).toDate(),
            allDay: true,
            backgroundColor: EVENT_COLORS.blockedPeriod,
            borderColor: EVENT_COLORS.blockedPeriod,
            textColor: '#ffffff',
            editable: false,
            className: 'fc-blocked-period fc-unavailability',
            data: blockedPeriod,
            display: 'block',
        }));
    }

    /**
     * Create working plan related events (exceptions, breaks, non-working periods).
     *
     * @param {string} recordId - Provider ID.
     * @returns {Array} Calendar event objects.
     */
    function createWorkingPlanEvents(recordId) {
        const events = [];
        const provider = findProvider(recordId);

        const workingPlan = JSON.parse(provider?.settings?.working_plan || vars('company_working_plan'));
        const workingPlanExceptions = JSON.parse(provider?.settings?.working_plan_exceptions || '{}');

        const firstWeekdayNumber = App.Utils.Date.getWeekdayId(vars('first_weekday'));
        const sortedWorkingPlan = App.Utils.Date.sortWeekDictionary(workingPlan, firstWeekdayNumber);

        const calendarDate = moment(fullCalendar.view.currentStart).clone();
        const viewEnd = fullCalendar.view.currentEnd;

        while (calendarDate.toDate() < viewEnd) {
            const weekdayNumber = parseInt(calendarDate.format('d'));
            const weekdayName = App.Utils.Date.getWeekdayName(weekdayNumber);
            const weekdayDate = calendarDate.format('YYYY-MM-DD');

            // Apply working plan exception if exists
            if (workingPlanExceptions[weekdayDate]) {
                sortedWorkingPlan[weekdayName] = workingPlanExceptions[weekdayDate];
                events.push(createWorkingPlanExceptionEvent(weekdayDate, workingPlanExceptions[weekdayDate], provider));
            }

            // Handle non-working days
            if (!sortedWorkingPlan[weekdayName]) {
                events.push(createNonWorkingDayEvent(calendarDate));
                calendarDate.add(1, 'day');
                continue;
            }

            // Add before/after work unavailability
            const dayPlan = sortedWorkingPlan[weekdayName];
            events.push(...createWorkHoursUnavailability(calendarDate, dayPlan, viewEnd));

            // Add break periods
            events.push(...createBreakEvents(calendarDate, dayPlan.breaks));

            calendarDate.add(1, 'day');
        }

        return events;
    }

    /**
     * Create a working plan exception event.
     *
     * @param {string} date - Date string (YYYY-MM-DD).
     * @param {Object} exception - Exception data.
     * @param {Object} provider - Provider object.
     * @returns {Object} Calendar event object.
     */
    function createWorkingPlanExceptionEvent(date, exception, provider) {
        const startTime = exception?.start || '00:00';
        const endTime = exception?.end || '00:00';

        return {
            title: lang('working_plan_exception'),
            start: moment(`${date} ${startTime}`, 'YYYY-MM-DD HH:mm', true).toDate(),
            end: moment(`${date} ${endTime}`, 'YYYY-MM-DD HH:mm', true).add(1, 'day').toDate(),
            allDay: true,
            color: EVENT_COLORS.unavailability,
            editable: false,
            className: 'fc-working-plan-exception fc-custom',
            display: 'block',
            data: {date, workingPlanException: exception, provider},
        };
    }

    /**
     * Create a non-working day event.
     *
     * @param {moment.Moment} calendarDate - Calendar date moment object.
     * @returns {Object} Calendar event object.
     */
    function createNonWorkingDayEvent(calendarDate) {
        return {
            title: lang('not_working'),
            start: calendarDate.clone().toDate(),
            end: calendarDate.clone().add(1, 'day').toDate(),
            allDay: false,
            color: EVENT_COLORS.notWorking,
            editable: false,
            display: 'background',
            className: 'fc-unavailability',
        };
    }

    /**
     * Create unavailability events for before/after working hours.
     *
     * @param {moment.Moment} calendarDate - Calendar date moment object.
     * @param {Object} dayPlan - Day working plan.
     * @param {Date} viewEnd - Calendar view end date.
     * @returns {Array} Array of calendar event objects.
     */
    function createWorkHoursUnavailability(calendarDate, dayPlan, viewEnd) {
        const events = [];
        const dateStr = calendarDate.format('YYYY-MM-DD');

        // Before work starts
        const [startHour, startMin] = dayPlan.start.split(':').map(Number);
        const workStart = calendarDate.clone().hour(startHour).minute(startMin);

        if (calendarDate.toDate() < workStart.toDate()) {
            events.push({
                title: lang('not_working'),
                start: calendarDate.clone().toDate(),
                end: moment(`${dateStr} ${dayPlan.start}:00`).toDate(),
                allDay: false,
                color: EVENT_COLORS.notWorking,
                editable: false,
                display: 'background',
                className: 'fc-unavailability',
            });
        }

        // After work ends
        const [endHour, endMin] = dayPlan.end.split(':').map(Number);
        const workEnd = calendarDate.clone().hour(endHour).minute(endMin);

        if (viewEnd > workEnd.toDate()) {
            events.push({
                title: lang('not_working'),
                start: moment(`${dateStr} ${dayPlan.end}:00`).toDate(),
                end: calendarDate.clone().add(1, 'day').toDate(),
                allDay: false,
                color: EVENT_COLORS.notWorking,
                editable: false,
                display: 'background',
                className: 'fc-unavailability',
            });
        }

        return events;
    }

    /**
     * Create break period events.
     *
     * @param {moment.Moment} calendarDate - Calendar date moment object.
     * @param {Array} breaks - Array of break periods.
     * @returns {Array} Array of calendar event objects.
     */
    function createBreakEvents(calendarDate, breaks) {
        if (!breaks) return [];

        const dateStr = calendarDate.format('YYYY-MM-DD');

        return breaks.map((breakPeriod) => ({
            title: lang('break'),
            start: moment(`${dateStr} ${breakPeriod.start}`).toDate(),
            end: moment(`${dateStr} ${breakPeriod.end}`).toDate(),
            allDay: false,
            color: EVENT_COLORS.notWorking,
            editable: false,
            display: 'background',
            className: 'fc-unavailability fc-break',
        }));
    }

    // =============================================================================
    // Event Listeners
    // =============================================================================

    /**
     * Add all page event listeners.
     */
    function addEventListeners() {
        // Reload button
        $reloadAppointments.on('click', () => {
            closePopover();
            refreshCalendarAppointments(
                $calendar,
                $selectFilterItem.val(),
                getSelectedFilterType(),
                fullCalendar.view.activeStart,
                fullCalendar.view.activeEnd,
            );
        });

        // Popover close button
        $calendarPage.on('click', '.close-popover', closePopover);

        // Popover edit button
        $calendarPage.on('click', '.edit-popover', onEditPopoverClick);

        // Popover delete button
        $calendarPage.on('click', '.delete-popover', onDeletePopoverClick);

        // Filter change
        $selectFilterItem.on('change', () => {
            const providerId = $selectFilterItem.val();
            const provider = findProvider(providerId);

            if (provider?.timezone) {
                $('.provider-timezone').text(vars('timezones')[provider.timezone]);
            }

            $('#insert-working-plan-exception').toggle(isProviderFilter());
            $reloadAppointments.trigger('click');

            window.localStorage.setItem('EasyAppointments.SelectFilterItem', providerId);
        });
    }

    // =============================================================================
    // Initialization
    // =============================================================================

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

    /**
     * Populate the filter dropdown with providers and services.
     */
    function populateFilterDropdown() {
        $selectFilterItem.append(new Option(lang('all'), FILTER_TYPE_ALL, true, true));

        if (vars('available_providers').length > 0) {
            $('<optgroup/>', {
                label: lang('providers'),
                type: 'providers-group',
                html: vars('available_providers').map((provider) =>
                    $('<option/>', {
                        value: provider.id,
                        type: FILTER_TYPE_PROVIDER,
                        'google-sync': provider.settings.google_sync,
                        'caldav-sync': provider.settings.caldav_sync,
                        text: `${provider.first_name} ${provider.last_name}`,
                    }),
                ),
            }).appendTo($selectFilterItem);
        }

        if (vars('available_services').length > 0) {
            $('<optgroup/>', {
                label: lang('services'),
                type: 'services-group',
                html: vars('available_services').map((service) =>
                    $('<option/>', {
                        value: service.id,
                        type: FILTER_TYPE_SERVICE,
                        text: service.name,
                    }),
                ),
            }).appendTo($selectFilterItem);
        }
    }

    /**
     * Initialize the calendar page.
     */
    function initialize() {
        const {columnFormat, timeFormat, slotTimeFormat} = getFormatSettings();
        const initialView = window.innerWidth < 468 ? 'timeGridDay' : 'timeGridWeek';
        const firstWeekdayNumber = App.Utils.Date.getWeekdayId(vars('first_weekday'));

        // Create FullCalendar instance
        fullCalendar = new FullCalendar.Calendar($calendar[0], {
            initialView,
            locale: vars('language_code'),
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
            selectable: true,
            selectMirror: true,
            themeSystem: 'bootstrap5',
            selectLongPressDelay: 100,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridDay,timeGridWeek,dayGridMonth',
            },
            buttonText: {
                today: lang('today'),
                day: lang('day'),
                week: lang('week'),
                month: lang('month'),
            },
            windowResize: onWindowResize,
            datesSet: onDatesSet,
            dateClick: onDateClick,
            eventClick: onEventClick,
            eventResize: onEventResize,
            eventDrop: onEventDrop,
            select: onSelect,
        });

        fullCalendar.render();
        $calendar.data('fullCalendar', fullCalendar);

        onWindowResize();

        // Setup filter dropdown
        populateFilterDropdown();
        $('#insert-working-plan-exception').hide();

        // Select provider if user is a provider
        if (vars('role_slug') === App.Layouts.Backend.DB_SLUG_PROVIDER) {
            $selectFilterItem.find(`optgroup:eq(0) option[value="${vars('user_id')}"]`).prop('selected', true);
        }

        addEventListeners();

        // Restore saved filter selection
        const savedFilter = window.localStorage.getItem('EasyAppointments.SelectFilterItem');
        if (savedFilter && $selectFilterItem.find(`option[value="${savedFilter}"]`).length) {
            $selectFilterItem.val(savedFilter).trigger('change');
        } else {
            $reloadAppointments.trigger('click');
        }

        // Display edit dialog if appointment hash provided
        if (vars('edit_appointment')) {
            populateAppointmentModal(vars('edit_appointment'));
            fullCalendar.gotoDate(moment(vars('edit_appointment').start_datetime).toDate());
        }

        // Disable actions if no filter options
        if (!$selectFilterItem.find('option').length) {
            $('#calendar-actions button').prop('disabled', true);
        }

        // Auto-refresh every 60 seconds
        setInterval(() => {
            if ($('.popover').length || App.Utils.CalendarSync.isCurrentlySyncing()) {
                return;
            }

            refreshCalendarAppointments(
                $calendar,
                $selectFilterItem.val(),
                getSelectedFilterType(),
                fullCalendar.view.activeStart,
                fullCalendar.view.activeEnd,
            );
        }, 60000);
    }

    // =============================================================================
    // Public API
    // =============================================================================

    return {
        initialize,
        FILTER_TYPE_ALL,
        FILTER_TYPE_PROVIDER,
        FILTER_TYPE_SERVICE,
    };
})();
