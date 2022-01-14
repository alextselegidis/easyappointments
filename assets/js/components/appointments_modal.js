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
 * Backend Calendar Appointments Modal
 *
 * This module implements the appointments modal functionality.
 *
 * Old Name: BackendCalendarAppointmentsModal
 */
App.Components.AppointmentsModal = (function () {
    function updateTimezone() {
        const providerId = $('#select-provider').val();

        const provider = App.Vars.available_providers.find(function (availableProvider) {
            return Number(availableProvider.id) === Number(providerId);
        });

        if (provider && provider.timezone) {
            $('.provider-timezone').text(App.Vars.timezones[provider.timezone]);
        }
    }

    function bindEventHandlers() {
        /**
         * Event: Manage Appointments Dialog Save Button "Click"
         *
         * Stores the appointment changes or inserts a new appointment depending on the dialog mode.
         */
        $('#appointments-modal #save-appointment').on('click', () => {
            // Before doing anything the appointment data need to be validated.
            if (!validateAppointmentForm()) {
                return;
            }

            // Prepare appointment data for AJAX request.
            const $dialog = $('#appointments-modal');

            // ID must exist on the object in order for the model to update the record and not to perform
            // an insert operation.

            const startDatetime = moment($dialog.find('#start-datetime').datetimepicker('getDate')).format(
                'YYYY-MM-DD HH:mm:ss'
            );
            const endDatetime = moment($dialog.find('#end-datetime').datetimepicker('getDate')).format(
                'YYYY-MM-DD HH:mm:ss'
            );

            const appointment = {
                id_services: $dialog.find('#select-service').val(),
                id_users_provider: $dialog.find('#select-provider').val(),
                start_datetime: startDatetime,
                end_datetime: endDatetime,
                location: $dialog.find('#appointment-location').val(),
                notes: $dialog.find('#appointment-notes').val(),
                is_unavailable: Number(false)
            };

            if ($dialog.find('#appointment-id').val() !== '') {
                // Set the id value, only if we are editing an appointment.
                appointment.id = $dialog.find('#appointment-id').val();
            }

            const customer = {
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
            const successCallback = function (response) {
                // Display success message to the user.
                Backend.displayNotification(App.Lang.appointment_saved);

                // Close the modal dialog and refresh the calendar appointments.
                $dialog.find('.alert').addClass('d-none');
                $dialog.modal('hide');
                $('#select-filter-item').trigger('change');
            };

            // Define error callback.
            const errorCallback = function () {
                $dialog.find('.modal-message').text(App.Lang.service_communication_error);
                $dialog.find('.modal-message').addClass('alert-danger').removeClass('d-none');
                $dialog.find('.modal-body').scrollTop(0);
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
        $('#insert-appointment').on('click', () => {
            $('.popover').remove();

            resetAppointmentDialog();

            const $dialog = $('#appointments-modal');

            // Set the selected filter item and find the next appointment time as the default modal values.
            if ($('#select-filter-item option:selected').attr('type') === 'provider') {
                const providerId = $('#select-filter-item').val();

                const providers = App.Vars.available_providers.filter(
                    (provider) => Number(provider.id) === Number(providerId)
                );

                if (providers.length) {
                    $dialog.find('#select-service').val(providers[0].services[0]).trigger('change');
                    $dialog.find('#select-provider').val(providerId);
                }
            } else if ($('#select-filter-item option:selected').attr('type') === 'service') {
                $dialog
                    .find('#select-service option[value="' + $('#select-filter-item').val() + '"]')
                    .prop('selected', true);
            } else {
                $dialog.find('#select-service option:first').prop('selected', true).trigger('change');
            }

            const serviceId = $dialog.find('#select-service').val();

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

            $dialog
                .find('#start-datetime')
                .val(App.Utils.Date.format(startMoment.toDate(), App.Vars.date_format, App.Vars.time_format, true));

            $dialog
                .find('#end-datetime')
                .val(
                    App.Utils.Date.format(
                        startMoment.add(duration, 'minutes').toDate(),
                        App.Vars.date_format,
                        App.Vars.time_format,
                        true
                    )
                );

            // Display modal form.
            $dialog.find('.modal-header h3').text(App.Lang.new_appointment_title);

            $dialog.modal('show');
        });

        /**
         * Event: Pick Existing Customer Button "Click"
         *
         * @param {jQuery.Event}
         */
        $('#select-customer').on('click', (event) => {
            const $list = $('#existing-customers-list');

            if (!$list.is(':visible')) {
                $(this).find('span').text(App.Lang.hide);
                $list.empty();
                $list.slideDown('slow');
                $('#filter-existing-customers').fadeIn('slow');
                $('#filter-existing-customers').val('');
                App.Vars.customers.forEach(function (customer) {
                    $('<div/>', {
                        'data-id': customer.id,
                        'text': customer.first_name + ' ' + customer.last_name
                    }).appendTo($list);
                });
            } else {
                $list.slideUp('slow');
                $('#filter-existing-customers').fadeOut('slow');
                $(event.target).find('span').text(App.Lang.select);
            }
        });

        /**
         * Event: Select Existing Customer From List "Click"
         */
        $('#appointments-modal').on('click', '#existing-customers-list div', (event) => {
            const customerId = $(event.target).attr('data-id');

            const customer = App.Vars.customers.find(function (customer) {
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

        let filterExistingCustomersTimeout = null;

        /**
         * Event: Filter Existing Customers "Change"
         */
        $('#filter-existing-customers').on('keyup', (event) => {
            if (filterExistingCustomersTimeout) {
                clearTimeout(filterExistingCustomersTimeout);
            }

            const keyword = $(event.target).val().toLowerCase();

            filterExistingCustomersTimeout = setTimeout(function () {
                const $list = $('#existing-customers-list');

                $('#loading').css('visibility', 'hidden');

                App.Http.Customers.search(keyword, 50)
                    .done((response) => {
                        $list.empty();

                        response.forEach((customer) => {
                            $('<div/>', {
                                'data-id': customer.id,
                                'text': customer.first_name + ' ' + customer.last_name
                            }).appendTo($list);

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
                        $list.empty();

                        App.Vars.customers.forEach(function (customer, index) {
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
                                }).appendTo($list);
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
         * When the user clicks on a service, its available providers should become visible. Also we need to
         * update the start and end time of the appointment.
         */
        $('#select-service').on('change', () => {
            const serviceId = $('#select-service').val();

            $('#select-provider').empty();

            // Automatically update the service duration.
            const service = App.Vars.available_services.find((availableService) => {
                return Number(availableService.id) === Number(serviceId);
            });

            const duration = service ? service.duration : 60;

            const start = $('#start-datetime').datetimepicker('getDate');
            $('#end-datetime').datetimepicker('setDate', new Date(start.getTime() + duration * 60000));

            // Update the providers select box.

            App.Vars.available_providers.forEach((provider) => {
                provider.services.forEach((providerServiceId) => {
                    if (App.Vars.role_slug === Backend.DB_SLUG_PROVIDER && Number(provider.id) !== App.Vars.user.id) {
                        return; // continue
                    }

                    if (
                        App.Vars.role_slug === Backend.DB_SLUG_SECRETARY &&
                        App.Vars.secretaryProviders.indexOf(provider.id) === -1
                    ) {
                        return; // continue
                    }

                    // If the current provider is able to provide the selected service, add him to the listbox.
                    if (Number(providerServiceId) === Number(serviceId)) {
                        $('#select-provider').append(
                            new Option(provider.first_name + ' ' + provider.last_name, provider.id)
                        );
                    }
                });
            });
        });

        /**
         * Event: Provider "Change"
         */
        $('#select-provider').on('change', () => {
            updateTimezone();
        });

        /**
         * Event: Enter New Customer Button "Click"
         */
        $('#new-customer').on('click', () => {
            $('#appointments-modal')
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
    function resetAppointmentDialog() {
        const $dialog = $('#appointments-modal');

        // Empty form fields.
        $dialog.find('input, textarea').val('');
        $dialog.find('.modal-message').fadeOut();

        // Prepare service and provider select boxes.
        $dialog.find('#select-service').val($dialog.find('#select-service').eq(0).attr('value'));

        // Fill the providers list box with providers that can serve the appointment's service and then select the
        // user's provider.
        $dialog.find('#select-provider').empty();
        App.Vars.available_providers.forEach((provider) => {
            const serviceId = $dialog.find('#select-service').val();

            const canProvideService =
                provider.services.filter((providerServiceId) => {
                    return Number(providerServiceId) === Number(serviceId);
                }).length > 0;

            if (canProvideService) {
                // Add the provider to the list box.
                $dialog
                    .find('#select-provider')
                    .append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
            }
        });

        // Close existing customers-filter frame.
        $('#existing-customers-list').slideUp('slow');
        $('#filter-existing-customers').fadeOut('slow');
        $('#select-customer span').text(App.Lang.select);

        // Setup start and datetimepickers.
        // Get the selected service duration. It will be needed in order to calculate the appointment end datetime.
        const serviceId = $dialog.find('#select-service').val();

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

        $dialog.find('#start-datetime').datetimepicker({
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
                const serviceId = $('#select-service').val();

                // Automatically update the #end-datetime DateTimePicker based on service duration.
                const service = App.Vars.available_services.find(
                    (availableService) => Number(availableService.id) === Number(serviceId)
                );

                const start = $('#start-datetime').datetimepicker('getDate');
                $('#end-datetime').datetimepicker('setDate', new Date(start.getTime() + service.duration * 60000));
            }
        });
        $dialog.find('#start-datetime').datetimepicker('setDate', startDatetime);

        $dialog.find('#end-datetime').datetimepicker({
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
        $dialog.find('#end-datetime').datetimepicker('setDate', endDatetime);
    }

    /**
     * Validate the manage appointment dialog data.
     *
     * Validation checks need to run every time the data are going to be saved.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validateAppointmentForm() {
        const $dialog = $('#appointments-modal');

        // Reset previous validation css formatting.
        $dialog.find('.is-invalid').removeClass('is-invalid');
        $dialog.find('.modal-message').addClass('d-none');

        try {
            // Check required fields.
            let missingRequiredField = false;

            $dialog.find('.required').each((index, requiredField) => {
                if ($(requiredField).val() === '' || $(requiredField).val() === null) {
                    $(requiredField).addClass('is-invalid');
                    missingRequiredField = true;
                }
            });

            if (missingRequiredField) {
                throw new Error(App.Lang.fields_are_required);
            }

            // Check email address.
            if (!App.Utils.Validation.email($dialog.find('#email').val())) {
                $dialog.find('#email').addClass('is-invalid');
                throw new Error(App.Lang.invalid_email);
            }

            // Check appointment start and end time.
            const start = $('#start-datetime').datetimepicker('getDate');
            const end = $('#end-datetime').datetimepicker('getDate');
            if (start > end) {
                $dialog.find('#start-datetime, #end-datetime').addClass('is-invalid');
                throw new Error(App.Lang.start_date_before_end_error);
            }

            return true;
        } catch (error) {
            $dialog.find('.modal-message').addClass('alert-danger').text(error.message).removeClass('d-none');
            return false;
        }
    }

    function initialize() {
        bindEventHandlers();
    }

    return {
        resetAppointmentDialog,
        bindEventHandlers,
        initialize
    };
})();
