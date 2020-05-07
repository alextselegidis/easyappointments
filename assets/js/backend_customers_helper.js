/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

(function () {

    'use strict';

    /**
     * CustomersHelper Class
     *
     * This class contains the methods that are used in the backend customers page.
     *
     * @class CustomersHelper
     */
    function CustomersHelper() {
        this.filterResults = {};
        this.filterLimit = 20;
    }

    /**
     * Binds the default event handlers of the backend customers page.
     */
    CustomersHelper.prototype.bindEventHandlers = function () {
        var instance = this;

        /**
         * Event: Filter Customers Form "Submit"
         */
        $('#filter-customers form').submit(function (event) {
            var key = $('#filter-customers .key').val();
            $('#filter-customers .selected').removeClass('selected');
            instance.filterLimit = 20;
            instance.resetForm();
            instance.filter(key);
            return false;
        });

        /**
         * Event: Filter Customers Clear Button "Click"
         */
        $('#filter-customers .clear').click(function () {
            $('#filter-customers .key').val('');
            instance.filterLimit = 20;
            instance.filter('');
            instance.resetForm();
        });

        /**
         * Event: Filter Entry "Click"
         *
         * Display the customer data of the selected row.
         */
        $(document).on('click', '.entry', function () {
            if ($('#filter-customers .filter').prop('disabled')) {
                return; // Do nothing when user edits a customer record.
            }

            var customerId = $(this).attr('data-id');
            var customer = {};
            $.each(instance.filterResults, function (index, item) {
                if (Number(item.id) === Number(customerId)) {
                    customer = item;
                    return false;
                }
            });

            instance.display(customer);
            $('#filter-customers .selected').removeClass('selected');
            $(this).addClass('selected');
            $('#edit-customer, #delete-customer').prop('disabled', false);
        });

        /**
         * Event: Appointment Row "Click"
         *
         * Display appointment data of the selected row.
         */
        $(document).on('click', '.appointment-row', function () {
            $('#customer-appointments .selected').removeClass('selected');
            $(this).addClass('selected');

            var customerId = $('#filter-customers .selected').attr('data-id');
            var appointmentId = $(this).attr('data-id');
            var appointment = {};

            $.each(instance.filterResults, function (index, customer) {
                if (customer.id === customerId) {
                    $.each(customer.appointments, function (index, customerAppointment) {
                        if (Number(customerAppointment.id) === Number(appointmentId)) {
                            appointment = customerAppointment;
                            return false;
                        }
                    });
                    return false;
                }
            });

            instance.displayAppointment(appointment);
        });

        /**
         * Event: Add Customer Button "Click"
         */
        $('#add-customer').click(function () {
            instance.resetForm();
            $('#add-edit-delete-group').hide();
            $('#save-cancel-group').show();
            $('.record-details').find('input, textarea').prop('readonly', false);

            $('#filter-customers button').prop('disabled', true);
            $('#filter-customers .results').css('color', '#AAA');
        });

        /**
         * Event: Edit Customer Button "Click"
         */
        $('#edit-customer').click(function () {
            $('.record-details').find('input, textarea').prop('readonly', false);
            $('#add-edit-delete-group').hide();
            $('#save-cancel-group').show();

            $('#filter-customers button').prop('disabled', true);
            $('#filter-customers .results').css('color', '#AAA');
        });

        /**
         * Event: Cancel Customer Add/Edit Operation Button "Click"
         */
        $('#cancel-customer').click(function () {
            var id = $('#customer-id').val();
            instance.resetForm();
            if (id) {
                instance.select(id, true);
            }
        });

        /**
         * Event: Save Add/Edit Customer Operation "Click"
         */
        $('#save-customer').click(function () {
            var customer = {
                first_name: $('#first-name').val(),
                last_name: $('#last-name').val(),
                email: $('#email').val(),
                phone_number: $('#phone-number').val(),
                address: $('#address').val(),
                city: $('#city').val(),
                zip_code: $('#zip-code').val(),
                notes: $('#notes').val(),
                timezone: $('#timezone').val()
            };

            if ($('#customer-id').val()) {
                customer.id = $('#customer-id').val();
            }

            if (!instance.validate()) {
                return;
            }

            instance.save(customer);
        });

        /**
         * Event: Delete Customer Button "Click"
         */
        $('#delete-customer').click(function () {
            var customerId = $('#customer-id').val();
            var buttons = [
                {
                    text: EALang.delete,
                    click: function () {
                        instance.delete(customerId);
                        $('#message_box').dialog('close');
                    }
                },
                {
                    text: EALang.cancel,
                    click: function () {
                        $('#message_box').dialog('close');
                    }
                }
            ];

            GeneralFunctions.displayMessageBox(EALang.delete_customer,
                EALang.delete_record_prompt, buttons);
        });
    };

    /**
     * Save a customer record to the database (via ajax post).
     *
     * @param {Object} customer Contains the customer data.
     */
    CustomersHelper.prototype.save = function (customer) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_customer';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            customer: JSON.stringify(customer)
        };

        $.post(url, data)
            .done(function (response) {
                Backend.displayNotification(EALang.customer_saved);
                this.resetForm();
                $('#filter-customers .key').val('');
                this.filter('', response.id, true);
            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Delete a customer record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    CustomersHelper.prototype.delete = function (id) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_customer';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            customer_id: id
        };

        $.post(url, data)
            .done(function () {
                Backend.displayNotification(EALang.customer_deleted);
                this.resetForm();
                this.filter($('#filter-customers .key').val());
            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Validate customer data before save (insert or update).
     */
    CustomersHelper.prototype.validate = function () {
        $('#form-message')
            .removeClass('alert-danger')
            .hide();
        $('.has-error').removeClass('has-error');

        try {
            // Validate required fields.
            var missingRequired = false;

            $('.required').each(function () {
                if ($(this).val() === '') {
                    $(this).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(EALang.fields_are_required);
            }

            // Validate email address.
            if (!GeneralFunctions.validateEmail($('#email').val())) {
                $('#email').closest('.form-group').addClass('has-error');
                throw new Error(EALang.invalid_email);
            }

            return true;
        } catch (error) {
            $('#form-message')
                .addClass('alert-danger')
                .text(error.message)
                .show();
            return false;
        }
    };

    /**
     * Bring the customer form back to its initial state.
     */
    CustomersHelper.prototype.resetForm = function () {
        $('.record-details').find('input, textarea').val('');
        $('.record-details').find('input, textarea').prop('readonly', true);

        $('#customer-appointments').empty();
        $('#appointment-details').toggleClass('hidden', true).empty();
        $('#edit-customer, #delete-customer').prop('disabled', true);
        $('#add-edit-delete-group').show();
        $('#save-cancel-group').hide();

        $('.record-details .has-error').removeClass('has-error');
        $('.record-details #form-message').hide();

        $('#filter-customers button').prop('disabled', false);
        $('#filter-customers .selected').removeClass('selected');
        $('#filter-customers .results').css('color', '');
    };

    /**
     * Display a customer record into the form.
     *
     * @param {Object} customer Contains the customer record data.
     */
    CustomersHelper.prototype.display = function (customer) {
        $('#customer-id').val(customer.id);
        $('#first-name').val(customer.first_name);
        $('#last-name').val(customer.last_name);
        $('#email').val(customer.email);
        $('#phone-number').val(customer.phone_number);
        $('#address').val(customer.address);
        $('#city').val(customer.city);
        $('#zip-code').val(customer.zip_code);
        $('#notes').val(customer.notes);
        $('#timezone').val(customer.timezone);

        $('#customer-appointments').empty();
        $.each(customer.appointments, function (index, appointment) {
            if (GlobalVariables.user.role_slug === Backend.DB_SLUG_PROVIDER && parseInt(appointment.id_users_provider) !== GlobalVariables.user.id) {
                return true; // continue
            }

            if (GlobalVariables.user.role_slug === Backend.DB_SLUG_SECRETARY && GlobalVariables.secretaryProviders.indexOf(appointment.id_users_provider) === -1) {
                return true; // continue
            }

            var start = GeneralFunctions.formatDate(Date.parse(appointment.start_datetime), GlobalVariables.dateFormat, true);
            var end = GeneralFunctions.formatDate(Date.parse(appointment.end_datetime), GlobalVariables.dateFormat, true);

            $('<div/>', {
                'class': 'appointment-row',
                'data-id': appointment.id,
                'html': [
                    $('<span/>', {
                        'text': start + ' - ' + end
                    }),
                    $('<br/>'),
                    $('<span/>', {
                        'text': appointment.service.name
                    }),
                    $('<span/>', {
                        'text': appointment.provider.first_name + ' ' + appointment.provider.last_name
                    })
                ]
            })
                .appendTo('#customer-appointments');
        });

        $('#appointment-details').empty();
    };

    /**
     * Filter customer records.
     *
     * @param {String} key This key string is used to filter the customer records.
     * @param {Number} selectId Optional, if set then after the filter operation the record with the given
     * ID will be selected (but not displayed).
     * @param {Boolean} display Optional (false), if true then the selected record will be displayed on the form.
     */
    CustomersHelper.prototype.filter = function (key, selectId, display) {
        display = display || false;

        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_customers';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            key: key,
            limit: this.filterLimit
        };

        $.post(url, data)
            .done(function (response) {
                this.filterResults = response;

                $('#filter-customers .results').empty();
                $.each(response, function (index, customer) {
                    $('#filter-customers .results')
                        .append(this.getFilterHtml(customer))
                        .append($('<hr/>'));
                }.bind(this));

                if (!response.length) {
                    $('#filter-customers .results').append(
                        $('<em/>', {
                            'text': EALang.no_records_found
                        })
                    );
                } else if (response.length === this.filterLimit) {
                    $('<button/>', {
                        'type': 'button',
                        'class': 'well btn-block load-more text-center',
                        'text': EALang.load_more,
                        'click': function () {
                            this.filterLimit += 20;
                            this.filter(key, selectId, display);
                        }.bind(this)
                    })
                        .appendTo('#filter-customers .results');
                }

                if (selectId) {
                    this.select(selectId, display);
                }

            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Get the filter results row HTML code.
     *
     * @param {Object} customer Contains the customer data.
     *
     * @return {String} Returns the record HTML code.
     */
    CustomersHelper.prototype.getFilterHtml = function (customer) {
        var name = customer.first_name + ' ' + customer.last_name;

        var info = customer.email;

        info = customer.phone_number ? info + ', ' + customer.phone_number : info;

        return $('<div/>', {
            'class': 'customer-row entry',
            'data-id': customer.id,
            'html': [
                $('<strong/>', {
                    'text': name
                }),
                $('<br/>'),
                $('<span/>', {
                    'text': info
                }),
                $('<br/>'),
            ]
        });
    };

    /**
     * Select a specific record from the current filter results.
     *
     * If the customer id does not exist in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} display Optional (false), if true then the method will display the record
     * on the form.
     */
    CustomersHelper.prototype.select = function (id, display) {
        display = display || false;

        $('#filter-customers .selected').removeClass('selected');

        $('#filter-customers .entry').each(function () {
            if (Number($(this).attr('data-id')) === Number(id)) {
                $(this).addClass('selected');
                return false;
            }
        });

        if (display) {
            $.each(this.filterResults, function (index, customer) {
                if (Number(customer.id) === Number(id)) {
                    this.display(customer);
                    $('#edit-customer, #delete-customer').prop('disabled', false);
                    return false;
                }
            }.bind(this));
        }
    };

    /**
     * Display appointment details on customers backend page.
     *
     * @param {Object} appointment Appointment data
     */
    CustomersHelper.prototype.displayAppointment = function (appointment) {
        var start = GeneralFunctions.formatDate(Date.parse(appointment.start_datetime), GlobalVariables.dateFormat, true);
        var end = GeneralFunctions.formatDate(Date.parse(appointment.end_datetime), GlobalVariables.dateFormat, true);
        var timezone = GlobalVariables.timezones[GlobalVariables.user.timezone];

        $('<div/>', {
            'html': [
                $('<strong/>', {
                    'text': appointment.service.name
                }),
                $('<br/>'),
                $('<span/>', {
                    'text': appointment.provider.first_name + ' ' + appointment.provider.last_name
                }),
                $('<br/>'),
                $('<span/>', {
                    'text': appointment.provider.first_name + ' ' + appointment.provider.last_name
                }),
                $('<br/>'),
                $('<span/>', {
                    'text': EALang.timezone + ': ' + timezone
                }),
                $('<br/>')
            ]
        })
            .appendTo('#appointment-details');

        $('#appointment-details').removeClass('hidden');
    };

    window.CustomersHelper = CustomersHelper;
})();
