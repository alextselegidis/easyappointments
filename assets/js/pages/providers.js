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

App.Pages.Providers = (function () {
    const $providers = $('#providers');
    let filterResults = {};
    let filterLimit = 20;
    let enableSubmit;
    let enableCancel;
    let workingPlanManager;

    /**
     * Bind the event handlers for the backend/users "Providers" tab.
     */
    function bindEventHandlers() {
        /**
         * Event: Filter Providers Form "Submit"
         *
         * Filter the provider records with the given key string.
         *
         * @param {jQuery.Event} event
         */
        $providers.on('submit', '#filter-providers form', function (event) {
            event.preventDefault();
            const key = $('#filter-providers .key').val();
            $('.selected').removeClass('selected');
            resetForm();
            filter(key);
        });

        /**
         * Event: Filter Provider Row "Click"
         *
         * Display the selected provider data to the user.
         */
        $providers.on('click', '.provider-row', function (event) {
            if ($('#filter-providers .filter').prop('disabled')) {
                $('#filter-providers .results').css('color', '#AAA');
                return; // Exit because we are currently on edit mode.
            }

            const providerId = $(event.currentTarget).attr('data-id');
            const provider = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(providerId);
            });

            display(provider);
            $('#filter-providers .selected').removeClass('selected');
            $(event.currentTarget).addClass('selected');
            $('#edit-provider, #delete-provider').prop('disabled', false);
        });

        /**
         * Event: Add New Provider Button "Click"
         */
        $providers.on('click', '#add-provider', function () {
            resetForm();
            $('#filter-providers button').prop('disabled', true);
            $('#filter-providers .results').css('color', '#AAA');
            $('#providers .add-edit-delete-group').hide();
            $('#providers .save-cancel-group').show();
            $('#providers .record-details').find('input, select, textarea').prop('disabled', false);
            $('#provider-password, #provider-password-confirm').addClass('required');
            $providers
                .find(
                    '.add-break, .edit-break, .delete-break, .add-working-plan-exception, .edit-working-plan-exception, .delete-working-plan-exception, #reset-working-plan'
                )
                .prop('disabled', false);
            $('#provider-services input:checkbox').prop('disabled', false);

            // Apply default working plan
            const companyWorkingPlan = JSON.parse(App.Vars.company_working_plan);
            workingPlanManager.setup(companyWorkingPlan);
            workingPlanManager.timepickers(false);
        });

        /**
         * Event: Edit Provider Button "Click"
         */
        $providers.on('click', '#edit-provider', function () {
            $('#providers .add-edit-delete-group').hide();
            $('#providers .save-cancel-group').show();
            $('#filter-providers button').prop('disabled', true);
            $('#filter-providers .results').css('color', '#AAA');
            $('#providers .record-details').find('input, select, textarea').prop('disabled', false);
            $('#provider-password, #provider-password-confirm').removeClass('required');
            $('#provider-services input:checkbox').prop('disabled', false);
            $providers
                .find(
                    '.add-break, .edit-break, .delete-break, .add-working-plan-exception, .edit-working-plan-exception, .delete-working-plan-exception, #reset-working-plan'
                )
                .prop('disabled', false);
            $('#providers input:checkbox').prop('disabled', false);
            workingPlanManager.timepickers(false);
        });

        /**
         * Event: Delete Provider Button "Click"
         */
        $providers.on('click', '#delete-provider', function () {
            const providerId = $('#provider-id').val();

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
                        remove(providerId);
                        $('#message-box').dialog('close');
                    }
                }
            ];

            App.Utils.Message.show(App.Lang.delete_provider, App.Lang.delete_record_prompt, buttons);
        });

        /**
         * Event: Save Provider Button "Click"
         */
        $providers.on('click', '#save-provider', function () {
            const provider = {
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
                    working_plan: JSON.stringify(workingPlanManager.get()),
                    working_plan_exceptions: JSON.stringify(workingPlanManager.getWorkingPlanExceptions()),
                    notifications: Number($('#provider-notifications').prop('checked')),
                    calendar_view: $('#provider-calendar-view').val()
                }
            };

            // Include provider services.
            provider.services = [];
            $('#provider-services input:checkbox').each(function (index, checkbox) {
                if ($(checkbox).prop('checked')) {
                    provider.services.push($(checkbox).attr('data-id'));
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

            if (!validate()) {
                return;
            }

            save(provider);
        });

        /**
         * Event: Cancel Provider Button "Click"
         *
         * Cancel add or edit of an provider record.
         */
        $providers.on('click', '#cancel-provider', function () {
            const id = $('#filter-providers .selected').attr('data-id');
            resetForm();
            if (id) {
                select(id, true);
            }
        });

        /**
         * Event: Display Provider Details "Click"
         */
        $providers.on('shown.bs.tab', 'a[data-toggle="tab"]', function () {
            Backend.placeFooterToBottom();
        });

        /**
         * Event: Reset Working Plan Button "Click".
         */
        $providers.on('click', '#reset-working-plan', function () {
            $('.breaks tbody').empty();
            $('.working-plan-exceptions tbody').empty();
            $('.work-start, .work-end').val('');
            const companyWorkingPlan = JSON.parse(App.Vars.company_working_plan);
            workingPlanManager.setup(companyWorkingPlan);
            workingPlanManager.timepickers(false);
        });
    }

    /**
     * Save provider record to database.
     *
     * @param {Object} provider Contains the provider record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    function save(provider) {
        App.Http.Providers.save(provider).then((response) => {
            Backend.displayNotification(App.Lang.provider_saved);
            resetForm();
            $('#filter-providers .key').val('');
            filter('', response.id, true);
        });
    }

    /**
     * Delete a provider record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    function remove(id) {
        App.Http.Providers.destroy(id).then(() => {
            Backend.displayNotification(App.Lang.provider_deleted);
            resetForm();
            filter($('#filter-providers .key').val());
        });
    }

    /**
     * Validates a provider record.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $('#providers .is-invalid').removeClass('is-invalid');
        $('#providers .form-message').removeClass('alert-danger').hide();

        try {
            // Validate required fields.
            let missingRequired = false;

            $('#providers .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(App.Lang.fields_are_required);
            }

            // Validate passwords.
            if ($('#provider-password').val() !== $('#provider-password-confirm').val()) {
                $('#provider-password, #provider-password-confirm').addClass('is-invalid');
                throw new Error(App.Lang.passwords_mismatch);
            }

            if (
                $('#provider-password').val().length < App.Vars.min_password_length &&
                $('#provider-password').val() !== ''
            ) {
                $('#provider-password, #provider-password-confirm').addClass('is-invalid');
                throw new Error(App.Lang.password_length_notice.replace('$number', MIN_PASSWORD_LENGTH));
            }

            // Validate user email.
            if (!App.Utils.Validation.email($('#provider-email').val())) {
                $('#provider-email').addClass('is-invalid');
                throw new Error(App.Lang.invalid_email);
            }

            // Check if username exists
            if ($('#provider-username').attr('already-exists') === 'true') {
                $('#provider-username').addClass('is-invalid');
                throw new Error(App.Lang.username_already_exists);
            }

            return true;
        } catch (error) {
            $('#providers .form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Resets the provider tab form back to its initial state.
     */
    function resetForm() {
        $('#filter-providers .selected').removeClass('selected');
        $('#filter-providers button').prop('disabled', false);
        $('#filter-providers .results').css('color', '');

        $('#providers .add-edit-delete-group').show();
        $('#providers .save-cancel-group').hide();
        $('#providers .record-details h3 a').remove();
        $('#providers .record-details').find('input, select, textarea').val('').prop('disabled', true);
        $('#providers .record-details #provider-calendar-view').val('default');
        $('#providers .record-details #provider-timezone').val('UTC');
        $('#providers .add-break, .add-working-plan-exception, #reset-working-plan').prop('disabled', true);
        workingPlanManager.timepickers(true);
        $('#providers .working-plan input:text').timepicker('destroy');
        $('#providers .working-plan input:checkbox').prop('disabled', true);
        $('.breaks').find('.edit-break, .delete-break').prop('disabled', true);
        $('.working-plan-exceptions')
            .find('.edit-working-plan-exception, .delete-working-plan-exception')
            .prop('disabled', true);

        $('#providers .record-details .is-invalid').removeClass('is-invalid');
        $('#providers .record-details .form-message').hide();

        $('#edit-provider, #delete-provider').prop('disabled', true);
        $('#provider-services input:checkbox').prop('disabled', true).prop('checked', false);
        $('#provider-services a').remove();
        $('#providers .working-plan tbody').empty();
        $('#providers .breaks tbody').empty();
        $('#providers .working-plan-exceptions tbody').empty();
    }

    /**
     * Display a provider record into the provider form.
     *
     * @param {Object} provider Contains the provider record data.
     */
    function display(provider) {
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
        $('#provider-notifications').prop('checked', Boolean(Number(provider.settings.notifications)));

        // Add dedicated provider link.
        let dedicatedUrl = App.Utils.Url.siteUrl('?provider=' + encodeURIComponent(provider.id));
        let $link = $('<a/>', {
            'href': dedicatedUrl,
            'html': [
                $('<span/>', {
                    'class': 'fas fa-link'
                })
            ]
        });

        $('#providers .details-view h3').find('a').remove().end().append($link);

        $('#provider-services a').remove();
        $('#provider-services input:checkbox').prop('checked', false);

        provider.services.forEach(function (providerServiceId) {
            const $checkbox = $('#provider-services input[data-id="' + providerServiceId + '"]');

            if (!$checkbox.length) {
                return;
            }

            $checkbox.prop('checked', true);

            // Add dedicated service-provider link.
            dedicatedUrl = App.Utils.Url.siteUrl(
                '?provider=' + encodeURIComponent(provider.id) + '&service=' + encodeURIComponent(providerServiceId)
            );

            $link = $('<a/>', {
                'href': dedicatedUrl,
                'html': [
                    $('<span/>', {
                        'class': 'fas fa-link'
                    })
                ]
            });

            $checkbox.parent().append($link);
        });

        // Display working plan
        const workingPlan = JSON.parse(provider.settings.working_plan);
        workingPlanManager.setup(workingPlan);
        $('.working-plan').find('input').prop('disabled', true);
        $('.breaks').find('.edit-break, .delete-break').prop('disabled', true);
        $('#providers .working-plan-exceptions tbody').empty();
        const workingPlanExceptions = JSON.parse(provider.settings.working_plan_exceptions);
        workingPlanManager.setupWorkingPlanExceptions(workingPlanExceptions);
        $('.working-plan-exceptions')
            .find('.edit-working-plan-exception, .delete-working-plan-exception')
            .prop('disabled', true);
        $('#providers .working-plan input:checkbox').prop('disabled', true);
        Backend.placeFooterToBottom();
    }

    /**
     * Filters provider records depending a string keyword.
     *
     * @param {string} keyword This is used to filter the provider records of the database.
     * @param {numeric} selectId Optional, if set, when the function is complete a result row can be set as selected.
     * @param {bool} show Optional (false), if true the selected record will be also displayed.
     */
    function filter(keyword, selectId = null, show = false) {
        App.Http.Providers.search(keyword, filterLimit).then((response) => {
            filterResults = response;

            $('#filter-providers .results').empty();
            response.forEach(function (provider) {
                $('#filter-providers .results').append(getFilterHtml(provider)).append($('<hr/>'));
            });

            if (!response.length) {
                $('#filter-providers .results').append(
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
                }).appendTo('#filter-providers .results');
            }

            if (selectId) {
                select(selectId, show);
            }
        });
    }

    /**
     * Get an provider row html code that is going to be displayed on the filter results list.
     *
     * @param {Object} provider Contains the provider record data.
     *
     * @return {String} The html code that represents the record on the filter results list.
     */
    function getFilterHtml(provider) {
        const name = provider.first_name + ' ' + provider.last_name;

        let info = provider.email;

        info = provider.mobile_number ? info + ', ' + provider.mobile_number : info;

        info = provider.phone_number ? info + ', ' + provider.phone_number : info;

        return $('<div/>', {
            'class': 'provider-row entry',
            'data-id': provider.id,
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
     * Select and display a providers filter result on the form.
     *
     * @param {Number} id Record id to be selected.
     * @param {Boolean} show Optional (false), if true the record will be displayed on the form.
     */
    function select(id, show = false) {
        // Select record in filter results.
        $('#filter-providers .provider-row[data-id="' + id + '"]').addClass('selected');

        // Display record in form (if display = true).
        if (show) {
            const provider = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(id);
            });

            display(provider);

            $('#edit-provider, #delete-provider').prop('disabled', false);
        }
    }

    function init() {
        workingPlanManager = new App.Utils.WorkingPlan();
        workingPlanManager.bindEventHandlers();

        resetForm();
        filter('');
        bindEventHandlers();

        App.Vars.services.forEach(function (service) {
            $('<div/>', {
                'class': 'checkbox',
                'html': [
                    $('<div/>', {
                        'class': 'checkbox form-check',
                        'html': [
                            $('<input/>', {
                                'class': 'form-check-input',
                                'type': 'checkbox',
                                'data-id': service.id,
                                'prop': {
                                    'disabled': true
                                }
                            }),
                            $('<label/>', {
                                'class': 'form-check-label',
                                'text': service.name,
                                'for': service.id
                            })
                        ]
                    })
                ]
            }).appendTo('#provider-services');
        });
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
