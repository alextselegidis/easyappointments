/**
 * This namespace contains functions that are used by the backend calendar page.
 * 
 * @namespace BackendCalendar
 */
var BackendCalendar = {
    // :: NAMESPACE CONSTANTS
    FILTER_TYPE_PROVIDER   : 'provider',
    FILTER_TYPE_SERVICE    : 'service',
    
    // :: NAMESPACE VALIABLES
    lastFocusedEventData   : undefined, // Contain event data for later use.
    
    /**
     * This function makes the necessary initialization for the default backend
     * calendar page. If this namespace is used in another page then this function
     * might not be needed.
     * 
     * @param {bool} defaultEventHandlers (OPTIONAL = TRUE) Determines whether the
     * default event handlers will be set for the current page.
     */
    initialize : function(defaultEventHandlers) {
        if (defaultEventHandlers === undefined) defaultEventHandlers = true;
        
        // :: INITIALIZE THE DOM ELEMENTS OF THE PAGE
        $('#calendar').fullCalendar({
            defaultView     : 'agendaWeek',
            height          : BackendCalendar.getCalendarHeight(),
            editable        : true,
            slotMinutes     : 15,
            columnFormat    : {
                month   : 'ddd',
                week    : 'ddd d/M',
                day     : 'dddd d/M'
            },
            titleFormat     : {
                month   : 'MMMM yyyy',
                week    : "MMMM d[ yyyy]{ '&#8212;'[ MMM] d, yyyy}",
                day     : 'dddd, MMM d, yyyy'
            },
            header          : {
                left    : 'prev,next today',
                center  : 'title',
                right   : 'agendaDay,agendaWeek,month'
            },
            
            // Calendar events need to be declared on initialization.
            windowResize    : BackendCalendar.calendarWindowResize,
            viewDisplay     : BackendCalendar.calendarViewDisplay,
            dayClick        : BackendCalendar.calendarDayClick,
            eventClick      : BackendCalendar.calendarEventClick,
            eventResize     : BackendCalendar.calendarEventResize,
            eventDrop       : BackendCalendar.calendarEventDrop
        });
        
        // Trigger once to set the proper footer position after calendar 
        // initialization.
        BackendCalendar.calendarWindowResize(); 
        
        // :: FILL THE SELECT ELEMENTS OF THE PAGE
        var optgroupHtml = '<optgroup label="Providers">';
        $.each(GlobalVariables.availableProviders, function(index, provider) {
            var hasGoogleSync = (provider['settings']['google_sync'] === '1') 
                ? 'true' : 'false';
                
            optgroupHtml += '<option value="' + provider['id'] + '" ' + 
                    'type="' + BackendCalendar.FILTER_TYPE_PROVIDER + '" ' + 
                    'google-sync="' + hasGoogleSync + '">' + 
                    provider['first_name'] + ' ' + provider['last_name'] + '</option>';
        });
        optgroupHtml += '</optgroup>';
        $('#select-filter-item').append(optgroupHtml)
        
        optgroupHtml = '<optgroup label="Services">';
        $.each(GlobalVariables.availableServices, function(index, service) {
            optgroupHtml += '<option value="' + service['id'] + '" ' + 
                    'type="' + BackendCalendar.FILTER_TYPE_SERVICE + '">' + 
                    service['name'] + '</option>';
        });
        optgroupHtml += '</optgroup>';
        $('#select-filter-item').append(optgroupHtml)
        
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
    bindEventHandlers : function() {
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
                $('#google-sync, #enable-sync, #insert-unavailable-period')
                        .prop('disabled', true);
            } else {
                $('#google-sync, #enable-sync, #insert-unavailable-period')
                        .prop('disabled', false);
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
            var modalHandle = $('#manage-appointment');
            
            // :: APPLY APPOINTMENT DATA AND SHOW TO MODAL DIALOG
            modalHandle.find('input, textarea').val('');
            modalHandle.find('#appointment-id').val(appointmentData['id']);
            
            // Fill the services listbox and select the appointment service.
            $.each(GlobalVariables.availableServices, function(index, service) {
                var option = new Option(service['name'], service['id']);
                modalHandle.find('#select-service').append(option);
            });
            
            $('#manage-appointment #select-service').val(
                    appointmentData['id_services']);
            
            // Fill the providers listbox with providers that can serve the appointment's 
            // service and then select the user's provider.
            $.each(GlobalVariables.availableProviders, function(index, provider) {
                var canProvideService = false; 
                
                $.each(provider['services'], function(index, service) {
                    if (service === appointmentData['id_services']) {
                        canProvideService = true;
                        return;
                    }
                });
                
                if (canProvideService) {
                    var option = new Option(provider['first_name'] + ' ' 
                            + provider['last_name'], provider['id']);
                    modalHandle.find('#select-provider').append(option);
                }
            });
            
            modalHandle.find('#select-provider').val(appointmentData['id_users_provider']);
            
            // Set the start and end datetime of the appointment.\
            var startDatetime = Date.parseExact(appointmentData['start_datetime'],
                    'yyyy-MM-dd HH:mm:ss').toString('dd/MM/yyyy HH:mm');
            modalHandle.find('#start-datetime').datetimepicker({
                dateFormat  : 'dd/mm/yy',
                defaultValue: startDatetime
            });
            modalHandle.find('#start-datetime').val(startDatetime);
            
            var endDatetime = Date.parseExact(appointmentData['end_datetime'],
                    'yyyy-MM-dd HH:mm:ss').toString('dd/MM/yyyy HH:mm');
            modalHandle.find('#end-datetime').datetimepicker({
                dateFormat  : 'dd/mm/yy',
                defaultValue: endDatetime
            });
            modalHandle.find('#end-datetime').val(endDatetime);
            
            var customerData = appointmentData['customer'];
            modalHandle.find('#customer-id').val(appointmentData['id_users_customer']);
            modalHandle.find('#first-name').val(customerData['first_name']);
            modalHandle.find('#last-name').val(customerData['last_name']);
            modalHandle.find('#email').val(customerData['email']);
            modalHandle.find('#phone-number').val(customerData['phone_number']);
            modalHandle.find('#address').val(customerData['address']);
            modalHandle.find('#city').val(customerData['city']);
            modalHandle.find('#zip-code').val(customerData['zip_code']);
            modalHandle.find('#notes').val(appointmentData['notes']);
            
            // :: DISPLAY THE MANAGE APPOINTMENTS MODAL DIALOG
            $('#manage-appointment').modal('show');
        });
        
        /**
         * Event: Delete Popover Button "Click"
         * 
         * Displays a prompt on whether the user wants the appoinmtent to be
         * deleted. If he confirms the deletion then an ajax call is made to 
         * the server and deletes the appointment from the database.
         */
        $(document).on('click', '.delete-popover', function() {
            $(this).parents().eq(2).remove(); // Hide the popover
            
            var messageButtons = {
                'Delete'    : function() {
                    var postUrl = GlobalVariables.baseUrl + 'backend/ajax_delete_appointment';
                    
                    var postData = { 
                        'appointment_id' : BackendCalendar.lastFocusedEventData.data['id'] 
                    };
                    
                    $.post(postUrl, postData, function(response) {
                        /////////////////////////////////////////////////////////
                        console.log('Delete Appointment Response :', response);
                        /////////////////////////////////////////////////////////
                        
                        if (response.error) {
                            GeneralFunctions.displayMessageBox('Delete Appointment Error',
                                'An unexpected error occured during the deletion of the ' 
                                + 'appointment. Please try again.');
                            return;
                        }
                        
                        // Close dialog and refresh calendar events.
                        $('#message_box').dialog('close');
                        $('#select-filter-item').trigger('change');
                        
                    }, 'json');
                },
                'Cancel'    : function() {
                    $('#message_box').dialog('close');
                }
            };
            
            GeneralFunctions.displayMessageBox('Delete Appointment', 'Are you sure ' 
                    + 'that you want to delete this appointment? This action cannot ' 
                    + 'be undone.', messageButtons);
        });
        
        /**
         * Event: Manage Appointments Dialog Cancel Button "Click"
         * 
         * Closes the dialog without making any actions.
         */
        $('#manage-appointment #cancel-button').click(function() {
            $('#manage-appointment').modal('hide');
        });
        
        /**
         * Event: Manage Appointments Dialog Save Button "Click"
         * 
         * Stores the appointment changes.
         */
        $('#manage-appointment #save-button').click(function() {
            // :: PREPARE APPOINTMENT DATA FOR AJAX CALL
            var modalHandle = $('#manage-appointment');
            
            // Id must exist on the object in order for the model to update 
            // the record and not to perform an insert operation.
            
            var startDatetime = Date.parseExact(modalHandle.find('#start-datetime').val(),
                    'dd/MM/yyyy HH:mm').toString('yyyy-MM-dd HH:mm:ss');
            var endDatetime = Date.parseExact(modalHandle.find('#end-datetime').val(),
                    'dd/MM/yyyy HH:mm').toString('yyyy-MM-dd HH:mm:ss');
            
            var appointmentData = {
                'id'                 : modalHandle.find('#appointment-id').val(),
                'id_services'        : modalHandle.find('#select-service').val(),
                'id_users_provider'  : modalHandle.find('#select-provider').val(),
                'id_users_customer'  : modalHandle.find('#customer-id').val(),
                'start_datetime'     : startDatetime,
                'end_datetime'       : endDatetime,
                'notes'              : modalHandle.find('#notes').val()
            };
            
            var customerData = {
                'id'            : modalHandle.find('#customer-id').val(), 
                'first_name'    : modalHandle.find('#first-name').val(),
                'last_name'     : modalHandle.find('#last-name').val(),
                'email'         : modalHandle.find('#email').val(),
                'phone_number'  : modalHandle.find('#phone-number').val(),
                'address'       : modalHandle.find('#address').val(),
                'city'          : modalHandle.find('#city').val(),
                'zip_code'      : modalHandle.find('#zip-code').val()
            };
            
            // :: DEFINE SUCCESS EVENT CALLBACK
            var successCallback = function(response) {
                if (response.error) {
                    // There was something wrong within the ajax function.
                    modalHandle.find('.modal-header').append(
                        '<br><div class="alert alert-error">' + 
                            response.error +
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
            
            // :: DEFINE ERROR EVENT CALLBACK
            var errorCallback = function() {
                // Display error message to the user.
                modalHandle.find('.modal-header').append(
                        '<br><div class="alert alert-error">' + 
                            'A server communication error occured, please try again.' +
                        '</div>');
            };
            
            // :: CALL THE UPDATE APPOINTMENT METHOD
            BackendCalendar.updateAppointmentData(appointmentData, customerData, 
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
    },
            
    /**
     * This method calculates the proper calendar height, in order to be displayed
     * correctly, even when the browser window is resizing.
     * 
     * @return {int} Returns the calendar element height in pixels.
     */
    getCalendarHeight : function () {
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
    refreshCalendarAppointments : function(calendarHandle, recordId, filterType, 
            startDate, endDate) {
        var postUrl = GlobalVariables.baseUrl + 'backend/ajax_get_calendar_appointments';
            
        var postData = {
            record_id   : recordId,
            start_date  : startDate.toString('yyyy-MM-dd'),
            end_date    : endDate.toString('yyyy-MM-dd'),
            filter_type : filterType
        };

        $.post(postUrl, postData, function(response) {
            ////////////////////////////////////////////////////////////////////
            //console.log('Refresh Calendar Appointments Response :', response);
            ////////////////////////////////////////////////////////////////////
            
            // Add the appointments to the calendar.
            var calendarEvents = new Array();
            
            $.each(response, function(index, appointment){
                var event = {
                    id          : appointment['id'],
                    title       : appointment['service']['name'],
                    start       : appointment['start_datetime'],
                    end         : appointment['end_datetime'],
                    allDay      : false,
                    data        : appointment // For later use
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
    updateAppointmentData : function(appointmentData, customerData, 
            successCallback, errorCallback) {
        // :: MAKE AN AJAX CALL TO SERVER - STORE APPOINTMENT DATA
        var postUrl = GlobalVariables.baseUrl + 'backend/ajax_save_appointment_changes';
        
        var postData = {};
        postData['appointment_data'] = JSON.stringify(appointmentData);
        
        if (customerData !== undefined) {
            postData['customer_data'] = JSON.stringify(customerData);
        }
        
        $.ajax({
            type        : 'POST',
            url         : postUrl,
            data        : postData,
            dataType    : 'json',
            success     : function(response) {
                /////////////////////////////////////////////////////////////
                console.log('Update Appointment Data Response:', response);
                /////////////////////////////////////////////////////////////            
                
                if (successCallback !== undefined) {
                    successCallback(response);
                }
            },
            error       : function(jqXHR, textStatus, errorThrown) {
                //////////////////////////////////////////////////////////////////
                //console.log('Update Appointment Data Error:', jqXHR, textStatus, 
                //        errorThrown);
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
    calendarEventResize : function(event, dayDelta, minuteDelta, revertFunc, 
            jsEvent, ui, view) {
        
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
            if (response.error) {
                // Display error message to the user.
                Backend.displayNotification(reponse.error);
                return;
            }

            // Display success notification to user.
            var undoFunction = function() {
                appointmentData['end_datetime'] = Date.parseExact(
                        appointmentData['end_datetime'], 'yyyy-MM-dd HH:mm:ss')
                        .add({ minutes: -minuteDelta })
                        .toString('yyyy-MM-dd HH:mm:ss');
                
                var postUrl  = GlobalVariables.baseUrl 
                        + 'backend/ajax_save_appointment_changes';
                var postData = { 
                    'appointment_data' : JSON.stringify(appointmentData) 
                };

                $.post(postUrl, postData, function(response) {
                    $('#notification').hide('blind');
                    revertFunc();
                });
            };

            Backend.displayNotification('Appointment updated successfully!', [
                {
                    'label'     : 'Undo',
                    'function'  : undoFunction
                }
            ]);
            $('#footer').css('position', 'static'); // Footer position fix.
        };

        // :: UPDATE APPOINTMENT DATA VIA AJAX CALL
        BackendCalendar.updateAppointmentData(appointmentData, undefined, 
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
    calendarWindowResize : function(view) {
        $('#calendar').fullCalendar('option', 'height', 
                BackendCalendar.getCalendarHeight());
    },
    
    /**
     * Calendar Day "Click" Callback
     * 
     * When the user clicks on a day square on the calendar, then he will 
     * automatically be transfered to that day view calendar.
     */
    calendarDayClick : function(date, allDay, jsEvent, view) {
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
    calendarEventClick : function(event, jsEvent, view) {
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
                    + event.title 
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
            placement       : 'top',
            title           : event.title,
            content         : html,
            html            : true,
            container       : 'body',
            trigger         : 'manual'
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
        event.data['end_datetime']   = appointmentData['end_datetime'];
        
        // :: DEFINE THE SUCCESS CALLBACK FUNCTION
        var successCallback = function(response) {
            if (response.error) {
                // Display error message to the user.
                Backend.displayNotification(reponse.error);
                return;
            }

            // Display success notification to user.
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
                event.data['end_datetime']   = appointmentData['end_datetime'];
        
                var postUrl  = GlobalVariables.baseUrl 
                        + 'backend/ajax_save_appointment_changes';
                var postData = { 
                    'appointment_data' : JSON.stringify(appointmentData) 
                };

                $.post(postUrl, postData, function(response) {
                    $('#notification').hide('blind');
                    revertFunc();
                });
            };

            Backend.displayNotification('Appointment updated successfully!', [
                {
                    'label'     : 'Undo',
                    'function'  : undoFunction
                }
            ]);
            
            $('#footer').css('position', 'static'); // Footer position fix.
        };

        // :: UPDATE APPOINTMENT DATA VIA AJAX CALL
        BackendCalendar.updateAppointmentData(appointmentData, undefined, 
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
            
            if (response.error) {
                GeneralFunctions.displayMessageBox('Disable Sync Error', 'An unexpected ' +
                        'error occured during the disable provider sync operation : ' + 
                        response.error);
            }
            
        }, 'json');
    }
};