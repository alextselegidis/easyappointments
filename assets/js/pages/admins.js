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
 * Admins page.
 *
 * This module implements the functionality of admins page.
 */
App.Pages.Admins = (function () {
    const $admins = $('#admins');
    const $adminId = $('#admin-id');
    const $firstName = $('#admin-first-name');
    const $lastName = $('#admin-last-name');
    const $email = $('#admin-email');
    const $mobileNumber = $('#admin-mobile-number');
    const $phoneNumber = $('#admin-phone-number');
    const $address = $('#admin-address');
    const $city = $('#admin-city');
    const $state = $('#admin-state');
    const $zipCode = $('#admin-zip-code');
    const $notes = $('#admin-notes');
    const $timezone = $('#admin-timezone');
    const $username = $('#admin-username');
    const $password = $('#admin-password');
    const $passwordConfirmation = $('#admin-password-confirm');
    const $notifications = $('#admin-notifications');
    const $calendarView = $('#admin-calendar-view');
    const $filterAdmins = $('#filter-admins');
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
         */
        $admins.on('blur', '#admin-username', function () {
            const $input = $(this);

            if ($input.prop('readonly') === true || $input.val() === '') {
                return;
            }

            const adminId = $input.parents().eq(2).find('.record-id').val();

            if (!adminId) {
                return;
            }

            const username = $input.val();

            App.Http.Account.validateUsername(adminId, username).done((response) => {
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
         * Event: Filter Admins Form "Submit"
         *
         * Filter the admin records with the given key string.
         *
         * @param {jQuery.Event} event
         */
        $admins.on('submit', '#filter-admins form', function (event) {
            event.preventDefault();
            const key = $('#filter-admins .key').val();
            $('#filter-admins .selected').removeClass('selected');
            resetForm();
            filter(key);
        });

        /**
         * Event: Filter Admin Row "Click"
         *
         * Display the selected admin data to the user.
         */
        $admins.on('click', '.admin-row', function (event) {
            if ($('#filter-admins .filter').prop('disabled')) {
                $('#filter-admins .results').css('color', '#AAA');
                return; // exit because we are currently on edit mode
            }

            const adminId = $(event.currentTarget).attr('data-id');

            const admin = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(adminId);
            });

            display(admin);
            $('#filter-admins .selected').removeClass('selected');
            $(event.currentTarget).addClass('selected');
            $('#edit-admin, #delete-admin').prop('disabled', false);
        });

        /**
         * Event: Add New Admin Button "Click"
         */
        $admins.on('click', '#add-admin', function () {
            resetForm();
            $admins.find('.add-edit-delete-group').hide();
            $admins.find('.save-cancel-group').show();
            $admins.find('.record-details').find('input, textarea').prop('disabled', false);
            $admins.find('.record-details').find('select').prop('disabled', false);
            $('#admin-password, #admin-password-confirm').addClass('required');
            $('#filter-admins button').prop('disabled', true);
            $('#filter-admins .results').css('color', '#AAA');
        });

        /**
         * Event: Edit Admin Button "Click"
         */
        $admins.on('click', '#edit-admin', function () {
            $admins.find('.add-edit-delete-group').hide();
            $admins.find('.save-cancel-group').show();
            $admins.find('.record-details').find('input, textarea').prop('disabled', false);
            $admins.find('.record-details').find('select').prop('disabled', false);
            $('#admin-password, #admin-password-confirm').removeClass('required');
            $('#filter-admins button').prop('disabled', true);
            $('#filter-admins .results').css('color', '#AAA');
        });

        /**
         * Event: Delete Admin Button "Click"
         */
        $admins.on('click', '#delete-admin', function () {
            const adminId = $('#admin-id').val();

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
                        remove(adminId);
                        $('#message-box').dialog('close');
                    }
                }
            ];

            App.Utils.Message.show(App.Lang.delete_admin, App.Lang.delete_record_prompt, buttons);
        });

        /**
         * Event: Save Admin Button "Click"
         */
        $admins.on('click', '#save-admin', function () {
            const admin = {
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
                timezone: $timezone.val(),
                settings: {
                    username: $username.val(),
                    notifications: Number($notifications.prop('checked')),
                    calendar_view: $calendarView.val()
                }
            };

            // Include password if changed.
            if ($password.val() !== '') {
                admin.settings.password = $password.val();
            }

            // Include id if changed.
            if ($adminId.val() !== '') {
                admin.id = $('#admin-id').val();
            }

            if (!validate()) {
                return;
            }

            save(admin);
        });

        /**
         * Event: Cancel Admin Button "Click"
         *
         * Cancel add or edit of an admin record.
         */
        $admins.on('click', '#cancel-admin', function () {
            const id = $adminId.val();

            resetForm();

            if (id) {
                select(id, true);
            }
        });
    }

    /**
     * Save admin record to database.
     *
     * @param {Object} admin Contains the admin record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    function save(admin) {
        App.Http.Admins.save(admin).then((response) => {
            App.Layouts.Backend.displayNotification(App.Lang.admin_saved);
            resetForm();
            $('#filter-admins .key').val('');
            filter('', response.id, true);
        });
    }

    /**
     * Delete an admin record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    function remove(id) {
        App.Http.Admins.destroy(id).then(() => {
            App.Layouts.Backend.displayNotification(App.Lang.admin_deleted);
            resetForm();
            filter($('#filter-admins .key').val());
        });
    }

    /**
     * Validates an admin record.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $admins.find('.is-invalid').removeClass('is-invalid');

        try {
            // Validate required fields.
            let missingRequired = false;

            $admins.find('.required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error('Fields with * are  required.');
            }

            // Validate passwords.
            if ($password.val() !== $passwordConfirmation.val()) {
                $('#admin-password, #admin-password-confirm').addClass('is-invalid');
                throw new Error(App.Lang.passwords_mismatch);
            }

            if ($password.val().length < App.Vars.min_password_length && $password.val() !== '') {
                $('#admin-password, #admin-password-confirm').addClass('is-invalid');
                throw new Error(App.Lang.password_length_notice.replace('$number', BackendAdmins.MIN_PASSWORD_LENGTH));
            }

            // Validate user email.
            if (!App.Utils.Validation.email($email.val())) {
                $email.addClass('is-invalid');
                throw new Error(App.Lang.invalid_email);
            }

            // Check if username exists
            if ($username.attr('already-exists') === 'true') {
                $username.addClass('is-invalid');
                throw new Error(App.Lang.username_already_exists);
            }

            return true;
        } catch (error) {
            $admins.find('.form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Resets the admin form back to its initial state.
     */
    function resetForm() {
        $('#filter-admins .selected').removeClass('selected');
        $('#filter-admins button').prop('disabled', false);
        $('#filter-admins .results').css('color', '');

        $admins.find('.add-edit-delete-group').show();
        $admins.find('.save-cancel-group').hide();
        $admins.find('.record-details').find('input, select, textarea').val('').prop('disabled', true);
        $admins.find('.record-details #admin-calendar-view').val('default');
        $admins.find('.record-details #admin-timezone').val('UTC');
        $('#edit-admin, #delete-admin').prop('disabled', true);

        $('#admins .is-invalid').removeClass('is-invalid');
        $('#admins .form-message').hide();
    }

    /**
     * Display a admin record into the admin form.
     *
     * @param {Object} admin Contains the admin record data.
     */
    function display(admin) {
        $adminId.val(admin.id);
        $firstName.val(admin.first_name);
        $lastName.val(admin.last_name);
        $email.val(admin.email);
        $mobileNumber.val(admin.mobile_number);
        $phoneNumber.val(admin.phone_number);
        $address.val(admin.address);
        $city.val(admin.city);
        $state.val(admin.state);
        $zipCode.val(admin.zip_code);
        $notes.val(admin.notes);
        $timezone.val(admin.timezone);

        $username.val(admin.settings.username);
        $calendarView.val(admin.settings.calendar_view);
        $notifications.prop('checked', Boolean(Number(admin.settings.notifications)));
    }

    /**
     * Filters admin records by a keyword string.
     *
     * @param {String} keyword This string is used to filter the admin records of the database.
     * @param {Number} [selectId] (OPTIONAL = undefined) This record id will be selected when
     * the filter operation is finished.
     * @param {Boolean} [show] (OPTIONAL = false) If true the selected record data are going
     * to be displayed on the details column (requires a selected record though).
     */
    function filter(keyword, selectId = null, show = false) {
        App.Http.Admins.search(keyword, filterLimit).then((response) => {
            filterResults = response;

            $filterAdmins.find('.results').empty();

            response.forEach(function (admin) {
                $('#filter-admins .results').append(getFilterHtml(admin)).append($('<hr/>'));
            });

            if (!response.length) {
                $filterAdmins.find('.results').append(
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
                    }
                }).appendTo('#filter-admins .results');
            }

            if (selectId) {
                select(selectId, show);
            }
        });
    }

    /**
     * Get an admin row html code that is going to be displayed on the filter results list.
     *
     * @param {Object} admin Contains the admin record data.
     *
     * @return {String} The html code that represents the record on the filter results list.
     */
    function getFilterHtml(admin) {
        const name = admin.first_name + ' ' + admin.last_name;

        let info = admin.email;

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
                $('<br/>')
            ]
        });
    }

    /**
     * Select a specific record from the current filter results. If the admin id does not exist
     * in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} show Optional (false), if true then the method will display the record
     * on the form.
     */
    function select(id, show = false) {
        $filterAdmins.find('.selected').removeClass('selected');

        $filterAdmins.find('.admin-row[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const admin = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(id);
            });

            display(admin);

            $('#edit-admin, #delete-admin').prop('disabled', false);
        }
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        resetForm();
        filter('');
        addEventListeners();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        filter,
        save,
        remove,
        getFilterHtml,
        resetForm,
        select
    };
})();
