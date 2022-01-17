/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

/**
 * Appointments modal component.
 *
 * This module implements the appointments modal functionality.
 *
 * Old Name: BackendCalendarAppointmentsModal
 */
App.Components.AppointmentsModal = (function () {
    const $appointmentsModal = $('#appointments-modal');
    const $startDatetime = $('#start-datetime');
    const $endDatetime = $('#end-datetime');
    const $filterExistingCustomers = $('#filter-existing-customers');
    const $customerId = $('#customer-id');
    const $firstName = $('#first-name');
    const $lastName = $('#last-name');
    const $email = $('#email');
    const $phoneNumber = $('#phone-number');
    const $address = $('#address');
    const $city = $('#city');
    const $zipCode = $('#zip-code');
    const $customerNotes = $('#customer-notes');
    const $selectCustomer = $('#select-customer');
    const $saveAppointment = $('#save-appointment');
    const $appointmentId = $('#appointment-id');
    const $appointmentLocation = $('#appointment-location');
    const $appointmentNotes = $('#appointment-notes');
    const $selectFilterItem = $('#select-filter-item');
    const $selectService = $('#select-service');
    const $selectProvider = $('#select-provider');
    const $insertAppointment = $('#insert-appointment');
    const $existingCustomersList = $('#existing-customers-list');
    const $newCustomer = $('#new-customer');

    /**
     * Update the displayed timezone.
     */
    function updateTimezone() {
        const providerId = $selectProvider.val();

        const provider = App.Vars.available_providers.find(function (availableProvider) {
            return Number(availableProvider.id) === Number(providerId);
        });

        if (provider && provider.timezone) {
            $('.provider-timezone').text(App.Vars.timezones[provider.timezone]);
        }
    }

    /**
     * Add the component event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Manage Appointments Dialog Save Button "Click"
         *
         * Stores the appointment changes or inserts a new appointment depending on the dialog mode.
         */
        $saveAppointment.on('click', () => {
            // Before doing anything the appointment data need to be validated.
            if (!validateAppointmentForm()) {
                return;
            }

            // ID must exist on the object in order for the model to update the record and not to perform
            // an insert operation.

            const startDatetime = moment($startDatetime.datetimepicker('getDate')).format('YYYY-MM-DD HH:mm:ss');
            const endDatetime = moment($endDatetime.datetimepicker('getDate')).format('YYYY-MM-DD HH:mm:ss');

            const appointment = {
                id_services: $selectService.val(),
                id_users_provider: $selectProvider.val(),
                start_datetime: startDatetime,
                end_datetime: endDatetime,
                location: $appointmentLocation.val(),
                notes: $appointmentNotes.val(),
                is_unavailable: Number(false)
            };

            if ($appointmentId.val() !== '') {
                // Set the id value, only if we are editing an appointment.
                appointment.id = $appointmentId.val();
            }

            const customer = {
                first_name: $firstName.val(),
                last_name: $lastName.val(),
                email: $email.val(),
                phone_number: $phoneNumber.val(),
                address: $address.val(),
                city: $city.val(),
                zip_code: $zipCode.val(),
                notes: $customerNotes.val()
            };

            if ($customerId.val() !== '') {
                // Set the id value, only if we are editing an appointment.
                customer.id = $customerId.val();
                appointment.id_users_customer = customer.id;
            }

            // Define success callback.
            const successCallback = () => {
                // Display success message to the user.
                Backend.displayNotification(App.Lang.appointment_saved);

                // Close the modal dialog and refresh the calendar appointments.
                $appointmentsModal.find('.alert').addClass('d-none').modal('hide');
                $selectFilterItem.trigger('change');
            };

            // Define error callback.
            const errorCallback = () => {
                $appointmentsModal.find('.modal-message').text(App.Lang.service_communication_error);
                $appointmentsModal.find('.modal-message').addClass('alert-danger').removeClass('d-none');
                $appointmentsModal.find('.modal-body').scrollTop(0);
            };

            // Save appointment data.
            App.Http.Calendar.saveAppointment(appointment, customer, successCallback, errorCallback);
        });

        /**
         * Event: Insert Appointment Button "Click"
         *
         * When the user presses this button, the manage appointment dialog opens and lets the user create a new
         * appointment.
         */
        $insertAppointment.on('click', () => {
            $('.popover').remove();

            resetModal();

            // Set the selected filter item and find the next appointment time as the default modal values.
            if ($$selectFilterItem.find('option:selected').attr('type') === 'provider') {
                const providerId = $('#select-filter-item').val();

                const providers = App.Vars.available_providers.filter(
                    (provider) => Number(provider.id) === Number(providerId)
                );

                if (providers.length) {
                    $selectService.val(providers[0].services[0]).trigger('change');
                    $selectProvider.val(providerId);
                }
            } else if ($selectFilterItem.find('option:selected').attr('type') === 'service') {
                $selectService.find('option[value="' + $selectFilterItem.val() + '"]').prop('selected', true);
            } else {
                $selectService.find('option:first').prop('selected', true).trigger('change');
            }

            const serviceId = $selectService.val();

            const service = App.Vars.available_services.find(
                (availableService) => Number(availableService.id) === Number(serviceId)
            );

            const duration = service ? service.duration : 60;

            const startMoment = moment();

            const currentMin = parseInt(startMoment.format('mm'));

            if (currentMin > 0 && currentMin < 15) {
                startMoment.set({minutes: 15});
            } else if (currentMin > 15 && currentMin < 30) {
                startMoment.set({minutes: 30});
            } else if (currentMin > 30 && currentMin < 45) {
                startMoment.set({minutes: 45});
            } else {
                startMoment.add(1, 'hour').set({minutes: 0});
            }

            $startDatetime.val(
                App.Utils.Date.format(startMoment.toDate(), App.Vars.date_format, App.Vars.time_format, true)
            );

            $endDatetime.val(
                App.Utils.Date.format(
                    startMoment.add(duration, 'minutes').toDate(),
                    App.Vars.date_format,
                    App.Vars.time_format,
                    true
                )
            );

            // Display modal form.
            $appointmentsModal.find('.modal-header h3').text(App.Lang.new_appointment_title);

            $appointmentsModal.modal('show');
        });

        /**
         * Event: Pick Existing Customer Button "Click"
         *
         * @param {jQuery.Event}
         */
        $selectCustomer.on('click', (event) => {
            if (!$existingCustomersList.is(':visible')) {
                $(event.target).find('span').text(App.Lang.hide);
                $existingCustomersList.empty();
                $existingCustomersList.slideDown('slow');
                $filterExistingCustomers.fadeIn('slow').val('');
                App.Vars.customers.forEach(function (customer) {
                    $('<div/>', {
                        'data-id': customer.id,
                        'text': customer.first_name + ' ' + customer.last_name
                    }).appendTo($existingCustomersList);
                });
            } else {
                $existingCustomersList.slideUp('slow');
                $filterExistingCustomers.fadeOut('slow');
                $(event.target).find('span').text(App.Lang.select);
            }
        });

        /**
         * Event: Select Existing Customer From List "Click"
         *
         * @param {jQuery.Event}
         */
        $appointmentsModal.on('click', '#existing-customers-list div', (event) => {
            const customerId = $(event.target).attr('data-id');

            const customer = App.Vars.customers.find(function (customer) {
                return Number(customer.id) === Number(customerId);
            });

            if (customer) {
                $customerId.val(customer.id);
                $firstName.val(customer.first_name);
                $lastName.val(customer.last_name);
                $email.val(customer.email);
                $phoneNumber.val(customer.phone_number);
                $address.val(customer.address);
                $city.val(customer.city);
                $zipCode.val(customer.zip_code);
                $customerNotes.val(customer.notes);
            }

            $selectCustomer.trigger('click'); // Hide the list.
        });

        let filterExistingCustomersTimeout = null;

        /**
         * Event: Filter Existing Customers "Change"
         *
         * @param {jQuery.Event}
         */
        $filterExistingCustomers.on('keyup', (event) => {
            if (filterExistingCustomersTimeout) {
                clearTimeout(filterExistingCustomersTimeout);
            }

            const keyword = $(event.target).val().toLowerCase();

            filterExistingCustomersTimeout = setTimeout(function () {
                $('#loading').css('visibility', 'hidden');

                App.Http.Customers.search(keyword, 50)
                    .done((response) => {
                        $existingCustomersList.empty();

                        response.forEach((customer) => {
                            $('<div/>', {
                                'data-id': customer.id,
                                'text': customer.first_name + ' ' + customer.last_name
                            }).appendTo($existingCustomersList);

                            // Verify if this customer is on the old customer list.
                            const result = App.Vars.customers.filter((existingCustomer) => {
                                return Number(existingCustomer.id) === Number(customer.id);
                            });

                            // Add it to the customer list.
                            if (!result.length) {
                                App.Vars.customers.push(customer);
                            }
                        });
                    })
                    .fail(() => {
                        // If there is any error on the request, search by the local client database.
                        $existingCustomersList.empty();

                        App.Vars.customers.forEach((customer) => {
                            if (
                                customer.first_name.toLowerCase().indexOf(keyword) !== -1 ||
                                customer.last_name.toLowerCase().indexOf(keyword) !== -1 ||
                                customer.email.toLowerCase().indexOf(keyword) !== -1 ||
                                customer.phone_number.toLowerCase().indexOf(keyword) !== -1 ||
                                customer.address.toLowerCase().indexOf(keyword) !== -1 ||
                                customer.city.toLowerCase().indexOf(keyword) !== -1 ||
                                customer.zip_code.toLowerCase().indexOf(keyword) !== -1 ||
                                customer.notes.toLowerCase().indexOf(keyword) !== -1
                            ) {
                                $('<div/>', {
                                    'data-id': customer.id,
                                    'text': customer.first_name + ' ' + customer.last_name
                                }).appendTo($existingCustomersList);
                            }
                        });
                    })
                    .always(function () {
                        $('#loading').css('visibility', '');
                    });
            }, 1000);
        });

        /**
         * Event: Selected Service "Change"
         *
         * When the user clicks on a service, its available providers should become visible. We also need to
         * update the start and end time of the appointment.
         */
        $selectService.on('change', () => {
            const serviceId = $selectService.val();

            $selectProvider.empty();

            // Automatically update the service duration.
            const service = App.Vars.available_services.find((availableService) => {
                return Number(availableService.id) === Number(serviceId);
            });

            const duration = service ? service.duration : 60;

            const start = $startDatetime.datetimepicker('getDate');
            $endDatetime.datetimepicker('setDate', new Date(start.getTime() + duration * 60000));

            // Update the providers select box.

            App.Vars.available_providers.forEach((provider) => {
                provider.services.forEach((providerServiceId) => {
                    if (App.Vars.role_slug === Backend.DB_SLUG_PROVIDER && Number(provider.id) !== App.Vars.user_id) {
                        return; // continue
                    }

                    if (
                        App.Vars.role_slug === Backend.DB_SLUG_SECRETARY &&
                        App.Vars.secretaryProviders.indexOf(provider.id) === -1
                    ) {
                        return; // continue
                    }

                    // If the current provider is able to provide the selected service, add him to the list box.
                    if (Number(providerServiceId) === Number(serviceId)) {
                        $selectProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
                    }
                });
            });
        });

        /**
         * Event: Provider "Change"
         */
        $selectProvider.on('change', () => {
            updateTimezone();
        });

        /**
         * Event: Enter New Customer Button "Click"
         */
        $newCustomer.on('click', () => {
            $appointmentsModal
                .find(
                    '#customer-id, #first-name, #last-name, #email, ' +
                        '#phone-number, #address, #city, #zip-code, #customer-notes'
                )
                .val('');
        });
    }

    /**
     * Reset Appointment Dialog
     *
     * This method resets the manage appointment dialog modal to its initial state. After that you can make
     * any modification might be necessary in order to bring the dialog to the desired state.
     */
    function resetModal() {
        // Empty form fields.
        $appointmentsModal.find('input, textarea').val('');
        $appointmentsModal.find('.modal-message').fadeOut();

        // Prepare service and provider select boxes.
        $selectService.val($selectService.eq(0).attr('value'));

        // Fill the providers list box with providers that can serve the appointment's service and then select the
        // user's provider.
        $selectProvider.empty();
        App.Vars.available_providers.forEach((provider) => {
            const serviceId = $selectService.val();

            const canProvideService =
                provider.services.filter((providerServiceId) => {
                    return Number(providerServiceId) === Number(serviceId);
                }).length > 0;

            if (canProvideService) {
                // Add the provider to the list box.
                $selectProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
            }
        });

        // Close existing customers-filter frame.
        $existingCustomersList.slideUp('slow');
        $filterExistingCustomers.fadeOut('slow');
        $selectCustomer.find('span').text(App.Lang.select);

        // Setup start and datetimepickers.
        // Get the selected service duration. It will be needed in order to calculate the appointment end datetime.
        const serviceId = $selectService.val();

        const service = App.Vars.available_services.forEach((service) => Number(service.id) === Number(serviceId));

        const duration = service ? service.duration : 0;

        const startDatetime = new Date();
        const endDatetime = moment().add(duration, 'minutes').toDate();
        let dateFormat;

        switch (App.Vars.date_format) {
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
                throw new Error('Invalid App.Vars.date_format value.');
        }

        const firstWeekDay = App.Vars.first_weekday;

        const firstWeekDayNumber = App.Utils.Date.getWeekdayId(firstWeekDay);

        $startDatetime.datetimepicker({
            dateFormat: dateFormat,
            timeFormat: App.Vars.time_format === 'regular' ? 'h:mm tt' : 'HH:mm',

            // Translation
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
            prevText: App.Lang.previous,
            nextText: App.Lang.next,
            currentText: App.Lang.now,
            closeText: App.Lang.close,
            timeOnlyTitle: App.Lang.select_time,
            timeText: App.Lang.time,
            hourText: App.Lang.hour,
            minuteText: App.Lang.minutes,
            firstDay: firstWeekDayNumber,
            onClose: function () {
                const serviceId = $selectService.val();

                // Automatically update the #end-datetime DateTimePicker based on service duration.
                const service = App.Vars.available_services.find(
                    (availableService) => Number(availableService.id) === Number(serviceId)
                );

                const start = $startDatetime.datetimepicker('getDate');
                $endDatetime.datetimepicker('setDate', new Date(start.getTime() + service.duration * 60000));
            }
        });
        $startDatetime.datetimepicker('setDate', startDatetime);

        $endDatetime.datetimepicker({
            dateFormat: dateFormat,
            timeFormat: App.Vars.time_format === 'regular' ? 'h:mm tt' : 'HH:mm',

            // Translation
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
            prevText: App.Lang.previous,
            nextText: App.Lang.next,
            currentText: App.Lang.now,
            closeText: App.Lang.close,
            timeOnlyTitle: App.Lang.select_time,
            timeText: App.Lang.time,
            hourText: App.Lang.hour,
            minuteText: App.Lang.minutes,
            firstDay: firstWeekDayNumber
        });
        $endDatetime.datetimepicker('setDate', endDatetime);
    }

    /**
     * Validate the manage appointment dialog data.
     *
     * Validation checks need to run every time the data are going to be saved.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validateAppointmentForm() {
        // Reset previous validation css formatting.
        $appointmentsModal.find('.is-invalid').removeClass('is-invalid');
        $appointmentsModal.find('.modal-message').addClass('d-none');

        try {
            // Check required fields.
            let missingRequiredField = false;

            $appointmentsModal.find('.required').each((index, requiredField) => {
                if ($(requiredField).val() === '' || $(requiredField).val() === null) {
                    $(requiredField).addClass('is-invalid');
                    missingRequiredField = true;
                }
            });

            if (missingRequiredField) {
                throw new Error(App.Lang.fields_are_required);
            }

            // Check email address.
            if (!App.Utils.Validation.email($appointmentsModal.find('#email').val())) {
                $appointmentsModal.find('#email').addClass('is-invalid');
                throw new Error(App.Lang.invalid_email);
            }

            // Check appointment start and end time.
            const start = $startDatetime.datetimepicker('getDate');
            const end = $endDatetime.datetimepicker('getDate');
            if (start > end) {
                $startDatetime.addClass('is-invalid');
                $endDatetime.addClass('is-invalid');
                throw new Error(App.Lang.start_date_before_end_error);
            }

            return true;
        } catch (error) {
            $appointmentsModal
                .find('.modal-message')
                .addClass('alert-danger')
                .text(error.message)
                .removeClass('d-none');
            return false;
        }
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        addEventListeners();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        resetModal,
        initialize
    };
})();
