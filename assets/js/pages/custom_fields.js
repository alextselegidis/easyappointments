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
 * Custom Fields page.
 *
 * This module implements the functionality of the custom fields page.
 */
App.Pages.CustomFields = (function () {
    const $customFields = $('#custom-fields');
    const $id = $('#id');
    const $name = $('#name');
    const $label = $('#label');
    const $type = $('#type');
    const $displayColumn = $('#display-column');
    const $sortOrder = $('#sort-order');
    const $required = $('#required');
    const $active = $('#active');
    const $filterCustomFields = $('#filter-custom-fields');
    let filterResults = {};
    let filterLimit = 20;

    /**
     * Add page event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Filter Custom Fields Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $customFields.on('submit', '#filter-custom-fields form', function (event) {
            event.preventDefault();
            const key = $(this).find('.key').val();
            $filterCustomFields.find('.selected').removeClass('selected');
            resetForm();
            filter(key);
        });

        /**
         * Event: Filter Custom Field Row "Click"
         */
        $customFields.on('click', '.custom-field-row', function () {
            if ($filterCustomFields.find('.filter').prop('disabled')) {
                $filterCustomFields.find('.results').css('color', '#AAA');
                return; // Exit because we are on edit mode.
            }

            const customFieldId = $(this).attr('data-id');

            const customField = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(customFieldId);
            });

            display(customField);

            $filterCustomFields.find('.selected').removeClass('selected');
            $(this).addClass('selected');
            $('#edit-custom-field, #delete-custom-field').prop('disabled', false);
        });

        /**
         * Event: Add New Custom Field Button "Click"
         */
        $customFields.on('click', '#add-custom-field', function () {
            resetForm();
            $customFields.find('.add-edit-delete-group').hide();
            $customFields.find('.save-cancel-group').show();
            $customFields.find('.record-details').find('input, textarea, select').prop('disabled', false);
            $filterCustomFields.find('button').prop('disabled', true);
            $filterCustomFields.find('.results').css('color', '#AAA');

            // Set defaults
            $active.prop('checked', true);
            $type.val('text');
            $displayColumn.val('1');
            $sortOrder.val('0');
        });

        /**
         * Event: Edit Custom Field Button "Click"
         */
        $customFields.on('click', '#edit-custom-field', function () {
            $customFields.find('.add-edit-delete-group').hide();
            $customFields.find('.save-cancel-group').show();
            $customFields.find('.record-details').find('input, textarea, select').prop('disabled', false);
            $filterCustomFields.find('button').prop('disabled', true);
            $filterCustomFields.find('.results').css('color', '#AAA');
        });

        /**
         * Event: Delete Custom Field Button "Click"
         */
        $customFields.on('click', '#delete-custom-field', function () {
            const customFieldId = $id.val();

            const buttons = [
                {
                    text: lang('cancel'),
                    click: function () {
                        $('#message-box').dialog('close');
                    }
                },
                {
                    text: lang('delete'),
                    click: function () {
                        remove(customFieldId);
                        $('#message-box').dialog('close');
                    }
                }
            ];

            App.Utils.Message.show(lang('delete_custom_field'), lang('delete_record_prompt'), buttons);
        });

        /**
         * Event: Save Custom Field Button "Click"
         */
        $customFields.on('click', '#save-custom-field', function () {
            save();
        });

        /**
         * Event: Cancel Custom Field Button "Click"
         */
        $customFields.on('click', '#cancel-custom-field', function () {
            const id = $id.val();
            resetForm();
            if (id) {
                select(id, true);
            }
        });

        /**
         * Event: Type Select "Change"
         */
        $type.on('change', function () {
            const type = $(this).val();
            if (type === 'select') {
                $('#options-section').show();
                if ($id.val()) {
                    loadOptions($id.val());
                }
            } else {
                $('#options-section').hide();
            }
        });

        /**
         * Event: Add Option Button "Click"
         */
        $customFields.on('click', '#add-option', function () {
            $('#option-modal').modal('show');
            $('#option-id').val('');
            $('#option-field-id').val($id.val());
            $('#option-value').val('');
            $('#option-label').val('');
            $('#option-sort-order').val('0');
        });

        /**
         * Event: Save Option Button "Click"
         */
        $customFields.on('click', '#save-option', function () {
            const option = {
                id_custom_fields: $('#option-field-id').val(),
                option_value: $('#option-value').val(),
                option_label: $('#option-label').val(),
                sort_order: $('#option-sort-order').val()
            };

            const optionId = $('#option-id').val();
            if (optionId) {
                option.id = optionId;
            }

            App.Http.CustomFields.saveOption(option).done(function () {
                $('#option-modal').modal('hide');
                loadOptions($id.val());
                App.Layouts.Backend.displayNotification(lang('custom_field_saved'));
            });
        });

        /**
         * Event: Delete Option Button "Click"
         */
        $customFields.on('click', '.delete-option', function () {
            const optionId = $(this).data('option-id');

            if (confirm(lang('delete_record_prompt'))) {
                App.Http.CustomFields.destroyOption(optionId).done(function () {
                    loadOptions($id.val());
                    App.Layouts.Backend.displayNotification(lang('custom_field_deleted'));
                });
            }
        });
    }

    /**
     * Save custom field record to database.
     */
    function save() {
        const customField = {
            name: $name.val(),
            label: $label.val(),
            type: $type.val(),
            required: $required.prop('checked') ? 1 : 0,
            display_column: $displayColumn.val(),
            sort_order: $sortOrder.val(),
            active: $active.prop('checked') ? 1 : 0
        };

        if ($id.val()) {
            customField.id = $id.val();
        }

        App.Http.CustomFields.save(customField).done(function (response) {
            App.Layouts.Backend.displayNotification(lang('custom_field_saved'));
            resetForm();
            $filterCustomFields.find('.key').val('');
            filter('', response.id);
        });
    }

    /**
     * Delete a custom field record from database.
     *
     * @param {Number} id Record ID to be deleted.
     */
    function remove(id) {
        App.Http.CustomFields.destroy(id).done(function () {
            App.Layouts.Backend.displayNotification(lang('custom_field_deleted'));
            resetForm();
            filter($filterCustomFields.find('.key').val());
        });
    }

    /**
     * Validates a custom field record.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $customFields.find('.is-invalid').removeClass('is-invalid');
        $customFields.find('.form-message').removeClass('alert-danger alert-success').hide();

        try {
            const customField = {
                name: $name.val(),
                label: $label.val(),
                type: $type.val()
            };

            if (!customField.name || !customField.label) {
                throw new Error('Required fields are missing.');
            }

            return true;
        } catch (error) {
            $customFields.find('.form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Resets the custom field tab form back to its initial state.
     */
    function resetForm() {
        $filterCustomFields.find('.selected').removeClass('selected');
        $filterCustomFields.find('button').prop('disabled', false);
        $filterCustomFields.find('.results').css('color', '');

        $customFields.find('.add-edit-delete-group').show();
        $customFields.find('.save-cancel-group').hide();
        $customFields.find('.record-details').find('input, textarea, select').val('').prop('disabled', true);
        $customFields.find('.record-details').find('input[type="checkbox"]').prop('checked', false);
        $customFields.find('.form-message').hide();
        $('#edit-custom-field, #delete-custom-field').prop('disabled', true);
        $('#options-section').hide();
        $customFields.find('.is-invalid').removeClass('is-invalid');
    }

    /**
     * Display a custom field record into the custom field form.
     *
     * @param {Object} customField Contains the custom field record data.
     */
    function display(customField) {
        $id.val(customField.id);
        $name.val(customField.name);
        $label.val(customField.label);
        $type.val(customField.type);
        $displayColumn.val(customField.display_column);
        $sortOrder.val(customField.sort_order);
        $required.prop('checked', Boolean(customField.required));
        $active.prop('checked', Boolean(customField.active));

        if (customField.type === 'select') {
            $('#options-section').show();
            loadOptions(customField.id);
        } else {
            $('#options-section').hide();
        }
    }

    /**
     * Filters custom field records depending on the given keyword.
     *
     * @param {String} keyword This keyword is used to filter the custom field records.
     * @param {Number} selectId Optional, if set then after the filter operation the record with the given
     * ID will be selected (but not displayed).
     * @param {Boolean} display Optional (false), if true then the selected record will be displayed on the form.
     */
    function filter(keyword, selectId = null, display = false) {
        App.Http.CustomFields.search(keyword, filterLimit).done(function (response) {
            filterResults = response;

            $filterCustomFields.find('.results').empty();

            response.forEach(function (customField) {
                $filterCustomFields.find('.results').append(getFilterHtml(customField)).append($('<hr/>'));
            });

            if (!response.length) {
                $filterCustomFields.find('.results').append(
                    $('<em/>', {
                        'text': lang('no_records_found')
                    })
                );
            } else if (response.length === filterLimit) {
                $('<button/>', {
                    'type': 'button',
                    'class': 'btn btn-outline-secondary w-100 load-more text-center',
                    'text': lang('load_more'),
                    'click': function () {
                        filterLimit += 20;
                        filter(keyword, selectId, display);
                    }
                }).appendTo($filterCustomFields.find('.results'));
            }

            if (selectId) {
                select(selectId, display);
            }
        });
    }

    /**
     * Get Filter Results Row HTML
     *
     * @param {Object} customField Contains the custom field data.
     *
     * @return {String} The HTML of the custom field row.
     */
    function getFilterHtml(customField) {
        const name = customField.name;
        const label = customField.label;
        const type = customField.type;
        const active = customField.active ? lang('active') : lang('inactive');

        const html = `
            <div class="custom-field-row entry" data-id="${customField.id}">
                <strong>${label}</strong><br>
                <small class="text-muted">
                    ${name} | ${type} | ${active}
                </small>
            </div>
        `;

        return html;
    }

    /**
     * Select a specific record from the current filter results. If the custom field id does not exist
     * in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} display Optional (false), if true then the method will display the record on the form.
     */
    function select(id, display = false) {
        $filterCustomFields.find('.selected').removeClass('selected');

        $filterCustomFields.find('.custom-field-row[data-id="' + id + '"]').addClass('selected');

        if (display) {
            const customField = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(id);
            });

            this.display(customField);
        }
    }

    /**
     * Load options for a select field.
     *
     * @param {Number} customFieldId
     */
    function loadOptions(customFieldId) {
        App.Http.CustomFields.getOptions(customFieldId).done(function (options) {
            const $optionsList = $('#options-list');
            $optionsList.empty();

            if (!options.length) {
                $optionsList.append('<div class="text-muted p-3">No options yet. Click "Add Option" to create one.</div>');
                return;
            }

            options.forEach(function (option) {
                const $item = $(`
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${option.option_label}</strong><br>
                            <small class="text-muted">Value: ${option.option_value}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger delete-option" data-option-id="${option.id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `);
                $optionsList.append($item);
            });
        });
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
