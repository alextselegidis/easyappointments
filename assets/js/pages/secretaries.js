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
 * Secretaries page module.
 *
 * This module contains the methods that are used in the admins page.
 */
App.Pages.Secretaries = (function () {
    const $secretaries = $('#secretaries');
    let filterResults = {};
    let filterLimit = 20;

    /**
     * Bind the event handlers for the backend/users "Secretaries" tab.
     */
    function bindEventHandlers() {
        /**
         * Event: Admin Username "Blur"
         *
         * When the admin leaves the username input field we will need to check if the username
         * is not taken by another record in the system.
         */
        $secretaries.on('blur', '#secretary-username', function () {
            const $input = $(this);

            if ($input.prop('readonly') === true || $input.val() === '') {
                return;
            }

            const secretaryId = $input.parents().eq(2).find('.record-id').val();

            if (!secretaryId) {
                return;
            }

            const username = $input.val();

            App.Http.Account.validateUsername(secretaryId, username).done((response) => {
                if (response.is_valid === 'false') {
                    $input.addClass('is-invalid');
                    $input.attr('already-exists', 'true');
                    $input.parents().eq(3).find('.form-message').text(App.Lang.username_already_exists);
                    $input.parents().eq(3).find('.form-message').show();
                } else {
                    $input.removeClass('is-invalid');
                    $input.attr('already-exists', 'false');
                    if ($input.parents().eq(3).find('.form-message').text() === App.Lang.username_already_exists) {
                        $input.parents().eq(3).find('.form-message').hide();
                    }
                }
            });
        });

        /**
         * Event: Filter Secretaries Form "Submit"
         *
         * Filter the secretary records with the given key string.
         *
         * @param {jQuery.Event} event
         */
        $secretaries.on(
            'submit',
            '#filter-secretaries form',
            function (event) {
                event.preventDefault();
                const key = $('#filter-secretaries .key').val();
                $('#filter-secretaries .selected').removeClass('selected');
                resetForm();
                filter(key);
            }.bind(this)
        );

        /**
         * Event: Filter Secretary Row "Click"
         *
         * Display the selected secretary data to the user.
         */
        $secretaries.on(
            'click',
            '.secretary-row',
            function (event) {
                if ($('#filter-secretaries .filter').prop('disabled')) {
                    $('#filter-secretaries .results').css('color', '#AAA');
                    return; // exit because we are currently on edit mode
                }

                const secretaryId = $(event.currentTarget).attr('data-id');

                const secretary = filterResults.find(function (filterResult) {
                    return Number(filterResult.id) === Number(secretaryId);
                });

                display(secretary);

                $('#filter-secretaries .selected').removeClass('selected');
                $(event.currentTarget).addClass('selected');
                $('#edit-secretary, #delete-secretary').prop('disabled', false);
            }.bind(this)
        );

        /**
         * Event: Add New Secretary Button "Click"
         */
        $secretaries.on(
            'click',
            '#add-secretary',
            function () {
                resetForm();
                $('#filter-secretaries button').prop('disabled', true);
                $('#filter-secretaries .results').css('color', '#AAA');

                $('#secretaries .add-edit-delete-group').hide();
                $('#secretaries .save-cancel-group').show();
                $('#secretaries .record-details').find('input, textarea').prop('disabled', false);
                $('#secretaries .record-details').find('select').prop('disabled', false);
                $('#secretary-password, #secretary-password-confirm').addClass('required');
                $('#secretary-providers input:checkbox').prop('disabled', false);
            }.bind(this)
        );

        /**
         * Event: Edit Secretary Button "Click"
         */
        $secretaries.on('click', '#edit-secretary', function () {
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
        $secretaries.on(
            'click',
            '#delete-secretary',
            function () {
                const secretaryId = $('#secretary-id').val();
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
                            remove(secretaryId);
                            $('#message-box').dialog('close');
                        }.bind(this)
                    }
                ];

                App.Utils.Message.show(App.Lang.delete_secretary, App.Lang.delete_record_prompt, buttons);
            }.bind(this)
        );

        /**
         * Event: Save Secretary Button "Click"
         */
        $secretaries.on(
            'click',
            '#save-secretary',
            function () {
                const secretary = {
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
                        notifications: Number($('#secretary-notifications').prop('checked')),
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

                if (!validate()) {
                    return;
                }

                save(secretary);
            }.bind(this)
        );

        /**
         * Event: Cancel Secretary Button "Click"
         *
         * Cancel add or edit of an secretary record.
         */
        $secretaries.on(
            'click',
            '#cancel-secretary',
            function () {
                const id = $('#secretary-id').val();
                resetForm();
                if (id) {
                    select(id, true);
                }
            }.bind(this)
        );
    }

    /**
     * Save secretary record to database.
     *
     * @param {Object} secretary Contains the secretary record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    function save(secretary) {
        App.Http.Secretaries.save(secretary).done((response) => {
            Backend.displayNotification(App.Lang.secretary_saved);
            resetForm();
            $('#filter-secretaries .key').val('');
            filter('', response.id, true);
        });
    }

    /**
     * Delete a secretary record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    function remove(id) {
        App.Http.Secretaries.destroy(id).done(() => {
            Backend.displayNotification(App.Lang.secretary_deleted);
            resetForm();
            filter($('#filter-secretaries .key').val());
        });
    }

    /**
     * Validates a secretary record.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $('#secretaries .is-invalid').removeClass('is-invalid');
        $('#secretaries .form-message').removeClass('alert-danger');

        try {
            // Validate required fields.
            let missingRequired = false;

            $('#secretaries .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });
            if (missingRequired) {
                throw new Error('Fields with * are  required.');
            }

            // Validate passwords.
            if ($('#secretary-password').val() !== $('#secretary-password-confirm').val()) {
                $('#secretary-password, #secretary-password-confirm').addClass('is-invalid');
                throw new Error('Passwords mismatch!');
            }

            if (
                $('#secretary-password').val().length < App.Vars.min_password_length &&
                $('#secretary-password').val() !== ''
            ) {
                $('#secretary-password, #secretary-password-confirm').addClass('is-invalid');
                throw new Error(
                    'Password must be at least ' + BackendSecretaries.MIN_PASSWORD_LENGTH + ' characters long.'
                );
            }

            // Validate user email.
            if (!App.Utils.Validation.email($('#secretary-email').val())) {
                $('#secretary-email').addClass('is-invalid');
                throw new Error('Invalid email address!');
            }

            // Check if username exists
            if ($('#secretary-username').attr('already-exists') === 'true') {
                $('#secretary-username').addClass('is-invalid');
                throw new Error('Username already exists.');
            }

            return true;
        } catch (error) {
            $('#secretaries .form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Resets the secretary tab form back to its initial state.
     */
    function resetForm() {
        $('#filter-secretaries .selected').removeClass('selected');
        $('#filter-secretaries button').prop('disabled', false);
        $('#filter-secretaries .results').css('color', '');

        $('#secretaries .record-details').find('input, select, textarea').val('').prop('disabled', true);
        $('#secretaries .record-details #secretary-calendar-view').val('default');
        $('#secretaries .record-details #secretary-timezone').val('UTC');
        $('#secretaries .add-edit-delete-group').show();
        $('#secretaries .save-cancel-group').hide();
        $('#edit-secretary, #delete-secretary').prop('disabled', true);
        $('#secretaries .form-message').hide();
        $('#secretary-providers input:checkbox').prop('checked', false);
        $('#secretaries .is-invalid').removeClass('is-invalid');
    }

    /**
     * Display a secretary record into the secretary form.
     *
     * @param {Object} secretary Contains the secretary record data.
     */
    function display(secretary) {
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
            const $checkbox = $('#secretary-providers input[data-id="' + secretaryProviderId + '"]');

            if (!$checkbox.length) {
                return;
            }

            $checkbox.prop('checked', true);
        });
    }

    /**
     * Filters secretary records based on a string keyword.
     *
     * @param {String} keyword This is used to filter the secretary records of the database.
     * @param {Number} selectId Optional, if provided the given ID will be selected in the filter results
     * (only selected, not displayed).
     * @param {Boolean} show Optional (false).
     */
    function filter(keyword, selectId = null, show = false) {
        App.Http.Secretaries.search(keyword, filterLimit).done((response) => {
            filterResults = response;

            $('#filter-secretaries .results').empty();

            response.forEach(
                function (secretary) {
                    $('#filter-secretaries .results').append(getFilterHtml(secretary)).append($('<hr/>'));
                }.bind(this)
            );

            if (!response.length) {
                $('#filter-secretaries .results').append(
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
                }).appendTo('#filter-secretaries .results');
            }

            if (selectId) {
                select(selectId, show);
            }
        });
    }

    /**
     * Get an secretary row html code that is going to be displayed on the filter results list.
     *
     * @param {Object} secretary Contains the secretary record data.
     *
     * @return {String} The html code that represents the record on the filter results list.
     */
    function getFilterHtml(secretary) {
        const name = secretary.first_name + ' ' + secretary.last_name;

        let info = secretary.email;

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
                $('<br/>')
            ]
        });
    }

    /**
     * Select a specific record from the current filter results. If the secretary id does not exist
     * in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} show Optional (false), if true the method will display the record in the form.
     */
    function select(id, show = false) {
        $('#filter-secretaries .selected').removeClass('selected');

        $('#filter-secretaries .secretary-row[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const secretary = filterResults.find(
                function (filterResult) {
                    return Number(filterResult.id) === Number(id);
                }.bind(this)
            );

            display(secretary);

            $('#edit-secretary, #delete-secretary').prop('disabled', false);
        }
    }

    function init() {
        resetForm();
        filter('');
        bindEventHandlers();

        App.Vars.providers.forEach(function (provider) {
            $('<div/>', {
                'class': 'checkbox',
                'html': [
                    $('<div/>', {
                        'class': 'checkbox form-check',
                        'html': [
                            $('<input/>', {
                                'class': 'form-check-input',
                                'type': 'checkbox',
                                'data-id': provider.id
                            }),
                            $('<label/>', {
                                'class': 'form-check-label',
                                'text': provider.first_name + ' ' + provider.last_name,
                                'for': provider.id
                            })
                        ]
                    })
                ]
            }).appendTo('#secretary-providers');
        });
    }

    document.addEventListener('DOMContentLoaded', init);

    return {};
})();
