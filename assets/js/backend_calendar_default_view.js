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
 * Backend Calendar
 *
 * This module implements the default calendar view of backend.
 *
 * @module BackendCalendarDefaultView
 */
window.BackendCalendarDefaultView = window.BackendCalendarDefaultView || {};

(function (exports) {
    'use strict';

    // Constants
    var FILTER_TYPE_PROVIDER = 'provider';
    var FILTER_TYPE_SERVICE = 'service';

    // Variables
    var lastFocusedEventData; // Contains event data for later use.

    /**
     * Bind event handlers for the calendar view.
     */
    function bindEventHandlers() {
        var $calendarPage = $('#calendar-page');

        /**
         * Event: Reload Button "Click"
         *
         * When the user clicks the reload button an the calendar items need to be refreshed.
         */
        $('#reload-appointments').on('click', function () {
            var calendarView = $('#calendar').fullCalendar('getView');

            refreshCalendarAppointments(
                $('#calendar'),
                $('#select-filter-item').val(),
                $('#select-filter-item').find('option:selected').attr('type'),
                calendarView.start,
                calendarView.end);
        });

        /**
         * Event: Popover Close Button "Click"
         *
         * Hides the open popover element.
         */
        $calendarPage.on('click', '.close-popover', function () {
            $(this).parents('.popover').popover('dispose');
        });

        /**
         * Event: Popover Edit Button "Click"
         *
         * Enables the edit dialog of the selected calendar event.
         */
        $calendarPage.on('click', '.edit-popover', function () {
            $(this).parents('.popover').popover('dispose');

            var $dialog;
            var startDatetime;
            var endDatetime;

            if (lastFocusedEventData.data.workingPlanException) {
                var date = lastFocusedEventData.data.date;
                var workingPlanException = lastFocusedEventData.data.workingPlanException;
                var provider = lastFocusedEventData.data.provider;

                WorkingPlanExceptionsModal
                    .edit(date, workingPlanException)
                    .done(function (date, workingPlanException) {
                        var successCallback = function () {
                            Backend.displayNotification(EALang.working_plan_exception_saved);

                            var workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};

                            workingPlanExceptions[date] = workingPlanException;

                            for (var index in GlobalVariables.availableProviders) {
                                var availableProvider = GlobalVariables.availableProviders[index];

                                if (Number(availableProvider.id) === Number(provider.id)) {
                                    availableProvider.settings.working_plan_exceptions = JSON.stringify(workingPlanExceptions);
                                    break;
                                }
                            }

                            $('#select-filter-item').trigger('change'); // Update the calendar.
                        };

                        BackendCalendarApi.saveWorkingPlanException(date, workingPlanException, provider.id, successCallback, null);
                    });
            } else if (lastFocusedEventData.data.is_unavailable === '0') {
                var appointment = lastFocusedEventData.data;
                $dialog = $('#manage-appointment');

                BackendCalendarAppointmentsModal.resetAppointmentDialog();

                // Apply appointment data and show modal dialog.
                $dialog.find('.modal-header h3').text(EALang.edit_appointment_title);
                $dialog.find('#appointment-id').val(appointment.id);
                $dialog.find('#select-service').val(appointment.id_services).trigger('change');
                $dialog.find('#select-provider').val(appointment.id_users_provider);

                // Set the start and end datetime of the appointment.
                startDatetime = Date.parseExact(appointment.start_datetime, 'yyyy-MM-dd HH:mm:ss');
                $dialog.find('#start-datetime').datetimepicker('setDate', startDatetime);

                endDatetime = Date.parseExact(appointment.end_datetime, 'yyyy-MM-dd HH:mm:ss');
                $dialog.find('#end-datetime').datetimepicker('setDate', endDatetime);

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
                startDatetime = Date.parseExact(unavailable.start_datetime, 'yyyy-MM-dd HH:mm:ss');
                unavailable.end_datetime = lastFocusedEventData.end.format('YYYY-MM-DD HH:mm:ss');
                endDatetime = Date.parseExact(unavailable.end_datetime, 'yyyy-MM-dd HH:mm:ss');

                $dialog = $('#manage-unavailable');
                BackendCalendarUnavailabilityEventsModal.resetUnavailableDialog();

                // Apply unavailable data to dialog.
                $dialog.find('.modal-header h3').text('Edit Unavailable Period');
                $dialog.find('#unavailable-start').datetimepicker('setDate', startDatetime);
                $dialog.find('#unavailable-id').val(unavailable.id);
                $dialog.find('#unavailable-provider').val(unavailable.id_users_provider);
                $dialog.find('#unavailable-end').datetimepicker('setDate', endDatetime);
                $dialog.find('#unavailable-notes').val(unavailable.notes);
                $dialog.modal('show');
            }
        });

        /**
         * Event: Popover Delete Button "Click"
         *
         * Displays a prompt on whether the user wants the appointment to be deleted. If he confirms the
         * deletion then an AJAX call is made to the server and deletes the appointment from the database.
         */
        $calendarPage.on('click', '.delete-popover', function () {
            $(this).parents('.popover').popover('dispose');

            var url;
            var data;

            if (lastFocusedEventData.data.workingPlanException) {
                var providerId = $('#select-filter-item').val();

                var provider = GlobalVariables.availableProviders.find(function (availableProvider) {
                    return Number(availableProvider.id) === Number(providerId);
                });

                if (!provider) {
                    throw new Error('Provider could not be found: ' + providerId);
                }

                var successCallback = function () {
                    Backend.displayNotification(EALang.working_plan_exception_deleted);

                    var workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions) || {};
                    delete workingPlanExceptions[date];

                    for (var index in GlobalVariables.availableProviders) {
                        var availableProvider = GlobalVariables.availableProviders[index];

                        if (Number(availableProvider.id) === Number(providerId)) {
                            availableProvider.settings.working_plan_exceptions = JSON.stringify(workingPlanExceptions);
                            break;
                        }
                    }

                    $('#select-filter-item').trigger('change'); // Update the calendar.
                };

                var date = lastFocusedEventData.start.format('YYYY-MM-DD');

                BackendCalendarApi.deleteWorkingPlanException(date, providerId, successCallback);
            } else if (lastFocusedEventData.data.is_unavailable === '0') {
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
                            url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_appointment';

                            data = {
                                csrfToken: GlobalVariables.csrfToken,
                                appointment_id: lastFocusedEventData.data.id,
                                delete_reason: $('#delete-reason').val()
                            };

                            $.post(url, data)
                                .done(function () {
                                    $('#message-box').dialog('close');

                                    // Refresh calendar event items.
                                    $('#select-filter-item').trigger('change');
                                });
                        }
                    }
                ];

                GeneralFunctions.displayMessageBox(EALang.delete_appointment_title,
                    EALang.write_appointment_removal_reason, buttons);

                $('<textarea/>', {
                    'class': 'form-control w-100',
                    'id': 'delete-reason',
                    'rows': '3'
                })
                    .appendTo('#message-box');
            } else {
                // Do not display confirmation prompt.
                url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_unavailable';

                data = {
                    csrfToken: GlobalVariables.csrfToken,
                    unavailable_id: lastFocusedEventData.data.id
                };

                $.post(url, data)
                    .done(function () {
                        $('#message-box').dialog('close');

                        // Refresh calendar event items.
                        $('#select-filter-item').trigger('change');
                    });
            }
        });

        /**
         * Event: Calendar Filter Item "Change"
         *
         * Load the appointments that correspond to the select filter item and display them on the calendar.
         */
        $('#select-filter-item').on('change', function () {
            // If current value is service, then the sync buttons must be disabled.
            if ($('#select-filter-item option:selected').attr('type') === FILTER_TYPE_SERVICE) {
                $('#google-sync, #enable-sync, #insert-appointment, #insert-dropdown').prop('disabled', true);
                $('#calendar').fullCalendar('option', {
                    selectable: false,
                    editable: false,
                });
            } else {
                $('#google-sync, #enable-sync, #insert-appointment, #insert-dropdown').prop('disabled', false);
                $('#calendar').fullCalendar('option', {
                    selectable: true,
                    editable: true,
                });

                var providerId = $('#select-filter-item').val();

                var provider = GlobalVariables.availableProviders.find(function (availableProvider) {
                    return Number(availableProvider.id) === Number(providerId);
                });

                if (provider && provider.timezone) {
                    $('.provider-timezone').text(GlobalVariables.timezones[provider.timezone]);
                }

                // If the user has already the sync enabled then apply the proper style changes.
                if ($('#select-filter-item option:selected').attr('google-sync') === 'true') {
                    $('#enable-sync').removeClass('btn-light').addClass('btn-secondary enabled');
                    $('#enable-sync span').text(EALang.disable_sync);
                    $('#google-sync').prop('disabled', false);
                } else {
                    $('#enable-sync').removeClass('btn-secondary enabled').addClass('btn-light');
                    $('#enable-sync span').text(EALang.enable_sync);
                    $('#google-sync').prop('disabled', true);
                }
            }
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
        var result = window.innerHeight - $('#footer').outerHeight() - $('#header').outerHeight()
            - $('#calendar-toolbar').outerHeight() - 60; // 60 for fine tuning
        return (result > 500) ? result : 500; // Minimum height is 500px
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
    function calendarEventClick(event, jsEvent) {
        $('.popover').popover('dispose'); // Close all open popovers.

        var $html;
        var displayEdit;
        var displayDelete;

        // Depending where the user clicked the event (title or empty space) we
        // need to use different selectors to reach the parent element.
        var $parent = $(jsEvent.target.offsetParent);
        var $altParent = $(jsEvent.target).parents().eq(1);

        if ($(this).hasClass('fc-unavailable') || $parent.hasClass('fc-unavailable') || $altParent.hasClass('fc-unavailable')) {
            displayEdit = (($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom'))
                && GlobalVariables.user.privileges.appointments.edit === true)
                ? 'mr-2' : 'd-none';
            displayDelete = (($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom'))
                && GlobalVariables.user.privileges.appointments.delete === true)
                ? 'mr-2' : 'd-none'; // Same value at the time.

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.start
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(event.start.format('YYYY-MM-DD HH:mm:ss'), GlobalVariables.dateFormat, true)
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.end
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(event.end.format('YYYY-MM-DD HH:mm:ss'), GlobalVariables.dateFormat, true)
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': EALang.notes
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
                                'class': 'close-popover btn btn-outline-secondary mr-2',
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-ban mr-2'
                                    }),
                                    $('<span/>', {
                                        'text': EALang.close
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'delete-popover btn btn-outline-secondary ' + displayDelete,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-trash-alt mr-2'
                                    }),
                                    $('<span/>', {
                                        'text': EALang.delete
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'edit-popover btn btn-primary ' + displayEdit,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-edit mr-2'
                                    }),
                                    $('<span/>', {
                                        'text': EALang.edit
                                    })
                                ]
                            })
                        ]
                    })
                ]
            });
        } else if ($(this).hasClass('fc-working-plan-exception') || $parent.hasClass('fc-working-plan-exception') || $altParent.hasClass('fc-working-plan-exception')) {
            displayDelete = (($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom'))
                && GlobalVariables.user.privileges.appointments.delete === true)
                ? 'mr-2' : 'd-none'; // Same value at the time.

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.provider
                    }),
                    $('<span/>', {
                        'text': event.data ? event.data.provider.first_name + ' ' + event.data.provider.last_name : '-'
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.start
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(event.data.date + ' ' + event.data.workingPlanException.start, GlobalVariables.dateFormat, true)
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.end
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(event.data.date + ' ' + event.data.workingPlanException.end, GlobalVariables.dateFormat, true)
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.timezone
                    }),
                    $('<span/>', {
                        'text': GlobalVariables.timezones[event.data.provider.timezone]
                    }),
                    $('<br/>'),

                    $('<hr/>'),

                    $('<div/>', {
                        'class': 'd-flex justify-content-between',
                        'html': [
                            $('<button/>', {
                                'class': 'close-popover btn btn-outline-secondary mr-2',
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-ban mr-2'
                                    }),
                                    $('<span/>', {
                                        'text': EALang.close
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'delete-popover btn btn-outline-secondary ' + displayDelete,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-trash-alt mr-2'
                                    }),
                                    $('<span/>', {
                                        'text': EALang.delete
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'edit-popover btn btn-primary ' + displayEdit,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-edit mr-2'
                                    }),
                                    $('<span/>', {
                                        'text': EALang.edit
                                    })
                                ]
                            }),
                        ]
                    })
                ]
            });
        } else {
            displayEdit = (GlobalVariables.user.privileges.appointments.edit === true)
                ? 'mr-2' : 'd-none';
            displayDelete = (GlobalVariables.user.privileges.appointments.delete === true)
                ? 'mr-2' : 'd-none';

            $html = $('<div/>', {
                'html': [
                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.start
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(event.start.format('YYYY-MM-DD HH:mm:ss'), GlobalVariables.dateFormat, true)
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.end
                    }),
                    $('<span/>', {
                        'text': GeneralFunctions.formatDate(event.end.format('YYYY-MM-DD HH:mm:ss'), GlobalVariables.dateFormat, true)
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.timezone
                    }),
                    $('<span/>', {
                        'text': GlobalVariables.timezones[event.data.provider.timezone]
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.service
                    }),
                    $('<span/>', {
                        'text': event.data.service.name
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.provider
                    }),
                    GeneralFunctions.renderMapIcon(event.data.provider),
                    $('<span/>', {
                        'text': event.data.provider.first_name + ' ' + event.data.provider.last_name
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.customer
                    }),
                    GeneralFunctions.renderMapIcon(event.data.customer),
                    $('<span/>', {
                        'class': 'd-inline-block ml-1',
                        'text': event.data.customer.first_name + ' ' + event.data.customer.last_name
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.email
                    }),
                    GeneralFunctions.renderMailIcon(event.data.customer.email),
                    $('<span/>', {
                        'class': 'd-inline-block ml-1',
                        'text': event.data.customer.email
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'class': 'd-inline-block mr-2',
                        'text': EALang.phone
                    }),
                    GeneralFunctions.renderPhoneIcon(event.data.customer.phone_number),
                    $('<span/>', {
                        'class': 'd-inline-block ml-1',
                        'text': event.data.customer.phone_number
                    }),
                    $('<br/>'),

                    $('<strong/>', {
                        'text': EALang.notes
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
                                'class': 'close-popover btn btn-outline-secondary mr-2',
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-ban mr-2'
                                    }),
                                    $('<span/>', {
                                        'text': EALang.close
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'delete-popover btn btn-outline-secondary ' + displayDelete,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-trash-alt mr-2'
                                    }),
                                    $('<span/>', {
                                        'text': EALang.delete
                                    })
                                ]
                            }),
                            $('<button/>', {
                                'class': 'edit-popover btn btn-primary ' + displayEdit,
                                'html': [
                                    $('<i/>', {
                                        'class': 'fas fa-edit mr-2'
                                    }),
                                    $('<span/>', {
                                        'text': EALang.edit
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
    function calendarEventResize(event, delta, revertFunc) {
        if (GlobalVariables.user.privileges.appointments.edit === false) {
            revertFunc();
            Backend.displayNotification(EALang.no_privileges_edit_appointments);
            return;
        }

        var $calendar = $('#calendar');
        var successCallback;

        if ($('#notification').is(':visible')) {
            $('#notification').hide('bind');
        }

        if (Boolean(Number(event.data.is_unavailable)) === false) {
            // Prepare appointment data.
            event.data.end_datetime = Date.parseExact(
                event.data.end_datetime, 'yyyy-MM-dd HH:mm:ss')
                .add({days: delta.days(), hours: delta.hours(), minutes: delta.minutes()})
                .toString('yyyy-MM-dd HH:mm:ss');

            var appointment = GeneralFunctions.clone(event.data);

            // Must delete the following because only appointment data should be provided to the AJAX call.
            delete appointment.customer;
            delete appointment.provider;
            delete appointment.service;

            // Success callback
            successCallback = function () {
                // Display success notification to user.
                var undoFunction = function () {
                    appointment.end_datetime = event.data.end_datetime = Date.parseExact(
                        appointment.end_datetime, 'yyyy-MM-dd HH:mm:ss')
                        .add({days: -delta.days(), hours: -delta.hours(), minutes: -delta.minutes()})
                        .toString('yyyy-MM-dd HH:mm:ss');

                    var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_appointment';

                    var data = {
                        csrfToken: GlobalVariables.csrfToken,
                        appointment_data: JSON.stringify(appointment)
                    };

                    $.post(url, data)
                        .done(function () {
                            $('#notification').hide('blind');
                        });

                    revertFunc();
                };

                Backend.displayNotification(EALang.appointment_updated, [
                    {
                        'label': EALang.undo,
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
                    unavailable.end_datetime = event.data.end_datetime = Date.parseExact(
                        unavailable.end_datetime, 'yyyy-MM-dd HH:mm:ss')
                        .add({minutes: -delta.minutes()})
                        .toString('yyyy-MM-dd HH:mm:ss');

                    var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_unavailable';

                    var data = {
                        csrfToken: GlobalVariables.csrfToken,
                        unavailable: JSON.stringify(unavailable)
                    };

                    $.post(url, data)
                        .done(function () {
                            $('#notification').hide('blind');
                        });

                    revertFunc();
                };

                Backend.displayNotification(EALang.unavailable_updated, [
                    {
                        'label': EALang.undo,
                        'function': undoFunction
                    }
                ]);

                $('#footer').css('position', 'static'); // Footer position fix.

                // Update the event data for later use.
                $calendar.fullCalendar('updateEvent', event);
            };

            BackendCalendarApi.saveUnavailable(unavailable, successCallback, null);
        }
    }

    /**
     * Calendar Window "Resize" Callback
     *
     * The calendar element needs to be re-sized too in order to fit into the window. Nevertheless, if the window
     * becomes very small the the calendar won't shrink anymore.
     *
     * @see getCalendarHeight()
     */
    function calendarWindowResize() {
        $('#calendar').fullCalendar('option', 'height', getCalendarHeight());
    }

    /**
     * Calendar Day "Click" Callback
     *
     * When the user clicks on a day square on the calendar, then he will automatically be transferred to that
     * day view calendar.
     *
     * @param {Date} date
     */
    function calendarDayClick(date) {
        if (!date.hasTime()) {
            $('#calendar').fullCalendar('changeView', 'agendaDay');
            $('#calendar').fullCalendar('gotoDate', date);
        }
    }

    /**
     * Calendar Event "Drop" Callback
     *
     * This event handler is triggered whenever the user drags and drops an event into a different position
     * on the calendar. We need to update the database with this change. This is done via an ajax call.
     *
     * @param {object} event
     * @param {object} delta
     * @param {function} revertFunc
     */
    function calendarEventDrop(event, delta, revertFunc) {
        if (GlobalVariables.user.privileges.appointments.edit === false) {
            revertFunc();
            Backend.displayNotification(EALang.no_privileges_edit_appointments);
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

            appointment.start_datetime = Date.parseExact(
                appointment.start_datetime, 'yyyy-MM-dd HH:mm:ss')
                .add({days: delta.days(), hours: delta.hours(), minutes: delta.minutes()})
                .toString('yyyy-MM-dd HH:mm:ss');

            appointment.end_datetime = Date.parseExact(
                appointment.end_datetime, 'yyyy-MM-dd HH:mm:ss')
                .add({days: delta.days(), hours: delta.hours(), minutes: delta.minutes()})
                .toString('yyyy-MM-dd HH:mm:ss');

            event.data.start_datetime = appointment.start_datetime;
            event.data.end_datetime = appointment.end_datetime;

            // Define success callback function.
            successCallback = function () {
                // Define the undo function, if the user needs to reset the last change.
                var undoFunction = function () {
                    appointment.start_datetime = Date.parseExact(
                        appointment.start_datetime, 'yyyy-MM-dd HH:mm:ss')
                        .add({days: -delta.days(), hours: -delta.hours(), minutes: -delta.minutes()})
                        .toString('yyyy-MM-dd HH:mm:ss');

                    appointment.end_datetime = Date.parseExact(
                        appointment.end_datetime, 'yyyy-MM-dd HH:mm:ss')
                        .add({days: -delta.days(), hours: -delta.hours(), minutes: -delta.minutes()})
                        .toString('yyyy-MM-dd HH:mm:ss');

                    event.data.start_datetime = appointment.start_datetime;
                    event.data.end_datetime = appointment.end_datetime;

                    var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_appointment';

                    var data = {
                        csrfToken: GlobalVariables.csrfToken,
                        appointment_data: JSON.stringify(appointment)
                    };

                    $.post(url, data)
                        .done(function () {
                            $('#notification').hide('blind');
                        });

                    revertFunc();
                };

                Backend.displayNotification(EALang.appointment_updated, [
                    {
                        'label': EALang.undo,
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
                    unavailable.start_datetime = Date.parseExact(
                        unavailable.start_datetime, 'yyyy-MM-dd HH:mm:ss')
                        .add({days: -delta.days(), minutes: -delta.minutes()})
                        .toString('yyyy-MM-dd HH:mm:ss');

                    unavailable.end_datetime = Date.parseExact(
                        unavailable.end_datetime, 'yyyy-MM-dd HH:mm:ss')
                        .add({days: -delta.days(), minutes: -delta.minutes()})
                        .toString('yyyy-MM-dd HH:mm:ss');

                    event.data.start_datetime = unavailable.start_datetime;
                    event.data.end_datetime = unavailable.end_datetime;

                    var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_unavailable';
                    var data = {
                        csrfToken: GlobalVariables.csrfToken,
                        unavailable: JSON.stringify(unavailable)
                    };

                    $.post(url, data)
                        .done(function () {
                            $('#notification').hide('blind');
                        });

                    revertFunc();
                };

                Backend.displayNotification(EALang.unavailable_updated, [
                    {
                        label: EALang.undo,
                        function: undoFunction
                    }
                ]);

                $('#footer').css('position', 'static'); // Footer position fix.
            };

            BackendCalendarApi.saveUnavailable(unavailable, successCallback);
        }
    }

    /**
     * Calendar "View Render" Callback
     *
     * Whenever the calendar changes or refreshes its view certain actions need to be made, in order to
     * display proper information to the user.
     */
    function calendarViewRender() {
        if ($('#select-filter-item').val() === null) {
            return;
        }

        refreshCalendarAppointments(
            $('#calendar'),
            $('#select-filter-item').val(),
            $('#select-filter-item option:selected').attr('type'),
            $('#calendar').fullCalendar('getView').start,
            $('#calendar').fullCalendar('getView').end);

        $(window).trigger('resize'); // Places the footer on the bottom.

        // Remove all open popovers.
        $('.close-popover').each(function (index, closePopoverButton) {
            $(closePopoverButton).parents('.popover').popover('dispose');
        });

        // Add new pop overs.
        $('.fv-events').each(function (index, eventElement) {
            $(eventElement).popover();
        });
    }

    /**
     * Convert titles to HTML
     *
     * On some calendar events the titles contain html markup that is not displayed properly due to the
     * FullCalendar plugin. This plugin sets the .fc-event-title value by using the $.text() method and
     * not the $.html() method. So in order for the title to display the html properly we convert all the
     * .fc-event-titles where needed into html.
     */
    function convertTitlesToHtml() {
        // Convert the titles to html code.
        $('.fc-custom').each(function (index, customEventElement) {
            var title = $(customEventElement).find('.fc-event-title').text();
            $(customEventElement).find('.fc-event-title').html(title);
            var time = $(customEventElement).find('.fc-event-time').text();
            $(customEventElement).find('.fc-event-time').html(time);
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
     * @param {Date} startDate Visible start date of the calendar.
     * @param {Date} endDate Visible end date of the calendar.
     */
    function refreshCalendarAppointments($calendar, recordId, filterType, startDate, endDate) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_get_calendar_appointments';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            record_id: recordId,
            start_date: startDate.format('YYYY-MM-DD'),
            end_date: endDate.format('YYYY-MM-DD'),
            filter_type: filterType
        };

        $('#loading').css('visibility', 'hidden');

        var calendarEventSource = [];

        return $.post(url, data)
            .done(function (response) {
                var $calendar = $('#calendar');

                $calendar.fullCalendar('removeEvents');

                // Add appointments to calendar.
                var appointmentEvents = [];
                response.appointments.forEach(function (appointment) {
                    var appointmentEvent = {
                        id: appointment.id,
                        title: appointment.service.name + ' - '
                            + appointment.customer.first_name + ' '
                            + appointment.customer.last_name,
                        start: moment(appointment.start_datetime),
                        end: moment(appointment.end_datetime),
                        allDay: false,
                        data: appointment // Store appointment data for later use.
                    };

                    calendarEventSource.push(appointmentEvent);
                });

                // Add custom unavailable periods (they are always displayed on the calendar, even if the provider won't
                // work on that day).
                response.unavailables.forEach(function (unavailable) {
                    var notes = unavailable.notes ? ' - ' + unavailable.notes : '';

                    if (unavailable.notes && unavailable.notes.length > 30) {
                        notes = unavailable.notes.substring(0, 30) + '...';
                    }

                    var unavailabilityEvent = {
                        title: EALang.unavailable + notes,
                        start: moment(unavailable.start_datetime),
                        end: moment(unavailable.end_datetime),
                        allDay: false,
                        color: '#879DB4',
                        editable: true,
                        className: 'fc-unavailable fc-custom',
                        data: unavailable
                    };

                    calendarEventSource.push(unavailabilityEvent);
                });

                var calendarView = $('#calendar').fullCalendar('getView');

                if (filterType === FILTER_TYPE_PROVIDER && calendarView.name !== 'month') {
                    var provider = GlobalVariables.availableProviders.find(function (availableProvider) {
                        return Number(availableProvider.id) === Number(recordId);
                    });

                    if (!provider) {
                        throw new Error('Provider was not found.');
                    }

                    var workingPlan = JSON.parse(provider.settings.working_plan);
                    var workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions);
                    var unavailabilityEvent;
                    var viewStart;
                    var viewEnd;
                    var breakStart;
                    var breakEnd;
                    var workingPlanExceptionStart;
                    var workingPlanExceptionEnd;
                    var weekdayNumber;
                    var weekdayName;
                    var weekdayDate;
                    var workingPlanExceptionEvent;
                    var startHour;
                    var endHour;
                    var workDateStart;
                    var workDateEnd;

                    // Sort the working plan starting with the first day as set in General settings to correctly align
                    // breaks in the calendar display.
                    var firstWeekdayNumber = GeneralFunctions.getWeekDayId(GlobalVariables.firstWeekday);
                    var sortedWorkingPlan = GeneralFunctions.sortWeekDictionary(workingPlan, firstWeekdayNumber);

                    switch (calendarView.name) {
                        case 'agendaDay':
                            weekdayNumber = parseInt(calendarView.start.format('d'));
                            weekdayName = GeneralFunctions.getWeekdayName(weekdayNumber);
                            weekdayDate = calendarView.start.clone().format('YYYY-MM-DD');

                            // Add working plan exception.
                            if (workingPlanExceptions && workingPlanExceptions[weekdayDate]) {
                                sortedWorkingPlan[weekdayName] = workingPlanExceptions[weekdayDate];
                                workingPlanExceptionStart = weekdayDate + ' ' + sortedWorkingPlan[weekdayName].start;
                                workingPlanExceptionEnd = weekdayDate + ' ' + sortedWorkingPlan[weekdayName].end;

                                workingPlanExceptionEvent = {
                                    title: EALang.working_plan_exception,
                                    start: moment(workingPlanExceptionStart, 'YYYY-MM-DD HH:mm', true),
                                    end: moment(workingPlanExceptionEnd, 'YYYY-MM-DD HH:mm', true).add(1, 'day'),
                                    allDay: true,
                                    color: '#879DB4',
                                    editable: false,
                                    className: 'fc-working-plan-exception fc-custom',
                                    data: {
                                        date: weekdayDate,
                                        workingPlanException: workingPlanExceptions[weekdayDate],
                                        provider: provider
                                    }
                                };

                                calendarEventSource.push(workingPlanExceptionEvent);
                            }

                            // Non-working day.
                            if (sortedWorkingPlan[weekdayName] === null) {
                                // Working plan exception.
                                unavailabilityEvent = {
                                    title: EALang.not_working,
                                    start: calendarView.intervalStart.clone(),
                                    end: calendarView.intervalEnd.clone(),
                                    allDay: false,
                                    color: '#BEBEBE',
                                    editable: false,
                                    className: 'fc-unavailable'
                                };

                                calendarEventSource.push(unavailabilityEvent);

                                return; // Go to next loop.
                            }

                            // Add unavailable period before work starts.
                            viewStart = moment(calendarView.start.format('YYYY-MM-DD') + ' 00:00:00');
                            startHour = sortedWorkingPlan[weekdayName].start.split(':');
                            workDateStart = viewStart.clone();
                            workDateStart.hour(parseInt(startHour[0]));
                            workDateStart.minute(parseInt(startHour[1]));

                            if (viewStart < workDateStart) {
                                var unavailablePeriodBeforeWorkStarts = {
                                    title: EALang.not_working,
                                    start: viewStart,
                                    end: workDateStart,
                                    allDay: false,
                                    color: '#BEBEBE',
                                    editable: false,
                                    className: 'fc-unavailable'
                                };

                                calendarEventSource.push(unavailablePeriodBeforeWorkStarts);
                            }

                            // Add unavailable period after work ends.
                            viewEnd = moment(calendarView.end.format('YYYY-MM-DD') + ' 00:00:00');
                            endHour = sortedWorkingPlan[weekdayName].end.split(':');
                            workDateEnd = viewStart.clone();
                            workDateEnd.hour(parseInt(endHour[0]));
                            workDateEnd.minute(parseInt(endHour[1]));

                            if (viewEnd > workDateEnd) {
                                var unavailablePeriodAfterWorkEnds = {
                                    title: EALang.not_working,
                                    start: workDateEnd,
                                    end: viewEnd,
                                    allDay: false,
                                    color: '#BEBEBE',
                                    editable: false,
                                    className: 'fc-unavailable'
                                };

                                calendarEventSource.push(unavailablePeriodAfterWorkEnds);
                            }

                            // Add unavailable periods for breaks.
                            sortedWorkingPlan[weekdayName].breaks.forEach(function (breakPeriod) {
                                var breakStartString = breakPeriod.start.split(':');
                                breakStart = viewStart.clone();
                                breakStart.hour(parseInt(breakStartString[0]));
                                breakStart.minute(parseInt(breakStartString[1]));

                                var breakEndString = breakPeriod.end.split(':');
                                breakEnd = viewStart.clone();
                                breakEnd.hour(parseInt(breakEndString[0]));
                                breakEnd.minute(parseInt(breakEndString[1]));

                                var unavailablePeriod = {
                                    title: EALang.break,
                                    start: breakStart,
                                    end: breakEnd,
                                    allDay: false,
                                    color: '#BEBEBE',
                                    editable: false,
                                    className: 'fc-unavailable fc-break'
                                };

                                calendarEventSource.push(unavailablePeriod);
                            });

                            break;

                        case 'agendaWeek':
                            var calendarDate = calendarView.start.clone();

                            while (calendarDate < calendarView.end) {
                                weekdayNumber = parseInt(calendarDate.format('d'))
                                weekdayName = GeneralFunctions.getWeekdayName(weekdayNumber);
                                weekdayDate = calendarDate.format('YYYY-MM-DD');

                                // Add working plan exception event.
                                if (workingPlanExceptions && workingPlanExceptions[weekdayDate]) {
                                    sortedWorkingPlan[weekdayName] = workingPlanExceptions[weekdayDate];

                                    workingPlanExceptionStart = weekdayDate + ' ' + sortedWorkingPlan[weekdayName].start;
                                    workingPlanExceptionEnd = weekdayDate + ' ' + sortedWorkingPlan[weekdayName].end;

                                    workingPlanExceptionEvent = {
                                        title: EALang.working_plan_exception,
                                        start: moment(workingPlanExceptionStart, 'YYYY-MM-DD HH:mm', true),
                                        end: moment(workingPlanExceptionEnd, 'YYYY-MM-DD HH:mm', true).add(1, 'day'),
                                        allDay: true,
                                        color: '#879DB4',
                                        editable: false,
                                        className: 'fc-working-plan-exception fc-custom',
                                        data: {
                                            date: weekdayDate,
                                            workingPlanException: workingPlanExceptions[weekdayDate],
                                            provider: provider
                                        }
                                    };

                                    calendarEventSource.push(workingPlanExceptionEvent);
                                }

                                // Non-working day.
                                if (sortedWorkingPlan[weekdayName] === null) {
                                    // Add a full day unavailable event.
                                    unavailabilityEvent = {
                                        title: EALang.not_working,
                                        start: calendarDate.clone(),
                                        end: calendarDate.clone().add(1, 'day'),
                                        allDay: false,
                                        color: '#BEBEBE',
                                        editable: false,
                                        className: 'fc-unavailable'
                                    };

                                    calendarEventSource.push(unavailabilityEvent);

                                    calendarDate.add(1, 'day');

                                    continue; // Go to the next loop.
                                }

                                // Add unavailable period before work starts.
                                startHour = sortedWorkingPlan[weekdayName].start.split(':');
                                workDateStart = calendarDate.clone();
                                workDateStart.hour(parseInt(startHour[0]));
                                workDateStart.minute(parseInt(startHour[1]));

                                if (calendarDate < workDateStart) {
                                    unavailabilityEvent = {
                                        title: EALang.not_working,
                                        start: calendarDate.clone(),
                                        end: moment(calendarDate.format('YYYY-MM-DD') + ' ' + sortedWorkingPlan[weekdayName].start + ':00'),
                                        allDay: false,
                                        color: '#BEBEBE',
                                        editable: false,
                                        className: 'fc-unavailable'
                                    };

                                    calendarEventSource.push(unavailabilityEvent);
                                }

                                // Add unavailable period after work ends.
                                endHour = sortedWorkingPlan[weekdayName].end.split(':');
                                workDateEnd = calendarDate.clone();
                                workDateEnd.hour(parseInt(endHour[0]));
                                workDateEnd.minute(parseInt(endHour[1]));

                                if (calendarView.end > workDateEnd) {
                                    unavailabilityEvent = {
                                        title: EALang.not_working,
                                        start: moment(calendarDate.format('YYYY-MM-DD') + ' ' + sortedWorkingPlan[weekdayName].end + ':00'),
                                        end: calendarDate.clone().add(1, 'day'),
                                        allDay: false,
                                        color: '#BEBEBE',
                                        editable: false,
                                        className: 'fc-unavailable'
                                    };

                                    calendarEventSource.push(unavailabilityEvent);
                                }

                                // Add unavailable periods during day breaks.
                                sortedWorkingPlan[weekdayName].breaks.forEach(function (breakPeriod) {
                                    var breakStartString = breakPeriod.start.split(':');
                                    breakStart = calendarDate.clone();
                                    breakStart.hour(parseInt(breakStartString[0]));
                                    breakStart.minute(parseInt(breakStartString[1]));

                                    var breakEndString = breakPeriod.end.split(':');
                                    breakEnd = calendarDate.clone();
                                    breakEnd.hour(parseInt(breakEndString[0]));
                                    breakEnd.minute(parseInt(breakEndString[1]));

                                    var unavailabilityEvent = {
                                        title: EALang.break,
                                        start: moment(calendarDate.format('YYYY-MM-DD') + ' ' + breakPeriod.start),
                                        end: moment(calendarDate.format('YYYY-MM-DD') + ' ' + breakPeriod.end),
                                        allDay: false,
                                        color: '#BEBEBE',
                                        editable: false,
                                        className: 'fc-unavailable fc-break'
                                    };

                                    calendarEventSource.push(unavailabilityEvent);
                                });

                                calendarDate.add(1, 'day');
                            }

                            break;
                    }
                }
            })
            .always(function () {
                $('#loading').css('visibility', '')
                $calendar.fullCalendar('addEventSource', calendarEventSource);
            });
    }


    exports.initialize = function () {
        // Dynamic date formats.
        var columnFormat = {};

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
                throw new Error('Invalid time format setting provided!', GlobalVariables.timeFormat);
        }

        var defaultView = window.innerWidth < 468 ? 'agendaDay' : 'agendaWeek';

        var firstWeekday = GlobalVariables.firstWeekday;
        var firstWeekdayNumber = GeneralFunctions.getWeekDayId(firstWeekday);

        // Initialize page calendar
        $('#calendar').fullCalendar({
            defaultView: defaultView,
            height: getCalendarHeight(),
            editable: true,
            firstDay: firstWeekdayNumber,
            slotDuration: '00:15:00', 
            snapDuration: '00:15:00',
            slotLabelInterval: '01:00',
            timeFormat: timeFormat,
            slotLabelFormat: slotTimeFormat,
            allDayText: EALang.all_day,
            columnFormat: columnFormat,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'agendaDay,agendaWeek,month'
            },

            // Selectable
            selectable: true,
            selectHelper: true,
            select: function (start, end) {
                if (!start.hasTime() || !end.hasTime()) {
                    return;
                }

                $('#insert-appointment').trigger('click');

                // Preselect service & provider.
                var service;

                if ($('#select-filter-item option:selected').attr('type') === FILTER_TYPE_SERVICE) {
                    service = GlobalVariables.availableServices.find(function (service) {
                        return Number(service.id) === Number($('#select-filter-item').val());
                    });
                    $('#select-service').val(service.id).trigger('change');

                } else {
                    var provider = GlobalVariables.availableProviders.find(function (provider) {
                        return Number(provider.id) === Number($('#select-filter-item').val());
                    });

                    service = GlobalVariables.availableServices.find(function (service) {
                        return provider.services.indexOf(service.id) !== -1
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
                }

                // Preselect time
                $('#start-datetime').datepicker('setDate', new Date(start.format('YYYY/MM/DD HH:mm:ss')));
                $('#end-datetime').datepicker('setDate', new Date(end.format('YYYY/MM/DD HH:mm:ss')));

                return false;
            },

            // Translations
            monthNames: [EALang.january, EALang.february, EALang.march, EALang.april,
                EALang.may, EALang.june, EALang.july, EALang.august,
                EALang.september, EALang.october, EALang.november,
                EALang.december],
            monthNamesShort: [EALang.january.substr(0, 3), EALang.february.substr(0, 3),
                EALang.march.substr(0, 3), EALang.april.substr(0, 3),
                EALang.may.substr(0, 3), EALang.june.substr(0, 3),
                EALang.july.substr(0, 3), EALang.august.substr(0, 3),
                EALang.september.substr(0, 3), EALang.october.substr(0, 3),
                EALang.november.substr(0, 3), EALang.december.substr(0, 3)],
            dayNames: [EALang.sunday, EALang.monday, EALang.tuesday, EALang.wednesday,
                EALang.thursday, EALang.friday, EALang.saturday],
            dayNamesShort: [EALang.sunday.substr(0, 3), EALang.monday.substr(0, 3),
                EALang.tuesday.substr(0, 3), EALang.wednesday.substr(0, 3),
                EALang.thursday.substr(0, 3), EALang.friday.substr(0, 3),
                EALang.saturday.substr(0, 3)],
            dayNamesMin: [EALang.sunday.substr(0, 2), EALang.monday.substr(0, 2),
                EALang.tuesday.substr(0, 2), EALang.wednesday.substr(0, 2),
                EALang.thursday.substr(0, 2), EALang.friday.substr(0, 2),
                EALang.saturday.substr(0, 2)],
            buttonText: {
                today: EALang.today,
                day: EALang.day,
                week: EALang.week,
                month: EALang.month
            },

            // Calendar events need to be declared on initialization.
            windowResize: calendarWindowResize,
            viewRender: calendarViewRender,
            dayClick: calendarDayClick,
            eventClick: calendarEventClick,
            eventResize: calendarEventResize,
            eventDrop: calendarEventDrop,
            eventAfterAllRender: convertTitlesToHtml
        });

        // Trigger once to set the proper footer position after calendar initialization.
        calendarWindowResize();

        // Fill the select list boxes of the page.
        if (GlobalVariables.availableProviders.length > 0) {
            $('<optgroup/>', {
                'label': EALang.providers,
                'type': 'providers-group',
                'html': GlobalVariables.availableProviders.map(function (availableProvider) {
                    var hasGoogleSync = availableProvider.settings.google_sync === '1' ? 'true' : 'false';

                    return $('<option/>', {
                        'value': availableProvider.id,
                        'type': FILTER_TYPE_PROVIDER,
                        'google-sync': hasGoogleSync,
                        'text': availableProvider.first_name + ' ' + availableProvider.last_name
                    })
                })
            })
                .appendTo('#select-filter-item');
        }

        if (GlobalVariables.availableServices.length > 0) {
            $('<optgroup/>', {
                'label': EALang.services,
                'type': 'services-group',
                'html': GlobalVariables.availableServices.map(function (availableService) {
                    return $('<option/>', {
                        'value': availableService.id,
                        'type': FILTER_TYPE_SERVICE,
                        'text': availableService.name
                    })
                })
            })
                .appendTo('#select-filter-item');
        }

        // Check permissions.
        if (GlobalVariables.user.role_slug === Backend.DB_SLUG_PROVIDER) {
            $('#select-filter-item optgroup:eq(0)')
                .find('option[value="' + GlobalVariables.user.id + '"]')
                .prop('selected', true);
            $('#select-filter-item').prop('disabled', true);
        }

        if (GlobalVariables.user.role_slug === Backend.DB_SLUG_SECRETARY) {
            // Remove the providers that are not connected to the secretary.
            $('#select-filter-item optgroup:eq(1)').remove();

            $('#select-filter-item option[type="provider"]').each(function (index, option) {
                var provider = GlobalVariables.secretaryProviders.find(function (secretaryProviderId) {
                    return Number($(option).val()) === Number(secretaryProviderId);
                });

                if (!provider) {
                    $(option).remove();
                }
            });

            if (!$('#select-filter-item option[type="provider"]').length) {
                $('#select-filter-item optgroup[type="providers-group"]').remove();
            }
        }

        // Bind the default event handlers.
        bindEventHandlers();

        $('#select-filter-item').trigger('change');

        // Display the edit dialog if an appointment hash is provided.
        if (GlobalVariables.editAppointment) {
            var $dialog = $('#manage-appointment');
            var appointment = GlobalVariables.editAppointment;
            BackendCalendarAppointmentsModal.resetAppointmentDialog();

            $dialog.find('.modal-header h3').text(EALang.edit_appointment_title);
            $dialog.find('#appointment-id').val(appointment.id);
            $dialog.find('#select-service').val(appointment.id_services).trigger('change');
            $dialog.find('#select-provider').val(appointment.id_users_provider);

            // Set the start and end datetime of the appointment.
            var startDatetime = Date.parseExact(appointment.start_datetime, 'yyyy-MM-dd HH:mm:ss');
            $dialog.find('#start-datetime').datetimepicker('setDate', startDatetime);

            var endDatetime = Date.parseExact(appointment.end_datetime, 'yyyy-MM-dd HH:mm:ss');
            $dialog.find('#end-datetime').datetimepicker('setDate', endDatetime);

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
        }

        if (!$('#select-filter-item option').length) {
            $('#calendar-actions button').prop('disabled', true);
        }

        // Fine tune the footer's position only for this page.
        if (window.innerHeight < 700) {
            $('#footer').css('position', 'static');
        }

        // Automatically refresh the calendar page every 10 seconds (without loading animation).
        var $calendar = $('#calendar');
        var $selectFilterItem = $('#select-filter-item');

        setInterval(function () {
            var calendarView = $calendar.fullCalendar('getView');

            refreshCalendarAppointments(
                $calendar,
                $selectFilterItem.val(),
                $selectFilterItem.find('option:selected').attr('type'),
                calendarView.start,
                calendarView.end);
        }, 60000);
    };

})(window.BackendCalendarDefaultView);
