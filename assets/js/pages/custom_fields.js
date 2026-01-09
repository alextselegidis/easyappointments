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
 * Custom fields page.
 *
 * This module implements the functionality of the custom fields page
 */
App.Pages.CustomFields = (function () {
    const $customFields = $('#custom-fields');
    const $id = $('#id');
    const $name = $('#name');
    const $label = $('#label');
    const $type = $('#type');
    const $required = $('#required');
    const $displayColumn = $('#display_column');
    const $active = $('#active');
    const $columnPosition = $('#column-position');
    const $filterCustomFields = $('#filter-custom-fields');
    const $optionsSection = $('#options-section');
    const $optionsList = $('#options-list');
    let filterResults = {};
    let filterLimit = 20;

    /**
     * Bind event handlers.
     */
    function bindEventHandlers() {
        /**
         * Event: Filter Custom Fields Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $customFields.on('submit', '#filter-custom-fields form', function (event) {
            event.preventDefault();
            const key = $('#filter-custom-fields .key').val();
            $('#filter-custom-fields .selected').removeClass('selected');
            filter(key);
        });

        /**
         * Event: Filter Custom Field Row "Click"
         *
         * Display the selected custom field data to the user.
         */
        $customFields.on('click', '.custom-field-row', function () {
            if ($('#edit-custom-field').prop('disabled')) {
                $('#edit-custom-field').prop('disabled', false);
                $('#delete-custom-field').prop('disabled', false);
            }

            const $this = $(this);
            const customFieldId = $this.attr('data-id');
            const customField = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(customFieldId);
            });

            display(customField);
            $('.selected').removeClass('selected');
            $this.addClass('selected');
        });

        /**
         * Event: Add New Custom Field Button "Click"
         */
        $('#add-custom-field').on('click', function () {
            resetForm();
            $customFields.find('.add-edit-delete-group').hide();
            $customFields.find('.save-cancel-group').show();
            $customFields.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $('#name').trigger('focus');
        });

        /**
         * Event: Edit Custom Field Button "Click"
         */
        $('#edit-custom-field').on('click', function () {
            $customFields.find('.add-edit-delete-group').hide();
            $customFields.find('.save-cancel-group').show();
            $customFields.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $('#name').trigger('focus');
        });

        /**
         * Event: Delete Custom Field Button "Click"
         */
        $('#delete-custom-field').on('click', function () {
            const customFieldId = $id.val();

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
                        remove(customFieldId);
                        messageModal.hide();
                    },
                },
            ];

            App.Utils.Message.show(lang('delete_custom_field'), lang('delete_record_prompt'), buttons);
        });

        /**
         * Event: Save Custom Field Button "Click"
         */
        $('#save-custom-field').on('click', function () {
            save();
        });

        /**
         * Event: Cancel Custom Field Button "Click"
         */
        $('#cancel-custom-field').on('click', function () {
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
                $optionsSection.show();
            } else {
                $optionsSection.hide();
            }
        });

        /**
         * Event: Add Option Button "Click"
         */
        $('#add-option').on('click', function () {
            addOptionRow();
        });

        /**
         * Event: Delete Option Button "Click"
         */
        $optionsList.on('click', '.delete-option', function () {
            $(this).closest('.option-row').remove();
        });
    }

    /**
     * Add an option row to the options list
     *
     * @param {Object} [option] - Option data
     */
    function addOptionRow(option) {
        const optionId = option ? option.id : '';
        const optionValue = option ? option.option_value : '';
        const optionLabel = option ? option.option_label : '';

        const $row = $('<div class="option-row input-group mb-2">')
            .append(`<input type="hidden" class="option-id" value="${optionId}">`)
            .append(`<input type="text" class="form-control option-value" placeholder="${lang('value')}" value="${optionValue}">`)
            .append(`<input type="text" class="form-control option-label" placeholder="${lang('label')}" value="${optionLabel}">`)
            .append(`<button type="button" class="btn btn-outline-danger delete-option"><i class="fas fa-trash"></i></button>`);

        $optionsList.append($row);
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
            display_column: $displayColumn.prop('checked') ? 1 : 0,
            active: $active.prop('checked') ? 1 : 0,
            column_position: $columnPosition.val(),
        };

        if ($id.val()) {
            customField.id = $id.val();
        }

        if (!validate()) {
            return;
        }

        App.Http.CustomFields.save(customField).then((response) => {
            // If it's a select field, save the options
            if (customField.type === 'select') {
                const promises = [];

                $('.option-row').each(function (index) {
                    const optionValue = $(this).find('.option-value').val();
                    const optionLabel = $(this).find('.option-label').val();
                    const optionId = $(this).find('.option-id').val();

                    if (optionValue && optionLabel) {
                        const option = {
                            id_custom_fields: response.id,
                            option_value: optionValue,
                            option_label: optionLabel,
                            sort_order: index,
                        };

                        if (optionId) {
                            option.id = optionId;
                        }

                        promises.push(App.Http.CustomFields.saveOption(option));
                    }
                });

                Promise.all(promises).then(() => {
                    App.Layouts.Backend.displayNotification(lang('custom_field_saved'));
                    resetForm();
                    $('#filter-custom-fields .key').val('');
                    filter('', response.id);
                });
            } else {
                App.Layouts.Backend.displayNotification(lang('custom_field_saved'));
                resetForm();
                $('#filter-custom-fields .key').val('');
                filter('', response.id);
            }
        });
    }

    /**
     * Delete a custom field record from database.
     *
     * @param {Number} id - Record ID to be deleted.
     */
    function remove(id) {
        App.Http.CustomFields.delete(id).then(() => {
            App.Layouts.Backend.displayNotification(lang('custom_field_deleted'));
            resetForm();
            filter($('#filter-custom-fields .key').val());
        });
    }

    /**
     * Validates a custom field record.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $customFields.find('.is-invalid').removeClass('is-invalid');
        $customFields.find('.form-message').hide();

        try {
            const customField = {
                name: $name.val(),
                label: $label.val(),
                type: $type.val(),
            };

            // Required fields.
            if (!customField.name || !customField.label || !customField.type) {
                throw new Error(lang('fields_are_required'));
            }

            return true;
        } catch (error) {
            $customFields.find('.form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Reset the custom field form.
     */
    function resetForm() {
        $customFields.find('.add-edit-delete-group').show();
        $customFields.find('.save-cancel-group').hide();
        $customFields.find('.record-details').find('input, select, textarea').val('').prop('disabled', true);
        $customFields.find('.record-details .form-message').hide();
        $customFields.find('.record-details h3 a').remove();

        $('#edit-custom-field').prop('disabled', true);
        $('#delete-custom-field').prop('disabled', true);

        $customFields.find('.selected').removeClass('selected');
        $customFields.find('.record-details .is-invalid').removeClass('is-invalid');

        $optionsSection.hide();
        $optionsList.empty();
    }

    /**
     * Display a custom field record into the form.
     *
     * @param {Object} customField - Contains the custom field record data.
     */
    function display(customField) {
        $id.val(customField.id);
        $name.val(customField.name);
        $label.val(customField.label);
        $type.val(customField.type);
        $required.prop('checked', Boolean(customField.required));
        $displayColumn.prop('checked', Boolean(customField.display_column));
        $active.prop('checked', Boolean(customField.active));
        $columnPosition.val(customField.column_position || 'left');

        if (customField.type === 'select') {
            $optionsSection.show();
            $optionsList.empty();

            App.Http.CustomFields.getOptions(customField.id).then((options) => {
                options.forEach((option) => {
                    addOptionRow(option);
                });
            });
        } else {
            $optionsSection.hide();
        }
    }

    /**
     * Filter custom field records.
     *
     * @param {String} keyword - Keyword to search in custom field data.
     * @param {Number} [selectId] - Optional, if provided the given ID will be selected.
     * @param {Boolean} [show=false] - Optional (false), if true the method will display the record details.
     */
    function filter(keyword, selectId = null, show = false) {
        App.Http.CustomFields.search(keyword, filterLimit).then((response) => {
            filterResults = response;
            $('#filter-custom-fields .results').empty();

            response.forEach((customField) => {
                $('#filter-custom-fields .results').append(getFilterHtml(customField)).append($('<hr/>'));
            });

            if (!response.length) {
                $('#filter-custom-fields .results').append(
                    $('<em/>', {
                        'text': lang('no_records_found'),
                    }),
                );
            }

            if (selectId) {
                select(selectId, show);
            }
        });
    }

    /**
     * Get Filter HTML
     *
     * Get a custom field row HTML for the filter results.
     *
     * @param {Object} customField - Custom field object.
     *
     * @return {String} Returns the record HTML code.
     */
    function getFilterHtml(customField) {
        const activeIcon = customField.active ? 'fa-check' : '';

        return $('<div/>', {
            'class': 'custom-field-row entry',
            'data-id': customField.id,
            'html': [
                $('<strong/>', {
                    'text': customField.label,
                }),
                $('<br/>'),
                $('<span/>', {
                    'class': 'text-muted',
                    'text': customField.name,
                }),
                $('<i/>', {
                    'class': `fas ${activeIcon} ms-2`,
                }),
            ],
        });
    }

    /**
     * Select a specific record from the list.
     *
     * @param {Number} id - Record ID.
     * @param {Boolean} [show] - Optional (false), if true the record will be displayed on the form.
     */
    function select(id, show) {
        $('#filter-custom-fields .selected').removeClass('selected');

        $('#filter-custom-fields .custom-field-row[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const customField = filterResults.find((filterResult) => {
                return Number(filterResult.id) === Number(id);
            });

            display(customField);
        }
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        resetForm();
        filter('');
        bindEventHandlers();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        filter,
        save,
        delete: remove,
        getFilterHtml,
    };
})();
