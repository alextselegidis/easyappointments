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
 * This namespace contains functions that are used by the backend calendar page.
 *
 * @namespace BackendCalendar
 */
window.BackendCalendar = window.BackendCalendar || {};

(function(exports) {

    'use strict';

    // Constants
    var FILTER_TYPE_PROVIDER =  'provider',
        FILTER_TYPE_SERVICE = 'service';

    // Variables
    var lastFocusedEventData; // Contains event data for later use.

    /**
     * Bind Event Handlers
     *
     * This method binds the default event handlers for the backend calendar page. If you do not need the
     * default handlers then initialize the page by setting the "defaultEventHandlers" argument to "false".
     */
    function _bindEventHandlers() {
        /**
         * Event: Calendar Filter Item "Change"
         *
         * Load the appointments that correspond to the select filter item and display them on the calendar.
         */
        $('#select-filter-item').change(function() {
            _refreshCalendarAppointments(
                    $('#calendar'),
                    $('#select-filter-item').val(),
                    $('#select-filter-item option:selected').attr('type'),
                    $('#calendar').fullCalendar('getView').visStart,
                    $('#calendar').fullCalendar('getView').visEnd);

            // If current value is service, then the sync buttons must be disabled.
            if ($('#select-filter-item option:selected').attr('type') === FILTER_TYPE_SERVICE) {
                $('#google-sync, #enable-sync, #insert-appointment, #insert-unavailable').prop('disabled', true);
            } else {
                $('#google-sync, #enable-sync, #insert-appointment, #insert-unavailable').prop('disabled', false);

                // If the user has already the sync enabled then apply the proper style changes.
                if ($('#select-filter-item option:selected').attr('google-sync') === 'true') {
                    $('#enable-sync').addClass('btn-danger enabled');
                    $('#enable-sync span:eq(1)').text(EALang['disable_sync']);
                    $('#google-sync').prop('disabled', false);
                } else {
                    $('#enable-sync').removeClass('btn-danger enabled');
                    $('#enable-sync span:eq(1)').text(EALang['enable_sync']);
                    $('#google-sync').prop('disabled', true);
                }
            }
        });

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
        $(document).on('click', '.close-popover', function() {
            $(this).parents().eq(2).remove();
        });

        /**
         * Event: Popover Edit Button "Click"
         *
         * Enables the edit dialog of the selected calendar event.
         */
        $(document).on('click', '.edit-popover', function() {
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
        $(document).on('click', '.delete-popover', function() {
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
                var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_unavailable',
                    postData = {
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
                    .datepicker('getDate').toString('yyyy-MM-dd HH:mm:ss'),
                endDatetime = $dialog.find('#end-datetime')
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
            var $dialog = $('#manage-unavailable'),
                start = $dialog.find('#unavailable-start').datetimepicker('getDate'),
                end = $dialog.find('#unavailable-end').datetimepicker('getDate');

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
                            var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_get_google_calendars',
                                postData = {
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

            var start = new Date(),
                currentMin = parseInt(start.toString('mm'));

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
            var start = new Date(),
                currentMin = parseInt(start.toString('mm'));

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
        $(document).on('click', '#existing-customers-list div', function() {
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
            var key = $(this).val().toLowerCase(),
                $list = $('#existing-customers-list'),
                postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_customers',
                postData = {
                    'csrfToken': GlobalVariables.csrfToken,
                    'key': key
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
            var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_select_google_calendar',
                postData = {
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
     * Get Calendar Component Height
     *
     * This method calculates the proper calendar height, in order to be displayed correctly, even when the
     * browser window is resizing.
     *
     * @return {Number} Returns the calendar element height in pixels.
     */
    function _getCalendarHeight() {
        var result = window.innerHeight - $('#footer').outerHeight() - $('#header').outerHeight()
                - $('#calendar-toolbar').outerHeight() - 60; // 60 for fine tuning
        return (result > 500) ? result : 500; // Minimum height is 500px
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
    function _refreshCalendarAppointments($calendar, recordId, filterType, startDate, endDate) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_get_calendar_appointments',
            postData = {
                csrfToken: GlobalVariables.csrfToken,
                record_id: recordId,
                start_date: startDate.toString('yyyy-MM-dd'),
                end_date: endDate.toString('yyyy-MM-dd'),
                filter_type: filterType
            };

        $.post(postUrl, postData, function(response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            // Add appointments to calendar.
            var calendarEvents = [],
                $calendar = $('#calendar');

            $.each(response.appointments, function(index, appointment) {
                var event = {
                    id: appointment['id'],
                    title: appointment['service']['name'] + ' - '
                            + appointment['customer']['first_name'] + ' '
                            + appointment['customer']['last_name'],
                    start: appointment['start_datetime'],
                    end: appointment['end_datetime'],
                    allDay: false,
                    data: appointment // Store appointment data for later use.
                };

                calendarEvents.push(event);
            });

            $calendar.fullCalendar('removeEvents');
            $calendar.fullCalendar('addEventSource', calendarEvents);

            // :: ADD PROVIDER'S UNAVAILABLE TIME PERIODS
            var calendarView = $calendar.fullCalendar('getView').name;

            if (filterType === FILTER_TYPE_PROVIDER && calendarView !== 'month') {
                $.each(GlobalVariables.availableProviders, function(index, provider) {
                    if (provider['id'] == recordId) {
                        var workingPlan = jQuery.parseJSON(provider.settings.working_plan);
                        var unavailablePeriod;

                        switch(calendarView) {
                            case 'agendaDay':
                                var selDayName = $calendar.fullCalendar('getView')
                                        .start.toString('dddd').toLowerCase();

                                // Add custom unavailable periods.
                                $.each(response.unavailables, function(index, unavailable) {
                                    var unavailablePeriod = {
                                        title: EALang['unavailable'] + ' <br><small>' + ((unavailable.notes.length > 30)
                                                        ? unavailable.notes.substring(0, 30) + '...'
                                                        : unavailable.notes) + '</small>',
                                        start: Date.parse(unavailable.start_datetime),
                                        end: Date.parse(unavailable.end_datetime),
                                        allDay: false,
                                        color: '#879DB4',
                                        editable: true,
                                        className: 'fc-unavailable fc-custom',
                                        data: unavailable
                                    };
                                    $calendar.fullCalendar('renderEvent', unavailablePeriod, false);
                                });

                                // Non-working day
                                if (workingPlan[selDayName] == null) {
                                    unavailablePeriod = {
                                            title: EALang['not_working'],
                                            start: GeneralFunctions.clone($calendar.fullCalendar('getView').start),
                                            end: GeneralFunctions.clone($calendar.fullCalendar('getView').end),
                                            allDay: false,
                                            color: '#BEBEBE',
                                            editable: false,
                                            className: 'fc-unavailable'
                                        };
                                        $calendar.fullCalendar('renderEvent', unavailablePeriod, true);
                                    return; // Go to next loop
                                }

                                // Add unavailable period before work starts.
                                var calendarDateStart = $calendar.fullCalendar('getView').start,
                                    workDateStart = Date.parseExact(
                                        calendarDateStart.toString('dd/MM/yyyy') + ' '
                                        + workingPlan[selDayName].start,
                                        'dd/MM/yyyy HH:mm');

                                if (calendarDateStart < workDateStart) {
                                    unavailablePeriod = {
                                        title: EALang['not_working'],
                                        start: calendarDateStart,
                                        end: workDateStart,
                                        allDay: false,
                                        color: '#BEBEBE',
                                        editable: false,
                                        className: 'fc-unavailable'
                                    };
                                    $calendar.fullCalendar('renderEvent', unavailablePeriod, false);
                                }

                                // Add unavailable period after work ends.
                                var calendarDateEnd = $calendar.fullCalendar('getView').end,
                                    workDateEnd = Date.parseExact(
                                        calendarDateStart.toString('dd/MM/yyyy') + ' '
                                        + workingPlan[selDayName].end,
                                        'dd/MM/yyyy HH:mm'); // Use calendarDateStart ***
                                if (calendarDateEnd > workDateEnd) {
                                    var unavailablePeriod = {
                                        title: EALang['not_working'],
                                        start: workDateEnd,
                                        end: calendarDateEnd,
                                        allDay: false,
                                        color: '#BEBEBE',
                                        editable: false,
                                        className: 'fc-unavailable'
                                    };
                                    $calendar.fullCalendar('renderEvent', unavailablePeriod, false);
                                }

                                // Add unavailable periods for breaks.
                                var breakStart, breakEnd;
                                $.each(workingPlan[selDayName].breaks, function(index, currBreak) {
                                    breakStart = Date.parseExact(calendarDateStart.toString('dd/MM/yyyy')
                                            + ' ' + currBreak.start, 'dd/MM/yyyy HH:mm');
                                    breakEnd = Date.parseExact(calendarDateStart.toString('dd/MM/yyyy')
                                            + ' ' + currBreak.end, 'dd/MM/yyyy HH:mm');
                                    var unavailablePeriod = {
                                        title: EALang['break'],
                                        start: breakStart,
                                        end: breakEnd,
                                        allDay: false,
                                        color: '#BEBEBE',
                                        editable: false,
                                        className: 'fc-unavailable fc-break'
                                    };
                                    $calendar.fullCalendar('renderEvent', unavailablePeriod, false);
                                });

                                break;

                            case 'agendaWeek':
                                var currDateStart = GeneralFunctions.clone($calendar.fullCalendar('getView').start),
                                    currDateEnd = GeneralFunctions.clone(currDateStart).addDays(1);

                                // Add custom unavailable periods (they are always displayed on the calendar, even if
                                // the provider won't work on that day).
                                $.each(response.unavailables, function(index, unavailable) {
                                    unavailablePeriod = {
                                        title: EALang['unavailable'] + ' <br><small>' + ((unavailable.notes.length > 30)
                                                ? unavailable.notes.substring(0, 30) + '...'
                                                : unavailable.notes) + '</small>',
                                        start: Date.parse(unavailable.start_datetime),
                                        end: Date.parse(unavailable.end_datetime),
                                        allDay: false,
                                        color: '#879DB4',
                                        editable: true,
                                        className: 'fc-unavailable fc-custom',
                                        data: unavailable
                                    };
                                    $calendar.fullCalendar('renderEvent', unavailablePeriod, false);
                                });

                                $.each(workingPlan, function(index, workingDay) {

                                    if (workingDay == null) {
                                        // Add a full day unavailable event.
                                        unavailablePeriod = {
                                            title: EALang['not_working'],
                                            start: GeneralFunctions.clone(currDateStart),
                                            end: GeneralFunctions.clone(currDateEnd),
                                            allDay: false,
                                            color: '#BEBEBE',
                                            editable: false,
                                            className: 'fc-unavailable'
                                        };
                                        $calendar.fullCalendar('renderEvent', unavailablePeriod, true);
                                        currDateStart.addDays(1);
                                        currDateEnd.addDays(1);
                                        return; // Go to the next loop.
                                    }

                                    var start,
                                        end;

                                    // Add unavailable period before work starts.
                                    start = Date.parseExact(currDateStart.toString('dd/MM/yyyy')
                                            + ' ' + workingDay.start, 'dd/MM/yyyy HH:mm');
                                    if (currDateStart < start) {
                                        unavailablePeriod = {
                                            title: EALang['not_working'],
                                            start: GeneralFunctions.clone(currDateStart),
                                            end: GeneralFunctions.clone(start),
                                            allDay: false,
                                            color: '#BEBEBE',
                                            editable: false,
                                            className: 'fc-unavailable'
                                        };
                                        $calendar.fullCalendar('renderEvent', unavailablePeriod, true);
                                    }

                                    // Add unavailable period after work ends.
                                    end = Date.parseExact(currDateStart.toString('dd/MM/yyyy')
                                            + ' ' + workingDay.end, 'dd/MM/yyyy HH:mm');
                                    if (currDateEnd > end) {
                                        unavailablePeriod = {
                                            title: EALang['not_working'],
                                            start: GeneralFunctions.clone(end),
                                            end: GeneralFunctions.clone(currDateEnd),
                                            allDay: false,
                                            color: '#BEBEBE',
                                            editable: false,
                                            className: 'fc-unavailable fc-brake'
                                        };
                                        $calendar.fullCalendar('renderEvent', unavailablePeriod, false);
                                    }

                                    // Add unavailable periods during day breaks.
                                    var breakStart, breakEnd;
                                    $.each(workingDay.breaks, function(index, currBreak) {
                                        breakStart = Date.parseExact(currDateStart.toString('dd/MM/yyyy')
                                                + ' ' + currBreak.start, 'dd/MM/yyyy HH:mm');
                                        breakEnd = Date.parseExact(currDateStart.toString('dd/MM/yyyy')
                                                + ' ' + currBreak.end, 'dd/MM/yyyy HH:mm');
                                        var unavailablePeriod = {
                                            title: EALang['break'],
                                            start: breakStart,
                                            end: breakEnd,
                                            allDay: false,
                                            color: '#BEBEBE',
                                            editable: false,
                                            className: 'fc-unavailable fc-break'
                                        };
                                        $calendar.fullCalendar('renderEvent', unavailablePeriod, false);
                                    });

                                    currDateStart.addDays(1);
                                    currDateEnd.addDays(1);
                                });
                                break;
                        }
                    }
                });
            }
        }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
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
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_appointment',
            postData = {
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
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_unavailable',
            postData = {
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
     * Calendar Event "Resize" Callback
     *
     * The user can change the duration of an event by resizing an appointment object on the calendar. This
     * change needs to be stored to the database too and this is done via an ajax call.
     *
     * @see updateAppointmentData()
     */
    function _calendarEventResize(event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view) {
        if (GlobalVariables.user.privileges.appointments.edit == false) {
            revertFunc();
            Backend.displayNotification(EALang['no_privileges_edit_appointments']);
            return;
        }

        if ($('#notification').is(':visible')) {
            $('#notification').hide('bind');
        }

        if (event.data.is_unavailable == false) {
            // Prepare appointment data.
            var appointment = GeneralFunctions.clone(event.data);

            // Must delete the following because only appointment data should be provided to the ajax call.
            delete appointment['customer'];
            delete appointment['provider'];
            delete appointment['service'];

            appointment['end_datetime'] = Date.parseExact(
                    appointment['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                    .add({ minutes: minuteDelta })
                    .toString('yyyy-MM-dd HH:mm:ss');

            // Success callback
            var successCallback = function(response) {
                if (response.exceptions) {
                    response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                    GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE, GeneralFunctions.EXCEPTIONS_MESSAGE);
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                    return;
                }

                if (response.warnings) {
                    // Display warning information to the user.
                    response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                    GeneralFunctions.displayMessageBox(GeneralFunctions.WARNINGS_TITLE, GeneralFunctions.WARNINGS_MESSAGE);
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
                }

                // Display success notification to user.
                var undoFunction = function() {
                    appointment['end_datetime'] = Date.parseExact(
                            appointment['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                            .add({ minutes: -minuteDelta })
                            .toString('yyyy-MM-dd HH:mm:ss');

                    var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_appointment';
                    var postData = {
                        'csrfToken': GlobalVariables.csrfToken,
                        'appointment_data': JSON.stringify(appointment)
                    };

                    $.post(postUrl, postData, function(response) {
                        $('#notification').hide('blind');
                        revertFunc();
                    }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
                };

                Backend.displayNotification(EALang['appointment_updated'], [
                    {
                        'label': 'Undo',
                        'function': undoFunction
                    }
                ]);
                $('#footer').css('position', 'static'); // Footer position fix.
            };

            // Update appointment data.
            _saveAppointment(appointment, undefined, successCallback, undefined);
        } else {
            // Update unvailable time period.
            var unavailable = {
                id: event.data.id,
                start_datetime: event.start.toString('yyyy-MM-dd HH:mm:ss'),
                end_datetime: event.end.toString('yyyy-MM-dd HH:mm:ss'),
                id_users_provider: event.data.id_users_provider
            };

            // Define success callback function.
            var successCallback = function(response) {
                if (response.exceptions) {
                    response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                    GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE, GeneralFunctions.EXCEPTIONS_MESSAGE);
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                    return;
                }

                if (response.warnings) {
                    // Display warning information to the user.
                    response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                    GeneralFunctions.displayMessageBox(GeneralFunctions.WARNINGS_TITLE, GeneralFunctions.WARNINGS_MESSAGE);
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
                }

                // Display success notification to user.
                var undoFunction = function() {
                    unavailable['end_datetime'] = Date.parseExact(
                            unavailable['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                            .add({ minutes: -minuteDelta })
                            .toString('yyyy-MM-dd HH:mm:ss');

                    var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_unavailable';
                    var postData = {
                        'csrfToken': GlobalVariables.csrfToken,
                        'unavailable': JSON.stringify(unavailable)
                    };

                    $.post(postUrl, postData, function(response) {
                        $('#notification').hide('blind');
                        revertFunc();
                    }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
                };

                Backend.displayNotification(EALang['unavailable_updated'], [
                    {
                        'label': 'Undo',
                        'function': undoFunction
                    }
                ]);
                $('#footer').css('position', 'static'); // Footer position fix.
            };

            _saveUnavailable(unavailable, successCallback, undefined);
        }
    }

    /**
     * Calendar Window "Resize" Callback
     *
     * The calendar element needs to be resized too in order to fit into the window. Nevertheless, if the window
     * becomes very small the the calendar won't shrink anymore.
     *
     * @see _getCalendarHeight()
     */
    function _calendarWindowResize(view) {
        $('#calendar').fullCalendar('option', 'height', _getCalendarHeight());
    }

    /**
     * Calendar Day "Click" Callback
     *
     * When the user clicks on a day square on the calendar, then he will automatically be transfered to that
     * day view calendar.
     */
    function _calendarDayClick(date, allDay, jsEvent, view) {
        if (allDay) {
            $('#calendar').fullCalendar('gotoDate', date);
            $('#calendar').fullCalendar('changeView', 'agendaDay');
        }
    }

    /**
     * Calendar Event "Click" Callback
     *
     * When the user clicks on an appointment object on the calendar, then a data preview popover is display
     * above the calendar item.
     */
    function _calendarEventClick(event, jsEvent, view) {
        $('.popover').remove(); // Close all open popovers.

        var html,
            displayEdit,
            displayDelete;

        // Depending where the user clicked the event (title or empty space) we
        // need to use different selectors to reach the parent element.
        var $parent = $(jsEvent.target.offsetParent);
        var $altParent = $(jsEvent.target).parents().eq(1);

        if ($parent.hasClass('fc-unavailable') || $altParent.hasClass('fc-unavailable')) {
            displayEdit = (($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom'))
                    && GlobalVariables.user.privileges.appointments.edit == true)
                    ? '' : 'hide';
            displayDelete = (($parent.hasClass('fc-custom') || $altParent.hasClass('fc-custom'))
                    && GlobalVariables.user.privileges.appointments.delete == true)
                    ? '' : 'hide'; // Same value at the time.

            var notes = '';
            if (event.data) { // Only custom unavailable periods have notes.
                notes = '<strong>Notes</strong> ' + event.data.notes;
            }

            html =
                    '<style type="text/css">'
                        + '.popover-content strong {min-width: 80px; display:inline-block;}'
                        + '.popover-content button {margin-right: 10px;}'
                        + '</style>' +
                    '<strong>' + EALang['start'] + '</strong> '
                        + GeneralFunctions.formatDate(event.start, GlobalVariables.dateFormat, true)
                        + '<br>' +
                    '<strong>' + EALang['end'] + '</strong> '
                        + GeneralFunctions.formatDate(event.end, GlobalVariables.dateFormat, true)
                        + '<br>'
                        + notes
                        + '<hr>' +
                    '<center>' +
                        '<button class="edit-popover btn btn-primary ' + displayEdit + '">' + EALang['edit'] + '</button>' +
                        '<button class="delete-popover btn btn-danger ' + displayDelete + '">' + EALang['delete'] + '</button>' +
                        '<button class="close-popover btn btn-default" data-po=' + jsEvent.target + '>' + EALang['close'] + '</button>' +
                    '</center>';
        } else {
            displayEdit = (GlobalVariables.user.privileges.appointments.edit == true)
                    ? '' : 'hide';
            displayDelete = (GlobalVariables.user.privileges.appointments.delete == true)
                    ? '' : 'hide';

            html =
                    '<style type="text/css">'
                        + '.popover-content strong {min-width: 80px; display:inline-block;}'
                        + '.popover-content button {margin-right: 10px;}'
                        + '</style>' +
                    '<strong>' + EALang['start'] + '</strong> '
                        + GeneralFunctions.formatDate(event.start, GlobalVariables.dateFormat, true)
                        + '<br>' +
                    '<strong>' + EALang['end'] + '</strong> '
                        + GeneralFunctions.formatDate(event.end, GlobalVariables.dateFormat, true)
                        + '<br>' +
                    '<strong>' + EALang['service'] + '</strong> '
                        + event.data['service']['name']
                        + '<br>' +
                    '<strong>' + EALang['provider'] + '</strong> '
                        + event.data['provider']['first_name'] + ' '
                        + event.data['provider']['last_name']
                        + '<br>' +
                    '<strong>' + EALang['customer'] + '</strong> '
                        + event.data['customer']['first_name'] + ' '
                        + event.data['customer']['last_name']
                        + '<hr>' +
                    '<center>' +
                        '<button class="edit-popover btn btn-primary ' + displayEdit + '">' + EALang['edit'] + '</button>' +
                        '<button class="delete-popover btn btn-danger ' + displayDelete + '">' + EALang['delete'] + '</button>' +
                        '<button class="close-popover btn btn-default" data-po=' + jsEvent.target + '>' + EALang['close'] + '</button>' +
                    '</center>';
        }

        $(jsEvent.target).popover({
            placement: 'top',
            title: event.title,
            content: html,
            html: true,
            container: 'body',
            trigger: 'manual'
        });

        lastFocusedEventData = event;
        $(jsEvent.target).popover('toggle');

        // Fix popover position
        if ($('.popover').length > 0) {
            if ($('.popover').position().top < 200) $('.popover').css('top', '200px');
        }
    }

    /**
     * Calendar Event "Drop" Callback
     *
     * This event handler is triggered whenever the user drags and drops an event into a different position
     * on the calendar. We need to update the database with this change. This is done via an ajax call.
     */
    function _calendarEventDrop(event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view) {
        if (GlobalVariables.user.privileges.appointments.edit == false) {
            revertFunc();
            Backend.displayNotification(EALang['no_privileges_edit_appointments']);
            return;
        }

        if ($('#notification').is(':visible')) {
            $('#notification').hide('bind');
        }

        if (event.data.is_unavailable == false) {
            // Prepare appointment data.
            var appointment = GeneralFunctions.clone(event.data);

            // Must delete the following because only appointment data should be provided to the ajax call.
            delete appointment['customer'];
            delete appointment['provider'];
            delete appointment['service'];

            appointment['start_datetime'] = Date.parseExact(
                    appointment['start_datetime'], 'yyyy-MM-dd HH:mm:ss')
                    .add({ days: dayDelta, minutes: minuteDelta })
                    .toString('yyyy-MM-dd HH:mm:ss');

            appointment['end_datetime'] = Date.parseExact(
                    appointment['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                    .add({ days: dayDelta, minutes: minuteDelta })
                    .toString('yyyy-MM-dd HH:mm:ss');

            event.data['start_datetime'] = appointment['start_datetime'];
            event.data['end_datetime'] = appointment['end_datetime'];

            // Define success callback function.
            var successCallback = function(response) {
                if (response.exceptions) {
                    response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                    GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE, GeneralFunctions.EXCEPTIONS_MESSAGE);
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                    return;
                }

                if (response.warnings) {
                    // Display warning information to the user.
                    response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                    GeneralFunctions.displayMessageBox(GeneralFunctions.WARNINGS_TITLE, GeneralFunctions.WARNINGS_MESSAGE);
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
                }

                // Define the undo function, if the user needs to reset the last change.
                var undoFunction = function() {
                    appointment['start_datetime'] = Date.parseExact(
                            appointment['start_datetime'], 'yyyy-MM-dd HH:mm:ss')
                            .add({ days: -dayDelta, minutes: -minuteDelta })
                            .toString('yyyy-MM-dd HH:mm:ss');

                    appointment['end_datetime'] = Date.parseExact(
                            appointment['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                            .add({ days: -dayDelta, minutes: -minuteDelta })
                            .toString('yyyy-MM-dd HH:mm:ss');

                    event.data['start_datetime'] = appointment['start_datetime'];
                    event.data['end_datetime'] = appointment['end_datetime'];

                    var postUrl  = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_appointment',
                        postData = {
                            csrfToken: GlobalVariables.csrfToken,
                            appointment_data: JSON.stringify(appointment)
                        };

                    $.post(postUrl, postData, function(response) {
                        $('#notification').hide('blind');
                        revertFunc();
                    }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
                };

                Backend.displayNotification(EALang['appointment_updated'], [
                    {
                        'label': 'Undo',
                        'function': undoFunction
                    }
                ]);

                $('#footer').css('position', 'static'); // Footer position fix.
            };

            // Update appointment data.
            _saveAppointment(appointment, undefined, successCallback, undefined);
        } else {
            // Update unavailable time period.
            var unavailable = {
                id: event.data.id,
                start_datetime: event.start.toString('yyyy-MM-dd HH:mm:ss'),
                end_datetime: event.end.toString('yyyy-MM-dd HH:mm:ss'),
                id_users_provider: event.data.id_users_provider
            }

            var successCallback = function(response) {
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

                var undoFunction = function() {
                    unavailable['start_datetime'] = Date.parseExact(
                            unavailable['start_datetime'], 'yyyy-MM-dd HH:mm:ss')
                            .add({ days: -dayDelta, minutes: -minuteDelta })
                            .toString('yyyy-MM-dd HH:mm:ss');

                    unavailable['end_datetime'] = Date.parseExact(
                            unavailable['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                            .add({ days: -dayDelta, minutes: -minuteDelta })
                            .toString('yyyy-MM-dd HH:mm:ss');

                    event.data['start_datetime'] = unavailable['start_datetime'];
                    event.data['end_datetime'] = unavailable['end_datetime'];

                    var postUrl  = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_unavailable',
                        postData = {
                            csrfToken: GlobalVariables.csrfToken,
                            unavailable: JSON.stringify(unavailable)
                        };

                    $.post(postUrl, postData, function(response) {
                        $('#notification').hide('blind');
                        revertFunc();
                    }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
                };

                Backend.displayNotification(EALang['unavailable_updated'], [
                    {
                        label: 'Undo',
                        function: undoFunction
                    }
                ]);

                $('#footer').css('position', 'static'); // Footer position fix.
            };

            _saveUnavailable(unavailable, successCallback, undefined);
        }
    }

    /**
     * Calendar "View Display" Callback
     *
     * Whenever the calendar changes or refreshes its view certain actions need to be made, in order to
     * display proper information to the user.
     */
    function _calendarViewDisplay() {
        if ($('#select-filter-item').val() === null) {
             return;
        }

        _refreshCalendarAppointments(
                $('#calendar'),
                $('#select-filter-item').val(),
                $('#select-filter-item option:selected').attr('type'),
                $('#calendar').fullCalendar('getView').visStart,
                $('#calendar').fullCalendar('getView').visEnd);

        $(window).trigger('resize'); // Places the footer on the bottom.

        // Remove all open popovers.
        $('.close-popover').each(function() {
            $(this).parents().eq(2).remove();
        });

        // Add new pop overs.
        $('.fv-events').each(function(index, eventHandle) {
            $(eventHandle).popover();
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
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_disable_provider_sync',
            postData = {
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

        var startDatetime = new Date().addMinutes(GlobalVariables.bookAdvanceTimeout),
            endDatetime  = new Date().addMinutes(GlobalVariables.bookAdvanceTimeout).addMinutes(serviceDuration),
            dateFormat;

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
            var start = $('#start-datetime').datepicker('getDate'),
                end = $('#end-datetime').datepicker('getDate');
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
        var start = GeneralFunctions.formatDate(new Date(), GlobalVariables.dateFormat, true),
            end = GeneralFunctions.formatDate(new Date().addHours(1), GlobalVariables.dateFormat, true),
            dateFormat;

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
     * Convert titles to HTML
     *
     * On some calendar events the titles contain html markup that is not displayed properly due to the
     * fullcalendar plugin. This plugin sets the .fc-event-title value by using the $.text() method and
     * not the $.html() method. So in order for the title to displya the html properly we convert all the
     * .fc-event-titles where needed into html.
     */
    function _convertTitlesToHtml() {
        // Convert the titles to html code.
        $('.fc-custom').each(function() {
            var title = $(this).find('.fc-event-title').text();
            $(this).find('.fc-event-title').html(title);
            var time = $(this).find('.fc-event-time').text();
            $(this).find('.fc-event-time').html(time);
        });
    }

    /**
     * Initialize Module
     *
     * This function makes the necessary initialization for the default backend calendar page. If this module
     * is used in another page then this function might not be needed.
     *
     * @param {Boolean} defaultEventHandlers Optional (true), Determines whether the default event handlers will be
     * set for the current page.
     */
    exports.initialize = function(defaultEventHandlers) {
        defaultEventHandlers = defaultEventHandlers || true;

        // Dynamic Date Formats
        var columnFormat = {};

        switch(GlobalVariables.dateFormat) {
            case 'DMY':
                columnFormat = {
                    'month': 'ddd',
                    'week': 'ddd dd/MM',
                    'day': 'dddd dd/MM'
                };

                break;
            case 'MDY':
            case 'YMD':
                columnFormat = {
                    'month': 'ddd',
                    'week': 'ddd MM/dd',
                    'day': 'dddd MM/dd'
                };
                break;
            default:
                throw new Error('Invalid date format setting provided!', GlobalVariables.dateFormat);
        }


        var defaultView = window.innerWidth < 468 ? 'agendaDay' : 'agendaWeek';


        // Initialize page calendar
        $('#calendar').fullCalendar({
            defaultView: defaultView,
            height: _getCalendarHeight(),
            editable: true,
            firstDay: 1, // Monday
            slotMinutes: 30,
            snapMinutes: 15,
            axisFormat: 'HH:mm',
            timeFormat: 'HH:mm{ - HH:mm}',
            allDayText: EALang['all_day'],
            columnFormat: columnFormat,
            titleFormat: {
                month: 'MMMM yyyy',
                week: "MMMM d[ yyyy]{ '&#8212;'[ MMM] d, yyyy}",
                day: 'dddd, MMMM d, yyyy'
            },
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'agendaDay,agendaWeek,month'
            },

            // Translations
            monthNames: [EALang['january'], EALang['february'], EALang['march'], EALang['april'],
                           EALang['may'], EALang['june'], EALang['july'], EALang['august'],
                           EALang['september'],EALang['october'], EALang['november'],
                           EALang['december']],
            monthNamesShort: [EALang['january'].substr(0,3), EALang['february'].substr(0,3),
                    EALang['march'].substr(0,3), EALang['april'].substr(0,3),
                    EALang['may'].substr(0,3), EALang['june'].substr(0,3),
                    EALang['july'].substr(0,3), EALang['august'].substr(0,3),
                    EALang['september'].substr(0,3),EALang['october'].substr(0,3),
                    EALang['november'].substr(0,3), EALang['december'].substr(0,3)],
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
            buttonText: {
                today: EALang['today'],
                day: EALang['day'],
                week: EALang['week'],
                month: EALang['month']
            },

            // Calendar events need to be declared on initialization.
            windowResize: _calendarWindowResize,
            viewDisplay: _calendarViewDisplay,
            dayClick: _calendarDayClick,
            eventClick: _calendarEventClick,
            eventResize: _calendarEventResize,
            eventDrop: _calendarEventDrop,
            eventAfterAllRender: function() {
                _convertTitlesToHtml();
            }
        });

        // Trigger once to set the proper footer position after calendar initialization.
        _calendarWindowResize();

        // Fill the select listboxes of the page.
        if (GlobalVariables.availableProviders.length > 0) {
            var optgroupHtml = '<optgroup label="' + EALang['providers'] + '" type="providers-group">';
            $.each(GlobalVariables.availableProviders, function(index, provider) {
                var hasGoogleSync = (provider['settings']['google_sync'] === '1')
                        ? 'true' : 'false';

                optgroupHtml += '<option value="' + provider['id'] + '" '
                        + 'type="' + FILTER_TYPE_PROVIDER + '" '
                        + 'google-sync="' + hasGoogleSync + '">'
                        + provider['first_name'] + ' ' + provider['last_name']
                        + '</option>';
            });
            optgroupHtml += '</optgroup>';
            $('#select-filter-item').append(optgroupHtml);
        }

        if (GlobalVariables.availableServices.length > 0) {
            optgroupHtml = '<optgroup label="' + EALang['services'] + '" type="services-group">';
            $.each(GlobalVariables.availableServices, function(index, service) {
                optgroupHtml += '<option value="' + service['id'] + '" ' +
                        'type="' + FILTER_TYPE_SERVICE + '">' +
                        service['name'] + '</option>';
            });
            optgroupHtml += '</optgroup>';
            $('#select-filter-item').append(optgroupHtml);
        }

        // Privileges Checks
        if (GlobalVariables.user.role_slug == Backend.DB_SLUG_PROVIDER) {
            $('#select-filter-item optgroup:eq(0)')
                    .find('option[value="' + GlobalVariables.user.id + '"]').prop('selected', true);
            $('#select-filter-item').prop('disabled', true);
        }

        if (GlobalVariables.user.role_slug == Backend.DB_SLUG_SECRETARY) {
            $('#select-filter-item optgroup:eq(1)').remove();
        }

        if (GlobalVariables.user.role_slug == Backend.DB_SLUG_SECRETARY) {
            // Remove the providers that are not connected to the secretary.
            $('#select-filter-item option[type="provider"]').each(function(index, option) {
                var found = false;
                $.each(GlobalVariables.secretaryProviders, function(index, id) {
                    if ($(option).val() == id) {
                        found = true;
                        return false;
                    }
                });

                if (!found) {
                    $(option).remove();
                }
            });

            if ($('#select-filter-item option[type="provider"]').length == 0) {
                $('#select-filter-item optgroup[type="providers-group"]').remove();
            }
        }

        // Bind the default event handlers (if needed).
        if (defaultEventHandlers === true) {
            _bindEventHandlers();
            $('#select-filter-item').trigger('change');
        }

        // Display the edit dialog if an appointment hash is provided.
        if (GlobalVariables.editAppointment != null) {
            var $dialog = $('#manage-appointment'),
                appointment = GlobalVariables.editAppointment;
            _resetAppointmentDialog();

            $dialog.find('.modal-header h3').text(EALang['edit_appointment_title']);
            $dialog.find('#appointment-id').val(appointment['id']);
            $dialog.find('#select-service').val(appointment['id_services']).change();
            $dialog.find('#select-provider').val(appointment['id_users_provider']);

            // Set the start and end datetime of the appointment.
            var startDatetime = Date.parseExact(appointment['start_datetime'],
                    'yyyy-MM-dd HH:mm:ss');
            $dialog.find('#start-datetime').val(GeneralFunctions.formatDate(startDatetime, GlobalVariables.dateFormat, true));

            var endDatetime = Date.parseExact(appointment['end_datetime'],
                    'yyyy-MM-dd HH:mm:ss');
            $dialog.find('#end-datetime').val(GeneralFunctions.formatDate(endDatetime, GlobalVariables.dateFormat, true));

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

            $dialog.modal('show');
        }

        // Apply qtip to control tooltips.
        $('#calendar-toolbar button').qtip({
            position: {
                my: 'top center',
                at: 'bottom center'
            },
            style: {
                classes: 'qtip-green qtip-shadow custom-qtip'
            }
        });

        $('#select-filter-item').qtip({
            position: {
                my: 'middle left',
                at: 'middle right'
            },
            style: {
                classes: 'qtip-green qtip-shadow custom-qtip'
            }
        });

        // Fine tune the footer's position only for this page.
        if (window.innerHeight < 700) {
            $('#footer').css('position', 'static');
        }

        if ($('#select-filter-item option').length == 0) {
            $('#calendar-actions button').prop('disabled', true);
        }
    };

})(window.BackendCalendar);
