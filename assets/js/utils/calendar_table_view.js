/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Working plan utility.
 *
 * This module implements the functionality of table calendar view.
 *
 * Old Name: BackendCalendarTableView
 */
App.Utils.CalendarTableView = (function () {
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
    let $filterProvider;
    let $filterService;
    let $selectDate;
    let $popoverTarget;

    const moment = window.moment;
    let lastFocusedEventData;

    /**
     * Add the utility event listeners.
     */
    function addEventListeners() {
        $calendar.on('click', '.calendar-header .btn.previous', () => {
            const dayInterval = $selectFilterItem.val();
            const currentDate = $selectDate.datepicker('getDate');
            const startDate = moment(currentDate).subtract(1, 'days');
            const endDate = startDate.clone().add(dayInterval - 1, 'days');
            $selectDate.datepicker('setDate', startDate.toDate());
            createView(startDate.toDate(), endDate.toDate());
        });

        $calendar.on('click', '.calendar-header .btn.next', () => {
            const dayInterval = $selectFilterItem.val();
            const currentDate = $selectDate.datepicker('getDate');
            const startDate = moment(currentDate).add(1, 'days');
            const endDate = startDate.clone().add(dayInterval - 1, 'days');
            $selectDate.datepicker('setDate', startDate.toDate());
            createView(startDate.toDate(), endDate.toDate());
        });

        $calendarToolbar.on('change', '#select-filter-item', () => {
            const dayInterval = $selectFilterItem.val();
            const currentDate = $selectDate.datepicker('getDate');
            const startDate = moment(currentDate);
            const endDate = startDate.clone().add(dayInterval - 1, 'days');
            createView(startDate.toDate(), endDate.toDate());
        });

        $calendarToolbar.on('click', '#reload-appointments', () => {
            // Fetch the events and place them in the existing HTML format.
            const dayInterval = $selectFilterItem.val();
            const currentDate = $selectDate.datepicker('getDate');
            const startDateMoment = moment(currentDate);
            const startDate = startDateMoment.toDate();
            const endDateMoment = startDateMoment.clone().add(dayInterval - 1, 'days');
            const endDate = endDateMoment.toDate();

            App.Http.Calendar.getCalendarAppointmentsForTableView(startDate, endDate).done((response) => {
                let currentDate = startDate;

                while (currentDate <= endDate) {
                    $('.calendar-view .date-column').each((index, dateColumn) => {
                        const $dateColumn = $(dateColumn);
                        const date = new Date($dateColumn.data('date'));

                        if (moment(currentDate).format('YYYY-MM-DD') !== moment(date).format('YYYY-MM-DD')) {
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

                            createNonWorkingHours(
                                $providerColumn.find('.calendar-wrapper'),
                                $providerColumn.data('provider')
                            );

                            // Add the appointments to the column.
                            /** @var {Array} response.appointments */
                            createAppointments($providerColumn, response.appointments);

                            // Add the unavailabilities to the column.
                            /** @var {Array} response.unavailabilities */
                            createUnavailabilities($providerColumn, response.unavailabilities);

                            // Add the provider breaks to the column.
                            const workingPlan = JSON.parse(provider.settings.working_plan);
                            const day = moment(date).format('dddd').toLowerCase();
                            if (workingPlan[day]) {
                                const breaks = workingPlan[day].breaks;
                                createBreaks($providerColumn, breaks);
                            }
                        });
                    });

                    currentDate = moment(currentDate).add({days: 1}).toDate();
                }

                // setCalendarViewSize();
                App.Layouts.Backend.placeFooterToBottom();
            });
        });

        /**
         * Event: On Window Resize
         */
        $(window).on('resize', () => {
            setCalendarViewSize();
        });

        /**
         * Event: Popover Close Button "Click"
         *
         * Hides the open popover element.
         */
        $calendar.on('click', '.close-popover', (event) => {
            if ($popoverTarget) {
                $popoverTarget.popover('dispose');
            }
        });

        /**
         * Event: Popover Edit Button "Click"
         *
         * Enables the edit dialog of the selected table event.
         */
        $calendar.on('click', '.edit-popover', (event) => {
            if ($popoverTarget) {
                $popoverTarget.popover('dispose');
            }

            let startMoment;
            let endMoment;

            if (lastFocusedEventData.extendedProps.data.workingPlanException) {
                const date = lastFocusedEventData.extendedProps.data.date;
                const workingPlanException = lastFocusedEventData.extendedProps.data.workingPlanException;
                const provider = lastFocusedEventData.extendedProps.data.provider;

                App.Components.WorkingPlanExceptionsModal.edit(date, workingPlanException).done(
                    (date, workingPlanException) => {
                        const successCallback = () => {
                            App.Layouts.Backend.displayNotification(lang('working_plan_exception_saved'));

                            const workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};

                            workingPlanExceptions[date] = workingPlanException;

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
                            null
                        );
                    }
                );
            } else if (lastFocusedEventData.extendedProps.data.is_unavailability === false) {
                const appointment = lastFocusedEventData.extendedProps.data;
                App.Components.AppointmentsModal.resetModal();

                // Apply appointment data and show modal dialog.
                $appointmentsModal.find('.modal-header h3').text(lang('edit_appointment_title'));
                $appointmentsModal.find('#appointment-id').val(appointment.id);
                $appointmentsModal.find('#select-service').val(appointment.id_services).trigger('change');
                $appointmentsModal.find('#select-provider').val(appointment.id_users_provider);

                // Set the start and end datetime of the appointment.
                startMoment = moment(appointment.start_datetime);
                $appointmentsModal.find('#start-datetime').datetimepicker('setDate', startMoment.toDate());

                endMoment = moment(appointment.end_datetime);
                $appointmentsModal.find('#end-datetime').datetimepicker('setDate', endMoment.toDate());

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

                App.Components.ColorSelection.setColor(
                    $appointmentsModal.find('#appointment-color'),
                    appointment.color
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
                $unavailabilitiesModal.find('#unavailability-start').datetimepicker('setDate', startMoment.toDate());
                $unavailabilitiesModal.find('#unavailability-id').val(unavailability.id);
                $unavailabilitiesModal.find('#unavailability-provider').val(unavailability.id_users_provider);
                $unavailabilitiesModal.find('#unavailability-end').datetimepicker('setDate', endMoment.toDate());
                $unavailabilitiesModal.find('#unavailability-notes').val(unavailability.notes);

                $unavailabilitiesModal.modal('show');
            }
        });

        /**
         * Event: Popover Delete Button "Click"
         *
         * Displays a prompt on whether the user wants the appointment to be deleted. If he confirms the
         * deletion then an ajax call is made to the server and deletes the appointment from the database.
         */
        $calendar.on('click', '.delete-popover', (event) => {
            if ($popoverTarget) {
                $popoverTarget.popover('dispose');
            }

            // If id_role parameter exists the popover is an working plan exception.
            if (lastFocusedEventData.extendedProps.data.hasOwnProperty('id_roles')) {
                // Do not display confirmation prompt.

                const date = moment(lastFocusedEventData.start).format('YYYY-MM-DD');

                const providerId = lastFocusedEventData.extendedProps.data.id;

                App.Http.Calendar.deleteWorkingPlanException(date, providerId).done(() => {
                    $('#message-box').dialog('close');

                    const workingPlanExceptions = JSON.parse(
                        lastFocusedEventData.extendedProps.data.settings.working_plan_exceptions
                    );

                    delete workingPlanExceptions[moment(lastFocusedEventData.start).format('YYYY-MM-DD')];

                    lastFocusedEventData.extendedProps.data.settings.working_plan_exceptions =
                        JSON.stringify(workingPlanExceptions);

                    // Refresh calendar event items.
                    $reloadAppointments.trigger('click');
                });
            } else if (!lastFocusedEventData.extendedProps.data.is_unavailability) {
                const buttons = [
                    {
                        text: lang('cancel'),
                        click: () => {
                            $('#message-box').dialog('close');
                        }
                    },
                    {
                        text: lang('delete'),
                        click: () => {
                            const appointmentId = lastFocusedEventData.extendedProps.data.id;

                            const deleteReason = $('#delete-reason').val();

                            App.Http.Calendar.deleteAppointment(appointmentId, deleteReason).done(() => {
                                $('#message-box').dialog('close');

                                // Refresh calendar event items.
                                $reloadAppointments.trigger('click');
                            });
                        }
                    }
                ];

                App.Utils.Message.show(
                    lang('delete_appointment_title'),
                    lang('write_appointment_removal_reason'),
                    buttons
                );

                $('<textarea/>', {
                    'class': 'form-control w-100',
                    'id': 'delete-reason',
                    'rows': '3'
                }).appendTo('#message-box');
            } else {
                // Do not display confirmation prompt.
                const unavailabilityId = lastFocusedEventData.extendedProps.data.id;

                App.Http.Calendar.deleteUnavailability(unavailabilityId).done(() => {
                    $('#message-box').dialog('close');

                    // Refresh calendar event items.
                    $reloadAppointments.trigger('click');
                });
            }
        });
    }

    /**
     * Create table view header container.
     *
     * The header contains the date navigation elements (buttons and datepicker).
     */
    function createHeader() {
        $calendarFilter
            .find('select')
            .empty()
            .append(new Option(`1 ${lang('day')}`, '1'))
            .append(new Option(`3 ${lang('days')}`, '3'));

        const $calendarHeader = $('<div/>', {
            'class': 'calendar-header'
        }).appendTo('#calendar');

        $('<button/>', {
            'class': 'btn btn-xs btn-outline-secondary previous me-2',
            'html': [
                $('<span/>', {
                    'class': 'fas fa-chevron-left'
                })
            ]
        }).appendTo($calendarHeader);

        $selectDate = $('<input/>', {
            'type': 'text',
            'class': 'form-control d-inline-block select-date me-2',
            'value': App.Utils.Date.format(new Date(), vars('date_format'), vars('time_format'), false)
        }).appendTo($calendarHeader);

        $('<button/>', {
            'class': 'btn btn-xs btn-outline-secondary next',
            'html': [
                $('<span/>', {
                    'class': 'fas fa-chevron-right'
                })
            ]
        }).appendTo($calendarHeader);

        let dateFormat;

        switch (vars('date_format')) {
            case 'DMY':
                dateFormat = 'dd/mm/yy';
                break;

            case 'MDY':
                dateFormat = 'mm/dd/yy';
                break;

            case 'YMD':
                dateFormat = 'yy/mm/dd';
                break;

            default:
                throw new Error('Invalid date format setting provided: ' + vars('date_format'));
        }

        const firstWeekday = vars('first_weekday');
        const firstWeekdayNumber = App.Utils.Date.getWeekdayId(firstWeekday);

        $calendarHeader.find('.select-date').datepicker({
            defaultDate: new Date(),
            dateFormat: dateFormat,

            // Translation
            dayNames: [lang('sunday'), lang('monday'), lang('tuesday'), lang('wednesday'),
                lang('thursday'), lang('friday'), lang('saturday')],
            dayNamesShort: [lang('sunday').substring(0, 3), lang('monday').substring(0, 3),
                lang('tuesday').substring(0, 3), lang('wednesday').substring(0, 3),
                lang('thursday').substring(0, 3), lang('friday').substring(0, 3),
                lang('saturday').substring(0, 3)],
            dayNamesMin: [lang('sunday').substring(0, 2), lang('monday').substring(0, 2),
                lang('tuesday').substring(0, 2), lang('wednesday').substring(0, 2),
                lang('thursday').substring(0, 2), lang('friday').substring(0, 2),
                lang('saturday').substring(0, 2)],
            monthNames: [lang('january'), lang('february'), lang('march'), lang('april'),
                lang('may'), lang('june'), lang('july'), lang('august'), lang('september'),
                lang('october'), lang('november'), lang('december')],
            prevText: lang('previous'),
            nextText: lang('next'),
            currentText: lang('now'),
            closeText: lang('close'),
            timeOnlyTitle: lang('select_time'),
            timeText: lang('time'),
            hourText: lang('hour'),
            minuteText: lang('minutes'),
            firstDay: firstWeekdayNumber,
            onSelect: (dateText, instance) => {
                const startDate = new Date(instance.currentYear, instance.currentMonth, instance.currentDay);
                const endDate = moment(startDate).add(parseInt($selectFilterItem.val()) - 1, 'days').toDate();
                createView(startDate, endDate);
            }
        });

        const providers = vars('available_providers').filter(
            (provider) =>
                vars('role_slug') === App.Layouts.Backend.DB_SLUG_ADMIN ||
                (vars('role_slug') === App.Layouts.Backend.DB_SLUG_SECRETARY &&
                    vars('secretary_providers').indexOf(provider.id) !== -1) ||
                (vars('role_slug') === App.Layouts.Backend.DB_SLUG_PROVIDER &&
                    Number(provider.id) === Number(vars('user_id')))
        );

        // Create providers and service filters.
        $('<label/>', {
            'text': lang('provider')
        }).appendTo($calendarHeader);

        $filterProvider = $('<select/>', {
            'id': 'filter-provider',
            'multiple': 'multiple',
            'on': {
                'change': () => {
                    const firstColumnDate = $('.calendar-view .date-column:first').data('date');
                    const startDateMoment = moment(firstColumnDate);
                    const endDateMoment = moment(firstColumnDate).add(parseInt($selectFilterItem.val()) - 1, 'day');
                    createView(startDateMoment.toDate(), endDateMoment.toDate());
                }
            }
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

        const services = vars('available_services').filter((service) => {
            const provider = providers.find((provider) => provider.services.indexOf(service.id) !== -1);

            return vars('role_slug') === App.Layouts.Backend.DB_SLUG_ADMIN || provider;
        });

        $('<label/>', {
            'text': lang('service')
        }).appendTo($calendarHeader);

        $filterService = $('<select/>', {
            'id': 'filter-service',
            'multiple': 'multiple',
            'on': {
                'change': () => {
                    const firstColumnDate = $('.calendar-view .date-column:first').data('date');
                    const startDateMoment = moment(firstColumnDate);
                    const endDateMoment = moment(firstColumnDate).add({days: parseInt($selectFilterItem.val()) - 1});
                    createView(startDateMoment.toDate(), endDateMoment.toDate());
                }
            }
        }).appendTo($calendarHeader);

        services.forEach((service) => {
            $filterService.append(new Option(service.name, service.id));
        });

        App.Utils.UI.initializeDropdown($filterService);
    }

    /**
     * Create table schedule view.
     *
     * This method will destroy any previous instances and create a new view for displaying the appointments in
     * a table format.
     *
     * @param {Date} startDate Start date to be displayed.
     * @param {Date} endDate End date to be displayed.
     */
    function createView(startDate, endDate) {
        // Disable date navigation.
        $('#calendar .calendar-header .btn').addClass('disabled').prop('disabled', true);

        // Remember provider calendar view mode.
        const providerView = {};
        $('.provider-column').each((index, providerColumn) => {
            const $providerColumn = $(providerColumn);
            const providerId = $providerColumn.data('provider').id;
            providerView[providerId] = $providerColumn.find('.calendar-wrapper').data('fullCalendar').view.type;
        });

        $('#calendar .calendar-view').remove();

        App.Layouts.Backend.placeFooterToBottom();

        const $calendarView = $('<div/>', {
            'class': 'calendar-view'
        }).appendTo('#calendar');

        $calendarView.data({
            startDate: moment(startDate).format('YYYY-MM-DD'),
            endDate: moment(endDate).format('YYYY-MM-DD')
        });

        const $wrapper = $('<div/>').appendTo($calendarView);

        App.Http.Calendar.getCalendarAppointmentsForTableView(startDate, endDate).done((response) => {
            let currentDate = startDate;

            while (currentDate <= endDate) {
                createDateColumn($wrapper, currentDate, response);
                currentDate = moment(currentDate).add({days: 1}).toDate();
            }

            setCalendarViewSize();

            App.Layouts.Backend.placeFooterToBottom();

            // Activate calendar navigation.
            $('#calendar .calendar-header .btn').removeClass('disabled').prop('disabled', false);

            // Apply provider calendar view mode.
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
     * Create Date Column Container
     *
     * This element will contain the provider columns.
     *
     * @param {jQuery} $wrapper The wrapper div element of the table view.
     * @param {Date} date Selected date for the column.
     * @param {Object[]} events Events to be displayed on this date.
     */
    function createDateColumn($wrapper, date, events) {
        const $dateColumn = $('<div/>', {
            'class': 'date-column'
        }).appendTo($wrapper);

        $dateColumn.data('date', date.getTime());

        $('<h5/>', {
            'class': 'date-column-title',
            'text': App.Utils.Date.format(date, vars('date_format'), vars('time_format'))
        }).appendTo($dateColumn);

        const filterProviderIds = $filterProvider.val();
        const filterServiceIds = $filterService.val();

        let providers = vars('available_providers').filter((provider) => {
            const servedServiceIds = provider.services.filter((serviceId) => {
                const matches = filterServiceIds.filter(
                    (filterServiceId) => Number(serviceId) === Number(filterServiceId)
                );

                return matches.length;
            });

            return (
                (!filterProviderIds.length && !filterServiceIds.length) ||
                (filterProviderIds.length &&
                    !filterServiceIds.length &&
                    filterProviderIds.indexOf(provider.id) !== -1) ||
                (!filterProviderIds.length && filterServiceIds.length && servedServiceIds.length) ||
                (filterProviderIds.length &&
                    filterServiceIds.length &&
                    servedServiceIds.length &&
                    filterProviderIds.indexOf(provider.id) !== -1)
            );
        });

        if (vars('role_slug') === 'provider') {
            vars('available_providers').forEach((provider) => {
                if (Number(provider.id) === Number(vars('user_id'))) {
                    providers = [provider];
                }
            });
        }

        if (vars('role_slug') === 'secretary') {
            providers = [];
            vars('available_providers').forEach((provider) => {
                if (vars('secretary_providers').indexOf(provider.id) > -1) {
                    providers.push(provider);
                }
            });
        }

        providers.forEach((provider) => {
            createProviderColumn($dateColumn, date, provider, events);
        });
    }

    /**
     * Create Provider Column Container
     *
     * @param {jQuery} $dateColumn Element to container the provider's column.
     * @param {Date} date Selected date for the column.
     * @param {Object} provider Contains the provider data.
     * @param {Object[]} events Events to be displayed on this date.
     */
    function createProviderColumn($dateColumn, date, provider, events) {
        if (provider.services.length === 0) {
            return;
        }

        const $providerColumn = $('<div/>', {
            'class': 'provider-column'
        }).appendTo($dateColumn);

        $providerColumn.data('provider', provider);

        // Create calendar.
        createCalendar($providerColumn, date, provider);

        // Create non-working hours.
        createNonWorkingHours($providerColumn.find('.calendar-wrapper'), provider);

        // Add the appointments to the column.
        /** @var {Array} events.appointments */
        createAppointments($providerColumn, events.appointments);

        // Add the unavailabilities to the column.
        /** @var {Array} events.unavailabilities */
        createUnavailabilities($providerColumn, events.unavailabilities);

        App.Layouts.Backend.placeFooterToBottom();
    }

    /**
     * Get Calendar Component Height
     *
     * This method calculates the proper calendar height, in order to be displayed correctly, even when the browser
     * window is resizing.
     *
     * @return {Number} Returns the calendar element height in pixels.
     */
    function getCalendarHeight() {
        const result = window.innerHeight - $footer.outerHeight() - $header.outerHeight() - 60; // 60 for fine tuning
        return result > 500 ? result : 500; // Minimum height is 500px
    }

    function createCalendar($providerColumn, goToDate, provider) {
        const $wrapper = $('<div/>', {
            'class': 'calendar-wrapper'
        }).appendTo($providerColumn);

        let columnFormat = '';

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
                timeFormat = 'H:mm';
                slotTimeFormat = 'H';
                break;
            case 'regular':
                timeFormat = 'h:mm a';
                slotTimeFormat = 'h a';
                break;
            default:
                throw new Error('Invalid time format setting provided!' + vars('time_format'));
        }

        const firstWeekday = vars('first_weekday');
        const firstWeekdayNumber = App.Utils.Date.getWeekdayId(firstWeekday);

        const fullCalendar = new FullCalendar.Calendar($wrapper[0], {
            initialView: 'timeGridDay',
            height: getCalendarHeight(),
            editable: true,
            firstDay: firstWeekdayNumber,
            slotDuration: '00:15:00',
            snapDuration: '00:15:00',
            slotLabelInterval: '01:00',
            eventTimeFormat: timeFormat,
            eventTextColor: '#333',
            slotLabelFormat: slotTimeFormat,
            allDayContent: lang('all_day'),
            dayHeaderFormat: columnFormat,
            headerToolbar: {
                left: 'listDay,timeGridDay',
                center: '',
                right: ''
            },
            // Selectable
            selectable: true,
            selectHelper: true,
            select: (info) => {
                if (info.allDay) {
                    return;
                }

                $('#insert-appointment').trigger('click');

                // Preselect service & provider.
                const $providerColumn = $(info.jsEvent.target).parents('.provider-column');
                const providerId = $providerColumn.data('provider').id;

                const provider = vars('available_providers').find(
                    (provider) => Number(provider.id) === Number(providerId)
                );

                const service = vars('available_services').find(
                    (service) => provider.services.indexOf(service.id) !== -1
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
                $('#start-datetime').datepicker('setDate', info.start);
                $('#end-datetime').datepicker('setDate', App.Pages.Calendar.getSelectionEndDate(info));

                return false;
            },

            buttonText: {
                today: lang('today'),
                day: lang('day'),
                week: lang('week'),
                month: lang('month'),
                timeGridDay: lang('calendar'),
                listDay: lang('list')
            },

            // Calendar events need to be declared on initialization.
            eventClick: onEventClick,
            eventResize: onEventResize,
            eventDrop: onEventDrop
            // datesSet: onDatesSet
        });

        fullCalendar.render();

        $wrapper.data('fullCalendar', fullCalendar);

        fullCalendar.gotoDate(goToDate);

        $('<h6/>', {
            'text': provider.first_name + ' ' + provider.last_name
        }).prependTo($providerColumn);
    }

    // function onDatesSet() {
    //     $(element).fullCalendar('option', 'height', getCalendarHeight());
    // }

    function createNonWorkingHours($calendar, provider) {
        const workingPlan = JSON.parse(provider.settings.working_plan);
        const workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};
        const view = $calendar.data('fullCalendar').view;
        const start = moment(view.currentStart).clone();
        const end = moment(view.currentEnd).clone();
        const selDayName = start.format('dddd').toLowerCase();
        const selDayDate = start.format('YYYY-MM-DD');
        const calendarEventSource = [];

        if (workingPlanExceptions[selDayDate]) {
            workingPlan[selDayName] = workingPlanExceptions[selDayDate];

            const workingPlanExceptionStart = selDayDate + ' ' + workingPlan[selDayName].start;
            const workingPlanExceptionEnd = selDayDate + ' ' + workingPlan[selDayName].end;

            const workingPlanExceptionEvent = {
                title: lang('working_plan_exception'),
                start: moment(workingPlanExceptionStart, 'YYYY-MM-DD HH:mm', true),
                end: moment(workingPlanExceptionEnd, 'YYYY-MM-DD HH:mm', true).add(1, 'day'),
                allDay: true,
                color: '#879DB4',
                editable: false,
                className: 'fc-working-plan-exception fc-custom',
                data: {
                    date: selDayDate,
                    workingPlanException: workingPlanExceptions[selDayDate],
                    provider: provider
                }
            };

            calendarEventSource.push(workingPlanExceptionEvent);
        }

        if (workingPlan[selDayName] === null) {
            const nonWorkingDay = {
                title: lang('not_working'),
                start: start,
                end: end,
                allDay: false,
                color: '#BEBEBE',
                editable: false,
                className: 'fc-unavailability'
            };

            calendarEventSource.push(nonWorkingDay);

            $calendar.data('fullCalendar').addEventSource(calendarEventSource);

            return;
        }

        const workDateStart = moment(start.format('YYYY-MM-DD') + ' ' + workingPlan[selDayName].start);

        if (start < workDateStart) {
            const unavailabilityPeriod = {
                title: lang('not_working'),
                start: start.toDate(),
                end: workDateStart.toDate(),
                allDay: false,
                color: '#BEBEBE',
                editable: false,
                className: 'fc-unavailability'
            };

            calendarEventSource.push(unavailabilityPeriod);
        }

        // Add unavailability period after work ends.
        const workDateEnd = moment(start.format('YYYY-MM-DD') + ' ' + workingPlan[selDayName].end);

        if (end > workDateEnd) {
            const unavailabilityPeriod = {
                title: lang('not_working'),
                start: workDateEnd.toDate(),
                end: end.toDate(),
                allDay: false,
                color: '#BEBEBE',
                editable: false,
                className: 'fc-unavailability'
            };

            calendarEventSource.push(unavailabilityPeriod);
        }

        // Add unavailability periods for breaks.
        let breakStart;
        let breakEnd;

        workingPlan[selDayName].breaks.forEach((currentBreak) => {
            breakStart = moment(start.format('YYYY-MM-DD') + ' ' + currentBreak.start);
            breakEnd = moment(start.format('YYYY-MM-DD') + ' ' + currentBreak.end);

            const unavailabilityPeriod = {
                title: lang('break'),
                start: breakStart.toDate(),
                end: breakEnd.toDate(),
                allDay: false,
                color: '#BEBEBE',
                editable: false,
                className: 'fc-unavailability fc-break'
            };

            calendarEventSource.push(unavailabilityPeriod);
        });

        $calendar.data('fullCalendar').addEventSource(calendarEventSource);
    }

    /**
     * Create Appointment Events
     *
     * This method will add the appointment events on the table view.
     *
     * @param {jQuery} $providerColumn The provider column container.
     * @param {Object[]} appointments Contains the appointment events data.
     */
    function createAppointments($providerColumn, appointments) {
        if (appointments.length === 0) {
            return;
        }

        const filterServiceIds = $filterService.val();

        appointments = appointments.filter(
            (appointment) => !filterServiceIds.length || filterServiceIds.indexOf(appointment.id_services) !== -1
        );

        const calendarEvents = [];

        for (const index in appointments) {
            const appointment = appointments[index];

            if (Number(appointment.id_users_provider) !== Number($providerColumn.data('provider').id)) {
                continue;
            }

            const title = [
                appointment.service.name
            ];

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

            calendarEvents.push({
                id: appointment.id,
                title: title.join(' - '),
                start: moment(appointment.start_datetime).toDate(),
                end: moment(appointment.end_datetime).toDate(),
                allDay: false,
                color: appointment.color,
                data: appointment // Store appointment data for later use.
            });
        }

        $providerColumn.find('.calendar-wrapper').data('fullCalendar').addEventSource(calendarEvents);
    }

    /**
     * Create Unavailability Events
     *
     * This method will add the unavailabilities on the table view.
     *
     * @param {jQuery} $providerColumn The provider column container.
     * @param {Object[]} unavailabilities Contains the unavailabilities data.
     */
    function createUnavailabilities($providerColumn, unavailabilities) {
        if (unavailabilities.length === 0) {
            return;
        }

        const calendarEventSource = [];

        for (const index in unavailabilities) {
            const unavailability = unavailabilities[index];

            if (Number(unavailability.id_users_provider) !== Number($providerColumn.data('provider').id)) {
                continue;
            }

            const event = {
                title: lang('unavailability'),
                start: moment(unavailability.start_datetime).toDate(),
                end: moment(unavailability.end_datetime).toDate(),
                allDay: false,
                color: '#879DB4',
                editable: true,
                className: 'fc-unavailability fc-custom',
                data: unavailability
            };

            calendarEventSource.push(event);
        }

        $providerColumn.find('.calendar-wrapper').data('fullCalendar').addEventSource(calendarEventSource);
    }

    /**
     * Create break events in the table view.
     *
     * @param {jQuery} $providerColumn The provider column container.
     * @param {Object[]} breaks Contains the break events data.
     */
    function createBreaks($providerColumn, breaks) {
        if (breaks.length === 0) {
            return;
        }

        const currentDate = new Date($providerColumn.parents('.date-column').data('date'));
        const $tbody = $providerColumn.find('table tbody');

        for (const index in breaks) {
            const entry = breaks[index];
            const startHour = entry.start.split(':');
            const eventDate = new Date(
                currentDate.getFullYear(),
                currentDate.getMonth(),
                currentDate.getDate(),
                startHour[0],
                startHour[1]
            );
            const endHour = entry.end.split(':');
            const endDate = new Date(
                currentDate.getFullYear(),
                currentDate.getMonth(),
                currentDate.getDate(),
                endHour[0],
                endHour[1]
            );
            const eventDuration = Math.round((endDate - eventDate) / 60000);
            const $event = $('<div/>', {
                'class': 'event unavailability break'
            });

            $event.html(
                lang('break') +
                ' <span class="hour">' +
                moment(eventDate).format('HH:mm') +
                '</span> (' +
                eventDuration +
                "')"
            );

            $event.data(entry);

            $tbody.find('tr').each((index, tr) => {
                const $td = $(tr).find('td:first');

                const cellDate = moment(currentDate)
                    .set({
                        hour: parseInt($td.text().split(':')[0]),
                        minute: parseInt($td.text().split(':')[1])
                    })
                    .toDate();

                if (eventDate < cellDate) {
                    // Remove the hour from the event if it is the same as the row.
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
        }
    }

    /**
     * Get the event notes for the popup widget.
     *
     * @param {*|Event} event
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
        if ($popoverTarget) {
            $popoverTarget.popover('dispose');
        }

        let $html;
        let displayEdit;
        let displayDelete;

        // Depending on where the user clicked the event (title or empty space) we need to use different selectors to
        // reach the parent element.
        const $target = $(info.el);

        if ($target.hasClass('fc-unavailability')) {
            displayEdit =
                $target.hasClass('fc-custom') && vars('privileges').appointments.edit === true ? '' : 'd-none';
            displayDelete =
                $target.hasClass('fc-custom') && vars('privileges').appointments.delete === true ? '' : 'd-none'; // Same value at the time.

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('start')
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            moment(info.event.start).format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('end')
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('notes')
                    }),
                    $('<span/>', {
                        'text': getEventNotes(info.event)
                    }),
                    $('<br/>'),

                    $('<hr/>'),

                    $('<div/>', {
                        'class': 'd-flex justify-content-center',
                        'html': [
                            $('<button/>', {
                                'class': 'close-popover btn btn-outline-secondary me-2',
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-ban me-2'
                                    }),
                                    $('<span/>', {
                                        'text': lang('close')
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'delete-popover btn btn-outline-secondary me-2 ' + displayDelete,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-trash-alt me-2'
                                    }),
                                    $('<span/>', {
                                        'text': lang('delete')
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'edit-popover btn btn-primary ' + displayEdit,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-edit me-2'
                                    }),
                                    $('<span/>', {
                                        'text': lang('edit')
                                    })
                                ]
                            })
                        ]
                    })
                ]
            });
        } else if ($target.hasClass('fc-working-plan-exception')) {
            displayEdit =
                $target.hasClass('fc-custom') && vars('privileges').appointments.edit === true ? '' : 'd-none'; // Same value at the time.

            displayDelete =
                $target.hasClass('fc-custom') && vars('privileges').appointments.delete === true ? '' : 'd-none'; // Same value at the time.

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('provider')
                    }),
                    $('<span/>', {
                        'text': info.event.extendedProps.data
                            ? info.event.extendedProps.data.provider.first_name +
                            ' ' +
                            info.event.extendedProps.data.provider.last_name
                            : '-'
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('start')
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            info.event.extendedProps.data.date +
                            ' ' +
                            info.event.extendedProps.data.workingPlanException.start,
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('end')
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            info.event.extendedProps.data.date +
                            ' ' +
                            info.event.extendedProps.data.workingPlanException.end,
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('timezone')
                    }),
                    $('<span/>', {
                        'text': vars('timezones')[info.event.extendedProps.data.provider.timezone]
                    }),
                    $('<br/>'),

                    $('<hr/>'),

                    $('<div/>', {
                        'class': 'd-flex justify-content-center',
                        'html': [
                            $('<button/>', {
                                'class': 'close-popover btn btn-outline-secondary me-2',
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-ban me-2'
                                    }),
                                    $('<span/>', {
                                        'text': lang('close')
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'delete-popover btn btn-outline-secondary me-2 ' + displayDelete,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-trash-alt me-2'
                                    }),
                                    $('<span/>', {
                                        'text': lang('delete')
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'edit-popover btn btn-primary ' + displayEdit,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-edit me-2'
                                    }),
                                    $('<span/>', {
                                        'text': lang('edit')
                                    })
                                ]
                            })
                        ]
                    })
                ]
            });
        } else {
            displayEdit = vars('privileges').appointments.edit === true ? '' : 'd-none';
            displayDelete = vars('privileges').appointments.delete === true ? '' : 'd-none';

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
                        'text': lang('start')
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            moment(info.event.start).format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('end')
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('timezone')
                    }),
                    $('<span/>', {
                        'text': vars('timezones')[info.event.extendedProps.data.provider.timezone]
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('status')
                    }),
                    $('<span/>', {
                        'text': info.event.extendedProps.data.status || '-'
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('service')
                    }),
                    $('<span/>', {
                        'text': info.event.extendedProps.data.service.name
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('provider')
                    }),
                    App.Utils.CalendarEventPopover.renderMapIcon(info.event.extendedProps.data.provider),
                    $('<span/>', {
                        'text':
                            info.event.extendedProps.data.provider.first_name +
                            ' ' +
                            info.event.extendedProps.data.provider.last_name
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('customer')
                    }),
                    App.Utils.CalendarEventPopover.renderMapIcon(info.event.extendedProps.data.customer),
                    $('<span/>', {
                        'text': customerInfo.length ? customerInfo.join(' ') : '-'
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('email')
                    }),
                    App.Utils.CalendarEventPopover.renderMailIcon(info.event.extendedProps.data.customer.email),
                    $('<span/>', {
                        'text': info.event.extendedProps.data.customer.email || '-'
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('phone')
                    }),
                    App.Utils.CalendarEventPopover.renderPhoneIcon(info.event.extendedProps.data.customer.phone_number),
                    $('<span/>', {
                        'text': info.event.extendedProps.data.customer.phone_number || '-'
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block me-2',
                        'text': lang('notes')
                    }),
                    $('<span/>', {
                        'text': getEventNotes(info.event)
                    }),
                    $('<br/>'),

                    $('<hr/>'),

                    $('<div/>', {
                        'class': 'd-flex justify-content-center',
                        'html': [
                            $('<button/>', {
                                'class': 'close-popover btn btn-outline-secondary me-2',
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-ban me-2'
                                    }),
                                    $('<span/>', {
                                        'text': lang('close')
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'delete-popover btn btn-outline-secondary me-2 ' + displayDelete,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-trash-alt me-2'
                                    }),
                                    $('<span/>', {
                                        'text': lang('delete')
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'edit-popover btn btn-primary ' + displayEdit,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-edit me-2'
                                    }),
                                    $('<span/>', {
                                        'text': lang('edit')
                                    })
                                ]
                            })
                        ]
                    })
                ]
            });
        }

        $target.popover({
            placement: 'top',
            title: info.event.title,
            content: $html,
            html: true,
            container: '#calendar',
            trigger: 'manual'
        });

        lastFocusedEventData = info.event;

        $target.popover('show');

        $popoverTarget = $target;

        // Fix popover position.
        const $newPopover = $calendar.find('.popover');

        if ($newPopover.length > 0 && $newPopover.position().top < 200) {
            $newPopover.css('top', '200px');
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

        if ($notification.is(':visible')) {
            $notification.hide('bind');
        }

        let successCallback;

        if (info.event.extendedProps.data.is_unavailability === false) {
            // Prepare appointment data.
            info.event.extendedProps.data.end_datetime = moment(info.event.extendedProps.data.end_datetime)
                .add({days: info.endDelta.days, milliseconds: info.endDelta.milliseconds})
                .format('YYYY-MM-DD HH:mm:ss');

            const appointment = {...info.event.extendedProps.data};

            // Must delete the following because only appointment data should be provided to the AJAX call.
            delete appointment.customer;
            delete appointment.provider;
            delete appointment.service;

            // Success callback
            successCallback = () => {
                // Display success notification to user.
                const undoFunction = () => {
                    appointment.end_datetime = info.event.extendedProps.data.end_datetime = moment(
                        appointment.end_datetime
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
                        'function': undoFunction
                    }
                ]);

                $footer.css('position', 'static'); // Footer position fix.

                // Update the event data for later use.
                info.event.setProp('data', event.extendedProps.data);
            };

            // Update appointment data.
            App.Http.Calendar.saveAppointment(appointment, null, successCallback);
        } else {
            // Update unavailability time period.
            const unavailability = {
                id: info.event.extendedProps.data.id,
                start_datetime: moment(info.event.start).format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'),
                id_users_provider: info.event.extendedProps.data.id_users_provider
            };

            info.event.extendedProps.data.end_datetime = unavailability.end_datetime;

            // Define success callback function.
            successCallback = () => {
                // Display success notification to user.
                const undoFunction = () => {
                    unavailability.end_datetime = info.event.extendedProps.data.end_datetime = moment(
                        unavailability.end_datetime
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
                        'function': undoFunction
                    }
                ]);

                $footer.css('position', 'static'); // Footer position fix.

                // Update the event data for later use.
                info.event.setProp('data', info.event.extendedProps.data);
            };

            App.Http.Calendar.saveUnavailability(unavailability, successCallback);
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

        if (info.event.extendedProps.data.is_unavailability === false) {
            // Prepare appointment data.
            const appointment = {...info.event.extendedProps.data};

            // Must delete the following because only appointment data should be provided to the ajax call.
            delete appointment.customer;
            delete appointment.provider;
            delete appointment.service;

            appointment.start_datetime = moment(appointment.start_datetime)
                .add({days: info.endDelta.days(), hours: info.endDelta.hours(), minutes: info.endDelta.minutes()})
                .format('YYYY-MM-DD HH:mm:ss');

            appointment.end_datetime = moment(appointment.end_datetime)
                .add({days: info.endDelta.days(), hours: info.endDelta.hours(), minutes: info.endDelta.minutes()})
                .format('YYYY-MM-DD HH:mm:ss');

            info.event.extendedProps.data.start_datetime = appointment.start_datetime;
            info.event.extendedProps.data.end_datetime = appointment.end_datetime;

            // Define success callback function.
            successCallback = () => {
                // Define the undo function, if the user needs to reset the last change.
                const undoFunction = () => {
                    appointment.start_datetime = moment(appointment.start_datetime)
                        .add({days: -info.endDelta.days, milliseconds: -info.endDelta.milliseconds})
                        .format('YYYY-MM-DD HH:mm:ss');

                    appointment.end_datetime = moment(appointment.end_datetime)
                        .add({days: -info.endDelta.days, milliseconds: -info.endDelta.milliseconds})
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
                        'function': undoFunction
                    }
                ]);

                $footer.css('position', 'static'); // Footer position fix.
            };

            // Update appointment data.
            App.Http.Calendar.saveAppointment(appointment, null, successCallback);
        } else {
            // Update unavailability time period.
            const unavailability = {
                id: info.event.extendedProps.data.id,
                start_datetime: moment(info.event.start).format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: moment(info.event.end).format('YYYY-MM-DD HH:mm:ss'),
                id_users_provider: info.event.extendedProps.data.id_users_provider
            };

            successCallback = () => {
                const undoFunction = () => {
                    unavailability.start_datetime = moment(unavailability.start_datetime)
                        .add({days: -info.endDelta.days, milliseconds: -info.endDelta.milliseconds})
                        .format('YYYY-MM-DD HH:mm:ss');

                    unavailability.end_datetime = moment(unavailability.end_datetime)
                        .add({days: -info.endDelta.days, milliseconds: -info.endDelta.milliseconds})
                        .format('YYYY-MM-DD HH:mm:ss');

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
                        function: undoFunction
                    }
                ]);

                $footer.css('position', 'static'); // Footer position fix.
            };

            App.Http.Calendar.saveUnavailability(unavailability, successCallback);
        }
    }

    /**
     * Set Table Calendar View
     *
     * This method will set the optimal size in the calendar view elements in order to fit in the page without
     * using scrollbars.
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

    /**
     * Initialize Page
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

        // Hide Google Calendar Sync buttons cause they can not be used within this view.
        $('#enable-sync, #google-sync').hide();
    }

    return {
        initialize
    };
})();
