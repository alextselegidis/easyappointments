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
     * Providers Helper
     *
     * This class contains the Providers helper class declaration, along with the "Providers" tab
     * event handlers. By dividing the backend/users tab functionality into separate files
     * it is easier to maintain the code.
     *
     * @class ProvidersHelper
     */
    var ProvidersHelper = function () {
        this.filterResults = {}; // Store the results for later use.
        this.filterLimit = 20;
    };

    /**
     * Bind the event handlers for the backend/users "Providers" tab.
     */
    ProvidersHelper.prototype.bindEventHandlers = function () {
        /**
         * Event: Filter Providers Form "Submit"
         *
         * Filter the provider records with the given key string.
         */
        $('#providers').on('submit', '#filter-providers form', function () {
            var key = $('#filter-providers .key').val();
            $('.selected').removeClass('selected');
            this.resetForm();
            this.filter(key);
            return false;
        }.bind(this));

        /**
         * Event: Clear Filter Button "Click"
         */
        $('#providers').on('click', '#filter-providers .clear', function () {
            this.filter('');
            $('#filter-providers .key').val('');
            this.resetForm();
        }.bind(this));

        /**
         * Event: Filter Provider Row "Click"
         *
         * Display the selected provider data to the user.
         */
        $('#providers').on('click', '.provider-row', function (e) {
            if ($('#filter-providers .filter').prop('disabled')) {
                $('#filter-providers .results').css('color', '#AAA');
                return; // Exit because we are currently on edit mode.
            }

            var providerId = $(e.currentTarget).attr('data-id');
            var provider = {};

            $.each(this.filterResults, function (index, item) {
                if (item.id === providerId) {
                    provider = item;
                    return false;
                }
            });

            this.display(provider);
            $('#filter-providers .selected').removeClass('selected');
            $(e.currentTarget).addClass('selected');
            $('#edit-provider, #delete-provider').prop('disabled', false);
        }.bind(this));

        /**
         * Event: Add New Provider Button "Click"
         */
        $('#providers').on('click', '#add-provider', function () {
            this.resetForm();
            $('#filter-providers button').prop('disabled', true);
            $('#filter-providers .results').css('color', '#AAA');
            $('#providers .add-edit-delete-group').hide();
            $('#providers .save-cancel-group').show();
            $('#providers .record-details').find('input, textarea').prop('disabled', false);
            $('#providers .record-details').find('select').prop('disabled', false);
            $('#provider-password, #provider-password-confirm').addClass('required');
            $('#provider-notifications').prop('disabled', false);
            $('#providers').find('.add-break, .edit-break, .delete-break, .add-extra-periods, .edit-extra, .delete-extra, #reset-working-plan').prop('disabled', false);
            $('#provider-services input:checkbox').prop('disabled', false);

            // Apply default working plan
            BackendUsers.wp.setup(GlobalVariables.workingPlan);
            BackendUsers.wp.timepickers(false);
        }.bind(this));

        /**
         * Event: Edit Provider Button "Click"
         */
        $('#providers').on('click', '#edit-provider', function () {
            $('#providers .add-edit-delete-group').hide();
            $('#providers .save-cancel-group').show();
            $('#filter-providers button').prop('disabled', true);
            $('#filter-providers .results').css('color', '#AAA');
            $('#providers .record-details').find('input, textarea').prop('disabled', false);
            $('#providers .record-details').find('select').prop('disabled', false);
            $('#provider-password, #provider-password-confirm').removeClass('required');
            $('#provider-notifications').prop('disabled', false);
            $('#provider-services input:checkbox').prop('disabled', false);
            $('#providers').find('.add-break, .edit-break, .delete-break, .add-extra-periods, .edit-extra, .delete-extra, #reset-working-plan').prop('disabled', false);
            $('#providers input:checkbox').prop('disabled', false);
            BackendUsers.wp.timepickers(false);
        });

        /**
         * Event: Delete Provider Button "Click"
         */
        $('#providers').on('click', '#delete-provider', function () {
            var providerId = $('#provider-id').val();

            var buttons = [
                {
                    text: EALang.delete,
                    click: function () {
                        this.delete(providerId);
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

            GeneralFunctions.displayMessageBox(EALang.delete_provider,
                EALang.delete_record_prompt, buttons);
        }.bind(this));

        /**
         * Event: Save Provider Button "Click"
         */
        $('#providers').on('click', '#save-provider', function () {
            var provider = {
                first_name: $('#provider-first-name').val(),
                last_name: $('#provider-last-name').val(),
                email: $('#provider-email').val(),
                mobile_number: $('#provider-mobile-number').val(),
                phone_number: $('#provider-phone-number').val(),
                address: $('#provider-address').val(),
                city: $('#provider-city').val(),
                state: $('#provider-state').val(),
                zip_code: $('#provider-zip-code').val(),
                notes: $('#provider-notes').val(),
                timezone: $('#provider-timezone').val(),
                settings: {
                    username: $('#provider-username').val(),
                    working_plan: JSON.stringify(BackendUsers.wp.get()),
                    extra_working_plan: JSON.stringify(BackendUsers.wp.getExtraWP()),
                    notifications: $('#provider-notifications').hasClass('active'),
                    calendar_view: $('#provider-calendar-view').val()
                }
            };

            // Include provider services.
            provider.services = [];
            $('#provider-services input:checkbox').each(function () {
                if ($(this).prop('checked')) {
                    provider.services.push($(this).attr('data-id'));
                }
            });

            // Include password if changed.
            if ($('#provider-password').val() !== '') {
                provider.settings.password = $('#provider-password').val();
            }

            // Include id if changed.
            if ($('#provider-id').val() !== '') {
                provider.id = $('#provider-id').val();
            }

            if (!this.validate()) {
                return;
            }

            this.save(provider);
        }.bind(this));

        /**
         * Event: Cancel Provider Button "Click"
         *
         * Cancel add or edit of an provider record.
         */
        $('#providers').on('click', '#cancel-provider', function () {
            var id = $('#filter-providers .selected').attr('data-id');
            this.resetForm();
            if (id) {
                this.select(id, true);
            }
        }.bind(this));

        /**
         * Event: Display Provider Details "Click"
         */
        $('#providers').on('click', '.display-details', function () {
            $('#providers .switch-view .current').removeClass('current');
            $(this).addClass('current');
            $('.working-plan-view').hide('fade', function () {
                $('.details-view').show('fade');
            });
        });

        /**
         * Event: Display Provider Working Plan "Click"
         */
        $('#providers').on('click', '.display-working-plan', function () {
            $('#providers .switch-view .current').removeClass('current');
            $(this).addClass('current');
            $('.details-view').hide('fade', function () {
                $('.working-plan-view').show('fade');
            });
        });

        /**
         * Event: Reset Working Plan Button "Click".
         */
        $('#providers').on('click', '#reset-working-plan', function () {
            $('.breaks tbody').empty();
            $('.extra-periods tbody').empty();
            $('.work-start, .work-end').val('');
            BackendUsers.wp.setup(GlobalVariables.workingPlan);
            BackendUsers.wp.timepickers(false);
        });
    };

    /**
     * Save provider record to database.
     *
     * @param {Object} provider Contains the admin record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    ProvidersHelper.prototype.save = function (provider) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_provider';
        var data = {
            csrfToken: GlobalVariables.csrfToken,
            provider: JSON.stringify(provider)
        };

        $.post(url, data)
            .done(function (response) {
                Backend.displayNotification(EALang.provider_saved);
                this.resetForm();
                $('#filter-providers .key').val('');
                this.filter('', response.id, true);
            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Delete a provider record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    ProvidersHelper.prototype.delete = function (id) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_provider';
        var data = {
            csrfToken: GlobalVariables.csrfToken,
            provider_id: id
        };

        $.post(url, data)
            .done(function () {
                Backend.displayNotification(EALang.provider_deleted);
                this.resetForm();
                this.filter($('#filter-providers .key').val());
            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Validates a provider record.
     *
     * @return {Boolean} Returns the validation result.
     */
    ProvidersHelper.prototype.validate = function () {
        $('#providers .has-error').removeClass('has-error');

        try {
            // Validate required fields.
            var missingRequired = false;
            $('#providers .required').each(function () {
                if (!$(this).val()) {
                    $(this).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });
            if (missingRequired) {
                throw new Error(EALang.fields_are_required);
            }

            // Validate passwords.
            if ($('#provider-password').val() !== $('#provider-password-confirm').val()) {
                $('#provider-password, #provider-password-confirm').closest('.form-group').addClass('has-error');
                throw new Error(EALang.passwords_mismatch);
            }

            if ($('#provider-password').val().length < BackendUsers.MIN_PASSWORD_LENGTH
                && $('#provider-password').val() !== '') {
                $('#provider-password, #provider-password-confirm').closest('.form-group').addClass('has-error');
                throw new Error(EALang.password_length_notice.replace('$number', BackendUsers.MIN_PASSWORD_LENGTH));
            }

            // Validate user email.
            if (!GeneralFunctions.validateEmail($('#provider-email').val())) {
                $('#provider-email').closest('.form-group').addClass('has-error');
                throw new Error(EALang.invalid_email);
            }

            // Check if username exists
            if ($('#provider-username').attr('already-exists') === 'true') {
                $('#provider-username').closest('.form-group').addClass('has-error');
                throw new Error(EALang.username_already_exists);
            }

            return true;
        } catch (error) {
            $('#providers .form-message')
                .addClass('alert-danger')
                .text(error.message)
                .show();
            return false;
        }
    };

    /**
     * Resets the admin tab form back to its initial state.
     */
    ProvidersHelper.prototype.resetForm = function () {
        $('#filter-providers .selected').removeClass('selected');
        $('#filter-providers button').prop('disabled', false);
        $('#filter-providers .results').css('color', '');

        $('#providers .add-edit-delete-group').show();
        $('#providers .save-cancel-group').hide();
        $('#providers .record-details h3 a').remove();
        $('#providers .record-details').find('input, textarea').prop('disabled', true);
        $('#providers .record-details').find('select').prop('disabled', true);
        $('#providers .form-message').hide();
        $('#provider-notifications').removeClass('active');
        $('#provider-notifications').prop('disabled', true);
        $('#provider-services input:checkbox').prop('disabled', true);
        $('#providers .add-break, .add-extra-periods, #reset-working-plan').prop('disabled', true);
        BackendUsers.wp.timepickers(true);
        $('#providers .working-plan input:text').timepicker('destroy');
        $('#providers .working-plan input:checkbox').prop('disabled', true);
        $('.breaks').find('.edit-break, .delete-break').prop('disabled', true);
        $('.extra-periods').find('.edit-extra, .delete-extra').prop('disabled', true);

        $('#edit-provider, #delete-provider').prop('disabled', true);
        $('#providers .record-details').find('input, textarea').val('');
        $('#provider-services input:checkbox').prop('checked', false);
        $('#provider-services a').remove();
        $('#providers .working-plan tbody').empty();
        $('#providers .breaks tbody').empty();
        $('#providers .extra-periods tbody').empty();
    };

    /**
     * Display a provider record into the admin form.
     *
     * @param {Object} provider Contains the provider record data.
     */
    ProvidersHelper.prototype.display = function (provider) {
        $('#provider-id').val(provider.id);
        $('#provider-first-name').val(provider.first_name);
        $('#provider-last-name').val(provider.last_name);
        $('#provider-email').val(provider.email);
        $('#provider-mobile-number').val(provider.mobile_number);
        $('#provider-phone-number').val(provider.phone_number);
        $('#provider-address').val(provider.address);
        $('#provider-city').val(provider.city);
        $('#provider-state').val(provider.state);
        $('#provider-zip-code').val(provider.zip_code);
        $('#provider-notes').val(provider.notes);
        $('#provider-timezone').val(provider.timezone);

        $('#provider-username').val(provider.settings.username);
        $('#provider-calendar-view').val(provider.settings.calendar_view);
        if (provider.settings.notifications === '1') {
            $('#provider-notifications').addClass('active');
        } else {
            $('#provider-notifications').removeClass('active');
        }

        // Add dedicated provider link.
        var dedicatedUrl = GlobalVariables.baseUrl + '/index.php?provider=' + encodeURIComponent(provider.id);
        var linkHtml = '<a href="' + dedicatedUrl + '"><i class="fas fa-link"></i></a>';
        $('#providers .details-view h3')
            .find('a')
            .remove()
            .end()
            .append(linkHtml);

        $('#provider-services a').remove();
        $('#provider-services input:checkbox').prop('checked', false);
        $.each(provider.services, function (index, serviceId) {
            $('#provider-services input:checkbox').each(function () {
                if (Number($(this).attr('data-id')) === Number(serviceId)) {
                    $(this).prop('checked', true);
                    // Add dedicated service-provider link.
                    dedicatedUrl = GlobalVariables.baseUrl + '/index.php?provider=' + encodeURIComponent(provider.id)
                        + '&service=' + encodeURIComponent(serviceId);
                    linkHtml = '<a href="' + dedicatedUrl + '"><i class="fas fa-link"></i></a>';
                    $(this).parent().append(linkHtml);
                }
            });
        });

        // Display working plan
        var workingPlan = $.parseJSON(provider.settings.working_plan);
        BackendUsers.wp.setup(workingPlan);
        $('.breaks').find('.edit-break, .delete-break').prop('disabled', true);
        $('#providers .extra-periods tbody').empty();
        var extraWorkingPlan = $.parseJSON(provider.settings.extra_working_plan);
        BackendUsers.wp.setupExtraPeriods(extraWorkingPlan);
        $('.extra-periods').find('.edit-extra, .delete-extra').prop('disabled', true);
        $('#providers .working-plan input:checkbox').prop('disabled', true);
    };

    /**
     * Filters provider records depending a string key.
     *
     * @param {string} key This is used to filter the provider records of the database.
     * @param {numeric} selectId Optional, if set, when the function is complete a result row can be set as selected.
     * @param {bool} display Optional (false), if true the selected record will be also displayed.
     */
    ProvidersHelper.prototype.filter = function (key, selectId, display) {
        display = display || false;

        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_providers';
        var data = {
            csrfToken: GlobalVariables.csrfToken,
            key: key,
            limit: this.filterLimit
        };

        $.post(url, data)
            .done(function (response) {
                this.filterResults = response;

                $('#filter-providers .results').html('');
                $.each(response, function (index, provider) {
                    var html = this.getFilterHtml(provider);
                    $('#filter-providers .results').append(html);
                }.bind(this));

                if (!response.length) {
                    $('#filter-providers .results').html('<em>' + EALang.no_records_found + '</em>')
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
                        .appendTo('#filter-providers .results');
                }

                if (selectId) {
                    this.select(selectId, display);
                }
            }.bind(this))
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Get an provider row html code that is going to be displayed on the filter results list.
     *
     * @param {Object} provider Contains the provider record data.
     *
     * @return {String} The html code that represents the record on the filter results list.
     */
    ProvidersHelper.prototype.getFilterHtml = function (provider) {
        var name = provider.first_name + ' ' + provider.last_name,
            info = provider.email;

        info = provider.mobile_number ? info + ', ' + provider.mobile_number : info;

        info = provider.phone_number ? info + ', ' + provider.phone_number : info;

        var html =
            '<div class="provider-row entry" data-id="' + provider.id + '">' +
            '<strong>' + name + '</strong><br>' +
            info + '<br>' +
            '</div><hr>';

        return html;
    };

    /**
     * Initialize the editable functionality to the break day table cells.
     *
     * @param {Object} $selector The cells to be initialized.
     */
    ProvidersHelper.prototype.editableBreakDay = function ($selector) {
        var weekDays = {};
        weekDays[EALang.monday] = 'Monday';
        weekDays[EALang.tuesday] = 'Tuesday';
        weekDays[EALang.wednesday] = 'Wednesday';
        weekDays[EALang.thursday] = 'Thursday';
        weekDays[EALang.friday] = 'Friday';
        weekDays[EALang.saturday] = 'Saturday';
        weekDays[EALang.sunday] = 'Sunday';


        $selector.editable(function (value, settings) {
            return value;
        }, {
            type: 'select',
            data: weekDays,
            event: 'edit',
            height: '30px',
            submit: '<button type="button" class="d-none submit-editable">Submit</button>',
            cancel: '<button type="button" class="d-none cancel-editable">Cancel</button>',
            onblur: 'ignore',
            onreset: function (settings, td) {
                if (!BackendUsers.enableCancel) {
                    return false; // disable ESC button
                }
            },
            onsubmit: function (settings, td) {
                if (!BackendUsers.enableSubmit) {
                    return false; // disable Enter button
                }
            }
        });
    };

    /**
     * Initialize the editable functionality to the break time table cells.
     *
     * @param {jQuery} $selector The cells to be initialized.
     */
    ProvidersHelper.prototype.editableBreakTime = function ($selector) {
        $selector.editable(function (value, settings) {
            // Do not return the value because the user needs to press the "Save" button.
            return value;
        }, {
            event: 'edit',
            height: '25px',
            submit: '<button type="button" class="d-none submit-editable">Submit</button>',
            cancel: '<button type="button" class="d-none cancel-editable">Cancel</button>',
            onblur: 'ignore',
            onreset: function (settings, td) {
                if (!BackendUsers.enableCancel) {
                    return false; // disable ESC button
                }
            },
            onsubmit: function (settings, td) {
                if (!BackendUsers.enableSubmit) {
                    return false; // disable Enter button
                }
            }
        });
    };

    /**
     * Select and display a providers filter result on the form.
     *
     * @param {Number} id Record id to be selected.
     * @param {Boolean} display Optional (false), if true the record will be displayed on the form.
     */
    ProvidersHelper.prototype.select = function (id, display) {
        display = display || false;

        // Select record in filter results.
        $('#filter-providers .provider-row').each(function () {
            if (Number($(this).attr('data-id')) === Number(id)) {
                $(this).addClass('selected');
                return false;
            }
        });

        // Display record in form (if display = true).
        if (display) {
            $.each(this.filterResults, function (index, provider) {
                if (Number(provider.id) === Number(id)) {
                    this.display(provider);
                    $('#edit-provider, #delete-provider').prop('disabled', false);
                    return false;
                }
            }.bind(this));
        }
    };

    window.ProvidersHelper = ProvidersHelper;

})();
