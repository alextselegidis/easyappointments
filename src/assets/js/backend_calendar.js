/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Backend Calendar
 *
 * This module contains functions that are used by the backend calendar page.
 *
 * @module BackendCalendar
 */
window.BackendCalendar = window.BackendCalendar || {};

(function(exports) {

    'use strict';

    // Variables
    var lastFocusedEventData; // Contains event data for later use.

    /**
     * Bind Event Handlers
     *
     * This method binds the default event handlers for the backend calendar page. If you do not need the
     * default handlers then initialize the page by setting the "defaultEventHandlers" argument to "false".
     */
    function _bindEventHandlers() {

        var $calendarPage = $('#calendar-page'); 
        
        /**
         * Event: Google Sync Button "Click"
         *
         * Trigger the synchronization algorithm.
         */
        $('#google-sync').click(function() {
            var url = GlobalVariables.baseUrl + '/index.php/google/sync/' + $('#select-filter-item').val();

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json'
            })
                .done(function(response) {
                    if (response.exceptions) {
                        response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                        GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE,
                                GeneralFunctions.EXCEPTIONS_MESSAGE);
                        $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                        return;
                    }

                    if (response.warnings) {
                        response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                        GeneralFunctions.displayMessageBox(GeneralFunctions.WARNINGS_TITLE,
                                GeneralFunctions.WARNINGS_MESSAGE);
                        $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
                    }

                    Backend.displayNotification(EALang['google_sync_completed']);
                    $('#reload-appointments').trigger('click');
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    Backend.displayNotification(EALang['google_sync_failed']);
                });
        });

        /**
         * Event: Reload Button "Click"
         *
         * When the user clicks the reload button an the calendar items need to be refreshed.
         */
        $('#reload-appointments').click(function() {
            $('#select-filter-item').trigger('change');
        });

        /**
         * Event: Popover Close Button "Click"
         *
         * Hides the open popover element.
         */
        $calendarPage.on('click', '.close-popover', function() {
            $(this).parents().eq(2).remove();
        });

        /**
         * Event: Popover Edit Button "Click"
         *
         * Enables the edit dialog of the selected calendar event.
         */
        $calendarPage.on('click', '.edit-popover', function() {
            $(this).parents().eq(2).remove(); // Hide the popover

            var $dialog;

            if (lastFocusedEventData.data.is_unavailable == false) {
                var appointment = lastFocusedEventData.data;
                $dialog = $('#manage-appointment');

                _resetAppointmentDialog();

                // Apply appointment data and show modal dialog.
                $dialog.find('.modal-header h3').text(EALang['edit_appointment_title']);
                $dialog.find('#appointment-id').val(appointment['id']);
                $dialog.find('#select-service').val(appointment['id_services']).trigger('change');
                $dialog.find('#select-provider').val(appointment['id_users_provider']);

                // Set the start and end datetime of the appointment.
                var startDatetime = Date.parseExact(appointment['start_datetime'],
                        'yyyy-MM-dd HH:mm:ss');
                $dialog.find('#start-datetime').datetimepicker('setDate', startDatetime);

                var endDatetime = Date.parseExact(appointment['end_datetime'],
                        'yyyy-MM-dd HH:mm:ss');
                $dialog.find('#end-datetime').datetimepicker('setDate', endDatetime);

                var customer = appointment['customer'];
                $dialog.find('#customer-id').val(appointment['id_users_customer']);
                $dialog.find('#first-name').val(customer['first_name']);
                $dialog.find('#last-name').val(customer['last_name']);
                $dialog.find('#email').val(customer['email']);
                $dialog.find('#phone-number').val(customer['phone_number']);
                $dialog.find('#address').val(customer['address']);
                $dialog.find('#city').val(customer['city']);
                $dialog.find('#zip-code').val(customer['zip_code']);
                $dialog.find('#appointment-notes').val(appointment['notes']);
                $dialog.find('#customer-notes').val(customer['notes']);
            } else {
                var unavailable = lastFocusedEventData.data;

                // Replace string date values with actual date objects.
                unavailable.start_datetime = GeneralFunctions.clone(lastFocusedEventData.start);
                unavailable.end_datetime = GeneralFunctions.clone(lastFocusedEventData.end);

                $dialog = $('#manage-unavailable');
                _resetUnavailableDialog();

                // Apply unvailable data to dialog.
                $dialog.find('.modal-header h3').text('Edit Unavailable Period');
                $dialog.find('#unavailable-start').datetimepicker('setDate', unavailable.start_datetime);
                $dialog.find('#unavailable-id').val(unavailable.id);
                $dialog.find('#unavailable-end').datetimepicker('setDate', unavailable.end_datetime);
                $dialog.find('#unavailable-notes').val(unavailable.notes);
            }

            // :: DISPLAY EDIT DIALOG
            $dialog.modal('show');
        });

        /**
         * Event: Popover Delete Button "Click"
         *
         * Displays a prompt on whether the user wants the appoinmtent to be deleted. If he confirms the
         * deletion then an ajax call is made to the server and deletes the appointment from the database.
         */
        $calendarPage.on('click', '.delete-popover', function() {
            $(this).parents().eq(2).remove(); // Hide the popover

            if (lastFocusedEventData.data.is_unavailable == false) {
                var messageButtons = {};
                messageButtons['OK'] = function() {
                    var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_appointment';

                    var postData = {
                        'csrfToken': GlobalVariables.csrfToken,
                        'appointment_id' : lastFocusedEventData.data['id'],
                        'delete_reason': $('#delete-reason').val()
                    };

                    $.post(postUrl, postData, function(response) {
                        $('#message_box').dialog('close');

                        if (response.exceptions) {
                            response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                            GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE,
                                    GeneralFunctions.EXCEPTIONS_MESSAGE);
                            $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                            return;
                        }

                        if (response.warnings) {
                            response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                            GeneralFunctions.displayMessageBox(GeneralFunctions.WARNINGS_TITLE,
                                    GeneralFunctions.WARNINGS_MESSAGE);
                            $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
                        }

                        // Refresh calendar event items.
                        $('#select-filter-item').trigger('change');
                    }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
                };

                messageButtons[EALang['cancel']] = function() {
                    $('#message_box').dialog('close');
                };

                GeneralFunctions.displayMessageBox(EALang['delete_appointment_title'],
                        EALang['write_appointment_removal_reason'], messageButtons);

                $('#message_box').append('<textarea id="delete-reason" rows="3"></textarea>');
                $('#delete-reason').css('width', '100%');
            } else {
                // Do not display confirmation promt.
                var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_unavailable';
                var postData = {
                    csrfToken: GlobalVariables.csrfToken,
                    unavailable_id : lastFocusedEventData.data.id
                };

                $.post(postUrl, postData, function(response) {
                    $('#message_box').dialog('close');

                    if (response.exceptions) {
                        response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                        GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE, GeneralFunctions.EXCEPTIONS_MESSAGE);
                        $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                        return;
                    }

                    if (response.warnings) {
                        response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                        GeneralFunctions.displayMessageBox(GeneralFunctions.WARNINGS_TITLE, GeneralFunctions.WARNINGS_MESSAGE);
                        $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
                    }

                    // Refresh calendar event items.
                    $('#select-filter-item').trigger('change');
                }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
            }
        });

        /**
         * Event: Manage Appointments Dialog Cancel Button "Click"
         *
         * Closes the dialog without saving any changes to the database.
         */
        $('#manage-appointment #cancel-appointment').click(function() {
            $('#manage-appointment').modal('hide');
        });

        /**
         * Event: Manage Appointments Dialog Save Button "Click"
         *
         * Stores the appointment changes or inserts a new appointment depending the dialog mode.
         */
        $('#manage-appointment #save-appointment').click(function() {
            // Before doing anything the appointment data need to be validated.
            if (!_validateAppointmentForm()) {
                return;
            }

            // Prepare appointment data for AJAX request.
            var $dialog = $('#manage-appointment');

            // ID must exist on the object in order for the model to update the record and not to perform
            // an insert operation.

            var startDatetime = $dialog.find('#start-datetime')
                    .datepicker('getDate').toString('yyyy-MM-dd HH:mm:ss');
            var endDatetime = $dialog.find('#end-datetime')
                    .datepicker('getDate').toString('yyyy-MM-dd HH:mm:ss');

            var appointment = {
                id_services: $dialog.find('#select-service').val(),
                id_users_provider: $dialog.find('#select-provider').val(),
                start_datetime: startDatetime,
                end_datetime: endDatetime,
                notes: $dialog.find('#appointment-notes').val(),
                is_unavailable: false
            };

            if ($dialog.find('#appointment-id').val() !== '') {
                // Set the id value, only if we are editing an appointment.
                appointment['id'] = $dialog.find('#appointment-id').val();
            }

            var customer = {
                first_name: $dialog.find('#first-name').val(),
                last_name: $dialog.find('#last-name').val(),
                email: $dialog.find('#email').val(),
                phone_number: $dialog.find('#phone-number').val(),
                address: $dialog.find('#address').val(),
                city: $dialog.find('#city').val(),
                zip_code: $dialog.find('#zip-code').val(),
                notes: $dialog.find('#customer-notes').val()
            };

            if ($dialog.find('#customer-id').val() !== '') {
                // Set the id value, only if we are editing an appointment.
                customer['id'] = $dialog.find('#customer-id').val();
                appointment['id_users_customer'] = customer['id'];
            }

            // Define success callback.
            var successCallback = function(response) {
                if (!GeneralFunctions.handleAjaxExceptions(response)) {
                    $dialog.find('.modal-message').text(EALang['unexpected_issues_occurred']);
                    $dialog.find('.modal-message').addClass('alert-danger').removeClass('hidden');
                    return false;
                }

                // Display success message to the user.
                $dialog.find('.modal-message').text(EALang['appointment_saved']);
                $dialog.find('.modal-message').addClass('alert-success').removeClass('alert-danger hidden');
                $dialog.find('.modal-body').scrollTop(0);

                // Close the modal dialog and refresh the calendar appointments after one second.
                setTimeout(function() {
                    $dialog.find('.alert').addClass('hidden');
                    $dialog.modal('hide');
                    $('#select-filter-item').trigger('change');
                }, 2000);
            };

            // Define error callback.
            var errorCallback = function() {
                $dialog.find('.modal-message').text(EALang['server_communication_error']);
                $dialog.find('.modal-message').addClass('alert-danger').removeClass('hidden');
                $dialog.find('.modal-body').scrollTop(0);
            };

            // Save appointment data.
            _saveAppointment(appointment, customer, successCallback, errorCallback);
        });

        /**
         * Event: Manage Unavailable Dialog Save Button "Click"
         *
         * Stores the unavailable period changes or inserts a new record.
         */
        $('#manage-unavailable #save-unavailable').click(function() {
            var $dialog = $('#manage-unavailable');
            var start = $dialog.find('#unavailable-start').datetimepicker('getDate');
            var end = $dialog.find('#unavailable-end').datetimepicker('getDate');

            if (start > end) {
                // Start time is after end time - display message to user.
                $dialog.find('.modal-message')
                    .text(EALang['start_date_before_end_error'])
                    .addClass('alert-danger')
                    .removeClass('hidden');
                return;
            }

            // Unavailable period records go to the appointments table.
            var unavailable = {
                start_datetime: start.toString('yyyy-MM-dd HH:mm'),
                end_datetime: end.toString('yyyy-MM-dd HH:mm'),
                notes: $dialog.find('#unavailable-notes').val(),
                id_users_provider: $('#select-filter-item').val() // curr provider
            };

            if ($dialog.find('#unavailable-id').val() !== '') {
                // Set the id value, only if we are editing an appointment.
                unavailable.id = $dialog.find('#unavailable-id').val();
            }

            var successCallback = function(response) {
                if (response.exceptions) {
                    response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                    GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE,
                            GeneralFunctions.EXCEPTIONS_MESSAGE);
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));

                    $dialog.find('.modal-message')
                        .text(EALang['unexpected_issues_occurred'])
                        .addClass('alert-danger')
                        .removeClass('hidden');

                    return;
                }

                if (response.warnings) {
                    response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                    GeneralFunctions.displayMessageBox(GeneralFunctions.WARNINGS_TITLE,
                            GeneralFunctions.WARNINGS_MESSAGE);
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
                }

                // Display success message to the user.
                $dialog.find('.modal-message')
                    .text(EALang['unavailable_saved'])
                    .addClass('alert-success')
                    .removeClass('alert-danger hidden');

                // Close the modal dialog and refresh the calendar appointments after one second.
                setTimeout(function() {
                    $dialog.find('.alert').addClass('hidden');
                    $dialog.modal('hide');
                    $('#select-filter-item').trigger('change');
                }, 2000);
            };

            var errorCallback = function(jqXHR, textStatus, errorThrown) {
                GeneralFunctions.displayMessageBox('Communication Error', 'Unfortunately ' +
                        'the operation could not complete due to server communication errors.');

                $dialog.find('.modal-message').txt(EALang['service_communication_error']);
                $dialog.find('.modal-message').addClass('alert-danger').removeClass('hidden');
            };

            _saveUnavailable(unavailable, successCallback, errorCallback);
        });

        /**
         * Event: Manage Unavailable Dialog Cancel Button "Click"
         *
         * Closes the dialog without saveing any changes to the database.
         */
        $('#manage-unavailable #cancel-unavailable').click(function() {
            $('#manage-unavailable').modal('hide');
        });

        /**
         * Event: Enable - Disable Synchronization Button "Click"
         *
         * When the user clicks on the "Enable Sync" button, a popup should appear
         * that is going to follow the web server authorization flow of OAuth.
         */
        $('#enable-sync').click(function() {
            if ($('#enable-sync').hasClass('enabled') === false) {
                // Enable synchronization for selected provider.
                var authUrl = GlobalVariables.baseUrl + '/index.php/google/oauth/'
                        + $('#select-filter-item').val();

                var redirectUrl = GlobalVariables.baseUrl + '/index.php/google/oauth_callback';

                var windowHandle = window.open(authUrl, 'Authorize Easy!Appointments',
                        'width=800, height=600');

                var authInterval = window.setInterval(function() {
                    // When the browser redirects to the google user consent page the "window.document" variable
                    // becomes "undefined" and when it comes back to the redirect URL it changes back. So check
                    // whether the variable is undefined to avoid javascript errors.
                    if (windowHandle.document !== undefined) {
                        if (windowHandle.document.URL.indexOf(redirectUrl) !== -1) {
                            // The user has granted access to his data.
                            windowHandle.close();
                            window.clearInterval(authInterval);
                            $('#enable-sync').addClass('btn-danger enabled');
                            $('#enable-sync span:eq(1)').text(EALang['disable_sync']);
                            $('#google-sync').prop('disabled', false);
                            $('#select-filter-item option:selected').attr('google-sync', 'true');

                            // Display the calendar selection dialog. First we will get a list of the available
                            // user's calendars and then we will display a selection modal so the user can select
                            // the sync calendar.
                            var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_get_google_calendars';
                            var postData = {
                                csrfToken: GlobalVariables.csrfToken,
                                provider_id: $('#select-filter-item').val()
                            };

                            $.post(postUrl, postData, function(response) {
                                if (!GeneralFunctions.handleAjaxExceptions(response)) {
                                    return;
                                }

                                $('#google-calendar').empty();
                                $.each(response, function() {
                                    var option = '<option value="' + this.id  + '">' + this.summary + '</option>';
                                    $('#google-calendar').append(option);
                                });

                                $('#select-google-calendar').modal('show');
                            }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
                        }
                    }
                }, 100);

            } else {
                // Disable synchronization for selected provider.
                // Update page elements and make an AJAX call to remove the google sync setting of the
                // selected provider.
                $.each(GlobalVariables.availableProviders, function(index, provider) {
                    if (provider['id'] == $('#select-filter-item').val()) {
                        provider['settings']['google_sync'] = '0';
                        provider['settings']['google_token'] = null;

                        _disableProviderSync(provider['id']);

                        $('#enable-sync').removeClass('btn-danger enabled');
                        $('#enable-sync span:eq(1)').text(EALang['enable_sync']);
                        $('#google-sync').prop('disabled', true);
                        $('#select-filter-item option:selected').attr('google-sync', 'false');

                        return false;
                    }
                });
            }
        });

        /**
         * Event: Insert Appointment Button "Click"
         *
         * When the user presses this button, the manage appointment dialog opens and lets the user to
         * create a new appointment.
         */
        $('#insert-appointment').click(function() {
            _resetAppointmentDialog();
            var $dialog = $('#manage-appointment');

            // Set the selected filter item and find the next appointment time as the default modal values.
            if ($('#select-filter-item option:selected').attr('type') == 'provider') {
                var $providerOption = $dialog.find('#select-provider option[value="'
                        + $('#select-filter-item').val() + '"]');
                if ($providerOption.length === 0) { // Change the services until you find the correct.
                    $.each($dialog.find('#select-service option'), function() {
                        $(this).prop('selected', true).parent().change();
                        if ($providerOption.length > 0)
                            return false;
                    });
                }
                $providerOption.prop('selected', true);
            } else {
                $dialog.find('#select-service option[value="'
                        + $('#select-filter-item').val() + '"]').prop('selected', true);
            }

            var serviceDuration = 0;
            $.each(GlobalVariables.availableServices, function(index, service) {
                if (service['id'] == $dialog.find('#select-service').val()) {
                    serviceDuration = service['duration'];
                    return false; // exit loop
                }
            });

            var start = new Date();
            var currentMin = parseInt(start.toString('mm'));

            if (currentMin > 0 && currentMin < 15) {
                start.set({ 'minute': 15 });
            } else if (currentMin > 15 && currentMin < 30) {
                start.set({ 'minute': 30 });
            } else if (currentMin > 30 && currentMin < 45) {
                start.set({ 'minute': 45 });
            } else {
                start.addHours(1).set({ 'minute': 0 });
            }

            $dialog.find('#start-datetime').val(GeneralFunctions.formatDate(start, GlobalVariables.dateFormat, true));
            $dialog.find('#end-datetime').val(GeneralFunctions.formatDate(start.addMinutes(serviceDuration),
                    GlobalVariables.dateFormat, true));

            // Display modal form.
            $dialog.find('.modal-header h3').text(EALang['new_appointment_title']);
            $dialog.modal('show');
        });

        /**
         * Event : Insert Unavailable Time Period Button "Click"
         *
         * When the user clicks this button a popup dialog appears and the use can set a time period where
         * he cannot accept any appointments.
         */
        $('#insert-unavailable').click(function() {
            _resetUnavailableDialog();
            var $dialog = $('#manage-unavailable');

            // Set the default datetime values.
            var start = new Date();
            var currentMin = parseInt(start.toString('mm'));

            if (currentMin > 0 && currentMin < 15) {
                start.set({ 'minute': 15 });
            } else if (currentMin > 15 && currentMin < 30) {
                start.set({ 'minute': 30 });
            } else if (currentMin > 30 && currentMin < 45) {
                start.set({ 'minute': 45 });
            } else {
                start.addHours(1).set({ 'minute': 0 });
            }

            $dialog.find('#unavailable-start').val(GeneralFunctions.formatDate(start, GlobalVariables.dateFormat, true));
            $dialog.find('#unavailable-end').val(GeneralFunctions.formatDate(start.addHours(1), GlobalVariables.dateFormat, true));
            $dialog.find('.modal-header h3').text(EALang['new_unavailable_title']);
            $dialog.modal('show');
        });

        /**
         * Event: Pick Existing Customer Button "Click"
         */
        $('#select-customer').click(function() {
            var $list = $('#existing-customers-list');

            if (!$list.is(':visible')) {
                $(this).text(EALang['hide']);
                $list.empty();
                $list.slideDown('slow');
                $('#filter-existing-customers').fadeIn('slow');
                $('#filter-existing-customers').val('');
                $.each(GlobalVariables.customers, function(index, c) {
                    $list.append('<div data-id="' + c.id + '">'
                            + c.first_name + ' ' + c.last_name + '</div>');
                });
            } else {
                $list.slideUp('slow');
                $('#filter-existing-customers').fadeOut('slow');
                $(this).text(EALang['select']);
            }
        });

        /**
         * Event: Select Existing Customer From List "Click"
         */
        $calendarPage.on('click', '#existing-customers-list div', function() {
            var id = $(this).attr('data-id');

            $.each(GlobalVariables.customers, function(index, c) {
                if (c.id == id) {
                    $('#customer-id').val(c.id);
                    $('#first-name').val(c.first_name);
                    $('#last-name').val(c.last_name);
                    $('#email').val(c.email);
                    $('#phone-number').val(c.phone_number);
                    $('#address').val(c.address);
                    $('#city').val(c.city);
                    $('#zip-code').val(c.zip_code);
                    $('#customer-notes').val(c.notes);
                    return false;
                }
            });

            $('#select-customer').trigger('click'); // hide list
        });

        /**
         * Event: Filter Existing Customers "Change"
         */
        $('#filter-existing-customers').keyup(function() {
            var key = $(this).val().toLowerCase();
            var $list = $('#existing-customers-list');
            var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_customers';
            var postData = {
                csrfToken: GlobalVariables.csrfToken,
                key: key
            };

            // Try to get the updated customer list.
            $.ajax({
                type: 'POST',
                url: postUrl,
                data: postData,
                dataType: 'json',
                timeout: 1000,
                global: false,
                success: function(response) {
                    $list.empty();
                    $.each(response, function(index, c) {
                        $list.append('<div data-id="' + c.id + '">'
                                + c.first_name + ' ' + c.last_name + '</div>');

                        // Verify if this customer is on the old customer list.
                        var result = $.grep(GlobalVariables.customers,
                                function(e){ return e.id == c.id; });

                        // Add it to the customer list.
                        if(result.length == 0){
                            GlobalVariables.customers.push(c);
                        }
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // If there is any error on the request, search by the local client database.
                    $list.empty();
                    $.each(GlobalVariables.customers, function(index, c) {
                        if (c.first_name.toLowerCase().indexOf(key) != -1
                                || c.last_name.toLowerCase().indexOf(key) != -1
                                || c.email.toLowerCase().indexOf(key) != -1
                                || c.phone_number.toLowerCase().indexOf(key) != -1
                                || c.address.toLowerCase().indexOf(key) != -1
                                || c.city.toLowerCase().indexOf(key) != -1
                                || c.zip_code.toLowerCase().indexOf(key) != -1
                                || c.notes.toLowerCase().indexOf(key) != -1) {
                            $list.append('<div data-id="' + c.id + '">'
                                    + c.first_name + ' ' + c.last_name + '</div>');
                        }
                    });
                }
            });
        });

        /**
         * Event: Selected Service "Change"
         *
         * When the user clicks on a service, its available providers should become visible. Also we need to
         * update the start and end time of the appointment.
         */
        $('#select-service').change(function() {
            var sid = $('#select-service').val();
            $('#select-provider').empty();

            // Automatically update the service duration.
            $.each(GlobalVariables.availableServices, function(indexService, service) {
                if (service.id == sid) {
                    var start = $('#start-datetime').datepicker('getDate');
                    $('#end-datetime').datepicker('setDate', new Date(start.getTime() + service.duration * 60000));
                    return false; // break loop
                }
            });

            // Update the providers select box.
            $.each(GlobalVariables.availableProviders, function(indexProvider, provider) {
                $.each(provider.services, function(indexService, serviceId) {
                    // If the current provider is able to provide the selected service, add him to the listbox.
                    if (serviceId == sid) {
                        var optionHtml = '<option value="' + provider['id'] + '">'
                                + provider['first_name']  + ' ' + provider['last_name']
                                + '</option>';
                        $('#select-provider').append(optionHtml);
                    }
                });
            });
        });

        /**
         * Event: Enter New Customer Button "Click"
         */
        $('#new-customer').click(function() {
            $('#manage-appointment').find('#customer-id, #first-name, #last-name, #email, '
                    + '#phone-number, #address, #city, #zip-code, #customer-notes').val('');
        });

        /**
         * Event: Select Google Calendar "Click"
         */
        $('#select-calendar').click(function() {
            var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_select_google_calendar';
            var postData = {
                csrfToken: GlobalVariables.csrfToken,
                provider_id: $('#select-filter-item').val(),
                calendar_id: $('#google-calendar').val()
            };
            $.post(postUrl, postData, function(response) {
                if (!GeneralFunctions.handleAjaxExceptions(response)) {
                    return;
                }
                Backend.displayNotification(EALang['google_calendar_selected']);
                $('#select-google-calendar').modal('hide');
            }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
        });

        /**
         * Event: Close Google Calendar "Click"
         */
        $('#close-calendar').click(function() {
            $('#select-google-calendar').modal('hide');
        });
    }

    /**
     * Save Appointment
     *
     * This method stores the changes of an already registered appointment into the database, via an ajax call.
     *
     * @param {Object} appointment Contain the new appointment data. The ID of the appointment MUST be
     * already included. The rest values must follow the database structure.
     * @param {Object} customer Optional, contains the customer data.
     * @param {Function} successCallback Optional, if defined, this function is going to be executed on post success.
     * @param {Function} errorCallback Optional, if defined, this function is going to be executed on post failure.
     */
    function _saveAppointment(appointment, customer, successCallback, errorCallback) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_appointment';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            appointment_data: JSON.stringify(appointment)
        };

        if (customer !== undefined) {
            postData['customer_data'] = JSON.stringify(customer);
        }

        $.ajax({
            url: postUrl,
            type: 'POST',
            data: postData,
            dataType: 'json'
        })
            .done(function(response) {
                if (successCallback !== undefined) {
                    successCallback(response);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                if (errorCallback !== undefined) {
                    errorCallback();
                }
            });
    }

    /**
     * Save unavailable period to database.
     *
     * @param {Object} unavailable Containts the unavailable period data.
     * @param {Function} successCallback The ajax success callback function.
     * @param {Function} errorCallback The ajax failure callback function.
     */
    function _saveUnavailable(unavailable, successCallback, errorCallback) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_unavailable';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            unavailable: JSON.stringify(unavailable)
        };

        $.ajax({
            type: 'POST',
            url: postUrl,
            data: postData,
            success: successCallback,
            error: errorCallback
        });
    }    

    /**
     * Disable Provider Sync
     *
     * This method disables the google synchronization for a specific provider.
     *
     * @param {Number} providerId The selected provider record ID.
     */
    function _disableProviderSync(providerId) {
        // Make an ajax call to the server in order to disable the setting
        // from the database.
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_disable_provider_sync';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            provider_id: providerId
        };

        $.post(postUrl, postData, function(response) {
            if (response.exceptions) {
                response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE, GeneralFunctions.EXCEPTIONS_MESSAGE);
                $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                return;
            }
        }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
    }

    /**
     * Reset Apppointment Dialog
     *
     * This method resets the manage appointment dialog modal to its initial state. After that you can make
     * any modification might be necessary in order to bring the dialog to the desired state.
     */
    function _resetAppointmentDialog() {
        var $dialog = $('#manage-appointment');

        // Empty form fields.
        $dialog.find('input, textarea').val('');
        $dialog.find('.modal-message').fadeOut();

        // Prepare service and provider select boxes.
        $dialog.find('#select-service').val(
                $dialog.find('#select-service').eq(0).attr('value'));

        // Fill the providers listbox with providers that can serve the appointment's
        // service and then select the user's provider.
        $dialog.find('#select-provider').empty();
        $.each(GlobalVariables.availableProviders, function(index, provider) {
            var canProvideService = false;

            $.each(provider['services'], function(index, serviceId) {
                if (serviceId == $dialog.find('#select-service').val()) {
                    canProvideService = true;
                    return false;
                }
            });

            if (canProvideService) { // Add the provider to the listbox.
                var option = new Option(provider['first_name']
                       + ' ' + provider['last_name'], provider['id']);
                $dialog.find('#select-provider').append(option);
            }
        });

        // Close existing customers-filter frame.
        $('#existing-customers-list').slideUp('slow');
        $('#filter-existing-customers').fadeOut('slow');
        $('#select-customer').text(EALang['select']);

        // Setup start and datetimepickers.
        // Get the selected service duration. It will be needed in order to calculate the appointment end datetime.
        var serviceDuration = 0;
        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service['id'] == $dialog.find('#select-service').val()) {
                serviceDuration = service['duration'];
                return false;
            }
        });

        var startDatetime = new Date().addMinutes(GlobalVariables.bookAdvanceTimeout);
        var endDatetime  = new Date().addMinutes(GlobalVariables.bookAdvanceTimeout).addMinutes(serviceDuration);
        var dateFormat;

        switch(GlobalVariables.dateFormat) {
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
                throw new Error('Invalid GlobalVariables.dateFormat value.');
        }

        $dialog.find('#start-datetime').datetimepicker({
            dateFormat: dateFormat,

            // Translation
            dayNames: [EALang['sunday'], EALang['monday'], EALang['tuesday'], EALang['wednesday'],
                    EALang['thursday'], EALang['friday'], EALang['saturday']],
            dayNamesShort: [EALang['sunday'].substr(0,3), EALang['monday'].substr(0,3),
                    EALang['tuesday'].substr(0,3), EALang['wednesday'].substr(0,3),
                    EALang['thursday'].substr(0,3), EALang['friday'].substr(0,3),
                    EALang['saturday'].substr(0,3)],
            dayNamesMin: [EALang['sunday'].substr(0,2), EALang['monday'].substr(0,2),
                    EALang['tuesday'].substr(0,2), EALang['wednesday'].substr(0,2),
                    EALang['thursday'].substr(0,2), EALang['friday'].substr(0,2),
                    EALang['saturday'].substr(0,2)],
            monthNames: [EALang['january'], EALang['february'], EALang['march'], EALang['april'],
                    EALang['may'], EALang['june'], EALang['july'], EALang['august'], EALang['september'],
                    EALang['october'], EALang['november'], EALang['december']],
            prevText: EALang['previous'],
            nextText: EALang['next'],
            currentText: EALang['now'],
            closeText: EALang['close'],
            timeOnlyTitle: EALang['select_time'],
            timeText: EALang['time'],
            hourText: EALang['hour'],
            minuteText: EALang['minutes'],
            firstDay: 1
        });
        $dialog.find('#start-datetime').datepicker('setDate', startDatetime);

        $dialog.find('#end-datetime').datetimepicker({
            dateFormat: dateFormat,

            // Translation
            dayNames: [EALang['sunday'], EALang['monday'], EALang['tuesday'], EALang['wednesday'],
                    EALang['thursday'], EALang['friday'], EALang['saturday']],
            dayNamesShort: [EALang['sunday'].substr(0,3), EALang['monday'].substr(0,3),
                    EALang['tuesday'].substr(0,3), EALang['wednesday'].substr(0,3),
                    EALang['thursday'].substr(0,3), EALang['friday'].substr(0,3),
                    EALang['saturday'].substr(0,3)],
            dayNamesMin: [EALang['sunday'].substr(0,2), EALang['monday'].substr(0,2),
                    EALang['tuesday'].substr(0,2), EALang['wednesday'].substr(0,2),
                    EALang['thursday'].substr(0,2), EALang['friday'].substr(0,2),
                    EALang['saturday'].substr(0,2)],
            monthNames: [EALang['january'], EALang['february'], EALang['march'], EALang['april'],
                    EALang['may'], EALang['june'], EALang['july'], EALang['august'], EALang['september'],
                    EALang['october'], EALang['november'], EALang['december']],
            prevText: EALang['previous'],
            nextText: EALang['next'],
            currentText: EALang['now'],
            closeText: EALang['close'],
            timeOnlyTitle: EALang['select_time'],
            timeText: EALang['time'],
            hourText: EALang['hour'],
            minuteText: EALang['minutes'],
            firstDay: 1
        });
        $dialog.find('#end-datetime').datepicker('setDate', endDatetime);
    }

    /**
     * Validate the manage appointment dialog data. Validation checks need to
     * run every time the data are going to be saved.
     *
     * @returns {Boolean} Returns the validation result.
     */
    function _validateAppointmentForm() {
        var $dialog = $('#manage-appointment');

        // Reset previous validation css formating.
        $dialog.find('.form-group').removeClass('has-error');
        $dialog.find('.modal-message').addClass('hidden');

        try {
            // Check required fields.
            var missingRequiredField = false;

            $dialog.find('.required').each(function() {
                if ($(this).val() == '' || $(this).val() == null) {
                    $(this).parents('.form-group').addClass('has-error');
                    missingRequiredField = true;
                }
            });

            if (missingRequiredField) {
                throw EALang['fields_are_required'];
            }

            // Check email address.
            if (!GeneralFunctions.validateEmail($dialog.find('#email').val())) {
                $dialog.find('#email').parents('.form-group').eq(1).addClass('has-error');
                throw EALang['invalid_email'];
            }

            // Check appointment start and end time.
            var start = $('#start-datetime').datepicker('getDate');
            var end = $('#end-datetime').datepicker('getDate');
            if (start > end) {
                $dialog.find('#start-datetime').parents('.form-group').addClass('has-error');
                $dialog.find('#end-datetime').parents('.form-group').addClass('has-error');
                throw EALang['start_date_before_end_error'];
            }

            return true;
        } catch(exc) {
            $dialog.find('.modal-message').addClass('alert-danger').text(exc).removeClass('hidden');
            return false;
        }
    }

    /**
     * Reset unavailable dialog form.
     *
     * Reset the "#manage-unavailable" dialog. Use this method to bring the dialog to the initial state
     * before it becomes visible to the user.
     */
    function _resetUnavailableDialog() {
        var $dialog = $('#manage-unavailable');

        $dialog.find('#unavailable-id').val('');

        // Set default time values
        var start = GeneralFunctions.formatDate(new Date(), GlobalVariables.dateFormat, true);
        var end = GeneralFunctions.formatDate(new Date().addHours(1), GlobalVariables.dateFormat, true);
        var dateFormat;

        switch(GlobalVariables.dateFormat) {
            case 'DMY':
                dateFormat = 'dd/mm/yy';
                break;
            case 'MDY':
                dateFormat = 'mm/dd/yy';
                break;
            case 'YMD':
                dateFormat = 'yy/mm/dd';
                break;
        }


        $dialog.find('#unavailable-start').datetimepicker({
            dateFormat: dateFormat,

            // Translation
            dayNames: [EALang['sunday'], EALang['monday'], EALang['tuesday'], EALang['wednesday'],
                    EALang['thursday'], EALang['friday'], EALang['saturday']],
            dayNamesShort: [EALang['sunday'].substr(0,3), EALang['monday'].substr(0,3),
                    EALang['tuesday'].substr(0,3), EALang['wednesday'].substr(0,3),
                    EALang['thursday'].substr(0,3), EALang['friday'].substr(0,3),
                    EALang['saturday'].substr(0,3)],
            dayNamesMin: [EALang['sunday'].substr(0,2), EALang['monday'].substr(0,2),
                    EALang['tuesday'].substr(0,2), EALang['wednesday'].substr(0,2),
                    EALang['thursday'].substr(0,2), EALang['friday'].substr(0,2),
                    EALang['saturday'].substr(0,2)],
            monthNames: [EALang['january'], EALang['february'], EALang['march'], EALang['april'],
                    EALang['may'], EALang['june'], EALang['july'], EALang['august'], EALang['september'],
                    EALang['october'], EALang['november'], EALang['december']],
            prevText: EALang['previous'],
            nextText: EALang['next'],
            currentText: EALang['now'],
            closeText: EALang['close'],
            timeOnlyTitle: EALang['select_time'],
            timeText: EALang['time'],
            hourText: EALang['hour'],
            minuteText: EALang['minutes'],
            firstDay: 1
        });
        $dialog.find('#unavailable-start').val(start);

        $dialog.find('#unavailable-end').datetimepicker({
            dateFormat: dateFormat,

            // Translation
            dayNames: [EALang['sunday'], EALang['monday'], EALang['tuesday'], EALang['wednesday'],
                    EALang['thursday'], EALang['friday'], EALang['saturday']],
            dayNamesShort: [EALang['sunday'].substr(0,3), EALang['monday'].substr(0,3),
                    EALang['tuesday'].substr(0,3), EALang['wednesday'].substr(0,3),
                    EALang['thursday'].substr(0,3), EALang['friday'].substr(0,3),
                    EALang['saturday'].substr(0,3)],
            dayNamesMin: [EALang['sunday'].substr(0,2), EALang['monday'].substr(0,2),
                    EALang['tuesday'].substr(0,2), EALang['wednesday'].substr(0,2),
                    EALang['thursday'].substr(0,2), EALang['friday'].substr(0,2),
                    EALang['saturday'].substr(0,2)],
            monthNames: [EALang['january'], EALang['february'], EALang['march'], EALang['april'],
                    EALang['may'], EALang['june'], EALang['july'], EALang['august'], EALang['september'],
                    EALang['october'], EALang['november'], EALang['december']],
            prevText: EALang['previous'],
            nextText: EALang['next'],
            currentText: EALang['now'],
            closeText: EALang['close'],
            timeOnlyTitle: EALang['select_time'],
            timeText: EALang['time'],
            hourText: EALang['hour'],
            minuteText: EALang['minutes'],
            firstDay: 1
        });
        $dialog.find('#unavailable-end').val(end);

        // Clear the unavailable notes field.
        $dialog.find('#unavailable-notes').val('');
    }

    /**
     * Initialize Module
     *
     * This function makes the necessary initialization for the default backend calendar page. If this module
     * is used in another page then this function might not be needed.
     *
     * @param {String} view Optional (default), the calendar view to be loaded.
     */
    exports.initialize = function(view) { 
        // Load and initialize the calendar view. 
        if (view === 'table') {
            BackendCalendarTableView.initialize(); 
        } else {
            BackendCalendarDefaultView.initialize();
        }

        _bindEventHandlers(); // General event handling.
    };

})(window.BackendCalendar);
