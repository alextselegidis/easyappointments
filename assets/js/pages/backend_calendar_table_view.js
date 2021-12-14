/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

window.BackendCalendarTableView = window.BackendCalendarTableView || {};

/**
 * Backend Calendar
 *
 * This module implements the table calendar view of backend.
 *
 * @module BackendCalendarTableView
 */
(function (exports) {
    'use strict';

    var $filterProvider;
    var $filterService;
    var lastFocusedEventData;

    /**
     * Bind page event handlers.
     */
    function bindEventHandlers() {
        var $calendarToolbar = $('#calendar-toolbar');
        var $calendar = $('#calendar');

        $calendar.on('click', '.calendar-header .btn.previous', function () {
            var dayInterval = $('#select-filter-item').val();
            var currentDate = $('.select-date').datepicker('getDate');
            var startDate = moment(currentDate).subtract(1, 'days');
            var endDate = startDate.clone().add(dayInterval - 1, 'days');
            $('.select-date').datepicker('setDate', startDate.toDate());
            createView(startDate.toDate(), endDate.toDate());
        });

        $calendar.on('click', '.calendar-header .btn.next', function () {
            var dayInterval = $('#select-filter-item').val();
            var currentDate = $('.select-date').datepicker('getDate');
            var startDate = moment(currentDate).add(1, 'days');
            var endDate = startDate.clone().add(dayInterval - 1, 'days');
            $('.select-date').datepicker('setDate', startDate.toDate());
            createView(startDate.toDate(), endDate.toDate());
        });

        $calendarToolbar.on('change', '#select-filter-item', function () {
            var dayInterval = $('#select-filter-item').val();
            var currentDate = $('.select-date').datepicker('getDate');
            var startDate = moment(currentDate);
            var endDate = startDate.clone().add(dayInterval - 1, 'days');
            createView(startDate.toDate(), endDate.toDate());
        });

        $calendarToolbar.on('click', '#reload-appointments', function () {
            // Remove all the events from the tables.
            $('.calendar-view .event').remove();

            // Fetch the events and place them in the existing HTML format.
            var dayInterval = $('#select-filter-item').val();
            var currentDate = $('.select-date').datepicker('getDate');
            var startDateMoment = moment(currentDate);
            var startDate = startDateMoment.toDate();
            var endDateMoment = startDateMoment.clone().add(dayInterval - 1, 'days');
            var endDate = endDateMoment.toDate();

            getCalendarEvents(startDate, endDate).done(function (response) {
                var currentDate = startDate;

                while (currentDate <= endDate) {
                    $('.calendar-view .date-column').each(function (index, dateColumn) {
                        var $dateColumn = $(dateColumn);
                        var date = new Date($dateColumn.data('date'));

                        if (currentDate.getTime() !== date.getTime()) {
                            return true;
                        }

                        $dateColumn
                            .find('.date-column-title')
                            .text(GeneralFunctions.formatDate(date, GlobalVariables.dateFormat));

                        $dateColumn.find('.provider-column').each(function (index, providerColumn) {
                            var $providerColumn = $(providerColumn);
                            var provider = $providerColumn.data('provider');

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
                            var workingPlan = JSON.parse(provider.settings.working_plan);
                            var day = moment(date).format('dddd').toLowerCase();
                            if (workingPlan[day]) {
                                var breaks = workingPlan[day].breaks;
                                createBreaks($providerColumn, breaks);
                            }
                        });
                    });

                    currentDate.add({days: 1});
                }

                // setCalendarViewSize();
                Backend.placeFooterToBottom();
            });
        });

        /**
         * Event: On Window Resize
         */
        $(window).on('resize', function () {
            setCalendarViewSize();
        });

        /**
         * Event: Popover Close Button "Click"
         *
         * Hides the open popover element.
         */
        $calendar.on('click', '.close-popover', function () {
            $(this).parents('.popover').popover('dispose');
        });

        /**
         * Event: Popover Edit Button "Click"
         *
         * Enables the edit dialog of the selected table event.
         */
        $calendar.on('click', '.edit-popover', function () {
            $(this).parents('.popover').popover('dispose');

            var $dialog;
            var startMoment;
            var endMoment;

            if (lastFocusedEventData.data.workingPlanException) {
                var date = lastFocusedEventData.data.date;
                var workingPlanException = lastFocusedEventData.data.workingPlanException;
                var provider = lastFocusedEventData.data.provider;

                WorkingPlanExceptionsModal.edit(date, workingPlanException).done(function (date, workingPlanException) {
                    var successCallback = function () {
                        Backend.displayNotification(App.Lang.working_plan_exception_saved);

                        var workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};

                        workingPlanExceptions[date] = workingPlanException;

                        for (var index in GlobalVariables.availableProviders) {
                            var availableProvider = GlobalVariables.availableProviders[index];

                            if (Number(availableProvider.id) === Number(provider.id)) {
                                availableProvider.settings.working_plan_exceptions =
                                    JSON.stringify(workingPlanExceptions);
                                break;
                            }
                        }

                        $('#select-filter-item').trigger('change'); // Update the calendar.
                    };

                    BackendCalendarApi.saveWorkingPlanException(
                        date,
                        workingPlanException,
                        provider.id,
                        successCallback,
                        null
                    );
                });
            } else if (lastFocusedEventData.data.is_unavailable === '0') {
                var appointment = lastFocusedEventData.data;
                $dialog = $('#manage-appointment');

                BackendCalendarAppointmentsModal.resetAppointmentDialog();

                // Apply appointment data and show modal dialog.
                $dialog.find('.modal-header h3').text(App.Lang.edit_appointment_title);
                $dialog.find('#appointment-id').val(appointment.id);
                $dialog.find('#select-service').val(appointment.id_services).trigger('change');
                $dialog.find('#select-provider').val(appointment.id_users_provider);

                // Set the start and end datetime of the appointment.
                startMoment = moment(appointment.start_datetime);
                $dialog.find('#start-datetime').datetimepicker('setDate', startMoment);

                endMoment = moment(appointment.end_datetime);
                $dialog.find('#end-datetime').datetimepicker('setDate', endMoment);

                var customer = appointment.customer;
                $dialog.find('#customer-id').val(appointment.id_users_customer);
                $dialog.find('#first-name').val(customer.first_name);
                $dialog.find('#last-name').val(customer.last_name);
                $dialog.find('#email').val(customer.email);
                $dialog.find('#phone-number').val(customer.phone_number);
                $dialog.find('#address').val(customer.address);
                $dialog.find('#city').val(customer.city);
                $dialog.find('#zip-code').val(customer.zip_code);
                $dialog.find('#appointment-location').val(appointment.location);
                $dialog.find('#appointment-notes').val(appointment.notes);
                $dialog.find('#customer-notes').val(customer.notes);

                $dialog.modal('show');
            } else {
                var unavailable = lastFocusedEventData.data;

                // Replace string date values with actual date objects.
                unavailable.start_datetime = lastFocusedEventData.start.format('YYYY-MM-DD HH:mm:ss');
                startMoment = moment(unavailable.start_datetime);
                unavailable.end_datetime = lastFocusedEventData.end.format('YYYY-MM-DD HH:mm:ss');
                endMoment = moment(unavailable.end_datetime);

                $dialog = $('#manage-unavailable');
                BackendCalendarUnavailabilityEventsModal.resetUnavailableDialog();

                // Apply unavailable data to dialog.
                $dialog.find('.modal-header h3').text('Edit Unavailable Period');
                $dialog.find('#unavailable-start').datetimepicker('setDate', startMoment);
                $dialog.find('#unavailable-id').val(unavailable.id);
                $dialog.find('#unavailable-provider').val(unavailable.id_users_provider);
                $dialog.find('#unavailable-end').datetimepicker('setDate', endMoment);
                $dialog.find('#unavailable-notes').val(unavailable.notes);

                $dialog.modal('show');
            }
        });

        /**
         * Event: Popover Delete Button "Click"
         *
         * Displays a prompt on whether the user wants the appointment to be deleted. If he confirms the
         * deletion then an ajax call is made to the server and deletes the appointment from the database.
         */
        $calendar.on('click', '.delete-popover', function () {
            $(this).parents('.popover').popover('dispose'); // Hide the popover.

            var url;
            var data;

            // If id_role parameter exists the popover is an working plan exception.
            if (lastFocusedEventData.data.hasOwnProperty('id_roles')) {
                // Do not display confirmation prompt.
                url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_working_plan_exception';

                data = {
                    csrf_token: GlobalVariables.csrfToken,
                    working_plan_exception: lastFocusedEventData.start.format('YYYY-MM-DD'),
                    provider_id: lastFocusedEventData.data.id
                };

                $.post(url, data).done(function () {
                    $('#message-box').dialog('close');

                    var workingPlanExceptions = JSON.parse(lastFocusedEventData.data.settings.working_plan_exceptions);
                    delete workingPlanExceptions[lastFocusedEventData.start.format('YYYY-MM-DD')];
                    lastFocusedEventData.data.settings.working_plan_exceptions = JSON.stringify(workingPlanExceptions);

                    // Refresh calendar event items.
                    $('#select-filter-item').trigger('change');
                });
            } else if (lastFocusedEventData.data.is_unavailable === '0') {
                var buttons = [
                    {
                        text: App.Lang.cancel,
                        click: function () {
                            $('#message-box').dialog('close');
                        }
                    },
                    {
                        text: 'OK',
                        click: function () {
                            url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_appointment';

                            data = {
                                csrf_token: GlobalVariables.csrfToken,
                                appointment_id: lastFocusedEventData.data.id,
                                delete_reason: $('#delete-reason').val()
                            };

                            $.post(url, data).done(function () {
                                $('#message-box').dialog('close');

                                // Refresh calendar event items.
                                $('#select-filter-item').trigger('change');
                            });
                        }
                    }
                ];

                GeneralFunctions.displayMessageBox(
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
                url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_unavailable';

                data = {
                    csrf_token: GlobalVariables.csrfToken,
                    unavailable_id: lastFocusedEventData.data.id
                };

                $.post(url, data).done(function () {
                    $('#message-box').dialog('close');

                    // Refresh calendar event items.
                    $('#select-filter-item').trigger('change');
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
        var $calendarFilter = $('#calendar-filter');

        $calendarFilter
            .find('select')
            .empty()
            .append(new Option('1 ' + App.Lang.day, 1))
            .append(new Option('3 ' + App.Lang.days, 3));

        var $calendarHeader = $('<div/>', {
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

        $('<input/>', {
            'type': 'text',
            'class': 'form-control d-inline-block select-date me-2',
            'value': GeneralFunctions.formatDate(new Date(), GlobalVariables.dateFormat, false)
        }).appendTo($calendarHeader);

        $('<button/>', {
            'class': 'btn btn-xs btn-outline-secondary next',
            'html': [
                $('<span/>', {
                    'class': 'fas fa-chevron-right'
                })
            ]
        }).appendTo($calendarHeader);

        var dateFormat;

        switch (GlobalVariables.dateFormat) {
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
                throw new Error('Invalid date format setting provided: ' + GlobalVariables.dateFormat);
        }

        $calendarHeader.find('.select-date').datepicker({
            defaultDate: new Date(),
            dateFormat: dateFormat,
            onSelect: function (dateText, instance) {
                var startDate = new Date(instance.currentYear, instance.currentMonth, instance.currentDay);
                var endDate = new Date(startDate.getTime()).add({days: parseInt($('#select-filter-item').val()) - 1});
                createView(startDate, endDate);
            }
        });

        var providers = GlobalVariables.availableProviders.filter(function (provider) {
            return (
                GlobalVariables.user.role_slug === Backend.DB_SLUG_ADMIN ||
                (GlobalVariables.user.role_slug === Backend.DB_SLUG_SECRETARY &&
                    GlobalVariables.secretaryProviders.indexOf(provider.id) !== -1) ||
                (GlobalVariables.user.role_slug === Backend.DB_SLUG_PROVIDER &&
                    Number(provider.id) === Number(GlobalVariables.user.id))
            );
        });

        // Create providers and service filters.
        $('<label/>', {
            'text': App.Lang.provider
        }).appendTo($calendarHeader);

        $filterProvider = $('<select/>', {
            'id': 'filter-provider',
            'multiple': 'multiple',
            'on': {
                'change': function () {
                    var startDate = new Date($('.calendar-view .date-column:first').data('date'));
                    var endDate = new Date(startDate.getTime()).add({days: parseInt($('#select-date').val()) - 1});
                    createView(startDate, endDate);
                }
            }
        }).appendTo($calendarHeader);

        if (GlobalVariables.user.role_slug !== Backend.DB_SLUG_PROVIDER) {
            providers.forEach(function (provider) {
                $filterProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
            });
        } else {
            providers.forEach(function (provider) {
                if (Number(provider.id) === Number(GlobalVariables.user.id)) {
                    $filterProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
                }
            });
        }

        $filterProvider.select2();

        var services = GlobalVariables.availableServices.filter(function (service) {
            var provider = providers.find(function (provider) {
                return provider.services.indexOf(service.id) !== -1;
            });

            return GlobalVariables.user.role_slug === Backend.DB_SLUG_ADMIN || provider;
        });

        $('<label/>', {
            'text': App.Lang.service
        }).appendTo($calendarHeader);

        $filterService = $('<select/>', {
            'id': 'filter-service',
            'multiple': 'multiple',
            'on': {
                'change': function () {
                    var startDate = new Date($('.calendar-view .date-column:first').data('date'));
                    var endDate = new Date(startDate.getTime()).add({days: parseInt($('#select-date').val()) - 1});
                    createView(startDate, endDate);
                }
            }
        }).appendTo($calendarHeader);

        services.forEach(function (service) {
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
        var providerView = {};
        $('.provider-column').each(function (index, providerColumn) {
            var $providerColumn = $(providerColumn);
            var providerId = $providerColumn.data('provider').id;
            providerView[providerId] = $providerColumn.find('.calendar-wrapper').fullCalendar('getView').name;
        });

        $('#calendar .calendar-view').remove();

        Backend.placeFooterToBottom();

        var $calendarView = $('<div/>', {
            'class': 'calendar-view'
        }).appendTo('#calendar');

        $calendarView.data({
            startDate: moment(startDate).format('YYYY-MM-DD'),
            endDate: moment(endDate).format('YYYY-MM-DD')
        });

        var $wrapper = $('<div/>').appendTo($calendarView);

        getCalendarEvents(startDate, endDate).done(function (response) {
            var currentDate = startDate;

            while (currentDate <= endDate) {
                createDateColumn($wrapper, currentDate, response);
                currentDate.add({days: 1});
            }

            setCalendarViewSize();

            Backend.placeFooterToBottom();

            // Activate calendar navigation.
            $('#calendar .calendar-header .btn').removeClass('disabled').prop('disabled', false);

            // Apply provider calendar view mode.
            $('.provider-column').each(function (index, providerColumn) {
                var $providerColumn = $(providerColumn);
                var providerId = $providerColumn.data('provider').id;
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
        var $dateColumn = $('<div/>', {
            'class': 'date-column'
        }).appendTo($wrapper);

        $dateColumn.data('date', date.getTime());

        $('<h5/>', {
            'class': 'date-column-title',
            'text': GeneralFunctions.formatDate(date, GlobalVariables.dateFormat)
        }).appendTo($dateColumn);

        var filterProviderIds = $filterProvider.val();
        var filterServiceIds = $filterService.val();

        var providers = GlobalVariables.availableProviders.filter(function (provider) {
            var servedServiceIds = provider.services.filter(function (serviceId) {
                var matches = filterServiceIds.filter(function (filterServiceId) {
                    return Number(serviceId) === Number(filterServiceId);
                });

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

        if (GlobalVariables.user.role_slug === 'provider') {
            GlobalVariables.availableProviders.forEach(function (provider) {
                if (Number(provider.id) === Number(GlobalVariables.user.id)) {
                    providers = [provider];
                }
            });
        }

        if (GlobalVariables.user.role_slug === 'secretary') {
            providers = [];
            GlobalVariables.availableProviders.forEach(function (provider) {
                if (GlobalVariables.secretaryProviders.indexOf(provider.id) > -1) {
                    providers.push(provider);
                }
            });
        }

        providers.forEach(function (provider) {
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

        var $providerColumn = $('<div/>', {
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

        Backend.placeFooterToBottom();
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
        var result = window.innerHeight - $('#footer').outerHeight() - $('#header').outerHeight() - 60; // 60 for fine tuning
        return result > 500 ? result : 500; // Minimum height is 500px
    }

    function createCalendar($providerColumn, goToDate, provider) {
        var $wrapper = $('<div/>', {
            'class': 'calendar-wrapper'
        }).appendTo($providerColumn);

        var columnFormat = '';

        switch (GlobalVariables.dateFormat) {
            case 'DMY':
                columnFormat = 'ddd D/M';
                break;

            case 'MDY':
            case 'YMD':
                columnFormat = 'ddd M/D';
                break;

            default:
                throw new Error('Invalid date format setting provided!', GlobalVariables.dateFormat);
        }

        // Time formats
        var timeFormat = '';
        var slotTimeFormat = '';

        switch (GlobalVariables.timeFormat) {
            case 'military':
                timeFormat = 'H:mm';
                slotTimeFormat = 'H(:mm)';
                break;
            case 'regular':
                timeFormat = 'h:mm a';
                slotTimeFormat = 'h(:mm) a';
                break;
            default:
                throw new Error('Invalid time format setting provided!' + GlobalVariables.timeFormat);
        }

        var firstWeekday = GlobalVariables.firstWeekday;
        var firstWeekdayNumber = GeneralFunctions.getWeekDayId(firstWeekday);

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
            select: function (start, end, jsEvent) {
                if (!start.hasTime() || !end.hasTime()) {
                    return;
                }

                $('#insert-appointment').trigger('click');

                // Preselect service & provider.
                var $providerColumn = $(jsEvent.target).parents('.provider-column');
                var providerId = $providerColumn.data('provider').id;

                var provider = GlobalVariables.availableProviders.find(function (provider) {
                    return Number(provider.id) === Number(providerId);
                });

                var service = GlobalVariables.availableServices.find(function (service) {
                    return provider.services.indexOf(service.id) !== -1;
                });

                if (service) {
                    $('#select-service').val(service.id);
                }

                if (!$('#select-service').val()) {
                    $('#select-service option:first').prop('selected', true);
                }

                $('#select-service').trigger('change');

                if (provider) {
                    $('#select-provider').val(provider.id);
                }

                if (!$('#select-provider').val()) {
                    $('#select-provider option:first').prop('selected', true);
                }

                $('#select-provider').trigger('change');

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
        var workingPlan = JSON.parse(provider.settings.working_plan);
        var workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};
        var view = $calendar.fullCalendar('getView');
        var start = view.start.clone();
        var end = view.end.clone();
        var selDayName = start.format('dddd').toLowerCase();
        var selDayDate = start.format('YYYY-MM-DD');
        var calendarEventSource = [];

        if (workingPlanExceptions[selDayDate]) {
            workingPlan[selDayName] = workingPlanExceptions[selDayDate];

            var workingPlanExceptionStart = selDayDate + ' ' + workingPlan[selDayName].start;
            var workingPlanExceptionEnd = selDayDate + ' ' + workingPlan[selDayName].end;

            var workingPlanExceptionEvent = {
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
            var nonWorkingDay = {
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

        var workDateStart = moment(start.format('YYYY-MM-DD') + ' ' + workingPlan[selDayName].start);

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
        var workDateEnd = moment(start.format('YYYY-MM-DD') + ' ' + workingPlan[selDayName].end);

        if (end > workDateEnd) {
            var unavailablePeriod = {
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
        var breakStart;
        var breakEnd;

        workingPlan[selDayName].breaks.forEach(function (currentBreak) {
            breakStart = moment(start.format('YYYY-MM-DD') + ' ' + currentBreak.start);
            breakEnd = moment(start.format('YYYY-MM-DD') + ' ' + currentBreak.end);

            var unavailablePeriod = {
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

        var filterServiceIds = $filterService.val();

        appointments = appointments.filter(function (appointment) {
            return !filterServiceIds.length || filterServiceIds.indexOf(appointment.id_services) !== -1;
        });

        var calendarEvents = [];

        for (var index in appointments) {
            var appointment = appointments[index];

            if (appointment.id_users_provider !== $providerColumn.data('provider').id) {
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

        var calendarEventSource = [];

        for (var index in unavailabilityEvents) {
            var unavailability = unavailabilityEvents[index];

            if (unavailability.id_users_provider !== $providerColumn.data('provider').id) {
                continue;
            }

            var event = {
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

        var currentDate = new Date($providerColumn.parents('.date-column').data('date'));
        var $tbody = $providerColumn.find('table tbody');

        for (var index in breaks) {
            var entry = breaks[index];
            var startHour = entry.start.split(':');
            var eventDate = new Date(
                currentDate.getFullYear(),
                currentDate.getMonth(),
                currentDate.getDate(),
                startHour[0],
                startHour[1]
            );
            var endHour = entry.end.split(':');
            var endDate = new Date(
                currentDate.getFullYear(),
                currentDate.getMonth(),
                currentDate.getDate(),
                endHour[0],
                endHour[1]
            );
            var eventDuration = Math.round((endDate - eventDate) / 60000);
            var $event = $('<div/>', {
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

            $tbody.find('tr').each(function (index, tr) {
                var $td = $(tr).find('td:first');

                var cellDate = new Date(currentDate.getTime()).set({
                    hour: parseInt($td.text().split(':')[0]),
                    minute: parseInt($td.text().split(':')[1])
                });

                if (eventDate < cellDate) {
                    // Remove the hour from the event if it is the same as the row.
                    if (moment(eventDate).format('HH:mm') === $(tr).prev().find('td').eq(0).text()) {
                        $event.find('.hour').remove();
                    }

                    $(tr)
                        .prev()
                        .find('td:gt(0)')
                        .each(function (index, td) {
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

        var notes = event.data.notes;

        return notes.length > 100 ? notes.substring(0, 100) + '...' : notes;
    }

    /**
     * Calendar Event "Click" Callback
     *
     * When the user clicks on an appointment object on the calendar, then a data preview popover is display
     * above the calendar item.
     */
    function onEventClick(event, jsEvent) {
        $('.popover').popover('dispose'); // Close all open popovers.

        var $html;
        var displayEdit;
        var displayDelete;

        // Depending where the user clicked the event (title or empty space) we
        // need to use different selectors to reach the parent element.
        var $parent = $(jsEvent.target.offsetParent);
        var $altParent = $(jsEvent.target).parents().eq(1);

        if (
            $(this).hasClass('fc-unavailable') ||
            $parent.hasClass('fc-unavailable') ||
            $altParent.hasClass('fc-unavailable')
        ) {
            displayEdit =
                ($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom')) &&
                GlobalVariables.user.privileges.appointments.edit === true
                    ? ''
                    : 'd-none';
            displayDelete =
                ($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom')) &&
                GlobalVariables.user.privileges.appointments.delete === true
                    ? ''
                    : 'd-none'; // Same value at the time.

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'text': App.Lang.start
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(
                            event.start.format('YYYY-MM-DD HH:mm:ss'),
                            GlobalVariables.dateFormat,
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.end
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(
                            event.end.format('YYYY-MM-DD HH:mm:ss'),
                            GlobalVariables.dateFormat,
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
                GlobalVariables.user.privileges.appointments.edit === true
                    ? ''
                    : 'd-none'; // Same value at the time.

            displayDelete =
                ($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom')) &&
                GlobalVariables.user.privileges.appointments.delete === true
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
                        'text': GeneralFunctions.formatDate(
                            event.data.date + ' ' + event.data.workingPlanException.start,
                            GlobalVariables.dateFormat,
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.end
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(
                            event.data.date + ' ' + event.data.workingPlanException.end,
                            GlobalVariables.dateFormat,
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.timezone
                    }),
                    $('<span/>', {
                        'text': GlobalVariables.timezones[event.data.provider.timezone]
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
            displayEdit = GlobalVariables.user.privileges.appointments.edit === true ? '' : 'd-none';
            displayDelete = GlobalVariables.user.privileges.appointments.delete === true ? '' : 'd-none';

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'text': App.Lang.start
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(
                            event.start.format('YYYY-MM-DD HH:mm:ss'),
                            GlobalVariables.dateFormat,
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.end
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(
                            event.end.format('YYYY-MM-DD HH:mm:ss'),
                            GlobalVariables.dateFormat,
                            true
                        )
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.timezone
                    }),
                    $('<span/>', {
                        'text': GlobalVariables.timezones[event.data.provider.timezone]
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
                    GeneralFunctions.renderMapIcon(event.data.provider),
                    $('<span/>', {
                        'text': event.data.provider.first_name + ' ' + event.data.provider.last_name
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.customer
                    }),
                    GeneralFunctions.renderMapIcon(event.data.customer),
                    $('<span/>', {
                        'text': event.data.customer.first_name + ' ' + event.data.customer.last_name
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.email
                    }),
                    GeneralFunctions.renderMailIcon(event.data.customer.email),
                    $('<span/>', {
                        'text': event.data.customer.email
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': App.Lang.phone
                    }),
                    GeneralFunctions.renderPhoneIcon(event.data.customer.phone_number),
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
        if ($('.popover').length > 0 && $('.popover').position().top < 200) {
            $('.popover').css('top', '200px');
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
        if (GlobalVariables.user.privileges.appointments.edit === false) {
            revertFunc();
            Backend.displayNotification(App.Lang.no_privileges_edit_appointments);
            return;
        }

        var $calendar = $('#calendar');

        if ($('#notification').is(':visible')) {
            $('#notification').hide('bind');
        }

        var successCallback;

        if (event.data.is_unavailable === '0') {
            // Prepare appointment data.
            event.data.end_datetime = moment(event.data.end_datetime)
                .add({days: delta.days(), hours: delta.hours(), minutes: delta.minutes()})
                .format('YYYY-MM-DD HH:mm:ss');

            var appointment = GeneralFunctions.clone(event.data);

            // Must delete the following because only appointment data should be provided to the AJAX call.
            delete appointment.customer;
            delete appointment.provider;
            delete appointment.service;

            // Success callback
            successCallback = function () {
                // Display success notification to user.
                var undoFunction = function () {
                    appointment.end_datetime = event.data.end_datetime = moment(appointment.end_datetime)
                        .add({days: -delta.days(), hours: -delta.hours(), minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_appointment';

                    var data = {
                        csrf_token: GlobalVariables.csrfToken,
                        appointment_data: JSON.stringify(appointment)
                    };

                    $.post(url, data).done(function () {
                        $('#notification').hide('blind');
                    });

                    revertFunc();
                };

                Backend.displayNotification(App.Lang.appointment_updated, [
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
            BackendCalendarApi.saveAppointment(appointment, null, successCallback);
        } else {
            // Update unavailable time period.
            var unavailable = {
                id: event.data.id,
                start_datetime: event.start.format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: event.end.format('YYYY-MM-DD HH:mm:ss'),
                id_users_provider: event.data.id_users_provider
            };

            event.data.end_datetime = unavailable.end_datetime;

            // Define success callback function.
            successCallback = function () {
                // Display success notification to user.
                var undoFunction = function () {
                    unavailable.end_datetime = event.data.end_datetime = moment(unavailable.end_datetime)
                        .add({minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_unavailable';

                    var data = {
                        csrf_token: GlobalVariables.csrfToken,
                        unavailable: JSON.stringify(unavailable)
                    };

                    $.post(url, data).done(function () {
                        $('#notification').hide('blind');
                    });

                    revertFunc();
                };

                Backend.displayNotification(App.Lang.unavailable_updated, [
                    {
                        'label': App.Lang.undo,
                        'function': undoFunction
                    }
                ]);

                $('#footer').css('position', 'static'); // Footer position fix.

                // Update the event data for later use.
                $calendar.fullCalendar('updateEvent', event);
            };

            BackendCalendarApi.saveUnavailable(unavailable, successCallback);
        }
    }

    /**
     * Calendar Event "Drop" Callback
     *
     * This event handler is triggered whenever the user drags and drops an event into a different position
     * on the calendar. We need to update the database with this change. This is done via an ajax call.
     */
    function onEventDrop(event, delta, revertFunc) {
        if (GlobalVariables.user.privileges.appointments.edit === false) {
            revertFunc();
            Backend.displayNotification(App.Lang.no_privileges_edit_appointments);
            return;
        }

        if ($('#notification').is(':visible')) {
            $('#notification').hide('bind');
        }

        var successCallback;

        if (event.data.is_unavailable === '0') {
            // Prepare appointment data.
            var appointment = GeneralFunctions.clone(event.data);

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
            successCallback = function () {
                // Define the undo function, if the user needs to reset the last change.
                var undoFunction = function () {
                    appointment.start_datetime = moment(appointment.start_datetime)
                        .add({days: -delta.days(), hours: -delta.hours(), minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    appointment.end_datetime = moment(appointment.end_datetime)
                        .add({days: -delta.days(), hours: -delta.hours(), minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    event.data.start_datetime = appointment.start_datetime;
                    event.data.end_datetime = appointment.end_datetime;

                    var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_appointment';

                    var data = {
                        csrf_token: GlobalVariables.csrfToken,
                        appointment_data: JSON.stringify(appointment)
                    };

                    $.post(url, data).done(function () {
                        $('#notification').hide('blind');
                    });

                    revertFunc();
                };

                Backend.displayNotification(App.Lang.appointment_updated, [
                    {
                        'label': App.Lang.undo,
                        'function': undoFunction
                    }
                ]);

                $('#footer').css('position', 'static'); // Footer position fix.
            };

            // Update appointment data.
            BackendCalendarApi.saveAppointment(appointment, null, successCallback);
        } else {
            // Update unavailable time period.
            var unavailable = {
                id: event.data.id,
                start_datetime: event.start.format('YYYY-MM-DD HH:mm:ss'),
                end_datetime: event.end.format('YYYY-MM-DD HH:mm:ss'),
                id_users_provider: event.data.id_users_provider
            };

            successCallback = function () {
                var undoFunction = function () {
                    unavailable.start_datetime = moment(unavailable.start_datetime)
                        .add({days: -delta.days(), minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    unavailable.end_datetime = moment(unavailable.end_datetime)
                        .add({days: -delta.days(), minutes: -delta.minutes()})
                        .format('YYYY-MM-DD HH:mm:ss');

                    event.data.start_datetime = unavailable.start_datetime;
                    event.data.end_datetime = unavailable.end_datetime;

                    var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_unavailable';

                    var data = {
                        csrf_token: GlobalVariables.csrfToken,
                        unavailable: JSON.stringify(unavailable)
                    };

                    $.post(url, data).done(function () {
                        $('#notification').hide('blind');
                    });

                    revertFunc();
                };

                Backend.displayNotification(App.Lang.unavailable_updated, [
                    {
                        label: App.Lang.undo,
                        function: undoFunction
                    }
                ]);

                $('#footer').css('position', 'static'); // Footer position fix.
            };

            BackendCalendarApi.saveUnavailable(unavailable, successCallback);
        }
    }

    /**
     * Set Table Calendar View
     *
     * This method will set the optimal size in the calendar view elements in order to fit in the page without
     * using scrollbars.
     */
    function setCalendarViewSize() {
        var height =
            window.innerHeight -
            $('#header').outerHeight() -
            $('#footer').outerHeight() -
            $('#calendar-toolbar').outerHeight() -
            $('.calendar-header').outerHeight() -
            50;

        if (height < 500) {
            height = 500;
        }

        var $dateColumn = $('.date-column');
        var $calendarViewDiv = $('.calendar-view > div');

        $calendarViewDiv.css('min-width', '1000%');

        var width = 0;

        $dateColumn.each(function (index, dateColumn) {
            width += $(dateColumn).outerWidth();
        });

        $calendarViewDiv.css('min-width', width + 200);

        var dateColumnHeight = $dateColumn.outerHeight();

        $('.calendar-view .not-working').outerHeight((dateColumnHeight > height ? dateColumnHeight : height) - 70);
    }

    /**
     * Get the calendar events.
     *
     * @param {Date} startDate The start date of the selected period.
     * @param {Date} endDate The end date of the selected period.
     *
     * @return {jQuery.jqXHR}
     */
    function getCalendarEvents(startDate, endDate) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_get_calendar_events';

        var data = {
            csrf_token: GlobalVariables.csrfToken,
            startDate: moment(startDate).format('YYYY-MM-DD'),
            endDate: moment(endDate).format('YYYY-MM-DD')
        };

        return $.ajax({
            url: url,
            data: data,
            method: 'POST',
            beforeSend: function () {
                // $('#loading').css('visibility', 'hidden');
            },
            complete: function () {
                // $('#loading').css('visibility', '');
            }
        });
    }

    /**
     * Initialize Page
     */
    exports.initialize = function () {
        createHeader();

        var startDate = moment().toDate();
        var endDate = moment()
            .add(Number($('#select-filter-item').val()) - 1, 'days')
            .toDate();

        createView(startDate, endDate);

        $('#insert-working-plan-exception').hide();

        bindEventHandlers();

        // Hide Google Calendar Sync buttons cause they can not be used within this view.
        $('#enable-sync, #google-sync').hide();
    };
})(window.BackendCalendarTableView);
