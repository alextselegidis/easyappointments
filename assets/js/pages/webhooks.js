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
 * Webhooks page.
 *
 * This module implements the functionality of the webhooks page.
 */
App.Pages.Webhooks = (function () {
    const $webhooks = $('#webhooks');
    const $id = $('#id');
    const $name = $('#name');
    const $url = $('#url');
    const $actions = $('#actions');
    const $secretToken = $('#secret-token');
    const $isSslVerified = $('#is-ssl-verified');
    const $notes = $('#notes');
    const $filterWebhooks = $('#filter-webhooks');
    let filterResults = {};
    let filterLimit = 20;

    /**
     * Add page event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Filter Webhooks Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $webhooks.on('submit', '#filter-webhooks form', (event) => {
            event.preventDefault();
            const key = $filterWebhooks.find('.key').val();
            $filterWebhooks.find('.selected').removeClass('selected');
            App.Pages.Webhooks.resetForm();
            App.Pages.Webhooks.filter(key);
        });

        /**
         * Event: Filter Webhook Row "Click"
         *
         * Display the selected webhook data to the user.
         */
        $webhooks.on('click', '.webhook-row', (event) => {
            if ($filterWebhooks.find('.filter').prop('disabled')) {
                $filterWebhooks.find('.results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            const webhookId = $(event.currentTarget).attr('data-id');

            const webhook = filterResults.find((filterResult) => Number(filterResult.id) === Number(webhookId));

            App.Pages.Webhooks.display(webhook);

            $filterWebhooks.find('.selected').removeClass('selected');
            $(event.currentTarget).addClass('selected');
            $('#edit-webhook, #delete-webhook').prop('disabled', false);
        });

        /**
         * Event: Add New Webhook Button "Click"
         */
        $webhooks.on('click', '#add-webhook', () => {
            App.Pages.Webhooks.resetForm();
            $webhooks.find('.add-edit-delete-group').hide();
            $webhooks.find('.save-cancel-group').show();
            $webhooks.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $webhooks.find('.record-details .form-label span').prop('hidden', false);
            $filterWebhooks.find('button').prop('disabled', true);
            $filterWebhooks.find('.results').css('color', '#AAA');
        });

        /**
         * Event: Cancel Webhook Button "Click"
         *
         * Cancel add or edit of a webhook record.
         */
        $webhooks.on('click', '#cancel-webhook', () => {
            const id = $id.val();

            App.Pages.Webhooks.resetForm();

            if (id !== '') {
                select(id, true);
            }
        });

        /**
         * Event: Save Webhook Button "Click"
         */
        $webhooks.on('click', '#save-webhook', () => {
            const webhook = {
                name: $name.val(),
                url: $url.val(),
                actions: '',
                secret_token: $secretToken.val(),
                is_ssl_verified: Number($isSslVerified.prop('checked')),
                notes: $notes.val(),
            };

            const actions = [];

            $actions.find('input:checked').each((index, checkbox) => {
                var action = $(checkbox).data('action');
                actions.push(action);
            });

            webhook.actions = actions.join(',');

            if ($id.val() !== '') {
                webhook.id = $id.val();
            }

            if (!App.Pages.Webhooks.validate()) {
                return;
            }

            App.Pages.Webhooks.save(webhook);
        });

        /**
         * Event: Edit Webhook Button "Click"
         */
        $webhooks.on('click', '#edit-webhook', () => {
            $webhooks.find('.add-edit-delete-group').hide();
            $webhooks.find('.save-cancel-group').show();
            $webhooks.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $webhooks.find('.record-details .form-label span').prop('hidden', false);
            $filterWebhooks.find('button').prop('disabled', true);
            $filterWebhooks.find('.results').css('color', '#AAA');
        });

        /**
         * Event: Delete Webhook Button "Click"
         */
        $webhooks.on('click', '#delete-webhook', () => {
            const webhookId = $id.val();
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
                        App.Pages.Webhooks.remove(webhookId);
                        messageModal.hide();
                    },
                },
            ];

            App.Utils.Message.show(lang('delete_webhook'), lang('delete_record_prompt'), buttons);
        });
    }

    /**
     * Save webhook record to database.
     *
     * @param {Object} webhook Contains the webhook record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    function save(webhook) {
        App.Http.Webhooks.save(webhook).then((response) => {
            App.Layouts.Backend.displayNotification(lang('webhook_saved'));
            App.Pages.Webhooks.resetForm();
            $filterWebhooks.find('.key').val('');
            App.Pages.Webhooks.filter('', response.id, true);
        });
    }

    /**
     * Delete a webhook record from database.
     *
     * @param {Number} id Record ID to be deleted.
     */
    function remove(id) {
        App.Http.Webhooks.destroy(id).then(() => {
            App.Layouts.Backend.displayNotification(lang('webhook_deleted'));
            App.Pages.Webhooks.resetForm();
            App.Pages.Webhooks.filter($filterWebhooks.find('.key').val());
        });
    }

    /**
     * Validates a webhook record.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $webhooks.find('.is-invalid').removeClass('is-invalid');
        $webhooks.find('.form-message').removeClass('alert-danger').hide();

        try {
            // Validate required fields.
            let missingRequired = false;

            $webhooks.find('.required').each((index, requiredField) => {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(lang('fields_are_required'));
            }

            return true;
        } catch (error) {
            $webhooks.find('.form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Resets the webhook tab form back to its initial state.
     */
    function resetForm() {
        $filterWebhooks.find('.selected').removeClass('selected');
        $filterWebhooks.find('button').prop('disabled', false);
        $filterWebhooks.find('.results').css('color', '');

        $webhooks.find('.record-details').find('input, select, textarea').val('').prop('disabled', true);
        $webhooks.find('.record-details .form-label span').prop('hidden', true);
        $webhooks.find('.record-details h3 a').remove();

        $webhooks.find('.add-edit-delete-group').show();
        $webhooks.find('.save-cancel-group').hide();
        $('#edit-webhook, #delete-webhook').prop('disabled', true);

        $webhooks.find('.record-details .is-invalid').removeClass('is-invalid');
        $webhooks.find('.record-details .form-message').hide();

        $actions.find('input:checkbox').prop('checked', false);
    }

    /**
     * Display a webhook record into the webhook form.
     *
     * @param {Object} webhook Contains the webhook record data.
     */
    function display(webhook) {
        $id.val(webhook.id);
        $name.val(webhook.name);
        $url.val(webhook.url);
        $secretToken.val(webhook.secret_token);
        $isSslVerified.prop('checked', Boolean(Number(webhook.is_ssl_verified)));

        $actions.find('input:checkbox').prop('checked', false);

        if (webhook.actions && webhook.actions.length) {
            const actions = webhook.actions.split(',');
            actions.forEach((action) => $(`[data-action="${action}"]`).prop('checked', true));
        }
    }

    /**
     * Filters webhook records depending a string keyword.
     *
     * @param {String} keyword This is used to filter the webhook records of the database.
     * @param {Number} selectId Optional, if set then after the filter operation the record with this
     * ID will be selected (but not displayed).
     * @param {Boolean} show Optional (false), if true then the selected record will be displayed on the form.
     */
    function filter(keyword, selectId = null, show = false) {
        App.Http.Webhooks.search(keyword, filterLimit).then((response) => {
            filterResults = response;

            $filterWebhooks.find('.results').empty();

            response.forEach((webhook) => {
                $filterWebhooks.find('.results').append(App.Pages.Webhooks.getFilterHtml(webhook)).append($('<hr/>'));
            });

            if (response.length === 0) {
                $filterWebhooks.find('.results').append(
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
                        App.Pages.Webhooks.filter(keyword, selectId, show);
                    },
                }).appendTo('#filter-webhooks .results');
            }

            if (selectId) {
                App.Pages.Webhooks.select(selectId, show);
            }
        });
    }

    /**
     * Get Filter HTML
     *
     * Get a webhook row HTML code that is going to be displayed on the filter results list.
     *
     * @param {Object} webhook Contains the webhook record data.
     *
     * @return {String} The HTML code that represents the record on the filter results list.
     */
    function getFilterHtml(webhook) {
        const name = webhook.name;

        const actionCount = webhook.actions && webhook.actions.length ? webhook.actions.split(',').length : 0;

        const info = `${actionCount} ${lang('actions')}`;

        return $('<div/>', {
            'class': 'webhook-row entry',
            'data-id': webhook.id,
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
     * Select a specific record from the current filter results. If the webhook id does not exist
     * in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} show Optional (false), if true then the method will display the record on the form.
     */
    function select(id, show = false) {
        $filterWebhooks.find('.selected').removeClass('selected');

        $filterWebhooks.find('.webhook-row[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const webhook = filterResults.find((filterResult) => Number(filterResult.id) === Number(id));

            App.Pages.Webhooks.display(webhook);

            $('#edit-webhook, #delete-webhook').prop('disabled', false);
        }
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        App.Pages.Webhooks.resetForm();
        App.Pages.Webhooks.filter('');
        App.Pages.Webhooks.addEventListeners();
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
