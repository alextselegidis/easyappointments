/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

/**
 * Working plan utility.
 *
 * This module implements the functionality of table calendar view.
 *
 * Old Name: BackendCalendarTableView
 */
App.Utils.CalendarTableView = (function () {
    const $selectFilterItem = $('#select-filter-item');
    const $selectService = $('#select-service');
    const $selectProvider = $('#select-provider');
    const $appointmentsModal = $('#appointments-modal');
    const $unavailabilitiesModal = $('#unavailabilities-modal');
    let $filterProvider;
    let $filterService;
    let $selectDate;
    let lastFocusedEventData;

    /**
     * Add the utility event listeners.
     */
    function addEventListeners() {
        const $calendarToolbar = $('#calendar-toolbar');
        const $calendar = $('#calendar');

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
            // Remove all the events from the tables.
            $('.calendar-view .event').remove();

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

                            $providerColumn.find('.calendar-wrapper').fullCalendar('removeEvents');

                            createNonWorkingHours(
                                $providerColumn.find('.calendar-wrapper'),
                                $providerColumn.data('provider')
                            );

                            // Add the appointments to the column.
                            createAppointments($providerColumn, response.appointments);

                            // Add the unavailability events to the column.
                            createUnavailabilityEvents($providerColumn, response.unavailability_events);

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
            $(event.target).parents('.popover').popover('dispose');
        });

        /**
         * Event: Popover Edit Button "Click"
         *
         * Enables the edit dialog of the selected table event.
         */
        $calendar.on('click', '.edit-popover', (event) => {
            $(event.target).parents('.popover').popover('dispose');

            let startMoment;
            let endMoment;

            if (lastFocusedEventData.data.workingPlanException) {
                const date = lastFocusedEventData.data.date;
                const workingPlanException = lastFocusedEventData.data.workingPlanException;
                const provider = lastFocusedEventData.data.provider;

                App.Components.WorkingPlanExceptionsModal.edit(date, workingPlanException).done(
                    (date, workingPlanException) => {
                        const successCallback = () => {
                            App.Layouts.Backend.displayNotification(App.Lang.working_plan_exception_saved);

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

                            $selectFilterItem.trigger('change'); // Update the calendar.
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
            } else if (lastFocusedEventData.data.is_unavailable === '0') {
                const appointment = lastFocusedEventData.data;
                BackendCalendarAppointmentsModal.resetAppointmentDialog();

                // Apply appointment data and show modal dialog.
                $appointmentsModal.find('.modal-header h3').text(App.Lang.edit_appointment_title);
                $appointmentsModal.find('#appointment-id').val(appointment.id);
                $appointmentsModal.find('#select-service').val(appointment.id_services).trigger('change');
                $appointmentsModal.find('#select-provider').val(appointment.id_users_provider);

                // Set the start and end datetime of the appointment.
                startMoment = moment(appointment.start_datetime);
                $appointmentsModal.find('#start-datetime').datetimepicker('setDate', startMoment);

                endMoment = moment(appointment.end_datetime);
                $appointmentsModal.find('#end-datetime').datetimepicker('setDate', endMoment);

                const customer = appointment.customer;
                $appointmentsModal.find('#customer-id').val(appointment.id_users_customer);
                $appointmentsModal.find('#first-name').val(customer.first_name);
                $appointmentsModal.find('#last-name').val(customer.last_name);
                $appointmentsModal.find('#email').val(customer.email);
                $appointmentsModal.find('#phone-number').val(customer.phone_number);
                $appointmentsModal.find('#address').val(customer.address);
                $appointmentsModal.find('#city').val(customer.city);
                $appointmentsModal.find('#zip-code').val(customer.zip_code);
                $appointmentsModal.find('#appointment-location').val(appointment.location);
                $appointmentsModal.find('#appointment-notes').val(appointment.notes);
                $appointmentsModal.find('#customer-notes').val(customer.notes);

                $appointmentsModal.modal('show');
            } else {
                const unavailable = lastFocusedEventData.data;

                // Replace string date values with actual date objects.
                unavailable.start_datetime = lastFocusedEventData.start.format('YYYY-MM-DD HH:mm:ss');
                startMoment = moment(unavailable.start_datetime);
                unavailable.end_datetime = lastFocusedEventData.end.format('YYYY-MM-DD HH:mm:ss');
                endMoment = moment(unavailable.end_datetime);

                BackendCalendarUnavailabilityEventsModal.resetUnavailableDialog();

                // Apply unavailable data to dialog.
                $unavailabilitiesModal.find('.modal-header h3').text('Edit Unavailable Period');
                $unavailabilitiesModal.find('#unavailable-start').datetimepicker('setDate', startMoment);
                $unavailabilitiesModal.find('#unavailable-id').val(unavailable.id);
                $unavailabilitiesModal.find('#unavailable-provider').val(unavailable.id_users_provider);
                $unavailabilitiesModal.find('#unavailable-end').datetimepicker('setDate', endMoment);
                $unavailabilitiesModal.find('#unavailable-notes').val(unavailable.notes);

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
            $(event.target).parents('.popover').popover('dispose'); // Hide the popover.

            // If id_role parameter exists the popover is an working plan exception.
            if (lastFocusedEventData.data.hasOwnProperty('id_roles')) {
                // Do not display confirmation prompt.

                const date = lastFocusedEventData.start.format('YYYY-MM-DD');

                const providerId = lastFocusedEventData.data.id;

                App.Http.Calendar.deleteWorkingPlanException(date, providerId).done(() => {
                    $('#message-box').dialog('close');

                    const workingPlanExceptions = JSON.parse(
                        lastFocusedEventData.data.settings.working_plan_exceptions
                    );

                    delete workingPlanExceptions[lastFocusedEventData.start.format('YYYY-MM-DD')];

                    lastFocusedEventData.data.settings.working_plan_exceptions = JSON.stringify(workingPlanExceptions);

                    // Refresh calendar event items.
                    $selectFilterItem.trigger('change');
                });
            } else if (lastFocusedEventData.data.is_unavailable === '0') {
                const buttons = [
                    {
                        text: App.Lang.cancel,
                        click: () => {
                            $('#message-box').dialog('close');
                        }
                    },
                    {
                        text: 'OK',
                        click: () => {
                            const appointmentId = lastFocusedEventData.data.id;

                            const deleteReason = $('#delete-reason').val();

                            App.Http.Calendar.deleteAppointment(appointmentId, deleteReason).done(() => {
                                $('#message-box').dialog('close');

                                // Refresh calendar event items.
                                $selectFilterItem.trigger('change');
                            });
                        }
                    }
                ];

                App.Utils.Message.show(
                    App.Lang.delete_appointment_title,
                    App.Lang.write_appointment_removal_reason,
                    buttons
                );

                $('<textarea/>', {
                    'class': 'form-control w-100',
                    'id': 'delete-reason',
                    'rows': '3'
                }).appendTo('#message-box');
            } else {
                // Do not display confirmation prompt.
                const unavailableId = lastFocusedEventData.data.id;

                App.Http.Calendar.deleteUnavailable(unavailableId).done(() => {
                    $('#message-box').dialog('close');

                    // Refresh calendar event items.
                    $selectFilterItem.trigger('change');
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
        const $calendarFilter = $('#calendar-filter');

        $calendarFilter
            .find('select')
            .empty()
            .append(new Option('1 ' + App.Lang.day, 1))
            .append(new Option('3 ' + App.Lang.days, 3));

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

        $calendarHeader.find('.select-date').datepicker({
            defaultDate: new Date(),
            dateFormat: dateFormat,
            onSelect: (dateText, instance) => {
                const startDate = new Date(instance.currentYear, instance.currentMonth, instance.currentDay);
                const endDate = new Date(startDate.getTime()).add({days: parseInt($selectFilterItem.val()) - 1});
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
            'text': App.Lang.provider
        }).appendTo($calendarHeader);

        $filterProvider = $('<select/>', {
            'id': 'filter-provider',
            'multiple': 'multiple',
            'on': {
                'change': () => {
                    const firstColumnDate = $('.calendar-view .date-column:first').data('date');
                    const startDateMoment = moment(firstColumnDate);
                    const endDateMoment = moment(firstColumnDate).add({
                        days: parseInt($selectDate.val()) - 1
                    });
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

        $filterProvider.select2();

        const services = vars('available_services').filter((service) => {
            const provider = providers.find((provider) => provider.services.indexOf(service.id) !== -1);

            return vars('role_slug') === App.Layouts.Backend.DB_SLUG_ADMIN || provider;
        });

        $('<label/>', {
            'text': App.Lang.service
        }).appendTo($calendarHeader);

        $filterService = $('<select/>', {
            'id': 'filter-service',
            'multiple': 'multiple',
            'on': {
                'change': () => {
                    const firstColumnDate = $('.calendar-view .date-column:first').data('date');
                    const startDateMoment = moment(firstColumnDate);
                    const endDateMoment = moment(firstColumnDate).add({days: parseInt($selectDate.val()) - 1});
                    createView(startDateMoment.toDate(), endDateMoment.toDate());
                }
            }
        }).appendTo($calendarHeader);

        services.forEach((service) => {
            $filterService.append(new Option(service.name, service.id));
        });

        $filterService.select2();
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
            providerView[providerId] = $providerColumn.find('.calendar-wrapper').fullCalendar('getView').name;
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
                    .fullCalendar('changeView', providerView[providerId] || 'agendaDay');
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

        // Create non working hours.
        createNonWorkingHours($providerColumn.find('.calendar-wrapper'), provider);

        // Add the appointments to the column.
        createAppointments($providerColumn, events.appointments);

        // Add the unavailability events to the column.
        createUnavailabilityEvents($providerColumn, events.unavailability_events);

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
        const result = window.innerHeight - $('#footer').outerHeight() - $('#header').outerHeight() - 60; // 60 for fine tuning
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
                slotTimeFormat = 'H(:mm)';
                break;
            case 'regular':
                timeFormat = 'h:mm a';
                slotTimeFormat = 'h(:mm) a';
                break;
            default:
                throw new Error('Invalid time format setting provided!' + vars('time_format'));
        }

        const firstWeekday = vars('first_weekday');
        const firstWeekdayNumber = App.Utils.Date.getWeekdayId(firstWeekday);

        $wrapper.fullCalendar({
            defaultView: 'agendaDay',
            height: getCalendarHeight(),
            editable: true,
            timeFormat: timeFormat,
            slotLabelFormat: slotTimeFormat,
            allDaySlot: true,
            columnFormat: columnFormat,
            firstDay: firstWeekdayNumber,
            snapDuration: '00:15:00',
            header: {
                left: 'listDay,agendaDay',
                center: '',
                right: ''
            },
            // Selectable
            selectable: true,
            selectHelper: true,
            select: (start, end, jsEvent) => {
                if (!start.hasTime() || !end.hasTime()) {
                    return;
                }

                $('#insert-appointment').trigger('click');

                // Preselect service & provider.
                const $providerColumn = $(jsEvent.target).parents('.provider-column');
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
                $('#start-datetime').datepicker('setDate', new Date(start.format('YYYY/MM/DD HH:mm:ss')));
                $('#end-datetime').datepicker('setDate', new Date(end.format('YYYY/MM/DD HH:mm:ss')));

                return false;
            },

            // Translations
            monthNames: [
                App.Lang.january,
                App.Lang.february,
                App.Lang.march,
                App.Lang.april,
                App.Lang.may,
                App.Lang.june,
                App.Lang.july,
                App.Lang.august,
                App.Lang.september,
                App.Lang.october,
                App.Lang.november,
                App.Lang.december
            ],
            monthNamesShort: [
                App.Lang.january.substr(0, 3),
                App.Lang.february.substr(0, 3),
                App.Lang.march.substr(0, 3),
                App.Lang.april.substr(0, 3),
                App.Lang.may.substr(0, 3),
                App.Lang.june.substr(0, 3),
                App.Lang.july.substr(0, 3),
                App.Lang.august.substr(0, 3),
                App.Lang.september.substr(0, 3),
                App.Lang.october.substr(0, 3),
                App.Lang.november.substr(0, 3),
                App.Lang.december.substr(0, 3)
            ],
            dayNames: [
                App.Lang.sunday,
                App.Lang.monday,
                App.Lang.tuesday,
                App.Lang.wednesday,
                App.Lang.thursday,
                App.Lang.friday,
                App.Lang.saturday
            ],
            dayNamesShort: [
                App.Lang.sunday.substr(0, 3),
                App.Lang.monday.substr(0, 3),
                App.Lang.tuesday.substr(0, 3),
                App.Lang.wednesday.substr(0, 3),
                App.Lang.thursday.substr(0, 3),
                App.Lang.friday.substr(0, 3),
                App.Lang.saturday.substr(0, 3)
            ],
            dayNamesMin: [
                App.Lang.sunday.substr(0, 2),
                App.Lang.monday.substr(0, 2),
                App.Lang.tuesday.substr(0, 2),
                App.Lang.wednesday.substr(0, 2),
                App.Lang.thursday.substr(0, 2),
                App.Lang.friday.substr(0, 2),
                App.Lang.saturday.substr(0, 2)
            ],
            buttonText: {
                today: App.Lang.today,
                day: App.Lang.day,
                week: App.Lang.week,
                month: App.Lang.month,
                agendaDay: App.Lang.calendar,
                listDay: App.Lang.list
            },

            // Calendar events need to be declared on initialization.
            eventClick: onEventClick,
            eventResize: onEventResize,
            eventDrop: onEventDrop,
            viewRender: onViewRender
        });

        $wrapper.fullCalendar('gotoDate', moment(goToDate));

        $('<h6/>', {
            'text': provider.first_name + ' ' + provider.last_name
        }).prependTo($providerColumn);
    }

    function onViewRender(view, element) {
        $(element).fullCalendar('option', 'height', getCalendarHeight());
    }

    function createNonWorkingHours($calendar, provider) {
        const workingPlan = JSON.parse(provider.settings.working_plan);
        const workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};
        const view = $calendar.fullCalendar('getView');
        const start = view.start.clone();
        const end = view.end.clone();
        const selDayName = start.format('dddd').toLowerCase();
        const selDayDate = start.format('YYYY-MM-DD');
        const calendarEventSource = [];

        if (workingPlanExceptions[selDayDate]) {
            workingPlan[selDayName] = workingPlanExceptions[selDayDate];

            const workingPlanExceptionStart = selDayDate + ' ' + workingPlan[selDayName].start;
            const workingPlanExceptionEnd = selDayDate + ' ' + workingPlan[selDayName].end;

            const workingPlanExceptionEvent = {
                title: App.Lang.working_plan_exception,
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
                title: App.Lang.not_working,
                start: start,
                end: end,
                allDay: false,
                color: '#BEBEBE',
                editable: false,
                className: 'fc-unavailable'
            };

            calendarEventSource.push(nonWorkingDay);

            $calendar.fullCalendar('addEventSource', calendarEventSource);

            return;
        }

        const workDateStart = moment(start.format('YYYY-MM-DD') + ' ' + workingPlan[selDayName].start);

        if (start < workDateStart) {
            unavailablePeriod = {
                title: App.Lang.not_working,
                start: start,
                end: workDateStart,
                allDay: false,
                color: '#BEBEBE',
                editable: false,
                className: 'fc-unavailable'
            };

            calendarEventSource.push(unavailablePeriod);
        }

        // Add unavailable period after work ends.
        const workDateEnd = moment(start.format('YYYY-MM-DD') + ' ' + workingPlan[selDayName].end);

        if (end > workDateEnd) {
            const unavailablePeriod = {
                title: App.Lang.not_working,
                start: workDateEnd,
                end: end,
                allDay: false,
                color: '#BEBEBE',
                editable: false,
                className: 'fc-unavailable'
            };

            calendarEventSource.push(unavailablePeriod);
        }

        // Add unavailable periods for breaks.
        let breakStart;
        let breakEnd;

        workingPlan[selDayName].breaks.forEach((currentBreak) => {
            breakStart = moment(start.format('YYYY-MM-DD') + ' ' + currentBreak.start);
            breakEnd = moment(start.format('YYYY-MM-DD') + ' ' + currentBreak.end);

            const unavailablePeriod = {
                title: App.Lang.break,
                start: breakStart,
                end: breakEnd,
                allDay: false,
                color: '#BEBEBE',
                editable: false,
                className: 'fc-unavailable fc-break'
            };

            calendarEventSource.push(unavailablePeriod);
        });

        $calendar.fullCalendar('addEventSource', calendarEventSource);
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

            calendarEvents.push({
                id: appointment.id,
                title:
                    appointment.service.name +
                    ' - ' +
                    appointment.customer.first_name +
                    ' ' +
                    appointment.customer.last_name,
                start: moment(appointment.start_datetime),
                end: moment(appointment.end_datetime),
                allDay: false,
                data: appointment // Store appointment data for later use.
            });
        }

        $providerColumn.find('.calendar-wrapper').fullCalendar('addEventSource', calendarEvents);
    }

    /**
     * Create Unavailability Events
     *
     * This method will add the unavailability events on the table view.
     *
     * @param {jQuery} $providerColumn The provider column container.
     * @param {Object[]} unavailabilityEvents Contains the unavailability events data.
     */
    function createUnavailabilityEvents($providerColumn, unavailabilityEvents) {
        if (unavailabilityEvents.length === 0) {
            return;
        }

        const calendarEventSource = [];

        for (const index in unavailabilityEvents) {
            const unavailability = unavailabilityEvents[index];

            if (unavailability.id_users_provider !== $providerColumn.data('provider').id) {
                continue;
            }

            const event = {
                title: App.Lang.unavailable,
                start: moment(unavailability.start_datetime),
                end: moment(unavailability.end_datetime),
                allDay: false,
                color: '#879DB4',
                editable: true,
                className: 'fc-unavailable fc-custom',
                data: unavailability
            };

            calendarEventSource.push(event);
        }

        $providerColumn.find('.calendar-wrapper').fullCalendar('addEventSource', calendarEventSource);
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
                App.Lang.break +
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
     * @param {Event} event
     */
    function getEventNotes(event) {
        if (!event.data || !event.data.notes) {
            return '-';
        }

        const notes = event.data.notes;

        return notes.length > 100 ? notes.substring(0, 100) + '...' : notes;
    }

    /**
     * Calendar Event "Click" Callback
     *
     * When the user clicks on an appointment object on the calendar, then a data preview popover is display
     * above the calendar item.
     */
    function onEventClick(event, jsEvent) {
        const $popover = $('.popover');

        $popover.popover('dispose'); // Close all open popovers.

        let $html;
        let displayEdit;
        let displayDelete;

        // Depending on where the user clicked the event (title or empty space) we need to use different selectors to
        // reach the parent element.
        const $parent = $(jsEvent.target.offsetParent);
        const $altParent = $(jsEvent.target).parents().eq(1);

        if (
            $(this).hasClass('fc-unavailable') ||
            $parent.hasClass('fc-unavailable') ||
            $altParent.hasClass('fc-unavailable')
        ) {
            displayEdit =
                ($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom')) &&
                vars('privileges').appointments.edit === true
                    ? ''
                    : 'd-none';
            displayDelete =
                ($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom')) &&
                vars('privileges').appointments.delete === true
                    ? ''
                    : 'd-none'; // Same value at the time.

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'text': App.Lang.start
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            event.start.format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.end
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            event.end.format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.notes
                    }),
                    $('<span/>', {
                        'text': getEventNotes(event)
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
                                        'text': App.Lang.close
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
                                        'text': App.Lang.delete
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
                                        'text': App.Lang.edit
                                    })
                                ]
                            })
                        ]
                    })
                ]
            });
        } else if (
            $(this).hasClass('fc-working-plan-exception') ||
            $parent.hasClass('fc-working-plan-exception') ||
            $altParent.hasClass('fc-working-plan-exception')
        ) {
            displayEdit =
                ($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom')) &&
                vars('privileges').appointments.edit === true
                    ? ''
                    : 'd-none'; // Same value at the time.

            displayDelete =
                ($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom')) &&
                vars('privileges').appointments.delete === true
                    ? ''
                    : 'd-none'; // Same value at the time.

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'text': App.Lang.provider
                    }),
                    $('<span/>', {
                        'text': event.data ? event.data.provider.first_name + ' ' + event.data.provider.last_name : '-'
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.start
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            event.data.date + ' ' + event.data.workingPlanException.start,
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.end
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            event.data.date + ' ' + event.data.workingPlanException.end,
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.timezone
                    }),
                    $('<span/>', {
                        'text': vars('timezones')[event.data.provider.timezone]
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
                                        'text': App.Lang.close
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
                                        'text': App.Lang.delete
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
                                        'text': App.Lang.edit
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

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'text': App.Lang.start
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            event.start.format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.end
                    }),
                    $('<span/>', {
                        'text': App.Utils.Date.format(
                            event.end.format('YYYY-MM-DD HH:mm:ss'),
                            vars('date_format'),
                            vars('time_format'),
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.timezone
                    }),
                    $('<span/>', {
                        'text': vars('timezones')[event.data.provider.timezone]
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.service
                    }),
                    $('<span/>', {
                        'text': event.data.service.name
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.provider
                    }),
                    App.Utils.CalendarEventPopover.renderMapIcon(event.data.provider),
                    $('<span/>', {
                        'text': event.data.provider.first_name + ' ' + event.data.provider.last_name
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.customer
                    }),
                    App.Utils.CalendarEventPopover.renderMapIcon(event.data.customer),
                    $('<span/>', {
                        'text': event.data.customer.first_name + ' ' + event.data.customer.last_name
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.email
                    }),
                    App.Utils.CalendarEventPopover.renderMailIcon(event.data.customer.email),
                    $('<span/>', {
                        'text': event.data.customer.email
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.phone
                    }),
                    App.Utils.CalendarEventPopover.renderPhoneIcon(event.data.customer.phone_number),
                    $('<span/>', {
                        'text': event.data.customer.phone_number
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.notes
                    }),
                    $('<span/>', {
                        'text': getEventNotes(event)
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
                                        'text': App.Lang.close
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
                                        'text': App.Lang.delete
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
                                        'text': App.Lang.edit
                                    })
                                ]
                            })
                        ]
                    })
                ]
            });
        }

        $(jsEvent.target).popover({
            placement: 'top',
            title: event.title,
            content: $html,
            html: true,
            container: '#calendar',
            trigger: 'manual'
        });

        lastFocusedEventData = event;

        $(jsEvent.target).popover('toggle');

        // Fix popover position.
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
     */
    function onEventResize(event, delta, revertFunc) {
        if (vars('privileges').appointments.edit === false) {
            revertFunc();
            App.Layouts.Backend.displayNotification(App.Lang.no_privileges_edit_appointments);
            return;
        }

        const $calendar = $('#calendar');

        const $notification = $('#notification');

        if ($notification.is(':visible')) {
            $notification.hide('bind');
        }

        let successCallback;

        if (event.data.is_unavailable === '0') {
            // Prepare appointment data.
            event.data.end_datetime = moment(event.data.end_datetime)
                .add({days: delta.days(), hours: delta.hours(), minutes: delta.minutes()})
                .format('YYYY-MM-DD HH:mm:ss');

            const appointment = {...event.data};

            // Must delete the following because only appointment data should be provided to the AJAX call.
            delete appointment.customer;
            delete appointment.provider;
            delete appointment.service;

            // Success callback
            successCallback = () => {
                // Display success notification to user.
                const undoFunction = () => {
                    appointment.end_datetime = event.data.end_datetime = moment(appointment.end_datetime)
                        .add({days: -delta.days(), hours: -delta.hours(), minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    App.Http.Calendar.saveAppointment(appointment).done(() => {
                        $('#notification').hide('blind');
                    });

                    revertFunc();
                };

                App.Layouts.Backend.displayNotification(App.Lang.appointment_updated, [
                    {
                        'label': App.Lang.undo,
                        'function': undoFunction
                    }
                ]);
                $('#footer').css('position', 'static'); // Footer position fix.

                // Update the event data for later use.
                $calendar.fullCalendar('updateEvent', event);
            };

            // Update appointment data.
            App.Http.Calendar.saveAppointment(appointment, null, successCallback);
        } else {
            // Update unavailable time period.
            const unavailable = {
                id: event.data.id,
                start_datetime: event.start.format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: event.end.format('YYYY-MM-DD HH:mm:ss'),
                id_users_provider: event.data.id_users_provider
            };

            event.data.end_datetime = unavailable.end_datetime;

            // Define success callback function.
            successCallback = () => {
                // Display success notification to user.
                const undoFunction = () => {
                    unavailable.end_datetime = event.data.end_datetime = moment(unavailable.end_datetime)
                        .add({minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    App.Http.Calendar.saveUnavailable(unavailable).done(() => {
                        $('#notification').hide('blind');
                    });

                    revertFunc();
                };

                App.Layouts.Backend.displayNotification(App.Lang.unavailable_updated, [
                    {
                        'label': App.Lang.undo,
                        'function': undoFunction
                    }
                ]);

                $('#footer').css('position', 'static'); // Footer position fix.

                // Update the event data for later use.
                $calendar.fullCalendar('updateEvent', event);
            };

            App.Http.Calendar.saveUnavailable(unavailable, successCallback);
        }
    }

    /**
     * Calendar Event "Drop" Callback
     *
     * This event handler is triggered whenever the user drags and drops an event into a different position
     * on the calendar. We need to update the database with this change. This is done via an ajax call.
     */
    function onEventDrop(event, delta, revertFunc) {
        if (vars('privileges').appointments.edit === false) {
            revertFunc();
            App.Layouts.Backend.displayNotification(App.Lang.no_privileges_edit_appointments);
            return;
        }

        const $notification = $('#notification');

        if ($notification.is(':visible')) {
            $notification.hide('bind');
        }

        let successCallback;

        if (event.data.is_unavailable === '0') {
            // Prepare appointment data.
            const appointment = {...event.data};

            // Must delete the following because only appointment data should be provided to the ajax call.
            delete appointment.customer;
            delete appointment.provider;
            delete appointment.service;

            appointment.start_datetime = moment(appointment.start_datetime)
                .add({days: delta.days(), hours: delta.hours(), minutes: delta.minutes()})
                .format('YYYY-MM-DD HH:mm:ss');

            appointment.end_datetime = moment(appointment.end_datetime)
                .add({days: delta.days(), hours: delta.hours(), minutes: delta.minutes()})
                .format('YYYY-MM-DD HH:mm:ss');

            event.data.start_datetime = appointment.start_datetime;
            event.data.end_datetime = appointment.end_datetime;

            // Define success callback function.
            successCallback = () => {
                // Define the undo function, if the user needs to reset the last change.
                const undoFunction = () => {
                    appointment.start_datetime = moment(appointment.start_datetime)
                        .add({days: -delta.days(), hours: -delta.hours(), minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    appointment.end_datetime = moment(appointment.end_datetime)
                        .add({days: -delta.days(), hours: -delta.hours(), minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    event.data.start_datetime = appointment.start_datetime;
                    event.data.end_datetime = appointment.end_datetime;

                    App.Http.Calendar.saveAppointment(appointment).done(() => {
                        $('#notification').hide('blind');
                    });

                    revertFunc();
                };

                App.Layouts.Backend.displayNotification(App.Lang.appointment_updated, [
                    {
                        'label': App.Lang.undo,
                        'function': undoFunction
                    }
                ]);

                $('#footer').css('position', 'static'); // Footer position fix.
            };

            // Update appointment data.
            App.Http.Calendar.saveAppointment(appointment, null, successCallback);
        } else {
            // Update unavailable time period.
            const unavailable = {
                id: event.data.id,
                start_datetime: event.start.format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: event.end.format('YYYY-MM-DD HH:mm:ss'),
                id_users_provider: event.data.id_users_provider
            };

            successCallback = () => {
                const undoFunction = () => {
                    unavailable.start_datetime = moment(unavailable.start_datetime)
                        .add({days: -delta.days(), minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    unavailable.end_datetime = moment(unavailable.end_datetime)
                        .add({days: -delta.days(), minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    event.data.start_datetime = unavailable.start_datetime;
                    event.data.end_datetime = unavailable.end_datetime;

                    App.Http.Calendar.saveUnavailable(unavailable).done(() => {
                        $('#notification').hide('blind');
                    });

                    revertFunc();
                };

                App.Layouts.Backend.displayNotification(App.Lang.unavailable_updated, [
                    {
                        label: App.Lang.undo,
                        function: undoFunction
                    }
                ]);

                $('#footer').css('position', 'static'); // Footer position fix.
            };

            App.Http.Calendar.saveUnavailable(unavailable, successCallback);
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
            $('#header').outerHeight() -
            $('#footer').outerHeight() -
            $('#calendar-toolbar').outerHeight() -
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
