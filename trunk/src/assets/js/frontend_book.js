/**
 * This namespace contains functions that implement the book appointment 
 * page functionality. Once the initialize() method is called the page is 
 * fully functional and can serve the appointment booking process.
 * 
 * @namespace FrontendBook
 */
var FrontendBook = {
    /**
     * Determines the functionality of the page.
     * 
     * @type Boolean
     */
    manageMode      : false,  
    
    /**
     * This method initializes the book appointment page.
     * 
     * @param {bool} bindEventHandlers (OPTIONAL) Determines whether 
     * the default event handlers will be binded to the dom elements.
     * @param {bool} manageMode (OPTIONAL) Determines whether the customer
     * is going to make changes to an existing appointment rather than
     * booking a new one.
     */
    initialize : function(bindEventHandlers, manageMode) {
        if (bindEventHandlers === undefined) {
            bindEventHandlers = true; // Default Value
        }
        
        if (manageMode === undefined) {
            manageMode = false; // Default Value
        }
        
        FrontendBook.manageMode = manageMode;
        
        // Initialize page's components (tooltips, datepickers etc).
        $('.book-step').qtip({
            position: {
                my: 'top center',
                at: 'bottom center'
            },
            style: {
                classes: 'qtip-green qtip-shadow custom-qtip'
            }
        });
        
        $('#select-date').datepicker({
            dateFormat  : 'dd-mm-yy',
            minDate     : 0,
            defaultDate : Date.today(),
            onSelect    : function(dateText, instance) {
                FrontendBook.getAvailableHours(dateText);
                FrontendBook.updateConfirmFrame();
            }
        });
       
        // Bind the event handlers (might not be necessary every time
        // we use this class).
        if (bindEventHandlers) {
            FrontendBook.bindEventHandlers();
        }
        
        // If the manage mode is true, the appointments data should be
        // loaded by default.
        if (FrontendBook.manageMode) {
            FrontendBook.applyAppointmentData(GlobalVariables.appointmentData,
                    GlobalVariables.providerData, GlobalVariables.customerData);
        } else {
            $('#select-service').trigger('change'); // Load the available hours.
        }
    },
    
    /**
     * This method binds the necessary event handlers for the book 
     * appointments page.
     */
    bindEventHandlers : function() {
        /**
         * Event : Selected Provider "Changed"
         * 
         * Whenever the provider changes the available appointment
         * date - time periods must be updated.
         */
        $('#select-provider').change(function() {
            FrontendBook.getAvailableHours(Date.today().toString('dd-MM-yyyy'));
            FrontendBook.updateConfirmFrame();
        });
        
        /**
         * Event : Selected Service "Changed"
         * 
         * When the user clicks on a service, its available providers should 
         * become visible. 
         */
        $('#select-service').change(function() {
            var currServiceId = $('#select-service').val();
            $('#select-provider').empty();

            $.each(GlobalVariables.availableProviders, function(indexProvider, provider) {
                $.each(provider['services'], function(indexService, serviceId) {
                    // If the current provider is able to provide the selected service,
                    // add him to the listbox. 
                    if (serviceId == currServiceId) { 
                        var optionHtml = '<option value="' + provider['id'] + '">' 
                                + provider['last_name']  + ' ' + provider['first_name'] 
                                + '</option>';
                        $('#select-provider').append(optionHtml);
                    }
                });
            });

            FrontendBook.getAvailableHours(Date.today().toString('dd-MM-yyyy'));
            FrontendBook.updateConfirmFrame();
        });
        
        /**
         * Event : Next Step Button "Clicked"
         * 
         * This handler is triggered every time the user pressed the 
         * "next" button on the book wizard. Some special tasks might 
         * be perfomed, depending the current wizard step.
         */
        $('.button-next').click(function() {
            // If we are on the 2nd tab then the user should have 
            // an appointment hour selected.
            if ($(this).attr('data-step_index') === '2') {
                if ($('.selected-hour').length == 0) {
                    if ($('#select-hour-prompt').length == 0) {
                        $('#available-hours').append('<br><br>'
                                + '<strong id="select-hour-prompt" class="text-error">'
                                + 'Please select an appointment hour before continuing!' 
                                + '</strong>');
                    }
                    return;
                }
            }
            
            // If we are on the 3rd tab then we will need to validate the user's 
            // input before proceeding to the next step.
            if ($(this).attr('data-step_index') === '3') {
                if (!FrontendBook.validateCustomerForm()) {
                    return; // Validation failed, do not continue.
                } else {
                    FrontendBook.updateConfirmFrame();
                }
            }
            
            // Display the next step tab (uses jquery animation effect).
            var nextTabIndex = parseInt($(this).attr('data-step_index')) + 1;

            $(this).parents().eq(1).hide('fade', function() {    
                $('.active-step').removeClass('active-step');
                $('#step-' + nextTabIndex).addClass('active-step');
                $('#wizard-frame-' + nextTabIndex).show('fade');
            });
        });

        /**
         * Event : Back Step Button "Clicked"
         * 
         * This handler is triggered every time the user pressed the 
         * "back" button on the book wizard.
         */
        $('.button-back').click(function() {
            var prevTabIndex = parseInt($(this).attr('data-step_index')) - 1;

            $(this).parents().eq(1).hide('fade', function() {    
                $('.active-step').removeClass('active-step');
                $('#step-' + prevTabIndex).addClass('active-step');
                $('#wizard-frame-' + prevTabIndex).show('fade');
            });
        });

        /**
         * Event : Available Hour "Click"
         * 
         * Triggered whenever the user clicks on an available hour
         * for his appointment.
         */
        $('#available-hours').on('click', '.available-hour', function() {
            $('.selected-hour').removeClass('selected-hour');
            $(this).addClass('selected-hour');
            FrontendBook.updateConfirmFrame();
        });
        
        if (FrontendBook.manageMode) {
            /**
             * Event: Cancel Appointment Button "Click"
             * 
             * When the user clicks the "Cancel" button this form is going to
             * be submitted. We need the user to confirm this action because
             * once the appointment is cancelled, it will be delete from the 
             * database.
             */
            $('#cancel-appointment').click(function() {
                event.preventDefault();
                
                var dialogButtons = {
                    'Yes'   : function() {
                        $('#cancel-appointment-form').submit();
                    },
                    'No'    : function() {
                        $('#message_box').dialog('close');
                    }
                };
                
                GeneralFunctions.displayMessageBox('Cancel Appointment', 'Are you sure ' 
                        + 'that you want to cancel this appointment? This action can\'t ' 
                        + 'be undone.', dialogButtons);
            });
        }
    },
    
    /**
     * This function makes an ajax call and returns the available 
     * hours for the selected service, provider and date.
     * 
     * @param {string} selDate The selected date of which the available
     * hours we need to receive.
     */
    getAvailableHours : function(selDate) {
        // Find the selected service duration (it is going to 
        // be send within the "postData" object.
        var selServiceDuration = 15; // Default value of duration (in minutes).
        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service['id'] == $('#select-service').val()) {
                selServiceDuration = service['duration']; 
            }
        });
        
        // If the manage mode is true then the appointment's start 
        // date should return as available too.
        var appointmentId = (FrontendBook.manageMode) 
                ? GlobalVariables.appointmentData['id'] : undefined;

        var postData = {
            'service_id'        : $('#select-service').val(),
            'provider_id'       : $('#select-provider').val(),
            'selected_date'     : selDate,
            'service_duration'  : selServiceDuration,
            'manage_mode'       : FrontendBook.manageMode,
            'appointment_id'    : appointmentId
        };

        // Make ajax post request and get the available hours.
        var ajaxurl = GlobalVariables.baseUrl + 'appointments/ajax_get_available_hours';
        jQuery.post(ajaxurl, postData, function(postResponse) {
            ////////////////////////////////////////////////////////////////////////////////
            //console.log('\n\n Get Available Hours Post Response :', postResponse, '\n\n');
            ////////////////////////////////////////////////////////////////////////////////

            try {
                var jsonResponse = jQuery.parseJSON(postResponse);
                ////////////////////////////////////////////////////////////////////////////////
                //console.log('\n\n Get Available Hours JSON Response :', jsonResponse, '\n\n');
                ////////////////////////////////////////////////////////////////////////////////

                if (jsonResponse.length > 0) {
                    // Fill the available time div
                    var currColumn = 1;
                    $('#available-hours').html('<div style="width:50px; float:left;"></div>');
                    
                    $.each(jsonResponse, function(index, availableHour) {
                        if ((currColumn * 10) < (index + 1)) {
                            currColumn++;
                            $('#available-hours')
                                    .append('<div style="width:50px; float:left;"></div>');
                        }

                        $('#available-hours div:eq(' + (currColumn - 1) + ')')
                                .append('<span class="available-hour">' + availableHour 
                                + '</span><br/>');
                    });

                    if (FrontendBook.manageMode) {
                        // Set the appointment start time as selected.
                        $('.available-hour').removeClass('selected-hour');
                        $('.available-hour').filter(function() {
                            return $(this).text() === Date.parseExact(
                                    GlobalVariables.appointmentData['start_datetime'],
                                    'yyyy-MM-dd HH:mm:ss').toString('HH:mm');
                        }).addClass('selected-hour');
                    } else {
                        // Set the first item as selected.
                        $('.available-hour:eq(0)').addClass('selected-hour');
                    }
                    
                    FrontendBook.updateConfirmFrame();
                } else {
                    $('#available-hours').text('There are no available appointment'
                            + 'hours for the selected date. Please choose another '
                            + 'date.');
                }
                
            } catch(exception) {
                GeneralFunctions.displayMessageBox('Unexpected Error', 'An unexpected '
                        + 'error occured during the available hours calculation. Please '
                        + 'refresh the page and try again.');
            }
        });
    },

    /**
     * This function validates the customer's data input.
     * It only checks for empty fields by the time.
     * 
     * @return {bool} Returns the validation result.
     */
    validateCustomerForm : function() {
        var validationResult = true;
        $('.required').css('border', '');

        $('.required').each(function() {
            if ($(this).val() == '') {
                validationResult = false; 
                $(this).css('border', '2px solid red');
            }
        });

        return validationResult;
    },

    /**
     * Every time this function is executed, it updates the confirmation
     * page with the latest customer settigns and input for the appointment 
     * booking.
     */
    updateConfirmFrame : function() {
        // :: UPDATE APPOINTMENT DETAILS DIV
        var selectedDate = $('#select-date').datepicker('getDate');
        if (selectedDate !== null) {
            selectedDate = Date.parse(selectedDate).toString('dd/MM/yyyy');
        }

        $('#appointment-details').html(
            '<h3>' + $('#select-service option:selected').text() + '</h3>' +  
            '<p>' 
        		+ $('#select-provider option:selected').text()
        		+ '<br/>'
        		+ '<strong class="text-info">' 
        			+ selectedDate + ' ' +  $('.selected-hour').text() 
    			+ '</strong>' + 
            '</p>'
        );

        // :: UPDATE CUSTOMER'S DETAILS DIV
        $('#customer-details').html(
            '<h3>' + $('#first-name').val() + ' ' + $('#last-name').val() + '</h3>' + 
            '<p>' + 
            	'Phone: ' + $('#phone-number').val() + 
            	'<br/>' + 
            	'Email: ' + $('#email').val() + 
            	'<br/>' + 
            	'Address: ' + $('#address').val() + 
            	'<br/>' + 
            	'City: ' + $('#city').val() + 
            	'<br/>' + 
            	'Zip Code: ' + $('#zip-code').val() + 
        	'</p>'
        );
            
        // :: UPDATE APPOINTMENT DATA FORM 
        var postData = new Object();
        
        postData['customer'] = {
            'last_name'      : $('#last-name').val(),
            'first_name'     : $('#first-name').val(),
            'email'          : $('#email').val(),
            'phone_number'   : $('#phone-number').val(),
            'address'        : $('#address').val(),
            'city'           : $('#city').val(),
            'zip_code'       : $('#zip-code').val()
        };
        
        postData['appointment'] = {
            'start_datetime'    : $('#select-date').datepicker('getDate').toString('yyyy-MM-dd') 
                                        + ' ' + $('.selected-hour').text() + ':00',
            'end_datetime'      : FrontendBook.calcEndDatetime(),
            'notes'             : $('#notes').val(),
            'id_users_provider' : $('#select-provider').val(),
            'id_services'       : $('#select-service').val()
        };
        
        postData['manage_mode'] = FrontendBook.manageMode;
        
        if (FrontendBook.manageMode) {
            postData['appointment']['id'] = GlobalVariables.appointmentData['id'];
            postData['customer']['id'] = GlobalVariables.customerData['id'];
        }
        
        $('input[name="post_data"]').val(JSON.stringify(postData));
    },
    
    /** 
     * This method calculates the end datetime of the current appointment. 
     * End datetime is depending on the service and start datetime fieldss.
     * 
     * @return {string} Returns the end datetime in string format.
     */
    calcEndDatetime : function() {
        // Find selected service duration. 
        var selServiceDuration = undefined;
        
        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service.id == $('#select-service').val()) {
                selServiceDuration = service.duration;
                return; // Stop searching ... 
            }
        });
        
        // Add the duration to the start datetime.
        var startDatetime = $('#select-date').datepicker('getDate').toString('dd-MM-yyyy') 
                + ' ' + $('.selected-hour').text();
        startDatetime = Date.parseExact(startDatetime, 'dd-MM-yyyy HH:mm');
        var endDatetime = undefined;
        
        if (selServiceDuration !== undefined && startDatetime !== null) {
            endDatetime = startDatetime.add({ 'minutes' : parseInt(selServiceDuration) });
        } else {
            endDatetime = new Date();
        }
        
        return endDatetime.toString('yyyy-MM-dd HH:mm:ss');
    },
    
    /**
     * This method applies the appointment's data to the wizard so 
     * that the user can start making changes on an existing record.
     * 
     * @param {object} appointmentData Selected appointment's data.
     * @param {object} providerData Selected provider's data.
     * @param {object} customerData Selected customer's data.
     * @returns {bool} Returns the operation result.
     */
    applyAppointmentData : function(appointmentData, providerData, customerData) {
        try {
            // Select Service & Provider
            $('#select-service').val(appointmentData['id_services']).trigger('change');
            $('#select-provider').val(appointmentData['id_users_provider']);
            
            // Set Appointment Date
            $('#select-date').datepicker('setDate', Date.parseExact(
                    appointmentData['start_datetime'], 'yyyy-MM-dd HH:mm:ss'));
            FrontendBook.getAvailableHours($('#select-date').val());
            
            // Apply Customer's Data
            $('#last-name').val(customerData['last_name']);
            $('#first-name').val(customerData['first_name']);
            $('#email').val(customerData['email']);
            $('#phone-number').val(customerData['phone_number']);
            
            $('#address').val(customerData['address']);
            $('#city').val(customerData['city']);
            $('#zip-code').val(customerData['zip_code']);
            var appointmentNotes = (appointmentData['notes'] !== null) ? appointmentData['notes'] : '';
            $('#notes').val(appointmentNotes);
            
            FrontendBook.updateConfirmFrame();
            
            return true;
        } catch(exc) {
            console.log(exc);
            return false;
        }
    }
}