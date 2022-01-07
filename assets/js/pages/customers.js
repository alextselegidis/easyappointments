/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Customers page module.
 *
 * This module contains the methods that are used in the customers page.
 */
App.Pages.Customers = (function () {
    const $customers = $('#customers');
    let filterResults = {};
    let filterLimit = 20;

    /**
     * Binds the default event handlers of the backend customers page.
     */
    function bindEventHandlers() {
        /**
         * Event: Filter Customers Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $customers.on('submit', '#filter-customers form', function (event) {
            event.preventDefault();
            const key = $('#filter-customers .key').val();
            $('#filter-customers .selected').removeClass('selected');
            filterLimit = 20;
            resetForm();
            filter(key);
        });

        /**
         * Event: Filter Entry "Click"
         *
         * Display the customer data of the selected row.
         */
        $customers.on('click', '.customer-row', function () {
            if ($('#filter-customers .filter').prop('disabled')) {
                return; // Do nothing when user edits a customer record.
            }

            const customerId = $(this).attr('data-id');
            const customer = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(customerId);
            });

            display(customer);
            $('#filter-customers .selected').removeClass('selected');
            $(this).addClass('selected');
            $('#edit-customer, #delete-customer').prop('disabled', false);
        });

        /**
         * Event: Add Customer Button "Click"
         */
        $customers.on('click', '#add-customer', function () {
            resetForm();
            $('#add-edit-delete-group').hide();
            $('#save-cancel-group').show();
            $('.record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-customers button').prop('disabled', true);
            $('#filter-customers .results').css('color', '#AAA');
        });

        /**
         * Event: Edit Customer Button "Click"
         */
        $customers.on('click', '#edit-customer', function () {
            $('.record-details').find('input, select, textarea').prop('disabled', false);
            $('#add-edit-delete-group').hide();
            $('#save-cancel-group').show();
            $('#filter-customers button').prop('disabled', true);
            $('#filter-customers .results').css('color', '#AAA');
        });

        /**
         * Event: Cancel Customer Add/Edit Operation Button "Click"
         */
        $customers.on('click', '#cancel-customer', function () {
            const id = $('#customer-id').val();
            resetForm();
            if (id) {
                select(id, true);
            }
        });

        /**
         * Event: Save Add/Edit Customer Operation "Click"
         */
        $customers.on('click', '#save-customer', function () {
            const customer = {
                first_name: $('#first-name').val(),
                last_name: $('#last-name').val(),
                email: $('#email').val(),
                phone_number: $('#phone-number').val(),
                address: $('#address').val(),
                city: $('#city').val(),
                zip_code: $('#zip-code').val(),
                notes: $('#notes').val(),
                timezone: $('#timezone').val(),
                language: $('#language').val() || 'english'
            };

            if ($('#customer-id').val()) {
                customer.id = $('#customer-id').val();
            }

            if (!validate()) {
                return;
            }

            save(customer);
        });

        /**
         * Event: Delete Customer Button "Click"
         */
        $customers.on('click', '#delete-customer', function () {
            const customerId = $('#customer-id').val();
            const buttons = [
                {
                    text: App.Lang.cancel,
                    click: function () {
                        $('#message-box').dialog('close');
                    }
                },
                {
                    text: App.Lang.delete,
                    click: function () {
                        remove(customerId);
                        $('#message-box').dialog('close');
                    }
                }
            ];

            App.Utils.Message.show(App.Lang.delete_customer, App.Lang.delete_record_prompt, buttons);
        });
    }

    /**
     * Save a customer record to the database (via ajax post).
     *
     * @param {Object} customer Contains the customer data.
     */
    function save(customer) {
        App.Http.Customers.save(customer).then((response) => {
            Backend.displayNotification(App.Lang.customer_saved);
            resetForm();
            $('#filter-customers .key').val('');
            filter('', response.id, true);
        });
    }

    /**
     * Delete a customer record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    function remove(id) {
        App.Http.Customers.destroy(id).then(() => {
            Backend.displayNotification(App.Lang.customer_deleted);
            resetForm();
            filter($('#filter-customers .key').val());
        });
    }

    /**
     * Validate customer data before save (insert or update).
     */
    function validate() {
        $('#form-message').removeClass('alert-danger').hide();
        $('.is-invalid').removeClass('is-invalid');

        try {
            // Validate required fields.
            let missingRequired = false;

            $('.required').each(function (index, requiredField) {
                if ($(requiredField).val() === '') {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(App.Lang.fields_are_required);
            }

            // Validate email address.
            if (!App.Utils.Validation.email($('#email').val())) {
                $('#email').addClass('is-invalid');
                throw new Error(App.Lang.invalid_email);
            }

            return true;
        } catch (error) {
            $('#form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Bring the customer form back to its initial state.
     */
    function resetForm() {
        $('.record-details').find('input, select, textarea').val('').prop('disabled', true);
        $('.record-details #timezone').val('UTC');

        $('#language').val('english');

        $('#customer-appointments').empty();
        $('#edit-customer, #delete-customer').prop('disabled', true);
        $('#add-edit-delete-group').show();
        $('#save-cancel-group').hide();

        $('.record-details .is-invalid').removeClass('is-invalid');
        $('.record-details #form-message').hide();

        $('#filter-customers button').prop('disabled', false);
        $('#filter-customers .selected').removeClass('selected');
        $('#filter-customers .results').css('color', '');
    }

    /**
     * Display a customer record into the form.
     *
     * @param {Object} customer Contains the customer record data.
     */
    function display(customer) {
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
        $('#language').val(customer.language || 'english');

        $('#customer-appointments').empty();

        if (!customer.appointments.length) {
            $('<p/>', {
                'text': App.Lang.no_records_found
            }).appendTo('#customer-appointments');
        }

        customer.appointments.forEach(function (appointment) {
            if (
                App.Vars.role_slug === Backend.DB_SLUG_PROVIDER &&
                parseInt(appointment.id_users_provider) !== App.Vars.user_id
            ) {
                return;
            }

            if (
                App.Vars.role_slug === Backend.DB_SLUG_SECRETARY &&
                App.Vars.secretary_providers.indexOf(appointment.id_users_provider) === -1
            ) {
                return;
            }

            const start = App.Utils.Date.format(
                moment(appointment.start_datetime).toDate(),
                App.Vars.date_format,
                App.Vars.time_format,
                true
            );

            const end = App.Utils.Date.format(
                moment(appointment.end_datetime).toDate(),
                App.Vars.date_format,
                App.Vars.time_format,
                true
            );

            $('<div/>', {
                'class': 'appointment-row',
                'data-id': appointment.id,
                'html': [
                    // Service - Provider

                    $('<a/>', {
                        'href': App.Utils.Url.siteUrl(`backend/index/${appointment.hash}`),
                        'html': [
                            $('<i/>', {
                                'class': 'fas fa-edit me-1'
                            }),
                            $('<strong/>', {
                                'text':
                                    appointment.service.name +
                                    ' - ' +
                                    appointment.provider.first_name +
                                    ' ' +
                                    appointment.provider.last_name
                            }),
                            $('<br/>')
                        ]
                    }),

                    // Start

                    $('<small/>', {
                        'text': start
                    }),
                    $('<br/>'),

                    // End

                    $('<small/>', {
                        'text': end
                    }),
                    $('<br/>'),

                    // Timezone

                    $('<small/>', {
                        'text': App.Vars.timezones[appointment.provider.timezone]
                    })
                ]
            }).appendTo('#customer-appointments');
        });
    }

    /**
     * Filter customer records.
     *
     * @param {String} keyword This keyword string is used to filter the customer records.
     * @param {Number} selectId Optional, if set then after the filter operation the record with the given
     * ID will be selected (but not displayed).
     * @param {Boolean} show Optional (false), if true then the selected record will be displayed on the form.
     */
    function filter(keyword, selectId = null, show = false) {
        App.Http.Customers.search(keyword, filterLimit).then((response) => {
            filterResults = response;

            $('#filter-customers .results').empty();

            response.forEach(
                function (customer) {
                    $('#filter-customers .results').append(getFilterHtml(customer)).append($('<hr/>'));
                }.bind(this)
            );

            if (!response.length) {
                $('#filter-customers .results').append(
                    $('<em/>', {
                        'text': App.Lang.no_records_found
                    })
                );
            } else if (response.length === filterLimit) {
                $('<button/>', {
                    'type': 'button',
                    'class': 'btn btn-outline-secondary w-100 load-more text-center',
                    'text': App.Lang.load_more,
                    'click': function () {
                        filterLimit += 20;
                        filter(keyword, selectId, show);
                    }.bind(this)
                }).appendTo('#filter-customers .results');
            }

            if (selectId) {
                select(selectId, show);
            }
        });
    }

    /**
     * Get the filter results row HTML code.
     *
     * @param {Object} customer Contains the customer data.
     *
     * @return {String} Returns the record HTML code.
     */
    function getFilterHtml(customer) {
        const name = customer.first_name + ' ' + customer.last_name;

        let info = customer.email;

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
                $('<br/>')
            ]
        });
    }

    /**
     * Select a specific record from the current filter results.
     *
     * If the customer id does not exist in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} show Optional (false), if true then the method will display the record
     * on the form.
     */
    function select(id, show = false) {
        $('#filter-customers .selected').removeClass('selected');

        $('#filter-customers .entry[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const customer = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(id);
            });

            display(customer);

            $('#edit-customer, #delete-customer').prop('disabled', false);
        }
    }

    function init() {
        resetForm();
        bindEventHandlers();
        filter('');
    }

    document.addEventListener('DOMContentLoaded', init);

    return {
        filter,
        save,
        remove,
        getFilterHtml,
        resetForm,
        select
    };
})();
