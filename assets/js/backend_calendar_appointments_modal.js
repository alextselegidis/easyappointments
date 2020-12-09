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
 * Backend Calendar Appointments Modal
 *
 * This module implements the appointments modal functionality.
 *
 * @module BackendCalendarAppointmentsModal
 */
window.BackendCalendarAppointmentsModal = window.BackendCalendarAppointmentsModal || {};

(function (exports) {

    'use strict';

    function updateTimezone() {
        var providerId = $('#select-provider').val();

        var provider = GlobalVariables.availableProviders.find(function (availableProvider) {
            return Number(availableProvider.id) === Number(providerId);
        });

        if (provider && provider.timezone) {
            $('.provider-timezone').text(GlobalVariables.timezones[provider.timezone]);
        }
    }

    function bindEventHandlers() {
        /**
         * Event: Manage Appointments Dialog Save Button "Click"
         *
         * Stores the appointment changes or inserts a new appointment depending the dialog mode.
         */
        $('#manage-appointment #save-appointment').on('click', function () {
            // Before doing anything the appointment data need to be validated.
            if (!validateAppointmentForm()) {
                return;
            }

            // Prepare appointment data for AJAX request.
            var $dialog = $('#manage-appointment');

            // ID must exist on the object in order for the model to update the record and not to perform
            // an insert operation.

            var startDatetime = $dialog.find('#start-datetime').datetimepicker('getDate').toString('yyyy-MM-dd HH:mm:ss');
            var endDatetime = $dialog.find('#end-datetime').datetimepicker('getDate').toString('yyyy-MM-dd HH:mm:ss');

            var appointment = {
                id_services: $dialog.find('#select-service').val(),
                id_users_provider: $dialog.find('#select-provider').val(),
                start_datetime: startDatetime,
                end_datetime: endDatetime,
                location: $dialog.find('#appointment-location').val(),
                notes: $dialog.find('#appointment-notes').val(),
                is_unavailable: false
            };

            if ($dialog.find('#appointment-id').val() !== '') {
                // Set the id value, only if we are editing an appointment.
                appointment.id = $dialog.find('#appointment-id').val();
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
                customer.id = $dialog.find('#customer-id').val();
                appointment.id_users_customer = customer.id;
            }

            // Define success callback.
            var successCallback = function (response) {
                // Display success message to the user.
                Backend.displayNotification(EALang.appointment_saved);

                // Close the modal dialog and refresh the calendar appointments.
                $dialog.find('.alert').addClass('d-none');
                $dialog.modal('hide');
                $('#select-filter-item').trigger('change');
            };

            // Define error callback.
            var errorCallback = function () {
                $dialog.find('.modal-message').text(EALang.service_communication_error);
                $dialog.find('.modal-message').addClass('alert-danger').removeClass('d-none');
                $dialog.find('.modal-body').scrollTop(0);
            };

            // Save appointment data.
            BackendCalendarApi.saveAppointment(appointment, customer, successCallback, errorCallback);
        });

        /**
         * Event: Insert Appointment Button "Click"
         *
         * When the user presses this button, the manage appointment dialog opens and lets the user to
         * create a new appointment.
         */
        $('#insert-appointment').on('click', function () {
            $('.popover').remove();

            BackendCalendarAppointmentsModal.resetAppointmentDialog();
            var $dialog = $('#manage-appointment');

            // Set the selected filter item and find the next appointment time as the default modal values.
            if ($('#select-filter-item option:selected').attr('type') === 'provider') {
                var providerId = $('#select-filter-item').val();

                var providers = GlobalVariables.availableProviders.filter(function (provider) {
                    return Number(provider.id) === Number(providerId);
                });

                if (providers.length) {
                    $dialog.find('#select-service').val(providers[0].services[0]).trigger('change');
                    $dialog.find('#select-provider').val(providerId);
                }
            } else if ($('#select-filter-item option:selected').attr('type') === 'service') {
                $dialog.find('#select-service option[value="' + $('#select-filter-item').val() + '"]')
                    .prop('selected', true);
            } else {
                $dialog.find('#select-service option:first')
                    .prop('selected', true)
                    .trigger('change');
            }

            var serviceId = $dialog.find('#select-service').val();

            var service = GlobalVariables.availableServices.find(function (availableService) {
                return Number(availableService.id) === Number(serviceId);
            });

            var duration = service ? service.duration : 60;

            var start = new Date();
            var currentMin = parseInt(start.toString('mm'));

            if (currentMin > 0 && currentMin < 15) {
                start.set({'minute': 15});
            } else if (currentMin > 15 && currentMin < 30) {
                start.set({'minute': 30});
            } else if (currentMin > 30 && currentMin < 45) {
                start.set({'minute': 45});
            } else {
                start.addHours(1).set({'minute': 0});
            }

            $dialog.find('#start-datetime').val(GeneralFunctions.formatDate(start, GlobalVariables.dateFormat, true));
            $dialog.find('#end-datetime').val(GeneralFunctions.formatDate(start.addMinutes(duration),
                GlobalVariables.dateFormat, true));

            // Display modal form.
            $dialog.find('.modal-header h3').text(EALang.new_appointment_title);
            $dialog.modal('show');
        });

        /**
         * Event: Pick Existing Customer Button "Click"
         */
        $('#select-customer').on('click', function () {
            var $list = $('#existing-customers-list');

            if (!$list.is(':visible')) {
                $(this).find('span').text(EALang.hide);
                $list.empty();
                $list.slideDown('slow');
                $('#filter-existing-customers').fadeIn('slow');
                $('#filter-existing-customers').val('');
                GlobalVariables.customers.forEach(function (customer) {
                    $('<div/>', {
                        'data-id': customer.id,
                        'text': customer.first_name + ' ' + customer.last_name
                    })
                        .appendTo($list);
                });
            } else {
                $list.slideUp('slow');
                $('#filter-existing-customers').fadeOut('slow');
                $(this).find('span').text(EALang.select);
            }
        });

        /**
         * Event: Select Existing Customer From List "Click"
         */
        $('#manage-appointment').on('click', '#existing-customers-list div', function () {
            var customerId = $(this).attr('data-id');

            var customer = GlobalVariables.customers.find(function (customer) {
                return Number(customer.id) === Number(customerId);
            });

            if (customer) {
                $('#customer-id').val(customer.id);
                $('#first-name').val(customer.first_name);
                $('#last-name').val(customer.last_name);
                $('#email').val(customer.email);
                $('#phone-number').val(customer.phone_number);
                $('#address').val(customer.address);
                $('#city').val(customer.city);
                $('#zip-code').val(customer.zip_code);
                $('#customer-notes').val(customer.notes);
            }

            $('#select-customer').trigger('click'); // Hide the list.
        });

        var filterExistingCustomersTimeout = null;

        /**
         * Event: Filter Existing Customers "Change"
         */
        $('#filter-existing-customers').on('keyup', function () {
            if (filterExistingCustomersTimeout) {
                clearTimeout(filterExistingCustomersTimeout);
            }

            var key = $(this).val().toLowerCase();

            filterExistingCustomersTimeout = setTimeout(function() {
                var $list = $('#existing-customers-list');

                var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_customers';

                var data = {
                    csrfToken: GlobalVariables.csrfToken,
                    key: key
                };

                $('#loading').css('visibility', 'hidden');

                // Try to get the updated customer list.
                $.post(url, data)
                    .done(function (response) {
                        $list.empty();

                        response.forEach(function (customer) {
                            $('<div/>', {
                                'data-id': customer.id,
                                'text': customer.first_name + ' ' + customer.last_name
                            })
                                .appendTo($list);

                            // Verify if this customer is on the old customer list.
                            var result = GlobalVariables.customers.filter(function (globalVariablesCustomer) {
                                return Number(globalVariablesCustomer.id) === Number(customer.id);
                            });

                            // Add it to the customer list.
                            if (!result.length) {
                                GlobalVariables.customers.push(customer);
                            }
                        })
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        // If there is any error on the request, search by the local client database.
                        $list.empty();

                        GlobalVariables.customers.forEach(function (customer, index) {
                            if (customer.first_name.toLowerCase().indexOf(key) !== -1
                                || customer.last_name.toLowerCase().indexOf(key) !== -1
                                || customer.email.toLowerCase().indexOf(key) !== -1
                                || customer.phone_number.toLowerCase().indexOf(key) !== -1
                                || customer.address.toLowerCase().indexOf(key) !== -1
                                || customer.city.toLowerCase().indexOf(key) !== -1
                                || customer.zip_code.toLowerCase().indexOf(key) !== -1
                                || customer.notes.toLowerCase().indexOf(key) !== -1) {
                                $('<div/>', {
                                    'data-id': customer.id,
                                    'text': customer.first_name + ' ' + customer.last_name
                                })
                                    .appendTo($list);
                            }
                        });
                    })
                    .always(function() {
                        $('#loading').css('visibility', '');
                    });
            }, 1000);
        });

        /**
         * Event: Selected Service "Change"
         *
         * When the user clicks on a service, its available providers should become visible. Also we need to
         * update the start and end time of the appointment.
         */
        $('#select-service').on('change', function () {
            var serviceId = $('#select-service').val();

            $('#select-provider').empty();

            // Automatically update the service duration.
            var service = GlobalVariables.availableServices.find(function (availableService) {
                return Number(availableService.id) === Number(serviceId);
            });

            var duration = service ? service.duration : 60;

            var start = $('#start-datetime').datetimepicker('getDate');
            $('#end-datetime').datetimepicker('setDate', new Date(start.getTime() + duration * 60000));

            // Update the providers select box.

            GlobalVariables.availableProviders.forEach(function (provider) {
                provider.services.forEach(function (providerServiceId) {
                    if (GlobalVariables.user.role_slug === Backend.DB_SLUG_PROVIDER && Number(provider.id) !== GlobalVariables.user.id) {
                        return; // continue
                    }

                    if (GlobalVariables.user.role_slug === Backend.DB_SLUG_SECRETARY && GlobalVariables.secretaryProviders.indexOf(provider.id) === -1) {
                        return; // continue
                    }

                    // If the current provider is able to provide the selected service, add him to the listbox.
                    if (Number(providerServiceId) === Number(serviceId)) {
                        $('#select-provider')
                            .append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
                    }
                });
            });
        });

        /**
         * Event: Provider "Change"
         */
        $('#select-provider').on('change', function () {
            updateTimezone();
        });

        /**
         * Event: Enter New Customer Button "Click"
         */
        $('#new-customer').on('click', function () {
            $('#manage-appointment').find('#customer-id, #first-name, #last-name, #email, '
                + '#phone-number, #address, #city, #zip-code, #customer-notes').val('');
        });
    }

    /**
     * Reset Appointment Dialog
     *
     * This method resets the manage appointment dialog modal to its initial state. After that you can make
     * any modification might be necessary in order to bring the dialog to the desired state.
     */
    exports.resetAppointmentDialog = function () {
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
        GlobalVariables.availableProviders.forEach(function (provider, index) {
            var canProvideService = false;

            var serviceId = $dialog.find('#select-service').val();

            var canProvideService = provider.services.filter(function (providerServiceId) {
                return Number(providerServiceId) === Number(serviceId)
            }).length > 0;

            if (canProvideService) { // Add the provider to the listbox.
                $dialog.find('#select-provider')
                    .append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
            }
        });

        // Close existing customers-filter frame.
        $('#existing-customers-list').slideUp('slow');
        $('#filter-existing-customers').fadeOut('slow');
        $('#select-customer span').text(EALang.select);

        // Setup start and datetimepickers.
        // Get the selected service duration. It will be needed in order to calculate the appointment end datetime.
        var serviceId = $dialog.find('#select-service').val();

        var service = GlobalVariables.availableServices.forEach(function (service) {
            return Number(service.id) === Number(serviceId);
        });

        var duration = service ? service.duration : 0;

        var startDatetime = new Date();
        var endDatetime = new Date().addMinutes(duration);
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
                throw new Error('Invalid GlobalVariables.dateFormat value.');
        }

        var firstWeekDay = GlobalVariables.firstWeekday;
        var firstWeekDayNumber = GeneralFunctions.getWeekDayId(firstWeekDay);

        $dialog.find('#start-datetime').datetimepicker({
            dateFormat: dateFormat,
            timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm TT' : 'HH:mm',

            // Translation
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
            monthNames: [EALang.january, EALang.february, EALang.march, EALang.april,
                EALang.may, EALang.june, EALang.july, EALang.august, EALang.september,
                EALang.october, EALang.november, EALang.december],
            prevText: EALang.previous,
            nextText: EALang.next,
            currentText: EALang.now,
            closeText: EALang.close,
            timeOnlyTitle: EALang.select_time,
            timeText: EALang.time,
            hourText: EALang.hour,
            minuteText: EALang.minutes,
            firstDay: firstWeekDayNumber,
            onClose: function () {
                var serviceId = $('#select-service').val();

                // Automatically update the #end-datetime DateTimePicker based on service duration.
                var service = GlobalVariables.availableServices.find(function (availableService) {
                    return Number(availableService.id) === Number(serviceId);
                });

                var start = $('#start-datetime').datetimepicker('getDate');
                $('#end-datetime').datetimepicker('setDate', new Date(start.getTime() + service.duration * 60000));
            }
        });
        $dialog.find('#start-datetime').datetimepicker('setDate', startDatetime);

        $dialog.find('#end-datetime').datetimepicker({
            dateFormat: dateFormat,
            timeFormat: GlobalVariables.timeFormat === 'regular' ? 'h:mm TT' : 'HH:mm',

            // Translation
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
            monthNames: [EALang.january, EALang.february, EALang.march, EALang.april,
                EALang.may, EALang.june, EALang.july, EALang.august, EALang.september,
                EALang.october, EALang.november, EALang.december],
            prevText: EALang.previous,
            nextText: EALang.next,
            currentText: EALang.now,
            closeText: EALang.close,
            timeOnlyTitle: EALang.select_time,
            timeText: EALang.time,
            hourText: EALang.hour,
            minuteText: EALang.minutes,
            firstDay: firstWeekDayNumber
        });
        $dialog.find('#end-datetime').datetimepicker('setDate', endDatetime);
    };

    /**
     * Validate the manage appointment dialog data. Validation checks need to
     * run every time the data are going to be saved.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validateAppointmentForm() {
        var $dialog = $('#manage-appointment');

        // Reset previous validation css formatting.
        $dialog.find('.has-error').removeClass('has-error');
        $dialog.find('.modal-message').addClass('d-none');

        try {
            // Check required fields.
            var missingRequiredField = false;

            $dialog.find('.required').each(function (index, requiredField) {
                if ($(requiredField).val() === '' || $(requiredField).val() === null) {
                    $(requiredField).closest('.form-group').addClass('has-error');
                    missingRequiredField = true;
                }
            });

            if (missingRequiredField) {
                throw new Error(EALang.fields_are_required);
            }

            // Check email address.
            if (!GeneralFunctions.validateEmail($dialog.find('#email').val())) {
                $dialog.find('#email').closest('.form-group').addClass('has-error');
                throw new Error(EALang.invalid_email);
            }

            // Check appointment start and end time.
            var start = $('#start-datetime').datetimepicker('getDate');
            var end = $('#end-datetime').datetimepicker('getDate');
            if (start > end) {
                $dialog.find('#start-datetime, #end-datetime').closest('.form-group').addClass('has-error');
                throw new Error(EALang.start_date_before_end_error);
            }

            return true;
        } catch (error) {
            $dialog.find('.modal-message').addClass('alert-danger').text(error.message).removeClass('d-none');
            return false;
        }
    }

    exports.initialize = function () {
        bindEventHandlers();
    };

})(window.BackendCalendarAppointmentsModal);
