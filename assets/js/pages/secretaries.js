/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Secretaries page.
 *
 * This module implements the functionality of the secretaries page.
 */
App.Pages.Secretaries = (function () {
    const $secretaries = $('#secretaries');
    const $id = $('#id');
    const $firstName = $('#first-name');
    const $lastName = $('#last-name');
    const $email = $('#email');
    const $mobileNumber = $('#mobile-number');
    const $phoneNumber = $('#phone-number');
    const $address = $('#address');
    const $city = $('#city');
    const $state = $('#state');
    const $zipCode = $('#zip-code');
    const $notes = $('#notes');
    const $language = $('#language');
    const $timezone = $('#timezone');
    const $ldapDn = $('#ldap-dn');
    const $username = $('#username');
    const $password = $('#password');
    const $passwordConfirmation = $('#password-confirm');
    const $notifications = $('#notifications');
    const $calendarView = $('#calendar-view');
    const $filterSecretaries = $('#filter-secretaries');
    let filterResults = {};
    let filterLimit = 20;

    /**
     * Add the page event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Admin Username "Blur"
         *
         * When the admin leaves the username input field we will need to check if the username
         * is not taken by another record in the system.
         *
         * @param {jQuery.Event} event
         */
        $secretaries.on('blur', '#username', (event) => {
            const $input = $(event.target);

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
                    $input.parents().eq(3).find('.form-message').text(lang('username_already_exists'));
                    $input.parents().eq(3).find('.form-message').show();
                } else {
                    $input.removeClass('is-invalid');
                    $input.attr('already-exists', 'false');
                    if ($input.parents().eq(3).find('.form-message').text() === lang('username_already_exists')) {
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
        $secretaries.on('submit', '#filter-secretaries form', (event) => {
            event.preventDefault();
            const key = $('#filter-secretaries .key').val();
            $filterSecretaries.find('.selected').removeClass('selected');
            App.Pages.Secretaries.resetForm();
            App.Pages.Secretaries.filter(key);
        });

        /**
         * Event: Filter Secretary Row "Click"
         *
         * Display the selected secretary data to the user.
         */
        $secretaries.on('click', '.secretary-row', (event) => {
            if ($('#filter-secretaries .filter').prop('disabled')) {
                $('#filter-secretaries .results').css('color', '#AAA');
                return; // exit because we are currently on edit mode
            }

            const secretaryId = $(event.currentTarget).attr('data-id');

            const secretary = filterResults.find((filterResult) => Number(filterResult.id) === Number(secretaryId));

            App.Pages.Secretaries.display(secretary);

            $('#filter-secretaries .selected').removeClass('selected');
            $(event.currentTarget).addClass('selected');
            $('#edit-secretary, #delete-secretary').prop('disabled', false);
        });

        /**
         * Event: Add New Secretary Button "Click"
         */
        $secretaries.on('click', '#add-secretary', () => {
            App.Pages.Secretaries.resetForm();
            $filterSecretaries.find('button').prop('disabled', true);
            $filterSecretaries.find('.results').css('color', '#AAA');

            $secretaries.find('.add-edit-delete-group').hide();
            $secretaries.find('.save-cancel-group').show();
            $secretaries.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $secretaries.find('.record-details .form-label span').prop('hidden', false);
            $('#password, #password-confirm').addClass('required');
            $('#secretary-providers input:checkbox').prop('disabled', false);
        });

        /**
         * Event: Edit Secretary Button "Click"
         */
        $secretaries.on('click', '#edit-secretary', () => {
            $filterSecretaries.find('button').prop('disabled', true);
            $filterSecretaries.find('.results').css('color', '#AAA');
            $secretaries.find('.add-edit-delete-group').hide();
            $secretaries.find('.save-cancel-group').show();
            $secretaries.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $secretaries.find('.record-details .form-label span').prop('hidden', false);
            $('#password, #password-confirm').removeClass('required');
            $('#secretary-providers input:checkbox').prop('disabled', false);
        });

        /**
         * Event: Delete Secretary Button "Click"
         */
        $secretaries.on('click', '#delete-secretary', () => {
            const secretaryId = $id.val();

            const buttons = [
                {
                    text: lang('cancel'),
                    click: (event, messageModal) => {
                        messageModal.hide();
                    },
                },
                {
                    text: lang('delete'),
                    click: (event, messageModal) => {
                        remove(secretaryId);
                        messageModal.hide();
                    },
                },
            ];

            App.Utils.Message.show(lang('delete_secretary'), lang('delete_record_prompt'), buttons);
        });

        /**
         * Event: Save Secretary Button "Click"
         */
        $secretaries.on('click', '#save-secretary', () => {
            const secretary = {
                first_name: $firstName.val(),
                last_name: $lastName.val(),
                email: $email.val(),
                mobile_number: $mobileNumber.val(),
                phone_number: $phoneNumber.val(),
                address: $address.val(),
                city: $city.val(),
                state: $state.val(),
                zip_code: $zipCode.val(),
                notes: $notes.val(),
                language: $language.val(),
                timezone: $timezone.val(),
                ldap_dn: $ldapDn.val(),
                settings: {
                    username: $username.val(),
                    notifications: Number($notifications.prop('checked')),
                    calendar_view: $calendarView.val(),
                },
            };

            // Include secretary services.
            secretary.providers = [];

            $('#secretary-providers input:checkbox').each((index, checkbox) => {
                if ($(checkbox).prop('checked')) {
                    secretary.providers.push($(checkbox).attr('data-id'));
                }
            });

            // Include password if changed.
            if ($password.val() !== '') {
                secretary.settings.password = $password.val();
            }

            // Include ID if changed.
            if ($id.val() !== '') {
                secretary.id = $id.val();
            }

            if (!App.Pages.Secretaries.validate()) {
                return;
            }

            App.Pages.Secretaries.save(secretary);
        });

        /**
         * Event: Cancel Secretary Button "Click"
         *
         * Cancel add or edit of an secretary record.
         */
        $secretaries.on('click', '#cancel-secretary', () => {
            const id = $id.val();
            resetForm();
            if (id) {
                select(id, true);
            }
        });
    }

    /**
     * Save secretary record to database.
     *
     * @param {Object} secretary Contains the secretary record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    function save(secretary) {
        App.Http.Secretaries.save(secretary).done((response) => {
            App.Layouts.Backend.displayNotification(lang('secretary_saved'));
            App.Pages.Secretaries.resetForm();
            $('#filter-secretaries .key').val('');
            App.Pages.Secretaries.filter('', response.id, true);
        });
    }

    /**
     * Delete a secretary record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    function remove(id) {
        App.Http.Secretaries.destroy(id).done(() => {
            App.Layouts.Backend.displayNotification(lang('secretary_deleted'));
            App.Pages.Secretaries.resetForm();
            App.Pages.Secretaries.filter($('#filter-secretaries .key').val());
        });
    }

    /**
     * Validates a secretary record.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $('#secretaries .is-invalid').removeClass('is-invalid');
        $secretaries.find('.form-message').removeClass('alert-danger');

        try {
            // Validate required fields.
            let missingRequired = false;

            $secretaries.find('.required').each((index, requiredField) => {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });
            if (missingRequired) {
                throw new Error(lang('fields_are_required'));
            }

            // Validate passwords.
            if ($password.val() !== $passwordConfirmation.val()) {
                $('#password, #password-confirm').addClass('is-invalid');
                throw new Error(lang('passwords_mismatch'));
            }

            if ($password.val().length < vars('min_password_length') && $password.val() !== '') {
                $('#password, #password-confirm').addClass('is-invalid');
                throw new Error(lang('password_length_notice').replace('$number', vars('min_password_length')));
            }

            // Validate user email.
            if (!App.Utils.Validation.email($email.val())) {
                $email.addClass('is-invalid');
                throw new Error(lang('invalid_email'));
            }

            // Validate phone number.
            const phoneNumber = $phoneNumber.val();

            if (phoneNumber && !App.Utils.Validation.phone(phoneNumber)) {
                $phoneNumber.addClass('is-invalid');
                throw new Error(lang('invalid_phone'));
            }

            // Validate mobile number.
            const mobileNumber = $mobileNumber.val();

            if (mobileNumber && !App.Utils.Validation.phone(mobileNumber)) {
                $mobileNumber.addClass('is-invalid');
                throw new Error(lang('invalid_phone'));
            }

            // Check if username exists
            if ($username.attr('already-exists') === 'true') {
                $username.addClass('is-invalid');
                throw new Error(lang('username_already_exists'));
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
        $filterSecretaries.find('.selected').removeClass('selected');
        $filterSecretaries.find('button').prop('disabled', false);
        $filterSecretaries.find('.results').css('color', '');
        $secretaries.find('.record-details').find('input, select, textarea').val('').prop('disabled', true);
        $secretaries.find('.record-details .form-label span').prop('hidden', true);
        $secretaries.find('.record-details #calendar-view').val('default');
        $secretaries.find('.record-details #timezone').val(vars('default_timezone'));
        $secretaries.find('.record-details #language').val(vars('default_language'));
        $secretaries.find('.record-details #notifications').prop('checked', true);
        $secretaries.find('.add-edit-delete-group').show();
        $secretaries.find('.save-cancel-group').hide();
        $secretaries.find('.form-message').hide();
        $secretaries.find('.is-invalid').removeClass('is-invalid');
        $('#edit-secretary, #delete-secretary').prop('disabled', true);
        $('#secretary-providers input:checkbox').prop('disabled', true).prop('checked', false);
    }

    /**
     * Display a secretary record into the secretary form.
     *
     * @param {Object} secretary Contains the secretary record data.
     */
    function display(secretary) {
        $id.val(secretary.id);
        $firstName.val(secretary.first_name);
        $lastName.val(secretary.last_name);
        $email.val(secretary.email);
        $mobileNumber.val(secretary.mobile_number);
        $phoneNumber.val(secretary.phone_number);
        $address.val(secretary.address);
        $city.val(secretary.city);
        $state.val(secretary.state);
        $zipCode.val(secretary.zip_code);
        $notes.val(secretary.notes);
        $language.val(secretary.language);
        $timezone.val(secretary.timezone);
        $ldapDn.val(secretary.ldap_dn);

        $username.val(secretary.settings.username);
        $calendarView.val(secretary.settings.calendar_view);
        $notifications.prop('checked', Boolean(Number(secretary.settings.notifications)));

        $('#secretary-providers input:checkbox').prop('checked', false);

        secretary.providers.forEach((secretaryProviderId) => {
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

            $filterSecretaries.find('.results').empty();

            response.forEach((secretary) => {
                $filterSecretaries
                    .find('.results')
                    .append(App.Pages.Secretaries.getFilterHtml(secretary))
                    .append($('<hr/>'));
            });

            if (!response.length) {
                $('#filter-secretaries .results').append(
                    $('<em/>', {
                        'text': lang('no_records_found'),
                    }),
                );
            } else if (response.length === filterLimit) {
                $('<button/>', {
                    'type': 'button',
                    'class': 'btn btn-outline-secondary w-100 load-more text-center',
                    'text': lang('load_more'),
                    'click': () => {
                        filterLimit += 20;
                        App.Pages.Customers.filter(keyword, selectId, show);
                    },
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
                    'text': name,
                }),
                $('<br/>'),
                $('<small/>', {
                    'class': 'text-muted',
                    'text': info,
                }),
                $('<br/>'),
            ],
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
        $filterSecretaries.find('.selected').removeClass('selected');

        $('#filter-secretaries .secretary-row[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const secretary = filterResults.find((filterResult) => Number(filterResult.id) === Number(id));

            App.Pages.Secretaries.display(secretary);

            $('#edit-secretary, #delete-secretary').prop('disabled', false);
        }
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        App.Pages.Secretaries.resetForm();
        App.Pages.Secretaries.filter('');
        App.Pages.Secretaries.addEventListeners();

        vars('providers').forEach((provider) => {
            const checkboxId = `provider-service-${provider.id}`;

            $('<div/>', {
                'class': 'checkbox',
                'html': [
                    $('<div/>', {
                        'class': 'checkbox form-check',
                        'html': [
                            $('<input/>', {
                                'id': checkboxId,
                                'class': 'form-check-input',
                                'type': 'checkbox',
                                'data-id': provider.id,
                                'prop': {
                                    'disabled': true,
                                },
                            }),
                            $('<label/>', {
                                'class': 'form-check-label',
                                'text': provider.first_name + ' ' + provider.last_name,
                                'for': checkboxId,
                            }),
                        ],
                    }),
                ],
            }).appendTo('#secretary-providers');
        });
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        filter,
        save,
        remove,
        validate,
        getFilterHtml,
        resetForm,
        display,
        select,
        addEventListeners,
    };
})();
