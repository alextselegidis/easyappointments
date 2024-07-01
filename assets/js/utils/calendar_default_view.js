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
 * Default calendar view utility.
 *
 * This module implements the functionality of the default calendar view.
 *
 * Old Name: BackendCalendarDefaultView
 */
App.Utils.CalendarDefaultView = (function () {
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
    const FILTER_TYPE_ALL = 'all';
    const FILTER_TYPE_PROVIDER = 'provider';
    const FILTER_TYPE_SERVICE = 'service';
    const moment = window.moment;

    let $popoverTarget;
    let fullCalendar = null;
    let lastFocusedEventData; // Contains event data for later use.

    /**
     * Add the utility event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Reload Button "Click"
         *
         * When the user clicks the reload button, the calendar items need to be refreshed.
         */
        $reloadAppointments.on('click', () => {
            const calendarView = fullCalendar.view;

            if ($popoverTarget) {
                $popoverTarget.popover('dispose');
            }

            refreshCalendarAppointments(
                $calendar,
                $selectFilterItem.val(),
                $selectFilterItem.find('option:selected').attr('type'),
                calendarView.activeStart,
                calendarView.activeEnd,
            );
        });

        /**
         * Event: Popover Close Button "Click"
         *
         * Hides the open popover element.
         */
        $calendarPage.on('click', '.close-popover', () => {
            if ($popoverTarget) {
                $popoverTarget.popover('dispose');
            }
        });

        /**
         * Event: Popover Edit Button "Click"
         *
         * Enables the edit dialog of the selected calendar event.
         *
         * @param {jQuery.Event} event
         */
        $calendarPage.on('click', '.edit-popover', (event) => {
            if ($popoverTarget) {
                $popoverTarget.popover('dispose');
            }

            let startMoment;
            let endMoment;

            const data = lastFocusedEventData.extendedProps.data;

            if (data.hasOwnProperty('workingPlanException')) {
                const originalDate = lastFocusedEventData.extendedProps.data.date;
                const workingPlanException = lastFocusedEventData.extendedProps.data.workingPlanException;
                const provider = lastFocusedEventData.extendedProps.data.provider;

                App.Components.WorkingPlanExceptionsModal.edit(originalDate, workingPlanException).done(
                    (date, workingPlanException) => {
                        const successCallback = () => {
                            App.Layouts.Backend.displayNotification(lang('working_plan_exception_saved'));

                            const workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};

                            workingPlanExceptions[date] = workingPlanException;

                            if (date !== originalDate) {
                                delete workingPlanExceptions[originalDate];
                            }

                            for (const index in vars('available_providers')) {
                                const availableProvider = vars('available_providers')[index];

                                if (Number(availableProvider.id) === Number(provider.id)) {
                                    availableProvider.settings.working_plan_exceptions =
                                        JSON.stringify(workingPlanExceptions);
                                    break;
                                }
                            }

                            $reloadAppointments.trigger('click'); // Update the calendar.
                        };

                        App.Http.Calendar.saveWorkingPlanException(
                            date,
                            workingPlanException,
                            provider.id,
                            successCallback,
                            null,
                            originalDate,
                        );
                    },
                );
            } else if (!lastFocusedEventData.extendedProps.data.is_unavailability) {
                const appointment = lastFocusedEventData.extendedProps.data;

                App.Components.AppointmentsModal.resetModal();

                // Apply appointment data and show modal dialog.
                $appointmentsModal.find('.modal-header h3').text(lang('edit_appointment_title'));
                $appointmentsModal.find('#appointment-id').val(appointment.id);
                $appointmentsModal.find('#select-service').val(appointment.id_services).trigger('change');
                $appointmentsModal.find('#select-provider').val(appointment.id_users_provider);

                // Set the start and end datetime of the appointment.
                startMoment = moment(appointment.start_datetime);
                App.Utils.UI.setDateTimePickerValue($appointmentsModal.find('#start-datetime'), startMoment.toDate());

                endMoment = moment(appointment.end_datetime);
                App.Utils.UI.setDateTimePickerValue($appointmentsModal.find('#end-datetime'), endMoment.toDate());

                const customer = appointment.customer;
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
                $appointmentsModal.find('#appointment-location').val(appointment.location);
                $appointmentsModal.find('#appointment-status').val(appointment.status);
                $appointmentsModal.find('#appointment-notes').val(appointment.notes);
                $appointmentsModal.find('#customer-notes').val(customer.notes);
                $appointmentsModal.find('#custom-field-1').val(customer.custom_field_1);
                $appointmentsModal.find('#custom-field-2').val(customer.custom_field_2);
                $appointmentsModal.find('#custom-field-3').val(customer.custom_field_3);
                $appointmentsModal.find('#custom-field-4').val(customer.custom_field_4);
                $appointmentsModal.find('#custom-field-5').val(customer.custom_field_5);

                App.Components.ColorSelection.setColor(
                    $appointmentsModal.find('#appointment-color'),
                    appointment.color,
                );

                $appointmentsModal.modal('show');
            } else {
                const unavailability = lastFocusedEventData.extendedProps.data;

                // Replace string date values with actual date objects.
                unavailability.start_datetime = moment(lastFocusedEventData.start).format('YYYY-MM-DD HH:mm:ss');
                startMoment = moment(unavailability.start_datetime);
                unavailability.end_datetime = moment(lastFocusedEventData.end).format('YYYY-MM-DD HH:mm:ss');
                endMoment = moment(unavailability.end_datetime);

                App.Components.UnavailabilitiesModal.resetModal();

                // Apply unavailability data to dialog.
                $unavailabilitiesModal.find('.modal-header h3').text(lang('edit_unavailability_title'));
                App.Utils.UI.setDateTimePickerValue(
                    $unavailabilitiesModal.find('#unavailability-start'),
                    startMoment.toDate(),
                );
                App.Utils.UI.setDateTimePickerValue(
                    $unavailabilitiesModal.find('#unavailability-end'),
                    endMoment.toDate(),
                );
                $unavailabilitiesModal.find('#unavailability-id').val(unavailability.id);
                $unavailabilitiesModal.find('#unavailability-provider').val(unavailability.id_users_provider);
                $unavailabilitiesModal.find('#unavailability-notes').val(unavailability.notes);
                $unavailabilitiesModal.modal('show');
            }
        });

        /**
         * Event: Popover Delete Button "Click"
         *
         * Displays a prompt on whether the user wants the appointment to be deleted. If he confirms the
         * deletion then an AJAX call is made to the server and deletes the appointment from the database.
         *
         * @param {jQuery.Event} event
         */
        $calendarPage.on('click', '.delete-popover', (event) => {
            if ($popoverTarget) {
                $popoverTarget.popover('dispose');
            }

            if (lastFocusedEventData.extendedProps.data.workingPlanException !== undefined) {
                const providerId = $selectFilterItem.val();

                const provider = vars('available_providers').find(
                    (availableProvider) => Number(availableProvider.id) === Number(providerId),
                );

                if (!provider) {
                    throw new Error('Provider could not be found: ' + providerId);
                }

                const successCallback = () => {
                    App.Layouts.Backend.displayNotification(lang('working_plan_exception_deleted'));

                    const workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};
                    delete workingPlanExceptions[date];

                    for (const index in vars('available_providers')) {
                        const availableProvider = vars('available_providers')[index];

                        if (Number(availableProvider.id) === Number(providerId)) {
                            availableProvider.settings.working_plan_exceptions = JSON.stringify(workingPlanExceptions);
                            break;
                        }
                    }

                    $reloadAppointments.trigger('click'); // Update the calendar.
                };

                const date = moment(lastFocusedEventData.start).format('YYYY-MM-DD');

                App.Http.Calendar.deleteWorkingPlanException(date, providerId, successCallback);
            } else if (!lastFocusedEventData.extendedProps.data.is_unavailability) {
                const buttons = [
                    {
                        text: lang('cancel'),
                        click: (event, messageModal) => {
                            messageModal.hide();
                        },
                    },
                    {
                        text: lang('delete'),
                        click: (event, messageModal) => {
                            const appointmentId = lastFocusedEventData.extendedProps.data.id;

                            const cancellationReason = $('#cancellation-reason').val();

                            App.Http.Calendar.deleteAppointment(appointmentId, cancellationReason).done(() => {
                                messageModal.hide();

                                // Refresh calendar event items.
                                $reloadAppointments.trigger('click');
                            });
                        },
                    },
                ];

                App.Utils.Message.show(
                    lang('delete_appointment_title'),
                    lang('write_appointment_removal_reason'),
                    buttons,
                );

                $('<textarea/>', {
                    'class': 'form-control w-100',
                    'id': 'cancellation-reason',
                    'rows': '3',
                }).appendTo('#message-modal .modal-body');
            } else {
                // Do not display confirmation prompt.

                const unavailabilityId = lastFocusedEventData.extendedProps.data.id;

                App.Http.Calendar.deleteUnavailability(unavailabilityId).done(() => {
                    // Refresh calendar event items.
                    $reloadAppointments.trigger('click');
                });
            }
        });

        /**
         * Event: Calendar Filter Item "Change"
         *
         * Load the appointments that correspond to the select filter item and display them on the calendar.
         */
        $selectFilterItem.on('change', () => {
            const providerId = $selectFilterItem.val();

            const provider = vars('available_providers').find(
                (availableProvider) => Number(availableProvider.id) === Number(providerId),
            );

            if (provider && provider.timezone) {
                $('.provider-timezone').text(vars('timezones')[provider.timezone]);
            }

            $('#insert-working-plan-exception').toggle(
                providerId === App.Utils.CalendarDefaultView.FILTER_TYPE_PROVIDER,
            );

            $reloadAppointments.trigger('click');

            window.localStorage.setItem('EasyAppointments.SelectFilterItem', $selectFilterItem.val());
        });
    }

    /**
     * Get Calendar Component Height
     *
     * This method calculates the proper calendar height, in order to be displayed correctly, even when the
     * browser window is resizing.
     *
     * @return {Number} Returns the calendar element height in pixels.
     */
    function getCalendarHeight() {
        const result =
            window.innerHeight - $footer.outerHeight() - $header.outerHeight() - $calendarToolbar.outerHeight() - 60; // 60 for fine tuning
        return result > 780 ? result : 780; // Minimum height is 800px
    }

    /**
     * Get the event notes for the popup widget.
     *
     * @param {Event} event
     */
    function getEventNotes(event) {
        if (!event.extendedProps || !event.extendedProps.data || !event.extendedProps.data.notes) {
            return '-';
        }

        const notes = event.extendedProps.data.notes;

        return notes.length > 100 ? notes.substring(0, 100) + '...' : notes;
    }

    /**
     * Calendar Event "Click" Callback
     *
     * When the user clicks on an appointment object on the calendar, then a data preview popover is display
     * above the calendar item.
     *
     * @param {Object} info
     */
    function onEventClick(info) {
        const $target = $(info.el);

        if ($popoverTarget) {
            $popoverTarget.popover('dispose');
        }

        let $html;
        let displayEdit;
        let displayDelete;

        // Depending on where the user clicked the event (title or empty space) we
        // need to use different selectors to reach the parent element.

        if ($target.hasClass('fc-unavailability')) {
            displayEdit =
                $target.hasClass('fc-custom') && vars('privileges').appointments.edit === true ? '' : 'd-none';
            displayDelete =
                $target.hasClass('fc-custom') && vars('privileges').appointments.delete === true ? 'me-2' : 'd-none'; // Same value at the time.

            let startDateTimeObject = info.event.start;
            let endDateTimeObject = info.event.end || info.event.start;

            if (info.event.extendedProps.data) {
                startDateTimeObject = new Date(info.event.extendedProps.data.start_datetime);
                endDateTimeObject = new Date(info.event.extendedProps.data.end_datetime);
            }

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('start'),
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            moment(startDateTimeObject).format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true,
                        ),
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('end'),
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            moment(endDateTimeObject).format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true,
                        ),
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('notes'),
                    }),
                    $('<span/>', {
                        'text': getEventNotes(info.event),
                    }),
                    $('<br/>'),

                    App.Utils.CalendarEventPopover.renderCustomContent(info),

                    $('<hr/>'),

                    $('<div/>', {
                        'class': 'd-flex justify-content-center',
                        'html': [
                            $('<button/>', {
                                'class': 'close-popover btn btn-outline-secondary me-2',
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-ban me-2',
                                    }),
                                    $('<span/>', {
                                        'text': lang('close'),
                                    }),
                                ],
                            }),
                            $('<button/>', {
                                'class': 'delete-popover btn btn-outline-secondary ' + displayDelete,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-trash-alt me-2',
                                    }),
                                    $('<span/>', {
                                        'text': lang('delete'),
                                    }),
                                ],
                            }),
                            $('<button/>', {
                                'class': 'edit-popover btn btn-primary ' + displayEdit,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-edit me-2',
                                    }),
                                    $('<span/>', {
                                        'text': lang('edit'),
                                    }),
                                ],
                            }),
                        ],
                    }),
                ],
            });
        } else if ($target.hasClass('fc-working-plan-exception')) {
            displayDelete =
                $target.hasClass('fc-custom') && vars('privileges').appointments.delete === true ? 'me-2' : 'd-none';

            const {date, workingPlanException, provider} = info.event.extendedProps.data;
            const startTime = workingPlanException?.start;
            const endTime = workingPlanException?.end;

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('provider'),
                    }),
                    $('<span/>', {
                        'text': `${provider.first_name} ${provider.last_name}`,
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('start'),
                    }),
                    $('<span/>', {
                        'text': startTime
                            ? App.Utils.Date.format(
                                  `${date} ${startTime}`,
                                  vars('date_format'),
                                  vars('time_format'),
                                  true,
                              )
                            : '-',
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('end'),
                    }),
                    $('<span/>', {
                        'text': endTime
                            ? App.Utils.Date.format(
                                  `${date} ${endTime}`,
                                  vars('date_format'),
                                  vars('time_format'),
                                  true,
                              )
                            : '-',
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('timezone'),
                    }),
                    $('<span/>', {
                        'text': startTime ? vars('timezones')[provider.timezone] : '-',
                    }),
                    $('<br/>'),

                    App.Utils.CalendarEventPopover.renderCustomContent(info),

                    $('<hr/>'),

                    $('<div/>', {
                        'class': 'd-flex justify-content-between',
                        'html': [
                            $('<button/>', {
                                'class': 'close-popover btn btn-outline-secondary me-2',
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-ban me-2',
                                    }),
                                    $('<span/>', {
                                        'text': lang('close'),
                                    }),
                                ],
                            }),
                            $('<button/>', {
                                'class': 'delete-popover btn btn-outline-secondary ' + displayDelete,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-trash-alt me-2',
                                    }),
                                    $('<span/>', {
                                        'text': lang('delete'),
                                    }),
                                ],
                            }),
                            $('<button/>', {
                                'class': 'edit-popover btn btn-primary',
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-edit me-2',
                                    }),
                                    $('<span/>', {
                                        'text': lang('edit'),
                                    }),
                                ],
                            }),
                        ],
                    }),
                ],
            });
        } else {
            displayEdit = vars('privileges').appointments.edit === true ? '' : 'd-none';
            displayDelete = vars('privileges').appointments.delete === true ? 'me-2' : 'd-none';

            const customerInfo = [];

            if (info.event.extendedProps.data.customer.first_name) {
                customerInfo.push(info.event.extendedProps.data.customer.first_name);
            }

            if (info.event.extendedProps.data.customer.last_name) {
                customerInfo.push(info.event.extendedProps.data.customer.last_name);
            }

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('start'),
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            moment(info.event.start).format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true,
                        ),
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('end'),
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true,
                        ),
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('timezone'),
                    }),
                    $('<span/>', {
                        'text': vars('timezones')[info.event.extendedProps.data.provider.timezone],
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('status'),
                    }),
                    $('<span/>', {
                        'text': info.event.extendedProps.data.status || '-',
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('service'),
                    }),
                    $('<span/>', {
                        'text': info.event.extendedProps.data.service.name,
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('provider'),
                    }),
                    App.Utils.CalendarEventPopover.renderMapIcon(info.event.extendedProps.data.provider),
                    $('<span/>', {
                        'text':
                            info.event.extendedProps.data.provider.first_name +
                            ' ' +
                            info.event.extendedProps.data.provider.last_name,
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('customer'),
                    }),
                    App.Utils.CalendarEventPopover.renderMapIcon(info.event.extendedProps.data.customer),
                    $('<span/>', {
                        'class': 'd-inline-block',
                        'text': customerInfo.length ? customerInfo.join(' ') : '-',
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('email'),
                    }),
                    App.Utils.CalendarEventPopover.renderMailIcon(info.event.extendedProps.data.customer.email),
                    $('<span/>', {
                        'class': 'd-inline-block',
                        'text': info.event.extendedProps.data.customer.email || '-',
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('phone'),
                    }),
                    App.Utils.CalendarEventPopover.renderPhoneIcon(info.event.extendedProps.data.customer.phone_number),
                    $('<span/>', {
                        'class': 'd-inline-block',
                        'text': info.event.extendedProps.data.customer.phone_number || '-',
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('notes'),
                    }),
                    $('<span/>', {
                        'text': getEventNotes(info.event),
                    }),
                    $('<br/>'),

                    App.Utils.CalendarEventPopover.renderCustomContent(info),

                    $('<hr/>'),

                    $('<div/>', {
                        'class': 'd-flex justify-content-center',
                        'html': [
                            $('<button/>', {
                                'class': 'close-popover btn btn-outline-secondary me-2',
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-ban me-2',
                                    }),
                                    $('<span/>', {
                                        'text': lang('close'),
                                    }),
                                ],
                            }),
                            $('<button/>', {
                                'class': 'delete-popover btn btn-outline-secondary ' + displayDelete,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-trash-alt me-2',
                                    }),
                                    $('<span/>', {
                                        'text': lang('delete'),
                                    }),
                                ],
                            }),
                            $('<button/>', {
                                'class': 'edit-popover btn btn-primary ' + displayEdit,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-edit me-2',
                                    }),
                                    $('<span/>', {
                                        'text': lang('edit'),
                                    }),
                                ],
                            }),
                        ],
                    }),
                ],
            });
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

        // Fix popover position.
        const $popover = $calendarPage.find('.popover');

        if ($popover.length > 0 && $popover.position().top < 200) {
            $popover.css('top', '200px');
        }
    }

    /**
     * Calendar Event "Resize" Callback
     *
     * The user can change the duration of an event by resizing an appointment object on the calendar. This
     * change needs to be stored to the database too and this is done via an ajax call.
     *
     * @see updateAppointmentData()
     *
     * @param {Object} info
     */
    function onEventResize(info) {
        if (vars('privileges').appointments.edit === false) {
            info.revert();
            App.Layouts.Backend.displayNotification(lang('no_privileges_edit_appointments'));
            return;
        }

        let successCallback;

        if ($notification.is(':visible')) {
            $notification.hide('bind');
        }

        if (!info.event.extendedProps.data.is_unavailability) {
            // Prepare appointment data.
            info.event.extendedProps.data.end_datetime = moment(info.event.extendedProps.data.end_datetime)
                .add({days: info.endDelta.days, milliseconds: info.endDelta.milliseconds})
                .format('YYYY-MM-DD HH:mm:ss');

            const appointment = {...info.event.extendedProps.data};

            appointment.is_unavailability = Number(appointment.is_unavailability);

            // Must delete the following because only appointment data should be provided to the AJAX call.
            delete appointment.customer;
            delete appointment.provider;
            delete appointment.service;

            // Success callback
            successCallback = () => {
                // Display success notification to user.
                const undoFunction = () => {
                    appointment.end_datetime = info.event.extendedProps.data.end_datetime = moment(
                        appointment.end_datetime,
                    )
                        .add({days: -info.endDelta.days, milliseconds: -info.endDelta.milliseconds})
                        .format('YYYY-MM-DD HH:mm:ss');

                    App.Http.Calendar.saveAppointment(appointment).done(() => {
                        $notification.hide('blind');
                    });

                    info.revert();
                };

                App.Layouts.Backend.displayNotification(lang('appointment_updated'), [
                    {
                        'label': lang('undo'),
                        'function': undoFunction,
                    },
                ]);

                // Update the event data for later use.
                info.event.setProp('data', info.event.extendedProps.data);
            };

            // Update appointment data.
            App.Http.Calendar.saveAppointment(appointment, null, successCallback);
        } else {
            // Update unavailability time period.
            const unavailability = {
                id: info.event.extendedProps.data.id,
                start_datetime: moment(info.event.start).format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'),
                id_users_provider: info.event.extendedProps.data.id_users_provider,
            };

            info.event.extendedProps.data.end_datetime = unavailability.end_datetime;

            // Define success callback function.
            successCallback = () => {
                // Display success notification to user.
                const undoFunction = () => {
                    unavailability.end_datetime = info.event.extendedProps.data.end_datetime = moment(
                        unavailability.end_datetime,
                    )
                        .add({days: -info.endDelta.days, milliseconds: -info.endDelta.milliseconds})
                        .format('YYYY-MM-DD HH:mm:ss');

                    App.Http.Calendar.saveUnavailability(unavailability).done(() => {
                        $notification.hide('blind');
                    });

                    info.revert();
                };

                App.Layouts.Backend.displayNotification(lang('unavailability_updated'), [
                    {
                        'label': lang('undo'),
                        'function': undoFunction,
                    },
                ]);

                // Update the event data for later use.
                info.event.setProp('data', info.event.extendedProps.data);
            };

            App.Http.Calendar.saveUnavailability(unavailability, successCallback, null);
        }
    }

    /**
     * Calendar Window "Resize" Callback
     *
     * The calendar element needs to be re-sized too in order to fit into the window. Nevertheless, if the window
     * becomes very small the calendar won't shrink anymore.
     *
     * @see getCalendarHeight()
     */
    function onWindowResize() {
        fullCalendar.setOption('height', getCalendarHeight());
    }

    /**
     * Calendar Day "Click" Callback
     *
     * When the user clicks on a day square on the calendar, then he will automatically be transferred to that
     * day view calendar.
     *
     * @param {Object} info
     */
    function onDateClick(info) {
        if (info.allDay) {
            fullCalendar.changeView('timeGridDay');
            fullCalendar.gotoDate(info.date);
        }
    }

    /**
     * Calendar Event "Drop" Callback
     *
     * This event handler is triggered whenever the user drags and drops an event into a different position
     * on the calendar. We need to update the database with this change. This is done via an ajax call.
     *
     * @param {Object} info
     */
    function onEventDrop(info) {
        if (vars('privileges').appointments.edit === false) {
            info.revert();
            App.Layouts.Backend.displayNotification(lang('no_privileges_edit_appointments'));
            return;
        }

        if ($notification.is(':visible')) {
            $notification.hide('bind');
        }

        let successCallback;

        if (!info.event.extendedProps.data.is_unavailability) {
            // Prepare appointment data.
            const appointment = {...info.event.extendedProps.data};

            // Must delete the following because only appointment data should be provided to the ajax call.
            delete appointment.customer;
            delete appointment.provider;
            delete appointment.service;

            appointment.start_datetime = moment(appointment.start_datetime)
                .add({days: info.delta.days, millisecond: info.delta.milliseconds})
                .format('YYYY-MM-DD HH:mm:ss');

            appointment.end_datetime = moment(appointment.end_datetime)
                .add({days: info.delta.days, millisecond: info.delta.milliseconds})
                .format('YYYY-MM-DD HH:mm:ss');

            appointment.is_unavailability = 0;

            info.event.extendedProps.data.start_datetime = appointment.start_datetime;
            info.event.extendedProps.data.end_datetime = appointment.end_datetime;

            // Define success callback function.
            successCallback = () => {
                // Define the undo function, if the user needs to reset the last change.
                const undoFunction = () => {
                    appointment.start_datetime = moment(appointment.start_datetime)
                        .add({
                            days: -info.delta.days,
                            milliseconds: -info.delta.milliseconds,
                        })
                        .format('YYYY-MM-DD HH:mm:ss');

                    appointment.end_datetime = moment(appointment.end_datetime)
                        .add({days: -info.delta.days, milliseconds: -info.delta.milliseconds})
                        .format('YYYY-MM-DD HH:mm:ss');

                    info.event.extendedProps.data.start_datetime = appointment.start_datetime;
                    info.event.extendedProps.data.end_datetime = appointment.end_datetime;

                    App.Http.Calendar.saveAppointment(appointment).done(() => {
                        $notification.hide('blind');
                    });

                    info.revert();
                };

                App.Layouts.Backend.displayNotification(lang('appointment_updated'), [
                    {
                        'label': lang('undo'),
                        'function': undoFunction,
                    },
                ]);
            };

            // Update appointment data.
            App.Http.Calendar.saveAppointment(appointment, null, successCallback);
        } else {
            // Update unavailability time period.
            const unavailability = {
                id: info.event.extendedProps.data.id,
                start_datetime: moment(info.event.start).format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'),
                id_users_provider: info.event.extendedProps.data.id_users_provider,
            };

            successCallback = () => {
                const undoFunction = () => {
                    unavailability.start_datetime = moment(unavailability.start_datetime)
                        .add({days: -info.delta.days, milliseconds: -info.delta.milliseconds})
                        .format('YYYY-MM-DD HH:mm:ss');

                    unavailability.end_datetime = moment(unavailability.end_datetime)
                        .add({days: -info.delta.days, milliseconds: -info.delta.milliseconds})
                        .format('YYYY-MM-DD HH:mm:ss');

                    unavailability.is_unavailability = 1;

                    info.event.extendedProps.data.start_datetime = unavailability.start_datetime;
                    info.event.extendedProps.data.end_datetime = unavailability.end_datetime;

                    App.Http.Calendar.saveUnavailability(unavailability).done(() => {
                        $notification.hide('blind');
                    });

                    info.revert();
                };

                App.Layouts.Backend.displayNotification(lang('unavailability_updated'), [
                    {
                        label: lang('undo'),
                        function: undoFunction,
                    },
                ]);
            };

            App.Http.Calendar.saveUnavailability(unavailability, successCallback);
        }
    }

    function onSelect(info) {
        if (info.allDay) {
            return;
        }

        const isProviderDisplayed = $selectFilterItem.find('option:selected').attr('type') === FILTER_TYPE_PROVIDER;

        const buttons = [
            {
                text: lang('unavailability'),
                click: (event, messageModal) => {
                    $('#insert-unavailability').trigger('click');

                    if (isProviderDisplayed) {
                        $('#unavailability-provider').val($selectFilterItem.val());
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

                    // Preselect service & provider.
                    let service;

                    if (isProviderDisplayed) {
                        const provider = vars('available_providers').find(
                            (availableProvider) => Number(availableProvider.id) === Number($selectFilterItem.val()),
                        );

                        if (provider) {
                            service = vars('available_services').find(
                                (availableService) => provider.services.indexOf(availableService.id) !== -1,
                            );

                            if (service) {
                                $appointmentsModal.find('#select-service').val(service.id);
                            }
                        }

                        if (!$appointmentsModal.find('#select-service').val()) {
                            $('#select-service option:first').prop('selected', true);
                        }

                        $appointmentsModal.find('#select-service').trigger('change');

                        if (provider) {
                            $appointmentsModal.find('#select-provider').val(provider.id);
                        }

                        if (!$appointmentsModal.find('#select-provider').val()) {
                            $appointmentsModal.find('#select-provider option:first').prop('selected', true);
                        }

                        $appointmentsModal.find('#select-provider').trigger('change');
                    } else {
                        service = vars('available_services').find(
                            (availableService) => Number(availableService.id) === Number($selectFilterItem.val()),
                        );

                        if (service) {
                            $appointmentsModal.find('#select-service').val(service.id).trigger('change');
                        }
                    }

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

    /**
     * Calendar "View Render" Callback
     *
     * Whenever the calendar changes or refreshes its view certain actions need to be made, in order to
     * display proper information to the user.
     */
    function onDatesSet() {
        if ($selectFilterItem.val() === null) {
            return;
        }

        refreshCalendarAppointments(
            $calendar,
            $selectFilterItem.val(),
            $('#select-filter-item option:selected').attr('type'),
            fullCalendar.view.activeStart,
            fullCalendar.view.activeEnd,
        );

        $(window).trigger('resize'); // Places the footer on the bottom.

        // Remove all open popovers.
        $('.close-popover').each((index, closePopoverButton) => {
            if ($popoverTarget) {
                $popoverTarget.popover('dispose');
            }
        });

        // Add new popovers.
        $('.fv-events').each((index, eventEl) => {
            $(eventEl).popover();
        });
    }

    /**
     * Refresh Calendar Appointments
     *
     * This method reloads the registered appointments for the selected date period and filter type.
     *
     * @param {Object} $calendar The calendar jQuery object.
     * @param {Number} recordId The selected record id.
     * @param {String} filterType The filter type, could be either FILTER_TYPE_PROVIDER or FILTER_TYPE_SERVICE.
     * @param {String} startDate Visible start date of the calendar.
     * @param {String} endDate Visible end date of the calendar.
     */
    function refreshCalendarAppointments($calendar, recordId, filterType, startDate, endDate) {
        $('#loading').css('visibility', 'hidden');

        const calendarEventSource = [];

        startDate = moment(startDate).format('YYYY-MM-DD');

        endDate = moment(endDate).format('YYYY-MM-DD');

        App.Http.Calendar.getCalendarAppointments(recordId, startDate, endDate, filterType)
            .done((response) => {
                const calendarEventSources = fullCalendar.getEventSources();

                calendarEventSources.forEach((calendarEventSource) => calendarEventSource.remove());

                // Add appointments to calendar.
                response.appointments.forEach((appointment) => {
                    const title = [appointment.service.name];

                    const customerInfo = [];

                    if (appointment.customer.first_name) {
                        customerInfo.push(appointment.customer.first_name);
                    }

                    if (appointment.customer.last_name) {
                        customerInfo.push(appointment.customer.last_name);
                    }

                    if (customerInfo.length) {
                        title.push(customerInfo.join(' '));
                    }

                    const appointmentEvent = {
                        id: appointment.id,
                        title: title.join(' - '),
                        start: moment(appointment.start_datetime).toDate(),
                        end: moment(appointment.end_datetime).toDate(),
                        allDay: false,
                        color: appointment.color,
                        data: appointment, // Store appointment data for later use.
                    };

                    calendarEventSource.push(appointmentEvent);
                });

                // Add custom unavailability periods (they are always displayed on the calendar, even if the provider
                // won't work on that day).
                response.unavailabilities.forEach((unavailability) => {
                    let notes = unavailability.notes ? ' - ' + unavailability.notes : '';

                    if (unavailability.notes && unavailability.notes.length > 30) {
                        notes = ' - ' + unavailability.notes.substring(0, 30) + '...';
                    }

                    const unavailabilityEvent = {
                        title: lang('unavailability') + notes,
                        start: moment(unavailability.start_datetime).toDate(),
                        end: moment(unavailability.end_datetime).toDate(),
                        allDay: false,
                        color: '#879DB4',
                        editable: true,
                        className: 'fc-unavailability fc-custom',
                        data: unavailability,
                    };

                    calendarEventSource.push(unavailabilityEvent);
                });

                response?.blocked_periods?.forEach((blockedPeriod) => {
                    const blockedPeriodEvent = {
                        title: blockedPeriod.name,
                        start: moment(blockedPeriod.start_datetime).toDate(),
                        end: moment(blockedPeriod.end_datetime).toDate(),
                        allDay: true,
                        backgroundColor: '#d65069',
                        borderColor: '#d65069',
                        textColor: '#ffffff',
                        editable: false,
                        className: 'fc-blocked-period fc-unavailability',
                        data: blockedPeriod,
                    };

                    calendarEventSource.push(blockedPeriodEvent);
                });

                const calendarView = fullCalendar.view;

                if (calendarView.type === 'dayGridMonth') {
                    return;
                }

                const provider = vars('available_providers').find(
                    (availableProvider) => Number(availableProvider.id) === Number(recordId),
                );

                const workingPlan = JSON.parse(
                    provider ? provider.settings.working_plan : vars('company_working_plan'),
                );
                const workingPlanExceptions = JSON.parse(provider ? provider.settings.working_plan_exceptions : '{}');
                let unavailabilityEvent;
                let breakStart;
                let breakEnd;
                let workingPlanExceptionStart;
                let workingPlanExceptionEnd;
                let weekdayNumber;
                let weekdayName;
                let weekdayDate;
                let workingPlanExceptionEvent;
                let startHour;
                let endHour;
                let workDateStart;
                let workDateEnd;

                // Sort the working plan starting with the first day as set in General settings to correctly align
                // breaks in the calendar display.
                const firstWeekdayNumber = App.Utils.Date.getWeekdayId(vars('first_weekday'));
                const sortedWorkingPlan = App.Utils.Date.sortWeekDictionary(workingPlan, firstWeekdayNumber);

                const calendarDate = moment(calendarView.currentStart).clone();

                while (calendarDate.toDate() < calendarView.currentEnd) {
                    weekdayNumber = parseInt(calendarDate.format('d'));
                    weekdayName = App.Utils.Date.getWeekdayName(weekdayNumber);
                    weekdayDate = calendarDate.format('YYYY-MM-DD');

                    // Add working plan exception event.
                    if (workingPlanExceptions && workingPlanExceptions.hasOwnProperty(weekdayDate)) {
                        sortedWorkingPlan[weekdayName] = workingPlanExceptions[weekdayDate];

                        const startTime = sortedWorkingPlan[weekdayName]?.start || '00:00';
                        const endTime = sortedWorkingPlan[weekdayName]?.end || '00:00';

                        workingPlanExceptionStart = `${weekdayDate} ${startTime}`;
                        workingPlanExceptionEnd = `${weekdayDate} ${endTime}`;

                        workingPlanExceptionEvent = {
                            title: lang('working_plan_exception'),
                            start: moment(workingPlanExceptionStart, 'YYYY-MM-DD HH:mm', true).toDate(),
                            end: moment(workingPlanExceptionEnd, 'YYYY-MM-DD HH:mm', true).add(1, 'day').toDate(),
                            allDay: true,
                            color: '#879DB4',
                            editable: false,
                            className: 'fc-working-plan-exception fc-custom',
                            data: {
                                date: weekdayDate,
                                workingPlanException: workingPlanExceptions[weekdayDate],
                                provider: provider,
                            },
                        };

                        calendarEventSource.push(workingPlanExceptionEvent);
                    }

                    // Non-working day.
                    if (sortedWorkingPlan[weekdayName] === null) {
                        // Add a full day unavailability event.
                        unavailabilityEvent = {
                            title: lang('not_working'),
                            start: calendarDate.clone().toDate(),
                            end: calendarDate.clone().add(1, 'day').toDate(),
                            allDay: false,
                            color: '#BEBEBE',
                            editable: false,
                            display: 'background',
                            className: 'fc-unavailability',
                        };

                        calendarEventSource.push(unavailabilityEvent);

                        calendarDate.add(1, 'day');

                        continue; // Go to the next loop.
                    }

                    // Add unavailability period before work starts.
                    startHour = sortedWorkingPlan[weekdayName].start.split(':');
                    workDateStart = calendarDate.clone();
                    workDateStart.hour(parseInt(startHour[0]));
                    workDateStart.minute(parseInt(startHour[1]));

                    if (calendarDate.toDate() < workDateStart.toDate()) {
                        unavailabilityEvent = {
                            title: lang('not_working'),
                            start: calendarDate.clone().toDate(),
                            end: moment(
                                calendarDate.format('YYYY-MM-DD') + ' ' + sortedWorkingPlan[weekdayName].start + ':00',
                            ).toDate(),
                            allDay: false,
                            color: '#BEBEBE',
                            editable: false,
                            display: 'background',
                            className: 'fc-unavailability',
                        };

                        calendarEventSource.push(unavailabilityEvent);
                    }

                    // Add unavailability period after work ends.
                    endHour = sortedWorkingPlan[weekdayName].end.split(':');
                    workDateEnd = calendarDate.clone();
                    workDateEnd.hour(parseInt(endHour[0]));
                    workDateEnd.minute(parseInt(endHour[1]));

                    if (calendarView.currentEnd > workDateEnd.toDate()) {
                        unavailabilityEvent = {
                            title: lang('not_working'),
                            start: moment(
                                calendarDate.format('YYYY-MM-DD') + ' ' + sortedWorkingPlan[weekdayName].end + ':00',
                            ).toDate(),
                            end: calendarDate.clone().add(1, 'day').toDate(),
                            allDay: false,
                            color: '#BEBEBE',
                            editable: false,
                            display: 'background',
                            className: 'fc-unavailability',
                        };

                        calendarEventSource.push(unavailabilityEvent);
                    }

                    // Add unavailability periods during day breaks.
                    sortedWorkingPlan[weekdayName].breaks.forEach((breakPeriod) => {
                        const breakStartString = breakPeriod.start.split(':');
                        breakStart = calendarDate.clone();
                        breakStart.hour(parseInt(breakStartString[0]));
                        breakStart.minute(parseInt(breakStartString[1]));

                        const breakEndString = breakPeriod.end.split(':');
                        breakEnd = calendarDate.clone();
                        breakEnd.hour(parseInt(breakEndString[0]));
                        breakEnd.minute(parseInt(breakEndString[1]));

                        const unavailabilityEvent = {
                            title: lang('break'),
                            start: moment(calendarDate.format('YYYY-MM-DD') + ' ' + breakPeriod.start).toDate(),
                            end: moment(calendarDate.format('YYYY-MM-DD') + ' ' + breakPeriod.end).toDate(),
                            allDay: false,
                            color: '#BEBEBE',
                            editable: false,
                            display: 'background',
                            className: 'fc-unavailability fc-break',
                        };

                        calendarEventSource.push(unavailabilityEvent);
                    });

                    calendarDate.add(1, 'day');
                }
            })
            .always(() => {
                $('#loading').css('visibility', '');
                fullCalendar.addEventSource(calendarEventSource);
            });
    }

    function initialize() {
        // Dynamic date formats.
        let columnFormat = {};

        switch (vars('date_format')) {
            case 'DMY':
                columnFormat = 'ddd D/M';
                break;

            case 'MDY':
            case 'YMD':
                columnFormat = 'ddd M/D';
                break;

            default:
                throw new Error('Invalid date format setting provided!', vars('date_format'));
        }

        // Time formats
        let timeFormat = '';
        let slotTimeFormat = '';

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
                throw new Error('Invalid time format setting provided!', vars('time_format'));
        }

        const initialView = window.innerWidth < 468 ? 'timeGridDay' : 'timeGridWeek';

        const firstWeekday = vars('first_weekday');
        const firstWeekdayNumber = App.Utils.Date.getWeekdayId(firstWeekday);

        // Initialize page calendar
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
            eventColor: '#7cbae8',
            slotLabelFormat: slotTimeFormat,
            allDayContent: lang('all_day'),
            selectable: true,
            selectMirror: true,
            themeSystem: 'bootstrap5',
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

        // Trigger once to set the proper footer position after calendar initialization.
        onWindowResize();

        $selectFilterItem.append(new Option(lang('all'), FILTER_TYPE_ALL, true, true));

        $('#insert-working-plan-exception').hide();

        // Fill the select list boxes of the page.
        if (vars('available_providers').length > 0) {
            $('<optgroup/>', {
                'label': lang('providers'),
                'type': 'providers-group',
                'html': vars('available_providers').map((availableProvider) => {
                    const {settings} = availableProvider;

                    return $('<option/>', {
                        'value': availableProvider.id,
                        'type': FILTER_TYPE_PROVIDER,
                        'google-sync': settings.google_sync,
                        'caldav-sync': settings.caldav_sync,
                        'text': availableProvider.first_name + ' ' + availableProvider.last_name,
                    });
                }),
            }).appendTo('#select-filter-item');
        }

        if (vars('available_services').length > 0) {
            $('<optgroup/>', {
                'label': lang('services'),
                'type': 'services-group',
                'html': vars('available_services').map((availableService) =>
                    $('<option/>', {
                        'value': availableService.id,
                        'type': FILTER_TYPE_SERVICE,
                        'text': availableService.name,
                    }),
                ),
            }).appendTo('#select-filter-item');
        }

        // Check permissions.
        if (vars('role_slug') === App.Layouts.Backend.DB_SLUG_PROVIDER) {
            $selectFilterItem
                .find('optgroup:eq(0)')
                .find('option[value="' + vars('user_id') + '"]')
                .prop('selected', true);
        }

        // Add the page event listeners.
        addEventListeners();

        const localSelectFilterItemValue = window.localStorage.getItem('EasyAppointments.SelectFilterItem');

        if (
            localSelectFilterItemValue &&
            $selectFilterItem.find(`option[value="${localSelectFilterItemValue}"]`).length
        ) {
            $selectFilterItem.val(localSelectFilterItemValue).trigger('change');
        } else {
            $reloadAppointments.trigger('click');
        }

        // Display the edit dialog if an appointment hash is provided.
        if (vars('edit_appointment')) {
            const appointment = vars('edit_appointment');

            App.Components.AppointmentsModal.resetModal();

            $appointmentsModal.find('.modal-header h3').text(lang('edit_appointment_title'));
            $appointmentsModal.find('#appointment-id').val(appointment.id);
            $appointmentsModal.find('#select-service').val(appointment.id_services).trigger('change');
            $appointmentsModal.find('#select-provider').val(appointment.id_users_provider);

            // Set the start and end datetime of the appointment.
            const startDatetimeMoment = moment(appointment.start_datetime);
            App.Utils.UI.setDateTimePickerValue(
                $appointmentsModal.find('#start-datetime'),
                startDatetimeMoment.toDate(),
            );

            const endDatetimeMoment = moment(appointment.end_datetime);
            App.Utils.UI.setDateTimePickerValue($appointmentsModal.find('#end-datetime'), endDatetimeMoment.toDate());

            const customer = appointment.customer;
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
            $appointmentsModal.find('#appointment-location').val(appointment.location);
            $appointmentsModal.find('#appointment-status').val(appointment.status);
            $appointmentsModal.find('#appointment-notes').val(appointment.notes);
            $appointmentsModal.find('#customer-notes').val(customer.notes);
            $appointmentsModal.find('#custom-field-1').val(customer.custom_field_1);
            $appointmentsModal.find('#custom-field-2').val(customer.custom_field_2);
            $appointmentsModal.find('#custom-field-3').val(customer.custom_field_3);
            $appointmentsModal.find('#custom-field-4').val(customer.custom_field_4);
            $appointmentsModal.find('#custom-field-5').val(customer.custom_field_5);

            App.Components.ColorSelection.setColor($appointmentsModal.find('#appointment-color'), appointment.color);

            $appointmentsModal.modal('show');

            fullCalendar.gotoDate(moment(appointment.start_datetime).toDate());
        }

        if (!$selectFilterItem.find('option').length) {
            $('#calendar-actions button').prop('disabled', true);
        }

        // Automatically refresh the calendar page every 10 seconds (without loading animation).
        setInterval(() => {
            if ($('.popover').length) {
                return;
            }

            refreshCalendarAppointments(
                $calendar,
                $selectFilterItem.val(),
                $selectFilterItem.find('option:selected').attr('type'),
                fullCalendar.view.activeStart,
                fullCalendar.view.activeEnd,
            );
        }, 60000);
    }

    return {
        initialize,
        FILTER_TYPE_ALL,
        FILTER_TYPE_PROVIDER,
        FILTER_TYPE_SERVICE,
    };
})();
