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
     * Secretaries Helper
     *
     * This class contains the Secretaries helper class declaration, along with the "Secretaries"
     * tab event handlers. By dividing the backend/users tab functionality into separate files
     * it is easier to maintain the code.
     *
     * @class SecretariesHelper
     */
    var SecretariesHelper = function () {
        this.filterResults = {}; // Store the results for later use.
        this.filterLimit = 20;
    };

    /**
     * Bind the event handlers for the backend/users "Secretaries" tab.
     */
    SecretariesHelper.prototype.bindEventHandlers = function () {
        /**
         * Event: Filter Secretaries Form "Submit"
         *
         * Filter the secretary records with the given key string.
         *
         * @param {jQuery.Event} event
         */
        $('#secretaries').on('submit', '#filter-secretaries form', function (event) {
            event.preventDefault();
            var key = $('#filter-secretaries .key').val();
            $('#filter-secretaries .selected').removeClass('selected');
            this.resetForm();
            this.filter(key);
        }.bind(this));

        /**
         * Event: Clear Filter Results Button "Click"
         */
        $('#secretaries').on('click', '#filter-secretaries .clear', function () {
            this.filter('');
            $('#filter-secretaries .key').val('');
            this.resetForm();
        }.bind(this));

        /**
         * Event: Filter Secretary Row "Click"
         *
         * Display the selected secretary data to the user.
         */
        $('#secretaries').on('click', '.secretary-row', function (event) {
            if ($('#filter-secretaries .filter').prop('disabled')) {
                $('#filter-secretaries .results').css('color', '#AAA');
                return; // exit because we are currently on edit mode
            }

            var secretaryId = $(event.currentTarget).attr('data-id');

            var secretary = this.filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(secretaryId);
            });

            this.display(secretary);

            $('#filter-secretaries .selected').removeClass('selected');
            $(event.currentTarget).addClass('selected');
            $('#edit-secretary, #delete-secretary').prop('disabled', false);
        }.bind(this));

        /**
         * Event: Add New Secretary Button "Click"
         */
        $('#secretaries').on('click', '#add-secretary', function () {
            this.resetForm();
            $('#filter-secretaries button').prop('disabled', true);
            $('#filter-secretaries .results').css('color', '#AAA');

            $('#secretaries .add-edit-delete-group').hide();
            $('#secretaries .save-cancel-group').show();
            $('#secretaries .record-details').find('input, textarea').prop('disabled', false);
            $('#secretaries .record-details').find('select').prop('disabled', false);
            $('#secretary-password, #secretary-password-confirm').addClass('required');
            $('#secretary-providers input:checkbox').prop('disabled', false);
        }.bind(this));

        /**
         * Event: Edit Secretary Button "Click"
         */
        $('#secretaries').on('click', '#edit-secretary', function () {
            $('#filter-secretaries button').prop('disabled', true);
            $('#filter-secretaries .results').css('color', '#AAA');
            $('#secretaries .add-edit-delete-group').hide();
            $('#secretaries .save-cancel-group').show();
            $('#secretaries .record-details').find('input, textarea').prop('disabled', false);
            $('#secretaries .record-details').find('select').prop('disabled', false);
            $('#secretary-password, #secretary-password-confirm').removeClass('required');
            $('#secretary-providers input:checkbox').prop('disabled', false);
        });

        /**
         * Event: Delete Secretary Button "Click"
         */
        $('#secretaries').on('click', '#delete-secretary', function () {
            var secretaryId = $('#secretary-id').val();
            var buttons = [
                {
                    text: EALang.cancel,
                    click: function () {
                        $('#message-box').dialog('close');
                    }
                },
                {
                    text: EALang.delete,
                    click: function () {
                        this.delete(secretaryId);
                        $('#message-box').dialog('close');
                    }.bind(this)
                }
            ];

            GeneralFunctions.displayMessageBox(EALang.delete_secretary,
                EALang.delete_record_prompt, buttons);
        }.bind(this));

        /**
         * Event: Save Secretary Button "Click"
         */
        $('#secretaries').on('click', '#save-secretary', function () {
            var secretary = {
                first_name: $('#secretary-first-name').val(),
                last_name: $('#secretary-last-name').val(),
                email: $('#secretary-email').val(),
                mobile_number: $('#secretary-mobile-number').val(),
                phone_number: $('#secretary-phone-number').val(),
                address: $('#secretary-address').val(),
                city: $('#secretary-city').val(),
                state: $('#secretary-state').val(),
                zip_code: $('#secretary-zip-code').val(),
                notes: $('#secretary-notes').val(),
                timezone: $('#secretary-timezone').val(),
                settings: {
                    username: $('#secretary-username').val(),
                    notifications: $('#secretary-notifications').prop('checked'),
                    calendar_view: $('#secretary-calendar-view').val()
                }
            };

            // Include secretary services.
            secretary.providers = [];
            $('#secretary-providers input:checkbox').each(function (index, checkbox) {
                if ($(checkbox).prop('checked')) {
                    secretary.providers.push($(checkbox).attr('data-id'));
                }
            });

            // Include password if changed.
            if ($('#secretary-password').val() !== '') {
                secretary.settings.password = $('#secretary-password').val();
            }

            // Include ID if changed.
            if ($('#secretary-id').val() !== '') {
                secretary.id = $('#secretary-id').val();
            }

            if (!this.validate()) {
                return;
            }

            this.save(secretary);
        }.bind(this));

        /**
         * Event: Cancel Secretary Button "Click"
         *
         * Cancel add or edit of an secretary record.
         */
        $('#secretaries').on('click', '#cancel-secretary', function () {
            var id = $('#secretary-id').val();
            this.resetForm();
            if (id) {
                this.select(id, true);
            }
        }.bind(this));
    };

    /**
     * Remove the previously registered event handlers.
     */
    SecretariesHelper.prototype.unbindEventHandlers = function () {
        $('#secretaries')
            .off('submit', '#filter-secretaries form')
            .off('click', '#filter-secretaries .clear')
            .off('click', '.secretary-row')
            .off('click', '#add-secretary')
            .off('click', '#edit-secretary')
            .off('click', '#delete-secretary')
            .off('click', '#save-secretary')
            .off('click', '#cancel-secretary');
    };

    /**
     * Save secretary record to database.
     *
     * @param {Object} secretary Contains the secretary record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    SecretariesHelper.prototype.save = function (secretary) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_secretary';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            secretary: JSON.stringify(secretary)
        };

        $.post(url, data)
            .done(function (response) {
                Backend.displayNotification(EALang.secretary_saved);
                this.resetForm();
                $('#filter-secretaries .key').val('');
                this.filter('', response.id, true);
            }.bind(this));
    };

    /**
     * Delete a secretary record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    SecretariesHelper.prototype.delete = function (id) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_secretary';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            secretary_id: id
        };

        $.post(url, data)
            .done(function () {
                Backend.displayNotification(EALang.secretary_deleted);
                this.resetForm();
                this.filter($('#filter-secretaries .key').val());
            }.bind(this));
    };

    /**
     * Validates a secretary record.
     *
     * @return {Boolean} Returns the validation result.
     */
    SecretariesHelper.prototype.validate = function () {
        $('#secretaries .has-error').removeClass('has-error');
        $('#secretaries .form-message').removeClass('alert-danger');

        try {
            // Validate required fields.
            var missingRequired = false;
            $('#secretaries .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });
            if (missingRequired) {
                throw new Error('Fields with * are  required.');
            }

            // Validate passwords.
            if ($('#secretary-password').val() !== $('#secretary-password-confirm').val()) {
                $('#secretary-password, #secretary-password-confirm').closest('.form-group').addClass('has-error');
                throw new Error('Passwords mismatch!');
            }

            if ($('#secretary-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#secretary-password').val() !== '') {
                $('#secretary-password, #secretary-password-confirm').closest('.form-group').addClass('has-error');
                throw new Error('Password must be at least ' + BackendUsers.MIN_PASSWORD_LENGTH
                    + ' characters long.');
            }

            // Validate user email.
            if (!GeneralFunctions.validateEmail($('#secretary-email').val())) {
                $('#secretary-email').closest('.form-group').addClass('has-error');
                throw new Error('Invalid email address!');
            }

            // Check if username exists
            if ($('#secretary-username').attr('already-exists') === 'true') {
                $('#secretary-username').closest('.form-group').addClass('has-error');
                throw new Error('Username already exists.');
            }

            return true;
        } catch (error) {
            $('#secretaries .form-message')
                .addClass('alert-danger')
                .text(error.message)
                .show();
            return false;
        }
    };

    /**
     * Resets the secretary tab form back to its initial state.
     */
    SecretariesHelper.prototype.resetForm = function () {
        $('#filter-secretaries .selected').removeClass('selected');
        $('#filter-secretaries button').prop('disabled', false);
        $('#filter-secretaries .results').css('color', '');

        $('#secretaries .record-details')
            .find('input, select, textarea')
            .val('')
            .prop('disabled', true);
        $('#secretaries .record-details #secretary-calendar-view').val('default');
        $('#secretaries .record-details #secretary-timezone').val('UTC');
        $('#secretaries .add-edit-delete-group').show();
        $('#secretaries .save-cancel-group').hide();
        $('#edit-secretary, #delete-secretary').prop('disabled', true);
        $('#secretaries .form-message').hide();
        $('#secretary-providers input:checkbox').prop('checked', false);
        $('#secretaries .has-error').removeClass('has-error');
    };

    /**
     * Display a secretary record into the secretary form.
     *
     * @param {Object} secretary Contains the secretary record data.
     */
    SecretariesHelper.prototype.display = function (secretary) {
        $('#secretary-id').val(secretary.id);
        $('#secretary-first-name').val(secretary.first_name);
        $('#secretary-last-name').val(secretary.last_name);
        $('#secretary-email').val(secretary.email);
        $('#secretary-mobile-number').val(secretary.mobile_number);
        $('#secretary-phone-number').val(secretary.phone_number);
        $('#secretary-address').val(secretary.address);
        $('#secretary-city').val(secretary.city);
        $('#secretary-state').val(secretary.state);
        $('#secretary-zip-code').val(secretary.zip_code);
        $('#secretary-notes').val(secretary.notes);
        $('#secretary-timezone').val(secretary.timezone);

        $('#secretary-username').val(secretary.settings.username);
        $('#secretary-calendar-view').val(secretary.settings.calendar_view);
        $('#secretary-notifications').prop('checked', Boolean(Number(secretary.settings.notifications)));

        $('#secretary-providers input:checkbox').prop('checked', false);

        secretary.providers.forEach(function (secretaryProviderId) {
            var $checkbox = $('#secretary-providers input[data-id="' + secretaryProviderId + '"]');

            if (!$checkbox.length) {
                return;
            }

            $checkbox.prop('checked', true);
        });
    };

    /**
     * Filters secretary records depending a string key.
     *
     * @param {String} key This is used to filter the secretary records of the database.
     * @param {Numeric} selectId Optional, if provided the given ID will be selected in the filter results
     * (only selected, not displayed).
     * @param {Bool} display Optional (false).
     */
    SecretariesHelper.prototype.filter = function (key, selectId, display) {
        display = display || false;

        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_secretaries';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            key: key,
            limit: this.filterLimit
        };

        $.post(url, data)
            .done(function (response) {
                this.filterResults = response;

                $('#filter-secretaries .results').empty();

                response.forEach(function (secretary) {
                    $('#filter-secretaries .results')
                        .append(this.getFilterHtml(secretary))
                        .append($('<hr/>'));
                }.bind(this));

                if (!response.length) {
                    $('#filter-secretaries .results').append(
                        $('<em/>', {
                            'text': EALang.no_records_found
                        })
                    );
                } else if (response.length === this.filterLimit) {
                    $('<button/>', {
                        'type': 'button',
                        'class': 'btn btn-block btn-outline-secondary load-more text-center',
                        'text': EALang.load_more,
                        'click': function () {
                            this.filterLimit += 20;
                            this.filter(key, selectId, display);
                        }.bind(this)
                    })
                        .appendTo('#filter-secretaries .results');
                }

                if (selectId) {
                    this.select(selectId, display);
                }
            }.bind(this));
    };

    /**
     * Get an secretary row html code that is going to be displayed on the filter results list.
     *
     * @param {Object} secretary Contains the secretary record data.
     *
     * @return {String} The html code that represents the record on the filter results list.
     */
    SecretariesHelper.prototype.getFilterHtml = function (secretary) {
        var name = secretary.first_name + ' ' + secretary.last_name;

        var info = secretary.email;

        info = secretary.mobile_number ? info + ', ' + secretary.mobile_number : info;

        info = secretary.phone_number ? info + ', ' + secretary.phone_number : info;

        return $('<div/>', {
            'class': 'secretary-row entry',
            'data-id': secretary.id,
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
     * Select a specific record from the current filter results. If the secretary id does not exist
     * in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} display Optional (false), if true the method will display the record in the form.
     */
    SecretariesHelper.prototype.select = function (id, display) {
        display = display || false;

        $('#filter-secretaries .selected').removeClass('selected');

        $('#filter-secretaries .secretary-row[data-id="' + id + '"]').addClass('selected');

        if (display) {
            var secretary = this.filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(id);
            }.bind(this));

            this.display(secretary);

            $('#edit-secretary, #delete-secretary').prop('disabled', false);
        }
    };

    window.SecretariesHelper = SecretariesHelper;

})();
