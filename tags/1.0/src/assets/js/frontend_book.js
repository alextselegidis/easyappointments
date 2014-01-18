/**
 * This namespace contains functions that implement the book appointment page 
 * functionality. Once the initialize() method is called the page is fully 
 * functional and can serve the appointment booking process.
 * 
 * @namespace FrontendBook
 */
var FrontendBook = {
    /**
     * Determines the functionality of the page.
     * 
     * @type {bool}
     */
    manageMode: false,  
    
    /**
     * This method initializes the book appointment page.
     * 
     * @param {bool} bindEventHandlers (OPTIONAL) Determines whether the default   
     * event handlers will be binded to the dom elements.
     * @param {bool} manageMode (OPTIONAL) Determines whether the customer is going 
     * to make  changes to an existing appointment rather than booking a new one.
     */
    initialize: function(bindEventHandlers, manageMode) {
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
            dateFormat: 'dd-mm-yy',
            firstDay: 1, // Monday
            minDate: 0,
            defaultDate: Date.today(),
           
            dayNames: [EALang['sunday'], EALang['monday'], EALang['tuesday'], EALang['wednesday'], EALang['thursday'], EALang['friday'], EALang['saturday']],
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
            
            onSelect: function(dateText, instance) {
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
    bindEventHandlers: function() {
        /**
         * Event: Selected Provider "Changed"
         * 
         * Whenever the provider changes the available appointment
         * date - time periods must be updated.
         */
        $('#select-provider').change(function() {
            FrontendBook.getAvailableHours(Date.today().toString('dd-MM-yyyy'));
            FrontendBook.updateConfirmFrame();
        });
        
        /**
         * Event: Selected Service "Changed"
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
                                + provider['first_name']  + ' ' + provider['last_name'] 
                                + '</option>';
                        $('#select-provider').append(optionHtml);
                    }
                });
            });

            FrontendBook.getAvailableHours($('#select-date').val());
            FrontendBook.updateConfirmFrame();
            FrontendBook.updateServiceDescription($('#select-service').val(), $('#service-description'));
        });
        
        /**
         * Event: Next Step Button "Clicked"
         * 
         * This handler is triggered every time the user pressed the 
         * "next" button on the book wizard. Some special tasks might 
         * be perfomed, depending the current wizard step.
         */
        $('.button-next').click(function() {
            // If we are on the 2nd tab then the user should have an appointment hour 
            // selected.
            if ($(this).attr('data-step_index') === '2') {
                if ($('.selected-hour').length == 0) {
                    if ($('#select-hour-prompt').length == 0) {
                        $('#available-hours').append('<br><br>'
                                + '<strong id="select-hour-prompt" class="text-error">'
                                + EALang['appointment_hour_missing'] 
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
         * Event: Back Step Button "Clicked"
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
         * Event: Available Hour "Click"
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
            $('#cancel-appointment').click(function(event) {
                var dialogButtons = {};
                dialogButtons['OK'] = function() {
                    if ($('#cancel-reason').val() === '') {
                        $('#cancel-reason').css('border', '2px solid red');
                        return;
                    }
                    $('#cancel-appointment-form textarea').val($('#cancel-reason').val());
                    $('#cancel-appointment-form').submit();
                };
                    
                dialogButtons[EALang['cancel']] = function() {
                    $('#message_box').dialog('close');
                };
                
                GeneralFunctions.displayMessageBox(EALang['cancel_appointment_title'], 
                        EALang['write_appointment_removal_reason'], dialogButtons);
                        
                $('#message_box').append('<textarea id="cancel-reason" rows="3"></textarea>');
                $('#cancel-reason').css('width', '353px');
                return false;
            });
        }
        
        /**
         * Event: Book Appointment Form "Submit"
         * 
         * Before the form is submitted to the server we need to make sure that
         * in the meantime the selected appointment date/time wasn't reserved by
         * another customer or event. 
         */
        $('#book-appointment-submit').click(function(event) {
            var formData = jQuery.parseJSON($('input[name="post_data"]').val());
            
            var postData = {
                'id_users_provider': formData['appointment']['id_users_provider'],
                'id_services': formData['appointment']['id_services'],
                'start_datetime': formData['appointment']['start_datetime'],
            };
            
            if (GlobalVariables.manageMode) {
                postData.exclude_appointment_id = GlobalVariables.appointmentData.id;
            }
            
            var postUrl = GlobalVariables.baseUrl + 'appointments/ajax_check_datetime_availability';
            
            $.post(postUrl, postData, function(response) {
                ////////////////////////////////////////////////////////////////////////
                console.log('Check Date/Time Availability Post Response :', response);
                ////////////////////////////////////////////////////////////////////////
                
                if (response.exceptions) {
                    response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
                    GeneralFunctions.displayMessageBox('Unexpected Issues', 'Unfortunately '
                            + 'the check appointment time availability could not be completed. '
                            + 'The following issues occurred:');
                    $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
                    return false;
                } 
                
                if (response === true) {
                    $('#book-appointment-form').submit();
                } else {
                    GeneralFunctions.displayMessageBox('Appointment Hour Taken', 'Unfortunately '
                            + 'the selected appointment hour is not available anymore. Please select '
                            + 'another hour.');
                    FrontendBook.getAvailableHours($('#select-date').val());
                }
            }, 'json');
        });
    },
    
    /**
     * This function makes an ajax call and returns the available 
     * hours for the selected service, provider and date.
     * 
     * @param {string} selDate The selected date of which the available
     * hours we need to receive.
     */
    getAvailableHours: function(selDate) {
        $('#available-hours').empty();
        
        // Find the selected service duration (it is going to 
        // be send within the "postData" object).
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
            'service_id': $('#select-service').val(),
            'provider_id': $('#select-provider').val(),
            'selected_date': selDate,
            'service_duration': selServiceDuration,
            'manage_mode': FrontendBook.manageMode,
            'appointment_id': appointmentId
        };

        // Make ajax post request and get the available hours.
        var ajaxurl = GlobalVariables.baseUrl + 'appointments/ajax_get_available_hours';
        jQuery.post(ajaxurl, postData, function(response) {
            ///////////////////////////////////////////////////////////////
            console.log('Get Available Hours JSON Response:', response);
            ///////////////////////////////////////////////////////////////
            
            if (!GeneralFunctions.handleAjaxExceptions(response)) return;

            // The response contains the available hours for the selected provider and
            // service. Fill the available hours div with response data. 
            if (response.length > 0) {
                var currColumn = 1;
                $('#available-hours').html('<div style="width:50px; float:left;"></div>');

                $.each(response, function(index, availableHour) {
                    if ((currColumn * 10) < (index + 1)) {
                        currColumn++;
                        $('#available-hours').append('<div style="width:50px; float:left;"></div>');
                    }

                    $('#available-hours div:eq(' + (currColumn - 1) + ')').append(
                            '<span class="available-hour">' + availableHour + '</span><br/>');
                });

                if (FrontendBook.manageMode) {
                    // Set the appointment's start time as the default selection.
                    $('.available-hour').removeClass('selected-hour');
                    $('.available-hour').filter(function() {
                        return $(this).text() === Date.parseExact(
                                GlobalVariables.appointmentData['start_datetime'],
                                'yyyy-MM-dd HH:mm:ss').toString('HH:mm');
                    }).addClass('selected-hour');
                } else {
                    // Set the first available hour as the default selection.
                    $('.available-hour:eq(0)').addClass('selected-hour');
                }

                FrontendBook.updateConfirmFrame();
                
            } else {
                $('#available-hours').text(EALang['no_available_hours']);
            }
        }, 'json');
    },

    /**
     * This function validates the customer's data input. The user cannot contiue
     * without passing all the validation checks.
     * 
     * @return {bool} Returns the validation result.
     */
    validateCustomerForm: function() {
        $('#wizard-frame-3 input').css('border', '');
        
        try {
            // Validate required fields.
            var missingRequiredField = false;
            $('.required').each(function() {
                if ($(this).val() == '') {
                    $(this).css('border', '2px solid red');
                    missingRequiredField = true;
                }
            });
            if (missingRequiredField) {
                throw EALang['fields_are_required'];
            }
            
            // Validate email address.
            if (!GeneralFunctions.validateEmail($('#email').val())) {
                $('#email').css('border', '2px solid red');
                throw EALang['invalid_email'];
            }
            
            return true;
        } catch(exc) {
            $('#form-message').text(exc);
            return false;
        }
    },

    /**
     * Every time this function is executed, it updates the confirmation
     * page with the latest customer settigns and input for the appointment 
     * booking.
     */
    updateConfirmFrame: function() {
        // Appointment Details
        var selectedDate = $('#select-date').datepicker('getDate');
        if (selectedDate !== null) {
            selectedDate = Date.parse(selectedDate).toString('dd/MM/yyyy');
        }

        var selServiceId = $('#select-service').val();
        var servicePrice, serviceCurrency;
        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service.id == selServiceId) {
                servicePrice = '<br>' + service.price;
                serviceCurrency = service.currency;
                return false; // break loop
            }
        });

        $('#appointment-details').html(
            '<h4>' + $('#select-service option:selected').text() + '</h4>' +  
            '<p>' 
        		+ '<strong class="text-info">' 
                    + $('#select-provider option:selected').text() + '<br>'
        			+ selectedDate + ' ' +  $('.selected-hour').text() 
                    + servicePrice + ' ' + serviceCurrency
    			+ '</strong>' + 
            '</p>'
        );

        // Customer Details
        $('#customer-details').html(
            '<h4>' + $('#first-name').val() + ' ' + $('#last-name').val() + '</h4>' + 
            '<p>' + 
            	EALang['phone'] + ': ' + $('#phone-number').val() + 
            	'<br/>' + 
            	EALang['email'] + ': ' + $('#email').val() + 
            	'<br/>' + 
            	EALang['address'] + ': ' + $('#address').val() + 
            	'<br/>' + 
            	EALang['city'] + ': ' + $('#city').val() + 
            	'<br/>' + 
            	EALang['zip_code'] + ': ' + $('#zip-code').val() + 
        	'</p>'
        );
            
        // Update appointment form data for submission to server when the user confirms
        // the appointment.
        var postData = new Object();
        
        postData['customer'] = {
            'last_name': $('#last-name').val(),
            'first_name': $('#first-name').val(),
            'email': $('#email').val(),
            'phone_number': $('#phone-number').val(),
            'address': $('#address').val(),
            'city': $('#city').val(),
            'zip_code': $('#zip-code').val()
        };
        
        postData['appointment'] = {
            'start_datetime': $('#select-date').datepicker('getDate').toString('yyyy-MM-dd') 
                                    + ' ' + $('.selected-hour').text() + ':00',
            'end_datetime': FrontendBook.calcEndDatetime(),
            'notes': $('#notes').val(),
            'is_unavailable': false,
            'id_users_provider': $('#select-provider').val(),
            'id_services': $('#select-service').val()
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
    calcEndDatetime: function() {
        // Find selected service duration. 
        var selServiceDuration = undefined;
        
        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service.id == $('#select-service').val()) {
                selServiceDuration = service.duration;
                return false; // Stop searching ... 
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
     * @param {object} appointment Selected appointment's data.
     * @param {object} provider Selected provider's data.
     * @param {object} customer Selected customer's data.
     * @returns {bool} Returns the operation result.
     */
    applyAppointmentData: function(appointment, provider, customer) {
        try {
            // Select Service & Provider
            $('#select-service').val(appointment['id_services']).trigger('change');
            $('#select-provider').val(appointment['id_users_provider']);
            
            // Set Appointment Date
            $('#select-date').datepicker('setDate', 
                    Date.parseExact(appointment['start_datetime'], 'yyyy-MM-dd HH:mm:ss'));
            FrontendBook.getAvailableHours($('#select-date').val());
            
            // Apply Customer's Data
            $('#last-name').val(customer['last_name']);
            $('#first-name').val(customer['first_name']);
            $('#email').val(customer['email']);
            $('#phone-number').val(customer['phone_number']);
            $('#address').val(customer['address']);
            $('#city').val(customer['city']);
            $('#zip-code').val(customer['zip_code']);
            var appointmentNotes = (appointment['notes'] !== null) 
                    ? appointment['notes'] : '';
            $('#notes').val(appointmentNotes);
            
            FrontendBook.updateConfirmFrame();
            
            return true;
        } catch(exc) {
            console.log(exc); // log exception
            return false;
        }
    },
    
    /**
     * This method updates a div's html content with a brief description of the 
     * user selected service (only if available in db). This is usefull for the 
     * customers upon selecting the correct service.
     * 
     * @param {int} serviceId The selected service record id.
     * @param {object} $div The destination div jquery object (e.g. provide $('#div-id') 
     * object as value).
     */
    updateServiceDescription: function(serviceId, $div) {
        var html = ''; 
        
        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service.id == serviceId) { // Just found the service.
                html = '<strong>' + service.name + ' </strong>';
                
                if (service.description != '' && service.description != null) {
                    html += '<br>' + service.description + '<br>';
                }
                
                if (service.duration != '' && service.duration != null) {
                    html += '[' + EALang['duration'] + ' ' + service.duration 
                            + ' ' + EALang['minutes'] + '] ';
                }
                
                if (service.price != '' && service.price != null) {
                    html += '[' + EALang['price'] + ' ' + service.price + ' ' + service.currency  + ']';
                }   
                
                html += '<br>';
                
                return false;
            }
        });
        
        $div.html(html);
        
        if (html != '') {
            $div.show();
        } else {
            $div.hide();
        }
        
    }
    
};