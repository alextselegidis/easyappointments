/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Booking page.
 *
 * This module implements the functionality of the booking page
 *
 * Old Name: FrontendBook
 */
App.Pages.Booking = (function () {
    const $cookieNoticeLink = $('.cc-link');
    const $selectDate = $('#select-date');
    const $selectService = $('#select-service');
    const $selectProvider = $('#select-provider');
    const $selectTimezone = $('#select-timezone');
    const $firstName = $('#first-name');
    const $lastName = $('#last-name');
    const $email = $('#email');
    const $phoneNumber = $('#phone-number');
    const $address = $('#address');
    const $city = $('#city');
    const $zipCode = $('#zip-code');
    const $notes = $('#notes');
    const $captchaTitle = $('.captcha-title');
    const $availableHours = $('#available-hours');
    const $bookAppointmentSubmit = $('#book-appointment-submit');
    const $deletePersonalInformation = $('#delete-personal-information');

    /**
     * Contains terms and conditions consent.
     *
     * @type {Object}
     */
    let termsAndConditionsConsent;

    /**
     * Contains privacy policy consent.
     *
     * @type {Object}
     */
    let privacyPolicyConsent;

    /**
     * Determines the functionality of the page.
     *
     * @type {Boolean}
     */
    let manageMode = false;

    /**
     * Initialize the module.
     */
    function initialize() {
        if (vars('display_cookie_notice')) {
            cookieconsent.initialise({
                palette: {
                    popup: {
                        background: '#ffffffbd',
                        text: '#666666'
                    },
                    button: {
                        background: '#429a82',
                        text: '#ffffff'
                    }
                },
                content: {
                    message: lang('website_using_cookies_to_ensure_best_experience'),
                    dismiss: 'OK'
                }
            });

            $cookieNoticeLink.replaceWith(
                $('<a/>', {
                    'data-toggle': 'modal',
                    'data-target': '#cookie-notice-modal',
                    'href': '#',
                    'class': 'cc-link',
                    'text': $cookieNoticeLink.text()
                })
            );
        }

        manageMode = vars('manage_mode');

        // Initialize page's components (tooltips, datepickers etc).
        tippy('[data-tippy-content]');

        const weekdayId = App.Utils.Date.getWeekdayId(vars('first_weekday'));

        $selectDate.datepicker({
            dateFormat: 'dd-mm-yy',
            firstDay: weekdayId,
            minDate: 0,
            defaultDate: moment().toDate(),

            dayNames: [
                lang('sunday'),
                lang('monday'),
                lang('tuesday'),
                lang('wednesday'),
                lang('thursday'),
                lang('friday'),
                lang('saturday')
            ],
            dayNamesShort: [
                lang('sunday').substr(0, 3),
                lang('monday').substr(0, 3),
                lang('tuesday').substr(0, 3),
                lang('wednesday').substr(0, 3),
                lang('thursday').substr(0, 3),
                lang('friday').substr(0, 3),
                lang('saturday').substr(0, 3)
            ],
            dayNamesMin: [
                lang('sunday').substr(0, 2),
                lang('monday').substr(0, 2),
                lang('tuesday').substr(0, 2),
                lang('wednesday').substr(0, 2),
                lang('thursday').substr(0, 2),
                lang('friday').substr(0, 2),
                lang('saturday').substr(0, 2)
            ],
            monthNames: [
                lang('january'),
                lang('february'),
                lang('march'),
                lang('april'),
                lang('may'),
                lang('june'),
                lang('july'),
                lang('august'),
                lang('september'),
                lang('october'),
                lang('november'),
                lang('december')
            ],
            prevText: lang('previous'),
            nextText: lang('next'),
            currentText: lang('now'),
            closeText: lang('close'),

            onSelect: () => {
                App.Http.Booking.getAvailableHours(moment($selectDate.datepicker('getDate')).format('YYYY-MM-DD'));
                updateConfirmFrame();
            },

            onChangeMonthYear: (year, month) => {
                const currentDate = new Date(year, month - 1, 1);

                App.Http.Booking.getUnavailabilityDates(
                    $selectProvider.val(),
                    $selectService.val(),
                    moment(currentDate).format('YYYY-MM-DD')
                );
            }
        });

        $selectTimezone.val(Intl.DateTimeFormat().resolvedOptions().timeZone);

        // Bind the event handlers (might not be necessary every time we use this class).
        addEventListeners();

        // If the manage mode is true, the appointments data should be loaded by default.
        if (manageMode) {
            applyAppointmentData(vars('appointment_data'), vars('provider_data'), vars('customer_data'));
        } else {
            // Check if a specific service was selected (via URL parameter).
            const selectedServiceId = App.Utils.Url.queryParam('service');

            if (selectedServiceId && $selectService.find('option[value="' + selectedServiceId + '"]').length > 0) {
                $selectService.val(selectedServiceId);
            }

            $selectService.trigger('change'); // Load the available hours.

            // Check if a specific provider was selected.
            const selectedProviderId = App.Utils.Url.queryParam('provider');

            if (selectedProviderId && $selectProvider.find('option[value="' + selectedProviderId + '"]').length === 0) {
                // Select a service of this provider in order to make the provider available in the select box.
                for (const index in vars('available_providers')) {
                    const provider = vars('available_providers')[index];

                    if (provider.id === selectedProviderId && provider.services.length > 0) {
                        $selectService.val(provider.services[0]).trigger('change');
                    }
                }
            }

            if (selectedProviderId && $selectProvider.find('option[value="' + selectedProviderId + '"]').length > 0) {
                $selectProvider.val(selectedProviderId).trigger('change');
            }
        }
    }

    /**
     * Add the page event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Timezone "Changed"
         */
        $selectTimezone.on('change', () => {
            const date = $selectDate.datepicker('getDate');

            if (!date) {
                return;
            }

            App.Http.Booking.getAvailableHours(moment(date).format('YYYY-MM-DD'));

            updateConfirmFrame();
        });

        /**
         * Event: Selected Provider "Changed"
         *
         * Whenever the provider changes the available appointment date - time periods must be updated.
         */
        $selectProvider.on('change', (event) => {
            const $target = $(event.target);

            App.Http.Booking.getUnavailabilityDates(
                $target,
                $selectService.val(),
                moment($selectDate.datepicker('getDate')).format('YYYY-MM-DD')
            );
            updateConfirmFrame();
        });

        /**
         * Event: Selected Service "Changed"
         *
         * When the user clicks on a service, its available providers should
         * become visible.
         */
        $selectService.on('change', (event) => {
            const $target = $(event.target);
            const serviceId = $selectService.val();

            $selectProvider.empty();

            vars('available_providers').forEach((provider) => {
                // If the current provider is able to provide the selected service, add him to the list box.
                const canServeService =
                    provider.services.filter((providerServiceId) => Number(providerServiceId) === Number(serviceId))
                        .length > 0;

                if (canServeService) {
                    $selectProvider.append(new Option(provider.first_name + ' ' + provider.last_name, provider.id));
                }
            });

            // Add the "Any Provider" entry.
            if ($selectProvider.find('option').length >= 1 && vars('display_any_provider') === '1') {
                $selectProvider.prepend(new Option('- ' + lang('any_provider') + ' -', 'any-provider', true, true));
            }

            App.Http.Booking.getUnavailabilityDates(
                $selectProvider.val(),
                $target.val(),
                moment($selectDate.datepicker('getDate')).format('YYYY-MM-DD')
            );

            updateConfirmFrame();

            updateServiceDescription(serviceId);
        });

        /**
         * Event: Next Step Button "Clicked"
         *
         * This handler is triggered every time the user pressed the "next" button on the book wizard.
         * Some special tasks might be performed, depending on the current wizard step.
         */
        $('.button-next').on('click', (event) => {
            const $target = $(event.target);

            // If we are on the first step and there is no provider selected do not continue with the next step.
            if ($target.attr('data-step_index') === '1' && !$selectProvider.val()) {
                return;
            }

            // If we are on the 2nd tab then the user should have an appointment hour selected.
            if ($target.attr('data-step_index') === '2') {
                if (!$('.selected-hour').length) {
                    if (!$('#select-hour-prompt').length) {
                        $('<div/>', {
                            'id': 'select-hour-prompt',
                            'class': 'text-danger mb-4',
                            'text': lang('appointment_hour_missing')
                        }).prependTo('#available-hours');
                    }
                    return;
                }
            }

            // If we are on the 3rd tab then we will need to validate the user's input before proceeding to the next
            // step.
            if ($target.attr('data-step_index') === '3') {
                if (!validateCustomerForm()) {
                    return; // Validation failed, do not continue.
                } else {
                    updateConfirmFrame();

                    const $acceptToTermsAndConditions = $('#accept-to-terms-and-conditions');
                    if ($acceptToTermsAndConditions.length && $acceptToTermsAndConditions.prop('checked') === true) {
                        const newTermsAndConditionsConsent = {
                            first_name: $firstName.val(),
                            last_name: $lastName.val(),
                            email: $email.val(),
                            type: 'terms-and-conditions'
                        };

                        if (
                            JSON.stringify(newTermsAndConditionsConsent) !== JSON.stringify(termsAndConditionsConsent)
                        ) {
                            termsAndConditionsConsent = newTermsAndConditionsConsent;
                            App.Http.Booking.saveConsent(termsAndConditionsConsent);
                        }
                    }

                    const $acceptToPrivacyPolicy = $('#accept-to-privacy-policy');
                    if ($acceptToPrivacyPolicy.length && $acceptToPrivacyPolicy.prop('checked') === true) {
                        const newPrivacyPolicyConsent = {
                            first_name: $firstName.val(),
                            last_name: $lastName.val(),
                            email: $email.val(),
                            type: 'privacy-policy'
                        };

                        if (JSON.stringify(newPrivacyPolicyConsent) !== JSON.stringify(privacyPolicyConsent)) {
                            privacyPolicyConsent = newPrivacyPolicyConsent;
                            App.Http.Booking.saveConsent(privacyPolicyConsent);
                        }
                    }
                }
            }

            // Display the next step tab (uses jquery animation effect).
            const nextTabIndex = parseInt($target.attr('data-step_index')) + 1;

            $target
                .parents()
                .eq(1)
                .hide('fade', () => {
                    $('.active-step').removeClass('active-step');
                    $('#step-' + nextTabIndex).addClass('active-step');
                    $('#wizard-frame-' + nextTabIndex).show('fade');
                });
        });

        /**
         * Event: Back Step Button "Clicked"
         *
         * This handler is triggered every time the user pressed the "back" button on the
         * book wizard.
         */
        $('.button-back').on('click', (event) => {
            const prevTabIndex = parseInt($(event.target).attr('data-step_index')) - 1;

            $(event.target)
                .parents()
                .eq(1)
                .hide('fade', () => {
                    $('.active-step').removeClass('active-step');
                    $('#step-' + prevTabIndex).addClass('active-step');
                    $('#wizard-frame-' + prevTabIndex).show('fade');
                });
        });

        /**
         * Event: Available Hour "Click"
         *
         * Triggered whenever the user clicks on an available hour for his appointment.
         */
        $availableHours.on('click', '.available-hour', (event) => {
            $availableHours.find('.selected-hour').removeClass('selected-hour');
            $(event.target).addClass('selected-hour');
            updateConfirmFrame();
        });

        if (manageMode) {
            /**
             * Event: Cancel Appointment Button "Click"
             *
             * When the user clicks the "Cancel" button this form is going to be submitted. We need
             * the user to confirm this action because once the appointment is cancelled, it will be
             * delete from the database.
             *
             * @param {jQuery.Event} event
             */
            $('#cancel-appointment').on('click', () => {
                const $cancelAppointmentForm = $('#cancel-appointment-form');

                let $cancelReason;

                const buttons = [
                    {
                        text: lang('cancel'),
                        click: () => {
                            $('#message-box').dialog('close');
                        }
                    },
                    {
                        text: 'OK',
                        click: () => {
                            if ($cancelReason.val() === '') {
                                $cancelReason.css('border', '2px solid #DC3545');
                                return;
                            }
                            $cancelAppointmentForm.find('textarea').val($cancelReason.val());
                            $cancelAppointmentForm.submit();
                        }
                    }
                ];

                App.Utils.Message.show(
                    lang('cancel_appointment_title'),
                    lang('write_appointment_removal_reason'),
                    buttons
                );

                $cancelReason = $('<textarea/>', {
                    'class': 'form-control',
                    'id': 'cancel-reason',
                    'rows': '3',
                    'css': {
                        'width': '100%'
                    }
                }).appendTo('#message-box');

                return false;
            });

            $deletePersonalInformation.on('click', () => {
                const buttons = [
                    {
                        text: lang('cancel'),
                        click: () => {
                            $('#message-box').dialog('close');
                        }
                    },
                    {
                        text: lang('delete'),
                        click: () => {
                            App.Http.Booking.deletePersonalInformation(vars('customer_token'));
                        }
                    }
                ];

                App.Utils.Message.show(
                    lang('delete_personal_information'),
                    lang('delete_personal_information_prompt'),
                    buttons
                );
            });
        }

        /**
         * Event: Book Appointment Form "Submit"
         *
         * Before the form is submitted to the server we need to make sure that
         * in the meantime the selected appointment date/time wasn't reserved by
         * another customer or event.
         *
         * @param {jQuery.Event} event
         */
        $bookAppointmentSubmit.on('click', () => {
            App.Http.Booking.registerAppointment();
        });

        /**
         * Event: Refresh captcha image.
         */
        $captchaTitle.on('click', 'button', () => {
            $('.captcha-image').attr('src', App.Utils.Url.siteUrl('captcha?' + Date.now()));
        });

        $selectDate.on('mousedown', '.ui-datepicker-calendar td', () => {
            setTimeout(() => {
                App.Http.Booking.applyPreviousUnavailabilityDates();
            }, 300);
        });
    }

    /**
     * This function validates the customer's data input. The user cannot continue without passing all the validation
     * checks.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validateCustomerForm() {
        $('#wizard-frame-3 .is-invalid').removeClass('is-invalid');
        $('#wizard-frame-3 label.text-danger').removeClass('text-danger');

        try {
            // Validate required fields.
            let missingRequiredField = false;

            $('.required').each((index, requiredField) => {
                if (!$(requiredField).val()) {
                    $(requiredField).parents('.form-group').addClass('is-invalid');
                    missingRequiredField = true;
                }
            });

            if (missingRequiredField) {
                throw new Error(lang('fields_are_required'));
            }

            const $acceptToTermsAndConditions = $('#accept-to-terms-and-conditions');
            if ($acceptToTermsAndConditions.length && !$acceptToTermsAndConditions.prop('checked')) {
                $acceptToTermsAndConditions.parents('.form-check').addClass('text-danger');
                throw new Error(lang('fields_are_required'));
            }

            const $acceptToPrivacyPolicy = $('#accept-to-privacy-policy');
            if ($acceptToPrivacyPolicy.length && !$acceptToPrivacyPolicy.prop('checked')) {
                $acceptToPrivacyPolicy.parents('.form-check').addClass('text-danger');
                throw new Error(lang('fields_are_required'));
            }

            // Validate email address.
            if ($email.val() && !App.Utils.Validation.email($email.val())) {
                $email.parents('.form-group').addClass('is-invalid');
                throw new Error(lang('invalid_email'));
            }

            return true;
        } catch (error) {
            $('#form-message').text(error.message);
            return false;
        }
    }

    /**
     * Every time this function is executed, it updates the confirmation page with the latest
     * customer settings and input for the appointment booking.
     */
    function updateConfirmFrame() {
        if ($availableHours.find('.selected-hour').text() === '') {
            return;
        }

        // Appointment Details
        let selectedDate = $selectDate.datepicker('getDate');

        if (selectedDate !== null) {
            selectedDate = App.Utils.Date.format(selectedDate, vars('date_format'), vars('time_format'));
        }

        const serviceId = $selectService.val();
        let servicePrice = '';
        let serviceCurrency = '';

        vars('available_services').forEach((service) => {
            if (Number(service.id) === Number(serviceId) && Number(service.price) > 0) {
                servicePrice = service.price;
                serviceCurrency = service.currency;
                return false; // Break loop
            }
        });

        $('#appointment-details').empty();

        $('<div/>', {
            'html': [
                $('<h4/>', {
                    'text': lang('appointment')
                }),
                $('<p/>', {
                    'html': [
                        $('<span/>', {
                            'text': lang('service') + ': ' + $selectService.find('option:selected').text()
                        }),
                        $('<br/>'),
                        $('<span/>', {
                            'text': lang('provider') + ': ' + $selectProvider.find('option:selected').text()
                        }),
                        $('<br/>'),
                        $('<span/>', {
                            'text':
                                lang('start') +
                                ': ' +
                                selectedDate +
                                ' ' +
                                $availableHours.find('.selected-hour').text()
                        }),
                        $('<br/>'),
                        $('<span/>', {
                            'text': lang('timezone') + ': ' + $selectTimezone.find('option:selected').text()
                        }),
                        $('<br/>'),
                        $('<span/>', {
                            'text': lang('price') + ': ' + servicePrice + ' ' + serviceCurrency,
                            'prop': {
                                'hidden': !servicePrice
                            }
                        })
                    ]
                })
            ]
        }).appendTo('#appointment-details');

        // Customer Details
        const firstName = App.Utils.String.escapeHtml($firstName.val());
        const lastName = App.Utils.String.escapeHtml($lastName.val());
        const phoneNumber = App.Utils.String.escapeHtml($phoneNumber.val());
        const email = App.Utils.String.escapeHtml($email.val());
        const address = App.Utils.String.escapeHtml($address.val());
        const city = App.Utils.String.escapeHtml($city.val());
        const zipCode = App.Utils.String.escapeHtml($zipCode.val());

        $('#customer-details').empty();

        $('<div/>', {
            'html': [
                $('<h4/>)', {
                    'text': lang('customer')
                }),
                $('<p/>', {
                    'html': [
                        $('<span/>', {
                            'text': lang('customer') + ': ' + firstName + ' ' + lastName
                        }),
                        $('<br/>'),
                        $('<span/>', {
                            'text': lang('phone_number') + ': ' + phoneNumber
                        }),
                        $('<br/>'),
                        $('<span/>', {
                            'text': lang('email') + ': ' + email
                        }),
                        $('<br/>'),
                        $('<span/>', {
                            'text': address ? lang('address') + ': ' + address : ''
                        }),
                        $('<br/>'),
                        $('<span/>', {
                            'text': city ? lang('city') + ': ' + city : ''
                        }),
                        $('<br/>'),
                        $('<span/>', {
                            'text': zipCode ? lang('zip_code') + ': ' + zipCode : ''
                        }),
                        $('<br/>')
                    ]
                })
            ]
        }).appendTo('#customer-details');

        // Update appointment form data for submission to server when the user confirms the appointment.
        const data = {};

        data.customer = {
            last_name: $lastName.val(),
            first_name: $firstName.val(),
            email: $email.val(),
            phone_number: $phoneNumber.val(),
            address: $address.val(),
            city: $city.val(),
            zip_code: $zipCode.val(),
            timezone: $selectTimezone.val()
        };

        data.appointment = {
            start_datetime:
                moment($selectDate.datepicker('getDate')).format('YYYY-MM-DD') +
                ' ' +
                moment($('.selected-hour').data('value'), 'HH:mm').format('HH:mm') +
                ':00',
            end_datetime: calculateEndDatetime(),
            notes: $notes.val(),
            is_unavailability: false,
            id_users_provider: $selectProvider.val(),
            id_services: $selectService.val()
        };

        data.manage_mode = manageMode;

        if (manageMode) {
            data.appointment.id = vars('appointment_data').id;
            data.customer.id = vars('customer_data').id;
        }
        $('input[name="csrfToken"]').val(vars('csrf_token'));
        $('input[name="post_data"]').val(JSON.stringify(data));
    }

    /**
     * This method calculates the end datetime of the current appointment.
     *
     * End datetime is depending on the service and start datetime fields.
     *
     * @return {String} Returns the end datetime in string format.
     */
    function calculateEndDatetime() {
        // Find selected service duration.
        const serviceId = $selectService.val();

        const service = vars('available_services').find(
            (availableService) => Number(availableService.id) === Number(serviceId)
        );

        // Add the duration to the start datetime.
        const selectedDate = moment($selectDate.datepicker('getDate')).format('YYYY-MM-DD');

        const selectedHour = $('.selected-hour').data('value'); // HH:mm

        const startMoment = moment(selectedDate + ' ' + selectedHour);

        let endMoment;

        if (service.duration && startMoment) {
            endMoment = startMoment.clone().add({'minutes': parseInt(service.duration)});
        } else {
            endMoment = moment();
        }

        return endMoment.format('YYYY-MM-DD HH:mm:ss');
    }

    /**
     * This method applies the appointment's data to the wizard so
     * that the user can start making changes on an existing record.
     *
     * @param {Object} appointment Selected appointment's data.
     * @param {Object} provider Selected provider's data.
     * @param {Object} customer Selected customer's data.
     *
     * @return {Boolean} Returns the operation result.
     */
    function applyAppointmentData(appointment, provider, customer) {
        try {
            // Select Service & Provider
            $selectService.val(appointment.id_services).trigger('change');
            $selectProvider.val(appointment.id_users_provider);

            // Set Appointment Date
            const startMoment = moment(appointment.start_datetime);
            $selectDate.datepicker('setDate', startMoment.toDate());
            App.Http.Booking.getAvailableHours(startMoment.format('YYYY-MM-DD'));

            // Apply Customer's Data
            $lastName.val(customer.last_name);
            $firstName.val(customer.first_name);
            $email.val(customer.email);
            $phoneNumber.val(customer.phone_number);
            $address.val(customer.address);
            $city.val(customer.city);
            $zipCode.val(customer.zip_code);
            if (customer.timezone) {
                $selectTimezone.val(customer.timezone);
            }
            const appointmentNotes = appointment.notes !== null ? appointment.notes : '';
            $notes.val(appointmentNotes);

            updateConfirmFrame();

            return true;
        } catch (exc) {
            return false;
        }
    }

    /**
     * This method updates a div's HTML content with a brief description of the
     * user selected service (only if available in db). This is useful for the
     * customers upon selecting the correct service.
     *
     * @param {Number} serviceId The selected service record id.
     */
    function updateServiceDescription(serviceId) {
        const $serviceDescription = $('#service-description');

        $serviceDescription.empty();

        const service = vars('available_services').find(
            (availableService) => Number(availableService.id) === Number(serviceId)
        );

        if (!service) {
            return;
        }

        $('<strong/>', {
            'text': service.name
        }).appendTo($serviceDescription);

        if (service.description) {
            $('<br/>').appendTo($serviceDescription);

            $('<span/>', {
                'html': App.Utils.String.escapeHtml(service.description).replaceAll('\n', '<br/>')
            }).appendTo($serviceDescription);
        }

        if (service.duration || Number(service.price) > 0 || service.location) {
            $('<br/>').appendTo($serviceDescription);
        }

        if (service.duration) {
            $('<span/>', {
                'text': '[' + lang('duration') + ' ' + service.duration + ' ' + lang('minutes') + ']'
            }).appendTo($serviceDescription);
        }

        if (Number(service.price) > 0) {
            $('<span/>', {
                'text': '[' + lang('price') + ' ' + service.price + ' ' + service.currency + ']'
            }).appendTo($serviceDescription);
        }

        if (service.location) {
            $('<span/>', {
                'text': '[' + lang('location') + ' ' + service.location + ']'
            }).appendTo($serviceDescription);
        }
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        manageMode,
        initialize,
        updateConfirmFrame
    };
})();
