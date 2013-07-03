/**
 * This namespace contains functions that are used by the backend calendar page.
 * 
 * @namespace BackendCalendar
 */
var BackendCalendar = {
    // :: NAMESPACE CONSTANTS
    FILTER_TYPE_PROVIDER: 'provider',
    FILTER_TYPE_SERVICE: 'service',
    
    // :: NAMESPACE VALIABLES
    lastFocusedEventData: undefined, // Contain event data for later use.
    
    /**
     * This function makes the necessary initialization for the default backend
     * calendar page. If this namespace is used in another page then this function
     * might not be needed.
     * 
     * @param {bool} defaultEventHandlers (OPTIONAL = TRUE) Determines whether the
     * default event handlers will be set for the current page.
     */
    initialize: function(defaultEventHandlers) {
        if (defaultEventHandlers === undefined) defaultEventHandlers = true;
        
        // :: INITIALIZE THE DOM ELEMENTS OF THE PAGE
        $('#calendar').fullCalendar({
            'defaultView': 'agendaWeek',
            'height': BackendCalendar.getCalendarHeight(),
            'editable': true,
            'slotMinutes': 30,
            'columnFormat': {
                'month': 'ddd',
                'week': 'ddd d/M',
                'day': 'dddd d/M'
            },
            'titleFormat': {
                'month': 'MMMM yyyy',
                'week': "MMMM d[ yyyy]{ '&#8212;'[ MMM] d, yyyy}",
                'day': 'dddd, MMM d, yyyy'
            },
            'header': {
                'left': 'prev,next today',
                'center': 'title',
                'right': 'agendaDay,agendaWeek,month'
            },
            // Calendar events need to be declared on initialization.
            'windowResize': BackendCalendar.calendarWindowResize,
            'viewDisplay': BackendCalendar.calendarViewDisplay,
            'dayClick': BackendCalendar.calendarDayClick,
            'eventClick': BackendCalendar.calendarEventClick,
            'eventResize': BackendCalendar.calendarEventResize,
            'eventDrop': BackendCalendar.calendarEventDrop
        });
        
        // Trigger once to set the proper footer position after calendar 
        // initialization.
        BackendCalendar.calendarWindowResize(); 
        
        // :: FILL THE SELECT ELEMENTS OF THE PAGE
        var optgroupHtml = '<optgroup label="Providers">';
        $.each(GlobalVariables.availableProviders, function(index, provider) {
            var hasGoogleSync = (provider['settings']['google_sync'] === '1') 
                    ? 'true' : 'false';
                
            optgroupHtml += '<option value="' + provider['id'] + '" '  
                    + 'type="' + BackendCalendar.FILTER_TYPE_PROVIDER + '" '  
                    + 'google-sync="' + hasGoogleSync + '">'  
                    + provider['first_name'] + ' ' + provider['last_name'] 
                    + '</option>';
        });
        optgroupHtml += '</optgroup>';
        $('#select-filter-item').append(optgroupHtml);
        
        optgroupHtml = '<optgroup label="Services">';
        $.each(GlobalVariables.availableServices, function(index, service) {
            optgroupHtml += '<option value="' + service['id'] + '" ' + 
                    'type="' + BackendCalendar.FILTER_TYPE_SERVICE + '">' + 
                    service['name'] + '</option>';
        });
        optgroupHtml += '</optgroup>';
        $('#select-filter-item').append(optgroupHtml);
        
        // :: BIND THE DEFAULT EVENT HANDLERS
        if (defaultEventHandlers === true) {
            BackendCalendar.bindEventHandlers();
            $('#select-filter-item').trigger('change');
        }
    },
    
    /**
     * This method binds the default event handlers for the backend calendar
     * page. If you do not need the default handlers then initialize the page
     * by setting the "defaultEventHandlers" argument to "false".
     */
    bindEventHandlers: function() {
        /**
         * Event: Calendar Filter Item "Change"
         * 
         * Load the appointments that correspond to the select filter item and
         * display them on the calendar.
         */
        $('#select-filter-item').change(function() { 
            BackendCalendar.refreshCalendarAppointments(
                    $('#calendar'),
                    $('#select-filter-item').val(),
                    $('#select-filter-item option:selected').attr('type'), 
                    $('#calendar').fullCalendar('getView').visStart,
                    $('#calendar').fullCalendar('getView').visEnd);
                    
            // If current value is service, then the sync buttons must be 
            // disabled.
            if ($('#select-filter-item option:selected').attr('type') 
                    === BackendCalendar.FILTER_TYPE_SERVICE) {
                $('#calendar-actions').hide();
            } else {
                $('#calendar-actions').show();
                // If the user has already the sync enabled then apply the proper
                // style changes.
                if ($('#select-filter-item option:selected').attr('google-sync') 
                        === 'true') {
                    $('#enable-sync').addClass('btn-success enabled');
                    $('#enable-sync i').addClass('icon-white');
                    $('#enable-sync span').text('Disable Sync');
                    $('#google-sync').prop('disabled', false);
                } else {
                    $('#enable-sync').removeClass('btn-success enabled');
                    $('#enable-sync i').removeClass('icon-white');
                    $('#enable-sync span').text('Enable Sync');
                    $('#google-sync').prop('disabled', true);
                }
            }
        });
        
        /**
         * Event: Reload Button "Click"
         * 
         * When the user clicks the reload button an the calendar items need to 
         * be refreshed.
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
            
            var appointmentData = BackendCalendar.lastFocusedEventData.data;
            var dialog = $('#manage-appointment');
            
            BackendCalendar.resetAppointmentDialog();
            
            // :: APPLY APPOINTMENT DATA AND SHOW TO MODAL DIALOG
            dialog.find('.modal-header h3').text('Edit Appointment');
            dialog.find('#appointment-id').val(appointmentData['id']);
            dialog.find('#select-service').val(appointmentData['id_services']);
            dialog.find('#select-provider').val(appointmentData['id_users_provider']);
            
            // Set the start and end datetime of the appointment.\
            var startDatetime = Date.parseExact(appointmentData['start_datetime'],
                    'yyyy-MM-dd HH:mm:ss').toString('dd/MM/yyyy HH:mm');            
            dialog.find('#start-datetime').val(startDatetime);
            
            var endDatetime = Date.parseExact(appointmentData['end_datetime'],
                    'yyyy-MM-dd HH:mm:ss').toString('dd/MM/yyyy HH:mm');
            dialog.find('#end-datetime').val(endDatetime);
            
            var customerData = appointmentData['customer'];
            dialog.find('#customer-id').val(appointmentData['id_users_customer']);
            dialog.find('#first-name').val(customerData['first_name']);
            dialog.find('#last-name').val(customerData['last_name']);
            dialog.find('#email').val(customerData['email']);
            dialog.find('#phone-number').val(customerData['phone_number']);
            dialog.find('#address').val(customerData['address']);
            dialog.find('#city').val(customerData['city']);
            dialog.find('#zip-code').val(customerData['zip_code']);
            dialog.find('#notes').val(appointmentData['notes']);
            
            // :: DISPLAY THE MANAGE APPOINTMENTS MODAL DIALOG
            dialog.modal('show');
        });
        
        /**
         * Event: Popover Delete Button "Click"
         * 
         * Displays a prompt on whether the user wants the appoinmtent to be
         * deleted. If he confirms the deletion then an ajax call is made to 
         * the server and deletes the appointment from the database.
         */
        $(document).on('click', '.delete-popover', function() {
            $(this).parents().eq(2).remove(); // Hide the popover
            
            var messageButtons = {
                'OK': function() {
                    var postUrl = GlobalVariables.baseUrl + 'backend/ajax_delete_appointment';
                    
                    var postData = { 
                        'appointment_id' : BackendCalendar.lastFocusedEventData.data['id'],
                        'delete_reason': $('#delete-reason').val()
                    };
                    
                    $.post(postUrl, postData, function(response) {
                        /////////////////////////////////////////////////////////
                        console.log('Delete Appointment Response :', response);
                        /////////////////////////////////////////////////////////
                        
                        $('#message_box').dialog('close');
                        
                        if (response.exceptions) {
                            response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                            GeneralFunctions.displayMessageBox('Unexpected Issues', 'Unfortunately '
                                    + 'the operation could not complete successfully. The following '
                                    + 'issues occured:');
                            $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                            return;
                        }
                        
                        if (response.warnings) {
                            response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                            GeneralFunctions.displayMessageBox('Unexpected Warnings', 'The operation '
                                    + 'was completed but the following warnings appeared:');
                            $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
                        }
                        
                        // Refresh calendar event items.                        
                        $('#select-filter-item').trigger('change');
                    }, 'json');
                },
                'Cancel': function() {
                    $('#message_box').dialog('close');
                }
            };
            
            GeneralFunctions.displayMessageBox('Delete Appointment', 'Please take a minute '
                    + 'to write the reason you are deleting the appointment:', messageButtons);
            $('#message_box').append('<textarea id="delete-reason"></textarea>');
            $('#delete-reason').css('width', '320px');
        });
        
        /**
         * Event: Manage Appointments Dialog Cancel Button "Click"
         * 
         * Closes the dialog without saving any changes to the database.
         */
        $('#manage-appointment #cancel-button').click(function() {
            $('#manage-appointment').modal('hide');
        });
        
        /**
         * Event: Manage Appointments Dialog Save Button "Click"
         * 
         * Stores the appointment changes or inserts a new appointment depending the dialog
         * mode.
         */
        $('#manage-appointment #save-button').click(function() {
            // Before doing anything the appointment data need to be validated.
            if (!BackendCalendar.validateAppointmentForm()) {
                return; // validation failed
            }
            
            // :: PREPARE APPOINTMENT DATA FOR AJAX CALL
            var modalHandle = $('#manage-appointment');
            
            // Id must exist on the object in order for the model to update 
            // the record and not to perform an insert operation.
            
            var startDatetime = Date.parseExact(modalHandle.find('#start-datetime').val(),
                    'dd/MM/yyyy HH:mm').toString('yyyy-MM-dd HH:mm:ss');
            var endDatetime = Date.parseExact(modalHandle.find('#end-datetime').val(),
                    'dd/MM/yyyy HH:mm').toString('yyyy-MM-dd HH:mm:ss');
            
            var appointmentData = {
                'id_services': modalHandle.find('#select-service').val(),
                'id_users_provider': modalHandle.find('#select-provider').val(),
                'start_datetime': startDatetime,
                'end_datetime': endDatetime,
                'notes': modalHandle.find('#notes').val()
            };
            
            if (modalHandle.find('#appointment-id').val() !== '') {
                // Set the id value, only if we are editing an appointment.
                appointmentData['id'] = modalHandle.find('#appointment-id').val();
            }
            
            var customerData = {
                'first_name': modalHandle.find('#first-name').val(),
                'last_name': modalHandle.find('#last-name').val(),
                'email': modalHandle.find('#email').val(),
                'phone_number': modalHandle.find('#phone-number').val(),
                'address': modalHandle.find('#address').val(),
                'city': modalHandle.find('#city').val(),
                'zip_code': modalHandle.find('#zip-code').val()
            };
            
            if (modalHandle.find('#customer-id').val() !== '') {
                // Set the id value, only if we are editing an appointment.
                customerData['id'] = modalHandle.find('#customer-id').val();
                appointmentData['id_users_customer'] = customerData['id'];
            }
            
            // :: DEFINE SUCCESS EVENT CALLBACK
            var successCallback = function(response) {
                if (response.exceptions) {             
                    response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                    GeneralFunctions.displayMessageBox('Unexpected Issues', 'Unfortunately '
                            + 'the operation could not complete successfully. The following '
                            + 'issues occured:');
                    $('#messsage_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                    
                    modalHandle.find('.modal-header').append(
                        '<br><div class="alert alert-error">' + 
                            'Unexpected issues occured!' +
                        '</div>');
                
                    return;
                }
                
                // Display success message to the user.
                modalHandle.find('.modal-header').append(
                        '<br><div class="alert alert-success">' + 
                            'Appointment saved successfully!' +
                        '</div>');
                
                // Close the modal dialog and refresh the calendar appointments 
                // after one second.
                setTimeout(function() {
                    modalHandle.find('.alert').remove();
                    modalHandle.modal('hide');
                    $('#select-filter-item').trigger('change');
                }, 2000);
            };
            
            // :: DEFINE AJAX ERROR EVENT CALLBACK
            var errorCallback = function() {
                modalHandle.find('.modal-header').append(
                        '<br><div class="alert alert-error">' + 
                            'A server communication error occured, please try again.' +
                        '</div>');
            };
            
            // :: CALL THE UPDATE APPOINTMENT METHOD
            BackendCalendar.saveAppointmentData(appointmentData, customerData, 
                    successCallback, errorCallback);
        }); 
        
        /**
         * Event: Enable - Disable Synchronization Button "Click"
         * 
         * When the user clicks on the "Enable Sync" button, a popup should appear
         * that is going to follow the web server authorization flow of OAuth. 
         */
        $('#enable-sync').click(function() {
            if ($('#enable-sync').hasClass('enabled') === false) {
                // :: ENABLE SYNCHRONIZATION FOR SELECTED PROVIDER
                var authUrl = GlobalVariables.baseUrl + 'google/oauth/' 
                        + $('#select-filter-item').val();
                
                var redirectUrl = GlobalVariables.baseUrl + 'google/oauth_callback';

                var windowHandle = window.open(authUrl, 'Authorize Easy!Appointments',
                        'width=800, height=600');

                var authInterval = window.setInterval(function() {
                    // When the browser redirects to the google user consent page the 
                    // "window.document" variable becomes "undefined" and when it comes 
                    // back to the redirect url it changes back. So check whether the 
                    // variable is undefined to avoid javascript errors.
                    if (windowHandle.document !== undefined) {
                        if (windowHandle.document.URL.indexOf(redirectUrl) !== -1) {
                            // The user has granted access to his data.
                            windowHandle.close();
                            window.clearInterval(authInterval);
                            $('#enable-sync').addClass('btn-success enabled');
                            $('#enable-sync i').addClass('icon-white');
                            $('#enable-sync span').text('Disable Sync');
                            $('#google-sync').prop('disabled', false);
                        }
                    }
                }, 100);
                
            } else {
                // :: DISABLE SYNCHRONIZATION FOR SELECTED PROVIDER
                // Update page elements and make an ajax call to remove the google 
                // sync setting of the selected provider.
                $.each(GlobalVariables.availableProviders, function(index, provider) {
                    if (provider['id'] == $('#select-filter-item').val()) {
                        provider['settings']['google_sync'] = '0';
                        provider['settings']['google_token'] = null;
                        
                        BackendCalendar.disableProviderSync(provider['id']);
                        
                        $('#enable-sync').removeClass('btn-success enabled');
                        $('#enable-sync i').removeClass('icon-white');
                        $('#enable-sync span').text('Enable Sync');
                        $('#google-sync').prop('disabled', true);
                        
                        return;
                    }
                });
            }
        });
        
        /**
         * Event: Insert Appointment Button "Click"
         * 
         * When the user presses this button, the manage appointment dialog opens and lets
         * the user to create a new appointment.
         */
        $('#insert-appointment').click(function() {
            BackendCalendar.resetAppointmentDialog();
            var dialog = $('#manage-appointment');
            dialog.find('.modal-header h3').text('New Appointment');
            dialog.modal('show');
        });
        
        /**
         * Event : Insert Unavailable Time Period Button "Click"
         * 
         * When the user clicks this button a popup dialog appears and the use can set 
         * a time period where he cannot accept any appointments.
         */
        $('#insert-unavailable').click(function() {
            // @task Implement this event handler.
        });
    },
            
    /**
     * This method calculates the proper calendar height, in order to be displayed
     * correctly, even when the browser window is resizing.
     * 
     * @return {int} Returns the calendar element height in pixels.
     */
    getCalendarHeight: function () {
        var result = window.innerHeight - $('#footer').height() - $('#header').height() 
                - $('#calendar-toolbar').height() - 80; // 80 for fine tuning
        return (result > 500) ? result : 500; // Minimum height is 500px
    },
           
    /**
     * This method reloads the registered appointments for the selected date period 
     * and filter type.
     * 
     * @param {object} calendarHandle The calendar jQuery object.
     * @param {int} recordId The selected record id.
     * @param {string} filterType The filter type, could be either FILTER_TYPE_PROVIDER
     * or FILTER_TYPE_SERVICE
     * @param {date} startDate Visible start date of the calendar.
     * @param {type} endDate Visible end date of the calendar.
     */
    refreshCalendarAppointments: function(calendarHandle, recordId, filterType, 
            startDate, endDate) {
        var postUrl = GlobalVariables.baseUrl + 'backend/ajax_get_calendar_appointments';
            
        var postData = {
            'record_id': recordId,
            'start_date': startDate.toString('yyyy-MM-dd'),
            'end_date': endDate.toString('yyyy-MM-dd'),
            'filter_type': filterType
        };

        $.post(postUrl, postData, function(response) {
            ////////////////////////////////////////////////////////////////////
            //console.log('Refresh Calendar Appointments Response :', response);
            ////////////////////////////////////////////////////////////////////
            
            if (response.exceptions) {
                response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                GeneralFunctions.displayMessageBox('Unexpected Issues', 'Unfortunately '
                        + 'the operation could not complete successfully. The following '
                        + 'issues occured:');
                $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                return;
            }
            
            // Add the appointments to the calendar.
            var calendarEvents = new Array();
            
            $.each(response, function(index, appointment){
                var event = {
                    'id': appointment['id'],
                    'title': appointment['service']['name'] + ' - ' 
                            + appointment['customer']['first_name'] + ' ' 
                            + appointment['customer']['last_name'],
                    'start': appointment['start_datetime'],
                    'end': appointment['end_datetime'],
                    'allDay': false,
                    'data': appointment // Store appointment data for later use.
                };
                
                calendarEvents.push(event);
            });
            
            calendarHandle.fullCalendar('removeEvents');
            calendarHandle.fullCalendar('addEventSource', calendarEvents);
        }, 'json');
    },
    
    /**
     * This method stores the changes of an already registered appointment 
     * into the database, via an ajax call.
     * 
     * @param {object} appointmentData Contain the new appointment data. The 
     * id of the appointment MUST be already included. The rest values must 
     * follow the database structure.
     * @param {object} customerData (OPTIONAL) contains the customer data.
     * @param {function} successCallback (OPTIONAL) If defined, this function is
     * going to be executed on post success.
     * @param {function} errorCallback (OPTIONAL) If defined, this function is 
     * going to be executed on post failure.
     */
    saveAppointmentData : function(appointmentData, customerData, 
            successCallback, errorCallback) {
        // :: MAKE AN AJAX CALL TO SERVER - STORE APPOINTMENT DATA
        var postUrl = GlobalVariables.baseUrl + 'backend/ajax_save_appointment';
        
        var postData = {};
        
        postData['appointment_data'] = JSON.stringify(appointmentData);
        
        if (customerData !== undefined) {
            postData['customer_data'] = JSON.stringify(customerData);
        }
        
        $.ajax({
            'type': 'POST',
            'url': postUrl,
            'data': postData,
            'dataType': 'json',
            'success': function(response) {
                /////////////////////////////////////////////////////////////
                console.log('Save Appointment Data Response:', response);
                /////////////////////////////////////////////////////////////            
                
                if (successCallback !== undefined) {
                    successCallback(response);
                }
            },
            'error': function(jqXHR, textStatus, errorThrown) {
                //////////////////////////////////////////////////////////////////
                console.log('Save Appointment Data Error:', jqXHR, textStatus, 
                        errorThrown);
                //////////////////////////////////////////////////////////////////
                
                if (errorCallback !== undefined) {
                    errorCallback();
                }
            }
        });
    },
    
    /**
     * Calendar Event "Resize" Callback
     * 
     * The user can change the duration of an event by resizing an appointment
     * object on the calendar. This change needs to be stored to the database
     * too and this is done via an ajax call.
     * 
     * @see updateAppointmentData()
     */
    calendarEventResize: function(event, dayDelta, minuteDelta, revertFunc, 
            jsEvent, ui, view) {
        if ($('#notification').is(':visible')) {
            $('#notification').hide('bind');
        }  
        
        // :: PREPARE THE APPOINTMENT DATA
        var appointmentData = GeneralFunctions.clone(event.data);

        // Must delete the following because only appointment data should be 
        // provided to the ajax call.
        delete appointmentData['customer'];
        delete appointmentData['provider'];
        delete appointmentData['service'];

        appointmentData['end_datetime'] = Date.parseExact(
                appointmentData['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                .add({ minutes: minuteDelta })
                .toString('yyyy-MM-dd HH:mm:ss');

        // :: DEFINE THE SUCCESS CALLBACK FUNCTION
        var successCallback = function(response) {
            if (response.exceptions) {
                response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                GeneralFunctions.displayMessageBox('Unexpected Issues', 'Unfortunately ' 
                        + 'the operation could not complete successfully. The following '
                        + 'issues occured:');
                $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                return;
            }
            
            if (response.warnings) {
                // Display warning information to the user.
                response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                GeneralFunctions.displayMessageBox('Warnings', 'The operation completed but '
                        + 'there were some warnings:');
                $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
            }

            // Display success notification to user.
            var undoFunction = function() {
                appointmentData['end_datetime'] = Date.parseExact(
                        appointmentData['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                        .add({ minutes: -minuteDelta })
                        .toString('yyyy-MM-dd HH:mm:ss');
                
                var postUrl = GlobalVariables.baseUrl + 'backend/ajax_save_appointment';
                     
                var postData = { 
                    'appointment_data': JSON.stringify(appointmentData) 
                };

                $.post(postUrl, postData, function(response) {
                    $('#notification').hide('blind');
                    revertFunc();
                });
            };

            Backend.displayNotification('Appointment updated successfully!', [
                {
                    'label': 'Undo',
                    'function': undoFunction
                }
            ]);
            $('#footer').css('position', 'static'); // Footer position fix.
        };

        // :: UPDATE APPOINTMENT DATA VIA AJAX CALL
        BackendCalendar.saveAppointmentData(appointmentData, undefined, 
                successCallback, undefined);
    },
            
    /**
     * Calendar Window "Resize" Callback
     * 
     * The calendar element needs to be resized too in order to fit into the
     * window. Nevertheless, if the window becomes very small the the calendar
     * won't shrink anymore.
     * 
     * @see getCalendarHeight()
     */
    calendarWindowResize: function(view) {
        $('#calendar').fullCalendar('option', 'height', 
                BackendCalendar.getCalendarHeight());
    },
    
    /**
     * Calendar Day "Click" Callback
     * 
     * When the user clicks on a day square on the calendar, then he will 
     * automatically be transfered to that day view calendar.
     */
    calendarDayClick: function(date, allDay, jsEvent, view) {
        if (allDay) {
            // Switch to day view
            $('#calendar').fullCalendar('gotoDate', date);
            $('#calendar').fullCalendar('changeView', 'agendaDay');
        }
    },
           
    /**
     * Calendar Event "Click" Callback
     * 
     * When the user clicks on an appointment object on the calendar, then 
     * a data preview popover is display above the calendar item. 
     */
    calendarEventClick: function(event, jsEvent, view) {
        $('.popover').remove(); // Close all open popovers.
        
        // Display a popover with the event details.
        var html = 
                '<style type="text/css">' 
                    + '.popover-content strong {min-width: 80px; display:inline-block;}' 
                    + '.popover-content button {margin-right: 10px;}'
                    + '</style>' +
                '<strong>Start</strong> ' 
                    + event.start.toString('dd/MM/yyyy HH:mm') 
                    + '<br>' + 
                '<strong>End</strong> ' 
                    + event.end.toString('dd/MM/yyyy HH:mm') 
                    + '<br>' + 
                '<strong>Service</strong> ' 
                    + event.data['service']['name'] 
                    + '<br>' +  
                '<strong>Provider</strong> ' 
                    + event.data['provider']['first_name'] + ' ' 
                    + event.data['provider']['last_name'] 
                    + '<br>' +
                '<strong>Customer</strong> ' 
                    + event.data['customer']['first_name'] + ' ' 
                    + event.data['customer']['last_name'] 
                    + '<hr>' +
                '<center>' + 
                '<button class="edit-popover btn btn-primary">Edit</button>' +
                '<button class="delete-popover btn btn-danger">Delete</button>' +
                '<button class="close-popover btn" data-po=' + jsEvent.target + '>Close</button>' +
                '</center>';

        $(jsEvent.target).popover({
            'placement': 'top',
            'title': event.title,
            'content': html,
            'html': true,
            'container': 'body',
            'trigger': 'manual'
        });
        
        BackendCalendar.lastFocusedEventData = event;
        
        $(jsEvent.target).popover('show');
    },
    
    /**
     * Calendar Event "Drop" Callback
     * 
     * This event handler is triggered whenever the user drags and drops 
     * an event into a different position on the calendar. We need to update
     * the database with this change. This is done via an ajax call.
     */
    calendarEventDrop : function(event, dayDelta, minuteDelta, allDay, 
            revertFunc, jsEvent, ui, view) {
        if ($('#notification').is(':visible')) {
            $('#notification').hide('bind');
        }       
                
        // :: PREPARE THE APPOINTMENT DATA        
        var appointmentData = GeneralFunctions.clone(event.data);
        
        // Must delete the following because only appointment data should be 
        // provided to the ajax call.
        delete appointmentData['customer'];
        delete appointmentData['provider'];
        delete appointmentData['service'];

        appointmentData['start_datetime'] = Date.parseExact(
                appointmentData['start_datetime'], 'yyyy-MM-dd HH:mm:ss')
                .add({ days: dayDelta, minutes: minuteDelta })
                .toString('yyyy-MM-dd HH:mm:ss');
        
        appointmentData['end_datetime'] = Date.parseExact(
                appointmentData['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                .add({ days: dayDelta, minutes: minuteDelta })
                .toString('yyyy-MM-dd HH:mm:ss');
        
        event.data['start_datetime'] = appointmentData['start_datetime'];
        event.data['end_datetime'] = appointmentData['end_datetime'];
        
        // :: DEFINE THE SUCCESS CALLBACK FUNCTION
        var successCallback = function(response) {
            if (response.exceptions) {
                response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                GeneralFunctions.displayMessageBox('Unexpected Issues', 'Unfortunately ' 
                        + 'the operation could not complete successfully. The following '
                        + 'issues occured:');
                $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                return;
            }
            
            if (response.warnings) {
                // Display warning information to the user.
                response.warnings = GeneralFunctions.parseExceptions(response.warnings);
                GeneralFunctions.displayMessageBox('Warnings', 'The operation completed but '
                        + 'there were some warnings:');
                $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
            }
            
            // Define the undo function, if the user needs to reset the last change.
            var undoFunction = function() {
                appointmentData['start_datetime'] = Date.parseExact(
                        appointmentData['start_datetime'], 'yyyy-MM-dd HH:mm:ss')
                        .add({ days: -dayDelta, minutes: -minuteDelta })
                        .toString('yyyy-MM-dd HH:mm:ss');

                appointmentData['end_datetime'] = Date.parseExact(
                        appointmentData['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                        .add({ days: -dayDelta, minutes: -minuteDelta })
                        .toString('yyyy-MM-dd HH:mm:ss');
                
                event.data['start_datetime'] = appointmentData['start_datetime'];
                event.data['end_datetime'] = appointmentData['end_datetime'];
        
                var postUrl  = GlobalVariables.baseUrl + 'backend/ajax_save_appointment';
                
                var postData = { 
                    'appointment_data': JSON.stringify(appointmentData) 
                };

                $.post(postUrl, postData, function(response) {
                    $('#notification').hide('blind');
                    revertFunc();
                });
            };

            Backend.displayNotification('Appointment updated successfully!', [
                {
                    'label': 'Undo',
                    'function': undoFunction
                }
            ]);
            
            $('#footer').css('position', 'static'); // Footer position fix.
        };

        // :: UPDATE APPOINTMENT DATA VIA AJAX CALL
        BackendCalendar.saveAppointmentData(appointmentData, undefined, 
                successCallback, undefined);
    },
    
    /**
     * Calendar "View Display" Callback
     * 
     * Whenever the calendar changes or refreshes its view certain actions 
     * need to be made, in order to display proper information to the user.
     */
    calendarViewDisplay : function(view) {
        // Place the footer into correct position because the calendar
        // height might change.
        BackendCalendar.refreshCalendarAppointments(
                $('#calendar'),
                $('#select-filter-item').val(),
                $('#select-filter-item option:selected').attr('type'), 
                $('#calendar').fullCalendar('getView').visStart,
                $('#calendar').fullCalendar('getView').visEnd);
        $(window).trigger('resize'); 

        $('.fv-events').each(function(index, eventHandle) {
            $(eventHandle).popover();
        });
    },

    /**
     * This method disables the google synchronization for a specific provider.
     * 
     * @param {int} providerId The selected provider record id.
     */
    disableProviderSync: function(providerId) {
        // Make an ajax call to the server in order to disable the setting
        // from the database.
        var postUrl = GlobalVariables.baseUrl + 'backend/ajax_disable_provider_sync';
        
        var postData = {
            'provider_id' : providerId
        };
        
        $.post(postUrl, postData, function(response) {
            ////////////////////////////////////////////////////////////
            //console.log('Disable Provider Sync Response :', response);
            ////////////////////////////////////////////////////////////
            
            if (response.exceptions) {
                response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                GeneralFunctions.displayMessageBox('Unexpected Issues', 'Unfortunately ' 
                        + 'the operation could not complete successfully. The following '
                        + 'issues occured:');
                $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                return;
            }
        }, 'json');
    },
    
    /**
     * This method resets the manage appointment dialog modal to its initial 
     * state. After that you can make any modification might be necessary in 
     * order to bring the dialog to the desired state.
     */
    resetAppointmentDialog: function() {
        var dialog = $('#manage-appointment');
        
        // :: EMPTY FORM FIELDS
        dialog.find('input, textarea').val('');
        dialog.find('#modal-message').hide();
        dialog.find('#select-service, #select-provider').empty();
        
        // :: PREPARE SERVICE AND PROVIDER LISTBOXES
        $.each(GlobalVariables.availableServices, function(index, service) {
            var option = new Option(service['name'], service['id']);
            dialog.find('#select-service').append(option);
        });
        dialog.find('#select-service').val(
                dialog.find('#select-service').eq(0).attr('value'));
        
        // Fill the providers listbox with providers that can serve the appointment's 
        // service and then select the user's provider.
        $.each(GlobalVariables.availableProviders, function(index, provider) {
            var canProvideService = false; 

            $.each(provider['services'], function(index, service) {
                if (service == dialog.find('#select-service').val()) {
                    canProvideService = true;
                    return;
                }
            });

            if (canProvideService) { // Add the provider to the listbox.
                var option = new Option(provider['first_name']  
                       + ' ' + provider['last_name'], provider['id']);
                dialog.find('#select-provider').append(option);
            }
        });
            
        // :: SETUP START AND END DATETIME PICKERS
        // Get the selected service duration. It will be needed in order to calculate
        // the appointment end datetime.
        var serviceDuration = 0;
        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service['id'] == dialog.find('#select-service').val()) {
                serviceDuration = service['duration'];
                return;
            }
        });
        
        var startDatetime = new Date().addMinutes(GlobalVariables.bookAdvanceTimeout)
                .toString('dd/MM/yyyy HH:mm');
        var endDatetime  = new Date().addMinutes(GlobalVariables.bookAdvanceTimeout)
                .addMinutes(serviceDuration).toString('dd/MM/yyyy HH:mm');
        
        dialog.find('#start-datetime').datetimepicker({
            'dateFormat': 'dd/mm/yy',
            'minDate': 0
        });
        dialog.find('#start-datetime').val(startDatetime);
        
        dialog.find('#end-datetime').datetimepicker({
            'dateFormat': 'dd/mm/yy',
            'minDate': 0
        });
        dialog.find('#end-datetime').val(endDatetime);
    },
            
    /**
     * Validate the manage appointment dialog data. Validation checks need to
     * run every time the data are going to be saved.
     * 
     * @returns {bool} Returns the validation result.
     */
    validateAppointmentForm: function() {
        var dialog = $('#manage-appointment');
        
        // Reset previous validation css formating.
        dialog.find('.control-group').removeClass('error');
        dialog.find('#modal-message').hide();
        
        try {
            // :: CHECK REQUIRED FIELDS
            var missingRequiredField = false;
            dialog.find('.required').each(function() {
                if ($(this).val() === '') {
                    $(this).parents().eq(1).addClass('error');
                    missingRequiredField = true;
                }
            }); 
            if (missingRequiredField) {
                throw 'Fields with * are required!';                       
            }
             
            // :: CHECK EMAIL ADDRESS
            if (!GeneralFunctions.validateEmail(dialog.find('#email').val())) {
                dialog.find('#email').parents().eq(1).addClass('error');
                throw 'Invalid email address!';
            }
            
            return true;
            
        } catch(exc) {
            dialog.find('#modal-message').addClass('alert-error').text(exc).show('fade');
            return false;
        }
    }
};