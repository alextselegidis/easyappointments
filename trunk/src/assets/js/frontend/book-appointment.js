/**
 * This class implements the book appointment page functionality. 
 * Once the initialize() method is called the page is fully functional 
 * and can serve the appointment booking process.
 * 
 * @class Implelements the js part of the appointment booking page.
 */
var bookAppointment = {
    /**
     * This method initializes the book appointment page.
     */
    initialize : function() {
        // Initialize page components.
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
            //dateFormat  : 'dd/mm/yy',
            minDate     : 0,
            defaultDate : Date.today(),
            onSelect    : function(dateText, inst) {
                bookAppointment.refreshAvailableHours(dateText);
                bookAppointment.updateConfirmData();
            }
        });
       
        // Bind event handlers.
        bookAppointment.bindEventHandlers();
       
        // Execute other necessary operations.
        $('#select-service').trigger('change');
    },
    
    /**
     * This method binds the necessary event handlers 
     * for the book appointments page.
     */
    bindEventHandlers : function() {
        /**
         * Event : Selected Provider "Changed"
         */
        $('#select-provider').change(function() {
            bookAppointment.refreshAvailableHours(Date.today().toString('MM/dd/yyyy'));
            bookAppointment.updateConfirmData();
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

            $.each(GlobalVariables.providers, function(indexProvider, provider) {
                $.each(provider['services'], function(indexService, serviceId) {
                    if (serviceId == currServiceId) { 
                        // This provider can provide the selected service.
                        // Add him to the list box.
                        var optionHtml = '<option value="' + provider['id'] + '">' + provider['last_name'] 
                            + ' ' + provider['first_name'] + '</option>';
                        $('#select-provider').append(optionHtml);
                    }
                });
            });

            bookAppointment.refreshAvailableHours(Date.today().toString('MM/dd/yyyy'));
            bookAppointment.updateConfirmData();
        });
        
        /**
         * Event : Next Step Button "Clicked"
         */
        $('.button-next').click(function() {
            // If we are on the 3rd tab then we will need to validate the user's 
            // input.
            if ($(this).attr('data-step_index') == 3) {
                if (!bookAppointment.validateCustomerDataForm()) {
                    return; // Do not continue.
                } else {
                    bookAppointment.updateConfirmData();
                }
            }

            var nextTabIndex = parseInt($(this).attr('data-step_index')) + 1;

            $(this).parents().eq(1).hide('fade', function() {    
                $('.active-step').removeClass('active-step');
                $('#step-' + nextTabIndex).addClass('active-step');
                $('#book-appointment-' + nextTabIndex).show('fade');
            });
        });

        /**
         * Event : Back Step Button "Clicked"
         */
        $('.button-back').click(function() {
            var prevTabIndex = parseInt($(this).attr('data-step_index')) - 1;

            $(this).parents().eq(1).hide('fade', function() {    
                $('.active-step').removeClass('active-step');
                $('#step-' + prevTabIndex).addClass('active-step');
                $('#book-appointment-' + prevTabIndex).show('fade');
            });
        });

        /**
         * Event : Available Hour "Click"
         */
        $('#available-hours').on('click', '.available-hour', function() {
            $('.selected-hour').removeClass('selected-hour');
            $(this).addClass('selected-hour');
            bookAppointment.updateConfirmData();
        });
    },
    
    /**
     * This function makes an ajax call and returns the available 
     * hours for the selected service, provider and date.
     * 
     * @param {string} selDate The selected date of which the available
     * hours we need to receive.
     */
    refreshAvailableHours : function(selDate) {
        // Fetch the available hours of the current date 
        // for the chosen service and provider.
        var selServiceDuration = 15; // Default duration.
        $.each(GlobalVariables.services, function(index, service) {
            if (service['id'] == $('#select-service').val()) {
                selServiceDuration = service['duration'];
            }
        })

        var postData = {
            'service_id'         : $('#select-service').val(),
            'provider_id'        : $('#select-provider').val(),
            'selected_date'      : selDate,
            'service_duration'   : selServiceDuration
        };

        // Make ajax post request and get the available hours.
        var ajaxurl = GlobalVariables.baseUrl + 'appointments/ajax_get_available_hours';
        jQuery.post(ajaxurl, postData, function(postResponse) {
            ////////////////////////////////////////////////////////////////////////////////
            console.log('\n\n Get Available Hours Post Response :', postResponse, '\n\n');
            ////////////////////////////////////////////////////////////////////////////////

            try {
                var jsonResponse = jQuery.parseJSON(postResponse);
                ////////////////////////////////////////////////////////////////////////////////
                console.log('\n\n Get Available Hours JSON Response :', jsonResponse, '\n\n');
                ////////////////////////////////////////////////////////////////////////////////

                // Fill the available time div
                var currColumn = 1;
                $('#available-hours').html('<div style="width:50px; float:left;"></div>');
                $.each(jsonResponse, function(index, availableHour) {
                    if ((currColumn * 10) < (index + 1)) {
                        currColumn++;
                        $('#available-hours').append('<div style="width:50px; float:left;"></div>');
                    }

                    $('#available-hours div:eq(' + (currColumn - 1) + ')')
                        .append('<span class="available-hour">' + availableHour + '</span><br/>');
                });

                // Set the first item as selected.
                $('.available-hour:eq(0)').addClass('selected-hour');
                bookAppointment.updateConfirmData();

            } catch(exception) {
                // @task Display message to the user.
            };
        });
    },

    /**
     * This function validates the customer's data input.
     * It only checks for empty fields by the time.
     * 
     * @return {bool} Returns the validation result.
     */
    validateCustomerDataForm : function() {
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
    updateConfirmData : function() {
        /*** SET APPOINTMENT INFO ***/
        var selectedDate = $('#select-date').datepicker('getDate');
        if (selectedDate != null) {
            selectedDate = Date.parse(selectedDate).toString('dd/MM/yyyy');
        }

        $('#appointment-info').html(
            '<h4>' + $('#select-service option:selected').text() + '</h4>' +
            $('#select-provider option:selected').text() + '<br/>' + 
            '<strong class="text-info">' + selectedDate + ' ' + $('.selected-hour').text() + '</strong>'
        );

        /*** SET CUSTOMER'S INFO ***/
        $('#customer-info').html(
            '<h4>' + $('#last-name').val() + ' ' + $('#first-name').val() + '</h4>' + 
            'Phone: ' + $('#phone-number').val() + '<br/>' + 
            'Email: ' + $('#email').val() + '<br/>' + 
            'Address: ' + $('#address').val() + '<br/>' + 
            'City: ' + $('#city').val() + '<br/>' + 
            'Zip Code: ' + $('#zip-code').val()
        );
            
        /*** UPDATE FORM POST DATA ***/
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
            'start_datetime'    : $('#select-date').datepicker('getDate').toString('yyyy-MM-dd') + ' ' + $('.selected-hour').text(),
            'end_datetime'      : bookAppointment.getEndDatetime(),
            'notes'             : $('#notes').val(),
            'id_users_provider' : $('#select-provider').val(),
            'id_services'       : $('#select-service').val()
        };
        
        $('input[name="post_data"]').val(JSON.stringify(postData));
    },
    
    /** 
     * This method calculates the end datetime of the current appointment. 
     * End datetime is depending on the service and start datetime fieldss.
     * 
     * @return {string} Returns the end datetime in string format.
     */
    getEndDatetime : function() {
        // Find selected service duration. 
        var selServiceDuration = undefined;
        
        $.each(GlobalVariables.services, function(index, service) {
            if (service.id == $('#select-service').val()) {
                selServiceDuration = service.duration;
                return; // Stop searching ... 
            }
        });
        
        // Add the duration to the start datetime.
        var startDatetime = $('#select-date').datepicker('getDate').toString('MM/dd/yyyy') + ' ' + $('.selected-hour').text();
        startDatetime = Date.parseExact(startDatetime, 'MM/dd/yyyy HH:mm');
        var endDatetime = undefined;
        
        if (selServiceDuration != undefined && startDatetime != null) {
            endDatetime = startDatetime.add({ minutstartDatetimees : parseInt(selServiceDuration) });
        } else {
            endDatetime = new Date();
        }
        
        return endDatetime.toString('yyyy-MM-dd HH:mm');
    }
}