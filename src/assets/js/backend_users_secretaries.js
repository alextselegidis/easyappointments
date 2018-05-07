/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
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
    };

    /**
     * Bind the event handlers for the backend/users "Secretaries" tab.
     */
    SecretariesHelper.prototype.bindEventHandlers = function () {
        /**
         * Event: Filter Secretaries Form "Submit"
         *
         * Filter the secretary records with the given key string.
         */
        $('#secretaries').on('submit', '#filter-secretaries form', function () {
            var key = $('#filter-secretaries .key').val();
            $('#filter-secretaries .selected').removeClass('selected');
            this.resetForm();
            this.filter(key);
            return false;
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
        $('#secretaries').on('click', '.secretary-row', function (e) {
            if ($('#filter-secretaries .filter').prop('disabled')) {
                $('#filter-secretaries .results').css('color', '#AAA');
                return; // exit because we are currently on edit mode
            }

            var secretaryId = $(e.currentTarget).attr('data-id');
            var secretary = {};

            $.each(this.filterResults, function (index, item) {
                if (item.id === secretaryId) {
                    secretary = item;
                    return false;
                }
            });

            this.display(secretary);

            $('#filter-secretaries .selected').removeClass('selected');
            $(e.currentTarget).addClass('selected');
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
            $('#secretaries .record-details').find('input, textarea').prop('readonly', false);
            $('#secretaries .record-details').find('select').prop('disabled', false);
            $('#secretary-password, #secretary-password-confirm').addClass('required');
            $('#secretary-notifications').prop('disabled', false);
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
            $('#secretaries .record-details').find('input, textarea').prop('readonly', false);
            $('#secretaries .record-details').find('select').prop('disabled', false);
            $('#secretary-password, #secretary-password-confirm').removeClass('required');
            $('#secretary-notifications').prop('disabled', false);
            $('#secretary-providers input:checkbox').prop('disabled', false);
        });

        /**
         * Event: Delete Secretary Button "Click"
         */
        $('#secretaries').on('click', '#delete-secretary', function () {
            var secretaryId = $('#secretary-id').val();
            var buttons = [
                {
                    text: EALang.delete,
                    click: function () {
                        this.delete(secretaryId);
                        $('#message_box').dialog('close');
                    }.bind(this)
                },
                {
                    text: EALang.cancel,
                    click: function () {
                        $('#message_box').dialog('close');
                    }
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
                settings: {
                    username: $('#secretary-username').val(),
                    notifications: $('#secretary-notifications').hasClass('active'),
                    calendar_view: $('#secretary-calendar-view').val()
                }
            };

            // Include secretary services.
            secretary.providers = [];
            $('#secretary-providers input:checkbox').each(function () {
                if ($(this).prop('checked')) {
                    secretary.providers.push($(this).attr('data-id'));
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
            if (id != '') {
                this.select(id, true);
            }
        }.bind(this));
    };

    /**
     * Save secretary record to database.
     *
     * @param {Object} secretary Contains the admin record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    SecretariesHelper.prototype.save = function (secretary) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_secretary';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            secretary: JSON.stringify(secretary)
        };

        $.post(postUrl, postData, function (response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }
            Backend.displayNotification(EALang.secretary_saved);
            this.resetForm();
            $('#filter-secretaries .key').val('');
            this.filter('', response.id, true);
        }.bind(this), 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Delete a secretary record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    SecretariesHelper.prototype.delete = function (id) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_secretary';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            secretary_id: id
        };

        $.post(postUrl, postData, function (response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }
            Backend.displayNotification(EALang.secretary_deleted);
            this.resetForm();
            this.filter($('#filter-secretaries .key').val());
        }.bind(this), 'json').fail(GeneralFunctions.ajaxFailureHandler);
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
            $('#secretaries .required').each(function () {
                if ($(this).val() == '' || $(this).val() == undefined) {
                    $(this).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });
            if (missingRequired) {
                throw 'Fields with * are  required.';
            }

            // Validate passwords.
            if ($('#secretary-password').val() != $('#secretary-password-confirm').val()) {
                $('#secretary-password, #secretary-password-confirm').closest('.form-group').addClass('has-error');
                throw 'Passwords mismatch!';
            }

            if ($('#secretary-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#secretary-password').val() != '') {
                $('#secretary-password, #secretary-password-confirm').closest('.form-group').addClass('has-error');
                throw 'Password must be at least ' + BackendUsers.MIN_PASSWORD_LENGTH
                + ' characters long.';
            }

            // Validate user email.
            if (!GeneralFunctions.validateEmail($('#secretary-email').val())) {
                $('#secretary-email').closest('.form-group').addClass('has-error');
                throw 'Invalid email address!';
            }

            // Check if username exists
            if ($('#secretary-username').attr('already-exists') == 'true') {
                $('#secretary-username').closest('.form-group').addClass('has-error');
                throw 'Username already exists.';
            }

            return true;
        } catch (message) {
            $('#secretaries .form-message')
                .addClass('alert-danger')
                .text(message)
                .show();
            return false;
        }
    };

    /**
     * Resets the admin tab form back to its initial state.
     */
    SecretariesHelper.prototype.resetForm = function () {
        $('#secretaries .record-details').find('input, textarea').val('');
        $('#secretaries .add-edit-delete-group').show();
        $('#secretaries .save-cancel-group').hide();
        $('#edit-secretary, #delete-secretary').prop('disabled', true);
        $('#secretaries .record-details').find('input, textarea').prop('readonly', true);
        $('#secretaries .record-details').find('select').prop('disabled', true);
        $('#secretaries .form-message').hide();
        $('#secretary-notifications').removeClass('active');
        $('#secretary-notifications').prop('disabled', true);
        $('#secretary-providers input:checkbox').prop('checked', false);
        $('#secretary-providers input:checkbox').prop('disabled', true);
        $('#secretaries .has-error').removeClass('has-error');

        $('#filter-secretaries .selected').removeClass('selected');
        $('#filter-secretaries button').prop('disabled', false);
        $('#filter-secretaries .results').css('color', '');
    };

    /**
     * Display a secretary record into the admin form.
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

        $('#secretary-username').val(secretary.settings.username);
        $('#secretary-calendar-view').val(secretary.settings.calendar_view);
        if (secretary.settings.notifications == true) {
            $('#secretary-notifications').addClass('active');
        } else {
            $('#secretary-notifications').removeClass('active');
        }

        $('#secretary-providers input:checkbox').prop('checked', false);
        $.each(secretary.providers, function (index, providerId) {
            $('#secretary-providers input:checkbox').each(function () {
                if ($(this).attr('data-id') == providerId) {
                    $(this).prop('checked', true);
                }
            });
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

        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_secretaries';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            key: key
        };

        $.post(postUrl, postData, function (response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            this.filterResults = response;

            $('#filter-secretaries .results').html('');
            $.each(response, function (index, secretary) {
                var html = this.getFilterHtml(secretary);
                $('#filter-secretaries .results').append(html);
            }.bind(this));

            if (response.length == 0) {
                $('#filter-secretaries .results').html('<em>' + EALang.no_records_found + '</em>')
            }

            if (selectId != undefined) {
                this.select(selectId, display);
            }
        }.bind(this), 'json').fail(GeneralFunctions.ajaxFailureHandler);
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

        info = (secretary.mobile_number != '' && secretary.mobile_number != null)
            ? info + ', ' + secretary.mobile_number : info;

        info = (secretary.phone_number != '' && secretary.phone_number != null)
            ? info + ', ' + secretary.phone_number : info;

        var html =
            '<div class="secretary-row entry" data-id="' + secretary.id + '">' +
            '<strong>' + name + '</strong><br>' +
            info + '<br>' +
            '</div><hr>';

        return html;
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

        $('#filter-secretaries .secretary-row').each(function () {
            if ($(this).attr('data-id') == id) {
                $(this).addClass('selected');
                return false;
            }
        });

        if (display) {
            $.each(this.filterResults, function (index, admin) {
                if (admin.id == id) {
                    this.display(admin);
                    $('#edit-secretary, #delete-secretary').prop('disabled', false);
                    return false;
                }
            }.bind(this));
        }
    };

    window.SecretariesHelper = SecretariesHelper;

})();
