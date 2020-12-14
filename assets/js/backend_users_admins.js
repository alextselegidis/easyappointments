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
     * This class contains the Admins helper class declaration, along with the "Admins" tab
     * event handlers. By dividing the backend/users tab functionality into separate files
     * it is easier to maintain the code.
     *
     * @class AdminsHelper
     */
    var AdminsHelper = function () {
        this.filterResults = []; // Store the results for later use.
        this.filterLimit = 20;
    };

    /**
     * Bind the event handlers for the backend/users "Admins" tab.
     */
    AdminsHelper.prototype.bindEventHandlers = function () {
        /**
         * Event: Filter Admins Form "Submit"
         *
         * Filter the admin records with the given key string.
         *
         * @param {jQuery.Event} event
         */
        $('#admins').on('submit', '#filter-admins form', function (event) {
            event.preventDefault();
            var key = $('#filter-admins .key').val();
            $('#filter-admins .selected').removeClass('selected');
            this.resetForm();
            this.filter(key);
        }.bind(this));

        /**
         * Event: Clear Filter Results Button "Click"
         */
        $('#admins').on('click', '#filter-admins .clear', function () {
            this.filter('');
            $('#filter-admins .key').val('');
            this.resetForm();
        }.bind(this));

        /**
         * Event: Filter Admin Row "Click"
         *
         * Display the selected admin data to the user.
         */
        $('#admins').on('click', '.admin-row', function (event) {
            if ($('#filter-admins .filter').prop('disabled')) {
                $('#filter-admins .results').css('color', '#AAA');
                return; // exit because we are currently on edit mode
            }

            var adminId = $(event.currentTarget).attr('data-id');

            var admin = this.filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(adminId);
            });

            this.display(admin);
            $('#filter-admins .selected').removeClass('selected');
            $(event.currentTarget).addClass('selected');
            $('#edit-admin, #delete-admin').prop('disabled', false);
        }.bind(this));

        /**
         * Event: Add New Admin Button "Click"
         */
        $('#admins').on('click', '#add-admin', function () {
            this.resetForm();
            $('#admins .add-edit-delete-group').hide();
            $('#admins .save-cancel-group').show();
            $('#admins .record-details').find('input, textarea').prop('disabled', false);
            $('#admins .record-details').find('select').prop('disabled', false);
            $('#admin-password, #admin-password-confirm').addClass('required');
            $('#filter-admins button').prop('disabled', true);
            $('#filter-admins .results').css('color', '#AAA');
        }.bind(this));

        /**
         * Event: Edit Admin Button "Click"
         */
        $('#admins').on('click', '#edit-admin', function () {
            $('#admins .add-edit-delete-group').hide();
            $('#admins .save-cancel-group').show();
            $('#admins .record-details').find('input, textarea').prop('disabled', false);
            $('#admins .record-details').find('select').prop('disabled', false);
            $('#admin-password, #admin-password-confirm').removeClass('required');
            $('#filter-admins button').prop('disabled', true);
            $('#filter-admins .results').css('color', '#AAA');
        });

        /**
         * Event: Delete Admin Button "Click"
         */
        $('#admins').on('click', '#delete-admin', function () {
            var adminId = $('#admin-id').val();

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
                        this.delete(adminId);
                        $('#message-box').dialog('close');
                    }.bind(this)
                }
            ];

            GeneralFunctions.displayMessageBox(EALang.delete_admin, EALang.delete_record_prompt, buttons);
        }.bind(this));

        /**
         * Event: Save Admin Button "Click"
         */
        $('#admins').on('click', '#save-admin', function () {
            var admin = {
                first_name: $('#admin-first-name').val(),
                last_name: $('#admin-last-name').val(),
                email: $('#admin-email').val(),
                mobile_number: $('#admin-mobile-number').val(),
                phone_number: $('#admin-phone-number').val(),
                address: $('#admin-address').val(),
                city: $('#admin-city').val(),
                state: $('#admin-state').val(),
                zip_code: $('#admin-zip-code').val(),
                notes: $('#admin-notes').val(),
                timezone: $('#admin-timezone').val(),
                settings: {
                    username: $('#admin-username').val(),
                    notifications: $('#admin-notifications').prop('checked'),
                    calendar_view: $('#admin-calendar-view').val()
                }
            };

            // Include password if changed.
            if ($('#admin-password').val() !== '') {
                admin.settings.password = $('#admin-password').val();
            }

            // Include id if changed.
            if ($('#admin-id').val() !== '') {
                admin.id = $('#admin-id').val();
            }

            if (!this.validate()) {
                return;
            }

            this.save(admin);
        }.bind(this));

        /**
         * Event: Cancel Admin Button "Click"
         *
         * Cancel add or edit of an admin record.
         */
        $('#admins').on('click', '#cancel-admin', function () {
            var id = $('#admin-id').val();
            this.resetForm();
            if (id) {
                this.select(id, true);
            }
        }.bind(this));
    };

    /**
     * Remove the previously registered event handlers.
     */
    AdminsHelper.prototype.unbindEventHandlers = function () {
        $('#admins')
            .off('submit', '#filter-admins form')
            .off('click', '#filter-admins .clear')
            .off('click', '.admin-row')
            .off('click', '#add-admin')
            .off('click', '#edit-admin')
            .off('click', '#delete-admin')
            .off('click', '#save-admin')
            .off('click', '#cancel-admin');
    };

    /**
     * Save admin record to database.
     *
     * @param {Object} admin Contains the admin record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    AdminsHelper.prototype.save = function (admin) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_admin';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            admin: JSON.stringify(admin)
        };

        $.post(url, data)
            .done(function (response) {
                Backend.displayNotification(EALang.admin_saved);
                this.resetForm();
                $('#filter-admins .key').val('');
                this.filter('', response.id, true);
            }.bind(this));
    };

    /**
     * Delete an admin record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    AdminsHelper.prototype.delete = function (id) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_admin';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            admin_id: id
        };

        $.post(url, data)
            .done(function (response) {
                Backend.displayNotification(EALang.admin_deleted);
                this.resetForm();
                this.filter($('#filter-admins .key').val());
            }.bind(this));
    };

    /**
     * Validates an admin record.
     *
     * @return {Boolean} Returns the validation result.
     */
    AdminsHelper.prototype.validate = function () {
        $('#admins .has-error').removeClass('has-error');

        try {
            // Validate required fields.
            var missingRequired = false;

            $('#admins .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error('Fields with * are  required.');
            }

            // Validate passwords.
            if ($('#admin-password').val() !== $('#admin-password-confirm').val()) {
                $('#admin-password, #admin-password-confirm').closest('.form-group').addClass('has-error');
                throw new Error(EALang.passwords_mismatch);
            }

            if ($('#admin-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#admin-password').val() !== '') {
                $('#admin-password, #admin-password-confirm').closest('.form-group').addClass('has-error');
                throw new Error(EALang.password_length_notice.replace('$number', BackendUsers.MIN_PASSWORD_LENGTH));
            }

            // Validate user email.
            if (!GeneralFunctions.validateEmail($('#admin-email').val())) {
                $('#admin-email').closest('.form-group').addClass('has-error');
                throw new Error(EALang.invalid_email);
            }

            // Check if username exists
            if ($('#admin-username').attr('already-exists') === 'true') {
                $('#admin-username').closest('.form-group').addClass('has-error');
                throw new Error(EALang.username_already_exists);
            }

            return true;
        } catch (error) {
            $('#admins .form-message')
                .addClass('alert-danger')
                .text(error.message)
                .show();
            return false;
        }
    };

    /**
     * Resets the admin form back to its initial state.
     */
    AdminsHelper.prototype.resetForm = function () {
        $('#filter-admins .selected').removeClass('selected');
        $('#filter-admins button').prop('disabled', false);
        $('#filter-admins .results').css('color', '');

        $('#admins .add-edit-delete-group').show();
        $('#admins .save-cancel-group').hide();
        $('#admins .record-details')
            .find('input, select, textarea')
            .val('')
            .prop('disabled', true);
        $('#admins .record-details #admin-calendar-view').val('default');
        $('#admins .record-details #admin-timezone').val('UTC');
        $('#edit-admin, #delete-admin').prop('disabled', true);

        $('#admins .has-error').removeClass('has-error');
        $('#admins .form-message').hide();
    };

    /**
     * Display a admin record into the admin form.
     *
     * @param {Object} admin Contains the admin record data.
     */
    AdminsHelper.prototype.display = function (admin) {
        $('#admin-id').val(admin.id);
        $('#admin-first-name').val(admin.first_name);
        $('#admin-last-name').val(admin.last_name);
        $('#admin-email').val(admin.email);
        $('#admin-mobile-number').val(admin.mobile_number);
        $('#admin-phone-number').val(admin.phone_number);
        $('#admin-address').val(admin.address);
        $('#admin-city').val(admin.city);
        $('#admin-state').val(admin.state);
        $('#admin-zip-code').val(admin.zip_code);
        $('#admin-notes').val(admin.notes);
        $('#admin-timezone').val(admin.timezone);

        $('#admin-username').val(admin.settings.username);
        $('#admin-calendar-view').val(admin.settings.calendar_view);
        $('#admin-notifications').prop('checked', Boolean(Number(admin.settings.notifications)));
    };

    /**
     * Filters admin records depending a key string.
     *
     * @param {String} key This string is used to filter the admin records of the database.
     * @param {Number} selectId (OPTIONAL = undefined) This record id will be selected when
     * the filter operation is finished.
     * @param {Boolean} display (OPTIONAL = false) If true the selected record data are going
     * to be displayed on the details column (requires a selected record though).
     */
    AdminsHelper.prototype.filter = function (key, selectId, display) {
        display = display || false;

        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_admins';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            key: key,
            limit: this.filterLimit
        };

        $.post(url, data)
            .done(function (response) {
                this.filterResults = response;

                $('#filter-admins .results').empty();

                response.forEach(function (admin) {
                    $('#filter-admins .results')
                        .append(this.getFilterHtml(admin))
                        .append($('<hr/>'));
                }.bind(this));

                if (!response.length) {
                    $('#filter-admins .results').append(
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
                        .appendTo('#filter-admins .results');
                }

                if (selectId) {
                    this.select(selectId, display);
                }
            }.bind(this));
    };

    /**
     * Get an admin row html code that is going to be displayed on the filter results list.
     *
     * @param {Object} admin Contains the admin record data.
     *
     * @return {String} The html code that represents the record on the filter results list.
     */
    AdminsHelper.prototype.getFilterHtml = function (admin) {
        var name = admin.first_name + ' ' + admin.last_name;

        var info = admin.email;

        info = admin.mobile_number ? info + ', ' + admin.mobile_number : info;

        info = admin.phone_number ? info + ', ' + admin.phone_number : info;

        return $('<div/>', {
            'class': 'admin-row entry',
            'data-id': admin.id,
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
     * Select a specific record from the current filter results. If the admin id does not exist
     * in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} display Optional (false), if true then the method will display the record
     * on the form.
     */
    AdminsHelper.prototype.select = function (id, display) {
        display = display || false;

        $('#filter-admins .selected').removeClass('selected');

        $('#filter-admins .admin-row[data-id="' + id + '"]').addClass('selected');

        if (display) {
            var admin = this.filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(id);
            });

            this.display(admin);

            $('#edit-admin, #delete-admin').prop('disabled', false);
        }
    };

    window.AdminsHelper = AdminsHelper;

})();
